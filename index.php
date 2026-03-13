<?php
/**
 * Login-Seite (Startseite)
 *
 * Benutzer authentisiert sich mit Name und Passwort.
 */

require_once __DIR__ . '/includes/auth.php';

// Bereits eingeloggt? → Dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = loginUser($username, $password);
    if ($result === true) {
        header('Location: dashboard.php');
        exit;
    }
    $error = $result;
}

$title = 'Anmelden – BSN Klassen-Plattform';
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
<div class="auth-wrapper">
    <div class="auth-card">
        <h1>📚 BSN Plattform</h1>
        <p class="subtitle">Gemeinsam lernen, arbeiten und Freizeit organisieren</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <div class="form-group">
                <label for="username">Benutzername</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Passwort</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:0.5rem;">
                Anmelden
            </button>
        </form>

        <p class="auth-footer">
            Noch kein Konto? <a href="register.php">Jetzt registrieren</a>
        </p>
    </div>
</div>
</body>
</html>
