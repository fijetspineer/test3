# BSN Klassen-Plattform

Datenbank-Anwendung mit Cloud-Storage und Webfrontend für die gemeinsame Organisation von Lernen, Arbeiten und Freizeitaktivitäten im Klassenverband.

## Funktionen

- **Benutzer-Authentifizierung**: Login und Registrierung mit sicherer Passwort-Speicherung (bcrypt-Hash)
- **Passwort-Regeln**: Mindestens 8 Zeichen, Groß-/Kleinbuchstaben, Ziffer und Sonderzeichen
- **Passwort ändern**: Über eine Eingabemaske mit Regelprüfung
- **Persönlicher Speicherbereich**: Jeder User hat einen eigenen Bereich für Datei-Uploads
- **Speicherlimit**: 10 MB pro User (konfigurierbar), Upload-Skript verhindert Überschreitung
- **Ordner-Freigabe**: Benutzer können Ordner zum Lesen für andere freigeben
- **Zugriffskontrolle**: Geschützte Bereiche sind nur mit Berechtigung zugänglich

## Voraussetzungen

- PHP 7.4+ (empfohlen: PHP 8.x)
- MySQL 5.7+ / MariaDB 10.3+
- Webserver (Apache/Nginx) mit PHP-Unterstützung

## Installation

### 1. Datenbank einrichten

```sql
-- MySQL-Konsole öffnen und SQL-Skript ausführen:
SOURCE sql/database.sql;
```

Oder importieren Sie `sql/database.sql` über phpMyAdmin.

### 2. Datenbank-Zugangsdaten konfigurieren

Erstellen Sie die Datei `config/db_credentials.php`:

```php
<?php
$db_host = 'localhost';
$db_name = 'bsn_platform';
$db_user = 'Ihr_DB_Benutzer';
$db_pass = 'Ihr_DB_Passwort';
```

> **Hinweis:** Diese Datei ist in `.gitignore` eingetragen und wird nicht ins Repository übernommen.

### 3. Webserver konfigurieren

Richten Sie den Document-Root Ihres Webservers auf das Projektverzeichnis.

Stellen Sie sicher, dass das Verzeichnis `uploads/` vom Webserver beschreibbar ist:

```bash
chmod 750 uploads/
```

### 4. Anwendung starten

Öffnen Sie die Anwendung im Browser und registrieren Sie den ersten Benutzer.

## Projektstruktur

```
├── config/
│   └── db.php                 # Datenbank-Verbindung (PDO)
├── css/
│   └── style.css              # Stylesheet
├── includes/
│   ├── auth.php               # Authentifizierungs-Funktionen
│   ├── storage.php            # Speicher- und Datei-Funktionen
│   ├── header.php             # HTML-Header Template
│   └── footer.php             # HTML-Footer Template
├── sql/
│   └── database.sql           # Datenbank-Schema
├── uploads/                   # Benutzer-Uploads (gitignored)
├── index.php                  # Login-Seite
├── register.php               # Registrierung
├── dashboard.php              # Persönlicher Speicherbereich
├── upload.php                 # Upload-Handler
├── download.php               # Download-Handler (mit Zugriffskontrolle)
├── delete_file.php            # Lösch-Handler
├── change_password.php        # Passwort ändern
├── share.php                  # Freigaben verwalten
├── shared_files.php           # Freigegebene Dateien anzeigen
└── logout.php                 # Abmelden
```

## Separation of Concerns

- **config/**: Datenbank-Konfiguration
- **includes/auth.php**: Reine Authentifizierungslogik (Login, Registrierung, Passwort-Validierung)
- **includes/storage.php**: Reine Datei- und Speicher-Verwaltungslogik
- **includes/header.php + footer.php**: Reine Darstellungslogik (Templates)
- **Seiten (*.php)**: Verbinden Logik mit Darstellung – jede Seite hat eine klare Aufgabe

## Sicherheitsmerkmale

- Passwörter werden als bcrypt-Hashes gespeichert (`password_hash()`)
- Prepared Statements für alle Datenbankabfragen (SQL-Injection-Schutz)
- HTML-Ausgabe wird mit `htmlspecialchars()` escaped (XSS-Schutz)
- Dateinamen werden sanitisiert (Path-Traversal-Schutz)
- Ordner-Zugriffskontrolle: Nur Eigentümer oder bei Freigabe
- Session-basierte Authentifizierung mit Login-Prüfung auf jeder geschützten Seite
