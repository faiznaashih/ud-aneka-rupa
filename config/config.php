<?php
// ============================================================
// config/config.php - Konfigurasi Database & Global
// ============================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konfigurasi Database
define('DB_HOST', 'sql104.infinityfree.com');
define('DB_USER', 'if0_42148494');
define('DB_PASS', 'Rezzq1388');  // ganti dengan password asli kamu
define('DB_NAME', 'if0_42148494_udanrp');

// Konfigurasi Aplikasi
define('APP_NAME', 'UD Aneka Rupa');
define('APP_URL', 'http://udanekarupa.infinityfreeapp.com');
define('UPLOAD_DIR', __DIR__ . '/../assets/images/produk/');
define('UPLOAD_URL', APP_URL . '/assets/images/produk/');
define('DEFAULT_IMG', APP_URL . '/assets/images/no-image.jpg');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('<div style="padding:20px;background:#fee;color:#c00;font-family:sans-serif;">
        <h3>⚠️ Koneksi Database Gagal</h3>
        <p>' . $conn->connect_error . '</p>
    </div>');
}

$conn->set_charset('utf8mb4');

// ============================================================
// HELPER FUNCTIONS
// ============================================================

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function generateKodePesanan() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        redirect(APP_URL . '/admin/login.php');
    }
}

function getStatusBadge($status) {
    $map = [
        'menunggu'   => ['bg' => 'warning',   'text' => 'Menunggu'],
        'diproses'   => ['bg' => 'info',       'text' => 'Diproses'],
        'dikirim'    => ['bg' => 'primary',    'text' => 'Dikirim'],
        'selesai'    => ['bg' => 'success',    'text' => 'Selesai'],
        'dibatalkan' => ['bg' => 'danger',     'text' => 'Dibatalkan'],
    ];
    return $map[$status] ?? ['bg' => 'secondary', 'text' => ucfirst($status)];
}

function uploadGambar($file, $old_gambar = '') {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return ['success' => false, 'message' => 'Format file tidak didukung.'];
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Ukuran file maksimal 2MB.'];
    }

    $nama_file = 'produk_' . time() . '_' . rand(100, 999) . '.' . $ext;
    $tujuan = UPLOAD_DIR . $nama_file;

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $tujuan)) {
        if ($old_gambar && file_exists(UPLOAD_DIR . $old_gambar)) {
            unlink(UPLOAD_DIR . $old_gambar);
        }
        return ['success' => true, 'filename' => $nama_file];
    }

    return ['success' => false, 'message' => 'Gagal mengupload gambar.'];
}