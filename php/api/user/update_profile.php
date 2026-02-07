<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';
$conn = getDbConnection();
requireLogin();
if (!$conn) {
    sendJsonResponse('error', null, 'Database connection failed');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse('error', null, 'Invalid request method');
}
$userId = getCurrentUserId();
$height = isset($_POST['height_cm']) ? sanitizeInput($_POST['height_cm'], 'float') : null;
$weight = isset($_POST['weight_kg']) ? sanitizeInput($_POST['weight_kg'], 'float') : null;
$dob = isset($_POST['dob']) && !empty($_POST['dob']) ? sanitizeInput($_POST['dob']) : null;
$goal = isset($_POST['fitness_goal']) ? sanitizeInput($_POST['fitness_goal']) : 'general_fitness';
$gender = isset($_POST['gender']) ? sanitizeInput($_POST['gender']) : null;
$activityLevel = isset($_POST['activity_level']) ? sanitizeInput($_POST['activity_level']) : null;
if ($height !== null && ($height < 50 || $height > 300)) {
    sendJsonResponse('error', null, 'Height must be between 50 and 300 cm');
}
if ($weight !== null && ($weight < 20 || $weight > 500)) {
    sendJsonResponse('error', null, 'Weight must be between 20 and 500 kg');
}
$validGoals = ['general_fitness', 'weight_loss', 'muscle_gain', 'endurance'];
if (!in_array($goal, $validGoals)) {
    $goal = 'general_fitness';
}
if ($dob !== null) {
    $dobDate = DateTime::createFromFormat('Y-m-d', $dob);
    if (!$dobDate || $dobDate->format('Y-m-d') !== $dob) {
        sendJsonResponse('error', null, 'Invalid date format');
    }
    if ($dobDate > new DateTime()) {
        sendJsonResponse('error', null, 'Date of birth cannot be in the future');
    }
    $minDate = new DateTime('-120 years');
    if ($dobDate < $minDate) {
        sendJsonResponse('error', null, 'Invalid date of birth');
    }
}
try {
    $sql = "UPDATE users 
            SET height_cm = ?, weight_kg = ?, dob = ?, fitness_goal = ?, gender = ?, activity_level = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Update profile prepare failed: " . $conn->error);
        sendJsonResponse('error', null, 'Failed to update profile');
    }
    $stmt->bind_param("ddssssi", $height, $weight, $dob, $goal, $gender, $activityLevel, $userId);
    if ($stmt->execute()) {
        $stmt->close();
        sendJsonResponse('success', null, 'Profile updated successfully');
    } else {
        error_log("Update profile execute failed: " . $stmt->error);
        $stmt->close();
        sendJsonResponse('error', null, 'Failed to update profile');
    }
} catch (Exception $e) {
    error_log("Update profile error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to update profile');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>