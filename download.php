<?php
/**
 * Datei-Download Handler
 *
 * Prüft Zugriffsrechte und sendet die Datei.
 * Zugriff nur für Eigentümer oder bei freigegebenen Ordnern.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/storage.php';

requireLogin();

$fileId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$userId = currentUserId();

if ($fileId <= 0) {
    http_response_code(400);
    die('Ungültige Anfrage.');
}

$file = getFileForDownload($fileId, $userId);

if (!$file) {
    http_response_code(403);
    die('Zugriff verweigert. Sie haben keine Berechtigung, diese Datei herunterzuladen.');
}

$fullPath = UPLOAD_BASE . '/' . $file['filepath'];

if (!file_exists($fullPath)) {
    http_response_code(404);
    die('Datei nicht gefunden.');
}

// Datei senden
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file['filename']) . '"');
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: no-cache, must-revalidate');

readfile($fullPath);
exit;
