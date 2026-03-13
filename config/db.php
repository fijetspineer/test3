<?php
/**
 * Datenbank-Verbindung (PDO)
 *
 * Trennung von Konfiguration und Logik:
 * Die tatsächlichen Zugangsdaten werden aus db_credentials.php geladen,
 * die NICHT im Repository liegt (.gitignore).
 */

// Standardwerte (für Entwicklung)
$db_host = 'localhost';
$db_name = 'bsn_platform';
$db_user = 'root';
$db_pass = '';

// Lade Credentials-Datei, falls vorhanden
$credentialsFile = __DIR__ . '/db_credentials.php';
if (file_exists($credentialsFile)) {
    require $credentialsFile;
}

try {
    $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die('Datenbankverbindung fehlgeschlagen: ' . $e->getMessage());
}
