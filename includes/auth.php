<?php
/**
 * Authentifizierung – Funktionen
 *
 * Enthält alle Funktionen rund um Login, Registrierung,
 * Passwort-Hashing und Session-Verwaltung.
 */

session_start();

require_once __DIR__ . '/../config/db.php';

// ----------------------------------------------------------------
// Session-Prüfung
// ----------------------------------------------------------------

/**
 * Prüft, ob der Benutzer eingeloggt ist.
 * Leitet bei fehlender Berechtigung zur Login-Seite um.
 */
function requireLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Gibt die ID des aktuell eingeloggten Benutzers zurück.
 */
function currentUserId(): int
{
    return (int) ($_SESSION['user_id'] ?? 0);
}

/**
 * Gibt den Benutzernamen des aktuell eingeloggten Benutzers zurück.
 */
function currentUsername(): string
{
    return $_SESSION['username'] ?? '';
}

// ----------------------------------------------------------------
// Registrierung
// ----------------------------------------------------------------

/**
 * Registriert einen neuen Benutzer.
 *
 * @return string|true  true bei Erfolg, Fehlermeldung als String bei Fehler.
 */
function registerUser(string $username, string $password, string $passwordConfirm)
{
    global $pdo;

    $username = trim($username);

    if ($username === '' || $password === '') {
        return 'Benutzername und Passwort dürfen nicht leer sein.';
    }

    if (strlen($username) < 3 || strlen($username) > 50) {
        return 'Der Benutzername muss zwischen 3 und 50 Zeichen lang sein.';
    }

    if ($password !== $passwordConfirm) {
        return 'Die Passwörter stimmen nicht überein.';
    }

    $pwError = validatePassword($password);
    if ($pwError !== true) {
        return $pwError;
    }

    // Prüfe ob Benutzername bereits existiert
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        return 'Dieser Benutzername ist bereits vergeben.';
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    $stmt->execute([$username, $hash]);

    return true;
}

// ----------------------------------------------------------------
// Login
// ----------------------------------------------------------------

/**
 * Authentifiziert einen Benutzer.
 *
 * @return string|true  true bei Erfolg, Fehlermeldung als String bei Fehler.
 */
function loginUser(string $username, string $password)
{
    global $pdo;

    $username = trim($username);

    $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return 'Ungültiger Benutzername oder Passwort.';
    }

    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];

    return true;
}

// ----------------------------------------------------------------
// Passwort-Validierung & -Änderung
// ----------------------------------------------------------------

/**
 * Überprüft Passwort-Regeln.
 *
 * Regeln:
 * - Mindestens 8 Zeichen
 * - Mindestens ein Großbuchstabe
 * - Mindestens ein Kleinbuchstabe
 * - Mindestens eine Ziffer
 * - Mindestens ein Sonderzeichen
 *
 * @return string|true  true wenn gültig, Fehlermeldung bei Verstoß.
 */
function validatePassword(string $password)
{
    if (strlen($password) < 8) {
        return 'Das Passwort muss mindestens 8 Zeichen lang sein.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return 'Das Passwort muss mindestens einen Großbuchstaben enthalten.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        return 'Das Passwort muss mindestens einen Kleinbuchstaben enthalten.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        return 'Das Passwort muss mindestens eine Ziffer enthalten.';
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return 'Das Passwort muss mindestens ein Sonderzeichen enthalten.';
    }
    return true;
}

/**
 * Ändert das Passwort des aktuell eingeloggten Benutzers.
 *
 * @return string|true  true bei Erfolg, Fehlermeldung bei Fehler.
 */
function changePassword(string $currentPassword, string $newPassword, string $newPasswordConfirm)
{
    global $pdo;

    $userId = currentUserId();

    $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($currentPassword, $user['password'])) {
        return 'Das aktuelle Passwort ist falsch.';
    }

    if ($newPassword !== $newPasswordConfirm) {
        return 'Die neuen Passwörter stimmen nicht überein.';
    }

    $pwError = validatePassword($newPassword);
    if ($pwError !== true) {
        return $pwError;
    }

    $hash = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
    $stmt->execute([$hash, $userId]);

    return true;
}
