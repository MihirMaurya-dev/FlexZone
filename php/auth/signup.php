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
$username = sanitizeInput($_POST['username'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '', 'email');
$password = $_POST['password'] ?? '';
$errors = [];
if (empty($username) || strlen($username) < 3) {
    $errors[] = 'Username must be at least 3 characters';
}
if (strlen($username) > 50) {
    $errors[] = 'Username must be less than 50 characters';
}
if (!$email) {
    $errors[] = 'Valid email is required';
}
if (empty($password) || strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters';
}
if (!empty($errors)) {
    sendJsonResponse('error', null, implode('. ', $errors));
}
try {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $checkSql = "SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1";
    $checkStmt = $conn->prepare($checkSql);
    if ($checkStmt === false) {
        error_log("Signup check prepare failed: " . $conn->error);
        sendJsonResponse('error', null, 'An error occurred. Please try again.');
    }
    $checkStmt->bind_param("ss", $email, $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    if ($checkResult->num_rows > 0) {
        $checkStmt->close();
        sendJsonResponse('error', null, 'Email or username already exists');
    }
    $checkStmt->close();
    $insertSql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    if ($insertStmt === false) {
        error_log("Signup insert prepare failed: " . $conn->error);
        sendJsonResponse('error', null, 'An error occurred. Please try again.');
    }
    $insertStmt->bind_param("sss", $username, $email, $hashedPassword);
    if ($insertStmt->execute()) {
        $insertStmt->close();
        sendJsonResponse('success', null, 'Account created successfully! You can now log in.');
    } else {
        error_log("Signup insert failed: " . $insertStmt->error);
        $insertStmt->close();
        sendJsonResponse('error', null, 'Failed to create account. Please try again.');
    }
} catch (Exception $e) {
    error_log("Signup error: " . $e->getMessage());
    sendJsonResponse('error', null, 'An error occurred during registration');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>