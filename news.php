<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$s = getAllSettings();

$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$news = null;
if ($id > 0) {
    $news = getNewsById($id);
    if (!$news || !$news['is_published']) {
        header('Location: ' . BASE_URL . '/news.php');
        exit;
    }
    try {
        getPDO()->prepare("UPDATE news SET views = views + 1 WHERE id = ?")->execute([$id]);
    } catch (Exception $e) {}
    $pageTitle = $news['title'];
} else {
    $pageTitle = 'Berita & Informasi';
    $allNews   = getNews(100);
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- Page Header -->
<section class="page-header pt-5 mt-5">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <h1 class="page-header-title" data-aos="fade-up">Berita & Informasi</h1>
        <p class="page-header-sub" data-aos="fade-up" data-aos-delay="100">Update terbaru dari PT. Prima Bahari Sejahtera</p>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="150">
            <ol class="breadcrumb breadcrumb-dark">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/news.php">Berita</a></li>
                <?php if ($news): ?><li class="breadcrumb-item active">Detail</li><?php endif; ?>
            </ol>
        </nav>
    </div>
</section>

<?php if ($news): ?>
<!-- ── DETAIL VIEW ── -->
<section class="section-pad mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!empty($news['image'])): ?>
                <img src="<?= uploadUrl($news['image']) ?>" alt="<?= htmlspecialchars($news['title']) ?>" class="img-fluid rounded-4 mb-4 w-100" style="max-height:420px;object-fit:cover;">
                <?php endif; ?>
                <div class="d-flex align-items-center gap-3 mb-3 text-muted small">
                    <?php if (!empty($news['category'])): ?>
                    <span class="badge bg-primary"><?= htmlspecialchars($news['category']) ?></span>
                    <?php endif; ?>
                    <span><i class="bi bi-calendar3 me-1"></i><?= formatDate($news['published_at']) ?></span>
                    <?php if (!empty($news['author'])): ?>
                    <span><i class="bi bi-person me-1"></i><?= htmlspecialchars($news['author']) ?></span>
                    <?php endif; ?>
                </div>
                <h1 class="mb-4" style="font-size:clamp(1.5rem,4vw,2.2rem);"><?= htmlspecialchars($news['title']) ?></h1>
                <div class="news-content" style="line-height:1.9;color:#334155;text-align:justify;">
                    <?= nl2br(htmlspecialchars($news['content'])) ?>
                </div>
                <hr class="my-4">
                <a href="<?= BASE_URL ?>/news.php" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i> Kembali ke Berita
                </a>
            </div>
        </div>
    </div>
</section>

<?php else: ?>
<!-- ── LIST VIEW ── -->
<section class="section-pad pb-5 mb-4">
    <div class="container">
        <?php if (empty($allNews)): ?>
        <div class="text-center py-5">
            <i class="bi bi-newspaper" style="font-size:3rem;color:#ccc;"></i>
            <p class="mt-3 text-muted">Belum ada berita yang dipublikasikan.</p>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($allNews as $i => $n): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 60 ?>">
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
                        <p class="news-excerpt"><?= htmlspecialchars(truncate($n['excerpt'] ?? $n['content'] ?? '', 120)) ?></p>
                        <a href="<?= BASE_URL ?>/news.php?id=<?= $n['id'] ?>" class="news-read-more">
                            Baca Selengkapnya <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>