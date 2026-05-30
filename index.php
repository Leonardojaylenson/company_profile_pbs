<?php
// index.php — Homepage
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$s         = getAllSettings();
$services  = getFeaturedServices();
$news      = getNews(3);
$testi     = getTestimonials();
$pdo       = getPDO();
$routes    = $pdo->query("SELECT * FROM routes WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();
$pageTitle = '';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- ══ HERO SECTION ══════════════════════════════════════════ -->
<section class="hero-section" id="hero">
    <div class="hero-video-wrap">
        <?php if (!empty($s['hero_video'])): ?>
            <video autoplay muted loop playsinline>
                <source src="<?= assetUrl($s['hero_video']) ?>" type="video/mp4">
            </video>
        <?php else: ?>
            <!-- Fallback animated SVG ocean when no video uploaded -->
            <div class="hero-fallback-bg"></div>
        <?php endif; ?>
    </div>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-7" data-aos="fade-right" data-aos-duration="900">
                <div class="hero-eyebrow">
                    <i class="bi bi-anchor"></i>
                    <?= htmlspecialchars($s['site_tagline'] ?? 'Freight Forwarding & Export-Import') ?>
                </div>
                <h1 class="hero-title">
                    <?php
                    $heroTitle = $s['hero_title'] ?? 'Solusi Pengiriman Laut Terpercaya';
                    $words = explode(' ', $heroTitle);
                    $half  = ceil(count($words) / 2);
                    echo implode(' ', array_slice($words, 0, $half)) . ' ';
                    echo '<span>' . implode(' ', array_slice($words, $half)) . '</span>';
                    ?>
                </h1>
                <p class="hero-subtitle">
                    <?= htmlspecialchars($s['hero_subtitle'] ?? '') ?>
                </p>
                <div class="hero-actions">
                    <a href="<?= BASE_URL . htmlspecialchars($s['hero_btn1_url'] ?? '/services.php') ?>" class="hero-btn-primary">
                        <i class="bi bi-grid-3x3-gap"></i>
                        <?= htmlspecialchars($s['hero_btn1_text'] ?? 'Lihat Layanan') ?>
                    </a>
                    <a href="<?= BASE_URL . htmlspecialchars($s['hero_btn2_url'] ?? '/contact.php') ?>" class="hero-btn-secondary">
                        <i class="bi bi-headset"></i>
                        <?= htmlspecialchars($s['hero_btn2_text'] ?? 'Hubungi Kami') ?>
                    </a>
                </div>
                <div class="hero-stats" data-aos="fade-up" data-aos-delay="300">
                    <div class="hero-stat-item">
                        <div class="hero-stat-num"><?= htmlspecialchars($s['stat_years'] ?? '10+') ?></div>
                        <div class="hero-stat-label">Tahun Pengalaman</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="hero-stat-num"><?= htmlspecialchars($s['stat_routes'] ?? '50+') ?></div>
                        <div class="hero-stat-label">Rute Pelayaran</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="hero-stat-num"><?= htmlspecialchars($s['stat_containers'] ?? '5.000+') ?></div>
                        <div class="hero-stat-label">Kontainer/Tahun</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="hero-stat-num"><?= htmlspecialchars($s['stat_clients'] ?? '200+') ?></div>
                        <div class="hero-stat-label">Pelanggan Setia</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end" data-aos="fade-left" data-aos-duration="900">
                <div class="hero-ship-card">
                    <div class="ship-card-inner">
                        <i class="bi bi-tsunami hero-wave-icon"></i>
                        <div class="ship-card-content">
                            <div class="ship-status-badge"><span class="pulse-dot"></span> Pengiriman Aktif</div>
                            <div class="ship-route">Batam <i class="bi bi-arrow-right"></i> Jakarta</div>
                            <div class="ship-eta">ETA: 3-4 Hari Kerja</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href="#services" class="hero-scroll">
        <div class="scroll-mouse"><div class="scroll-dot"></div></div>
        <span>Scroll</span>
    </a>
</section>

<section class="services-section" id="services">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-7" data-aos="fade-up">
                <span class="section-label">Layanan Kami</span>
                <h2 class="section-title">Solusi Logistik Lengkap untuk Bisnis Anda</h2>
                <p class="text-muted">Dari muatan satuan hingga kontainer penuh, kami siap menangani semua kebutuhan pengiriman laut Anda.</p>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($services as $i => $svc): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $i * 80 ?>">
                <div class="service-card">
                    <?php if (!empty($svc['image'])): ?>
                    <div class="service-card-imgwrap">
                        <img src="<?= uploadUrl($svc['image']) ?>" alt="<?= htmlspecialchars($svc['title']) ?>">
                    </div>
                    <?php endif; ?>
                    <div class="service-card-body">
                        <h3 class="service-card-title"><?= htmlspecialchars($svc['title']) ?></h3>
                        <p class="service-card-desc"><?= htmlspecialchars(truncate($svc['short_desc'], 120)) ?></p>
                        <?php if (!empty($svc['features'])):
                            $features = json_decode($svc['features'], true) ?? [];
                        ?>
                        <ul class="service-features-mini">
                            <?php foreach ($features as $f): ?>
                            <li><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($f) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?= BASE_URL ?>/services.php" class="btn btn-primary px-5 py-3 rounded-pill">
                <i class="bi bi-grid me-2"></i> Lihat Semua Layanan
            </a>
        </div>
    </div>
</section>

<!-- ══ ABOUT STRIP ════════════════════════════════════════════ -->
<section class="about-section" id="about">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="about-image-wrap">
                    <?php if (!empty($s['about_image'])): ?>
                        <img src="<?= uploadUrl($s['about_image']) ?>" alt="Tentang PBS" class="about-main-img">
                    <?php else: ?>
                        <div class="about-placeholder">
                            <i class="bi bi-ship"></i>
                        </div>
                    <?php endif; ?>
                    <div class="about-badge">
                        <div class="about-badge-num"><?= htmlspecialchars($s['stat_years'] ?? '10+') ?></div>
                        <div class="about-badge-label">Tahun Terpercaya</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <span class="section-label">Tentang PBS</span>
                <h2 class="section-title"><?= htmlspecialchars($s['about_title'] ?? 'Pengiriman Andal, Koneksi Nusantara') ?></h2>
                <p class="text-muted mb-4"><?= nl2br(htmlspecialchars($s['about_description'] ?? '')) ?></p>
                <div class="about-highlights">
                    <div class="about-highlight-item">
                        <div class="about-hl-icon"><i class="bi bi-shield-check"></i></div>
                        <div>
                            <strong>Terpercaya & Berlisensi</strong>
                            <p>Beroperasi di bawah regulasi resmi Kementerian Perhubungan RI</p>
                        </div>
                    </div>
                    <div class="about-highlight-item">
                        <div class="about-hl-icon"><i class="bi bi-clock-history"></i></div>
                        <div>
                            <strong>On-Time Delivery</strong>
                            <p>Komitmen ketepatan waktu dengan monitoring 24/7</p>
                        </div>
                    </div>
                    <div class="about-highlight-item">
                        <div class="about-hl-icon"><i class="bi bi-headset"></i></div>
                        <div>
                            <strong>Customer Support 24/7</strong>
                            <p>Tim kami siap membantu kapanpun Anda membutuhkan</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= BASE_URL ?>/about.php" class="btn btn-primary me-3">Lebih Lanjut</a>
                    <a href="<?= BASE_URL ?>/contact.php" class="btn btn-outline-primary">Konsultasi Gratis</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══ ROUTES SECTION ═══════════════════════════════════════ -->
<?php if (!empty($routes)): ?>
<section class="routes-section">
    <div class="container mb-5">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-7" data-aos="fade-up">
                <span class="section-label">Jaringan Rute</span>
                <h2 class="section-title">Rute Pengiriman Kami</h2>
                <p class="text-muted">Menghubungkan pulau-pulau Indonesia dengan jadwal keberangkatan yang konsisten.</p>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($routes as $i => $route): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 60 ?>">
                <div class="route-card p-4 shadow-sm border-0 rounded-4 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="route-icon-circle bg-primary-subtle text-primary me-3">
                            <i class="bi bi-water"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Jalur Pelayaran</h5>
                    </div>
                    <div class="route-path d-flex align-items-center justify-content-between bg-light p-3 rounded-3 mb-3">
                        <span class="fw-bold text-dark"><?= htmlspecialchars($route['origin']) ?></span>
                        <i class="bi bi-arrow-right text-primary mx-2"></i>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($route['destination']) ?></span>
                    </div>
                    <div class="route-details d-flex gap-3 text-muted small mb-3">
                        <span><i class="bi bi-clock-history me-1"></i> <?= htmlspecialchars($route['duration']) ?></span>
                        <span><i class="bi bi-calendar-event me-1"></i> <?= htmlspecialchars($route['frequency']) ?></span>
                    </div>
                    <?php if (!empty($route['notes'])): ?>
                    <div class="route-notes">
                        <i class="bi bi-info-circle me-1"></i> <?= htmlspecialchars($route['notes']) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ══ TESTIMONIALS ══════════════════════════════════════════ -->
<?php if (!empty($testi)): ?>
<section class="testimonials-section">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-7" data-aos="fade-up">
                <span class="section-label">Testimoni</span>
                <h2 class="section-title">Apa Kata Pelanggan Kami</h2>
                <p class="text-muted">Kepuasan pelanggan adalah prioritas utama kami.</p>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($testi as $i => $t): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 80 ?>">
                <div class="testi-card">
                    <div class="testi-stars">
                        <?php for ($r = 1; $r <= 5; $r++): ?>
                            <i class="bi bi-star-fill<?= $r > $t['rating'] ? ' text-muted' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="testi-content">"<?= htmlspecialchars($t['content']) ?>"</p>
                    <div class="testi-author">
                        <div class="testi-avatar">
                            <?php if (!empty($t['avatar'])): ?>
                                <img src="<?= uploadUrl($t['avatar']) ?>" alt="<?= htmlspecialchars($t['name']) ?>">
                            <?php else: ?>
                                <span><?= mb_strtoupper(mb_substr($t['name'], 0, 1)) ?></span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <strong class="testi-name"><?= htmlspecialchars($t['name']) ?></strong>
                            <small class="testi-role">
                                <?= htmlspecialchars($t['position'] ?? '') ?>
                                <?php if (!empty($t['company'])): ?> · <?= htmlspecialchars($t['company']) ?><?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ══ NEWS SECTION ══════════════════════════════════════════ -->
<?php if (!empty($news)): ?>
<section class="news-section">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-7" data-aos="fade-up">
                <span class="section-label">Berita & Info</span>
                <h2 class="section-title">Informasi Terbaru dari PBS</h2>
                <p class="text-muted">Update layanan, rute baru, dan tips logistik untuk bisnis Anda.</p>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($news as $i => $n): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $i * 80 ?>">
                <article class="news-card">
                    <div class="news-card-imgwrap">
                        <?php if (!empty($n['image'])): ?>
                            <img src="<?= uploadUrl($n['image']) ?>" alt="<?= htmlspecialchars($n['title']) ?>">
                        <?php else: ?>
                            <div class="news-img-placeholder"><i class="bi bi-newspaper"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="news-card-body">
                        <div class="news-meta">
                            <span class="news-category"><?= htmlspecialchars($n['category'] ?? 'Berita') ?></span>
                            <span><i class="bi bi-calendar3 me-1"></i><?= formatDate($n['published_at']) ?></span>
                        </div>
                        <h3 class="news-title">
                            <a href="<?= BASE_URL ?>/news.php?id=<?= $n['id'] ?>"><?= htmlspecialchars($n['title']) ?></a>
                        </h3>
                        <p class="news-excerpt"><?= htmlspecialchars(truncate($n['excerpt'] ?? '', 110)) ?></p>
                        <a href="<?= BASE_URL ?>/news.php?id=<?= $n['id'] ?>" class="news-read-more">
                            Baca Selengkapnya <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?= BASE_URL ?>/news.php" class="btn btn-outline-primary px-5 py-3 rounded-pill">Semua Berita</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ══ CTA SECTION ════════════════════════════════════════════ -->
<section class="cta-section">
    <div class="container">
        <div class="cta-box" data-aos="zoom-in">
            <div class="cta-deco-1"></div>
            <div class="cta-deco-2"></div>
            <div class="row align-items-center position-relative">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <span class="section-label text-white opacity-75">Mulai Sekarang</span>
                    <h2 class="cta-title"><?= htmlspecialchars($s['cta_title'] ?? 'Siap Kirimkan Muatan Anda?') ?></h2>
                    <p class="cta-desc"><?= htmlspecialchars($s['cta_description'] ?? '') ?></p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?= BASE_URL ?>/contact.php" class="btn btn-white me-2 mb-2">
                        <i class="bi bi-envelope me-2"></i> Kirim Pesan
                    </a>
                    <?php if (!empty($s['contact_whatsapp'])): 
                        $waNum = preg_replace('/[^0-9]/', '', $s['contact_whatsapp']);
                    ?>
                    <a href="https://wa.me/<?= $waNum ?>?text=Halo%2C%20saya%20ingin%20bertanya%20tentang%20layanan%20pengiriman%20PBS." target="_blank" class="btn btn-outline-white mb-2">
                        <i class="bi bi-whatsapp me-2"></i> WhatsApp
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>