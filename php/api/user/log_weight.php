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
$weight = isset($_POST['weight_kg']) ? sanitizeInput($_POST['weight_kg'], 'float') : null;
if (!$weight || $weight <= 0) {
    sendJsonResponse('error', null, 'Invalid weight value');
}
if ($weight < 20 || $weight > 500) {
    sendJsonResponse('error', null, 'Weight must be between 20 and 500 kg');
}
$userId = getCurrentUserId();
$date = date('Y-m-d');
try {
    $checkSql = "SELECT log_id FROM weight_log WHERE user_id = ? AND DATE(log_date) = ? LIMIT 1";
    $checkStmt = $conn->prepare($checkSql);
    if ($checkStmt === false) {
        error_log("Log weight check prepare failed: " . $conn->error);
        sendJsonResponse('error', null, 'Failed to log weight');
    }
    $checkStmt->bind_param("is", $userId, $date);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $action = '';
    if ($checkResult->num_rows > 0) {
        $row = $checkResult->fetch_assoc();
        $logId = $row['log_id'];
        $checkStmt->close();
        $updateSql = "UPDATE weight_log SET weight_kg = ? WHERE log_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        if ($updateStmt === false) {
            error_log("Log weight update prepare failed: " . $conn->error);
            sendJsonResponse('error', null, 'Failed to update weight');
        }
        $updateStmt->bind_param("di", $weight, $logId);
        if ($updateStmt->execute()) {
            $updateStmt->close();
            $action = 'updated';
        } else {
            error_log("Log weight update execute failed: " . $updateStmt->error);
            $updateStmt->close();
            sendJsonResponse('error', null, 'Failed to update weight');
        }
    } else {
        $checkStmt->close();
        $insertSql = "INSERT INTO weight_log (user_id, weight_kg, log_date) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        if ($insertStmt === false) {
            error_log("Log weight insert prepare failed: " . $conn->error);
            sendJsonResponse('error', null, 'Failed to log weight');
        }
        $insertStmt->bind_param("ids", $userId, $weight, $date);
        if ($insertStmt->execute()) {
            $insertStmt->close();
            $action = 'logged';
        } else {
            error_log("Log weight insert execute failed: " . $insertStmt->error);
            $insertStmt->close();
            sendJsonResponse('error', null, 'Failed to log weight');
        }
    }
    $updateUserSql = "UPDATE users SET weight_kg = ? WHERE id = ?";
    $updateUserStmt = $conn->prepare($updateUserSql);
    if ($updateUserStmt !== false) {
        $updateUserStmt->bind_param("di", $weight, $userId);
        $updateUserStmt->execute();
        $updateUserStmt->close();
    }
    sendJsonResponse('success', [
        'weight_kg' => $weight,
        'log_date' => $date
    ], "Weight $action successfully!");
} catch (Exception $e) {
    error_log("Log weight error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to log weight');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>