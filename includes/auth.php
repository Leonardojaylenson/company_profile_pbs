<?php
// ============================================================
// includes/auth.php — Session & Authentication
// ============================================================

require_once dirname(__DIR__) . '/config/database.php';

function startAdminSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => false,       // Set true di production (HTTPS)
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function adminLogin(string $username, string $password): array {
    startAdminSession();
    $pdo  = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1");
    $stmt->execute([$username, $username]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password'])) {
        return ['success' => false, 'message' => 'Username atau password salah.'];
    }

    // Update last login
    $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?")->execute([$admin['id']]);

    $_SESSION['admin_id']       = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_name']     = $admin['full_name'];
    $_SESSION['admin_role']     = $admin['role'];
    $_SESSION['admin_avatar']   = $admin['avatar'];
    $_SESSION['logged_in']      = true;
    session_regenerate_id(true);

    return ['success' => true];
}

function adminLogout(): void {
    startAdminSession();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}

function isAdminLoggedIn(): bool {
    startAdminSession();
    return !empty($_SESSION['logged_in']) && !empty($_SESSION['admin_id']);
}

function requireAdminLogin(): void {
    if (!isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

function requireSuperAdmin(): void {
    requireAdminLogin();
    if (($_SESSION['admin_role'] ?? '') !== 'superadmin') {
        die('<h3>Akses ditolak. Hanya Superadmin yang bisa mengakses halaman ini.</h3>');
    }
}

function currentAdmin(): array {
    return [
        'id'       => $_SESSION['admin_id']       ?? 0,
        'username' => $_SESSION['admin_username']  ?? '',
        'name'     => $_SESSION['admin_name']      ?? '',
        'role'     => $_SESSION['admin_role']      ?? '',
        'avatar'   => $_SESSION['admin_avatar']    ?? null,
    ];
}

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken()) . '">';
}

