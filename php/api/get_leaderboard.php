<?php
define('FLEXZONE_APP', true);
require_once '../config/db_connection.php';

$conn = getVerifiedConnection();
$limit = isset($_GET['limit']) ? max(1, min(100, sanitizeInput($_GET['limit'], 'int'))) : 50;
$timeFrame = isset($_GET['timeframe']) ? sanitizeInput($_GET['timeframe']) : 'all_time';

$dateConditionWl = "";
$dateConditionLog = "";

switch ($timeFrame) {
    case 'week':
        $dateConditionWl = " AND wl.log_date >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)";
        $dateConditionLog = " AND log_date >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)";
        break;
    case 'month':
        $dateConditionWl = " AND wl.log_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')";
        $dateConditionLog = " AND log_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')";
        break;
    case 'year':
        $dateConditionWl = " AND wl.log_date >= DATE_FORMAT(CURDATE(), '%Y-01-01')";
        $dateConditionLog = " AND log_date >= DATE_FORMAT(CURDATE(), '%Y-01-01')";
        break;
    case 'all_time':
    default:
        break;
}

try {
    $sql = "SELECT 
                u.username,
                SUM(wl.duration_seconds) AS total_duration,
                COUNT(wl.log_id) AS total_workouts,
                SUM(wl.calories_burned) AS total_calories
            FROM users u
            INNER JOIN workout_log wl ON u.id = wl.user_id
            WHERE wl.duration_seconds > 0" . $dateConditionWl . "
            GROUP BY u.id, u.username
            HAVING total_duration > 0
            ORDER BY total_duration DESC
            LIMIT ?";
            
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
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
                        WHERE wl.duration_seconds > 0" . $dateConditionWl . "
                        GROUP BY u.id
                        HAVING total_duration > (
                            SELECT COALESCE(SUM(duration_seconds), 0)
                            FROM workout_log
                            WHERE user_id = ?" . $dateConditionLog . "
                        )
                    ) as ranked_users";
                    
        $rankStmt = $conn->prepare($rankSql);
        if ($rankStmt !== false) {
            $rankStmt->bind_param("i", $userId);
            $rankStmt->execute();
            $rankResult = $rankStmt->get_result();
            if ($rankData = $rankResult->fetch_assoc()) {
                $userRank = (int)$rankData['user_rank'];
            }
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
