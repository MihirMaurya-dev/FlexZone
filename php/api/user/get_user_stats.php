<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

try {
    // Combine Total and Weekly stats into one query
    $statsSql = "
        SELECT 
            COALESCE(SUM(duration_seconds), 0) as total_duration,
            COALESCE(SUM(calories_burned), 0) as total_calories,
            COUNT(log_id) as total_workouts,
            COALESCE(SUM(CASE WHEN YEARWEEK(log_date, 1) = YEARWEEK(CURDATE(), 1) THEN 1 ELSE 0 END), 0) as workouts_this_week
        FROM workout_log 
        WHERE user_id = ?
    ";
    
    $stmt = $conn->prepare($statsSql);
    if ($stmt === false) {
        throw new Exception("Stats prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $combinedStats = $result->fetch_assoc();
    $stmt->close();

    // Get activity level for weekly goal
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
        'light'     => 3,
        'moderate'  => 5,
        'active'    => 6
    ];
    $weeklyGoal = $goals[strtolower($activityLevel)] ?? 5;

    // Last 7 days activity
    $dailyStats = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dailyStats[$date] = [
            'date' => $date,
            'duration' => 0
        ];
    }

    $dailySql = "
        SELECT DATE(log_date) as date, SUM(duration_seconds) as duration 
        FROM workout_log 
        WHERE user_id = ? AND log_date >= CURDATE() - INTERVAL 6 DAY 
        GROUP BY DATE(log_date) 
        ORDER BY DATE(log_date) ASC
    ";
    
    $stmt = $conn->prepare($dailySql);
    if ($stmt) {
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
        'total_duration' => (int)($combinedStats['total_duration'] ?? 0),
        'total_calories' => (int)($combinedStats['total_calories'] ?? 0),
        'total_workouts' => (int)($combinedStats['total_workouts'] ?? 0),
        'workouts_this_week' => (int)($combinedStats['workouts_this_week'] ?? 0),
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
