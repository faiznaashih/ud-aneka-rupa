<?php
// ============================================================
// admin/produk.php - CRUD Produk
// ============================================================
require_once '../config/config.php';
requireAdminLogin();

$page_title = 'Kelola Produk';
$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ---- HAPUS PRODUK ----
if ($action === 'hapus' && $id) {
    $p = $conn->query("SELECT gambar FROM produk WHERE id=$id")->fetch_assoc();
    if ($p && $p['gambar'] && file_exists("../assets/images/produk/" . $p['gambar'])) {
        unlink("../assets/images/produk/" . $p['gambar']);
    }
    $conn->query("DELETE FROM produk WHERE id=$id");
    $_SESSION['flash'] = ['type' => 'success', 'title' => 'Produk berhasil dihapus!'];
    redirect(APP_URL . '/admin/produk.php');
}

// ---- SIMPAN (tambah/edit) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = sanitize($_POST['nama_produk']);
    $desc    = sanitize($_POST['deskripsi']);
    $harga   = (float)$_POST['harga'];
    $stok    = (int)$_POST['stok'];
    $berat   = (int)$_POST['berat_gram'];
    $kat     = sanitize($_POST['kategori']);
    $status  = sanitize($_POST['status']);
    $edit_id = (int)($_POST['edit_id'] ?? 0);

    // Upload gambar
    $gambar = $_POST['old_gambar'] ?? '';
    if (!empty($_FILES['gambar']['name'])) {
        $upload = uploadGambar($_FILES['gambar'], $edit_id ? $gambar : '');
        if ($upload['success']) {
            $gambar = $upload['filename'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'title' => 'Upload Gagal', 'text' => $upload['message']];
            redirect(APP_URL . '/admin/produk.php?action=' . ($edit_id ? "edit&id=$edit_id" : 'tambah'));
        }
    }

    if ($edit_id) {
        $stmt = $conn->prepare("UPDATE produk SET nama_produk=?,deskripsi=?,harga=?,stok=?,berat_gram=?,gambar=?,kategori=?,status=? WHERE id=?");
        $stmt->bind_param('ssdiisssi', $nama, $desc, $harga, $stok, $berat, $gambar, $kat, $status, $edit_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO produk (nama_produk,deskripsi,harga,stok,berat_gram,gambar,kategori,status) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param('ssdiisss', $nama, $desc, $harga, $stok, $berat, $gambar, $kat, $status);
    }
    $stmt->execute();
    $_SESSION['flash'] = ['type' => 'success', 'title' => $edit_id ? 'Produk berhasil diperbarui!' : 'Produk berhasil ditambahkan!'];
    redirect(APP_URL . '/admin/produk.php');
}

// ---- GET PRODUK UNTUK EDIT ----
$produk_edit = null;
if ($action === 'edit' && $id) {
    $produk_edit = $conn->query("SELECT * FROM produk WHERE id=$id")->fetch_assoc();
    if (!$produk_edit) redirect(APP_URL . '/admin/produk.php');
}

// ---- LIST dengan search & pagination ----
$search   = sanitize($_GET['search'] ?? '');
$per_page = 10;
$cur_page = max(1, (int)($_GET['page'] ?? 1));
$offset   = ($cur_page - 1) * $per_page;

$where = $search ? "WHERE nama_produk LIKE '%$search%'" : '';
$total = $conn->query("SELECT COUNT(*) as c FROM produk $where")->fetch_assoc()['c'];
$total_pages = ceil($total / $per_page);
$produk_list = $conn->query("SELECT * FROM produk $where ORDER BY id DESC LIMIT $per_page OFFSET $offset")->fetch_all(MYSQLI_ASSOC);

include 'includes/header_admin.php';
?>

<?php if ($action === 'tambah' || $action === 'edit'): ?>
<!-- ===================== FORM TAMBAH/EDIT ===================== -->
<div class="page-card" style="max-width:760px;">
    <div class="page-card-header">
        <h6 class="page-card-title">
            <i class="fa-solid fa-<?= $action === 'tambah' ? 'plus' : 'pen' ?> me-2" style="color:var(--primary);"></i>
            <?= $action === 'tambah' ? 'Tambah Produk Baru' : 'Edit Produk' ?>
        </h6>
        <a href="produk.php" class="btn btn-sm btn-outline-secondary" style="border-radius:50px;">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <form method="POST" action="produk.php" enctype="multipart/form-data">
        <input type="hidden" name="edit_id" value="<?= $produk_edit['id'] ?? 0 ?>">
        <input type="hidden" name="old_gambar" value="<?= $produk_edit['gambar'] ?? '' ?>">

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="nama_produk" class="form-control"
                       value="<?= htmlspecialchars($produk_edit['nama_produk'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select">
                    <?php foreach(['original','pedas','gurih','manis'] as $k): ?>
                    <option value="<?= $k ?>" <?= ($produk_edit['kategori'] ?? '') === $k ? 'selected' : '' ?>><?= ucfirst($k) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi Produk</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($produk_edit['deskripsi'] ?? '') ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="harga" class="form-control" min="0"
                       value="<?= $produk_edit['harga'] ?? '' ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Stok <span class="text-danger">*</span></label>
                <input type="number" name="stok" class="form-control" min="0"
                       value="<?= $produk_edit['stok'] ?? 0 ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Berat (gram)</label>
                <input type="number" name="berat_gram" class="form-control" min="0"
                       value="<?= $produk_edit['berat_gram'] ?? 250 ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Gambar Produk</label>
                <input type="file" name="gambar" class="form-control" accept="image/*"
                       onchange="previewImage(this,'imgPreview')">
                <div class="mt-2">
                    <?php
                    $old_img = $produk_edit['gambar'] ?? '';
                    $prev_src = ($old_img && file_exists("../assets/images/produk/$old_img"))
                        ? APP_URL . "/assets/images/produk/$old_img"
                        : '';
                    ?>
                    <img id="imgPreview"
                         src="<?= $prev_src ?>"
                         class="img-preview"
                         style="<?= $prev_src ? '' : 'display:none;' ?>">
                </div>
                <div class="form-text">Format: JPG/PNG/WEBP. Maks 2MB.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="aktif" <?= ($produk_edit['status'] ?? 'aktif') === 'aktif' ? 'selected' : '' ?>>✅ Aktif</option>
                    <option value="nonaktif" <?= ($produk_edit['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>⛔ Nonaktif</option>
                </select>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn-orange px-4 py-2">
                <i class="fa-solid fa-floppy-disk me-2"></i>
                <?= $action === 'tambah' ? 'Simpan Produk' : 'Update Produk' ?>
            </button>
            <a href="produk.php" class="btn btn-outline-secondary px-4 py-2" style="border-radius:8px;">Batal</a>
        </div>
    </form>
</div>

<?php else: ?>
<!-- ===================== LIST PRODUK ===================== -->
<div class="page-card">
    <div class="page-card-header">
        <h6 class="page-card-title"><i class="fa-solid fa-boxes-stacked me-2" style="color:var(--primary);"></i>Daftar Produk (<?= $total ?>)</h6>
        <a href="produk.php?action=tambah" class="btn-orange" style="border-radius:50px;padding:8px 18px;font-size:.83rem;">
            <i class="fa-solid fa-plus me-1"></i> Tambah Produk
        </a>
    </div>

    <!-- Search -->
    <form method="GET" class="mb-3">
        <div class="d-flex gap-2" style="max-width:360px;">
            <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn-orange px-3" style="border-radius:10px;"><i class="fa-solid fa-search"></i></button>
            <?php if ($search): ?><a href="produk.php" class="btn btn-outline-secondary" style="border-radius:10px;">Reset</a><?php endif; ?>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($produk_list)): ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">Produk tidak ditemukan</td></tr>
                <?php else: ?>
                <?php foreach ($produk_list as $i => $p): ?>
                <tr>
                    <td class="text-muted small"><?= $offset + $i + 1 ?></td>
                    <td>
                        <?php $img = !empty($p['gambar']) && file_exists("../assets/images/produk/{$p['gambar']}")
                            ? APP_URL . "/assets/images/produk/{$p['gambar']}"
                            : "https://placehold.co/80x80/FFF7ED/F97316?text=IMG"; ?>
                        <img src="<?= $img ?>" class="img-preview" alt="">
                    </td>
                    <td>
                        <div class="fw-600"><?= htmlspecialchars($p['nama_produk']) ?></div>
                        <div class="text-muted small"><?= $p['berat_gram'] ?>g</div>
                    </td>
                    <td><span class="badge bg-secondary text-white"><?= ucfirst($p['kategori']) ?></span></td>
                    <td class="fw-700" style="color:var(--primary);"><?= formatRupiah($p['harga']) ?></td>
                    <td>
                        <span class="badge <?= $p['stok'] <= 5 ? 'bg-danger' : ($p['stok'] <= 20 ? 'bg-warning text-dark' : 'bg-success') ?>">
                            <?= $p['stok'] ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($p['status'] === 'aktif'): ?>
                        <span class="badge badge-status bg-success">Aktif</span>
                        <?php else: ?>
                        <span class="badge badge-status bg-secondary">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="produk.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-warning" title="Edit" style="border-radius:6px;">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form id="del-prod-<?= $p['id'] ?>" action="produk.php?action=hapus&id=<?= $p['id'] ?>" method="GET">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" style="border-radius:6px;"
                                        onclick="confirmDelete('del-prod-<?= $p['id'] ?>')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
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
                <a class="page-link" href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include 'includes/footer_admin.php'; ?>
