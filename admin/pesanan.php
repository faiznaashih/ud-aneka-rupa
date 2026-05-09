<?php
// ============================================================
// admin/pesanan.php - Kelola Pesanan
// ============================================================
require_once '../config/config.php';
requireAdminLogin();

$page_title = 'Kelola Pesanan';

// ---- Update Status ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $pid    = (int)$_POST['pesanan_id'];
    $status = sanitize($_POST['status']);
    $valid  = ['menunggu','diproses','dikirim','selesai','dibatalkan'];
    if (in_array($status, $valid)) {
        $upd = $conn->prepare("UPDATE pesanan SET status=? WHERE id=?");
        $upd->bind_param('si', $status, $pid);
        $upd->execute();
        $_SESSION['flash'] = ['type' => 'success', 'title' => 'Status pesanan diperbarui!'];
    }
    redirect(APP_URL . '/admin/pesanan.php' . (isset($_GET['status']) ? '?status='.$_GET['status'] : ''));
}

// ---- Filter & Pagination ----
$filter  = sanitize($_GET['status'] ?? '');
$search  = sanitize($_GET['search'] ?? '');
$per_page = 10;
$cur_page = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($cur_page - 1) * $per_page;

$where_parts = [];
$params = [];
$types  = '';

if ($filter && $filter !== 'semua') {
    $where_parts[] = "p.status = ?";
    $params[] = $filter;
    $types .= 's';
}
if ($search) {
    $where_parts[] = "(p.kode_pesanan LIKE ? OR p.nama_pelanggan LIKE ? OR p.no_hp LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}
$where = $where_parts ? 'WHERE ' . implode(' AND ', $where_parts) : '';

// Count
$count_sql = "SELECT COUNT(*) as c FROM pesanan p $where";
$stmt_c = $conn->prepare($count_sql);
if ($params) $stmt_c->bind_param($types, ...$params);
$stmt_c->execute();
$total = $stmt_c->get_result()->fetch_assoc()['c'];
$total_pages = ceil($total / $per_page);

// Get pesanan
$sql = "SELECT p.*, pr.nama_produk, pr.gambar FROM pesanan p JOIN produk pr ON p.id_produk = pr.id $where ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$params_p = $params;
$params_p[] = $per_page;
$params_p[] = $offset;
$types_p = $types . 'ii';
$stmt->bind_param($types_p, ...$params_p);
$stmt->execute();
$pesanan_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Count per status for tabs
$counts = [];
foreach (['menunggu','diproses','dikirim','selesai','dibatalkan'] as $s) {
    $r = $conn->query("SELECT COUNT(*) as c FROM pesanan WHERE status='$s'")->fetch_assoc();
    $counts[$s] = $r['c'];
}
$counts['semua'] = array_sum($counts);

include 'includes/header_admin.php';
?>

<!-- Status Tabs -->
<div class="page-card mb-3 py-2">
    <div class="d-flex flex-wrap gap-2">
        <?php
        $tab_list = ['semua' => 'Semua', 'menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'];
        $tab_colors = ['semua' => 'secondary','menunggu' => 'warning','diproses' => 'info','dikirim' => 'primary','selesai' => 'success','dibatalkan' => 'danger'];
        foreach ($tab_list as $k => $label):
            $active = ($filter === $k || (!$filter && $k === 'semua')) ? 'active' : '';
            $url = 'pesanan.php?status=' . ($k === 'semua' ? '' : $k) . ($search ? '&search='.urlencode($search) : '');
        ?>
        <a href="<?= $url ?>"
           class="btn btn-sm <?= $active ? 'btn-'.$tab_colors[$k] : 'btn-outline-'.$tab_colors[$k] ?> fw-600"
           style="border-radius:50px;font-size:.78rem;">
            <?= $label ?>
            <span class="badge bg-white text-dark ms-1"><?= $counts[$k] ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- List Pesanan -->
<div class="page-card">
    <div class="page-card-header">
        <h6 class="page-card-title">
            <i class="fa-solid fa-receipt me-2" style="color:var(--primary);"></i>
            Daftar Pesanan <?= $filter ? '— '.ucfirst($filter) : '' ?> (<?= $total ?>)
        </h6>
    </div>

    <!-- Search -->
    <form method="GET" class="mb-3">
        <?php if ($filter): ?><input type="hidden" name="status" value="<?= htmlspecialchars($filter) ?>"><?php endif; ?>
        <div class="d-flex gap-2" style="max-width:400px;">
            <input type="text" name="search" class="form-control" placeholder="Cari kode/nama/HP..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn-orange px-3" style="border-radius:10px;"><i class="fa-solid fa-search"></i></button>
            <?php if ($search): ?><a href="pesanan.php<?= $filter ? '?status='.$filter : '' ?>" class="btn btn-outline-secondary" style="border-radius:10px;">Reset</a><?php endif; ?>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Jml</th>
                    <th>Total</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pesanan_list)): ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada pesanan</td></tr>
                <?php else: ?>
                <?php foreach ($pesanan_list as $i => $p): ?>
                <tr>
                    <td class="text-muted small"><?= $offset + $i + 1 ?></td>
                    <td>
                        <code style="color:var(--primary);font-size:.78rem;"><?= htmlspecialchars($p['kode_pesanan']) ?></code>
                    </td>
                    <td>
                        <div class="fw-600 small"><?= htmlspecialchars($p['nama_pelanggan']) ?></div>
                        <div class="text-muted" style="font-size:.72rem;"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($p['no_hp']) ?></div>
                    </td>
                    <td class="small text-muted"><?= htmlspecialchars($p['nama_produk']) ?></td>
                    <td class="text-center fw-600"><?= $p['jumlah'] ?></td>
                    <td class="fw-700" style="color:var(--primary);font-size:.85rem;"><?= formatRupiah($p['total_harga']) ?></td>
                    <td class="small text-muted"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                    <td>
                        <?php $b = getStatusBadge($p['status']); ?>
                        <span class="badge badge-status bg-<?= $b['bg'] ?>"><?= $b['text'] ?></span>
                    </td>
                    <td>
                        <!-- Update Status Modal Trigger -->
                        <button class="btn btn-sm btn-warning" style="border-radius:6px;"
                                onclick="updateStatus(<?= $p['id'] ?>, '<?= $p['status'] ?>')">
                            <i class="fa-solid fa-edit"></i>
                        </button>
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
                <a class="page-link" href="?page=<?= $i ?>&status=<?= urlencode($filter) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<!-- Hidden form for status update -->
<form id="form-update-status" method="POST">
    <input type="hidden" name="update_status" value="1">
    <input type="hidden" name="pesanan_id" id="input-pesanan-id">
    <input type="hidden" name="status" id="input-status">
</form>

<?php
$extra_js = <<<JS
<script>
function updateStatus(id, currentStatus) {
    const options = {
        menunggu:   'Menunggu',
        diproses:   'Diproses',
        dikirim:    'Dikirim',
        selesai:    'Selesai',
        dibatalkan: 'Dibatalkan'
    };

    let inputHtml = '<select id="swal-status" class="swal2-input">';
    for (const [val, label] of Object.entries(options)) {
        inputHtml += `<option value="\${val}" \${val === currentStatus ? 'selected' : ''}>\${label}</option>`;
    }
    inputHtml += '</select>';

    Swal.fire({
        title: 'Update Status Pesanan',
        html: `<p style="color:#78716C;margin-bottom:8px;">Pilih status baru untuk pesanan ini:</p>\${inputHtml}`,
        showCancelButton: true,
        confirmButtonText: 'Update',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#F97316',
        cancelButtonColor: '#78716C',
        preConfirm: () => {
            return document.getElementById('swal-status').value;
        }
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('input-pesanan-id').value = id;
            document.getElementById('input-status').value = result.value;
            document.getElementById('form-update-status').submit();
        }
    });
}
</script>
JS;
include 'includes/footer_admin.php';
?>
