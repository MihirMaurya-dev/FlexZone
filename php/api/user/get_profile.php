<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';
$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse('error', null, 'Database connection failed');
}
$userId = getCurrentUserId();
try {
    $sql = "SELECT height_cm, weight_kg, dob, fitness_goal, created_at 
            FROM users 
            WHERE id = ? 
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Get profile prepare failed: " . $conn->error);
        sendJsonResponse('error', null, 'Failed to retrieve profile');
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $stmt->close();
        sendJsonResponse('error', null, 'Profile not found');
    }
    $profile = $result->fetch_assoc();
    $stmt->close();
    $profile['height_cm'] = $profile['height_cm'] ? (float)$profile['height_cm'] : null;
    $profile['weight_kg'] = $profile['weight_kg'] ? (float)$profile['weight_kg'] : null;
    $profile['dob'] = $profile['dob'] ?: null;
    sendJsonResponse('success', ['profile' => $profile]);
} catch (Exception $e) {
    error_log("Get profile error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to retrieve profile');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>