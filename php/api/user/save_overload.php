<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';
$conn = getVerifiedConnection();
$userId = getRequiredUserId();

// Auto-create table if missing
$conn->query("CREATE TABLE IF NOT EXISTS progressive_overload (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    exercise_name VARCHAR(100) NOT NULL,
    weight DECIMAL(6,2) NOT NULL,
    reps INT NOT NULL,
    estimated_1rm DECIMAL(6,2) NOT NULL,
    log_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse('error', null, 'Invalid request method');
}

$exercise = sanitizeInput($_POST['exercise_name'] ?? '');
$weight = sanitizeInput($_POST['weight'] ?? '', 'float');
$reps = sanitizeInput($_POST['reps'] ?? '', 'int');

if (!$exercise || $weight <= 0 || $reps <= 0) {
    sendJsonResponse('error', null, 'Invalid input. Weight and Reps must be greater than 0.');
}

// Epley Formula for 1RM: w * (1 + r/30)
$estimated_1rm = $weight * (1 + ($reps / 30));

$sql = "INSERT INTO progressive_overload (user_id, exercise_name, weight, reps, estimated_1rm) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isdid", $userId, $exercise, $weight, $reps, $estimated_1rm);

if ($stmt->execute()) {
    sendJsonResponse('success', null, 'Progressive overload entry saved!');
} else {
    error_log("Save overload error: " . $stmt->error);
    sendJsonResponse('error', null, 'Database error');
}
$stmt->close();
$conn->close();
?>
