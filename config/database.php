<?php
define('DB_HOST',     'localhost');
define('DB_NAME',     'pbs_db');
define('DB_USER',     'root');         
define('DB_PASS',     '');            
define('DB_CHARSET',  'utf8mb4');

// Path / URL
define('BASE_URL',    'http://localhost/pbs'); 
define('BASE_PATH',   dirname(__DIR__));         
define('UPLOAD_PATH', BASE_PATH . '/public/uploads/');
define('UPLOAD_URL',  BASE_URL  . '/public/uploads/');

// Session
define('SESSION_NAME', 'pbs_admin_session');
define('SESSION_LIFETIME', 3600); // 1 jam

// App info
define('APP_NAME',    'PBS Admin Panel');
define('APP_VERSION', '1.0.0');

function getPDO(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}