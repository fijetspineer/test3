<?php
/**
 * HTML-Header Template
 *
 * Eingebunden am Anfang jeder Seite für konsistentes Layout.
 *
 * @param string $title  Seitentitel
 */

if (!isset($title)) {
    $title = 'BSN Klassen-Plattform';
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php if (isset($_SESSION['user_id'])): ?>
<nav class="navbar">
    <div class="nav-brand">📚 BSN Klassen-Plattform</div>
    <div class="nav-links">
        <a href="dashboard.php">📁 Meine Dateien</a>
        <a href="shared_files.php">🔗 Freigegebene Dateien</a>
        <a href="share.php">⚙️ Freigaben verwalten</a>
        <a href="change_password.php">🔑 Passwort ändern</a>
        <span class="nav-user">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="logout.php" class="btn-logout">Abmelden</a>
    </div>
</nav>
<?php endif; ?>
<main class="container">
