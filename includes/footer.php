<?php
// includes/footer.php — Public Site Footer
$s = $s ?? getAllSettings();
$phone    = $s['contact_phone']     ?? '';
$wa       = $s['contact_whatsapp']  ?? '';
$email    = $s['contact_email']     ?? '';
$address  = $s['contact_address']   ?? '';
$waClean  = preg_replace('/[^0-9]/', '', $wa);
$services = getServices();
?>

<!-- ── FOOTER ── -->
<footer class="pbs-footer">
    <div class="footer-top">
        <div class="container">
            <div class="row g-5">
                <!-- Brand -->
                <div class="col-lg-4">
                    <?php if (!empty($s['logo'])): ?>
                        <img src="<?= assetUrl($s['logo']) ?>" alt="Logo" height="56" class="mb-3">
                    <?php else: ?>
                        <h4 class="footer-brand-text"><?= htmlspecialchars($s['site_name']) ?></h4>
                    <?php endif; ?>
                    <p class="footer-desc"><?= htmlspecialchars($s['site_tagline'] ?? '') ?></p>
                    <p class="footer-desc small"><?= truncate($s['site_description'] ?? '', 160) ?></p>
                    <div class="social-links mt-4">
                        <?php if (!empty($s['social_instagram'])): ?>
                            <a href="<?= htmlspecialchars($s['social_instagram']) ?>" target="_blank"><i class="bi bi-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($s['social_facebook'])): ?>
                            <a href="<?= htmlspecialchars($s['social_facebook']) ?>" target="_blank"><i class="bi bi-facebook"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($s['social_linkedin'])): ?>
                            <a href="<?= htmlspecialchars($s['social_linkedin']) ?>" target="_blank"><i class="bi bi-linkedin"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($waClean)): ?>
                            <a href="https://wa.me/<?= $waClean ?>" target="_blank"><i class="bi bi-whatsapp"></i></a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Layanan -->
                <div class="col-lg-3 col-sm-6">
                    <h6 class="footer-heading">Layanan Kami</h6>
                    <ul class="footer-links">
                        <?php foreach ($services as $svc): ?>
                            <li><a href="<?= BASE_URL ?>/services.php#service-<?= $svc['id'] ?>"><i class="bi bi-chevron-right"></i> <?= htmlspecialchars($svc['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Navigasi -->
                <div class="col-lg-2 col-sm-6">
                    <h6 class="footer-heading">Navigasi</h6>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>/"><i class="bi bi-chevron-right"></i> Beranda</a></li>
                        <li><a href="<?= BASE_URL ?>/about.php"><i class="bi bi-chevron-right"></i> Tentang Kami</a></li>
                        <li><a href="<?= BASE_URL ?>/services.php"><i class="bi bi-chevron-right"></i> Layanan</a></li>
                        <li><a href="<?= BASE_URL ?>/news.php"><i class="bi bi-chevron-right"></i> Berita</a></li>
                        <li><a href="<?= BASE_URL ?>/contact.php"><i class="bi bi-chevron-right"></i> Kontak</a></li>
                    </ul>
                </div>

                <!-- Kontak -->
                <div class="col-lg-3">
                    <h6 class="footer-heading">Hubungi Kami</h6>
                    <ul class="footer-contact">
                        <?php if ($address): ?>
                            <li><i class="bi bi-geo-alt-fill"></i><span><?= nl2br(htmlspecialchars($address)) ?></span></li>
                        <?php endif; ?>
                        <?php if ($phone): ?>
                            <li><i class="bi bi-telephone-fill"></i><a href="tel:<?= preg_replace('/[^0-9+]/', '', $phone) ?>"><?= htmlspecialchars($phone) ?></a></li>
                        <?php endif; ?>
                        <?php if ($wa): ?>
                            <li><i class="bi bi-whatsapp"></i><a href="https://wa.me/<?= $waClean ?>" target="_blank"><?= htmlspecialchars($wa) ?></a></li>
                        <?php endif; ?>
                        <?php if ($email): ?>
                            <li><i class="bi bi-envelope-fill"></i><a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p class="mb-0"><?= htmlspecialchars($s['footer_copyright'] ?? '© 2025 PT. Prima Bahari Sejahtera.') ?></p>
        </div>
    </div>
</footer>

<!-- WhatsApp Float Button -->
<?php if ($waClean): ?>
<a href="https://wa.me/<?= $waClean ?>?text=Halo%2C%20saya%20ingin%20bertanya%20tentang%20layanan%20pengiriman." class="wa-float" target="_blank">
    <i class="bi bi-whatsapp"></i>
    <span class="wa-tooltip">Chat WhatsApp</span>
</a>
<?php endif; ?>

<!-- Back to Top -->
<button class="back-to-top" id="backToTop"><i class="bi bi-chevron-up"></i></button>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="<?= BASE_URL ?>/public/js/main.js"></script>
<?= $extraFooter ?? '' ?>
</body>
</html>