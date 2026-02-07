<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';
requireLogin();
$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse('error', null, 'Database connection failed');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse('error', null, 'Invalid request');
}
$userId = getCurrentUserId();
$settings = isset($_POST['settings']) ? $_POST['settings'] : '{}';
if (json_decode($settings) === null) {
    sendJsonResponse('error', null, 'Invalid data format');
}
try {
    $sql = "UPDATE users SET settings = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $settings, $userId);
    if ($stmt->execute()) {
        sendJsonResponse('success', null, 'Settings saved');
    } else {
        sendJsonResponse('error', null, 'Failed to save settings');
    }
    $stmt->close();
} catch (Exception $e) {
    sendJsonResponse('error', null, 'Error saving settings');
}
?>