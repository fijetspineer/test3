<?php
/**
 * Passwort ändern
 *
 * PHP-Skript sorgt für die Einhaltung der Passwortregeln.
 */

require_once __DIR__ . '/includes/auth.php';

requireLogin();

$title   = 'Passwort ändern – BSN Klassen-Plattform';
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPw  = $_POST['current_password']  ?? '';
    $newPw      = $_POST['new_password']       ?? '';
    $newPwConfirm = $_POST['new_password_confirm'] ?? '';

    $result = changePassword($currentPw, $newPw, $newPwConfirm);
    if ($result === true) {
        $success = 'Passwort erfolgreich geändert.';
    } else {
        $error = $result;
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="card" style="max-width:500px;margin:0 auto;">
    <h2>🔑 Passwort ändern</h2>

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

    <form method="POST" action="change_password.php">
        <div class="form-group">
            <label for="current_password">Aktuelles Passwort</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">Neues Passwort</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="new_password_confirm">Neues Passwort bestätigen</label>
            <input type="password" id="new_password_confirm" name="new_password_confirm" required>
        </div>
        <button type="submit" class="btn btn-primary">Passwort ändern</button>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
