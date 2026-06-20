<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME ?></title>

    <!-- LOCAL CSS (tidak pakai CDN) -->
    <link href="<?= APP_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= APP_URL ?>/assets/css/fontawesome.min.css" rel="stylesheet">
    <link href="<?= APP_URL ?>/assets/css/sweetalert2.min.css" rel="stylesheet">
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
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= APP_URL ?>">
            <div class="brand-icon">
                <i class="fa-solid fa-cookie-bite"></i>
            </div>
            <div>
                <span class="brand-name">UD Aneka Rupa</span>
                <small class="brand-tagline d-block">Pabrik Kerupuk</small>
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

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