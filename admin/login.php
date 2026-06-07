<?php
// admin/login.php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';

startAdminSession();
if (isAdminLoggedIn()) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'Token tidak valid. Silakan coba lagi.';
    } else {
        $result = adminLogin(trim($_POST['username'] ?? ''), $_POST['password'] ?? '');
        if ($result['success']) {
            header('Location: ' . BASE_URL . '/admin/dashboard.php');
            exit;
        }
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: #0F172A;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        /* Animated background */
        .bg-grid {
            position: fixed; inset: 0; z-index: 0;
            background-image:
                linear-gradient(rgba(28,79,138,0.15) 1px, transparent 1px),
                linear-gradient(90deg, rgba(28,79,138,0.15) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .bg-glow {
            position: fixed; z-index: 0;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.25;
        }
        .bg-glow-1 { width: 500px; height: 500px; background: #1B4F8A; top: -150px; left: -100px; }
        .bg-glow-2 { width: 400px; height: 400px; background: #C8202D; bottom: -100px; right: -80px; }

        /* Card */
        .login-wrap { position: relative; z-index: 1; width: 100%; max-width: 440px; padding: 24px; }
        .login-card {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.4);
        }
        .login-logo {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 32px;
        }
        .login-logo-icon {
            width: 48px; height: 48px; border-radius: 14px;
            background: linear-gradient(135deg, #C8202D, #A01820);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: white;
            box-shadow: 0 8px 24px rgba(200,32,45,0.4);
        }
        .login-logo-text { color: white; }
        .login-logo-text strong { display: block; font-size: 17px; font-weight: 800; letter-spacing: -0.3px; }
        .login-logo-text span { font-size: 12px; color: rgba(255,255,255,0.5); }

        .login-title { color: white; font-size: 26px; font-weight: 800; margin-bottom: 6px; letter-spacing: -0.5px; }
        .login-sub { color: rgba(255,255,255,0.45); font-size: 14px; margin-bottom: 32px; }

        .form-label { color: rgba(255,255,255,0.7); font-size: 13px; font-weight: 600; margin-bottom: 8px; }
        .form-control {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            color: white;
            padding: 12px 16px;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.2s;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.1);
            border-color: #1B4F8A;
            color: white;
            box-shadow: 0 0 0 3px rgba(27,79,138,0.3);
            outline: none;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.25); }
        .input-group .form-control { border-right: none; border-radius: 12px 0 0 12px; }
        .input-group .btn-eye {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-left: none;
            border-radius: 0 12px 12px 0;
            color: rgba(255,255,255,0.4);
            padding: 0 14px;
            transition: all 0.2s;
        }
        .input-group .btn-eye:hover { color: white; }

        .btn-login {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #C8202D, #A01820);
            border: none; border-radius: 12px;
            color: white; font-weight: 700; font-size: 15px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer; transition: all 0.2s;
            box-shadow: 0 8px 24px rgba(200,32,45,0.35);
            margin-top: 8px;
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 12px 32px rgba(200,32,45,0.45); }
        .btn-login:active { transform: translateY(0); }
        .backlogin {
            text-decoration: none;
            color: rgba(255,255,255,0.45);
            margin-top: 14px;
            display: block;
            text-align: center;
            font-size: 13px;
            transition: color 0.2s;
        }
        .backlogin:hover {
            color: white;
        }

        .alert-error {
            background: rgba(200,32,45,0.15);
            border: 1px solid rgba(200,32,45,0.3);
            border-radius: 12px;
            color: #ff8080;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
        }
        .footer-note { text-align: center; margin-top: 28px; color: rgba(255,255,255,0.2); font-size: 12px; }
    </style>
</head>
<body>
<div class="bg-grid"></div>
<div class="bg-glow bg-glow-1"></div>
<div class="bg-glow bg-glow-2"></div>

<div class="login-wrap">
    <div class="login-card">
        <div class="login-logo">
            <div class="login-logo-icon"><i class="bi bi-anchor"></i></div>
            <div class="login-logo-text">
                <strong>PBS Admin</strong>
                <span>Control Panel</span>
            </div>
        </div>

        <div class="login-title">Selamat Datang</div>
        <div class="login-sub">Masuk ke panel admin untuk mengelola website.</div>

        <?php if ($error): ?>
        <div class="alert-error"><i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <?= csrfField() ?>
            <div class="mb-3">
                <label class="form-label">Username atau Email</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username..." required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="passwordInput" class="form-control" placeholder="••••••••" required>
                    <button type="button" class="btn-eye" onclick="togglePassword()">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-login"><i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Panel</button>
            <a href="<?= BASE_URL ?>/index.php" class="backlogin">Back to Company Profile</a>
        </form>
    </div>
    <div class="footer-note">© <?= date('Y') ?> PT. Prima Bahari Sejahtera. All rights reserved.</div>
</div>

<script>
function togglePassword() {
    const inp = document.getElementById('passwordInput');
    const ico = document.getElementById('eyeIcon');
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>