<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';
$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse('error', null, 'Database connection failed');
}
$userId = getCurrentUserId();
try {
    $totalSql = "SELECT 
                    COALESCE(SUM(duration_seconds), 0) as total_duration,
                    COALESCE(SUM(calories_burned), 0) as total_calories,
                    COUNT(log_id) as total_workouts
                 FROM workout_log
                 WHERE user_id = ?";
    $stmt = $conn->prepare($totalSql);
    if ($stmt === false) {
        error_log("Get stats prepare failed: " . $conn->error);
        sendJsonResponse('error', null, 'Failed to retrieve statistics');
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalStats = $result->fetch_assoc();
    $stmt->close();
    $userSql = "SELECT activity_level FROM users WHERE id = ?";
    $stmt = $conn->prepare($userSql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $userRes = $stmt->get_result();
    $userData = $userRes->fetch_assoc();
    $stmt->close();
    $activityLevel = $userData['activity_level'] ?? 'moderate';
    $goals = [
        'sedentary' => 3,
        'light' => 3,
        'moderate' => 5,
        'active' => 6
    ];
    $weeklyGoal = $goals[strtolower($activityLevel)] ?? 5;
    $weeklySql = "SELECT COUNT(log_id) as workouts_this_week
                  FROM workout_log
                  WHERE user_id = ?
                  AND YEARWEEK(log_date, 1) = YEARWEEK(CURDATE(), 1)";
    $stmt = $conn->prepare($weeklySql);
    if ($stmt === false) {
        error_log("Get weekly stats prepare failed: " . $conn->error);
        $weeklyStats = ['workouts_this_week' => 0];
    } else {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $weeklyStats = $result->fetch_assoc();
        $stmt->close();
    }
    $dailyStats = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dailyStats[$date] = [
            'date' => $date,
            'duration' => 0
        ];
    }
    $dailySql = "SELECT DATE(log_date) as date, 
                        SUM(duration_seconds) as duration
                 FROM workout_log
                 WHERE user_id = ? 
                 AND log_date >= CURDATE() - INTERVAL 6 DAY
                 GROUP BY DATE(log_date)
                 ORDER BY DATE(log_date) ASC";
    $stmt = $conn->prepare($dailySql);
    if ($stmt === false) {
        error_log("Get daily stats prepare failed: " . $conn->error);
    } else {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if (isset($dailyStats[$row['date']])) {
                $dailyStats[$row['date']]['duration'] = (int)$row['duration'];
            }
        }
        $stmt->close();
    }
    $stats = [
        'total_duration' => (int)($totalStats['total_duration'] ?? 0),
        'total_calories' => (int)($totalStats['total_calories'] ?? 0),
        'total_workouts' => (int)($totalStats['total_workouts'] ?? 0),
        'workouts_this_week' => (int)($weeklyStats['workouts_this_week'] ?? 0),
        'weekly_goal' => $weeklyGoal,
        'last_7_days' => array_values($dailyStats)
    ];
    sendJsonResponse('success', ['stats' => $stats]);
} catch (Exception $e) {
    error_log("Get user stats error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to retrieve statistics');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>