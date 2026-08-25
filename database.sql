-- ================================================
-- DATABASE: buzzer_documentation
-- Jalankan semua query ini di phpMyAdmin (tab SQL)
-- ================================================

CREATE DATABASE IF NOT EXISTS buzzer_documentation;
USE buzzer_documentation;

-- ================================
-- TABEL USERS
-- ================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Untuk database yang sudah ada, jalankan ini:
-- ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT NULL AFTER role;

-- ================================
-- TABEL REPORTS (dokumentasi buzzer)
-- ================================
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    sosmed VARCHAR(50) NOT NULL,
    nama_akun_buzzer VARCHAR(100) NOT NULL,
    tanggal DATE NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ================================
-- TABEL SCREENSHOT (relasi 1 report -> banyak gambar)
-- ================================
CREATE TABLE report_screenshots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE
);

-- ================================
-- SEED ADMIN DEFAULT
-- username: admin
-- password: admin123  (plain text, sesuai permintaan tanpa security)
-- ================================
INSERT INTO users (username, email, password, role)
VALUES ('admin', 'admin@buzzerdoc.local', 'admin123', 'admin');
