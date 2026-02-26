<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse('error', null, 'Invalid request method');
}

$settings = $_POST['settings'] ?? '{}';

if (json_decode($settings) === null) {
    sendJsonResponse('error', null, 'Invalid data format');
}

try {
    $sql = "UPDATE users SET settings = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $settings, $userId);
    
    if ($stmt->execute()) {
        sendJsonResponse('success', null, 'Settings saved successfully');
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Save settings error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to save settings');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
