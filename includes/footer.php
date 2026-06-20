<!-- Footer -->
<footer class="footer mt-auto">
    <div class="footer-top">
        <div class="container">
            <div class="row g-4">
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
                        <p class="footer-desc">Produsen kerupuk berkualitas dengan cita rasa autentik sejak tahun 1985.</p>
                    </div>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="footer-heading">Menu</h6>
                    <ul class="footer-links">
                        <li><a href="<?= APP_URL ?>">Beranda</a></li>
                        <li><a href="<?= APP_URL ?>/produk.php">Produk</a></li>
                        <li><a href="<?= APP_URL ?>/cek_status.php">Cek Pesanan</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="footer-heading">Kategori</h6>
                    <ul class="footer-links">
                        <li><a href="<?= APP_URL ?>/produk.php?kategori=original">Original</a></li>
                        <li><a href="<?= APP_URL ?>/produk.php?kategori=pedas">Pedas</a></li>
                        <li><a href="<?= APP_URL ?>/produk.php?kategori=gurih">Gurih</a></li>
                        <li><a href="<?= APP_URL ?>/produk.php?kategori=manis">Manis</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h6 class="footer-heading">Kontak Kami</h6>
                    <ul class="footer-contact">
                        <li><i class="fa-solid fa-location-dot text-warning"></i><span>Jl. Industri No. 45, Sidoarjo, Jawa Timur</span></li>
                        <li><i class="fa-solid fa-phone text-warning"></i><span>+62 812-3456-7890</span></li>
                        <li><i class="fa-solid fa-envelope text-warning"></i><span>info@udanekarupa.com</span></li>
                        <li><i class="fa-solid fa-clock text-warning"></i><span>Senin - Sabtu: 08.00 - 17.00 WIB</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <p class="mb-0">© <?= date('Y') ?> <strong>UD Aneka Rupa</strong>. Semua hak dilindungi.</p>
            </div>
        </div>
    </div>
</footer>

<!-- LOCAL JS (tidak pakai CDN) -->
<script src="<?= APP_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/sweetalert2.all.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>

<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>