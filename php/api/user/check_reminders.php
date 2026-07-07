<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';
$conn = getVerifiedConnection();
$userId = getRequiredUserId();

// Find the last workout date
$sql = "SELECT log_date FROM workout_log WHERE user_id = ? ORDER BY log_date DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();
$conn->close();

if (!$row) {
    sendJsonResponse('success', ['remind' => true, 'days' => -1, 'message' => "Welcome! You haven't logged a workout yet. Let's get started!"]);
}

$lastDate = new DateTime($row['log_date']);
$now = new DateTime();
$diff = $now->diff($lastDate);
$days = $diff->days;

if ($days >= 3) {
    sendJsonResponse('success', ['remind' => true, 'days' => $days, 'message' => "It's been $days days since your last workout. Let's get back to it!"]);
} else {
    sendJsonResponse('success', ['remind' => false]);
}
?>
