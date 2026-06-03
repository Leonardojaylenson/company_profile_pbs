<?php
// about.php — About Page
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$s = getAllSettings();
$pageTitle = 'Tentang Kami';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- Page Header -->
<section class="page-header pt-5 mt-5">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content pt-4"> 
        <h1 class="page-header-title" data-aos="fade-up">Tentang PBS</h1>
        <p class="page-header-sub" data-aos="fade-up" data-aos-delay="100">Menghubungkan nusantara dengan layanan pengiriman laut terpercaya</p>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="150">
            <ol class="breadcrumb breadcrumb-dark">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Beranda</a></li>
                <li class="breadcrumb-item active">Tentang Kami</li>
            </ol>
        </nav>
    </div>
</section>

<!-- About Content -->
<section class="section-pad">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="about-image-wrap">
                    <?php if (!empty($s['about_image'])): ?>
                        <img src="<?= uploadUrl($s['about_image']) ?>" alt="Tentang PBS" class="about-main-img">
                    <?php else: ?>
                        <div class="about-placeholder"><i class="bi bi-ship"></i></div>
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
                <p class="text-muted mb-4"><?= nl2br(htmlspecialchars($s['about_description'] ?? 'PT. Prima Bahari Sejahtera adalah perusahaan pengiriman laut yang berpengalaman dalam menghubungkan pulau-pulau di Indonesia dengan layanan yang andal dan terpercaya.')) ?></p>
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
                    <div class="about-highlight-item">
                        <div class="about-hl-icon"><i class="bi bi-award"></i></div>
                        <div>
                            <strong>Berpengalaman</strong>
                            <p>Lebih dari <?= htmlspecialchars($s['stat_years'] ?? '10+') ?> tahun melayani pengiriman antar pulau</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= BASE_URL ?>/contact.php" class="btn btn-primary me-3">
                        <i class="bi bi-headset me-2"></i> Konsultasi Gratis
                    </a>
                    <a href="<?= BASE_URL ?>/services.php" class="btn btn-outline-primary">
                        <i class="bi bi-grid me-2"></i> Lihat Layanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="stats-section mb-3 mt-3">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-3 col-6" data-aos="zoom-in">
                <div class="stat-counter-card">
                    <i class="bi bi-calendar-check stat-icon"></i>
                    <div class="stat-number" data-count="<?= preg_replace('/\D/', '', $s['stat_years'] ?? '10') ?>">0</div>
                    <div class="stat-suffix">+</div>
                    <div class="stat-label">Tahun Pengalaman</div>
                </div>
            </div>
            <div class="col-lg-3 col-6" data-aos="zoom-in" data-aos-delay="100">
                <div class="stat-counter-card">
                    <i class="bi bi-geo-alt stat-icon"></i>
                    <div class="stat-number" data-count="<?= preg_replace('/\D/', '', $s['stat_routes'] ?? '50') ?>">0</div>
                    <div class="stat-suffix">+</div>
                    <div class="stat-label">Rute Pelayaran</div>
                </div>
            </div>
            <div class="col-lg-3 col-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="stat-counter-card">
                    <i class="bi bi-box-seam stat-icon"></i>
                    <div class="stat-number" data-count="<?= preg_replace('/\D/', '', str_replace(['.', ','], '', $s['stat_containers'] ?? '5000')) ?>">0</div>
                    <div class="stat-suffix">+</div>
                    <div class="stat-label">Kontainer/Tahun</div>
                </div>
            </div>
            <div class="col-lg-3 col-6" data-aos="zoom-in" data-aos-delay="300">
                <div class="stat-counter-card">
                    <i class="bi bi-people stat-icon"></i>
                    <div class="stat-number" data-count="<?= preg_replace('/\D/', '', $s['stat_clients'] ?? '200') ?>">0</div>
                    <div class="stat-suffix">+</div>
                    <div class="stat-label">Pelanggan Setia</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vision Mission -->
<section class="section-pad bg-light pb-5 mb-4">
    <div class="container">
        <div class="row justify-content-center text-center mb-1">
            <div class="col-lg-7" data-aos="fade-up">
                <span class="section-label">Nilai Kami</span>
                <h2 class="section-title">Visi & Misi</h2>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-6" data-aos="fade-up">
                <div class="p-4 bg-white rounded-4 h-100 shadow-sm">
                    <div class="mb-3"><i class="bi bi-eye-fill" style="font-size:2rem;"></i></div>
                    <h4>Visi</h4>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($s['vision'] ?? 'Menjadi perusahaan logistik laut terdepan yang menghubungkan seluruh nusantara dengan layanan yang handal, efisien, dan terpercaya.')) ?></p>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="p-4 bg-white rounded-4 h-100 shadow-sm">
                    <div class="mb-3"><i class="bi bi-bullseye" style="font-size:2rem;"></i></div>
                    <h4>Misi</h4>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($s['mission'] ?? "1. Menyediakan layanan pengiriman laut yang tepat waktu dan aman.\n2. Mengembangkan jaringan rute pelayaran yang semakin luas.\n3. Memberikan solusi logistik yang inovatif dan kompetitif.\n4. Membangun hubungan jangka panjang dengan pelanggan.")) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>