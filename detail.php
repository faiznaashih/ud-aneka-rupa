<?php
// ============================================================
// detail.php - Halaman Detail Produk
// ============================================================
require_once 'config/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { redirect(APP_URL . '/produk.php'); }

// Ambil data produk
$stmt = $conn->prepare("SELECT * FROM produk WHERE id = ? AND status = 'aktif'");
$stmt->bind_param('i', $id);
$stmt->execute();
$produk = $stmt->get_result()->fetch_assoc();

if (!$produk) {
    redirect(APP_URL . '/produk.php');
}

$page_title = htmlspecialchars($produk['nama_produk']);

// Produk terkait (kategori sama, exclude current)
$stmt2 = $conn->prepare("SELECT * FROM produk WHERE kategori = ? AND id != ? AND status='aktif' LIMIT 3");
$stmt2->bind_param('si', $produk['kategori'], $id);
$stmt2->execute();
$related = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="produk.php">Produk</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($produk['nama_produk']) ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Detail Content -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Gambar Produk -->
            <div class="col-lg-5">
                <div class="detail-img-wrap">
                    <?php
                    $img = !empty($produk['gambar']) && file_exists("assets/images/produk/{$produk['gambar']}")
                        ? "assets/images/produk/{$produk['gambar']}"
                        : "https://placehold.co/600x460/FFF7ED/F97316?text=" . urlencode($produk['nama_produk']);
                    ?>
                    <img src="<?= $img ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
                </div>
                <!-- Badge Kategori -->
                <div class="mt-3 d-flex gap-2 flex-wrap">
                    <span class="badge px-3 py-2" style="background:var(--primary);font-size:.82rem;">
                        <i class="fa-solid fa-tag me-1"></i> <?= ucfirst($produk['kategori']) ?>
                    </span>
                    <?php if ($produk['stok'] > 0): ?>
                    <span class="badge px-3 py-2" style="background:#22C55E;font-size:.82rem;">
                        <i class="fa-solid fa-circle-check me-1"></i> Tersedia
                    </span>
                    <?php else: ?>
                    <span class="badge bg-danger px-3 py-2" style="font-size:.82rem;">
                        <i class="fa-solid fa-circle-xmark me-1"></i> Stok Habis
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info Produk -->
            <div class="col-lg-7">
                <h1 style="font-family:'Playfair Display',serif;font-weight:700;font-size:clamp(1.5rem,3vw,2rem);">
                    <?= htmlspecialchars($produk['nama_produk']) ?>
                </h1>

                <div class="detail-price mb-4"><?= formatRupiah($produk['harga']) ?></div>

                <!-- Spesifikasi -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:var(--light-bg);">
                            <i class="fa-solid fa-weight-scale text-warning d-block mb-1" style="font-size:1.3rem;"></i>
                            <div class="fw-700" style="font-size:.95rem;"><?= $produk['berat_gram'] ?>g</div>
                            <div class="text-muted" style="font-size:.75rem;">Berat</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:var(--light-bg);">
                            <i class="fa-solid fa-layer-group text-warning d-block mb-1" style="font-size:1.3rem;"></i>
                            <div class="fw-700" style="font-size:.95rem;"><?= $produk['stok'] ?></div>
                            <div class="text-muted" style="font-size:.75rem;">Stok</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:var(--light-bg);">
                            <i class="fa-solid fa-fire-flame-curved text-warning d-block mb-1" style="font-size:1.3rem;"></i>
                            <div class="fw-700" style="font-size:.95rem;text-transform:capitalize;"><?= $produk['kategori'] ?></div>
                            <div class="text-muted" style="font-size:.75rem;">Kategori</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:var(--light-bg);">
                            <i class="fa-solid fa-shield-halved text-warning d-block mb-1" style="font-size:1.3rem;"></i>
                            <div class="fw-700" style="font-size:.95rem;">Higienis</div>
                            <div class="text-muted" style="font-size:.75rem;">Kualitas</div>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="mb-4">
                    <h6 class="fw-700 mb-2"><i class="fa-solid fa-align-left me-2 text-warning"></i>Deskripsi Produk</h6>
                    <p class="text-muted" style="line-height:1.8;"><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></p>
                </div>

                <!-- Divider -->
                <hr style="border-color:var(--primary-light);">

                <!-- Form Pesan Cepat -->
                <?php if ($produk['stok'] > 0): ?>
                <div>
                    <h6 class="fw-700 mb-3"><i class="fa-solid fa-cart-shopping me-2 text-warning"></i>Pesan Produk Ini</h6>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <label class="fw-600 mb-0" style="font-size:.9rem;">Jumlah:</label>
                        <div class="qty-control">
                            <button class="qty-btn" type="button" onclick="changeQty('minus')">−</button>
                            <input type="number"
                                   id="jumlah"
                                   class="qty-input"
                                   value="1"
                                   min="1"
                                   max="<?= $produk['stok'] ?>"
                                   oninput="updateTotal()">
                            <input type="hidden" id="harga_satuan" value="<?= $produk['harga'] ?>">
                            <button class="qty-btn" type="button" onclick="changeQty('plus')">+</button>
                        </div>
                        <span class="text-muted small">Maks. <?= $produk['stok'] ?></span>
                    </div>
                    <div class="mb-4 p-3 rounded-3" style="background:var(--light-bg);display:flex;justify-content:space-between;align-items:center;">
                        <span class="fw-600 text-muted">Total Estimasi:</span>
                        <span class="fw-800 fs-5" style="color:var(--primary);" id="total_display"><?= formatRupiah($produk['harga']) ?></span>
                    </div>
                    <a href="pesan.php?id=<?= $produk['id'] ?>&qty=1"
                       id="btn-pesan-link"
                       class="btn-primary-custom d-inline-flex w-100 justify-content-center py-3">
                        <i class="fa-solid fa-cart-plus me-2"></i> Pesan Sekarang
                    </a>
                </div>
                <?php else: ?>
                <div class="alert alert-danger rounded-3">
                    <i class="fa-solid fa-circle-xmark me-2"></i>
                    Maaf, stok produk ini sedang habis. Silakan pilih produk lain.
                </div>
                <a href="produk.php" class="btn-outline-custom d-inline-flex">
                    <i class="fa-solid fa-arrow-left me-2"></i> Lihat Produk Lain
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Produk Terkait -->
        <?php if (!empty($related)): ?>
        <div class="mt-5 pt-4" style="border-top:2px solid var(--primary-light);">
            <h4 class="mb-4 fw-700">
                <i class="fa-solid fa-fire-flame-curved me-2" style="color:var(--primary);"></i>
                Produk Kategori <?= ucfirst($produk['kategori']) ?> Lainnya
            </h4>
            <div class="row g-4">
                <?php foreach ($related as $r): ?>
                <div class="col-md-4">
                    <div class="product-card">
                        <div class="product-img-wrap">
                            <?php $rimg = !empty($r['gambar']) && file_exists("assets/images/produk/{$r['gambar']}")
                                ? "assets/images/produk/{$r['gambar']}"
                                : "https://placehold.co/400x300/FFF7ED/F97316?text=" . urlencode(substr($r['nama_produk'],0,15)); ?>
                            <img src="<?= $rimg ?>" alt="<?= htmlspecialchars($r['nama_produk']) ?>" loading="lazy">
                            <span class="product-badge"><?= ucfirst($r['kategori']) ?></span>
                        </div>
                        <div class="product-body">
                            <h5 class="product-name"><?= htmlspecialchars($r['nama_produk']) ?></h5>
                            <div class="product-meta">
                                <span class="product-price"><?= formatRupiah($r['harga']) ?></span>
                                <span class="product-weight"><?= $r['berat_gram'] ?>g</span>
                            </div>
                            <div class="product-actions">
                                <a href="detail.php?id=<?= $r['id'] ?>" class="btn-detail">Detail</a>
                                <a href="pesan.php?id=<?= $r['id'] ?>" class="btn-order-sm">Pesan</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$extra_js = <<<JS
<script>
// Update link pesan saat qty berubah
document.getElementById('jumlah')?.addEventListener('input', function() {
    const qty = this.value || 1;
    const link = document.getElementById('btn-pesan-link');
    if (link) link.href = 'pesan.php?id={$produk['id']}&qty=' + qty;
    updateTotal();
});
</script>
JS;
include 'includes/footer.php';
?>
