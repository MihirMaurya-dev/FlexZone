<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';
$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse('error', null, 'Database connection failed');
}
$input = file_get_contents("php://input");
$data = json_decode($input, true);
if ($data === null) {
    sendJsonResponse('error', null, 'Invalid JSON data');
}
$duration = isset($data['duration']) ? sanitizeInput($data['duration'], 'int') : null;
$calories = isset($data['calories']) ? sanitizeInput($data['calories'], 'int') : 0;
$workoutName = isset($data['name']) && !empty(trim($data['name'])) ? 
               sanitizeInput($data['name']) : 'General Workout';
if (!$duration || $duration <= 0) {
    sendJsonResponse('error', null, 'Invalid workout duration');
}
if ($duration > 86400) { 
    sendJsonResponse('error', null, 'Workout duration too long');
}
if ($calories < 0 || $calories > 10000) {
    sendJsonResponse('error', null, 'Invalid calories value');
}
$userId = getCurrentUserId();
try {
    $sql = "INSERT INTO workout_log (user_id, workout_name, duration_seconds, calories_burned, log_date) 
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Save workout prepare failed: " . $conn->error);
        sendJsonResponse('error', null, 'Failed to save workout');
    }
    $stmt->bind_param("isii", $userId, $workoutName, $duration, $calories);
    if ($stmt->execute()) {
        $workoutId = $stmt->insert_id;
        $stmt->close();
        try {
            $statsSql = "SELECT streak_current, streak_max, total_workouts, 
                        (SELECT DATE(log_date) FROM workout_log WHERE user_id = ? AND log_id != ? ORDER BY log_date DESC LIMIT 1) as last_date 
                        FROM users WHERE id = ?";
            $statsStmt = $conn->prepare($statsSql);
            $statsStmt->bind_param("iii", $userId, $workoutId, $userId);
            $statsStmt->execute();
            $statsResult = $statsStmt->get_result();
            if ($statsRow = $statsResult->fetch_assoc()) {
                $currentStreak = (int)$statsRow['streak_current'];
                $maxStreak = (int)$statsRow['streak_max'];
                $totalWorkouts = (int)$statsRow['total_workouts'] + 1;
                $lastDate = $statsRow['last_date'];
                $today = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                $newStreak = $currentStreak;
                if (!$lastDate) {
                    $newStreak = 1;
                } else if ($lastDate === $yesterday) {
                    $newStreak = $currentStreak + 1;
                } else if ($lastDate === $today) {
                    $newStreak = $currentStreak;
                } else {
                    $newStreak = 1;
                }
                $newMaxStreak = max($newStreak, $maxStreak);
                $updateStatsSql = "UPDATE users SET streak_current = ?, streak_max = ?, total_workouts = ? WHERE id = ?";
                $upStmt = $conn->prepare($updateStatsSql);
                if ($upStmt) {
                    $upStmt->bind_param("iiii", $newStreak, $newMaxStreak, $totalWorkouts, $userId);
                    $upStmt->execute();
                    $upStmt->close();
                }
                $badgesToAward = [];
                if ($totalWorkouts === 1) {
                    $badgesToAward[] = 'first_workout';
                }
                if ($newStreak >= 3) {
                    $badgesToAward[] = 'streak_3';
                }
                if ($totalWorkouts >= 100) {
                    $badgesToAward[] = 'workouts_100';
                }
                if ((int)date('H') < 8) {
                    $badgesToAward[] = 'early_bird';
                }
                foreach ($badgesToAward as $badge) {
                    awardBadge($conn, $userId, $badge);
                }
            }
            $statsStmt->close();
        } catch (Exception $e) {
            error_log("Streak update failed: " . $e->getMessage());
        }
        sendJsonResponse('success', [
            'log_id' => $workoutId,
            'message' => 'Workout saved successfully!'
        ]);
    } else {
        error_log("Save workout execute failed: " . $stmt->error);
        $stmt->close();
        sendJsonResponse('error', null, 'Failed to save workout');
    }
} catch (Exception $e) {
    error_log("Save workout error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to save workout');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
function awardBadge(mysqli $conn, int $userId, string $badgeType): void {
    $checkSql = "SELECT 1 FROM user_badges WHERE user_id = ? AND badge_type = ?";
    $stmt = $conn->prepare($checkSql);
    if ($stmt) {
        $stmt->bind_param("is", $userId, $badgeType);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) {
            $stmt->close();
            $insertSql = "INSERT INTO user_badges (user_id, badge_type) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            if ($insertStmt) {
                $insertStmt->bind_param("is", $userId, $badgeType);
                $insertStmt->execute();
                $insertStmt->close();
            }
        } else {
            $stmt->close();
        }
    }
}
?>