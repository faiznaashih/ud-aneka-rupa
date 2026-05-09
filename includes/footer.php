<!-- Footer -->
<footer class="footer mt-auto">
    <div class="footer-top">
        <div class="container">
            <div class="row g-4">
                <!-- Brand -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand mb-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="brand-icon-sm">
                                <i class="fa-solid fa-cookie-bite"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-white fw-bold">UD Aneka Rupa</h5>
                                <small class="text-warning">Pabrik Kerupuk</small>
                            </div>
                        </div>
                        <p class="footer-desc">
                            Produsen kerupuk berkualitas dengan cita rasa autentik. Menggunakan bahan-bahan pilihan dan proses produksi higienis sejak tahun 1985.
                        </p>
                    </div>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>

                <!-- Links -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="footer-heading">Menu</h6>
                    <ul class="footer-links">
                        <li><a href="<?= APP_URL ?>"><i class="fa-solid fa-chevron-right me-1"></i> Beranda</a></li>
                        <li><a href="<?= APP_URL ?>/produk.php"><i class="fa-solid fa-chevron-right me-1"></i> Produk</a></li>
                        <li><a href="<?= APP_URL ?>/cek_status.php"><i class="fa-solid fa-chevron-right me-1"></i> Cek Pesanan</a></li>
                    </ul>
                </div>

                <!-- Kategori -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="footer-heading">Kategori</h6>
                    <ul class="footer-links">
                        <li><a href="<?= APP_URL ?>/produk.php?kategori=original"><i class="fa-solid fa-chevron-right me-1"></i> Original</a></li>
                        <li><a href="<?= APP_URL ?>/produk.php?kategori=pedas"><i class="fa-solid fa-chevron-right me-1"></i> Pedas</a></li>
                        <li><a href="<?= APP_URL ?>/produk.php?kategori=gurih"><i class="fa-solid fa-chevron-right me-1"></i> Gurih</a></li>
                        <li><a href="<?= APP_URL ?>/produk.php?kategori=manis"><i class="fa-solid fa-chevron-right me-1"></i> Manis</a></li>
                    </ul>
                </div>

                <!-- Kontak -->
                <div class="col-lg-4 col-md-6">
                    <h6 class="footer-heading">Kontak Kami</h6>
                    <ul class="footer-contact">
                        <li>
                            <i class="fa-solid fa-location-dot text-warning"></i>
                            <span>Jl. Industri No. 45, Sidoarjo, Jawa Timur</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-phone text-warning"></i>
                            <span>+62 812-3456-7890</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-envelope text-warning"></i>
                            <span>info@udanekarupa.com</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-clock text-warning"></i>
                            <span>Senin - Sabtu: 08.00 - 17.00 WIB</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <p class="mb-0">© <?= date('Y') ?> <strong>UD Aneka Rupa</strong>. Semua hak dilindungi.</p>
                <p class="mb-0 text-muted small">Dibuat dengan <i class="fa-solid fa-heart text-danger"></i> untuk pelanggan setia kami</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Custom JS -->
<script src="<?= APP_URL ?>/assets/js/main.js"></script>

<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
