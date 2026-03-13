<?php
/**
 * Registrierung
 *
 * Neuer Benutzer kann sich mit Name und Passwort registrieren.
 * Passwort wird als bcrypt-Hash gespeichert.
 */

require_once __DIR__ . '/includes/auth.php';

// Bereits eingeloggt? → Dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username        = $_POST['username']         ?? '';
    $password        = $_POST['password']         ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    $result = registerUser($username, $password, $passwordConfirm);
    if ($result === true) {
        $success = 'Registrierung erfolgreich! Sie können sich jetzt anmelden.';
    } else {
        $error = $result;
    }
}

$title = 'Registrieren – BSN Klassen-Plattform';
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
        <h1>📝 Registrierung</h1>
        <p class="subtitle">Erstellen Sie Ihr Konto</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="password-rules">
            <strong>Passwort-Regeln:</strong>
            <ul>
                <li>Mindestens 8 Zeichen</li>
                <li>Mindestens ein Großbuchstabe</li>
                <li>Mindestens ein Kleinbuchstabe</li>
                <li>Mindestens eine Ziffer</li>
                <li>Mindestens ein Sonderzeichen</li>
            </ul>
        </div>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Benutzername</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       required autofocus minlength="3" maxlength="50">
            </div>
            <div class="form-group">
                <label for="password">Passwort</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Passwort bestätigen</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:0.5rem;">
                Registrieren
            </button>
        </form>

        <p class="auth-footer">
            Bereits registriert? <a href="index.php">Zur Anmeldung</a>
        </p>
    </div>
</div>
</body>
</html>
