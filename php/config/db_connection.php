<?php
declare(strict_types=1);
if (!defined('FLEXZONE_APP')) {
    define('FLEXZONE_APP', true);
}
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'flexzone');
define('DB_PORT', 3307);
define('DB_CHARSET', 'utf8mb4');
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');
if (!is_dir(__DIR__ . '/../../logs')) {
    mkdir(__DIR__ . '/../../logs', 0755, true);
}
function getDbConnection(): ?mysqli
{
    static $conn = null;
    if ($conn === null) {
        try {
            $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }
            if (!$conn->set_charset(DB_CHARSET)) {
                throw new Exception("Charset loading failed: " . $conn->error);
            }
        } catch (Exception $e) {
            error_log("Database connection exception: " . $e->getMessage());
            return null;
        }
    }
    return $conn;
}
function sendJsonResponse(string $status, ?array $data = null, ?string $message = null): void
{
    header('Content-Type: application/json; charset=utf-8');
    $response = ['status' => $status];
    if ($message !== null) {
        $response['message'] = $message;
    }
    if ($data !== null) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
function sanitizeInput($data, string $type = 'string')
{
    if ($data === null || $data === '') {
        return null;
    }
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        case 'int':
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        default:
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}
function isLoggedIn(): bool
{
    return isset($_SESSION['userid']) && isset($_SESSION['username']);
}
function requireLogin(): void
{
    if (!isLoggedIn()) {
        sendJsonResponse('error', null, 'Authentication required. Please log in.');
    }
}
function getCurrentUserId(): ?int
{
    return isLoggedIn() ? (int)$_SESSION['userid'] : null;
}
function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_secure', '0');
        ini_set('session.cookie_samesite', 'Strict');
        session_start();
        $timeout = 1800;
        if (isset($_SESSION['created']) && (time() - $_SESSION['created'] > $timeout)) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        } elseif (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        }
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            session_unset();
            session_destroy();
            session_start();
        }
        $_SESSION['last_activity'] = time();
    }
}
startSecureSession();
