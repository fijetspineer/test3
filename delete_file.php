<?php
/**
 * Datei-Löschung Handler
 *
 * Nur der Eigentümer darf seine Dateien löschen.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/storage.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$fileId = isset($_POST['file_id']) ? (int) $_POST['file_id'] : 0;
$folder = $_POST['folder'] ?? '';
$userId = currentUserId();

if ($fileId <= 0) {
    $_SESSION['flash_error'] = 'Ungültige Anfrage.';
    header('Location: dashboard.php');
    exit;
}

$result = deleteFile($fileId, $userId);

if ($result === true) {
    $_SESSION['flash_success'] = 'Datei erfolgreich gelöscht.';
} else {
    $_SESSION['flash_error'] = $result;
}

$redirectFolder = sanitizeFolderName($folder);
header('Location: dashboard.php' . ($redirectFolder ? '?folder=' . urlencode($redirectFolder) : ''));
exit;
