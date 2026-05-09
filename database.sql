-- ============================================================
-- DATABASE: ud_aneka_rupa
-- Website Sistem Informasi & Pemesanan Kerupuk
-- ============================================================

CREATE DATABASE IF NOT EXISTS ud_aneka_rupa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ud_aneka_rupa;

-- ============================================================
-- TABEL: admin
-- ============================================================
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: produk
-- ============================================================
CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    berat_gram INT COMMENT 'Berat per kemasan dalam gram',
    gambar VARCHAR(255),
    kategori ENUM('original','pedas','manis','gurih') DEFAULT 'original',
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: pesanan
-- ============================================================
CREATE TABLE pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_pesanan VARCHAR(20) NOT NULL UNIQUE,
    nama_pelanggan VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    alamat TEXT NOT NULL,
    id_produk INT NOT NULL,
    jumlah INT NOT NULL,
    total_harga DECIMAL(12,2) NOT NULL,
    catatan TEXT,
    status ENUM('menunggu','diproses','dikirim','selesai','dibatalkan') DEFAULT 'menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- DATA DUMMY: admin
-- Password: admin123 (hashed dengan password_hash PHP)
-- ============================================================
INSERT INTO admin (nama, username, password) VALUES
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ============================================================
-- DATA DUMMY: produk
-- ============================================================
INSERT INTO produk (nama_produk, deskripsi, harga, stok, berat_gram, gambar, kategori, status) VALUES
('Kerupuk Udang Original', 'Kerupuk udang renyah dengan cita rasa original. Dibuat dari udang pilihan yang segar dan diproses secara higienis. Cocok untuk camilan keluarga maupun teman makan.', 15000, 100, 250, 'kerupuk-udang.jpg', 'original', 'aktif'),
('Kerupuk Ikan Pedas', 'Kerupuk ikan dengan bumbu pedas yang menggugah selera. Menggunakan ikan laut segar pilihan dengan tambahan cabai merah berkualitas tinggi.', 18000, 80, 250, 'kerupuk-ikan-pedas.jpg', 'pedas', 'aktif'),
('Kerupuk Bawang Renyah', 'Kerupuk bawang dengan aroma dan rasa bawang yang kuat. Tekstur renyah dan gurih, cocok sebagai pelengkap nasi atau camilan.', 12000, 150, 200, 'kerupuk-bawang.jpg', 'gurih', 'aktif'),
('Kerupuk Udang Jumbo', 'Kerupuk udang ukuran jumbo dengan rasa yang lebih kuat. Kemasan premium 500 gram untuk keluarga besar.', 28000, 60, 500, 'kerupuk-udang-jumbo.jpg', 'original', 'aktif'),
('Kerupuk Manis Susu', 'Kerupuk unik dengan rasa manis susu. Cocok untuk anak-anak dan yang menyukai camilan manis.', 14000, 90, 200, 'kerupuk-manis.jpg', 'manis', 'aktif'),
('Kerupuk Ikan Original', 'Kerupuk ikan dengan bumbu original tanpa bahan pengawet. Menggunakan resep tradisional turun-temurun keluarga.', 16000, 120, 250, 'kerupuk-ikan.jpg', 'original', 'aktif');

-- ============================================================
-- DATA DUMMY: pesanan
-- ============================================================
INSERT INTO pesanan (kode_pesanan, nama_pelanggan, no_hp, alamat, id_produk, jumlah, total_harga, catatan, status) VALUES
('ORD-20240101-001', 'Budi Santoso', '081234567890', 'Jl. Mawar No. 10, Surabaya', 1, 5, 75000, 'Tolong dikemas rapi', 'selesai'),
('ORD-20240102-002', 'Siti Rahayu', '082345678901', 'Jl. Melati No. 5, Sidoarjo', 2, 3, 54000, '', 'dikirim'),
('ORD-20240103-003', 'Ahmad Fauzi', '083456789012', 'Jl. Kenanga No. 8, Gresik', 3, 10, 120000, 'Minta nota', 'diproses'),
('ORD-20240104-004', 'Dewi Lestari', '084567890123', 'Jl. Anggrek No. 3, Surabaya', 4, 2, 56000, '', 'menunggu'),
('ORD-20240105-005', 'Eko Prasetyo', '085678901234', 'Jl. Dahlia No. 15, Mojokerto', 5, 4, 56000, 'Jangan terlalu manis', 'menunggu');
