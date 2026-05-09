<?php
// ============================================================
// pesan.php - Form Pemesanan
// ============================================================
require_once 'config/config.php';

$id  = isset($_GET['id'])  ? (int)$_GET['id']  : 0;
$qty = isset($_GET['qty']) ? max(1, (int)$_GET['qty']) : 1;

if (!$id) redirect(APP_URL . '/produk.php');

// Ambil data produk
$stmt = $conn->prepare("SELECT * FROM produk WHERE id = ? AND status = 'aktif'");
$stmt->bind_param('i', $id);
$stmt->execute();
$produk = $stmt->get_result()->fetch_assoc();

if (!$produk || $produk['stok'] <= 0) {
    redirect(APP_URL . '/produk.php');
}

$page_title = 'Form Pemesanan';
$errors  = [];
$success = false;
$kode_pesanan = '';

// ---- Proses POST ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = sanitize($_POST['nama_pelanggan'] ?? '');
    $no_hp   = sanitize($_POST['no_hp'] ?? '');
    $alamat  = sanitize($_POST['alamat'] ?? '');
    $jumlah  = max(1, (int)($_POST['jumlah'] ?? 1));
    $catatan = sanitize($_POST['catatan'] ?? '');
    $id_produk = (int)($_POST['id_produk'] ?? 0);

    // Validasi
    if (!$nama)     $errors[] = 'Nama pelanggan wajib diisi.';
    if (!$no_hp)    $errors[] = 'Nomor HP wajib diisi.';
    if (!preg_match('/^[0-9]{10,13}$/', preg_replace('/[\s\-]/', '', $no_hp)))
                    $errors[] = 'Format nomor HP tidak valid.';
    if (!$alamat)   $errors[] = 'Alamat pengiriman wajib diisi.';
    if ($jumlah < 1)$errors[] = 'Jumlah pesanan minimal 1.';

    // Cek stok
    $produk_check = $conn->prepare("SELECT stok, harga FROM produk WHERE id = ?");
    $produk_check->bind_param('i', $id_produk);
    $produk_check->execute();
    $pc = $produk_check->get_result()->fetch_assoc();

    if (!$pc) {
        $errors[] = 'Produk tidak ditemukan.';
    } elseif ($jumlah > $pc['stok']) {
        $errors[] = "Stok tidak mencukupi. Stok tersedia: {$pc['stok']}";
    }

    if (empty($errors)) {
        $total  = $jumlah * $pc['harga'];
        $kode   = generateKodePesanan();

        $ins = $conn->prepare("INSERT INTO pesanan (kode_pesanan, nama_pelanggan, no_hp, alamat, id_produk, jumlah, total_harga, catatan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'menunggu')");
        $ins->bind_param('ssssiids', $kode, $nama, $no_hp, $alamat, $id_produk, $jumlah, $total, $catatan);

        if ($ins->execute()) {
            $success = true;
            $kode_pesanan = $kode;
        } else {
            $errors[] = 'Gagal menyimpan pesanan. Silakan coba lagi.';
        }
    }
}

include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="produk.php">Produk</a></li>
                <li class="breadcrumb-item active">Form Pemesanan</li>
            </ol>
        </nav>
        <h1 class="mb-1"><i class="fa-solid fa-cart-shopping me-2" style="color:var(--primary)!important;"></i> Form Pemesanan</h1>
        <p class="text-muted mb-0">Isi data Anda untuk melakukan pemesanan</p>
    </div>
</section>

<section class="py-5" style="background:var(--light-bg);">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <!-- Form -->
            <div class="col-lg-7">
                <div class="form-card">
                    <h5 class="fw-700 mb-4">
                        <i class="fa-solid fa-user-pen me-2" style="color:var(--primary);"></i>
                        Data Pemesanan
                    </h5>

                    <!-- Errors -->
                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <strong>Terdapat kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="pesan.php?id=<?= $produk['id'] ?>" id="formPesan" onsubmit="return validateForm('formPesan')">
                        <input type="hidden" name="id_produk" value="<?= $produk['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="nama_pelanggan"
                                   class="form-control"
                                   placeholder="Masukkan nama lengkap Anda"
                                   value="<?= htmlspecialchars($_POST['nama_pelanggan'] ?? '') ?>"
                                   required>
                            <div class="invalid-feedback">Nama wajib diisi.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor HP / WhatsApp <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="border:2px solid #E7E5E4;border-right:0;background:var(--light-bg);">
                                    <i class="fa-solid fa-phone"></i>
                                </span>
                                <input type="tel"
                                       name="no_hp"
                                       class="form-control"
                                       style="border-left:0;"
                                       placeholder="Contoh: 081234567890"
                                       value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>"
                                       required>
                            </div>
                            <div class="invalid-feedback">Nomor HP wajib diisi.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Pengiriman <span class="text-danger">*</span></label>
                            <textarea name="alamat"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Masukkan alamat lengkap termasuk kecamatan dan kota..."
                                      required><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                            <div class="invalid-feedback">Alamat wajib diisi.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Pesanan <span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="qty-control">
                                    <button class="qty-btn" type="button" onclick="changeQty('minus')">−</button>
                                    <input type="number"
                                           name="jumlah"
                                           id="jumlah"
                                           class="qty-input"
                                           value="<?= isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : $qty ?>"
                                           min="1"
                                           max="<?= $produk['stok'] ?>"
                                           oninput="updateTotal()"
                                           required>
                                    <input type="hidden" id="harga_satuan" value="<?= $produk['harga'] ?>">
                                    <button class="qty-btn" type="button" onclick="changeQty('plus')">+</button>
                                </div>
                                <small class="text-muted">Stok tersedia: <strong><?= $produk['stok'] ?></strong></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea name="catatan"
                                      class="form-control"
                                      rows="2"
                                      placeholder="Catatan tambahan untuk pesanan (misal: kemasan khusus, permintaan lain)"><?= htmlspecialchars($_POST['catatan'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn-primary-custom w-100 justify-content-center py-3" style="display:flex;">
                            <i class="fa-solid fa-paper-plane me-2"></i>
                            Kirim Pesanan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Ringkasan Pesanan -->
            <div class="col-lg-5">
                <div class="form-card">
                    <h5 class="fw-700 mb-4">
                        <i class="fa-solid fa-receipt me-2" style="color:var(--primary);"></i>
                        Ringkasan Pesanan
                    </h5>
                    <!-- Produk -->
                    <div class="d-flex gap-3 mb-4 p-3 rounded-3" style="background:var(--light-bg);">
                        <div style="width:80px;height:80px;border-radius:10px;overflow:hidden;flex-shrink:0;">
                            <?php
                            $img = !empty($produk['gambar']) && file_exists("assets/images/produk/{$produk['gambar']}")
                                ? "assets/images/produk/{$produk['gambar']}"
                                : "https://placehold.co/120x120/FFF7ED/F97316?text=IMG";
                            ?>
                            <img src="<?= $img ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                        <div>
                            <div class="fw-700" style="font-size:.9rem;"><?= htmlspecialchars($produk['nama_produk']) ?></div>
                            <div class="text-muted small mb-1"><?= $produk['berat_gram'] ?>g · <?= ucfirst($produk['kategori']) ?></div>
                            <div class="fw-800" style="color:var(--primary);"><?= formatRupiah($produk['harga']) ?></div>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2 text-muted small">
                            <span>Harga satuan</span>
                            <span class="fw-600 text-dark"><?= formatRupiah($produk['harga']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-muted small">
                            <span>Jumlah</span>
                            <span class="fw-600 text-dark" id="qty-display">× 1</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2 mt-2">
                            <span class="fw-700">Total</span>
                            <span class="fw-800 fs-5" style="color:var(--primary);" id="total_display"><?= formatRupiah($produk['harga']) ?></span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="mt-4 p-3 rounded-3" style="background:linear-gradient(135deg,var(--light-bg),#FEF3C7);border:1px solid var(--primary-light);">
                        <p class="mb-2 small fw-600"><i class="fa-solid fa-info-circle me-1" style="color:var(--primary);"></i> Informasi Pemesanan:</p>
                        <ul class="mb-0 small text-muted" style="padding-left:1.2rem;">
                            <li>Pesanan akan dikonfirmasi via WhatsApp</li>
                            <li>Pembayaran dilakukan saat barang diterima (COD)</li>
                            <li>Simpan kode pesanan untuk melacak status</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// SweetAlert success notification
if ($success):
$extra_js = <<<JS
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: '🎉 Pesanan Berhasil!',
        html: `
            <p>Pesanan Anda telah berhasil dikirim.</p>
            <div style="background:#FFF7ED;border:2px solid #FED7AA;border-radius:12px;padding:12px 20px;margin:12px 0;">
                <div style="color:#78716C;font-size:.85rem;margin-bottom:4px;">Kode Pesanan Anda:</div>
                <div style="font-size:1.3rem;font-weight:800;color:#F97316;letter-spacing:1px;">$kode_pesanan</div>
            </div>
            <p style="color:#78716C;font-size:.87rem;">Simpan kode ini untuk melacak status pesanan Anda</p>
        `,
        confirmButtonColor: '#F97316',
        confirmButtonText: 'Lihat Status Pesanan',
        showCancelButton: true,
        cancelButtonText: 'Pesan Lagi',
        cancelButtonColor: '#78716C'
    }).then(result => {
        if (result.isConfirmed) {
            window.location.href = 'cek_status.php?kode=$kode_pesanan';
        } else {
            window.location.href = 'produk.php';
        }
    });
});
</script>
JS;
endif;
include 'includes/footer.php';
?>
<?php
$extra_js = ($extra_js ?? '') . <<<JS
<script>
// Update qty display on sidebar
const jumlahInput = document.getElementById('jumlah');
if (jumlahInput) {
    function updateQtyDisplay() {
        const el = document.getElementById('qty-display');
        if (el) el.textContent = '× ' + (jumlahInput.value || 1);
        updateTotal();
    }
    jumlahInput.addEventListener('input', updateQtyDisplay);
}
</script>
JS;
?>
