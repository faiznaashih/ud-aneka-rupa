<?php
// ============================================================
// cek_status.php - Halaman Cek Status Pesanan
// ============================================================
require_once 'config/config.php';

$page_title  = 'Cek Status Pesanan';
$pesanan     = null;
$kode_input  = '';
$not_found   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['kode'])) {
    $kode_input = sanitize($_POST['kode_pesanan'] ?? $_GET['kode'] ?? '');

    if ($kode_input) {
        $stmt = $conn->prepare("
            SELECT p.*, pr.nama_produk, pr.gambar, pr.harga, pr.berat_gram
            FROM pesanan p
            JOIN produk pr ON p.id_produk = pr.id
            WHERE p.kode_pesanan = ?
        ");
        $stmt->bind_param('s', $kode_input);
        $stmt->execute();
        $pesanan = $stmt->get_result()->fetch_assoc();
        if (!$pesanan) $not_found = true;
    }
}

// Mapping step status (untuk progress bar)
$status_steps = ['menunggu', 'diproses', 'dikirim', 'selesai'];
$status_labels = ['Menunggu', 'Diproses', 'Dikirim', 'Selesai'];
$status_icons  = ['fa-clock', 'fa-gear', 'fa-truck', 'fa-circle-check'];

$current_step = 0;
if ($pesanan && $pesanan['status'] !== 'dibatalkan') {
    $current_step = array_search($pesanan['status'], $status_steps);
    if ($current_step === false) $current_step = 0;
}

include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>">Beranda</a></li>
                <li class="breadcrumb-item active">Cek Status Pesanan</li>
            </ol>
        </nav>
        <h1 class="mb-1"><i class="fa-solid fa-magnifying-glass me-2" style="color:var(--primary)!important;"></i> Cek Status Pesanan</h1>
        <p class="text-muted mb-0">Masukkan kode pesanan untuk melihat status pesanan Anda</p>
    </div>
</section>

<section class="py-5" style="background:var(--light-bg); min-height: 60vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Form Cek Status -->
                <div class="form-card mb-4">
                    <h5 class="fw-700 mb-4"><i class="fa-solid fa-search me-2" style="color:var(--primary);"></i>Lacak Pesanan</h5>
                    <form method="POST" action="cek_status.php" id="formCek" onsubmit="return validateForm('formCek')">
                        <div class="d-flex gap-2">
                            <input type="text"
                                   name="kode_pesanan"
                                   class="form-control form-control-lg"
                                   placeholder="Contoh: ORD-20240101-001"
                                   value="<?= htmlspecialchars($kode_input) ?>"
                                   style="border-radius:50px;border:2px solid var(--primary-light);"
                                   required>
                            <button type="submit" class="btn-primary-custom px-4" style="white-space:nowrap;border-radius:50px;">
                                <i class="fa-solid fa-search me-1"></i> Lacak
                            </button>
                        </div>
                        <div class="mt-2 small text-muted">
                            <i class="fa-solid fa-circle-info me-1 text-warning"></i>
                            Kode pesanan diberikan setelah Anda melakukan pemesanan (format: ORD-YYYYMMDD-XXXX)
                        </div>
                    </form>
                </div>

                <!-- Not Found -->
                <?php if ($not_found): ?>
                <div class="text-center py-5">
                    <i class="fa-solid fa-box-open d-block mb-3" style="font-size:3rem;color:var(--primary-light);"></i>
                    <h5 class="fw-700">Pesanan Tidak Ditemukan</h5>
                    <p class="text-muted">Kode pesanan "<strong><?= htmlspecialchars($kode_input) ?></strong>" tidak ditemukan di sistem kami.</p>
                    <p class="text-muted small">Pastikan kode pesanan yang Anda masukkan sudah benar.</p>
                </div>
                <?php endif; ?>

                <!-- Hasil Pesanan -->
                <?php if ($pesanan): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                            <div>
                                <div class="small text-muted mb-1">Kode Pesanan</div>
                                <div class="fw-800 fs-5" style="color:var(--primary);letter-spacing:.5px;">
                                    <?= htmlspecialchars($pesanan['kode_pesanan']) ?>
                                </div>
                            </div>
                            <div class="text-md-end">
                                <?php $badge = getStatusBadge($pesanan['status']); ?>
                                <span class="badge badge-status bg-<?= $badge['bg'] ?>">
                                    <?= $badge['text'] ?>
                                </span>
                                <div class="small text-muted mt-1">
                                    <i class="fa-regular fa-clock me-1"></i>
                                    <?= date('d M Y, H:i', strtotime($pesanan['created_at'])) ?> WIB
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="order-body">
                        <!-- Progress Steps -->
                        <?php if ($pesanan['status'] !== 'dibatalkan'): ?>
                        <div class="mb-4">
                            <h6 class="fw-700 mb-3 text-muted small text-uppercase">Progress Pesanan</h6>
                            <div class="progress-steps">
                                <?php foreach ($status_steps as $i => $step): ?>
                                <div class="step-item <?= $i <= $current_step ? 'active' : '' ?>">
                                    <div class="step-circle <?= $i < $current_step ? 'done' : ($i === $current_step ? 'active' : '') ?>">
                                        <i class="fa-solid <?= $status_icons[$i] ?>"></i>
                                    </div>
                                    <span class="step-label"><?= $status_labels[$i] ?></span>
                                </div>
                                <?php if ($i < count($status_steps) - 1): ?>
                                <div class="step-line <?= $i < $current_step ? 'done' : '' ?>"></div>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-danger rounded-3 mb-4">
                            <i class="fa-solid fa-circle-xmark me-2"></i>
                            Pesanan ini telah <strong>dibatalkan</strong>.
                        </div>
                        <?php endif; ?>

                        <!-- Data Pelanggan -->
                        <div class="order-meta-row mb-4">
                            <div class="order-meta-item">
                                <label><i class="fa-solid fa-user me-1"></i> Nama Pelanggan</label>
                                <span><?= htmlspecialchars($pesanan['nama_pelanggan']) ?></span>
                            </div>
                            <div class="order-meta-item">
                                <label><i class="fa-solid fa-phone me-1"></i> Nomor HP</label>
                                <span><?= htmlspecialchars($pesanan['no_hp']) ?></span>
                            </div>
                            <div class="order-meta-item">
                                <label><i class="fa-solid fa-location-dot me-1"></i> Alamat</label>
                                <span><?= htmlspecialchars($pesanan['alamat']) ?></span>
                            </div>
                        </div>

                        <!-- Detail Produk -->
                        <h6 class="fw-700 mb-3 text-muted small text-uppercase">Detail Produk</h6>
                        <div class="d-flex gap-3 p-3 rounded-3 mb-4" style="background:var(--light-bg);border:1px solid var(--primary-light);">
                            <div style="width:72px;height:72px;border-radius:10px;overflow:hidden;flex-shrink:0;">
                                <?php $img = !empty($pesanan['gambar']) && file_exists("assets/images/produk/{$pesanan['gambar']}")
                                    ? "assets/images/produk/{$pesanan['gambar']}"
                                    : "https://placehold.co/120x120/FFF7ED/F97316?text=IMG"; ?>
                                <img src="<?= $img ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-700"><?= htmlspecialchars($pesanan['nama_produk']) ?></div>
                                <div class="text-muted small"><?= $pesanan['berat_gram'] ?>g per kemasan</div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div class="text-muted small">
                                        <?= formatRupiah($pesanan['harga']) ?> × <?= $pesanan['jumlah'] ?>
                                    </div>
                                    <div class="fw-800" style="color:var(--primary);">
                                        <?= formatRupiah($pesanan['total_harga']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($pesanan['catatan']): ?>
                        <div class="mb-3 p-3 rounded-3" style="background:#FFFBF5;border:1px solid var(--primary-light);">
                            <div class="small text-muted mb-1"><i class="fa-solid fa-note-sticky me-1"></i> Catatan</div>
                            <div class="fw-600 small"><?= htmlspecialchars($pesanan['catatan']) ?></div>
                        </div>
                        <?php endif; ?>

                        <!-- Total -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <span class="fw-700">Total Pembayaran</span>
                            <span class="fw-800 fs-4" style="color:var(--primary);"><?= formatRupiah($pesanan['total_harga']) ?></span>
                        </div>

                        <div class="mt-4 d-flex gap-2 flex-wrap">
                            <a href="produk.php" class="btn-primary-custom">
                                <i class="fa-solid fa-cart-shopping me-1"></i> Pesan Lagi
                            </a>
                            <button onclick="window.print()" class="btn-outline-custom">
                                <i class="fa-solid fa-print me-1"></i> Cetak
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
