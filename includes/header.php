<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= APP_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Loading Overlay -->
<div id="loading-overlay">
    <div class="loader-wrap">
        <div class="loader-circle"></div>
        <p class="loader-text">Memuat...</p>
    </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top" id="mainNavbar">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= APP_URL ?>">
            <div class="brand-icon">
                <i class="fa-solid fa-cookie-bite"></i>
            </div>
            <div>
                <span class="brand-name">UD Aneka Rupa</span>
                <small class="brand-tagline d-block">Pabrik Kerupuk</small>
            </div>
        </a>

        <!-- Toggler Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Nav Links -->
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="<?= APP_URL ?>">
                        <i class="fa-solid fa-house me-1"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'produk.php' ? 'active' : '' ?>" href="<?= APP_URL ?>/produk.php">
                        <i class="fa-solid fa-boxes-stacked me-1"></i> Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'cek_status.php' ? 'active' : '' ?>" href="<?= APP_URL ?>/cek_status.php">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Cek Pesanan
                    </a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-order" href="<?= APP_URL ?>/produk.php">
                        <i class="fa-solid fa-cart-shopping me-1"></i> Pesan Sekarang
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
