<?php
// ─── Load Error Handler First (Prevents Crashes) ──────────────
require_once __DIR__ . '/error-handler.php';

// ─── FSPO Ltd – Configuration ───────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'fspo_user');        // Change to your MySQL username
define('DB_PASS', 'fspo_password');    // Change to your MySQL password
define('DB_NAME', 'fspo_db');

define('SITE_NAME', 'FSPO Ltd');
define('SITE_URL',  'http://localhost:8000');  // Change to your domain
define('SITE_EMAIL','info@fspoltd.rw');
define('SITE_PHONE','+250 785 723 677');
define('SITE_ADDRESS','Kigali-Gakinjiro, Rwanda');
define('SITE_HOURS', 'Mon – Sat / 07:00 AM – 08:00 PM');

// ─── PDO Connection ──────────────────────────────────────────
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:20px;background:#fee;border:1px solid red;margin:20px;border-radius:8px">
                <h2>Database Connection Error</h2>
                <p>Could not connect to the database. Please check your config.php settings.</p>
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
