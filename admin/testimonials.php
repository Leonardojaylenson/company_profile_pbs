<?php
// admin/testimonials.php — CRUD Testimoni
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdminLogin();
$admin = currentAdmin();
$pdo = getPDO();

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT name FROM testimonials WHERE id=?");
    $stmt->execute([(int)$_GET['delete']]);
    $row = $stmt->fetch();
    $pdo->prepare("DELETE FROM testimonials WHERE id=?")->execute([(int)$_GET['delete']]);
    logActivity($admin['id'], 'DELETE_TESTIMONIAL', 'Menghapus testimoni', [
        'Nama' => $row['name'] ?? '(tidak diketahui)',
        'ID'   => (string)(int)$_GET['delete'],
    ]);
    header('Location: ' . BASE_URL . '/admin/testimonials.php'); exit;
}
if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("SELECT name, is_active FROM testimonials WHERE id=?");
    $stmt->execute([(int)$_GET['toggle']]);
    $row = $stmt->fetch();
    $pdo->prepare("UPDATE testimonials SET is_active=1-is_active WHERE id=?")->execute([(int)$_GET['toggle']]);
    logActivity($admin['id'], 'TOGGLE_TESTIMONIAL', 'Mengubah status testimoni', [
        'Nama'   => $row['name'] ?? '-',
        'Status' => ['old'=>$row['is_active']?'Aktif':'Nonaktif','new'=>$row['is_active']?'Nonaktif':'Aktif'],
    ]);
    header('Location: ' . BASE_URL . '/admin/testimonials.php'); exit;
}

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = (int)($_POST['id'] ?? 0);
    $name       = sanitize($_POST['name'] ?? '');
    $position   = sanitize($_POST['position'] ?? '');
    $company    = sanitize($_POST['company'] ?? '');
    $content    = sanitize($_POST['content'] ?? '');
    $rating     = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_active  = isset($_POST['is_active']) ? 1 : 0;
    $avatar = $_POST['existing_avatar'] ?? '';
    if (!empty($_FILES['avatar']['name'])) { $up = handleUpload($_FILES['avatar'], 'testimonials'); if ($up) $avatar = $up; }

    if (empty($name)||empty($content)) { $error = 'Nama dan isi testimoni wajib diisi.'; }
    else {
        if ($id > 0) {
            $old = $pdo->prepare("SELECT * FROM testimonials WHERE id=?");
            $old->execute([$id]);
            $old = $old->fetch();

            $pdo->prepare("UPDATE testimonials SET name=?,position=?,company=?,content=?,rating=?,avatar=?,sort_order=?,is_active=? WHERE id=?")
                ->execute([$name,$position,$company,$content,$rating,$avatar,$sort_order,$is_active,$id]);

            $changes = [];
            if ($old['name']     !== $name)     $changes['Nama']      = ['old'=>$old['name'],    'new'=>$name];
            if ($old['position'] !== $position)  $changes['Jabatan']   = ['old'=>$old['position'],'new'=>$position];
            if ($old['company']  !== $company)   $changes['Perusahaan']= ['old'=>$old['company'], 'new'=>$company];
            if ((int)$old['rating'] !== $rating) $changes['Rating']    = ['old'=>$old['rating'].' bintang','new'=>$rating.' bintang'];
            if ($old['content']  !== $content)   $changes['Isi']       = 'Diubah ('.mb_strlen($content).' karakter)';
            if ((int)$old['is_active'] !== $is_active)
                $changes['Status'] = ['old'=>$old['is_active']?'Aktif':'Nonaktif','new'=>$is_active?'Aktif':'Nonaktif'];
            if (!empty($avatar) && $old['avatar'] !== $avatar)
                $changes['Foto'] = ['old'=>basename($old['avatar']??'-'),'new'=>basename($avatar)];

            logActivity($admin['id'], 'EDIT_TESTIMONIAL', 'Edit testimoni: '.$name.(!empty($changes)?' ('.count($changes).' perubahan)':''), $changes);
        } else {
            $pdo->prepare("INSERT INTO testimonials(name,position,company,content,rating,avatar,sort_order,is_active) VALUES(?,?,?,?,?,?,?,?)")
                ->execute([$name,$position,$company,$content,$rating,$avatar,$sort_order,$is_active]);
            logActivity($admin['id'], 'ADD_TESTIMONIAL', 'Menambahkan testimoni baru', [
                'Nama'       => $name,
                'Jabatan'    => $position ?: '-',
                'Perusahaan' => $company  ?: '-',
                'Rating'     => $rating.' bintang',
                'Status'     => $is_active ? 'Aktif' : 'Nonaktif',
            ]);
        }
        $success = 'Testimoni berhasil disimpan!';
    }
}

$items = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order ASC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Testimoni — <?= APP_NAME ?></title>
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
            <div class="topbar-title"><i class="bi bi-chat-quote-fill me-2"></i>Testimoni</div>
            <div class="topbar-actions"><button class="topbar-btn btn-primary-adm" onclick="openModal()"><i class="bi bi-plus-lg me-1"></i>Tambah</button></div>
        </div>
        <div class="adm-content">
            <?php if ($success): ?><div class="adm-alert success"><i class="bi bi-check-circle-fill"></i><?= $success ?></div><?php endif; ?>
            <?php if ($error): ?><div class="adm-alert error"><i class="bi bi-exclamation-circle"></i><?= $error ?></div><?php endif; ?>
            <div class="adm-card">
                <div class="adm-card-header"><h5>Testimoni (<?= count($items) ?>)</h5></div>
                <?php if (empty($items)): ?>
                <div class="empty-state"><i class="bi bi-chat-quote"></i><p>Belum ada testimoni.</p></div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="adm-table">
                        <thead><tr><th>Foto</th><th>Nama</th><th>Jabatan</th><th>Rating</th><th>Isi</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody>
                        <?php foreach ($items as $t): ?>
                        <tr>
                            <td class="nowrap"><?php if (!empty($t['avatar'])): ?><img src="<?= uploadUrl($t['avatar']) ?>" class="thumb" style="border-radius:50%;"><?php else: ?><div style="width:36px;height:36px;border-radius:50%;background:#1B4F8A;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;"><?= mb_strtoupper(mb_substr($t['name'],0,1)) ?></div><?php endif; ?></td>
                            <td class="nowrap"><strong><?= htmlspecialchars($t['name']) ?></strong></td>
                            <td class="text-muted small nowrap"><?= htmlspecialchars($t['position']??'') ?><?= !empty($t['company'])?' · '.htmlspecialchars($t['company']):'' ?></td>
                            <td class="nowrap"><?= str_repeat('★',(int)$t['rating']) ?></td>
                            <td class="text-muted small" style="min-width: 250px;"><?= htmlspecialchars(truncate($t['content'],60)) ?></td>
                            <td class="nowrap"><a href="?toggle=<?= $t['id'] ?>" class="adm-badge <?= $t['is_active']?'green':'gray' ?>"><?= $t['is_active']?'Aktif':'Nonaktif' ?></a></td>
                            
                            <td class="nowrap">
                                <div class="action-btns">
                                    <a href="#" class="btn-adm edit" onclick="openModal(<?= htmlspecialchars(json_encode($t)) ?>);return false;"><i class="bi bi-pencil"></i></a>
                                    <a href="?delete=<?= $t['id'] ?>" class="btn-adm del" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
                                </div>
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
<div class="adm-modal-overlay" id="testiModal">
<div class="adm-modal">
    <div class="adm-modal-header"><h5 id="modalTitle">Tambah Testimoni</h5><button class="adm-modal-close" onclick="closeModal()"><i class="bi bi-x"></i></button></div>
    <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" id="f_id">
    <input type="hidden" name="existing_avatar" id="f_existing_avatar">
    <div class="adm-modal-body">
        <div class="row g-3">
            <div class="col-md-6"><label class="adm-form-label">Nama *</label><input type="text" name="name" id="f_name" class="adm-input" required></div>
            <div class="col-md-3"><label class="adm-form-label">Rating</label><select name="rating" id="f_rating" class="adm-input"><option value="5">★★★★★</option><option value="4">★★★★</option><option value="3">★★★</option><option value="2">★★</option><option value="1">★</option></select></div>
            <div class="col-md-3"><label class="adm-form-label">Urutan</label><input type="number" name="sort_order" id="f_sort_order" class="adm-input" value="0"></div>
            <div class="col-md-6"><label class="adm-form-label">Jabatan</label><input type="text" name="position" id="f_position" class="adm-input" placeholder="Manager Logistik"></div>
            <div class="col-md-6"><label class="adm-form-label">Perusahaan</label><input type="text" name="company" id="f_company" class="adm-input"></div>
            <div class="col-12"><label class="adm-form-label">Isi Testimoni *</label><textarea name="content" id="f_content" class="adm-input" rows="4" required></textarea></div>
            <div class="col-md-8"><label class="adm-form-label">Foto</label><input type="file" name="avatar" class="adm-input" accept="image/*"><div id="curImgWrap" style="display:none;margin-top:8px;"><img id="curImg" src="" class="img-preview" style="border-radius:50%;"></div></div>
            <div class="col-md-4 d-flex align-items-end pb-1"><div class="form-check"><input type="checkbox" name="is_active" id="f_is_active" class="form-check-input" checked><label class="form-check-label" for="f_is_active">Aktif</label></div></div>
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
const modal=document.getElementById('testiModal');
function openModal(d=null){
    if(d){
        document.getElementById('modalTitle').textContent='Edit Testimoni';
        document.getElementById('f_id').value=d.id;
        document.getElementById('f_name').value=d.name||'';
        document.getElementById('f_position').value=d.position||'';
        document.getElementById('f_company').value=d.company||'';
        document.getElementById('f_content').value=d.content||'';
        document.getElementById('f_rating').value=d.rating||5;
        document.getElementById('f_sort_order').value=d.sort_order||0;
        document.getElementById('f_is_active').checked=d.is_active==1;
        document.getElementById('f_existing_avatar').value=d.avatar||'';
        if(d.avatar){document.getElementById('curImgWrap').style.display='block';document.getElementById('curImg').src='<?= BASE_URL ?>/public/'+d.avatar;}
        else{document.getElementById('curImgWrap').style.display='none';}
    } else {
        document.getElementById('modalTitle').textContent='Tambah Testimoni';
        document.getElementById('f_id').value='';
        ['f_name','f_position','f_company','f_content'].forEach(id=>document.getElementById(id).value='');
        document.getElementById('f_rating').value=5;
        document.getElementById('f_sort_order').value=0;
        document.getElementById('f_is_active').checked=true;
        document.getElementById('curImgWrap').style.display='none';
    }
    modal.classList.add('show');
}
function closeModal(){modal.classList.remove('show');}
modal.addEventListener('click',e=>{if(e.target===modal)closeModal();});
</script>
</body>
</html>