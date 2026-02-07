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
$equipment = isset($_POST['equipment']) ? $_POST['equipment'] : '[]';
if (json_decode($equipment) === null) {
    sendJsonResponse('error', null, 'Invalid data format');
}
try {
    $sql = "UPDATE users SET garage = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $equipment, $userId);
    if ($stmt->execute()) {
        sendJsonResponse('success', null, 'Garage updated');
    } else {
        sendJsonResponse('error', null, 'Failed to save (Database update required)');
    }
    $stmt->close();
} catch (Exception $e) {
    sendJsonResponse('error', null, 'Error saving equipment');
}
?>