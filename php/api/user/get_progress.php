<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

try {
    $sql = "SELECT * FROM body_measurements WHERE user_id = ? ORDER BY log_date DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    sendJsonResponse('success', ['history' => $history]);
} catch (Exception $e) {
    error_log("Get progress error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to retrieve progress data');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
