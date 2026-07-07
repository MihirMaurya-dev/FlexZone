<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

$page = isset($_GET['page']) ? max(1, sanitizeInput($_GET['page'], 'int')) : 1;
$limit = isset($_GET['limit']) ? max(1, min(100, sanitizeInput($_GET['limit'], 'int'))) : 50;
$offset = ($page - 1) * $limit;

$filterDate = !empty($_GET['date']) ? sanitizeInput($_GET['date']) : null;
$filterType = !empty($_GET['type']) ? sanitizeInput($_GET['type']) : null;
$searchQuery = !empty($_GET['search']) ? sanitizeInput($_GET['search']) : null;

try {
    $whereClauses = ["user_id = ?"];
    $params = [$userId];
    $types = "i";

    if ($filterDate) {
        $whereClauses[] = "DATE(log_date) = ?";
        $params[] = $filterDate;
        $types .= "s";
    }
    
    if ($filterType) {
        // e.g. workout_name LIKE '%beginner%'
        $whereClauses[] = "LOWER(workout_name) LIKE ?";
        $params[] = "%" . strtolower($filterType) . "%";
        $types .= "s";
    }

    if ($searchQuery) {
        $whereClauses[] = "LOWER(workout_name) LIKE ?";
        $params[] = "%" . strtolower($searchQuery) . "%";
        $types .= "s";
    }

    $whereSql = implode(" AND ", $whereClauses);

    // Total count for pagination
    $countSql = "SELECT COUNT(log_id) as total FROM workout_log WHERE $whereSql";
    $countStmt = $conn->prepare($countSql);
    $totalWorkouts = 0;
    
    if ($countStmt) {
        $countStmt->bind_param($types, ...$params);
        $countStmt->execute();
        $totalWorkouts = (int)$countStmt->get_result()->fetch_assoc()['total'];
        $countStmt->close();
    }

    $sql = "SELECT log_id, workout_name, duration_seconds, calories_burned, log_date 
            FROM workout_log 
            WHERE $whereSql 
            ORDER BY log_date DESC LIMIT ? OFFSET ?";
            
    $stmt = $conn->prepare($sql);
    
    // Add limit and offset to params
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    
    $stmt->bind_param($types, ...$params);
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
