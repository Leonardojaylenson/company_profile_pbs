<?php
// includes/header.php — Public Site Header
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$s = getAllSettings();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(($pageTitle ?? '') ? $pageTitle . ' — ' . $s['site_name'] : $s['site_name']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($s['site_description'] ?? '') ?>">
    <meta name="keywords"    content="<?= htmlspecialchars($s['meta_keywords']    ?? '') ?>">
    <!-- Open Graph -->
    <meta property="og:title"       content="<?= htmlspecialchars($s['site_name']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($s['site_description'] ?? '') ?>">
    <meta property="og:image"       content="<?= assetUrl($s['og_image'] ?? '') ?>">
    <meta property="og:type"        content="website">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= assetUrl($s['favicon'] ?? 'images/favicon.png') ?>">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Fraunces:opsz,wght@9..144,700;9..144,900&display=swap" rel="stylesheet">
    <!-- AOS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
    <?= $extraHead ?? '' ?>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar navbar-expand-lg pbs-navbar fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>/">
            <?php if (!empty($s['logo'])): ?>
                <img src="<?= assetUrl($s['logo']) ?>" alt="<?= htmlspecialchars($s['site_name']) ?>" height="48" class="brand-logo">
            <?php else: ?>
                <span class="brand-text"><?= htmlspecialchars($s['site_name']) ?></span>
            <?php endif; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item me-lg-4"><a class="nav-link <?= $currentPage==='index'?'active':'' ?>" href="<?= BASE_URL ?>/">Beranda</a></li>
                <li class="nav-item me-lg-4"><a class="nav-link <?= $currentPage==='about'?'active':'' ?>" href="<?= BASE_URL ?>/about.php">Tentang Kami</a></li>
                <li class="nav-item me-lg-4"><a class="nav-link <?= $currentPage==='services'?'active':'' ?>" href="<?= BASE_URL ?>/services.php">Layanan</a></li>
                <li class="nav-item me-lg-4"><a class="nav-link <?= $currentPage==='news'?'active':'' ?>" href="<?= BASE_URL ?>/news.php">Berita</a></li>
                <li class="nav-item me-lg-4"><a class="nav-link <?= $currentPage==='contact'?'active':'' ?>" href="<?= BASE_URL ?>/contact.php">Kontak</a></li>

                <li class="nav-item ms-lg-2">
                    <?php if (isAdminLoggedIn()): ?>
                    <a class="btn btn-outline-primary btn-sm px-3 rounded-pill d-inline-flex align-items-center justify-content-center" href="<?= BASE_URL ?>/admin/dashboard.php" style="height: 38px; min-width: 125px;">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <?php else: ?>
                    <a class="btn btn-outline-dark btn-sm px-3 rounded-pill d-inline-flex align-items-center justify-content-center" href="<?= BASE_URL ?>/admin/login.php" style="height: 38px; min-width: 125px;">
                        <i class="bi bi-person-lock me-2"></i> Admin
                    </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>