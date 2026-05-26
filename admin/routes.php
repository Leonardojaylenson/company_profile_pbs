<?php
// admin/routes.php — CRUD Rute Pelayaran
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdminLogin();
$admin = currentAdmin();
$pdo = getPDO();

if (isset($_GET['delete'])) { $pdo->prepare("DELETE FROM routes WHERE id=?")->execute([(int)$_GET['delete']]); header('Location: ' . BASE_URL . '/admin/routes.php'); exit; }
if (isset($_GET['toggle'])) { $pdo->prepare("UPDATE routes SET is_active=1-is_active WHERE id=?")->execute([(int)$_GET['toggle']]); header('Location: ' . BASE_URL . '/admin/routes.php'); exit; }

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)($_POST['id'] ?? 0);
    $origin      = sanitize($_POST['origin'] ?? '');
    $destination = sanitize($_POST['destination'] ?? '');
    $duration    = sanitize($_POST['duration'] ?? '');
    $frequency   = sanitize($_POST['frequency'] ?? '');
    $notes       = sanitize($_POST['notes'] ?? '');
    $sort_order  = (int)($_POST['sort_order'] ?? 0);
    $is_active   = isset($_POST['is_active']) ? 1 : 0;

    if (empty($origin) || empty($destination)) { $error = 'Asal dan tujuan wajib diisi.'; }
    else {
        if ($id > 0) {
            $pdo->prepare("UPDATE routes SET origin=?,destination=?,duration=?,frequency=?,notes=?,sort_order=?,is_active=? WHERE id=?")
                ->execute([$origin,$destination,$duration,$frequency,$notes,$sort_order,$is_active,$id]);
        } else {
            $pdo->prepare("INSERT INTO routes(origin,destination,duration,frequency,notes,sort_order,is_active) VALUES(?,?,?,?,?,?,?,?)")
                ->execute([$origin,$destination,$duration,$frequency,$notes,$sort_order,$is_active]);
        }
        logActivity($admin['id'], 'SAVE_ROUTE', "$origin → $destination");
        $success = 'Rute berhasil disimpan!';
    }
}

$routes = $pdo->query("SELECT * FROM routes ORDER BY sort_order ASC, id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Rute Pelayaran — <?= APP_NAME ?></title>
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
            <div class="topbar-title"><i class="bi bi-map-fill me-2"></i>Rute Pelayaran</div>
            <div class="topbar-actions">
                <button class="topbar-btn btn-primary-adm" onclick="openModal()"><i class="bi bi-plus-lg me-1"></i>Tambah Rute</button>
            </div>
        </div>
        <div class="adm-content">
            <?php if ($success): ?><div class="adm-alert success"><i class="bi bi-check-circle-fill"></i><?= $success ?></div><?php endif; ?>
            <?php if ($error): ?><div class="adm-alert error"><i class="bi bi-exclamation-circle"></i><?= $error ?></div><?php endif; ?>

            <div class="adm-card">
                <div class="adm-card-header"><h5>Daftar Rute (<?= count($routes) ?>)</h5></div>
                <?php if (empty($routes)): ?>
                <div class="empty-state"><i class="bi bi-map"></i><p>Belum ada rute. Klik "+ Tambah Rute".</p></div>
                <?php else: ?>
                <div class="table-responsive">
                <table class="adm-table">
                    <thead><tr><th>#</th><th>Rute</th><th>Durasi</th><th>Jadwal</th><th>Kapal</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php foreach ($routes as $i => $r): ?>
                    <tr>
                        <td class="text-muted"><?= $i+1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-700"><?= htmlspecialchars($r['origin']) ?></span>
                                <i class="bi bi-arrow-right text-primary"></i>
                                <span class="fw-700"><?= htmlspecialchars($r['destination']) ?></span>
                            </div>
                            <?php if ($r['notes']): ?><small class="text-muted"><?= htmlspecialchars($r['notes']) ?></small><?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($r['duration']) ?></td>
                        <td><?= htmlspecialchars($r['frequency']) ?></td>
                        <td><a href="?toggle=<?= $r['id'] ?>" class="adm-badge <?= $r['is_active'] ? 'green' : 'gray' ?>"><?= $r['is_active'] ? 'Aktif' : 'Nonaktif' ?></a></td>
                        <td>
                            <a href="#" class="btn-adm edit" onclick="openModal(<?= htmlspecialchars(json_encode($r)) ?>);return false;"><i class="bi bi-pencil"></i> Edit</a>
                            <a href="?delete=<?= $r['id'] ?>" class="btn-adm del ms-1" onclick="return confirm('Hapus rute ini?')"><i class="bi bi-trash"></i></a>
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
<div class="adm-modal-overlay" id="routeModal">
<div class="adm-modal">
    <div class="adm-modal-header">
        <h5 id="modalTitle">Tambah Rute</h5>
        <button class="adm-modal-close" onclick="closeModal()"><i class="bi bi-x"></i></button>
    </div>
    <form method="POST">
    <input type="hidden" name="id" id="f_id">
    <div class="adm-modal-body">
        <div class="row g-3">
            <div class="col-md-6"><label class="adm-form-label">Asal *</label><input type="text" name="origin" id="f_origin" class="adm-input" required placeholder="Batam"></div>
            <div class="col-md-6"><label class="adm-form-label">Tujuan *</label><input type="text" name="destination" id="f_destination" class="adm-input" required placeholder="Jakarta"></div>
            <div class="col-md-6"><label class="adm-form-label">Durasi Perjalanan</label><input type="text" name="duration" id="f_duration" class="adm-input" placeholder="3-4 Hari"></div>
            <div class="col-md-6"><label class="adm-form-label">Frekuensi / Jadwal</label><input type="text" name="frequency" id="f_frequency" class="adm-input" placeholder="Setiap Senin &amp; Kamis"></div>
            <div class="col-md-3"><label class="adm-form-label">Urutan</label><input type="number" name="sort_order" id="f_sort_order" class="adm-input" value="0"></div>
            <div class="col-md-3 d-flex align-items-end pb-1">
                <div class="form-check"><input type="checkbox" name="is_active" id="f_is_active" class="form-check-input" checked><label class="form-check-label" for="f_is_active">Aktif</label></div>
            </div>
            <div class="col-12"><label class="adm-form-label">Catatan</label><textarea name="notes" id="f_notes" class="adm-input" rows="2" placeholder="Menerima FCL & LCL"></textarea></div>
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
const ov = document.getElementById('sidebarOverlay');
const cl = document.getElementById('sidebarClose');
toggle?.addEventListener('click', () => { sidebar.classList.toggle('open'); ov.classList.toggle('show'); });
ov?.addEventListener('click', () => { sidebar.classList.remove('open'); ov.classList.remove('show'); });
cl?.addEventListener('click', () => { sidebar.classList.remove('open'); ov.classList.remove('show'); });
const modal = document.getElementById('routeModal');
function openModal(d=null) {
    if (d) {
        document.getElementById('modalTitle').textContent = 'Edit Rute';
        document.getElementById('f_id').value = d.id;
        document.getElementById('f_origin').value = d.origin||'';
        document.getElementById('f_destination').value = d.destination||'';
        document.getElementById('f_duration').value = d.duration||'';
        document.getElementById('f_frequency').value = d.frequency||'';
        document.getElementById('f_sort_order').value = d.sort_order||0;
        document.getElementById('f_is_active').checked = d.is_active==1;
        document.getElementById('f_notes').value = d.notes||'';
    } else {
        document.getElementById('modalTitle').textContent = 'Tambah Rute';
        document.getElementById('f_id').value = '';
        ['f_origin','f_destination','f_duration','f_frequency','f_notes'].forEach(id => document.getElementById(id).value='');
        document.getElementById('f_sort_order').value = 0;
        document.getElementById('f_is_active').checked = true;
    }
    modal.classList.add('show');
}
function closeModal() { modal.classList.remove('show'); }
modal.addEventListener('click', e => { if(e.target===modal) closeModal(); });
</script>
</body>
</html>