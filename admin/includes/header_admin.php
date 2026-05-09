<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' — Admin ' . APP_NAME : 'Admin ' . APP_NAME ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
    :root {
        --primary:      #F97316;
        --primary-dark: #EA580C;
        --light-bg:     #FFF7ED;
        --sidebar-w:    260px;
        --dark:         #1C1917;
        --gray:         #78716C;
    }
    * { box-sizing: border-box; }
    body { font-family: 'Poppins', sans-serif; background: #F5F5F4; margin: 0; }

    /* Sidebar */
    .admin-sidebar {
        position: fixed;
        top: 0; left: 0;
        width: var(--sidebar-w);
        height: 100vh;
        background: var(--dark);
        z-index: 1000;
        overflow-y: auto;
        transition: transform 0.3s ease;
    }
    .sidebar-brand {
        padding: 24px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .brand-logo {
        width: 40px; height: 40px;
        background: linear-gradient(135deg, var(--primary), #FBBF24);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 1rem;
    }
    .sidebar-menu { padding: 16px 0; }
    .menu-label {
        font-size: .68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #78716C;
        padding: 12px 20px 6px;
    }
    .menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 20px;
        color: #A8A29E;
        text-decoration: none;
        font-size: .875rem;
        font-weight: 500;
        transition: all .2s;
        border-left: 3px solid transparent;
    }
    .menu-link:hover, .menu-link.active {
        background: rgba(249,115,22,.08);
        color: var(--primary);
        border-left-color: var(--primary);
    }
    .menu-link i { width: 18px; text-align: center; font-size: .95rem; }

    /* Main Content */
    .admin-main {
        margin-left: var(--sidebar-w);
        min-height: 100vh;
        transition: margin .3s;
    }
    /* Top Bar */
    .admin-topbar {
        background: white;
        padding: 14px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #E7E5E4;
        position: sticky;
        top: 0;
        z-index: 500;
        box-shadow: 0 1px 6px rgba(0,0,0,.05);
    }
    .topbar-title { font-weight: 700; font-size: 1rem; color: var(--dark); }
    .topbar-user {
        display: flex; align-items: center; gap: 10px;
        font-size: .875rem; color: var(--dark);
    }
    .user-avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), #FBBF24);
        display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 700;
    }
    .admin-content { padding: 28px; }

    /* Stats Cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 22px 24px;
        border-left: 5px solid var(--primary);
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        transition: all .3s;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(249,115,22,.14); }
    .stat-card-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        margin-bottom: 14px;
    }
    .stat-num { font-size: 1.9rem; font-weight: 800; color: var(--dark); line-height: 1; }
    .stat-label { color: var(--gray); font-size: .82rem; margin-top: 4px; }

    /* Table */
    .admin-table { border-radius: 14px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
    .admin-table thead th {
        background: var(--dark);
        color: white;
        font-weight: 600;
        font-size: .82rem;
        padding: 14px 16px;
        border: none;
        letter-spacing: .3px;
    }
    .admin-table tbody tr { transition: background .2s; }
    .admin-table tbody tr:hover { background: var(--light-bg); }
    .admin-table tbody td {
        padding: 13px 16px;
        border-color: #F5F5F4;
        font-size: .875rem;
        vertical-align: middle;
    }

    /* Buttons */
    .btn-orange { background: var(--primary); color: white; border: none; border-radius: 8px; padding: 8px 16px; font-size: .83rem; font-weight: 600; transition: all .2s; }
    .btn-orange:hover { background: var(--primary-dark); color: white; transform: translateY(-1px); }
    .badge-status { font-size: .76rem; font-weight: 600; padding: 5px 12px; border-radius: 50px; }

    /* Form controls */
    .form-control, .form-select {
        border: 2px solid #E7E5E4;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: .875rem;
        transition: all .3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(249,115,22,.12);
    }

    /* Page card */
    .page-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        margin-bottom: 24px;
    }
    .page-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #F5F5F4;
    }
    .page-card-title { font-weight: 700; font-size: 1rem; color: var(--dark); margin: 0; }

    /* Product image preview */
    .img-preview {
        width: 60px; height: 60px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid #E7E5E4;
    }

    /* Mobile sidebar toggle */
    .sidebar-toggle { display: none; }
    @media (max-width: 768px) {
        .admin-sidebar { transform: translateX(-100%); }
        .admin-sidebar.show { transform: translateX(0); }
        .admin-main { margin-left: 0; }
        .sidebar-toggle { display: inline-flex; }
        .admin-content { padding: 16px; }
    }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2">
            <div class="brand-logo"><i class="fa-solid fa-cookie-bite"></i></div>
            <div>
                <div class="text-white fw-700" style="font-size:.9rem;">UD Aneka Rupa</div>
                <div class="text-warning" style="font-size:.68rem;">Admin Panel</div>
            </div>
        </div>
    </div>

    <!-- Menu -->
    <div class="sidebar-menu">
        <div class="menu-label">Utama</div>
        <a href="<?= APP_URL ?>/admin/dashboard.php" class="menu-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>

        <div class="menu-label mt-2">Kelola Data</div>
        <a href="<?= APP_URL ?>/admin/produk.php" class="menu-link <?= basename($_SERVER['PHP_SELF']) === 'produk.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-boxes-stacked"></i> Produk
        </a>
        <a href="<?= APP_URL ?>/admin/pesanan.php" class="menu-link <?= basename($_SERVER['PHP_SELF']) === 'pesanan.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-receipt"></i> Pesanan
        </a>
        <a href="<?= APP_URL ?>/admin/pelanggan.php" class="menu-link <?= basename($_SERVER['PHP_SELF']) === 'pelanggan.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-users"></i> Pelanggan
        </a>

        <div class="menu-label mt-2">Akun</div>
        <a href="<?= APP_URL ?>" class="menu-link" target="_blank">
            <i class="fa-solid fa-globe"></i> Lihat Website
        </a>
        <a href="<?= APP_URL ?>/admin/logout.php" class="menu-link" style="color:#EF4444 !important;">
            <i class="fa-solid fa-right-from-bracket"></i> Keluar
        </a>
    </div>
</aside>

<!-- Main -->
<main class="admin-main">
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary sidebar-toggle" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
                <i class="fa-solid fa-bars"></i>
            </button>
            <span class="topbar-title"><?= isset($page_title) ? $page_title : 'Dashboard' ?></span>
        </div>
        <div class="topbar-user">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['admin_nama'] ?? 'A', 0, 1)) ?></div>
            <div class="d-none d-md-block">
                <div class="fw-600" style="font-size:.85rem;"><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></div>
                <div class="text-muted" style="font-size:.72rem;">Administrator</div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="admin-content">
