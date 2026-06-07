<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$s        = getAllSettings();
$services = getServices(true);
$pageTitle = 'Layanan Kami';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="page-header pt-5 mt-5">
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
<section class="section-pad pb-5 mb-4">
    <div class="container">
        <?php if (empty($services)): ?>
        <div class="text-center py-5">
            <i class="bi bi-grid" style="font-size:3rem;color:#ccc;"></i>
            <p class="mt-3 text-muted">Belum ada layanan yang tersedia.</p>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($services as $i => $svc):
                $features = json_decode($svc['features'] ?? '[]', true) ?? [];
                $modalData = json_encode([
                    'title'    => $svc['title'],
                    'fullDesc' => $svc['full_desc'] ?? $svc['short_desc'] ?? '',
                    'features' => $features,
                    'image'    => !empty($svc['image']) ? uploadUrl($svc['image']) : '',
                    'url'      => BASE_URL . '/contact.php?service=' . urlencode($svc['title']),
                ]);
            ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 80 ?>" id="service-<?= $svc['id'] ?>">
                <div class="service-card">
                    <?php if (!empty($svc['image'])): ?>
                    <div class="service-card-imgwrap">
                        <img src="<?= uploadUrl($svc['image']) ?>" alt="<?= htmlspecialchars($svc['title']) ?>">
                    </div>
                    <?php endif; ?>
                    <div class="service-card-body">
                        <h3 class="service-card-title"><?= htmlspecialchars($svc['title']) ?></h3>
                        <p class="service-card-desc"><?= htmlspecialchars(truncate($svc['short_desc'] ?? '', 120)) ?></p>
                        <button class="svc-detail-btn" onclick='openServiceModal(<?= htmlspecialchars($modalData, ENT_QUOTES) ?>)'>
                            <i class="bi bi-info-circle me-1"></i> Detail Layanan
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal Detail Layanan -->
<div class="svc-modal-overlay" id="svcModalOverlay" onclick="if(event.target===this)closeSvcModal()">
    <div class="svc-modal">
        <div id="svcModalImg"></div>
        <div class="svc-modal-body">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <h4 class="svc-modal-title" id="svcModalTitle"></h4>
                <button class="svc-modal-close" onclick="closeSvcModal()"><i class="bi bi-x-lg"></i></button>
            </div>
            <p class="svc-modal-desc" id="svcModalDesc"></p>
            <p class="svc-modal-feat-label">Fitur Layanan</p>
            <ul class="svc-modal-features" id="svcModalFeatures"></ul>
            <a href="#" class="btn btn-primary" id="svcModalCta">
                <i class="bi bi-headset me-2"></i> Konsultasi Layanan Ini
            </a>
        </div>
    </div>
</div>

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