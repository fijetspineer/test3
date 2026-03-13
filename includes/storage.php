<?php
/**
 * Speicher- und Datei-Funktionen
 *
 * Enthält alle Funktionen rund um Datei-Upload, -Download,
 * -Löschung, Speicherplatz und Freigaben.
 */

require_once __DIR__ . '/../config/db.php';

// Basis-Verzeichnis für Uploads
define('UPLOAD_BASE', __DIR__ . '/../uploads');

// ----------------------------------------------------------------
// Speicherplatz
// ----------------------------------------------------------------

/**
 * Gibt den aktuell belegten Speicherplatz eines Users zurück (in Bytes).
 */
function getUsedStorage(int $userId): int
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(filesize), 0) AS used FROM files WHERE user_id = ?');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}

/**
 * Gibt das Speicherlimit eines Users zurück (in Bytes).
 */
function getStorageLimit(int $userId): int
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT storage_limit FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}

/**
 * Formatiert Bytes in lesbares Format (KB, MB, GB).
 */
function formatBytes(int $bytes): string
{
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2, ',', '.') . ' GB';
    }
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
    }
    if ($bytes >= 1024) {
        return number_format($bytes / 1024, 2, ',', '.') . ' KB';
    }
    return $bytes . ' Bytes';
}

// ----------------------------------------------------------------
// Datei-Upload
// ----------------------------------------------------------------

/**
 * Lädt eine Datei hoch und speichert sie im persönlichen Bereich.
 *
 * @param  array  $file    $_FILES-Eintrag
 * @param  int    $userId  Benutzer-ID
 * @param  string $folder  Optionaler Unterordner
 * @return string|true     true bei Erfolg, Fehlermeldung bei Fehler.
 */
function uploadFile(array $file, int $userId, string $folder = '')
{
    global $pdo;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'Fehler beim Hochladen der Datei.';
    }

    $folder = sanitizeFolderName($folder);

    // Speicherlimit prüfen
    $used  = getUsedStorage($userId);
    $limit = getStorageLimit($userId);
    if ($used + $file['size'] > $limit) {
        return 'Speicherlimit überschritten. Verfügbar: ' . formatBytes($limit - $used);
    }

    // Dateiname säubern
    $originalName = basename($file['name']);
    $safeName     = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalName);
    if ($safeName === '' || $safeName === '.' || $safeName === '..') {
        return 'Ungültiger Dateiname.';
    }

    // Zielverzeichnis erstellen
    $userDir = UPLOAD_BASE . '/' . $userId;
    $targetDir = $userDir;
    if ($folder !== '') {
        $targetDir .= '/' . $folder;
    }
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0750, true);
    }

    // Bei Namenskonflikten umbenennen
    $targetPath = $targetDir . '/' . $safeName;
    $counter = 1;
    while (file_exists($targetPath)) {
        $info = pathinfo($safeName);
        $newName = $info['filename'] . '_' . $counter;
        if (isset($info['extension'])) {
            $newName .= '.' . $info['extension'];
        }
        $targetPath = $targetDir . '/' . $newName;
        $safeName = $newName;
        $counter++;
    }

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return 'Datei konnte nicht gespeichert werden.';
    }

    // Relativer Pfad zur DB
    $relativePath = $userId . '/' . ($folder !== '' ? $folder . '/' : '') . $safeName;

    $stmt = $pdo->prepare(
        'INSERT INTO files (user_id, filename, filepath, filesize, folder) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $safeName, $relativePath, $file['size'], $folder]);

    return true;
}

// ----------------------------------------------------------------
// Datei-Listing
// ----------------------------------------------------------------

/**
 * Gibt alle Dateien eines Benutzers zurück, optional gefiltert nach Ordner.
 */
function getUserFiles(int $userId, string $folder = ''): array
{
    global $pdo;

    $folder = sanitizeFolderName($folder);

    $stmt = $pdo->prepare(
        'SELECT id, filename, filesize, folder, uploaded_at FROM files WHERE user_id = ? AND folder = ? ORDER BY uploaded_at DESC'
    );
    $stmt->execute([$userId, $folder]);
    return $stmt->fetchAll();
}

/**
 * Gibt alle Ordner eines Benutzers zurück.
 */
function getUserFolders(int $userId): array
{
    global $pdo;

    $stmt = $pdo->prepare(
        'SELECT DISTINCT folder FROM files WHERE user_id = ? AND folder != \'\' ORDER BY folder'
    );
    $stmt->execute([$userId]);
    return array_column($stmt->fetchAll(), 'folder');
}

// ----------------------------------------------------------------
// Datei-Download
// ----------------------------------------------------------------

/**
 * Gibt eine Datei-Info zurück, falls der Benutzer Zugriff hat.
 *
 * @param  int  $fileId
 * @param  int  $requestingUserId  Der anfragende Benutzer
 * @return array|null  Datei-Info oder null bei fehlendem Zugriff
 */
function getFileForDownload(int $fileId, int $requestingUserId): ?array
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM files WHERE id = ?');
    $stmt->execute([$fileId]);
    $file = $stmt->fetch();

    if (!$file) {
        return null;
    }

    // Eigentümer hat immer Zugriff
    if ($file['user_id'] === $requestingUserId) {
        return $file;
    }

    // Prüfe ob Ordner freigegeben ist
    if ($file['folder'] !== '') {
        $stmt = $pdo->prepare('SELECT id FROM shares WHERE user_id = ? AND folder = ?');
        $stmt->execute([$file['user_id'], $file['folder']]);
        if ($stmt->fetch()) {
            return $file;
        }
    }

    return null; // Kein Zugriff
}

// ----------------------------------------------------------------
// Datei-Löschung
// ----------------------------------------------------------------

/**
 * Löscht eine Datei. Nur der Eigentümer darf löschen.
 *
 * @return string|true  true bei Erfolg, Fehlermeldung bei Fehler.
 */
function deleteFile(int $fileId, int $userId)
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM files WHERE id = ? AND user_id = ?');
    $stmt->execute([$fileId, $userId]);
    $file = $stmt->fetch();

    if (!$file) {
        return 'Datei nicht gefunden oder keine Berechtigung.';
    }

    $fullPath = UPLOAD_BASE . '/' . $file['filepath'];
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }

    $stmt = $pdo->prepare('DELETE FROM files WHERE id = ?');
    $stmt->execute([$fileId]);

    return true;
}

// ----------------------------------------------------------------
// Freigaben (Shares)
// ----------------------------------------------------------------

/**
 * Gibt einen Ordner zum Lesen für andere User frei.
 *
 * @return string|true  true bei Erfolg, Fehlermeldung bei Fehler.
 */
function shareFolder(int $userId, string $folder)
{
    global $pdo;

    $folder = sanitizeFolderName($folder);
    if ($folder === '') {
        return 'Bitte einen gültigen Ordnernamen angeben.';
    }

    // Prüfe, ob Ordner Dateien enthält
    $stmt = $pdo->prepare('SELECT id FROM files WHERE user_id = ? AND folder = ? LIMIT 1');
    $stmt->execute([$userId, $folder]);
    if (!$stmt->fetch()) {
        return 'Der Ordner enthält keine Dateien.';
    }

    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO shares (user_id, folder) VALUES (?, ?)'
    );
    $stmt->execute([$userId, $folder]);

    return true;
}

/**
 * Hebt die Freigabe eines Ordners auf.
 *
 * @return string|true  true bei Erfolg, Fehlermeldung bei Fehler.
 */
function unshareFolder(int $userId, string $folder)
{
    global $pdo;

    $folder = sanitizeFolderName($folder);

    $stmt = $pdo->prepare('DELETE FROM shares WHERE user_id = ? AND folder = ?');
    $stmt->execute([$userId, $folder]);

    return true;
}

/**
 * Gibt alle Freigaben eines Benutzers zurück.
 */
function getUserShares(int $userId): array
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM shares WHERE user_id = ? ORDER BY folder');
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Gibt alle freigegebenen Ordner anderer Benutzer zurück.
 */
function getSharedFoldersFromOthers(int $excludeUserId): array
{
    global $pdo;

    $stmt = $pdo->prepare(
        'SELECT s.*, u.username FROM shares s JOIN users u ON s.user_id = u.id WHERE s.user_id != ? ORDER BY u.username, s.folder'
    );
    $stmt->execute([$excludeUserId]);
    return $stmt->fetchAll();
}

/**
 * Gibt Dateien eines freigegebenen Ordners zurück.
 */
function getSharedFiles(int $ownerUserId, string $folder): array
{
    global $pdo;

    $folder = sanitizeFolderName($folder);

    // Prüfe ob Ordner tatsächlich freigegeben ist
    $stmt = $pdo->prepare('SELECT id FROM shares WHERE user_id = ? AND folder = ?');
    $stmt->execute([$ownerUserId, $folder]);
    if (!$stmt->fetch()) {
        return [];
    }

    $stmt = $pdo->prepare(
        'SELECT id, filename, filesize, folder, uploaded_at FROM files WHERE user_id = ? AND folder = ? ORDER BY uploaded_at DESC'
    );
    $stmt->execute([$ownerUserId, $folder]);
    return $stmt->fetchAll();
}

// ----------------------------------------------------------------
// Hilfsfunktionen
// ----------------------------------------------------------------

/**
 * Säubert einen Ordnernamen (verhindert Path-Traversal).
 */
function sanitizeFolderName(string $folder): string
{
    $folder = trim($folder, " \t\n\r\0\x0B/\\");
    $folder = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $folder);
    // Verhindere Path-Traversal
    $folder = str_replace('..', '', $folder);
    return $folder;
}
