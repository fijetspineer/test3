-- ============================================================
-- Datenbank-Setup für BSN Klassen-Plattform
-- ============================================================

CREATE DATABASE IF NOT EXISTS bsn_platform
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bsn_platform;

-- ============================================================
-- Tabelle: users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,  -- bcrypt hash
    storage_limit BIGINT NOT NULL DEFAULT 10485760,  -- 10 MB default
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Tabelle: files
-- ============================================================
CREATE TABLE IF NOT EXISTS files (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT          NOT NULL,
    filename    VARCHAR(255) NOT NULL,
    filepath    VARCHAR(500) NOT NULL,
    filesize    BIGINT       NOT NULL DEFAULT 0,
    folder      VARCHAR(255) NOT NULL DEFAULT '',
    uploaded_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Tabelle: shares  (Ordnerfreigaben)
-- ============================================================
CREATE TABLE IF NOT EXISTS shares (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT          NOT NULL,
    folder      VARCHAR(255) NOT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_share (user_id, folder),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
