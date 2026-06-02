<?php
// admin/admins.php — Kelola Admin
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdminLogin();
$admin = currentAdmin();
$pdo   = getPDO();

// Hanya superadmin yang boleh akses halaman ini
if ($admin['role'] !== 'superadmin') {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

define('DEFAULT_PASSWORD', 'Admin@1234');

$success = '';
$error   = '';

// ─── Tambah Admin Baru ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $username  = trim($_POST['username']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role      = in_array($_POST['role'] ?? '', ['superadmin','admin','editor'])
                 ? $_POST['role'] : 'admin';

    if (!$username || !$email || !$full_name) {
        $error = 'Username, email, dan nama lengkap wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (preg_match('/\s/', $username)) {
        $error = 'Username tidak boleh mengandung spasi.';
    } else {
        // Cek duplikat
        $dup = $pdo->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
        $dup->execute([$username, $email]);
        if ($dup->fetch()) {
            $error = 'Username atau email sudah digunakan.';
        } else {
            $hashed = password_hash(DEFAULT_PASSWORD, PASSWORD_BCRYPT);
            $pdo->prepare("
                INSERT INTO admins (username, email, password, full_name, role)
                VALUES (?, ?, ?, ?, ?)
            ")->execute([$username, $email, $hashed, $full_name, $role]);

            logActivity($admin['id'], 'ADD_ADMIN', "Menambahkan admin baru: $username ($role)");
            $success = "Admin <strong>" . htmlspecialchars($username) . "</strong> berhasil ditambahkan. Password default: <code>" . DEFAULT_PASSWORD . "</code>";
        }
    }
}

// ─── Edit Admin ───────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $id        = (int)($_POST['id']        ?? 0);
    $email     = trim($_POST['email']     ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role      = in_array($_POST['role'] ?? '', ['superadmin','admin','editor'])
                 ? $_POST['role'] : 'admin';

    if (!$id || !$email || !$full_name) {
        $error = 'Data tidak lengkap.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif ($id === (int)$admin['id'] && $role !== 'superadmin') {
        // Cegah superadmin menurunkan role dirinya sendiri
        $error = 'Tidak dapat mengubah role akun Anda sendiri.';
    } else {
        // Cek duplikat email (kecuali milik sendiri)
        $dup = $pdo->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
        $dup->execute([$email, $id]);
        if ($dup->fetch()) {
            $error = 'Email sudah digunakan oleh admin lain.';
        } else {
            $pdo->prepare("
                UPDATE admins SET email = ?, full_name = ?, role = ? WHERE id = ?
            ")->execute([$email, $full_name, $role, $id]);

            // Reset password jika diminta
            if (!empty($_POST['reset_password'])) {
                $hashed = password_hash(DEFAULT_PASSWORD, PASSWORD_BCRYPT);
                $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?")->execute([$hashed, $id]);
                logActivity($admin['id'], 'RESET_PASSWORD', "Reset password admin id=$id");
                $success = 'Data admin diperbarui dan password direset ke default.';
            } else {
                $success = 'Data admin berhasil diperbarui.';
            }

            logActivity($admin['id'], 'EDIT_ADMIN', "Edit admin id=$id, role=$role");
        }
    }
}

// ─── Hapus Admin ──────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if ($delId === (int)$admin['id']) {
        $error = 'Tidak dapat menghapus akun Anda sendiri.';
    } else {
        // Cek apakah target adalah superadmin — hitung berapa superadmin tersisa
        $target = $pdo->prepare("SELECT role FROM admins WHERE id = ?");
        $target->execute([$delId]);
        $targetRow = $target->fetch();

        if (!$targetRow) {
            $error = 'Admin tidak ditemukan.';
        } elseif ($targetRow['role'] === 'superadmin') {
            $countSuper = $pdo->query("SELECT COUNT(*) FROM admins WHERE role = 'superadmin'")->fetchColumn();
            if ($countSuper <= 1) {
                $error = 'Tidak dapat menghapus superadmin terakhir.';
            } else {
                $pdo->prepare("DELETE FROM admins WHERE id = ?")->execute([$delId]);
                logActivity($admin['id'], 'DELETE_ADMIN', "Hapus admin id=$delId");
                $success = 'Admin berhasil dihapus.';
            }
        } else {
            $pdo->prepare("DELETE FROM admins WHERE id = ?")->execute([$delId]);
            logActivity($admin['id'], 'DELETE_ADMIN', "Hapus admin id=$delId");
            $success = 'Admin berhasil dihapus.';
        }
    }
}

// ─── Ambil semua admin ────────────────────────────────────────────────────────
$admins = $pdo->query("
    SELECT id, username, email, full_name, role, avatar, last_login, created_at
    FROM admins
    ORDER BY
        FIELD(role, 'superadmin', 'admin', 'editor'),
        created_at ASC
")->fetchAll();

// Admin yang sedang di-edit (dari GET ?edit=id)
$editTarget = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT id, username, email, full_name, role FROM admins WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editTarget = $stmt->fetch() ?: null;
}

$roleColors = ['superadmin' => 'red', 'admin' => 'blue', 'editor' => 'green'];
$roleLabels = ['superadmin' => 'Super Admin', 'admin' => 'Admin', 'editor' => 'Editor'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Admin — <?= htmlspecialchars(APP_NAME) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/admin.css">
    <style>
        .role-badge {
            display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;
        }
        .role-badge.red    { background:#fee2e2;color:#dc2626; }
        .role-badge.blue   { background:#dbeafe;color:#1d4ed8; }
        .role-badge.green  { background:#dcfce7;color:#16a34a; }
        .avatar-sm {
            width:36px;height:36px;border-radius:50%;background:#1B4F8A;color:#fff;
            display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;
        }
        .avatar-sm img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
        .self-row td { background:#f0f6ff!important; }
        .default-pass-box {
            background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;
            padding:12px 16px;font-size:13px;margin-bottom:16px;
        }
        .default-pass-box code { font-size:15px;font-weight:700;color:#16a34a;letter-spacing:.05em; }
    </style>
</head>
<body>
<div class="adm-layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="adm-main">

        <div class="adm-topbar">
            <button class="topbar-toggle" id="topbarToggle"><i class="bi bi-list"></i></button>
            <div class="topbar-title">
                <i class="bi bi-people-fill me-2"></i>Kelola Admin
            </div>
            <div class="topbar-actions">
                <button class="topbar-btn btn-primary-adm" onclick="openAddModal()">
                    <i class="bi bi-person-plus-fill me-1"></i>Tambah Admin
                </button>
            </div>
        </div>

        <div class="adm-content">

            <?php if ($success): ?>
                <div class="adm-alert success"><i class="bi bi-check-circle-fill"></i> <?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="adm-alert error"><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Info password default -->
            <div class="default-pass-box">
                <i class="bi bi-key-fill text-success me-2"></i>
                Password default untuk admin baru: <code><?= DEFAULT_PASSWORD ?></code>
                &nbsp;— Admin disarankan mengubahnya setelah login pertama.
            </div>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h5>Daftar Admin (<?= count($admins) ?>)</h5>
                </div>
                <div class="table-responsive">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th style="width:44px;"></th>
                                <th>Nama / Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Login Terakhir</th>
                                <th>Dibuat</th>
                                <th style="width:120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($admins as $a): ?>
                            <tr <?= (int)$a['id'] === (int)$admin['id'] ? 'class="self-row"' : '' ?>>
                                <td>
                                    <div class="avatar-sm">
                                        <?php if (!empty($a['avatar'])): ?>
                                            <img src="<?= BASE_URL ?>/public/<?= htmlspecialchars($a['avatar']) ?>" alt="">
                                        <?php else: ?>
                                            <?= mb_strtoupper(mb_substr($a['full_name'], 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="nowrap">
                                    <div class="fw-semibold"><?= htmlspecialchars($a['full_name']) ?></div>
                                    <div class="text-muted small">@<?= htmlspecialchars($a['username']) ?>
                                        <?php if ((int)$a['id'] === (int)$admin['id']): ?><span class="ms-1 text-primary small">(Anda)</span><?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-muted small nowrap"><?= htmlspecialchars($a['email']) ?></td>
                                <td class="nowrap"><span class="role-badge <?= $roleColors[$a['role']] ?? 'blue' ?>"><?= $roleLabels[$a['role']] ?? $a['role'] ?></span></td>
                                <td class="text-muted small nowrap"><?= $a['last_login'] ? date('d/m/Y H:i', strtotime($a['last_login'])) : '<span class="text-muted">Belum pernah</span>' ?></td>
                                <td class="text-muted small nowrap"><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
                                
                                <td class="nowrap">
                                    <div class="action-btns">
                                        <button class="btn-adm edit" onclick="openEditModal(<?= htmlspecialchars(json_encode($a), ENT_QUOTES) ?>)" title="Edit"><i class="bi bi-pencil"></i></button>
                                        <?php if ((int)$a['id'] !== (int)$admin['id']): ?>
                                            <a href="?delete=<?= (int)$a['id'] ?>" class="btn-adm del" onclick="return confirm('Hapus admin <?= htmlspecialchars(addslashes($a['username'])) ?>? Tindakan ini tidak bisa dibatalkan.')" title="Hapus"><i class="bi bi-trash"></i></a>
                                        <?php else: ?>
                                            <button class="btn-adm del" disabled title="Tidak bisa hapus diri sendiri" style="opacity:.35;cursor:not-allowed;"><i class="bi bi-trash"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- /adm-content -->
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL: Tambah Admin
═══════════════════════════════════════════════════════════ -->
<div class="adm-modal-overlay" id="addModal">
    <div class="adm-modal" style="max-width:500px;">
        <div class="adm-modal-header">
            <h5><i class="bi bi-person-plus-fill me-2"></i>Tambah Admin Baru</h5>
            <button class="adm-modal-close" onclick="closeAddModal()"><i class="bi bi-x"></i></button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="adm-modal-body">
                <div class="mb-3">
                    <label class="adm-form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="adm-input" required
                           pattern="[a-zA-Z0-9_]+" title="Hanya huruf, angka, dan underscore"
                           placeholder="contoh: john_doe">
                    <p class="adm-form-hint">Hanya huruf, angka, dan underscore. Tidak bisa diubah setelah dibuat.</p>
                </div>
                <div class="mb-3">
                    <label class="adm-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="adm-input" required placeholder="John Doe">
                </div>
                <div class="mb-3">
                    <label class="adm-form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="adm-input" required placeholder="john@example.com">
                </div>
                <div class="mb-3">
                    <label class="adm-form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" class="adm-input">
                        <option value="editor">Editor — Kelola konten</option>
                        <option value="admin" selected>Admin — Kelola semua fitur</option>
                        <option value="superadmin">Super Admin — Akses penuh + kelola admin</option>
                    </select>
                </div>
                <div class="adm-alert" style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;padding:10px 14px;font-size:13px;">
                    <i class="bi bi-key-fill text-success me-1"></i>
                    Password default akan disetel ke: <strong><?= DEFAULT_PASSWORD ?></strong>
                </div>
            </div>
            <div class="adm-modal-footer">
                <button type="submit" class="btn-adm save">
                    <i class="bi bi-person-check-fill me-1"></i>Tambah Admin
                </button>
                <button type="button" class="btn-adm secondary" onclick="closeAddModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL: Edit Admin
═══════════════════════════════════════════════════════════ -->
<div class="adm-modal-overlay" id="editModal">
    <div class="adm-modal" style="max-width:500px;">
        <div class="adm-modal-header">
            <h5><i class="bi bi-pencil-fill me-2"></i>Edit Admin</h5>
            <button class="adm-modal-close" onclick="closeEditModal()"><i class="bi bi-x"></i></button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id"     id="editId">
            <div class="adm-modal-body">
                <div class="mb-3">
                    <label class="adm-form-label">Username</label>
                    <input type="text" id="editUsername" class="adm-input" disabled
                           style="background:var(--adm-bg-secondary,#f8fafc);">
                    <p class="adm-form-hint">Username tidak bisa diubah.</p>
                </div>
                <div class="mb-3">
                    <label class="adm-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" id="editFullName" class="adm-input" required>
                </div>
                <div class="mb-3">
                    <label class="adm-form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="editEmail" class="adm-input" required>
                </div>
                <div class="mb-3" id="editRoleWrapper">
                    <label class="adm-form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" id="editRole" class="adm-input">
                        <option value="editor">Editor</option>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Super Admin</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                        <input type="checkbox" name="reset_password" value="1" id="resetPassCheck">
                        <span class="adm-form-label mb-0">Reset password ke default (<code><?= DEFAULT_PASSWORD ?></code>)</span>
                    </label>
                </div>
            </div>
            <div class="adm-modal-footer">
                <button type="submit" class="btn-adm save">
                    <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                </button>
                <button type="button" class="btn-adm secondary" onclick="closeEditModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Sidebar ──────────────────────────────────────────────────────────────────
const toggle  = document.getElementById('topbarToggle');
const sidebar = document.getElementById('pbsSidebar');
const ov      = document.getElementById('sidebarOverlay');
const cl      = document.getElementById('sidebarClose');
toggle?.addEventListener('click', () => { sidebar.classList.toggle('open'); ov.classList.toggle('show'); });
ov?.addEventListener('click',     () => { sidebar.classList.remove('open'); ov.classList.remove('show'); });
cl?.addEventListener('click',     () => { sidebar.classList.remove('open'); ov.classList.remove('show'); });

// ── Modal Tambah ──────────────────────────────────────────────────────────────
const addModal  = document.getElementById('addModal');
function openAddModal()  { addModal.classList.add('show'); }
function closeAddModal() { addModal.classList.remove('show'); }
addModal.addEventListener('click', e => { if (e.target === addModal) closeAddModal(); });

// ── Modal Edit ────────────────────────────────────────────────────────────────
const editModal     = document.getElementById('editModal');
const currentAdminId = <?= (int)$admin['id'] ?>;

function openEditModal(d) {
    document.getElementById('editId').value          = d.id;
    document.getElementById('editUsername').value    = d.username;
    document.getElementById('editFullName').value    = d.full_name;
    document.getElementById('editEmail').value       = d.email;
    document.getElementById('editRole').value        = d.role;
    document.getElementById('resetPassCheck').checked = false;

    // Kalau edit diri sendiri, disable role
    const roleWrapper = document.getElementById('editRoleWrapper');
    if (parseInt(d.id) === currentAdminId) {
        document.getElementById('editRole').disabled = true;
        roleWrapper.title = 'Tidak bisa mengubah role diri sendiri.';
    } else {
        document.getElementById('editRole').disabled = false;
        roleWrapper.title = '';
    }

    editModal.classList.add('show');
}
function closeEditModal() { editModal.classList.remove('show'); }
editModal.addEventListener('click', e => { if (e.target === editModal) closeEditModal(); });
</script>
</body>
</html>