<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

$page = isset($_GET['page']) ? max(1, sanitizeInput($_GET['page'], 'int')) : 1;
$limit = isset($_GET['limit']) ? max(1, min(100, sanitizeInput($_GET['limit'], 'int'))) : 50;
$offset = ($page - 1) * $limit;

try {
    // Total count for pagination
    $countSql = "SELECT COUNT(log_id) as total FROM workout_log WHERE user_id = ?";
    $countStmt = $conn->prepare($countSql);
    $totalWorkouts = 0;
    
    if ($countStmt) {
        $countStmt->bind_param("i", $userId);
        $countStmt->execute();
        $totalWorkouts = (int)$countStmt->get_result()->fetch_assoc()['total'];
        $countStmt->close();
    }

    $sql = "SELECT log_id, workout_name, duration_seconds, calories_burned, log_date 
            FROM workout_log 
            WHERE user_id = ? 
            ORDER BY log_date DESC LIMIT ? OFFSET ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $userId, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = [
            'log_id' => (int)$row['log_id'],
            'workout_name' => $row['workout_name'],
            'duration_seconds' => (int)$row['duration_seconds'],
            'calories_burned' => (int)$row['calories_burned'],
            'log_date' => $row['log_date']
        ];
    }
    $stmt->close();

    sendJsonResponse('success', [
        'history' => $history,
        'total' => $totalWorkouts,
        'page' => $page,
        'limit' => $limit
    ]);

} catch (Exception $e) {
    error_log("Get workout history error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to retrieve history');
} finally {
    if (isset($conn)) $conn->close();
}
?>
