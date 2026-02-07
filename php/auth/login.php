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
        sendJsonResponse('error', null, 'Invalid email or password');
    }
    $user = $result->fetch_assoc();
    $stmt->close();
    if (!password_verify($password, $user['password'])) {
        sendJsonResponse('error', null, 'Invalid email or password');
    }
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