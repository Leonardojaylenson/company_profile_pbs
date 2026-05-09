<?php
// admin/services.php — CRUD Layanan
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdminLogin();
$admin = currentAdmin();
$pdo = getPDO();

$success = $error = '';

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM services WHERE id=?")->execute([$id]);
    logActivity($admin['id'], 'DELETE_SERVICE', "ID: $id");
    header('Location: ' . BASE_URL . '/admin/services.php?msg=deleted');
    exit;
}
// TOGGLE ACTIVE
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE services SET is_active = 1-is_active WHERE id=?")->execute([$id]);
    header('Location: ' . BASE_URL . '/admin/services.php');
    exit;
}
// TOGGLE FEATURED
if (isset($_GET['featured'])) {
    $id = (int)$_GET['featured'];
    $pdo->prepare("UPDATE services SET is_featured = 1-is_featured WHERE id=?")->execute([$id]);
    header('Location: ' . BASE_URL . '/admin/services.php');
    exit;
}

// SAVE (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)($_POST['id'] ?? 0);
    $title       = sanitize($_POST['title'] ?? '');
    $short_desc  = sanitize($_POST['short_desc'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $icon        = sanitize($_POST['icon'] ?? 'bi-box-seam');
    $price_info  = sanitize($_POST['price_info'] ?? '');
    $sort_order  = (int)($_POST['sort_order'] ?? 0);
    $is_active   = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Features array
    $feats = array_filter(array_map('trim', explode("\n", $_POST['features'] ?? '')));
    $features_json = json_encode(array_values($feats));

    // Image upload
    $image = $_POST['existing_image'] ?? '';
    if (!empty($_FILES['image']['name'])) {
        $up = handleUpload($_FILES['image'], 'services');
        if ($up) $image = $up;
    }

    if (empty($title)) {
        $error = 'Judul layanan wajib diisi.';
    } else {
        if ($id > 0) {
            $pdo->prepare("UPDATE services SET title=?,short_desc=?,description=?,icon=?,price_info=?,image=?,features=?,sort_order=?,is_active=?,is_featured=? WHERE id=?")
                ->execute([$title,$short_desc,$description,$icon,$price_info,$image,$features_json,$sort_order,$is_active,$is_featured,$id]);
            logActivity($admin['id'], 'UPDATE_SERVICE', $title);
        } else {
            $pdo->prepare("INSERT INTO services(title,short_desc,description,icon,price_info,image,features,sort_order,is_active,is_featured) VALUES(?,?,?,?,?,?,?,?,?,?)")
                ->execute([$title,$short_desc,$description,$icon,$price_info,$image,$features_json,$sort_order,$is_active,$is_featured]);
            logActivity($admin['id'], 'ADD_SERVICE', $title);
        }
        $success = 'Layanan berhasil disimpan!';
    }
}

$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC, id ASC")->fetchAll();
$edit = null;
if (isset($_GET['edit'])) {
    $edit = $pdo->prepare("SELECT * FROM services WHERE id=?");
    $edit->execute([(int)$_GET['edit']]);
    $edit = $edit->fetch();
}
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Layanan — <?= APP_NAME ?></title>
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
            <div class="topbar-title"><i class="bi bi-grid-fill me-2"></i>Kelola Layanan</div>
            <div class="topbar-actions">
                <button class="topbar-btn btn-primary-adm" onclick="openModal()"><i class="bi bi-plus-lg me-1"></i>Tambah Layanan</button>
            </div>
        </div>
        <div class="adm-content">
            <?php if ($success): ?><div class="adm-alert success"><i class="bi bi-check-circle-fill"></i><?= $success ?></div><?php endif; ?>
            <?php if ($error): ?><div class="adm-alert error"><i class="bi bi-exclamation-circle"></i><?= $error ?></div><?php endif; ?>
            <?php if ($msg === 'deleted'): ?><div class="adm-alert error"><i class="bi bi-trash"></i>Layanan dihapus.</div><?php endif; ?>

            <div class="adm-card">
                <div class="adm-card-header"><h5>Daftar Layanan (<?= count($services) ?>)</h5></div>
                <?php if (empty($services)): ?>
                <div class="empty-state"><i class="bi bi-grid"></i><p>Belum ada layanan. Klik "+ Tambah Layanan".</p></div>
                <?php else: ?>
                <div class="table-responsive">
                <table class="adm-table">
                    <thead><tr><th>Gambar</th><th>Judul</th><th>Deskripsi</th><th>Sort</th><th>Status</th><th>Featured</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php foreach ($services as $svc): ?>
                    <tr>
                        <td><?php if (!empty($svc['image'])): ?><img src="<?= uploadUrl($svc['image']) ?>" class="thumb"><?php else: ?><div style="width:48px;height:36px;background:#f1f5f9;border-radius:6px;display:flex;align-items:center;justify-content:center;"><i class="bi <?= htmlspecialchars($svc['icon']) ?>"></i></div><?php endif; ?></td>
                        <td><strong><?= htmlspecialchars($svc['title']) ?></strong><br><small class="text-muted"><i class="bi <?= htmlspecialchars($svc['icon']) ?>"></i> <?= htmlspecialchars($svc['icon']) ?></small></td>
                        <td class="text-muted small"><?= htmlspecialchars(truncate($svc['short_desc'], 60)) ?></td>
                        <td><?= $svc['sort_order'] ?></td>
                        <td><a href="?toggle=<?= $svc['id'] ?>" class="adm-badge <?= $svc['is_active'] ? 'green' : 'gray' ?>"><?= $svc['is_active'] ? 'Aktif' : 'Nonaktif' ?></a></td>
                        <td><a href="?featured=<?= $svc['id'] ?>" class="adm-badge <?= $svc['is_featured'] ? 'yellow' : 'gray' ?>"><?= $svc['is_featured'] ? '★ Featured' : 'Biasa' ?></a></td>
                        <td>
                            <a href="?edit=<?= $svc['id'] ?>" class="btn-adm edit" onclick="openModal(<?= htmlspecialchars(json_encode($svc)) ?>);return false;"><i class="bi bi-pencil"></i> Edit</a>
                            <a href="?delete=<?= $svc['id'] ?>" class="btn-adm del ms-1" onclick="return confirm('Hapus layanan ini?')"><i class="bi bi-trash"></i></a>
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

<!-- Modal Add/Edit -->
<div class="adm-modal-overlay" id="serviceModal">
<div class="adm-modal" style="max-width:680px;">
    <div class="adm-modal-header">
        <h5 id="modalTitle">Tambah Layanan</h5>
        <button class="adm-modal-close" onclick="closeModal()"><i class="bi bi-x"></i></button>
    </div>
    <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" id="f_id">
    <input type="hidden" name="existing_image" id="f_existing_image">
    <div class="adm-modal-body">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="adm-form-label">Judul Layanan *</label>
                <input type="text" name="title" id="f_title" class="adm-input" required placeholder="Contoh: FCL (Full Container Load)">
            </div>
            <div class="col-md-4">
                <label class="adm-form-label">Icon Bootstrap</label>
                <input type="text" name="icon" id="f_icon" class="adm-input" placeholder="bi-box-seam">
                <p class="adm-form-hint"><a href="https://icons.getbootstrap.com" target="_blank">Cari icon</a></p>
            </div>
            <div class="col-12">
                <label class="adm-form-label">Deskripsi Singkat (untuk kartu homepage)</label>
                <textarea name="short_desc" id="f_short_desc" class="adm-input" rows="2"></textarea>
            </div>
            <div class="col-12">
                <label class="adm-form-label">Deskripsi Lengkap</label>
                <textarea name="description" id="f_description" class="adm-input" rows="4"></textarea>
            </div>
            <div class="col-md-6">
                <label class="adm-form-label">Info Harga (opsional)</label>
                <input type="text" name="price_info" id="f_price_info" class="adm-input" placeholder="Hubungi kami untuk penawaran">
            </div>
            <div class="col-md-3">
                <label class="adm-form-label">Urutan</label>
                <input type="number" name="sort_order" id="f_sort_order" class="adm-input" value="0">
            </div>
            <div class="col-md-3 d-flex flex-column gap-2 justify-content-end">
                <div class="form-check"><input type="checkbox" name="is_active" id="f_is_active" class="form-check-input" checked><label class="form-check-label" for="f_is_active">Aktif</label></div>
                <div class="form-check"><input type="checkbox" name="is_featured" id="f_is_featured" class="form-check-input"><label class="form-check-label" for="f_is_featured">Featured</label></div>
            </div>
            <div class="col-12">
                <label class="adm-form-label">Fitur-Fitur (satu per baris)</label>
                <textarea name="features" id="f_features" class="adm-input" rows="4" placeholder="Pengiriman aman&#10;Tracking real-time&#10;Asuransi muatan"></textarea>
            </div>
            <div class="col-12">
                <label class="adm-form-label">Gambar Layanan</label>
                <input type="file" name="image" id="f_image" class="adm-input" accept="image/*">
                <div id="currentImageWrap" class="mt-2" style="display:none;">
                    <p class="adm-form-hint">Gambar saat ini:</p>
                    <img id="currentImage" src="" class="img-preview">
                </div>
            </div>
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
const toggle = document.getElementById('topbarToggle');
const sidebar = document.getElementById('pbsSidebar');
const overlay_s = document.getElementById('sidebarOverlay');
const close_s   = document.getElementById('sidebarClose');
toggle?.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay_s.classList.toggle('show'); });
overlay_s?.addEventListener('click', () => { sidebar.classList.remove('open'); overlay_s.classList.remove('show'); });
close_s?.addEventListener('click', () => { sidebar.classList.remove('open'); overlay_s.classList.remove('show'); });

const modal = document.getElementById('serviceModal');
function openModal(data = null) {
    if (data) {
        document.getElementById('modalTitle').textContent = 'Edit Layanan';
        document.getElementById('f_id').value = data.id;
        document.getElementById('f_title').value = data.title || '';
        document.getElementById('f_icon').value = data.icon || 'bi-box-seam';
        document.getElementById('f_short_desc').value = data.short_desc || '';
        document.getElementById('f_description').value = data.description || '';
        document.getElementById('f_price_info').value = data.price_info || '';
        document.getElementById('f_sort_order').value = data.sort_order || 0;
        document.getElementById('f_is_active').checked = data.is_active == 1;
        document.getElementById('f_is_featured').checked = data.is_featured == 1;
        document.getElementById('f_existing_image').value = data.image || '';
        try { const feats = JSON.parse(data.features || '[]'); document.getElementById('f_features').value = feats.join('\n'); } catch(e) {}
        if (data.image) {
            document.getElementById('currentImageWrap').style.display = 'block';
            document.getElementById('currentImage').src = '<?= BASE_URL ?>/public/' + data.image;
        }
    } else {
        document.getElementById('modalTitle').textContent = 'Tambah Layanan';
        document.getElementById('f_id').value = '';
        document.querySelectorAll('#serviceModal input[type=text],#serviceModal input[type=number],#serviceModal textarea').forEach(el => { if(el.id !== 'f_sort_order') el.value = ''; });
        document.getElementById('f_sort_order').value = 0;
        document.getElementById('f_icon').value = 'bi-box-seam';
        document.getElementById('f_is_active').checked = true;
        document.getElementById('f_is_featured').checked = false;
        document.getElementById('currentImageWrap').style.display = 'none';
    }
    modal.classList.add('show');
}
function closeModal() { modal.classList.remove('show'); }
modal.addEventListener('click', e => { if(e.target === modal) closeModal(); });
</script>
</body>
</html>