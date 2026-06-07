<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdminLogin();
$admin = currentAdmin();
$pdo   = getPDO();

if ($admin['role'] !== 'superadmin') {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

$filterAdmin  = isset($_GET['admin_id']) ? (int)$_GET['admin_id'] : 0;
$filterAction = trim($_GET['action_filter'] ?? '');
$filterDate   = trim($_GET['date'] ?? '');
$page         = max(1, (int)($_GET['page'] ?? 1));
$perPage      = 50;
$offset       = ($page - 1) * $perPage;

$conditions = [];
$params     = [];

if ($filterAdmin > 0) {
    $conditions[] = 'l.admin_id = ?';
    $params[]     = $filterAdmin;
}
if ($filterAction !== '') {
    $conditions[] = 'l.action = ?';
    $params[]     = $filterAction;
}
if ($filterDate !== '') {
    $conditions[] = 'DATE(l.created_at) = ?';
    $params[]     = $filterDate;
}

$where = $conditions ? ('WHERE ' . implode(' AND ', $conditions)) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM activity_log l $where");
$countStmt->execute($params);
$totalRows  = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$logStmt = $pdo->prepare("
    SELECT
        l.id,
        l.admin_id,
        l.action,
        l.description,
        l.ip_address,
        l.created_at,
        a.username,
        a.full_name,
        a.role AS admin_role
    FROM activity_log l
    LEFT JOIN admins a ON a.id = l.admin_id
    $where
    ORDER BY l.created_at DESC
    LIMIT $perPage OFFSET $offset
");
$logStmt->execute($params);
$logs = $logStmt->fetchAll();

$allAdmins  = $pdo->query("SELECT id, username, full_name FROM admins ORDER BY full_name")->fetchAll();
$allActions = $pdo->query("SELECT DISTINCT action FROM activity_log ORDER BY action")->fetchAll(PDO::FETCH_COLUMN);

function actionStyle(string $action): array {
    return match (true) {
        str_contains($action, 'DELETE') || str_contains($action, 'HAPUS')
            => ['badge' => 'red',    'icon' => 'bi-trash-fill'],
        str_contains($action, 'ADD') || str_contains($action, 'CREATE')
            => ['badge' => 'green',  'icon' => 'bi-plus-circle-fill'],
        str_contains($action, 'UPDATE') || str_contains($action, 'EDIT')
            => ['badge' => 'blue',   'icon' => 'bi-pencil-fill'],
        str_contains($action, 'LOGIN') || str_contains($action, 'LOGOUT')
            => ['badge' => 'orange', 'icon' => 'bi-box-arrow-in-right'],
        str_contains($action, 'RESET') || str_contains($action, 'PASSWORD')
            => ['badge' => 'orange', 'icon' => 'bi-key-fill'],
        default
            => ['badge' => 'gray',   'icon' => 'bi-activity'],
    };
}

$todayStats = $pdo->query("
    SELECT COUNT(*) AS total_today FROM activity_log WHERE DATE(created_at) = CURDATE()
")->fetch();
$weekStats = $pdo->query("
    SELECT COUNT(*) AS total_week FROM activity_log WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
")->fetch();
$totalStats = $pdo->query("SELECT COUNT(*) AS total_all FROM activity_log")->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas — <?= htmlspecialchars(APP_NAME) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/admin.css">
    <style>
        .action-badge {
            display:inline-flex;align-items:center;gap:5px;
            padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;
            white-space:nowrap;
        }
        .action-badge.red    { background:#fee2e2;color:#dc2626; }
        .action-badge.green  { background:#dcfce7;color:#16a34a; }
        .action-badge.blue   { background:#dbeafe;color:#1d4ed8; }
        .action-badge.orange { background:#fff7ed;color:#ea580c; }
        .action-badge.gray   { background:#f1f5f9;color:#64748b; }

        .stat-chips  { display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px; }
        .stat-chip   { background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;
                       padding:10px 20px;display:flex;align-items:center;gap:10px;font-size:13px; }
        .stat-chip strong { font-size:22px;font-weight:700;color:#1B4F8A; }

        .filter-bar { background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;
                      padding:14px 18px;margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end; }
        .filter-bar .adm-input { height:36px;font-size:13px;padding:0 10px; }
        .filter-bar label { font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px; }

        .log-dot { width:8px;height:8px;border-radius:50%;display:inline-block;flex-shrink:0;margin-top:2px; }
        .log-dot.red    { background:#dc2626; }
        .log-dot.green  { background:#16a34a; }
        .log-dot.blue   { background:#1d4ed8; }
        .log-dot.orange { background:#ea580c; }
        .log-dot.gray   { background:#94a3b8; }

        .avatar-xs {
            width:28px;height:28px;border-radius:50%;background:#1B4F8A;color:#fff;
            display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:11px;flex-shrink:0;
        }

        .pagination-bar { display:flex;gap:6px;align-items:center;justify-content:center;flex-wrap:wrap;margin-top:20px; }
        .page-btn {
            padding:5px 12px;border-radius:8px;border:1.5px solid #e2e8f0;
            font-size:13px;font-weight:600;color:#64748b;text-decoration:none;background:#fff;transition:.15s;
        }
        .page-btn:hover   { background:#f0f6ff;border-color:#1B4F8A;color:#1B4F8A; }
        .page-btn.active  { background:#1B4F8A;color:#fff;border-color:#1B4F8A; }
        .page-btn.disabled{ opacity:.4;pointer-events:none; }

        .readonly-notice {
            display:inline-flex;align-items:center;gap:6px;
            background:#f0f6ff;border:1.5px solid #bfdbfe;border-radius:8px;
            padding:6px 14px;font-size:12px;font-weight:600;color:#1d4ed8;
        }
    </style>
</head>
<body>
<div class="adm-layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="adm-main">

        <div class="adm-topbar">
            <button class="topbar-toggle" id="topbarToggle"><i class="bi bi-list"></i></button>
            <div class="topbar-title">
                <i class="bi bi-clock-history me-2"></i>Log Aktivitas
            </div>
            <div class="topbar-actions">
                <span class="readonly-notice">
                    <i class="bi bi-shield-lock-fill"></i> Read-only — Log tidak bisa diedit atau dihapus
                </span>
            </div>
        </div>

        <div class="adm-content">

            <div class="stat-chips">
                <div class="stat-chip">
                    <i class="bi bi-clock text-primary fs-5"></i>
                    <div><strong><?= (int)$todayStats['total_today'] ?></strong><br><span>Aktivitas Hari Ini</span></div>
                </div>
                <div class="stat-chip">
                    <i class="bi bi-calendar-week text-primary fs-5"></i>
                    <div><strong><?= (int)$weekStats['total_week'] ?></strong><br><span>7 Hari Terakhir</span></div>
                </div>
                <div class="stat-chip">
                    <i class="bi bi-database text-primary fs-5"></i>
                    <div><strong><?= number_format((int)$totalStats['total_all']) ?></strong><br><span>Total Log</span></div>
                </div>
            </div>

            <form method="GET" id="filterForm">
                <div class="filter-bar">
                    <div>
                        <label>Admin</label>
                        <select name="admin_id" class="adm-input" style="min-width:160px;">
                            <option value="">Semua Admin</option>
                            <?php foreach ($allAdmins as $a): ?>
                                <option value="<?= (int)$a['id'] ?>"
                                    <?= $filterAdmin === (int)$a['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($a['full_name']) ?> (@<?= htmlspecialchars($a['username']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>Aksi</label>
                        <select name="action_filter" class="adm-input" style="min-width:180px;">
                            <option value="">Semua Aksi</option>
                            <?php foreach ($allActions as $act): ?>
                                <option value="<?= htmlspecialchars($act) ?>"
                                    <?= $filterAction === $act ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($act) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>Tanggal</label>
                        <input type="date" name="date" class="adm-input"
                               value="<?= htmlspecialchars($filterDate) ?>"
                               style="min-width:140px;">
                    </div>
                    <div class="d-flex gap-2 align-items-end">
                        <button type="submit" class="btn-adm save" style="height:36px;padding:0 16px;font-size:13px;">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <?php if ($filterAdmin || $filterAction || $filterDate): ?>
                            <a href="activity_log.php" class="btn-adm secondary" style="height:36px;padding:0 14px;font-size:13px;">
                                <i class="bi bi-x me-1"></i>Reset
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="ms-auto text-muted small d-flex align-items-end pb-1">
                        <?= number_format($totalRows) ?> entri ditemukan
                    </div>
                </div>
            </form>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h5>
                        Log Aktivitas
                        <?php if ($filterAdmin || $filterAction || $filterDate): ?>
                            <span class="adm-badge blue ms-2">Terfilter</span>
                        <?php endif; ?>
                    </h5>
                    <span class="text-muted small">Halaman <?= $page ?> / <?= $totalPages ?></span>
                </div>

                <?php if (empty($logs)): ?>
                    <div class="empty-state">
                        <i class="bi bi-clock-history"></i>
                        <p>Tidak ada log aktivitas<?= ($filterAdmin || $filterAction || $filterDate) ? ' untuk filter ini' : '' ?>.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Admin</th>
                                    <th>Aksi</th>
                                    <th>Deskripsi</th>
                                    <th style="white-space:nowrap;">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($logs as $i => $log): $style = actionStyle($log['action']); ?>
                                <tr>
                                    <td class="text-muted small nowrap"><?= $offset + $i + 1 ?></td>
                                    <td class="nowrap">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-xs"><?= mb_strtoupper(mb_substr($log['full_name'] ?? '?', 0, 1)) ?></div>
                                            <div>
                                                <div class="fw-semibold small"><?= htmlspecialchars($log['full_name'] ?? 'Dihapus') ?></div>
                                                <div class="text-muted" style="font-size:11px;">
                                                    @<?= htmlspecialchars($log['username'] ?? '-') ?>
                                                    <?php if ($log['admin_role']): ?> · <?= htmlspecialchars($log['admin_role']) ?><?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="nowrap">
                                        <span class="action-badge <?= $style['badge'] ?>"><i class="bi <?= $style['icon'] ?>"></i> <?= htmlspecialchars($log['action']) ?></span>
                                    </td>
                                    <td style="min-width: 250px;">
                                        <div class="d-flex align-items-start gap-2">
                                            <span class="log-dot <?= $style['badge'] ?> mt-1"></span>
                                            <span class="text-muted small" style="line-height:1.5;"><?= htmlspecialchars($log['description'] ?: '—') ?></span>
                                        </div>
                                    </td>
                                    <td class="nowrap">
                                        <div class="small fw-semibold"><?= date('d/m/Y', strtotime($log['created_at'])) ?></div>
                                        <div class="text-muted" style="font-size:11px;"><?= date('H:i:s', strtotime($log['created_at'])) ?></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($totalPages > 1):
                        $qp = $_GET;
                        unset($qp['page']);
                        $qBase = $qp ? ('&' . http_build_query($qp)) : '';
                    ?>
                    <div class="pagination-bar">
                        <a href="?page=1<?= $qBase ?>"
                           class="page-btn <?= $page <= 1 ? 'disabled' : '' ?>">
                            <i class="bi bi-chevron-double-left"></i>
                        </a>
                        <a href="?page=<?= max(1, $page-1) ?><?= $qBase ?>"
                           class="page-btn <?= $page <= 1 ? 'disabled' : '' ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>

                        <?php
                        $start = max(1, $page - 2);
                        $end   = min($totalPages, $page + 2);
                        if ($start > 1) echo '<span class="text-muted small">…</span>';
                        for ($p = $start; $p <= $end; $p++):
                        ?>
                            <a href="?page=<?= $p ?><?= $qBase ?>"
                               class="page-btn <?= $p === $page ? 'active' : '' ?>">
                                <?= $p ?>
                            </a>
                        <?php endfor;
                        if ($end < $totalPages) echo '<span class="text-muted small">…</span>';
                        ?>

                        <a href="?page=<?= min($totalPages, $page+1) ?><?= $qBase ?>"
                           class="page-btn <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <a href="?page=<?= $totalPages ?><?= $qBase ?>"
                           class="page-btn <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <i class="bi bi-chevron-double-right"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const toggle  = document.getElementById('topbarToggle');
const sidebar = document.getElementById('pbsSidebar');
const ov      = document.getElementById('sidebarOverlay');
const cl      = document.getElementById('sidebarClose');
toggle?.addEventListener('click', () => { sidebar.classList.toggle('open'); ov.classList.toggle('show'); });
ov?.addEventListener('click',     () => { sidebar.classList.remove('open'); ov.classList.remove('show'); });
cl?.addEventListener('click',     () => { sidebar.classList.remove('open'); ov.classList.remove('show'); });
</script>
</body>
</html>