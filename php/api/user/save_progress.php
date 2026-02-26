<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse('error', null, 'Invalid request method');
}

$logDate = sanitizeInput($_POST['log_date'] ?? date('Y-m-d'));
$weight = sanitizeInput($_POST['weight_kg'] ?? null, 'float');
$chest = sanitizeInput($_POST['chest_cm'] ?? null, 'float');
$waist = sanitizeInput($_POST['waist_cm'] ?? null, 'float');
$arms = sanitizeInput($_POST['arms_cm'] ?? null, 'float');
$thighs = sanitizeInput($_POST['thighs_cm'] ?? null, 'float');
$notes = sanitizeInput($_POST['notes'] ?? '');

$uploadDir = __DIR__ . '/../../../assets/progress_photos/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
$uploadedFiles = ['front' => null, 'side' => null, 'back' => null];

foreach (['photo_front', 'photo_side', 'photo_back'] as $key) {
    $shortKey = str_replace('photo_', '', $key);
    if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
        $fileType = $_FILES[$key]['type'];
        if (!in_array($fileType, $allowedTypes)) continue;

        $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
        $newFileName = $userId . '_' . $logDate . '_' . $shortKey . '.' . $ext;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES[$key]['tmp_name'], $destPath)) {
            $uploadedFiles[$shortKey] = 'assets/progress_photos/' . $newFileName;
        }
    }
}

try {
    $sql = "INSERT INTO body_measurements 
            (user_id, log_date, weight_kg, chest_cm, waist_cm, arms_cm, thighs_cm, photo_front, photo_side, photo_back, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isdddddssss", 
        $userId, $logDate, $weight, $chest, $waist, $arms, $thighs, 
        $uploadedFiles['front'], $uploadedFiles['side'], $uploadedFiles['back'], $notes
    );

    if ($stmt->execute()) {
        if ($weight) {
            // Sync with weight logs
            $wStmt = $conn->prepare("INSERT INTO weight_log (user_id, weight_kg, log_date) VALUES (?, ?, ?)");
            $wStmt->bind_param("ids", $userId, $weight, $logDate);
            $wStmt->execute();
            
            $uStmt = $conn->prepare("UPDATE users SET weight_kg = ? WHERE id = ?");
            $uStmt->bind_param("di", $weight, $userId);
            $uStmt->execute();
        }
        sendJsonResponse('success', null, 'Progress saved successfully!');
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }
} catch (Exception $e) {
    error_log("Save progress error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to save progress entry');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
