<?php
// ============================================================
// produk.php - Halaman Daftar Produk
// ============================================================
require_once 'config/config.php';

$page_title = 'Produk Kami';

// Parameter pencarian & filter
$search   = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? sanitize($_GET['kategori']) : '';

// Pagination
$per_page    = 6;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset      = ($current_page - 1) * $per_page;

// Build query WHERE
$where = "WHERE status = 'aktif'";
$params = [];
$types  = '';

if ($search) {
    $where .= " AND (nama_produk LIKE ? OR deskripsi LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}
if ($kategori && $kategori !== 'semua') {
    $where .= " AND kategori = ?";
    $params[] = $kategori;
    $types .= 's';
}

// Count total
$sql_count = "SELECT COUNT(*) as total FROM produk $where";
$stmt_count = $conn->prepare($sql_count);
if ($params) $stmt_count->bind_param($types, ...$params);
$stmt_count->execute();
$total_rows = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $per_page);

// Get products
$sql = "SELECT * FROM produk $where ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$params_page = $params;
$params_page[] = $per_page;
$params_page[] = $offset;
$types_page = $types . 'ii';
$stmt->bind_param($types_page, ...$params_page);
$stmt->execute();
$produk_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>">Beranda</a></li>
                <li class="breadcrumb-item active">Produk</li>
            </ol>
        </nav>
        <h1 class="mb-1"><i class="fa-solid fa-boxes-stacked text-primary me-2" style="color:var(--primary)!important;"></i> Produk Kami</h1>
        <p class="text-muted mb-0">Temukan berbagai pilihan kerupuk lezat dari UD Aneka Rupa</p>
    </div>
</section>

<!-- Main Content -->
<section class="py-5">
    <div class="container">
        <!-- Search & Filter Bar -->
        <div class="card border-0 shadow-sm mb-4 p-3" style="border-radius:var(--radius);">
            <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between">
                <!-- Search -->
                <form method="GET" action="produk.php" class="search-wrap flex-grow-1">
                    <?php if ($kategori): ?>
                    <input type="hidden" name="kategori" value="<?= htmlspecialchars($kategori) ?>">
                    <?php endif; ?>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Cari produk kerupuk..."
                           value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="search-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>

                <!-- Filter Kategori -->
                <div class="d-flex gap-2 flex-wrap">
                    <?php
                    $kategori_list = ['semua' => 'Semua', 'original' => 'Original', 'pedas' => 'Pedas', 'gurih' => 'Gurih', 'manis' => 'Manis'];
                    foreach ($kategori_list as $k => $label):
                        $isActive = ($k === $kategori || ($k === 'semua' && !$kategori)) ? 'active' : '';
                        $url = "produk.php?" . ($search ? "search=" . urlencode($search) . "&" : "") . "kategori=" . ($k === 'semua' ? '' : $k);
                    ?>
                    <a href="<?= $url ?>" class="filter-btn <?= $isActive ?>"><?= $label ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Result Info -->
        <?php if ($search || $kategori): ?>
        <div class="mb-3 d-flex align-items-center justify-content-between">
            <p class="mb-0 text-muted small">
                Menampilkan <strong><?= $total_rows ?></strong> produk
                <?= $search ? "untuk pencarian \"<strong>$search</strong>\"" : '' ?>
                <?= $kategori ? "kategori <strong>" . ucfirst($kategori) . "</strong>" : '' ?>
            </p>
            <a href="produk.php" class="btn btn-sm btn-outline-secondary" style="border-radius:50px;">
                <i class="fa-solid fa-xmark me-1"></i> Reset
            </a>
        </div>
        <?php endif; ?>

        <!-- Product Grid -->
        <?php if (empty($produk_list)): ?>
        <div class="empty-state">
            <i class="fa-solid fa-magnifying-glass d-block mb-3"></i>
            <h5>Produk Tidak Ditemukan</h5>
            <p>Coba kata kunci atau filter yang berbeda</p>
            <a href="produk.php" class="btn-primary-custom d-inline-flex mt-2">Reset Pencarian</a>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($produk_list as $p): ?>
            <div class="col-lg-4 col-md-6">
                <div class="product-card h-100"
                     data-name="<?= strtolower(htmlspecialchars($p['nama_produk'])) ?>"
                     data-kategori="<?= $p['kategori'] ?>">
                    <div class="product-img-wrap">
                        <?php
                        $img = !empty($p['gambar']) && file_exists("assets/images/produk/{$p['gambar']}")
                            ? "assets/images/produk/{$p['gambar']}"
                            : "https://placehold.co/400x300/FFF7ED/F97316?text=" . urlencode(substr($p['nama_produk'], 0, 15));
                        ?>
                        <img src="<?= $img ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>" loading="lazy">
                        <span class="product-badge"><?= ucfirst($p['kategori']) ?></span>
                    </div>
                    <div class="product-body">
                        <h5 class="product-name"><?= htmlspecialchars($p['nama_produk']) ?></h5>
                        <p class="product-desc"><?= htmlspecialchars($p['deskripsi']) ?></p>
                        <div class="product-meta">
                            <span class="product-price"><?= formatRupiah($p['harga']) ?></span>
                            <span class="product-weight"><i class="fa-solid fa-weight-scale me-1"></i><?= $p['berat_gram'] ?>g</span>
                        </div>
                        <div class="product-meta mb-2">
                            <?php if ($p['stok'] > 0): ?>
                            <span class="product-stock"><i class="fa-solid fa-circle-check me-1"></i>Stok: <?= $p['stok'] ?></span>
                            <?php else: ?>
                            <span class="text-danger small fw-600"><i class="fa-solid fa-circle-xmark me-1"></i>Stok Habis</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <a href="detail.php?id=<?= $p['id'] ?>" class="btn-detail">
                                <i class="fa-solid fa-eye me-1"></i> Detail
                            </a>
                            <?php if ($p['stok'] > 0): ?>
                            <a href="pesan.php?id=<?= $p['id'] ?>" class="btn-order-sm">
                                <i class="fa-solid fa-cart-plus me-1"></i> Pesan
                            </a>
                            <?php else: ?>
                            <button class="btn-order-sm" disabled style="opacity:.5;cursor:not-allowed;">
                                Habis
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav class="mt-5 d-flex justify-content-center">
            <ul class="pagination">
                <!-- Prev -->
                <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page - 1])) ?>">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                </li>
                <!-- Pages -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                <!-- Next -->
                <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page + 1])) ?>">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <p class="text-center text-muted small mt-2">Halaman <?= $current_page ?> dari <?= $total_pages ?></p>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
