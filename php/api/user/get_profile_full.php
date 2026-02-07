<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';
$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse('error', null, 'Database connection failed');
}
$userId = getCurrentUserId();
$response = [
    'user' => [],
    'stats' => [],
    'badges' => [],
    'garage' => [],
    'settings' => [],
    'activity' => []
];
try {
    $sql = "SELECT username, email, avatar, garage, settings, streak_current, total_workouts FROM users WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $userData = $userResult->fetch_assoc();
    $stmt->close();
    if ($userData) {
        $response['user'] = [
            'username' => $userData['username'],
            'email' => $userData['email'],
            'avatar' => isset($userData['avatar']) ? $userData['avatar'] : 'default_avatar.png'
        ];
        $response['stats'] = [
            'streak_current' => isset($userData['streak_current']) ? (int)$userData['streak_current'] : 0,
            'total_workouts' => isset($userData['total_workouts']) ? (int)$userData['total_workouts'] : 0,
            'last_workout' => '--'
        ];
        $response['garage'] = isset($userData['garage']) ? json_decode($userData['garage']) : [];
        $response['settings'] = isset($userData['settings']) ? json_decode($userData['settings']) : ['units' => 'kg'];
    }
    $lastWorkoutSql = "SELECT log_date FROM workout_log WHERE user_id = ? ORDER BY log_date DESC LIMIT 1";
    $lwStmt = $conn->prepare($lastWorkoutSql);
    $lwStmt->bind_param("i", $userId);
    $lwStmt->execute();
    $lwResult = $lwStmt->get_result();
    if ($row = $lwResult->fetch_assoc()) {
        $response['stats']['last_workout'] = date('M j, Y', strtotime($row['log_date']));
    }
    $lwStmt->close();
    $weightSql = "SELECT weight_kg, log_date FROM weight_log WHERE user_id = ? ORDER BY log_date DESC LIMIT 30";
    $wStmt = $conn->prepare($weightSql);
    $wStmt->bind_param("i", $userId);
    $wStmt->execute();
    $wResult = $wStmt->get_result();
    $weights = [];
    while ($row = $wResult->fetch_assoc()) {
        $weights[] = $row;
    }
    $response['weight_history'] = array_reverse($weights);
    $wStmt->close();
    $activitySql = "
        SELECT DATE(log_date) as date, COUNT(*) as count 
        FROM workout_log 
        WHERE user_id = ? AND log_date > DATE_SUB(NOW(), INTERVAL 1 YEAR)
        GROUP BY DATE(log_date)";
    try {
        $actStmt = $conn->prepare($activitySql);
        if ($actStmt) {
            $actStmt->bind_param("i", $userId);
            $actStmt->execute();
            $actResult = $actStmt->get_result();
            while ($row = $actResult->fetch_assoc()) {
                $response['activity'][$row['date']] = (int)$row['count'];
            }
            $actStmt->close();
        }
    } catch (Exception $e) {
    }
    $response['badges'] = [];
    $badgeSql = "SELECT b.name, b.icon, (ub.id IS NOT NULL) as unlocked 
                 FROM badges b 
                 LEFT JOIN user_badges ub ON b.badge_type = ub.badge_type AND ub.user_id = ?
                 ORDER BY b.id ASC";
    $bStmt = $conn->prepare($badgeSql);
    if ($bStmt) {
        $bStmt->bind_param("i", $userId);
        $bStmt->execute();
        $bResult = $bStmt->get_result();
        while ($row = $bResult->fetch_assoc()) {
            $row['unlocked'] = (bool)$row['unlocked'];
            $response['badges'][] = $row;
        }
        $bStmt->close();
    }
    sendJsonResponse('success', $response);
} catch (Exception $e) {
    error_log("Get full profile error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to load profile data');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>