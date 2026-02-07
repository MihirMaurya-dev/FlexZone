<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';
$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse('error', null, 'Database connection failed');
}
$userId = getCurrentUserId();
$startDate = isset($_GET['start_date']) ? sanitizeInput($_GET['start_date']) : null;
$endDate = isset($_GET['end_date']) ? sanitizeInput($_GET['end_date']) : null;
try {
    $sql = "SELECT weight_kg, log_date 
            FROM weight_log 
            WHERE user_id = ?";
    $params = [$userId];
    $types = "i";
    if ($startDate) {
        $sql .= " AND log_date >= ?";
        $params[] = $startDate;
        $types .= "s";
    }
    if ($endDate) {
        $sql .= " AND log_date <= ?";
        $params[] = $endDate;
        $types .= "s";
    }
    $sql .= " ORDER BY log_date ASC";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Get weight history prepare failed: " . $conn->error);
        sendJsonResponse('error', null, 'Failed to retrieve weight history');
    }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = [
            'weight_kg' => (float)$row['weight_kg'],
            'log_date' => $row['log_date']
        ];
    }
    $stmt->close();
    sendJsonResponse('success', ['history' => $history]);
} catch (Exception $e) {
    error_log("Get weight history error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to retrieve weight history');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>