<?php
// admin/dashboard.php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdminLogin();

$admin = currentAdmin();
$pdo   = getPDO();

$counts = [
    'services'     => $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn(),
    'news'         => $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn(),
    'messages'     => $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn(),
    'new_messages' => $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn(),
    'routes'       => $pdo->query("SELECT COUNT(*) FROM routes WHERE is_active=1")->fetchColumn(),
    'testimonials' => $pdo->query("SELECT COUNT(*) FROM testimonials WHERE is_active=1")->fetchColumn(),
];

$recentMessages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentNews     = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 5")->fetchAll();

try {
    $activities = $pdo->query("SELECT al.*, a.full_name FROM activity_log al LEFT JOIN admins a ON al.admin_id=a.id ORDER BY al.created_at DESC LIMIT 8")->fetchAll();
} catch (Exception $e) { $activities = []; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/admin.css">
</head>
<body>
<div class="adm-layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="adm-main">
        <!-- Topbar -->
        <div class="adm-topbar">
            <button class="topbar-toggle" id="topbarToggle"><i class="bi bi-list"></i></button>
            <div class="topbar-title">Dashboard</div>
            <div class="topbar-actions">
                <span class="topbar-btn d-none d-sm-flex"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($admin['name']) ?></span>
                <a href="<?= BASE_URL ?>/" target="_blank" class="topbar-btn"><i class="bi bi-globe me-1"></i> Website</a>
            </div>
        </div>

        <div class="adm-content">
            <!-- Welcome -->
            <div class="mb-4 p-4 rounded-3" style="background:linear-gradient(135deg,#1B4F8A,#0F3260);color:white;">
                <h5 class="fw-800 mb-1">Selamat Datang, <?= htmlspecialchars($admin['name']) ?>! 👋</h5>
                <p class="mb-0 opacity-75 small">Panel kontrol PT. Prima Bahari Sejahtera</p>
            </div>

            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon-wrap blue"><i class="bi bi-grid-fill"></i></div>
                        <div><div class="stat-val"><?= $counts['services'] ?></div><div class="stat-lbl">Layanan</div></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon-wrap green"><i class="bi bi-newspaper"></i></div>
                        <div><div class="stat-val"><?= $counts['news'] ?></div><div class="stat-lbl">Berita</div></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon-wrap <?= $counts['new_messages'] > 0 ? 'red' : 'yellow' ?>">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <div class="stat-val"><?= $counts['messages'] ?></div>
                            <div class="stat-lbl">Pesan <?php if($counts['new_messages']>0): ?><span class="badge bg-danger"><?= $counts['new_messages'] ?> baru</span><?php endif; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon-wrap red"><i class="bi bi-map-fill"></i></div>
                        <div><div class="stat-val"><?= $counts['routes'] ?></div><div class="stat-lbl">Rute Aktif</div></div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Recent Messages -->
                <div class="col-lg-6">
                    <div class="adm-card">
                        <div class="adm-card-header">
                            <h5><i class="bi bi-envelope me-2 text-danger"></i>Pesan Terbaru</h5>
                            <a href="<?= BASE_URL ?>/admin/messages.php" class="btn-adm edit">Lihat Semua</a>
                        </div>
                        <?php if (empty($recentMessages)): ?>
                        <div class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada pesan</p></div>
                        <?php else: ?>
                        <table class="adm-table">
                            <thead><tr><th>Nama</th><th>Subjek</th><th>Waktu</th></tr></thead>
                            <tbody>
                            <?php foreach ($recentMessages as $m): ?>
                            <tr>
                                <td><?= htmlspecialchars($m['name']) ?><?php if(!$m['is_read']): ?> <span class="adm-badge red ms-1">Baru</span><?php endif; ?></td>
                                <td class="text-muted small"><?= htmlspecialchars(truncate($m['subject'] ?? 'Pesan', 30)) ?></td>
                                <td class="text-muted small"><?= timeAgo($m['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent News -->
                <div class="col-lg-6">
                    <div class="adm-card">
                        <div class="adm-card-header">
                            <h5><i class="bi bi-newspaper me-2 text-success"></i>Berita Terbaru</h5>
                            <a href="<?= BASE_URL ?>/admin/news.php" class="btn-adm edit">Kelola</a>
                        </div>
                        <?php if (empty($recentNews)): ?>
                        <div class="empty-state"><i class="bi bi-newspaper"></i><p>Belum ada berita</p></div>
                        <?php else: ?>
                        <table class="adm-table">
                            <thead><tr><th>Judul</th><th>Status</th><th>Tanggal</th></tr></thead>
                            <tbody>
                            <?php foreach ($recentNews as $n): ?>
                            <tr>
                                <td><?= htmlspecialchars(truncate($n['title'], 35)) ?></td>
                                <td><span class="adm-badge <?= $n['is_published'] ? 'green' : 'gray' ?>"><?= $n['is_published'] ? 'Publik' : 'Draft' ?></span></td>
                                <td class="text-muted small"><?= date('d/m/Y', strtotime($n['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-12">
                    <div class="adm-card">
                        <div class="adm-card-header"><h5>Akses Cepat</h5></div>
                        <div class="adm-card-body">
                            <div class="row g-3">
                                <?php
                                $quick = [
                                    ['href'=>'settings.php',     'icon'=>'bi-gear-fill',       'label'=>'Pengaturan Situs',  'color'=>'blue'],
                                    ['href'=>'services.php',     'icon'=>'bi-grid-fill',       'label'=>'Kelola Layanan',    'color'=>'red'],
                                    ['href'=>'routes.php',       'icon'=>'bi-map-fill',        'label'=>'Rute Pelayaran',    'color'=>'green'],
                                    ['href'=>'news.php',         'icon'=>'bi-newspaper',       'label'=>'Kelola Berita',     'color'=>'yellow'],
                                    ['href'=>'testimonials.php', 'icon'=>'bi-chat-quote-fill', 'label'=>'Testimoni',         'color'=>'blue'],
                                    ['href'=>'gallery.php',      'icon'=>'bi-images',          'label'=>'Galeri',            'color'=>'green'],
                                    ['href'=>'messages.php',     'icon'=>'bi-envelope-fill',   'label'=>'Pesan Masuk',       'color'=>'red'],
                                ];
                                foreach ($quick as $q): ?>
                                <div class="col-6 col-md-4 col-lg-3">
                                    <a href="<?= BASE_URL ?>/admin/<?= $q['href'] ?>" class="d-flex align-items-center gap-2 p-3 rounded-3 text-decoration-none"
                                       style="background:var(--adm-bg);border:1px solid var(--adm-border);transition:all .2s;"
                                       onmouseover="this.style.background='white';this.style.borderColor='var(--adm-blue)'"
                                       onmouseout="this.style.background='var(--adm-bg)';this.style.borderColor='var(--adm-border)'">
                                        <div class="stat-icon-wrap <?= $q['color'] ?>" style="width:36px;height:36px;font-size:15px;border-radius:8px;">
                                            <i class="bi <?= $q['icon'] ?>"></i>
                                        </div>
                                        <span style="font-size:13px;font-weight:600;color:var(--adm-text);"><?= $q['label'] ?></span>
                                    </a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const toggle = document.getElementById('topbarToggle');
const sidebar = document.getElementById('pbsSidebar');
const overlay = document.getElementById('sidebarOverlay');
const close   = document.getElementById('sidebarClose');
toggle?.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); });
overlay?.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); });
close?.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); });
</script>
</body>
</html>