<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data === null) {
    sendJsonResponse('error', null, 'Invalid JSON payload');
}

$hydrationData = isset($data['hydration_data']) ? json_encode($data['hydration_data']) : null;
$challengeData = isset($data['challenge_data']) ? json_encode($data['challenge_data']) : null;

try {
    if ($hydrationData !== null && $challengeData !== null) {
        $sql = "UPDATE users SET hydration_data = ?, challenge_data = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $hydrationData, $challengeData, $userId);
    } elseif ($hydrationData !== null) {
        $sql = "UPDATE users SET hydration_data = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hydrationData, $userId);
    } elseif ($challengeData !== null) {
        $sql = "UPDATE users SET challenge_data = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $challengeData, $userId);
    } else {
        sendJsonResponse('error', null, 'No valid data provided');
    }

    if ($stmt->execute()) {
        sendJsonResponse('success', null, 'Data synchronized successfully');
    } else {
        throw new Exception("Database update failed");
    }
} catch (Exception $e) {
    error_log("Sync daily data error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to synchronize data');
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>
