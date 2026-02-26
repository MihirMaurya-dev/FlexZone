<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

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

$date = date('Y-m-d');

try {
    // Check if entry already exists for today
    $checkSql = "SELECT log_id FROM weight_log WHERE user_id = ? AND DATE(log_date) = ? LIMIT 1";
    $checkStmt = $conn->prepare($checkSql);
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
        $updateStmt->bind_param("di", $weight, $logId);
        if ($updateStmt->execute()) {
            $updateStmt->close();
            $action = 'updated';
        } else {
            throw new Exception("Update weight failed");
        }
    } else {
        $checkStmt->close();
        $insertSql = "INSERT INTO weight_log (user_id, weight_kg, log_date) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("ids", $userId, $weight, $date);
        if ($insertStmt->execute()) {
            $insertStmt->close();
            $action = 'logged';
        } else {
            throw new Exception("Insert weight failed");
        }
    }

    // Always update main user record
    $updateUserSql = "UPDATE users SET weight_kg = ? WHERE id = ?";
    $updateUserStmt = $conn->prepare($updateUserSql);
    $updateUserStmt->bind_param("di", $weight, $userId);
    $updateUserStmt->execute();
    $updateUserStmt->close();

    sendJsonResponse('success', ['weight_kg' => $weight, 'log_date' => $date], "Weight $action successfully!");

} catch (Exception $e) {
    error_log("Log weight error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to log weight');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
