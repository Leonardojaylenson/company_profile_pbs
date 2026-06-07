<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdminLogin();
$admin = currentAdmin();
$pdo   = getPDO();

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt  = $pdo->prepare("SELECT name FROM cargo_types WHERE id = ?");
    $stmt->execute([$delId]);
    $row = $stmt->fetch();

    $used = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE cargo_type_id = ?");
    $used->execute([$delId]);
    if ((int)$used->fetchColumn() > 0) {
        $error = 'Jenis kargo ini tidak bisa dihapus karena sudah digunakan pada pesan masuk.';
    } else {
        $pdo->prepare("DELETE FROM cargo_types WHERE id = ?")->execute([$delId]);
        logActivity($admin['id'], 'DELETE_CARGO_TYPE', 'Menghapus jenis kargo', [
            'Nama' => $row['name'] ?? '(tidak diketahui)',
            'ID'   => (string)$delId,
        ]);
        header('Location: ' . BASE_URL . '/admin/cargo_types.php?deleted=1');
        exit;
    }
}

if (isset($_GET['toggle'])) {
    $togId = (int)$_GET['toggle'];
    $stmt  = $pdo->prepare("SELECT name, is_active FROM cargo_types WHERE id = ?");
    $stmt->execute([$togId]);
    $row = $stmt->fetch();
    $pdo->prepare("UPDATE cargo_types SET is_active = 1 - is_active WHERE id = ?")->execute([$togId]);
    logActivity($admin['id'], 'TOGGLE_CARGO_TYPE', 'Mengubah status jenis kargo', [
        'Nama'   => $row['name'] ?? '-',
        'Status' => [
            'old' => $row['is_active'] ? 'Aktif' : 'Nonaktif',
            'new' => $row['is_active'] ? 'Nonaktif' : 'Aktif',
        ],
    ]);
    header('Location: ' . BASE_URL . '/admin/cargo_types.php');
    exit;
}

$success = $error ?? '';
$error   = $error ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)($_POST['id'] ?? 0);
    $name        = sanitize($_POST['name']        ?? '');
    $code        = strtoupper(trim($_POST['code'] ?? ''));
    $description = sanitize($_POST['description'] ?? '');
    $is_active   = isset($_POST['is_active']) ? 1 : 0;
    $sort_order  = (int)($_POST['sort_order'] ?? 0);

    if (empty($name)) {
        $error = 'Nama jenis kargo wajib diisi.';
    } elseif (empty($code)) {
        $error = 'Kode jenis kargo wajib diisi.';
    } elseif (!preg_match('/^[A-Z0-9_]{1,50}$/', $code)) {
        $error = 'Kode hanya boleh huruf kapital, angka, dan underscore (maks. 50 karakter).';
    } else {
        $chk = $pdo->prepare("SELECT id FROM cargo_types WHERE code = ? AND id != ?");
        $chk->execute([$code, $id]);
        if ($chk->fetch()) {
            $error = 'Kode "' . htmlspecialchars($code) . '" sudah digunakan jenis kargo lain.';
        }
    }

    if (empty($error)) {
        if ($id > 0) {
            /* Ambil data lama untuk diff log */
            $old = $pdo->prepare("SELECT * FROM cargo_types WHERE id = ?");
            $old->execute([$id]);
            $old = $old->fetch();

            $pdo->prepare(
                "UPDATE cargo_types
                 SET name=?, code=?, description=?, is_active=?, sort_order=?
                 WHERE id=?"
            )->execute([$name, $code, $description, $is_active, $sort_order, $id]);

            $changes = [];
            if ($old['name']        !== $name)       $changes['Nama']       = ['old' => $old['name'],        'new' => $name];
            if ($old['code']        !== $code)       $changes['Kode']       = ['old' => $old['code'],        'new' => $code];
            if ((int)$old['is_active'] !== $is_active) $changes['Status']   = ['old' => $old['is_active'] ? 'Aktif' : 'Nonaktif', 'new' => $is_active ? 'Aktif' : 'Nonaktif'];
            if ((int)$old['sort_order'] !== $sort_order) $changes['Urutan'] = ['old' => $old['sort_order'],  'new' => $sort_order];
            if ($old['description'] !== $description) $changes['Deskripsi'] = 'Diubah';

            logActivity($admin['id'], 'EDIT_CARGO_TYPE', 'Edit jenis kargo: ' . $name . (!empty($changes) ? ' (' . count($changes) . ' perubahan)' : ''), $changes);
            $success = 'Jenis kargo berhasil diperbarui!';
        } else {
            $pdo->prepare(
                "INSERT INTO cargo_types (name, code, description, is_active, sort_order)
                 VALUES (?, ?, ?, ?, ?)"
            )->execute([$name, $code, $description, $is_active, $sort_order]);

            logActivity($admin['id'], 'ADD_CARGO_TYPE', 'Menambahkan jenis kargo baru', [
                'Nama'   => $name,
                'Kode'   => $code,
                'Status' => $is_active ? 'Aktif' : 'Nonaktif',
            ]);
            $success = 'Jenis kargo berhasil ditambahkan!';
        }
    }
}

if (!empty($_GET['deleted'])) {
    $success = 'Jenis kargo berhasil dihapus.';
}

/* ═══════════════════════════════════════
   FETCH ALL
═══════════════════════════════════════ */
$allTypes = $pdo->query("SELECT * FROM cargo_types ORDER BY sort_order ASC, name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jenis Kargo — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/admin.css">
    <style>
        /* ── Responsive tambahan khusus halaman ini ── */

        /* Card summary stats */
        .cargo-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .cargo-stat-card {
            background: var(--adm-bg, #fff);
            border: 1px solid var(--adm-border, #e9ecef);
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .cargo-stat-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .cargo-stat-icon.blue  { background: #eff6ff; color: #3b82f6; }
        .cargo-stat-icon.green { background: #f0fdf4; color: #22c55e; }
        .cargo-stat-icon.gray  { background: #f8fafc; color: #94a3b8; }
        .cargo-stat-label { font-size: .72rem; color: #94a3b8; margin-bottom: 2px; }
        .cargo-stat-value { font-size: 1.25rem; font-weight: 700; color: #1e293b; line-height: 1; }

        /* Code badge */
        .code-badge {
            display: inline-block;
            font-family: 'Courier New', monospace;
            font-size: .72rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 5px;
            background: #f1f5f9;
            color: #475569;
            letter-spacing: .04em;
            border: 1px solid #e2e8f0;
        }

        /* Sort order input */
        .sort-input {
            width: 64px;
            text-align: center;
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: .82rem;
            color: #64748b;
        }

        /* Truncate deskripsi di tabel */
        .desc-cell {
            max-width: 220px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #64748b;
            font-size: .82rem;
        }

        /* ── Responsive: kartu di mobile ── */
        @media (max-width: 767.98px) {
            /* Sembunyikan tabel, tampilkan cards */
            .table-desktop { display: none !important; }
            .cards-mobile  { display: flex !important; }

            .cargo-stats {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (min-width: 768px) {
            .table-desktop { display: block !important; }
            .cards-mobile  { display: none !important; }
        }

        /* Mobile card list */
        .cards-mobile {
            display: none;
            flex-direction: column;
            gap: 10px;
            padding: 0 4px;
        }
        .mobile-cargo-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 14px 16px;
        }
        .mobile-cargo-card .mc-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
        }
        .mobile-cargo-card .mc-name {
            font-weight: 600;
            font-size: .92rem;
            color: #1e293b;
        }
        .mobile-cargo-card .mc-meta {
            font-size: .78rem;
            color: #94a3b8;
            margin-top: 2px;
        }
        .mobile-cargo-card .mc-desc {
            font-size: .8rem;
            color: #64748b;
            margin: 8px 0;
            line-height: 1.5;
        }
        .mobile-cargo-card .mc-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
        }

        /* Modal responsive */
        @media (max-width: 575.98px) {
            .adm-modal { margin: 8px; width: calc(100% - 16px) !important; max-width: 100% !important; }
            .adm-modal-body { padding: 12px 14px !important; }
            .adm-modal-header, .adm-modal-footer { padding: 12px 14px !important; }
        }
    </style>
</head>
<body>
<div class="adm-layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <div class="adm-main">

        <!-- Topbar -->
        <div class="adm-topbar">
            <button class="topbar-toggle" id="topbarToggle"><i class="bi bi-list"></i></button>
            <div class="topbar-title"><i class="bi bi-box-seam me-2"></i>Jenis Kargo</div>
            <div class="topbar-actions">
                <button class="topbar-btn btn-primary-adm" onclick="openModal()">
                    <i class="bi bi-plus-lg me-1"></i>
                    <span class="d-none d-sm-inline">Tambah Jenis Kargo</span>
                    <span class="d-inline d-sm-none">Tambah</span>
                </button>
            </div>
        </div>

        <div class="adm-content">

            <!-- Alert -->
            <?php if (!empty($success)): ?>
            <div class="adm-alert success"><i class="bi bi-check-circle-fill"></i><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
            <div class="adm-alert error"><i class="bi bi-exclamation-circle"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Stats -->
            <?php
            $totalAll    = count($allTypes);
            $totalActive = count(array_filter($allTypes, fn($r) => $r['is_active']));
            $totalInact  = $totalAll - $totalActive;
            ?>
            <div class="cargo-stats">
                <div class="cargo-stat-card">
                    <div class="cargo-stat-icon blue"><i class="bi bi-box-seam-fill"></i></div>
                    <div>
                        <div class="cargo-stat-label">Total</div>
                        <div class="cargo-stat-value"><?= $totalAll ?></div>
                    </div>
                </div>
                <div class="cargo-stat-card">
                    <div class="cargo-stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="cargo-stat-label">Aktif</div>
                        <div class="cargo-stat-value"><?= $totalActive ?></div>
                    </div>
                </div>
                <div class="cargo-stat-card">
                    <div class="cargo-stat-icon gray"><i class="bi bi-slash-circle"></i></div>
                    <div>
                        <div class="cargo-stat-label">Nonaktif</div>
                        <div class="cargo-stat-value"><?= $totalInact ?></div>
                    </div>
                </div>
            </div>

            <!-- Tabel -->
            <div class="adm-card">
                <div class="adm-card-header">
                    <h5>Semua Jenis Kargo (<?= $totalAll ?>)</h5>
                </div>

                <?php if (empty($allTypes)): ?>
                <div class="empty-state">
                    <i class="bi bi-box-seam"></i>
                    <p>Belum ada jenis kargo. Klik <strong>Tambah Jenis Kargo</strong> untuk memulai.</p>
                </div>
                <?php else: ?>

                <!-- Desktop: tabel -->
                <div class="table-responsive table-desktop">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th style="width:44px">No</th>
                                <th>Nama</th>
                                <th style="width:100px">Kode</th>
                                <th>Deskripsi</th>
                                <th style="width:80px;text-align:center">Urutan</th>
                                <th style="width:100px;text-align:center">Status</th>
                                <th style="width:110px">Dibuat</th>
                                <th style="width:110px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($allTypes as $i => $ct): ?>
                        <tr>
                            <td class="text-muted small nowrap"><?= $i + 1 ?></td>
                            <td class="nowrap"><strong><?= htmlspecialchars($ct['name']) ?></strong></td>
                            <td class="nowrap"><span class="code-badge"><?= htmlspecialchars($ct['code']) ?></span></td>
                            <td><span class="desc-cell" title="<?= htmlspecialchars($ct['description'] ?? '') ?>"><?= !empty($ct['description']) ? htmlspecialchars($ct['description']) : '<span class="text-muted">—</span>' ?></span></td>
                            <td class="nowrap" style="text-align:center"><span class="text-muted small"><?= (int)$ct['sort_order'] ?></span></td>
                            <td class="nowrap" style="text-align:center">
                                <a href="?toggle=<?= $ct['id'] ?>" class="adm-badge <?= $ct['is_active'] ? 'green' : 'gray' ?>" title="Klik untuk ubah status"><?= $ct['is_active'] ? 'Aktif' : 'Nonaktif' ?></a>
                            </td>
                            <td class="text-muted small nowrap"><?= date('d/m/Y', strtotime($ct['created_at'])) ?></td>
                            
                            <td class="nowrap">
                                <div class="action-btns">
                                    <a href="#" class="btn-adm edit" onclick="openModal(<?= htmlspecialchars(json_encode($ct)) ?>); return false;" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <a href="?delete=<?= $ct['id'] ?>" class="btn-adm del" onclick="return confirm('Hapus jenis kargo \'<?= htmlspecialchars(addslashes($ct['name'])) ?>\'?')" title="Hapus"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile: cards -->
                <div class="cards-mobile">
                    <?php foreach ($allTypes as $i => $ct): ?>
                    <div class="mobile-cargo-card">
                        <div class="mc-top">
                            <div>
                                <div class="mc-name"><?= htmlspecialchars($ct['name']) ?></div>
                                <div class="mc-meta">
                                    <span class="code-badge me-1"><?= htmlspecialchars($ct['code']) ?></span>
                                    Urutan: <?= (int)$ct['sort_order'] ?>
                                </div>
                            </div>
                            <a href="?toggle=<?= $ct['id'] ?>"
                               class="adm-badge <?= $ct['is_active'] ? 'green' : 'gray' ?>"
                               style="flex-shrink:0">
                                <?= $ct['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                            </a>
                        </div>
                        <?php if (!empty($ct['description'])): ?>
                        <div class="mc-desc"><?= htmlspecialchars($ct['description']) ?></div>
                        <?php endif; ?>
                        <div class="mc-actions">
                            <span class="text-muted" style="font-size:.75rem;flex:1">
                                <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($ct['created_at'])) ?>
                            </span>
                            <a href="#"
                               class="btn-adm edit"
                               onclick="openModal(<?= htmlspecialchars(json_encode($ct)) ?>); return false;">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            <a href="?delete=<?= $ct['id'] ?>"
                               class="btn-adm del"
                               onclick="return confirm('Hapus jenis kargo \'<?= htmlspecialchars(addslashes($ct['name'])) ?>\'?')">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php endif; ?>
            </div><!-- /adm-card -->

        </div><!-- /adm-content -->
    </div><!-- /adm-main -->
</div><!-- /adm-layout -->

<!-- ════════════════════════════════════════
     MODAL TAMBAH / EDIT
════════════════════════════════════════ -->
<div class="adm-modal-overlay" id="cargoModal">
    <div class="adm-modal" style="max-width:540px;width:100%">

        <div class="adm-modal-header">
            <h5 id="modalTitle">Tambah Jenis Kargo</h5>
            <button class="adm-modal-close" onclick="closeModal()"><i class="bi bi-x"></i></button>
        </div>

        <form method="POST" id="cargoForm">
            <input type="hidden" name="id" id="f_id">

            <div class="adm-modal-body">
                <div class="row g-3">

                    <!-- Nama -->
                    <div class="col-12">
                        <label class="adm-form-label">Nama Jenis Kargo <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="f_name" class="adm-input"
                               placeholder="contoh: Kargo Umum" required maxlength="100">
                        <div class="form-text text-muted mt-1" style="font-size:.75rem;">
                            Nama yang ditampilkan ke pengunjung saat memilih jenis kargo.
                        </div>
                    </div>

                    <!-- Kode -->
                    <div class="col-sm-5">
                        <label class="adm-form-label">Kode <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="f_code" class="adm-input"
                               placeholder="contoh: GEN" required maxlength="50"
                               style="text-transform:uppercase;font-family:'Courier New',monospace;letter-spacing:.05em"
                               oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9_]/g,'')">
                        <div class="form-text text-muted mt-1" style="font-size:.75rem;">
                            Huruf kapital, angka, underscore saja.
                        </div>
                    </div>

                    <!-- Sort Order -->
                    <div class="col-sm-3">
                        <label class="adm-form-label">Urutan</label>
                        <input type="number" name="sort_order" id="f_sort_order" class="adm-input"
                               placeholder="0" min="0" max="9999" value="0">
                        <div class="form-text text-muted mt-1" style="font-size:.75rem;">
                            0 = pertama.
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-sm-4 d-flex align-items-end pb-1">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="f_is_active"
                                   class="form-check-input" hidden="1" checked>
                            <label class="form-check-label" for="f_is_active" hidden="1">Aktif</label>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="col-12">
                        <label class="adm-form-label">Deskripsi</label>
                        <textarea name="description" id="f_description" class="adm-input"
                                  rows="3"
                                  placeholder="Keterangan singkat mengenai jenis kargo ini..."></textarea>
                    </div>

                </div>
            </div><!-- /modal-body -->

            <div class="adm-modal-footer">
                <button type="button" class="btn-adm secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-adm save">
                    <i class="bi bi-check-lg me-1"></i>Simpan
                </button>
            </div>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ── Sidebar toggle ── */
const toggle  = document.getElementById('topbarToggle');
const sidebar = document.getElementById('pbsSidebar');
const ov      = document.getElementById('sidebarOverlay');
const cl      = document.getElementById('sidebarClose');
toggle?.addEventListener('click', () => { sidebar.classList.toggle('open'); ov.classList.toggle('show'); });
ov?.addEventListener('click',     () => { sidebar.classList.remove('open'); ov.classList.remove('show'); });
cl?.addEventListener('click',     () => { sidebar.classList.remove('open'); ov.classList.remove('show'); });

/* ── Modal ── */
const modal = document.getElementById('cargoModal');

function openModal(d = null) {
    if (d) {
        document.getElementById('modalTitle').textContent    = 'Edit Jenis Kargo';
        document.getElementById('f_id').value               = d.id;
        document.getElementById('f_name').value             = d.name        || '';
        document.getElementById('f_code').value             = d.code        || '';
        document.getElementById('f_description').value      = d.description || '';
        document.getElementById('f_sort_order').value       = d.sort_order  ?? 0;
        document.getElementById('f_is_active').checked      = d.is_active == 1;
    } else {
        document.getElementById('modalTitle').textContent    = 'Tambah Jenis Kargo';
        document.getElementById('f_id').value               = '';
        document.getElementById('f_name').value             = '';
        document.getElementById('f_code').value             = '';
        document.getElementById('f_description').value      = '';
        document.getElementById('f_sort_order').value       = '0';
        document.getElementById('f_is_active').checked      = true;
    }
    modal.classList.add('show');
    setTimeout(() => document.getElementById('f_name').focus(), 120);
}

function closeModal() {
    modal.classList.remove('show');
}

/* Tutup modal klik di luar */
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

/* Tutup modal tekan Escape */
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

/* Buka modal otomatis jika ada error POST (form dikembalikan dengan error) */
<?php if (!empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
openModal({
    id:          <?= json_encode($_POST['id'] ?? '') ?>,
    name:        <?= json_encode($_POST['name'] ?? '') ?>,
    code:        <?= json_encode($_POST['code'] ?? '') ?>,
    description: <?= json_encode($_POST['description'] ?? '') ?>,
    sort_order:  <?= json_encode($_POST['sort_order'] ?? 0) ?>,
    is_active:   <?= json_encode(isset($_POST['is_active']) ? 1 : 0) ?>,
});
<?php endif; ?>
</script>
</body>
</html>