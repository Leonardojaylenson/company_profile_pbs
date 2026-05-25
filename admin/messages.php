<?php
// admin/messages.php — Kelola Pesan Masuk
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/mailer.php';
requireAdminLogin();
$admin = currentAdmin();
$pdo   = getPDO();
$s     = getAllSettings();

$reply_success = '';
$reply_error   = '';

// ─── Kirim email balasan via SMTP ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $msgId  = (int)($_POST['msg_id'] ?? 0);
    $stmt   = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
    $stmt->execute([$msgId]);
    $target = $stmt->fetch();

    $replyBody    = trim($_POST['reply_body']    ?? '');
    $replySubject = trim($_POST['reply_subject'] ?? '');

    if (!$target) {
        $reply_error = 'Pesan tidak ditemukan.';
    } elseif (empty($target['email'])) {
        $reply_error = 'Pengirim tidak memiliki alamat email.';
    } elseif ($replyBody === '') {
        $reply_error = 'Isi pesan balasan tidak boleh kosong.';
    } else {
        $subject = $replySubject ?: ('Re: ' . ($target['subject'] ?? ($s['smtp_default_subject'] ?? 'Pesan Anda')));
        $result  = sendReplyEmail($target['email'], $target['name'], $subject, $replyBody);

        if ($result === true) {
            $pdo->prepare("UPDATE messages SET is_replied = 1, is_read = 1 WHERE id = ?")
                ->execute([$msgId]);
            $reply_success = 'Email berhasil dikirim ke ' . htmlspecialchars($target['email']) . '.';
        } else {
            $reply_error = $result;
        }
    }
}

// ─── Hapus satu pesan ────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM messages WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: ' . BASE_URL . '/admin/messages.php?filter=' . urlencode($_GET['filter'] ?? 'all'));
    exit;
}

// ─── Toggle read / unread ────────────────────────────────────────────────────
if (isset($_GET['read'])) {
    $pdo->prepare("UPDATE messages SET is_read = 1 - is_read WHERE id = ?")->execute([(int)$_GET['read']]);
    header('Location: ' . BASE_URL . '/admin/messages.php?filter=' . urlencode($_GET['filter'] ?? 'all'));
    exit;
}

// ─── Tandai replied ───────────────────────────────────────────────────────────
if (isset($_GET['replied'])) {
    $pdo->prepare("UPDATE messages SET is_replied = 1 WHERE id = ?")->execute([(int)$_GET['replied']]);
    header('Location: ' . BASE_URL . '/admin/messages.php?filter=' . urlencode($_GET['filter'] ?? 'all'));
    exit;
}

// ─── Tandai semua sebagai dibaca ─────────────────────────────────────────────
if (isset($_GET['read_all'])) {
    $pdo->exec("UPDATE messages SET is_read = 1");
    header('Location: ' . BASE_URL . '/admin/messages.php');
    exit;
}

// ─── Bulk delete ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete'])) {
    $ids = array_filter(array_map('intval', $_POST['selected'] ?? []));
    if ($ids) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $pdo->prepare("DELETE FROM messages WHERE id IN ($placeholders)")->execute($ids);
    }
    header('Location: ' . BASE_URL . '/admin/messages.php?filter=' . urlencode($_POST['filter'] ?? 'all'));
    exit;
}

// ─── Filter ───────────────────────────────────────────────────────────────────
$filter = $_GET['filter'] ?? 'all';
$where  = match ($filter) {
    'unread'    => "WHERE m.is_read = 0",
    'read'      => "WHERE m.is_read = 1",
    'replied'   => "WHERE m.is_replied = 1",
    'unreplied' => "WHERE m.is_replied = 0",
    default     => "",
};

$messages = $pdo->query("
    SELECT m.*, ct.name AS cargo_type_name
    FROM messages m
    LEFT JOIN cargo_types ct ON ct.id = m.cargo_type_id
    $where
    ORDER BY m.created_at DESC
")->fetchAll();

$counts = $pdo->query("
    SELECT
        COUNT(*)          AS total,
        SUM(is_read  = 0) AS unread,
        SUM(is_replied = 0) AS unreplied
    FROM messages
")->fetch();

// Apakah SMTP sudah dikonfigurasi?
$smtpConfigured = !empty($s['smtp_gmail']) && !empty($s['smtp_app_password']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk — <?= htmlspecialchars(APP_NAME) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/admin.css">
    <style>
        .msg-unread td { font-weight: 600; background: #f0f6ff; }
        .msg-dot { width:8px;height:8px;border-radius:50%;background:#3b82f6;display:inline-block;margin-right:6px;flex-shrink:0; }
        .filter-tabs { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px; }
        .filter-tab  { padding:6px 16px;border-radius:20px;border:1.5px solid #e2e8f0;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;text-decoration:none;transition:.15s; }
        .filter-tab:hover,.filter-tab.active { background:#1B4F8A;color:#fff;border-color:#1B4F8A; }
        .stat-chips  { display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px; }
        .stat-chip   { background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:10px 18px;display:flex;align-items:center;gap:8px;font-size:13px; }
        .stat-chip strong { font-size:18px;font-weight:700; }
        .stat-chip.red    strong { color:#ef4444; }
        .stat-chip.orange strong { color:#f59e0b; }
        .bulk-bar  { display:none;align-items:center;gap:10px;padding:10px 16px;background:#fff3cd;border-radius:10px;margin-bottom:12px; }
        .bulk-bar.show { display:flex; }
        .smtp-warning { background:#fff3cd;border:1.5px solid #fbbf24;border-radius:10px;padding:10px 16px;font-size:13px;margin-bottom:12px;display:flex;align-items:center;gap:8px; }
    </style>
</head>
<body>
<div class="adm-layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="adm-main">

        <div class="adm-topbar">
            <button class="topbar-toggle" id="topbarToggle"><i class="bi bi-list"></i></button>
            <div class="topbar-title">
                <i class="bi bi-envelope-fill me-2"></i>Pesan Masuk
                <?php if ($counts['unread'] > 0): ?>
                    <span class="adm-badge red ms-2"><?= (int)$counts['unread'] ?> baru</span>
                <?php endif; ?>
            </div>
            <div class="topbar-actions">
                <?php if ($counts['unread'] > 0): ?>
                    <a href="?read_all=1" class="topbar-btn btn-secondary-adm"
                       onclick="return confirm('Tandai semua sebagai sudah dibaca?')">
                        <i class="bi bi-check-all me-1"></i>Tandai Semua Dibaca
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="adm-content">

            <?php if ($reply_success): ?>
                <div class="adm-alert success"><i class="bi bi-check-circle-fill"></i> <?= $reply_success ?></div>
            <?php endif; ?>
            <?php if ($reply_error): ?>
                <div class="adm-alert error"><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($reply_error) ?></div>
            <?php endif; ?>

            <?php if (!$smtpConfigured): ?>
                <div class="smtp-warning">
                    <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                    <span>SMTP belum dikonfigurasi. Tombol <strong>Balas Email</strong> tidak akan berfungsi.
                    <a href="settings.php#tab-smtp">Konfigurasi sekarang →</a></span>
                </div>
            <?php endif; ?>

            <!-- Stat chips -->
            <div class="stat-chips">
                <div class="stat-chip">
                    <i class="bi bi-envelope text-primary"></i>
                    <div><strong><?= (int)$counts['total'] ?></strong><br><span>Total</span></div>
                </div>
                <div class="stat-chip red">
                    <i class="bi bi-envelope-exclamation text-danger"></i>
                    <div><strong><?= (int)$counts['unread'] ?></strong><br><span>Belum Dibaca</span></div>
                </div>
                <div class="stat-chip orange">
                    <i class="bi bi-reply text-warning"></i>
                    <div><strong><?= (int)$counts['unreplied'] ?></strong><br><span>Belum Dibalas</span></div>
                </div>
            </div>

            <!-- Filter tabs -->
            <div class="filter-tabs">
                <a href="?filter=all"       class="filter-tab <?= $filter === 'all'       ? 'active' : '' ?>">Semua</a>
                <a href="?filter=unread"    class="filter-tab <?= $filter === 'unread'    ? 'active' : '' ?>">Belum Dibaca</a>
                <a href="?filter=read"      class="filter-tab <?= $filter === 'read'      ? 'active' : '' ?>">Sudah Dibaca</a>
                <a href="?filter=unreplied" class="filter-tab <?= $filter === 'unreplied' ? 'active' : '' ?>">Belum Dibalas</a>
                <a href="?filter=replied"   class="filter-tab <?= $filter === 'replied'   ? 'active' : '' ?>">Sudah Dibalas</a>
            </div>

            <!-- Bulk form wrapper -->
            <form method="POST" id="bulkForm">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">

                <div class="bulk-bar" id="bulkBar">
                    <i class="bi bi-check-square text-warning"></i>
                    <span id="bulkCount">0</span> pesan dipilih
                    <button type="submit" name="bulk_delete" value="1" class="btn-adm del ms-2"
                            onclick="return confirm('Hapus pesan yang dipilih?')">
                        <i class="bi bi-trash me-1"></i>Hapus Terpilih
                    </button>
                    <button type="button" class="btn-adm secondary ms-1" onclick="clearSelection()">Batal</button>
                </div>

                <div class="adm-card">
                    <div class="adm-card-header">
                        <h5>Pesan (<?= count($messages) ?>)</h5>
                        <label class="d-flex align-items-center gap-2 text-muted small" style="cursor:pointer;">
                            <input type="checkbox" id="checkAll" onchange="toggleAll(this)"> Pilih Semua
                        </label>
                    </div>

                    <?php if (empty($messages)): ?>
                        <div class="empty-state">
                            <i class="bi bi-envelope-open"></i>
                            <p>Tidak ada pesan<?= $filter !== 'all' ? ' pada filter ini' : '' ?>.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th style="width:36px;"></th>
                                        <th>Pengirim</th>
                                        <th>Subjek / Pesan</th>
                                        <th>Jenis Muatan</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($messages as $msg): ?>
                                    <tr class="<?= !$msg['is_read'] ? 'msg-unread' : '' ?>" id="row-<?= (int)$msg['id'] ?>">
                                        <td>
                                            <input type="checkbox" name="selected[]"
                                                   value="<?= (int)$msg['id'] ?>"
                                                   class="row-check" onchange="updateBulk()">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if (!$msg['is_read']): ?>
                                                    <span class="msg-dot"></span>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?= htmlspecialchars($msg['name']) ?></strong>
                                                    <div class="text-muted small"><?= htmlspecialchars($msg['email']) ?></div>
                                                    <?php if (!empty($msg['phone'])): ?>
                                                        <div class="text-muted small">
                                                            <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($msg['phone']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="max-width:300px;">
                                            <?php if (!empty($msg['subject'])): ?>
                                                <div class="fw-semibold small mb-1"><?= htmlspecialchars($msg['subject']) ?></div>
                                            <?php endif; ?>
                                            <div class="text-muted small">
                                                <?= htmlspecialchars(mb_substr($msg['message'], 0, 80)) ?><?= mb_strlen($msg['message']) > 80 ? '…' : '' ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php $cargo = $msg['cargo_type_name'] ?? $msg['cargo_type'] ?? '-'; ?>
                                            <span class="adm-badge blue"><?= htmlspecialchars($cargo) ?></span>
                                        </td>
                                        <td class="text-muted small" style="white-space:nowrap;">
                                            <?= date('d/m/Y', strtotime($msg['created_at'])) ?><br>
                                            <span><?= date('H:i', strtotime($msg['created_at'])) ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <a href="?read=<?= (int)$msg['id'] ?>&filter=<?= urlencode($filter) ?>"
                                                   class="adm-badge <?= $msg['is_read'] ? 'green' : 'gray' ?>"
                                                   style="white-space:nowrap;">
                                                    <?= $msg['is_read']
                                                        ? '<i class="bi bi-eye"></i> Dibaca'
                                                        : '<i class="bi bi-eye-slash"></i> Baru' ?>
                                                </a>
                                                <a href="?replied=<?= (int)$msg['id'] ?>&filter=<?= urlencode($filter) ?>"
                                                   class="adm-badge <?= $msg['is_replied'] ? 'green' : 'gray' ?>"
                                                   style="white-space:nowrap;">
                                                    <?= $msg['is_replied']
                                                        ? '<i class="bi bi-reply"></i> Dibalas'
                                                        : '<i class="bi bi-reply"></i> Pending' ?>
                                                </a>
                                            </div>
                                        </td>
                                        <td style="white-space:nowrap;">
                                            <!-- Detail -->
                                            <a href="#"
                                               class="btn-adm edit"
                                               onclick="openDetail(<?= htmlspecialchars(json_encode($msg), ENT_QUOTES) ?>);return false;"
                                               title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <!-- Balas via Email (SMTP) -->
                                            <?php if (!empty($msg['email'])): ?>
                                                <a href="#"
                                                   class="btn-adm secondary ms-1 <?= !$smtpConfigured ? 'disabled' : '' ?>"
                                                   onclick="openReply(<?= htmlspecialchars(json_encode($msg), ENT_QUOTES) ?>);return false;"
                                                   title="<?= $smtpConfigured ? 'Balas via Email' : 'SMTP belum dikonfigurasi' ?>">
                                                    <i class="bi bi-reply"></i>
                                                </a>
                                            <?php endif; ?>

                                            <!-- Balas via WhatsApp -->
                                            <?php if (!empty($msg['phone'])): ?>
                                                <?php
                                                    $wa = preg_replace('/[^0-9]/', '', $msg['phone']);
                                                    if ($wa && $wa[0] === '0') $wa = '62' . substr($wa, 1);
                                                    $waText = 'Halo ' . rawurlencode($msg['name']) . ', kami merespons pesan Anda tentang ' . rawurlencode($msg['subject'] ?? 'layanan kami') . '.';
                                                ?>
                                                <a href="https://wa.me/<?= htmlspecialchars($wa) ?>?text=<?= $waText ?>"
                                                   target="_blank" rel="noopener"
                                                   class="btn-adm secondary ms-1"
                                                   title="Balas via WhatsApp"
                                                   onclick="markReplied(<?= (int)$msg['id'] ?>)">
                                                    <i class="bi bi-whatsapp"></i>
                                                </a>
                                            <?php endif; ?>

                                            <!-- Hapus -->
                                            <a href="?delete=<?= (int)$msg['id'] ?>&filter=<?= urlencode($filter) ?>"
                                               class="btn-adm del ms-1"
                                               onclick="return confirm('Hapus pesan ini?')"
                                               title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div><!-- /adm-content -->
    </div><!-- /adm-main -->
</div><!-- /adm-layout -->

<!-- ═══════════════════════════════════════════════════════════
     MODAL: Detail Pesan
═══════════════════════════════════════════════════════════ -->
<div class="adm-modal-overlay" id="detailModal">
    <div class="adm-modal" style="max-width:600px;">
        <div class="adm-modal-header">
            <h5 id="detailTitle">Detail Pesan</h5>
            <button class="adm-modal-close" onclick="closeDetail()"><i class="bi bi-x"></i></button>
        </div>
        <div class="adm-modal-body" id="detailBody"></div>
        <div class="adm-modal-footer">
            <button id="detailBtnReply" type="button" class="btn-adm save"
                    onclick="switchToReply()">
                <i class="bi bi-reply me-1"></i>Balas Email
            </button>
            <a id="detailWa" href="#" class="btn-adm secondary" target="_blank" rel="noopener">
                <i class="bi bi-whatsapp me-1"></i>WhatsApp
            </a>
            <button type="button" class="btn-adm secondary" onclick="closeDetail()">Tutup</button>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL: Balas Email (SMTP)
═══════════════════════════════════════════════════════════ -->
<div class="adm-modal-overlay" id="replyModal">
    <div class="adm-modal" style="max-width:580px;">
        <div class="adm-modal-header">
            <h5 id="replyModalTitle">Balas Pesan</h5>
            <button class="adm-modal-close" onclick="closeReply()"><i class="bi bi-x"></i></button>
        </div>
        <form method="POST" id="replyForm">
            <input type="hidden" name="send_reply" value="1">
            <input type="hidden" name="msg_id"     id="replyMsgId">
            <input type="hidden" name="filter"     value="<?= htmlspecialchars($filter) ?>">
            <div class="adm-modal-body">
                <div class="mb-3">
                    <label class="adm-form-label">Kepada</label>
                    <input type="text" id="replyTo" class="adm-input" readonly
                           style="background:var(--adm-bg-secondary,#f8fafc);">
                </div>
                <div class="mb-3">
                    <label class="adm-form-label">Subject</label>
                    <input type="text" name="reply_subject" id="replySubject" class="adm-input">
                </div>
                <div class="mb-3">
                    <label class="adm-form-label">Pesan Balasan</label>
                    <textarea name="reply_body" id="replyBody" class="adm-input"
                              rows="7" required
                              placeholder="Tulis balasan Anda di sini..."></textarea>
                </div>
                <?php if (!$smtpConfigured): ?>
                    <div class="alert alert-warning py-2 small">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        SMTP belum dikonfigurasi.
                        <a href="settings.php#tab-smtp">Konfigurasi di Pengaturan →</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="adm-modal-footer">
                <button type="submit" class="btn-adm save" <?= !$smtpConfigured ? 'disabled' : '' ?>>
                    <i class="bi bi-send me-1"></i>Kirim Email
                </button>
                <button type="button" class="btn-adm secondary" onclick="closeReply()">Batal</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Sidebar toggle ───────────────────────────────────────────────────────────
const toggle  = document.getElementById('topbarToggle');
const sidebar = document.getElementById('pbsSidebar');
const ov      = document.getElementById('sidebarOverlay');
const cl      = document.getElementById('sidebarClose');
toggle?.addEventListener('click', () => { sidebar.classList.toggle('open'); ov.classList.toggle('show'); });
ov?.addEventListener('click',     () => { sidebar.classList.remove('open'); ov.classList.remove('show'); });
cl?.addEventListener('click',     () => { sidebar.classList.remove('open'); ov.classList.remove('show'); });

// ── Bulk select ──────────────────────────────────────────────────────────────
function toggleAll(el) {
    document.querySelectorAll('.row-check').forEach(c => c.checked = el.checked);
    updateBulk();
}
function updateBulk() {
    const checked = document.querySelectorAll('.row-check:checked');
    document.getElementById('bulkCount').textContent = checked.length;
    document.getElementById('bulkBar').classList.toggle('show', checked.length > 0);
}
function clearSelection() {
    document.querySelectorAll('.row-check, #checkAll').forEach(c => c.checked = false);
    document.getElementById('bulkBar').classList.remove('show');
}

// ── Tandai replied via fetch (untuk tombol WA) ───────────────────────────────
function markReplied(id) {
    fetch(location.pathname + '?replied=' + id).catch(() => {});
}

// ── Escape HTML (untuk innerHTML dari data server) ───────────────────────────
function esc(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── MODAL: Detail ─────────────────────────────────────────────────────────────
const detailModal = document.getElementById('detailModal');
let   currentMsg  = null;

function openDetail(d) {
    currentMsg = d;
    const cargo = d.cargo_type_name || d.cargo_type || '-';
    let wa = '';
    if (d.phone) {
        wa = d.phone.replace(/[^0-9]/g, '');
        if (wa[0] === '0') wa = '62' + wa.substring(1);
    }

    document.getElementById('detailTitle').textContent = 'Pesan dari ' + d.name;
    document.getElementById('detailBody').innerHTML = `
        <div class="row g-3">
            <div class="col-6">
                <label class="adm-form-label text-muted">Nama</label>
                <div class="fw-semibold">${esc(d.name)}</div>
            </div>
            <div class="col-6">
                <label class="adm-form-label text-muted">Email</label>
                <div>${d.email ? `<a href="mailto:${esc(d.email)}">${esc(d.email)}</a>` : '-'}</div>
            </div>
            <div class="col-6">
                <label class="adm-form-label text-muted">Telepon / WA</label>
                <div>${esc(d.phone || '-')}</div>
            </div>
            <div class="col-6">
                <label class="adm-form-label text-muted">Jenis Muatan</label>
                <div><span class="adm-badge blue">${esc(cargo)}</span></div>
            </div>
            ${d.subject ? `<div class="col-12"><label class="adm-form-label text-muted">Subjek</label><div class="fw-semibold">${esc(d.subject)}</div></div>` : ''}
            <div class="col-12">
                <label class="adm-form-label text-muted">Pesan</label>
                <div class="p-3 rounded-3" style="background:#f8fafc;white-space:pre-line;line-height:1.7;">${esc(d.message)}</div>
            </div>
            <div class="col-12 d-flex gap-2 flex-wrap">
                <span class="adm-badge ${d.is_read    ? 'green' : 'gray'}">${d.is_read    ? 'Sudah Dibaca'  : 'Belum Dibaca'}</span>
                <span class="adm-badge ${d.is_replied ? 'green' : 'gray'}">${d.is_replied ? 'Sudah Dibalas' : 'Belum Dibalas'}</span>
                <span class="adm-badge blue ms-auto small">${esc(d.created_at.substring(0, 16).replace('T', ' '))}</span>
            </div>
        </div>`;

    // Tombol Balas Email
    const btnReply = document.getElementById('detailBtnReply');
    if (d.email) {
        btnReply.style.display = '';
    } else {
        btnReply.style.display = 'none';
    }

    // Tombol WhatsApp
    const btnWa = document.getElementById('detailWa');
    if (wa) {
        btnWa.href = `https://wa.me/${wa}?text=${encodeURIComponent('Halo ' + d.name + ', kami merespons pesan Anda.')}`;
        btnWa.style.display = '';
    } else {
        btnWa.style.display = 'none';
    }

    // Auto-mark as read
    if (!d.is_read) {
        fetch(location.pathname + '?read=' + d.id).catch(() => {});
        document.querySelector('#row-' + d.id)?.classList.remove('msg-unread');
        currentMsg.is_read = 1;
    }

    detailModal.classList.add('show');
}
function closeDetail() { detailModal.classList.remove('show'); }
detailModal.addEventListener('click', e => { if (e.target === detailModal) closeDetail(); });

// Tombol "Balas Email" di modal detail — switch ke modal reply
function switchToReply() {
    if (!currentMsg) return;
    closeDetail();
    setTimeout(() => openReply(currentMsg), 150);
}

// ── MODAL: Balas Email ────────────────────────────────────────────────────────
const replyModal       = document.getElementById('replyModal');
const defaultSubject   = <?= json_encode($s['smtp_default_subject'] ?? 'Re: Pesan dari website') ?>;

function openReply(d) {
    currentMsg = d;
    document.getElementById('replyMsgId').value        = d.id;
    document.getElementById('replyModalTitle').textContent = 'Balas ke ' + d.name;
    document.getElementById('replyTo').value           = d.name + ' <' + (d.email || '') + '>';
    document.getElementById('replySubject').value      = 'Re: ' + (d.subject || defaultSubject);
    document.getElementById('replyBody').value         = '';
    replyModal.classList.add('show');
    setTimeout(() => document.getElementById('replyBody').focus(), 200);
}
function closeReply() { replyModal.classList.remove('show'); }
replyModal.addEventListener('click', e => { if (e.target === replyModal) closeReply(); });

// Konfirmasi sebelum kirim
document.getElementById('replyForm').addEventListener('submit', function (e) {
    const body = document.getElementById('replyBody').value.trim();
    if (!body) { e.preventDefault(); alert('Isi pesan tidak boleh kosong.'); }
});
</script>
</body>
</html>