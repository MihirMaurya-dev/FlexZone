<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="workout_history.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Date', 'Workout Name', 'Duration (Seconds)', 'Calories Burned']);

try {
    $sql = "SELECT log_date, workout_name, duration_seconds, calories_burned 
            FROM workout_log 
            WHERE user_id = ? 
            ORDER BY log_date DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['log_date'],
            $row['workout_name'],
            $row['duration_seconds'],
            $row['calories_burned']
        ]);
    }
} catch (Exception $e) {
    error_log("Export history error: " . $e->getMessage());
} finally {
    fclose($output);
    if (isset($conn)) {
        $conn->close();
    }
}
exit;
?>
