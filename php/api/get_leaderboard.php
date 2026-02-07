<?php
define('FLEXZONE_APP', true);
require_once '../config/db_connection.php';
$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse('error', null, 'Database connection failed');
}
$limit = isset($_GET['limit']) ? max(1, min(100, sanitizeInput($_GET['limit'], 'int'))) : 50;
$timeFrame = isset($_GET['timeframe']) ? sanitizeInput($_GET['timeframe']) : 'all_time';
try {
    $sql = "SELECT 
                u.username,
                SUM(wl.duration_seconds) AS total_duration,
                COUNT(wl.log_id) AS total_workouts,
                SUM(wl.calories_burned) AS total_calories
            FROM users u
            INNER JOIN workout_log wl ON u.id = wl.user_id
            WHERE wl.duration_seconds > 0";
    switch ($timeFrame) {
        case 'week':
            $sql .= " AND YEARWEEK(wl.log_date, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'month':
            $sql .= " AND YEAR(wl.log_date) = YEAR(CURDATE()) 
                     AND MONTH(wl.log_date) = MONTH(CURDATE())";
            break;
        case 'year':
            $sql .= " AND YEAR(wl.log_date) = YEAR(CURDATE())";
            break;
        case 'all_time':
        default:
            break;
    }
    $sql .= " GROUP BY u.id, u.username
             HAVING total_duration > 0
             ORDER BY total_duration DESC
             LIMIT ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Get leaderboard prepare failed: " . $conn->error);
        sendJsonResponse('error', null, 'Failed to retrieve leaderboard');
    }
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $leaderboard = [];
    $rank = 1;
    while ($row = $result->fetch_assoc()) {
        $leaderboard[] = [
            'rank' => $rank++,
            'username' => $row['username'],
            'total_duration' => (int)$row['total_duration'],
            'total_workouts' => (int)$row['total_workouts'],
            'total_calories' => (int)$row['total_calories']
        ];
    }
    $stmt->close();
    $userRank = null;
    if (isLoggedIn()) {
        $userId = getCurrentUserId();
        $rankSql = "SELECT COUNT(*) + 1 as user_rank
                    FROM (
                        SELECT u.id, SUM(wl.duration_seconds) AS total_duration
                        FROM users u
                        INNER JOIN workout_log wl ON u.id = wl.user_id
                        WHERE wl.duration_seconds > 0";
        switch ($timeFrame) {
            case 'week':
                $rankSql .= " AND YEARWEEK(wl.log_date, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $rankSql .= " AND YEAR(wl.log_date) = YEAR(CURDATE()) 
                             AND MONTH(wl.log_date) = MONTH(CURDATE())";
                break;
            case 'year':
                $rankSql .= " AND YEAR(wl.log_date) = YEAR(CURDATE())";
                break;
        }
        $rankSql .= " GROUP BY u.id
                      HAVING total_duration > (
                          SELECT COALESCE(SUM(duration_seconds), 0)
                          FROM workout_log
                          WHERE user_id = ?";
        switch ($timeFrame) {
            case 'week':
                $rankSql .= " AND YEARWEEK(log_date, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $rankSql .= " AND YEAR(log_date) = YEAR(CURDATE()) 
                             AND MONTH(log_date) = MONTH(CURDATE())";
                break;
            case 'year':
                $rankSql .= " AND YEAR(log_date) = YEAR(CURDATE())";
                break;
        }
        $rankSql .= ")) as ranked_users";
        $rankStmt = $conn->prepare($rankSql);
        if ($rankStmt !== false) {
            $rankStmt->bind_param("i", $userId);
            $rankStmt->execute();
            $rankResult = $rankStmt->get_result();
            $rankData = $rankResult->fetch_assoc();
            $userRank = (int)$rankData['user_rank'];
            $rankStmt->close();
        }
    }
    sendJsonResponse('success', [
        'leaderboard' => $leaderboard,
        'timeframe' => $timeFrame,
        'user_rank' => $userRank
    ]);
} catch (Exception $e) {
    error_log("Get leaderboard error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to retrieve leaderboard');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>