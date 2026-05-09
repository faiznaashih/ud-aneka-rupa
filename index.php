<?php
// ============================================================
// index.php - Halaman Beranda
// ============================================================
require_once 'config/config.php';

$page_title = 'Beranda';

// Ambil produk unggulan (6 produk aktif terbaru)
$sql_produk = "SELECT * FROM produk WHERE status = 'aktif' ORDER BY id DESC LIMIT 6";
$result_produk = $conn->query($sql_produk);
$produk_list = $result_produk->fetch_all(MYSQLI_ASSOC);

// Hitung statistik
$total_produk = $conn->query("SELECT COUNT(*) as total FROM produk WHERE status='aktif'")->fetch_assoc()['total'];
$total_pesanan = $conn->query("SELECT COUNT(*) as total FROM pesanan WHERE status='selesai'")->fetch_assoc()['total'];

include 'includes/header.php';
?>

<!-- ===================== HERO SECTION ===================== -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center gy-4">
            <!-- Left Content -->
            <div class="col-lg-6">
                <div class="hero-badge">
                    <i class="fa-solid fa-star text-warning"></i>
                    Kerupuk Premium Kualitas Terbaik
                </div>
                <h1 class="hero-title">
                    Nikmati Cita Rasa<br>
                    Kerupuk <span class="text-highlight">Autentik</span><br>
                    UD Aneka Rupa
                </h1>
                <p class="hero-desc">
                    Kerupuk tradisional dengan resep turun-temurun. Dibuat dari bahan pilihan, tanpa bahan pengawet berbahaya, renyah dan lezat untuk seluruh keluarga.
                </p>
                <div class="hero-buttons d-flex flex-wrap gap-3">
                    <a href="produk.php" class="btn-primary-custom">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        Lihat Produk
                    </a>
                    <a href="cek_status.php" class="btn-outline-custom">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Cek Pesanan
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-num"><?= $total_produk ?>+</div>
                        <div class="stat-label">Jenis Produk</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num"><?= number_format($total_pesanan) ?>+</div>
                        <div class="stat-label">Pesanan Selesai</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num">38+</div>
                        <div class="stat-label">Tahun Pengalaman</div>
                    </div>
                </div>
            </div>

            <!-- Right Image -->
            <div class="col-lg-6">
                <div class="hero-image-wrap">
                    <img src="assets/images/hero-kerupuk.png"
                         onerror="this.src='https://placehold.co/600x460/FFF7ED/F97316?text=UD+Aneka+Rupa&font=playfair-display'"
                         alt="Kerupuk UD Aneka Rupa"
                         class="img-fluid" style="height:460px;object-fit:cover;object-position:center;">
                    <!-- Float Cards -->
                    <div class="hero-float-card card-1">
                        <div class="float-icon">
                            <i class="fa-solid fa-award"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:.85rem;">Kualitas Terjamin</div>
                            <div style="font-size:.75rem;color:var(--gray);">Bersertifikat BPOM</div>
                        </div>
                    </div>
                    <div class="hero-float-card card-2">
                        <div class="float-icon">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:.85rem;">Pengiriman Cepat</div>
                            <div style="font-size:.75rem;color:var(--gray);">Seluruh Indonesia</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== FEATURES SECTION ===================== -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-leaf"></i>
                    </div>
                    <div class="feature-title">Bahan Alami</div>
                    <p class="feature-text">Menggunakan bahan alami pilihan tanpa pengawet berbahaya</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="feature-title">Higienis</div>
                    <p class="feature-text">Diproduksi dengan standar kebersihan dan keamanan pangan</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div class="feature-title">Kirim Cepat</div>
                    <p class="feature-text">Pengiriman ke seluruh Indonesia dengan aman dan tepat waktu</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                    <div class="feature-title">Harga Terjangkau</div>
                    <p class="feature-text">Harga langsung dari produsen, hemat dan berkualitas</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== PRODUK UNGGULAN ===================== -->
<section class="py-5" style="background: var(--light-bg);">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge"><i class="fa-solid fa-fire-flame-curved me-1"></i> Produk Kami</span>
            <h2 class="section-title">Produk <span>Unggulan</span></h2>
            <div class="divider-orange"></div>
            <p class="section-desc mt-2">Berbagai pilihan kerupuk lezat dengan kualitas terbaik untuk Anda</p>
        </div>

        <?php if (empty($produk_list)): ?>
        <div class="empty-state">
            <i class="fa-solid fa-box-open d-block"></i>
            <h5>Produk Belum Tersedia</h5>
            <p>Produk akan segera hadir. Pantau terus!</p>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($produk_list as $p): ?>
            <div class="col-lg-4 col-md-6 fade-in-up">
                <div class="product-card">
                    <div class="product-img-wrap">
                        <?php
                        $img_src = !empty($p['gambar']) && file_exists("assets/images/produk/" . $p['gambar'])
                            ? "assets/images/produk/" . $p['gambar']
                            : "https://placehold.co/400x300/FFF7ED/F97316?text=" . urlencode($p['nama_produk']);
                        ?>
                        <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                        <span class="product-badge"><?= ucfirst($p['kategori']) ?></span>
                    </div>
                    <div class="product-body">
                        <h5 class="product-name"><?= htmlspecialchars($p['nama_produk']) ?></h5>
                        <p class="product-desc"><?= htmlspecialchars($p['deskripsi']) ?></p>
                        <div class="product-meta">
                            <span class="product-price"><?= formatRupiah($p['harga']) ?></span>
                            <span class="product-weight"><i class="fa-solid fa-weight-scale me-1"></i><?= $p['berat_gram'] ?>g</span>
                        </div>
                        <div class="product-meta">
                            <span class="product-stock"><i class="fa-solid fa-circle-check me-1"></i>Stok: <?= $p['stok'] ?></span>
                        </div>
                        <div class="product-actions">
                            <a href="detail.php?id=<?= $p['id'] ?>" class="btn-detail">
                                <i class="fa-solid fa-eye me-1"></i> Detail
                            </a>
                            <a href="pesan.php?id=<?= $p['id'] ?>" class="btn-order-sm">
                                <i class="fa-solid fa-cart-plus me-1"></i> Pesan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="produk.php" class="btn-primary-custom">
                <i class="fa-solid fa-arrow-right"></i>
                Lihat Semua Produk
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ===================== CTA SECTION ===================== -->
<section class="py-5">
    <div class="container">
        <div class="cta-section text-center text-white">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <span class="badge bg-white bg-opacity-25 text-white mb-3 px-3 py-2">
                        <i class="fa-solid fa-bolt me-1"></i> Pesan Mudah & Cepat
                    </span>
                    <h2 class="display-6 fw-bold mb-3">Siap Memesan Kerupuk Lezat?</h2>
                    <p class="mb-4 opacity-75">
                        Pesan sekarang dan dapatkan kerupuk segar langsung dari pabrik.
                        Minimum order terjangkau, pengiriman ke seluruh wilayah.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="produk.php" class="btn btn-light fw-bold px-4 py-2" style="border-radius:50px;color:var(--primary);">
                            <i class="fa-solid fa-cart-shopping me-2"></i> Pesan Sekarang
                        </a>
                        <a href="cek_status.php" class="btn btn-outline-light fw-bold px-4 py-2" style="border-radius:50px;">
                            <i class="fa-solid fa-magnifying-glass me-2"></i> Cek Status
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== TESTIMONI ===================== -->
<section class="py-5" style="background: var(--light-bg);">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge"><i class="fa-solid fa-quote-left me-1"></i> Testimoni</span>
            <h2 class="section-title">Kata <span>Pelanggan</span> Kami</h2>
            <div class="divider-orange"></div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testi-card">
                    <div class="testi-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    <p class="testi-text">"Kerupuk udangnya enak banget! Renyah dan gurih. Keluarga saya suka semua, langsung habis dalam sehari!"</p>
                    <div class="testi-author">
                        <div class="testi-avatar">B</div>
                        <div>
                            <div class="testi-name">Budi Santoso</div>
                            <div class="testi-loc"><i class="fa-solid fa-location-dot me-1"></i>Surabaya</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testi-card">
                    <div class="testi-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    <p class="testi-text">"Pesan online gampang banget! Langsung bisa cek status pesanan. Kerupuk ikannya pedas sesuai selera saya."</p>
                    <div class="testi-author">
                        <div class="testi-avatar">S</div>
                        <div>
                            <div class="testi-name">Siti Rahayu</div>
                            <div class="testi-loc"><i class="fa-solid fa-location-dot me-1"></i>Sidoarjo</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testi-card">
                    <div class="testi-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i></div>
                    <p class="testi-text">"Kualitas kerupuknya bagus dan harganya terjangkau. Saya sering beli untuk dibawa sebagai oleh-oleh."</p>
                    <div class="testi-author">
                        <div class="testi-avatar">A</div>
                        <div>
                            <div class="testi-name">Ahmad Fauzi</div>
                            <div class="testi-loc"><i class="fa-solid fa-location-dot me-1"></i>Gresik</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
