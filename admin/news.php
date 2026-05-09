<?php
// admin/news.php — CRUD Berita
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdminLogin();
$admin = currentAdmin();
$pdo = getPDO();

if (isset($_GET['delete'])) { $pdo->prepare("DELETE FROM news WHERE id=?")->execute([(int)$_GET['delete']]); header('Location: ' . BASE_URL . '/admin/news.php'); exit; }
if (isset($_GET['toggle'])) { $pdo->prepare("UPDATE news SET is_published=1-is_published WHERE id=?")->execute([(int)$_GET['toggle']]); header('Location: ' . BASE_URL . '/admin/news.php'); exit; }

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = (int)($_POST['id'] ?? 0);
    $title        = sanitize($_POST['title'] ?? '');
    $excerpt      = sanitize($_POST['excerpt'] ?? '');
    $content      = sanitize($_POST['content'] ?? '');
    $category     = sanitize($_POST['category'] ?? 'Berita');
    $author       = sanitize($_POST['author'] ?? $admin['name']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $published_at = $_POST['published_at'] ?? date('Y-m-d H:i:s');
    $image = $_POST['existing_image'] ?? '';
    if (!empty($_FILES['image']['name'])) { $up = handleUpload($_FILES['image'], 'news'); if ($up) $image = $up; }

    if (empty($title)||empty($content)) { $error = 'Judul dan konten wajib diisi.'; }
    else {
        if ($id > 0) {
            $pdo->prepare("UPDATE news SET title=?,excerpt=?,content=?,category=?,author=?,is_published=?,published_at=?,image=? WHERE id=?")
                ->execute([$title,$excerpt,$content,$category,$author,$is_published,$published_at,$image,$id]);
        } else {
            $pdo->prepare("INSERT INTO news(title,excerpt,content,category,author,is_published,published_at,image) VALUES(?,?,?,?,?,?,?,?)")
                ->execute([$title,$excerpt,$content,$category,$author,$is_published,$published_at,$image]);
        }
        logActivity($admin['id'], 'SAVE_NEWS', $title);
        $success = 'Berita berhasil disimpan!';
    }
}

$allNews = $pdo->query("SELECT * FROM news ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Berita — <?= APP_NAME ?></title>
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
            <div class="topbar-title"><i class="bi bi-newspaper me-2"></i>Kelola Berita</div>
            <div class="topbar-actions">
                <button class="topbar-btn btn-primary-adm" onclick="openModal()"><i class="bi bi-plus-lg me-1"></i>Tambah Berita</button>
            </div>
        </div>
        <div class="adm-content">
            <?php if ($success): ?><div class="adm-alert success"><i class="bi bi-check-circle-fill"></i><?= $success ?></div><?php endif; ?>
            <?php if ($error): ?><div class="adm-alert error"><i class="bi bi-exclamation-circle"></i><?= $error ?></div><?php endif; ?>

            <div class="adm-card">
                <div class="adm-card-header"><h5>Semua Berita (<?= count($allNews) ?>)</h5></div>
                <?php if (empty($allNews)): ?>
                <div class="empty-state"><i class="bi bi-newspaper"></i><p>Belum ada berita.</p></div>
                <?php else: ?>
                <div class="table-responsive">
                <table class="adm-table">
                    <thead><tr><th>Gambar</th><th>Judul</th><th>Kategori</th><th>Penulis</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php foreach ($allNews as $n): ?>
                    <tr>
                        <td><?php if (!empty($n['image'])): ?><img src="<?= uploadUrl($n['image']) ?>" class="thumb"><?php else: ?><div style="width:48px;height:36px;background:#f1f5f9;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#aaa;"><i class="bi bi-image"></i></div><?php endif; ?></td>
                        <td><strong><?= htmlspecialchars(truncate($n['title'], 50)) ?></strong></td>
                        <td><span class="adm-badge blue"><?= htmlspecialchars($n['category'] ?? 'Berita') ?></span></td>
                        <td class="text-muted small"><?= htmlspecialchars($n['author'] ?? '-') ?></td>
                        <td class="text-muted small"><?= date('d/m/Y', strtotime($n['published_at'])) ?></td>
                        <td><a href="?toggle=<?= $n['id'] ?>" class="adm-badge <?= $n['is_published'] ? 'green' : 'gray' ?>"><?= $n['is_published'] ? 'Publik' : 'Draft' ?></a></td>
                        <td>
                            <a href="<?= BASE_URL ?>/news.php?id=<?= $n['id'] ?>" target="_blank" class="btn-adm secondary"><i class="bi bi-eye"></i></a>
                            <a href="#" class="btn-adm edit ms-1" onclick="openModal(<?= htmlspecialchars(json_encode($n)) ?>);return false;"><i class="bi bi-pencil"></i></a>
                            <a href="?delete=<?= $n['id'] ?>" class="btn-adm del ms-1" onclick="return confirm('Hapus berita ini?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="adm-modal-overlay" id="newsModal">
<div class="adm-modal" style="max-width:700px;">
    <div class="adm-modal-header">
        <h5 id="modalTitle">Tambah Berita</h5>
        <button class="adm-modal-close" onclick="closeModal()"><i class="bi bi-x"></i></button>
    </div>
    <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" id="f_id">
    <input type="hidden" name="existing_image" id="f_existing_image">
    <div class="adm-modal-body">
        <div class="row g-3">
            <div class="col-12"><label class="adm-form-label">Judul Berita *</label><input type="text" name="title" id="f_title" class="adm-input" required></div>
            <div class="col-md-4"><label class="adm-form-label">Kategori</label><input type="text" name="category" id="f_category" class="adm-input" placeholder="Berita"></div>
            <div class="col-md-4"><label class="adm-form-label">Penulis</label><input type="text" name="author" id="f_author" class="adm-input"></div>
            <div class="col-md-4"><label class="adm-form-label">Tanggal Publish</label><input type="datetime-local" name="published_at" id="f_published_at" class="adm-input"></div>
            <div class="col-12"><label class="adm-form-label">Ringkasan (Excerpt)</label><textarea name="excerpt" id="f_excerpt" class="adm-input" rows="2" placeholder="Ringkasan singkat berita..."></textarea></div>
            <div class="col-12"><label class="adm-form-label">Konten Lengkap *</label><textarea name="content" id="f_content" class="adm-input" rows="8" required></textarea></div>
            <div class="col-md-8"><label class="adm-form-label">Gambar</label><input type="file" name="image" class="adm-input" accept="image/*"><div id="currentImgWrap" style="display:none;margin-top:8px;"><img id="currentImg" src="" class="img-preview"></div></div>
            <div class="col-md-4 d-flex align-items-end pb-1"><div class="form-check"><input type="checkbox" name="is_published" id="f_is_published" class="form-check-input" checked><label class="form-check-label" for="f_is_published">Publish sekarang</label></div></div>
        </div>
    </div>
    <div class="adm-modal-footer">
        <button type="button" class="btn-adm secondary" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn-adm save"><i class="bi bi-check-lg me-1"></i>Simpan</button>
    </div>
    </form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const toggle=document.getElementById('topbarToggle'),sidebar=document.getElementById('pbsSidebar'),ov=document.getElementById('sidebarOverlay'),cl=document.getElementById('sidebarClose');
toggle?.addEventListener('click',()=>{sidebar.classList.toggle('open');ov.classList.toggle('show');});
ov?.addEventListener('click',()=>{sidebar.classList.remove('open');ov.classList.remove('show');});
cl?.addEventListener('click',()=>{sidebar.classList.remove('open');ov.classList.remove('show');});
const modal=document.getElementById('newsModal');
function openModal(d=null){
    if(d){
        document.getElementById('modalTitle').textContent='Edit Berita';
        document.getElementById('f_id').value=d.id;
        document.getElementById('f_title').value=d.title||'';
        document.getElementById('f_category').value=d.category||'Berita';
        document.getElementById('f_author').value=d.author||'';
        document.getElementById('f_excerpt').value=d.excerpt||'';
        document.getElementById('f_content').value=d.content||'';
        document.getElementById('f_is_published').checked=d.is_published==1;
        document.getElementById('f_existing_image').value=d.image||'';
        const dt=d.published_at?d.published_at.replace(' ','T').substring(0,16):'';
        document.getElementById('f_published_at').value=dt;
        if(d.image){document.getElementById('currentImgWrap').style.display='block';document.getElementById('currentImg').src='<?= BASE_URL ?>/public/'+d.image;}
    } else {
        document.getElementById('modalTitle').textContent='Tambah Berita';
        document.getElementById('f_id').value='';
        ['f_title','f_category','f_author','f_excerpt','f_content'].forEach(id=>document.getElementById(id).value='');
        document.getElementById('f_category').value='Berita';
        document.getElementById('f_is_published').checked=true;
        document.getElementById('f_published_at').value=new Date().toISOString().substring(0,16);
        document.getElementById('currentImgWrap').style.display='none';
    }
    modal.classList.add('show');
}
function closeModal(){modal.classList.remove('show');}
modal.addEventListener('click',e=>{if(e.target===modal)closeModal();});
</script>
</body>
</html>