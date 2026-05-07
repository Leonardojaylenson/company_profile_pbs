<?php
// admin/includes/sidebar.php
require_once dirname(__DIR__, 2) . '/includes/auth.php';
requireAdminLogin();
$admin = currentAdmin();
$currentFile = basename($_SERVER['PHP_SELF'], '.php');

$menus = [
    ['file' => 'dashboard',    'icon' => 'bi-speedometer2',    'label' => 'Dashboard'],
    ['file' => 'settings',     'icon' => 'bi-gear-fill',       'label' => 'Pengaturan Situs'],
    ['file' => 'services',     'icon' => 'bi-grid-fill',       'label' => 'Layanan'],
    ['file' => 'routes',       'icon' => 'bi-map-fill',        'label' => 'Rute Pelayaran'],
    ['file' => 'news',         'icon' => 'bi-newspaper',       'label' => 'Berita'],
    ['file' => 'testimonials', 'icon' => 'bi-chat-quote-fill', 'label' => 'Testimoni'],
    ['file' => 'gallery',      'icon' => 'bi-images',          'label' => 'Galeri'],
    ['file' => 'messages',     'icon' => 'bi-envelope-fill',   'label' => 'Pesan Masuk'],
];
if ($admin['role'] === 'superadmin') {
    $menus[] = ['file' => 'admins', 'icon' => 'bi-people-fill', 'label' => 'Kelola Admin'];
}
?>
<div class="pbs-sidebar" id="pbsSidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="bi bi-anchor"></i></div>
        <div class="sidebar-brand-text">
            <strong>PBS Admin</strong>
            <span>Control Panel</span>
        </div>
        <button class="sidebar-close d-lg-none" id="sidebarClose"><i class="bi bi-x-lg"></i></button>
    </div>

    <!-- Admin Info -->
    <div class="sidebar-user">
        <div class="sidebar-avatar">
            <?php if (!empty($admin['avatar'])): ?>
                <img src="<?= BASE_URL ?>/public/<?= htmlspecialchars($admin['avatar']) ?>" alt="">
            <?php else: ?>
                <span><?= mb_strtoupper(mb_substr($admin['name'], 0, 1)) ?></span>
            <?php endif; ?>
        </div>
        <div class="sidebar-user-info">
            <strong><?= htmlspecialchars($admin['name']) ?></strong>
            <span class="badge-role"><?= htmlspecialchars($admin['role']) ?></span>
        </div>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav">
        <div class="sidebar-nav-label">Menu Utama</div>
        <?php foreach ($menus as $m): ?>
        <a href="<?= BASE_URL ?>/admin/<?= $m['file'] ?>.php"
           class="sidebar-nav-item <?= $currentFile === $m['file'] ? 'active' : '' ?>">
            <i class="bi <?= $m['icon'] ?>"></i>
            <span><?= $m['label'] ?></span>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- Bottom -->
    <div class="sidebar-bottom">
        <a href="<?= BASE_URL ?>/" target="_blank" class="sidebar-nav-item">
            <i class="bi bi-box-arrow-up-right"></i><span>Lihat Website</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/logout.php" class="sidebar-nav-item text-danger-soft">
            <i class="bi bi-box-arrow-left"></i><span>Keluar</span>
        </a>
    </div>
</div>
<div class="sidebar-overlay d-lg-none" id="sidebarOverlay"></div>