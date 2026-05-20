<?php
// contact.php — Contact Page
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$s = getAllSettings();
$pageTitle = 'Kontak';
$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name'       => trim($_POST['name']    ?? ''),
        'email'      => trim($_POST['email']   ?? ''),
        'phone'      => trim($_POST['phone']   ?? ''),
        'subject'    => trim($_POST['subject'] ?? ''),
        'message'    => trim($_POST['message'] ?? ''),
        'cargo_type' => trim($_POST['cargo_type'] ?? 'Lainnya'),
    ];
    if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
        $error = 'Nama, email, dan pesan wajib diisi.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $success = saveMessage($data);
        if (!$success) $error = 'Gagal mengirim pesan. Silakan coba lagi.';
    }
}

$wa = preg_replace('/[^0-9]/', '', $s['contact_whatsapp'] ?? '');
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- Page Header -->
<section class="page-header pt-5 mt-5">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <h1 class="page-header-title" data-aos="fade-up">Hubungi Kami</h1>
        <p class="page-header-sub" data-aos="fade-up" data-aos-delay="100">Kami siap membantu kebutuhan pengiriman Anda</p>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="150">
            <ol class="breadcrumb breadcrumb-dark">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Beranda</a></li>
                <li class="breadcrumb-item active">Kontak</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section-pad pb-5 mb-4">
    <div class="container">
        <div class="row g-5">
            <!-- Info -->
            <div class="col-lg-4" data-aos="fade-right">
                <h3 class="mb-4">Informasi Kontak</h3>
                <div class="d-flex flex-column gap-4">
                    <?php if (!empty($s['contact_address'])): ?>
                    <div class="d-flex gap-3">
                        <div class="contact-icon-wrap"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <strong class="d-block mb-1">Alamat</strong>
                            <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($s['contact_address'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($s['contact_phone'])): ?>
                    <div class="d-flex gap-3">
                        <div class="contact-icon-wrap"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <strong class="d-block mb-1">Telepon</strong>
                            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $s['contact_phone']) ?>" class="text-muted text-decoration-none">
                                <?= htmlspecialchars($s['contact_phone']) ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($wa): ?>
                    <div class="d-flex gap-3">
                        <div class="contact-icon-wrap text-success"><i class="bi bi-whatsapp"></i></div>
                        <div>
                            <strong class="d-block mb-1">WhatsApp</strong>
                            <a href="https://wa.me/<?= $wa ?>" target="_blank" class="text-muted text-decoration-none">
                                <?= htmlspecialchars($s['contact_whatsapp']) ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($s['contact_email'])): ?>
                    <div class="d-flex gap-3">
                        <div class="contact-icon-wrap"><i class="bi bi-envelope-fill"></i></div>
                        <div>
                            <strong class="d-block mb-1">Email</strong>
                            <a href="mailto:<?= htmlspecialchars($s['contact_email']) ?>" class="text-muted text-decoration-none">
                                <?= htmlspecialchars($s['contact_email']) ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($wa): ?>
                <a href="https://wa.me/<?= $wa ?>?text=Halo%2C%20saya%20ingin%20bertanya%20tentang%20layanan%20PBS." target="_blank"
                   class="btn btn-success w-100 mt-4 py-3 rounded-pill">
                    <i class="bi bi-whatsapp me-2"></i> Chat via WhatsApp
                </a>
                <?php endif; ?>
            </div>

            <!-- Form -->
            <div class="col-lg-8" data-aos="fade-left">
                <?php if ($success): ?>
                <div class="alert alert-success rounded-3 p-4">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Pesan Terkirim!</strong> Terima kasih, kami akan menghubungi Anda segera.
                </div>
                <?php endif; ?>
                <?php if ($error): ?>
                <div class="alert alert-danger rounded-3"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="p-4 p-lg-5 bg-white rounded-4 shadow-sm">
                    <h4 class="mb-4">Kirim Pesan</h4>
                    <form method="POST" action="" id="contactForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-600">Nama Lengkap *</label>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600">Email *</label>
                                <input type="email" name="email" class="form-control" placeholder="email@domain.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600">No. Telepon / WA</label>
                                <input type="tel" name="phone" class="form-control" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600">Jenis Muatan</label>
                                <select name="cargo_type" class="form-select">
                                    <option value="FCL">FCL (Full Container Load)</option>
                                    <option value="LCL">LCL (Less than Container Load)</option>
                                    <option value="Break Bulk">Break Bulk</option>
                                    <option value="Project Cargo">Project Cargo</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-600">Subjek</label>
                                <input type="text" name="subject" class="form-control" placeholder="Pertanyaan tentang layanan pengiriman" value="<?= htmlspecialchars($_POST['subject'] ?? isset($_GET['service']) ? 'Pertanyaan tentang: ' . htmlspecialchars($_GET['service'] ?? '') : '') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-600">Pesan *</label>
                                <textarea name="message" class="form-control" rows="5" placeholder="Tuliskan kebutuhan pengiriman Anda..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill w-100">
                                    <i class="bi bi-send me-2"></i> Kirim Pesan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.contact-icon-wrap { width:40px;height:40px;border-radius:10px;background:var(--pbs-red-light);color:var(--pbs-red);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.contact-icon-wrap.text-success { background:#f0fdf4;color:#16a34a; }
.fw-600 { font-weight:600; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>