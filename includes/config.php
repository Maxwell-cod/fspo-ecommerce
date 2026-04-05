<?php
// ─── Load Error Handler First (Prevents Crashes) ──────────────
require_once __DIR__ . '/error-handler.php';

// ─── FSPO Ltd – Configuration ───────────────────────────────
// Support both environment variables (Render) and hardcoded values (Local development)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'fspo_user');
define('DB_PASS', getenv('DB_PASSWORD') ?: 'fspo_password');
define('DB_NAME', getenv('DB_NAME') ?: 'fspo_db');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_DRIVER', getenv('DB_DRIVER') ?: 'mysql');

define('SITE_NAME', 'FSPO Ltd');

// ─── Intelligent SITE_URL Detection ──────────────────────────
// Priority 1: Environment variable (if explicitly set on Render)
// Priority 2: Detect from HTTP_HOST (automatic on Render)
// Priority 3: Default to localhost for local development
if ($siteUrl = getenv('SITE_URL')) {
    // Explicitly set via environment variable
    define('SITE_URL', $siteUrl);
} else {
    // Auto-detect from HTTP_HOST with proper HTTPS detection
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
               (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $protocol = $isHttps ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
    define('SITE_URL', $protocol . $host);
}

define('SITE_EMAIL','info@fspoltd.rw');
define('SITE_PHONE','+250 785 723 677');
define('SITE_ADDRESS','Kigali-Gakinjiro, Rwanda');
define('SITE_HOURS', 'Mon – Sat / 07:00 AM – 08:00 PM');

// ─── PDO Connection ──────────────────────────────────────────
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            // Determine DSN based on database driver
            if (DB_DRIVER === 'pgsql') {
                // PostgreSQL DSN - uses TCP connection (works on Render)
                $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
            } else {
                // MySQL DSN - default for local development
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            }
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:20px;background:#fee;border:1px solid red;margin:20px;border-radius:8px">
                <h2>Database Connection Error</h2>
                <p>Could not connect to the database. Please check your config.php settings.</p>
                <p><strong>Driver:</strong> ' . DB_DRIVER . ' | <strong>Host:</strong> ' . DB_HOST . ' | <strong>Database:</strong> ' . DB_NAME . '</p>
                <small>' . htmlspecialchars($e->getMessage()) . '</small>
                </div>');
        }
    }
    return $pdo;
}

// ─── Session ─────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── CSRF Token Protection ──────────────────────────────────
function generateCSRFToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// ─── Helper Functions ─────────────────────────────────────────
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function requireAdmin(): void {
    if (!isAdmin()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

function formatRwf(float $amount): string {
    return 'Rwf ' . number_format($amount, 0, '.', ',');
}

function generateOrderNumber(): string {
    return 'FSPO-' . strtoupper(substr(md5(uniqid()), 0, 8));
}

function sanitize(string $str): string {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function getCartCount(): int {
    if (!isLoggedIn()) return 0;
    $db = getDB();
    $stmt = $db->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return (int)$stmt->fetchColumn();
}

function slug(string $str): string {
    return strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $str), '-'));
}
