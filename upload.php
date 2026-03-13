<?php
/**
 * Datei-Upload Handler
 *
 * Nimmt den Upload entgegen und leitet zurück zum Dashboard.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/storage.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$folder = $_POST['folder'] ?? '';
$userId = currentUserId();

if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
    $_SESSION['flash_error'] = 'Bitte eine Datei auswählen.';
    header('Location: dashboard.php' . ($folder ? '?folder=' . urlencode($folder) : ''));
    exit;
}

$result = uploadFile($_FILES['file'], $userId, $folder);

if ($result === true) {
    $_SESSION['flash_success'] = 'Datei erfolgreich hochgeladen.';
} else {
    $_SESSION['flash_error'] = $result;
}

$redirectFolder = sanitizeFolderName($folder);
header('Location: dashboard.php' . ($redirectFolder ? '?folder=' . urlencode($redirectFolder) : ''));
exit;
