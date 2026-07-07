<?php
define('FLEXZONE_APP', true);
require_once '../config/db_connection.php';

$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse('error', null, 'Database connection failed');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse('error', null, 'Invalid request method');
}

$email = sanitizeInput($_POST['email'] ?? '', 'email');
$password = $_POST['password'] ?? '';

if (!$email || empty($password)) {
    sendJsonResponse('error', null, 'Email and password are required');
}

try {
    // ---- Rate Limiting ----
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $limit_time = 15 * 60; // 15 minutes lockout
    $max_attempts = 5;
    
    // Auto-create table if missing
    $conn->query("CREATE TABLE IF NOT EXISTS login_attempts (
        ip_address VARCHAR(45) NOT NULL,
        attempts INT DEFAULT 1,
        last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (ip_address)
    )");

    $stmt = $conn->prepare("SELECT attempts, UNIX_TIMESTAMP(last_attempt) as last_ts FROM login_attempts WHERE ip_address = ?");
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if ($row) {
        if ($row['attempts'] >= $max_attempts && (time() - $row['last_ts']) < $limit_time) {
            $remaining = ceil(($limit_time - (time() - $row['last_ts'])) / 60);
            sendJsonResponse('error', null, "Too many failed login attempts. Try again in $remaining minutes.");
        }
        if ((time() - $row['last_ts']) >= $limit_time) {
            $stmt = $conn->prepare("UPDATE login_attempts SET attempts = 0 WHERE ip_address = ?");
            $stmt->bind_param("s", $ip_address);
            $stmt->execute();
            $stmt->close();
        }
    }
    // ---- End Rate Limiting ----

    $sql = "SELECT id, username, password FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Login prepare failed: " . $conn->error);
        sendJsonResponse('error', null, 'An error occurred. Please try again.');
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        
        $incStmt = $conn->prepare("INSERT INTO login_attempts (ip_address, attempts) VALUES (?, 1) ON DUPLICATE KEY UPDATE attempts = attempts + 1");
        $incStmt->bind_param("s", $ip_address);
        $incStmt->execute();
        $incStmt->close();
        
        sendJsonResponse('error', null, 'Invalid email or password');
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!password_verify($password, $user['password'])) {
        $incStmt = $conn->prepare("INSERT INTO login_attempts (ip_address, attempts) VALUES (?, 1) ON DUPLICATE KEY UPDATE attempts = attempts + 1");
        $incStmt->bind_param("s", $ip_address);
        $incStmt->execute();
        $incStmt->close();
        
        sendJsonResponse('error', null, 'Invalid email or password');
    }
    
    $resetStmt = $conn->prepare("UPDATE login_attempts SET attempts = 0 WHERE ip_address = ?");
    $resetStmt->bind_param("s", $ip_address);
    $resetStmt->execute();
    $resetStmt->close();

    session_regenerate_id(true);
    $_SESSION['userid'] = (int)$user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['last_activity'] = time();
    
    sendJsonResponse('success', [
        'userid' => $_SESSION['userid'],
        'username' => $_SESSION['username']
    ], 'Login successful');
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    sendJsonResponse('error', null, 'An error occurred during login');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
