<?php
// admin/settings.php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdminLogin();
$admin = currentAdmin();

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getPDO();
    $oldSettings = getAllSettings();

    $fields = [
        'site_name','site_tagline','site_description','meta_keywords',
        'hero_title','hero_subtitle','hero_btn1_text','hero_btn1_url','hero_btn2_text','hero_btn2_url',
        'stat_years','stat_routes','stat_containers','stat_clients',
        'about_title','about_description','vision','mission',
        'cta_title','cta_description',
        'contact_phone','contact_whatsapp','contact_email','contact_address',
        'social_instagram','social_facebook','social_linkedin',
        'footer_copyright',
        'smtp_gmail','smtp_app_password','smtp_from_name','smtp_reply_to','smtp_default_subject',
    ];

    $fieldLabels = [
        'site_name'=>'Nama Situs','site_tagline'=>'Tagline','site_description'=>'Deskripsi Situs',
        'meta_keywords'=>'Meta Keywords','hero_title'=>'Judul Hero','hero_subtitle'=>'Sub-judul Hero',
        'hero_btn1_text'=>'Teks Tombol 1','hero_btn1_url'=>'URL Tombol 1',
        'hero_btn2_text'=>'Teks Tombol 2','hero_btn2_url'=>'URL Tombol 2',
        'stat_years'=>'Statistik: Tahun','stat_routes'=>'Statistik: Rute',
        'stat_containers'=>'Statistik: Kontainer','stat_clients'=>'Statistik: Klien',
        'about_title'=>'Judul Tentang','about_description'=>'Deskripsi Tentang',
        'vision'=>'Visi','mission'=>'Misi','cta_title'=>'Judul CTA','cta_description'=>'Deskripsi CTA',
        'contact_phone'=>'Telepon','contact_whatsapp'=>'WhatsApp',
        'contact_email'=>'Email Kontak','contact_address'=>'Alamat',
        'social_instagram'=>'Instagram','social_facebook'=>'Facebook','social_linkedin'=>'LinkedIn',
        'footer_copyright'=>'Footer Copyright','smtp_gmail'=>'SMTP Gmail',
        'smtp_app_password'=>'SMTP App Password','smtp_from_name'=>'SMTP Nama Pengirim',
        'smtp_reply_to'=>'SMTP Reply-To','smtp_default_subject'=>'SMTP Subject Default',
    ];

    $changes = [];
    foreach ($fields as $f) {
        if (!isset($_POST[$f])) continue;
        $newVal = $_POST[$f];
        $oldVal = $oldSettings[$f] ?? '';
        if ($f === 'smtp_app_password') {
            if ($newVal !== '' && $newVal !== $oldVal)
                $changes[$fieldLabels[$f]] = ['old'=>'(tersembunyi)','new'=>'(diperbarui)'];
        } elseif ($newVal !== $oldVal) {
            $changes[$fieldLabels[$f]] = ['old'=>$oldVal,'new'=>$newVal];
        }
        $pdo->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")
            ->execute([$f,$newVal,$newVal]);
    }

    foreach (['logo','favicon','about_image','og_image'] as $imgKey) {
        if (!empty($_FILES[$imgKey]['name'])) {
            $path = handleUpload($_FILES[$imgKey], $imgKey);
            if ($path) {
                $pdo->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")
                    ->execute([$imgKey,$path,$path]);
                $labels = ['logo'=>'Logo','favicon'=>'Favicon','about_image'=>'Foto Tentang','og_image'=>'OG Image'];
                $changes[$labels[$imgKey]??$imgKey] = ['old'=>basename($oldSettings[$imgKey]??'-'),'new'=>basename($path)];
            }
        }
    }
    if (!empty($_FILES['hero_video']['name'])) {
        $path = handleVideoUpload($_FILES['hero_video']);
        if ($path) {
            $pdo->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")
                ->execute(['hero_video',$path,$path]);
            $changes['Video Hero'] = ['old'=>basename($oldSettings['hero_video']??'-'),'new'=>basename($path)];
        }
    }

    if (!empty($changes)) {
        logActivity($admin['id'], 'UPDATE_SETTINGS', 'Memperbarui pengaturan situs ('.count($changes).' perubahan)', $changes);
        $success = 'Pengaturan disimpan! '.count($changes).' field diubah.';
    } else {
        // TIDAK log sama sekali jika tidak ada perubahan
        $success = 'Pengaturan disimpan. Tidak ada perubahan.';
    }
    $s = getAllSettings();
}
$s = getAllSettings();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Pengaturan — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/admin.css">
</head>
<body>
<div class="adm-layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <button class="topbar-toggle" id="topbarToggle"><i class="bi bi-list"></i></button>
            <div class="topbar-title"><i class="bi bi-gear-fill me-2"></i>Pengaturan Situs</div>
        </div>
        <div class="adm-content">
            <?php if ($success): ?><div class="adm-alert success"><i class="bi bi-check-circle-fill"></i><?= $success ?></div><?php endif; ?>
            <?php if ($error): ?><div class="adm-alert error"><i class="bi bi-exclamation-circle"></i><?= $error ?></div><?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <!-- FIX: semua button tab harus type="button" agar tidak auto-submit form -->
                <ul class="nav nav-tabs mb-4" id="settingsTabs">
                    <li class="nav-item"><button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-general">Umum</button></li>
                    <li class="nav-item"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-hero">Hero</button></li>
                    <li class="nav-item"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-about">Tentang</button></li>
                    <li class="nav-item"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-contact">Kontak</button></li>
                    <li class="nav-item"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-social">Sosial Media</button></li>
                    <li class="nav-item"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-stats">Statistik</button></li>
                    <li class="nav-item"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-smtp">Email SMTP</button></li>
                </ul>

                <div class="tab-content">
                    <!-- GENERAL -->
                    <div class="tab-pane fade show active" id="tab-general">
                        <div class="adm-card">
                            <div class="adm-card-header"><h5>Informasi Situs</h5></div>
                            <div class="adm-card-body">
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="adm-form-label">Nama Situs</label><input type="text" name="site_name" class="adm-input" value="<?= htmlspecialchars($s['site_name']??'') ?>"></div>
                                    <div class="col-md-6"><label class="adm-form-label">Tagline</label><input type="text" name="site_tagline" class="adm-input" value="<?= htmlspecialchars($s['site_tagline']??'') ?>"></div>
                                    <div class="col-12"><label class="adm-form-label">Deskripsi Situs</label><textarea name="site_description" class="adm-input"><?= htmlspecialchars($s['site_description']??'') ?></textarea></div>
                                    <div class="col-12"><label class="adm-form-label">Meta Keywords</label><input type="text" name="meta_keywords" class="adm-input" value="<?= htmlspecialchars($s['meta_keywords']??'') ?>" placeholder="pengiriman laut, ekspedisi, batam"></div>
                                    <div class="col-md-4">
                                        <label class="adm-form-label">Logo</label>
                                        <?php if (!empty($s['logo'])): ?><img src="<?= assetUrl($s['logo']) ?>" class="img-preview d-block mb-2"><?php endif; ?>
                                        <input type="file" name="logo" class="adm-input" accept="image/*">
                                        <p class="adm-form-hint">PNG/JPG, maks 2MB</p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="adm-form-label">Favicon</label>
                                        <?php if (!empty($s['favicon'])): ?><img src="<?= assetUrl($s['favicon']) ?>" class="img-preview d-block mb-2"><?php endif; ?>
                                        <input type="file" name="favicon" class="adm-input" accept="image/*">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="adm-form-label">OG Image</label>
                                        <?php if (!empty($s['og_image'])): ?><img src="<?= assetUrl($s['og_image']) ?>" class="img-preview d-block mb-2"><?php endif; ?>
                                        <input type="file" name="og_image" class="adm-input" accept="image/*">
                                    </div>
                                    <div class="col-12"><label class="adm-form-label">Copyright Footer</label><input type="text" name="footer_copyright" class="adm-input" value="<?= htmlspecialchars($s['footer_copyright']??'© 2025 PT. Prima Bahari Sejahtera.') ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- HERO -->
                    <div class="tab-pane fade" id="tab-hero">
                        <div class="adm-card">
                            <div class="adm-card-header"><h5>Konten Hero / Banner Utama</h5></div>
                            <div class="adm-card-body">
                                <div class="row g-3">
                                    <div class="col-12"><label class="adm-form-label">Judul Utama Hero</label><input type="text" name="hero_title" class="adm-input" value="<?= htmlspecialchars($s['hero_title']??'') ?>"></div>
                                    <div class="col-12"><label class="adm-form-label">Sub-judul Hero</label><textarea name="hero_subtitle" class="adm-input" rows="3"><?= htmlspecialchars($s['hero_subtitle']??'') ?></textarea></div>
                                    <div class="col-md-3"><label class="adm-form-label">Teks Tombol 1</label><input type="text" name="hero_btn1_text" class="adm-input" value="<?= htmlspecialchars($s['hero_btn1_text']??'Lihat Layanan') ?>"></div>
                                    <div class="col-md-3"><label class="adm-form-label">URL Tombol 1</label><input type="text" name="hero_btn1_url" class="adm-input" value="<?= htmlspecialchars($s['hero_btn1_url']??'/services.php') ?>"></div>
                                    <div class="col-md-3"><label class="adm-form-label">Teks Tombol 2</label><input type="text" name="hero_btn2_text" class="adm-input" value="<?= htmlspecialchars($s['hero_btn2_text']??'Hubungi Kami') ?>"></div>
                                    <div class="col-md-3"><label class="adm-form-label">URL Tombol 2</label><input type="text" name="hero_btn2_url" class="adm-input" value="<?= htmlspecialchars($s['hero_btn2_url']??'/contact.php') ?>"></div>
                                    <div class="col-md-6">
                                        <label class="adm-form-label">Video Hero (MP4)</label>
                                        <?php if (!empty($s['hero_video'])): ?><p class="adm-form-hint mb-1">Aktif: <?= basename($s['hero_video']) ?></p><?php endif; ?>
                                        <input type="file" name="hero_video" class="adm-input" accept="video/mp4,video/webm">
                                        <p class="adm-form-hint">MP4/WebM, maks 50MB.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ABOUT -->
                    <div class="tab-pane fade" id="tab-about">
                        <div class="adm-card">
                            <div class="adm-card-header"><h5>Konten Tentang Kami</h5></div>
                            <div class="adm-card-body">
                                <div class="row g-3">
                                    <div class="col-12"><label class="adm-form-label">Judul Tentang</label><input type="text" name="about_title" class="adm-input" value="<?= htmlspecialchars($s['about_title']??'') ?>"></div>
                                    <div class="col-12"><label class="adm-form-label">Deskripsi Tentang</label><textarea name="about_description" class="adm-input" rows="5"><?= htmlspecialchars($s['about_description']??'') ?></textarea></div>
                                    <div class="col-md-6"><label class="adm-form-label">Visi</label><textarea name="vision" class="adm-input" rows="4"><?= htmlspecialchars($s['vision']??'') ?></textarea></div>
                                    <div class="col-md-6"><label class="adm-form-label">Misi</label><textarea name="mission" class="adm-input" rows="4"><?= htmlspecialchars($s['mission']??'') ?></textarea></div>
                                    <div class="col-md-6">
                                        <label class="adm-form-label">Foto Tentang Kami</label>
                                        <?php if (!empty($s['about_image'])): ?><img src="<?= uploadUrl($s['about_image']) ?>" class="img-preview d-block mb-2"><?php endif; ?>
                                        <input type="file" name="about_image" class="adm-input" accept="image/*">
                                    </div>
                                    <div class="col-12"><label class="adm-form-label">Judul CTA Section</label><input type="text" name="cta_title" class="adm-input" value="<?= htmlspecialchars($s['cta_title']??'') ?>"></div>
                                    <div class="col-12"><label class="adm-form-label">Deskripsi CTA</label><textarea name="cta_description" class="adm-input" rows="2"><?= htmlspecialchars($s['cta_description']??'') ?></textarea></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CONTACT -->
                    <div class="tab-pane fade" id="tab-contact">
                        <div class="adm-card">
                            <div class="adm-card-header"><h5>Informasi Kontak</h5></div>
                            <div class="adm-card-body">
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="adm-form-label">No. Telepon</label><input type="text" name="contact_phone" class="adm-input" value="<?= htmlspecialchars($s['contact_phone']??'') ?>"></div>
                                    <div class="col-md-6"><label class="adm-form-label">No. WhatsApp (format: 628xxx)</label><input type="text" name="contact_whatsapp" class="adm-input" value="<?= htmlspecialchars($s['contact_whatsapp']??'') ?>"></div>
                                    <div class="col-md-6"><label class="adm-form-label">Email</label><input type="email" name="contact_email" class="adm-input" value="<?= htmlspecialchars($s['contact_email']??'') ?>"></div>
                                    <div class="col-12"><label class="adm-form-label">Alamat</label><textarea name="contact_address" class="adm-input" rows="3"><?= htmlspecialchars($s['contact_address']??'') ?></textarea></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SOCIAL -->
                    <div class="tab-pane fade" id="tab-social">
                        <div class="adm-card">
                            <div class="adm-card-header"><h5>Media Sosial</h5></div>
                            <div class="adm-card-body">
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="adm-form-label"><i class="bi bi-instagram me-1"></i>Instagram URL</label><input type="url" name="social_instagram" class="adm-input" value="<?= htmlspecialchars($s['social_instagram']??'') ?>"></div>
                                    <div class="col-md-6"><label class="adm-form-label"><i class="bi bi-facebook me-1"></i>Facebook URL</label><input type="url" name="social_facebook" class="adm-input" value="<?= htmlspecialchars($s['social_facebook']??'') ?>"></div>
                                    <div class="col-md-6"><label class="adm-form-label"><i class="bi bi-linkedin me-1"></i>LinkedIn URL</label><input type="url" name="social_linkedin" class="adm-input" value="<?= htmlspecialchars($s['social_linkedin']??'') ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STATS -->
                    <div class="tab-pane fade" id="tab-stats">
                        <div class="adm-card">
                            <div class="adm-card-header"><h5>Angka Statistik Homepage</h5></div>
                            <div class="adm-card-body">
                                <div class="row g-3">
                                    <div class="col-md-3"><label class="adm-form-label">Tahun Pengalaman</label><input type="text" name="stat_years" class="adm-input" value="<?= htmlspecialchars($s['stat_years']??'10+') ?>"></div>
                                    <div class="col-md-3"><label class="adm-form-label">Rute Pelayaran</label><input type="text" name="stat_routes" class="adm-input" value="<?= htmlspecialchars($s['stat_routes']??'50+') ?>"></div>
                                    <div class="col-md-3"><label class="adm-form-label">Kontainer/Tahun</label><input type="text" name="stat_containers" class="adm-input" value="<?= htmlspecialchars($s['stat_containers']??'5.000+') ?>"></div>
                                    <div class="col-md-3"><label class="adm-form-label">Pelanggan Setia</label><input type="text" name="stat_clients" class="adm-input" value="<?= htmlspecialchars($s['stat_clients']??'200+') ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SMTP -->
                    <div class="tab-pane fade" id="tab-smtp">
                        <div class="adm-card">
                            <div class="adm-card-header"><h5>Konfigurasi Email SMTP (Gmail)</h5></div>
                            <div class="adm-card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="adm-form-label">Gmail Address</label>
                                        <input type="email" name="smtp_gmail" class="adm-input" value="<?= htmlspecialchars($s['smtp_gmail']??'') ?>" placeholder="nama@gmail.com">
                                        <p class="adm-form-hint">Akun Gmail yang dipakai untuk mengirim email.</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="adm-form-label">App Password</label>
                                        <input type="password" name="smtp_app_password" class="adm-input" value="<?= htmlspecialchars($s['smtp_app_password']??'') ?>" placeholder="xxxx xxxx xxxx xxxx" autocomplete="new-password">
                                        <p class="adm-form-hint">Buat di <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener">myaccount.google.com/apppasswords</a> (2FA harus aktif).</p>
                                    </div>
                                    <div class="col-md-6"><label class="adm-form-label">Nama Pengirim</label><input type="text" name="smtp_from_name" class="adm-input" value="<?= htmlspecialchars($s['smtp_from_name']??($s['site_name']??'')) ?>" placeholder="Nama perusahaan"></div>
                                    <div class="col-md-6">
                                        <label class="adm-form-label">Reply-To Email <small class="text-muted">(opsional)</small></label>
                                        <input type="email" name="smtp_reply_to" class="adm-input" value="<?= htmlspecialchars($s['smtp_reply_to']??'') ?>" placeholder="admin@domain.com">
                                        <p class="adm-form-hint">Jika kosong, pakai Gmail di atas.</p>
                                    </div>
                                    <div class="col-12"><label class="adm-form-label">Subject Default Balasan</label><input type="text" name="smtp_default_subject" class="adm-input" value="<?= htmlspecialchars($s['smtp_default_subject']??'Re: Pesan dari website '.($s['site_name']??'')) ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn-adm save"><i class="bi bi-check-lg me-2"></i>Simpan Semua Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const toggle=document.getElementById('topbarToggle'),sidebar=document.getElementById('pbsSidebar'),overlay=document.getElementById('sidebarOverlay'),close=document.getElementById('sidebarClose');
toggle?.addEventListener('click',()=>{sidebar.classList.toggle('open');overlay.classList.toggle('show');});
overlay?.addEventListener('click',()=>{sidebar.classList.remove('open');overlay.classList.remove('show');});
close?.addEventListener('click',()=>{sidebar.classList.remove('open');overlay.classList.remove('show');});
const tabLinks=document.querySelectorAll('#settingsTabs [data-bs-toggle="tab"]');
const savedHash=location.hash||'#tab-general';
const target=document.querySelector('#settingsTabs [data-bs-target="'+savedHash+'"]');
if(target) bootstrap.Tab.getOrCreateInstance(target).show();
tabLinks.forEach(btn=>{btn.addEventListener('shown.bs.tab',e=>{history.replaceState(null,'',e.target.dataset.bsTarget);});});
</script>
</body>
</html>