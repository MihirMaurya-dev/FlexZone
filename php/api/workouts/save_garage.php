<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse('error', null, 'Invalid request');
}

$equipment = $_POST['equipment'] ?? '[]';

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
        throw new Exception("Update failed: " . $stmt->error);
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Save garage error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Error saving equipment');
} finally {
    if (isset($conn)) $conn->close();
}
?>
