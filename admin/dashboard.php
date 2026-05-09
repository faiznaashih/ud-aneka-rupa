<?php
// ============================================================
// admin/dashboard.php - Dashboard Admin
// ============================================================
require_once '../config/config.php';
requireAdminLogin();

$page_title = 'Dashboard';

// Statistik
$total_produk   = $conn->query("SELECT COUNT(*) as c FROM produk WHERE status='aktif'")->fetch_assoc()['c'];
$total_pesanan  = $conn->query("SELECT COUNT(*) as c FROM pesanan")->fetch_assoc()['c'];
$pesanan_baru   = $conn->query("SELECT COUNT(*) as c FROM pesanan WHERE status='menunggu'")->fetch_assoc()['c'];
$total_pendapatan = $conn->query("SELECT COALESCE(SUM(total_harga),0) as t FROM pesanan WHERE status='selesai'")->fetch_assoc()['t'];
$pelanggan_unik = $conn->query("SELECT COUNT(DISTINCT no_hp) as c FROM pesanan")->fetch_assoc()['c'];

// 5 Pesanan terbaru
$pesanan_terbaru = $conn->query("
    SELECT p.*, pr.nama_produk FROM pesanan p
    JOIN produk pr ON p.id_produk = pr.id
    ORDER BY p.created_at DESC LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// 5 Produk stok rendah
$stok_rendah = $conn->query("SELECT * FROM produk WHERE stok < 20 AND status='aktif' ORDER BY stok ASC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

include 'includes/header_admin.php';
?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="border-left-color:#F97316;">
            <div class="stat-card-icon" style="background:#FFF7ED;color:#F97316;"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div class="stat-num"><?= $total_produk ?></div>
            <div class="stat-label">Produk Aktif</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="border-left-color:#3B82F6;">
            <div class="stat-card-icon" style="background:#EFF6FF;color:#3B82F6;"><i class="fa-solid fa-receipt"></i></div>
            <div class="stat-num"><?= $total_pesanan ?></div>
            <div class="stat-label">Total Pesanan</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="border-left-color:#FBBF24;">
            <div class="stat-card-icon" style="background:#FFFBEB;color:#D97706;"><i class="fa-solid fa-bell"></i></div>
            <div class="stat-num"><?= $pesanan_baru ?></div>
            <div class="stat-label">Pesanan Menunggu</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="border-left-color:#22C55E;">
            <div class="stat-card-icon" style="background:#F0FDF4;color:#22C55E;"><i class="fa-solid fa-money-bill-wave"></i></div>
            <div class="stat-num" style="font-size:1.3rem;"><?= formatRupiah($total_pendapatan) ?></div>
            <div class="stat-label">Total Pendapatan</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Pesanan Terbaru -->
    <div class="col-lg-8">
        <div class="page-card">
            <div class="page-card-header">
                <h6 class="page-card-title"><i class="fa-solid fa-receipt me-2" style="color:var(--primary);"></i>Pesanan Terbaru</h6>
                <a href="pesanan.php" class="btn-orange" style="border-radius:50px;padding:6px 16px;font-size:.8rem;">
                    Lihat Semua
                </a>
            </div>
            <?php if (empty($pesanan_terbaru)): ?>
            <p class="text-muted text-center py-4">Belum ada pesanan</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Produk</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pesanan_terbaru as $p): ?>
                        <tr>
                            <td>
                                <code class="text-warning" style="font-size:.78rem;"><?= htmlspecialchars($p['kode_pesanan']) ?></code>
                            </td>
                            <td>
                                <div class="fw-600"><?= htmlspecialchars($p['nama_pelanggan']) ?></div>
                                <div class="text-muted small"><?= date('d M Y', strtotime($p['created_at'])) ?></div>
                            </td>
                            <td class="text-muted small"><?= htmlspecialchars($p['nama_produk']) ?></td>
                            <td class="fw-700" style="color:var(--primary);"><?= formatRupiah($p['total_harga']) ?></td>
                            <td>
                                <?php $b = getStatusBadge($p['status']); ?>
                                <span class="badge badge-status bg-<?= $b['bg'] ?>"><?= $b['text'] ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar Widgets -->
    <div class="col-lg-4">
        <!-- Stok Rendah -->
        <div class="page-card mb-4">
            <div class="page-card-header">
                <h6 class="page-card-title"><i class="fa-solid fa-triangle-exclamation me-2" style="color:#FBBF24;"></i>Stok Rendah</h6>
            </div>
            <?php if (empty($stok_rendah)): ?>
            <p class="text-muted small text-center py-2">Semua stok aman ✓</p>
            <?php else: ?>
            <?php foreach ($stok_rendah as $p): ?>
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom:1px solid #F5F5F4;">
                <div>
                    <div class="fw-600" style="font-size:.85rem;"><?= htmlspecialchars($p['nama_produk']) ?></div>
                    <div class="text-muted" style="font-size:.75rem;"><?= ucfirst($p['kategori']) ?></div>
                </div>
                <span class="badge <?= $p['stok'] <= 5 ? 'bg-danger' : 'bg-warning text-dark' ?> rounded-pill px-3 py-2">
                    <?= $p['stok'] ?> tersisa
                </span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Quick Links -->
        <div class="page-card">
            <h6 class="page-card-title mb-3"><i class="fa-solid fa-bolt me-2" style="color:var(--primary);"></i>Aksi Cepat</h6>
            <div class="d-grid gap-2">
                <a href="produk.php?action=tambah" class="btn-orange" style="text-align:center;padding:10px;border-radius:10px;display:block;">
                    <i class="fa-solid fa-plus me-2"></i> Tambah Produk Baru
                </a>
                <a href="pesanan.php?status=menunggu" class="btn btn-outline-warning fw-600" style="border-radius:10px;font-size:.85rem;">
                    <i class="fa-solid fa-bell me-2"></i> Pesanan Menunggu (<?= $pesanan_baru ?>)
                </a>
                <a href="../" target="_blank" class="btn btn-outline-secondary fw-600" style="border-radius:10px;font-size:.85rem;">
                    <i class="fa-solid fa-globe me-2"></i> Preview Website
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>
