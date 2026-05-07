<?php
// ============================================================
// includes/functions.php — Helper Functions
// ============================================================

require_once dirname(__DIR__) . '/config/database.php';

// ── SETTINGS ─────────────────────────────────────────────────
function getSetting(string $key, string $default = ''): string {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? (string)$row['setting_value'] : $default;
}

function getAllSettings(): array {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $result = [];
    while ($row = $stmt->fetch()) {
        $result[$row['setting_key']] = $row['setting_value'];
    }
    return $result;
}

function updateSetting(string $key, string $value): bool {
    $pdo = getPDO();
    $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    return $stmt->execute([$value, $key]);
}

// ── SERVICES ─────────────────────────────────────────────────
function getServices(bool $activeOnly = true): array {
    $pdo = getPDO();
    $where = $activeOnly ? 'WHERE is_active = 1' : '';
    return $pdo->query("SELECT * FROM services $where ORDER BY sort_order ASC, id ASC")->fetchAll();
}

function getServiceById(int $id): ?array {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function getFeaturedServices(): array {
    $pdo = getPDO();
    return $pdo->query("SELECT * FROM services WHERE is_featured = 1 AND is_active = 1 ORDER BY sort_order ASC LIMIT 6")->fetchAll();
}
// ── NEWS ─────────────────────────────────────────────────────
function getNews(int $limit = 3, bool $publishedOnly = true): array {
    $pdo = getPDO();
    $where = $publishedOnly ? 'WHERE is_published = 1' : '';
    $stmt  = $pdo->prepare("SELECT * FROM news $where ORDER BY published_at DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getNewsById(int $id): ?array {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

// ── TESTIMONIALS ─────────────────────────────────────────────
function getTestimonials(): array {
    $pdo = getPDO();
    return $pdo->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();
}

// ── GALLERY ──────────────────────────────────────────────────
function getGallery(): array {
    $pdo = getPDO();
    return $pdo->query("SELECT * FROM gallery WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();
}

// ── MESSAGES ─────────────────────────────────────────────────
function saveMessage(array $data): bool {
    $pdo  = getPDO();
    $stmt = $pdo->prepare("INSERT INTO messages (name, email, phone, subject, message, cargo_type) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        sanitize($data['name']    ?? ''),
        sanitize($data['email']   ?? ''),
        sanitize($data['phone']   ?? ''),
        sanitize($data['subject'] ?? ''),
        sanitize($data['message'] ?? ''),
        sanitize($data['cargo_type'] ?? 'Lainnya'),
    ]);
}

// ── UPLOAD ────────────────────────────────────────────────────
function handleUpload(array $file, string $subDir = '', array $allowedTypes = ['image/jpeg','image/png','image/webp','image/gif']): string|false {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if (!in_array($file['type'], $allowedTypes)) return false;
    if ($file['size'] > 10 * 1024 * 1024) return false; // 10MB max

    $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = uniqid('pbs_', true) . '.' . strtolower($ext);
    $dir  = UPLOAD_PATH . ltrim($subDir, '/');

    if (!is_dir($dir)) mkdir($dir, 0775, true);

    $dest = rtrim($dir, '/') . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return false;

    return 'uploads/' . ltrim($subDir, '/') . '/' . $name;
}

function handleVideoUpload(array $file): string|false {
    return handleUpload($file, 'hero', ['video/mp4', 'video/webm', 'video/ogg']);
}

// ── UTILITY ───────────────────────────────────────────────────
function sanitize(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

function slugify(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^\w\s-]/', '', $text);
    $text = preg_replace('/[\s_-]+/', '-', $text);
    return trim($text, '-');
}

function timeAgo(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)      return $diff . ' detik lalu';
    if ($diff < 3600)    return floor($diff/60) . ' menit lalu';
    if ($diff < 86400)   return floor($diff/3600) . ' jam lalu';
    if ($diff < 604800)  return floor($diff/86400) . ' hari lalu';
    return date('d M Y', strtotime($datetime));
}

function formatDate(string $datetime, string $format = 'd F Y'): string {
    $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $result = date($format, strtotime($datetime));
    // Replace English month names with Indonesian
    $eng = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    $ind = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    return str_replace($eng, $ind, $result);
}

function truncate(string $text, int $length = 120): string {
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

function assetUrl(string $path): string {
    return BASE_URL . '/public/' . ltrim($path, '/');
}

function uploadUrl(string $path): string {
    return BASE_URL . '/public/' . ltrim($path, '/');
}

function isActive(string $page): string {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return $current === $page ? 'active' : '';
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// ── ACTIVITY LOG ──────────────────────────────────────────────
function logActivity(int $adminId, string $action, string $desc = ''): void {
    $pdo  = getPDO();
    $ip   = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $stmt = $pdo->prepare("INSERT INTO activity_log (admin_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$adminId, $action, $desc, $ip]);
}