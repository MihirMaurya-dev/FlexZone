<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data === null) {
    sendJsonResponse('error', null, 'Invalid JSON data');
}

$duration = isset($data['duration']) ? sanitizeInput($data['duration'], 'int') : null;
$calories = isset($data['calories']) ? sanitizeInput($data['calories'], 'int') : 0;
$workoutName = !empty($data['name']) ? sanitizeInput($data['name']) : 'General Workout';

if (!$duration || $duration <= 0) sendJsonResponse('error', null, 'Invalid duration');

try {
    $sql = "INSERT INTO workout_log (user_id, workout_name, duration_seconds, calories_burned, log_date) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $userId, $workoutName, $duration, $calories);
    
    if ($stmt->execute()) {
        $workoutId = $stmt->insert_id;
        $stmt->close();

        // Update Streaks and Award Badges
        try {
            $statsSql = "SELECT streak_current, streak_max, total_workouts, 
                        (SELECT DATE(log_date) FROM workout_log WHERE user_id = ? AND log_id != ? ORDER BY log_date DESC LIMIT 1) as last_date 
                        FROM users WHERE id = ?";
            $statsStmt = $conn->prepare($statsSql);
            $statsStmt->bind_param("iii", $userId, $workoutId, $userId);
            $statsStmt->execute();
            $statsRow = $statsStmt->get_result()->fetch_assoc();
            
            if ($statsRow) {
                $currentStreak = (int)$statsRow['streak_current'];
                $totalWorkouts = (int)$statsRow['total_workouts'] + 1;
                $lastDate = $statsRow['last_date'];
                
                $today = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                
                $newStreak = ($lastDate === $yesterday) ? $currentStreak + 1 : ($lastDate === $today ? $currentStreak : 1);
                $newMaxStreak = max($newStreak, (int)$statsRow['streak_max']);

                $conn->query("UPDATE users SET streak_current = $newStreak, streak_max = $newMaxStreak, total_workouts = $totalWorkouts WHERE id = $userId");

                // Badge Logic
                if ($totalWorkouts === 1) awardBadge($conn, $userId, 'first_workout');
                if ($newStreak >= 3) awardBadge($conn, $userId, 'streak_3');
                if ($totalWorkouts >= 100) awardBadge($conn, $userId, 'workouts_100');
                if ((int)date('H') < 8) awardBadge($conn, $userId, 'early_bird');
            }
            $statsStmt->close();
        } catch (Exception $streakEx) {
            error_log("Streak Error: " . $streakEx->getMessage());
        }

        sendJsonResponse('success', ['log_id' => $workoutId], 'Workout saved!');
    } else {
        throw new Exception("Insert failed: " . $stmt->error);
    }
} catch (Exception $e) {
    error_log("Save workout error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to save workout');
} finally {
    if (isset($conn)) $conn->close();
}

function awardBadge(mysqli $conn, int $userId, string $badgeType): void {
    $stmt = $conn->prepare("SELECT 1 FROM user_badges WHERE user_id = ? AND badge_type = ?");
    $stmt->bind_param("is", $userId, $badgeType);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        $ins = $conn->prepare("INSERT INTO user_badges (user_id, badge_type) VALUES (?, ?)");
        $ins->bind_param("is", $userId, $badgeType);
        $ins->execute();
        $ins->close();
    }
    $stmt->close();
}
?>
