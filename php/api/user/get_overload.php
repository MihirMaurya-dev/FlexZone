<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';
$conn = getVerifiedConnection();
$userId = getRequiredUserId();

$exercise = isset($_GET['exercise']) ? sanitizeInput($_GET['exercise']) : 'Bench Press';

$sql = "SELECT weight, reps, estimated_1rm, DATE(log_date) as log_date 
        FROM progressive_overload 
        WHERE user_id = ? AND exercise_name = ? 
        ORDER BY log_date ASC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    // If the table doesn't exist yet, just return empty history
    sendJsonResponse('success', ['history' => []]);
    exit;
}

$stmt->bind_param("is", $userId, $exercise);
$stmt->execute();
$res = $stmt->get_result();

$history = [];
while ($row = $res->fetch_assoc()) {
    $history[] = [
        'weight' => (float)$row['weight'],
        'reps' => (int)$row['reps'],
        'estimated_1rm' => (float)$row['estimated_1rm'],
        'log_date' => $row['log_date']
    ];
}
$stmt->close();
$conn->close();

sendJsonResponse('success', ['history' => $history]);
?>
