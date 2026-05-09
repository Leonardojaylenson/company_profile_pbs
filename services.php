<?php
// services.php — All Services Page
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$s        = getAllSettings();
$services = getServices(true);
$pageTitle = 'Layanan Kami';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- Page Header -->
<section class="page-header">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <h1 class="page-header-title" data-aos="fade-up">Layanan Kami</h1>
        <p class="page-header-sub" data-aos="fade-up" data-aos-delay="100">Solusi pengiriman laut lengkap untuk semua kebutuhan logistik bisnis Anda</p>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="150">
            <ol class="breadcrumb breadcrumb-dark">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Beranda</a></li>
                <li class="breadcrumb-item active">Layanan</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Services Grid -->
<section class="section-pad">
    <div class="container">
        <?php if (empty($services)): ?>
        <div class="text-center py-5">
            <i class="bi bi-grid" style="font-size:3rem;color:#ccc;"></i>
            <p class="mt-3 text-muted">Belum ada layanan yang tersedia.</p>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($services as $i => $svc): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 80 ?>" id="service-<?= $svc['id'] ?>">
                <div class="service-card h-100">
                    <div class="service-card-icon">
                        <i class="bi <?= htmlspecialchars($svc['icon'] ?? 'bi-box-seam') ?>"></i>
                    </div>
                    <?php if (!empty($svc['image'])): ?>
                    <div class="service-card-img">
                        <img src="<?= uploadUrl($svc['image']) ?>" alt="<?= htmlspecialchars($svc['title']) ?>">
                    </div>
                    <?php endif; ?>
                    <div class="service-card-body">
                        <h3 class="service-card-title"><?= htmlspecialchars($svc['title']) ?></h3>
                        <p class="service-card-desc"><?= nl2br(htmlspecialchars($svc['description'] ?? $svc['short_desc'] ?? '')) ?></p>
                        <?php if (!empty($svc['features'])):
                            $features = json_decode($svc['features'], true) ?? [];
                        ?>
                        <ul class="service-features-mini mt-3">
                            <?php foreach ($features as $f): ?>
                            <li><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($f) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        <?php if (!empty($svc['price_info'])): ?>
                        <div class="service-price mt-3">
                            <small class="text-muted">Mulai dari</small>
                            <strong class="d-block text-primary"><?= htmlspecialchars($svc['price_info']) ?></strong>
                        </div>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/contact.php?service=<?= urlencode($svc['title']) ?>" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-headset me-2"></i> Konsultasi Layanan Ini
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-box" data-aos="zoom-in">
            <div class="cta-deco-1"></div>
            <div class="cta-deco-2"></div>
            <div class="row align-items-center position-relative">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <span class="section-label text-white opacity-75">Hubungi Kami</span>
                    <h2 class="cta-title">Butuh Layanan Khusus?</h2>
                    <p class="cta-desc">Tim kami siap membantu merancang solusi logistik yang tepat untuk bisnis Anda.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?= BASE_URL ?>/contact.php" class="btn btn-white me-2 mb-2">
                        <i class="bi bi-envelope me-2"></i> Kirim Pesan
                    </a>
                    <?php if (!empty($s['contact_whatsapp'])): $wa = preg_replace('/[^0-9]/', '', $s['contact_whatsapp']); ?>
                    <a href="https://wa.me/<?= $wa ?>?text=Halo%2C%20saya%20ingin%20bertanya%20tentang%20layanan." target="_blank" class="btn btn-outline-white mb-2">
                        <i class="bi bi-whatsapp me-2"></i> WhatsApp
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>