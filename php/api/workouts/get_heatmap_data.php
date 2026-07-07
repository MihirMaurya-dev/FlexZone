<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

$months = isset($_GET['months']) ? max(1, min(12, sanitizeInput($_GET['months'], 'int'))) : 6;

try {
    // Get total duration per day for the last N months
    $sql = "SELECT DATE(log_date) as date, SUM(duration_seconds) as total_duration, COUNT(*) as workout_count 
            FROM workout_log 
            WHERE user_id = ? AND log_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
            GROUP BY DATE(log_date)
            ORDER BY DATE(log_date) ASC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $months);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $heatmap = [];
    while ($row = $result->fetch_assoc()) {
        $heatmap[$row['date']] = [
            'duration' => (int)$row['total_duration'],
            'count' => (int)$row['workout_count']
        ];
    }
    $stmt->close();

    sendJsonResponse('success', [
        'heatmap' => $heatmap,
        'months' => $months
    ]);

} catch (Exception $e) {
    error_log("Get heatmap error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to retrieve heatmap data');
} finally {
    if (isset($conn)) $conn->close();
}
?>
