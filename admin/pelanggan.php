<?php
// ============================================================
// admin/pelanggan.php - Data Pelanggan
// ============================================================
require_once '../config/config.php';
requireAdminLogin();

$page_title = 'Data Pelanggan';

$search   = sanitize($_GET['search'] ?? '');
$per_page = 10;
$cur_page = max(1, (int)($_GET['page'] ?? 1));
$offset   = ($cur_page - 1) * $per_page;

$where = $search ? "HAVING nama_pelanggan LIKE '%$search%' OR no_hp LIKE '%$search%'" : '';

$sql_count = "SELECT COUNT(*) as c FROM (
    SELECT no_hp FROM pesanan GROUP BY no_hp $where
) t";
$total = $conn->query($sql_count)->fetch_assoc()['c'];
$total_pages = ceil($total / $per_page);

$sql = "SELECT
    nama_pelanggan,
    no_hp,
    alamat,
    COUNT(*) as total_pesanan,
    SUM(total_harga) as total_belanja,
    MAX(created_at) as pesanan_terakhir
FROM pesanan
GROUP BY no_hp, nama_pelanggan, alamat
$where
ORDER BY total_pesanan DESC
LIMIT $per_page OFFSET $offset";

$pelanggan_list = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

include 'includes/header_admin.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h6 class="page-card-title">
            <i class="fa-solid fa-users me-2" style="color:var(--primary);"></i>
            Data Pelanggan (<?= $total ?>)
        </h6>
    </div>

    <!-- Search -->
    <form method="GET" class="mb-3">
        <div class="d-flex gap-2" style="max-width:380px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama / nomor HP..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn-orange px-3" style="border-radius:10px;"><i class="fa-solid fa-search"></i></button>
            <?php if ($search): ?><a href="pelanggan.php" class="btn btn-outline-secondary" style="border-radius:10px;">Reset</a><?php endif; ?>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Pelanggan</th>
                    <th>Nomor HP</th>
                    <th>Alamat</th>
                    <th>Total Pesanan</th>
                    <th>Total Belanja</th>
                    <th>Pesanan Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pelanggan_list)): ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">Data pelanggan tidak ditemukan</td></tr>
                <?php else: ?>
                <?php foreach ($pelanggan_list as $i => $c): ?>
                <tr>
                    <td class="text-muted small"><?= $offset + $i + 1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--primary),#FBBF24);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.85rem;flex-shrink:0;">
                                <?= strtoupper(substr($c['nama_pelanggan'], 0, 1)) ?>
                            </div>
                            <span class="fw-600 small"><?= htmlspecialchars($c['nama_pelanggan']) ?></span>
                        </div>
                    </td>
                    <td class="small">
                        <a href="https://wa.me/62<?= ltrim($c['no_hp'], '0') ?>" target="_blank" class="text-success fw-600" style="text-decoration:none;">
                            <i class="fab fa-whatsapp me-1"></i><?= htmlspecialchars($c['no_hp']) ?>
                        </a>
                    </td>
                    <td class="small text-muted" style="max-width:180px;">
                        <span style="overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                            <?= htmlspecialchars($c['alamat']) ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary rounded-pill"><?= $c['total_pesanan'] ?> pesanan</span>
                    </td>
                    <td class="fw-700" style="color:var(--primary);font-size:.85rem;"><?= formatRupiah($c['total_belanja']) ?></td>
                    <td class="small text-muted"><?= date('d M Y', strtotime($c['pesanan_terakhir'])) ?></td>
                    <td>
                        <a href="pesanan.php?search=<?= urlencode($c['no_hp']) ?>"
                           class="btn btn-sm btn-outline-primary fw-600" style="border-radius:6px;font-size:.75rem;">
                            <i class="fa-solid fa-eye me-1"></i> Riwayat
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav class="mt-3">
        <ul class="pagination pagination-sm justify-content-end mb-0">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i === $cur_page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?><?= $search ? '&search='.urlencode($search) : '' ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php include 'includes/footer_admin.php'; ?>
