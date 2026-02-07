<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';
requireLogin();
$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse('error', null, 'Database connection failed');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse('error', null, 'Invalid request method');
}
$userId = getCurrentUserId();
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
        $fileTmpPath = $_FILES[$key]['tmp_name'];
        $fileName = $_FILES[$key]['name'];
        $fileSize = $_FILES[$key]['size'];
        $fileType = $_FILES[$key]['type'];
        if (!in_array($fileType, $allowedTypes)) {
            continue;
        }
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = $userId . '_' . $logDate . '_' . $shortKey . '.' . $ext;
        $destPath = $uploadDir . $newFileName;
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $uploadedFiles[$shortKey] = 'assets/progress_photos/' . $newFileName;
        }
    }
}
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
        $wSql = "INSERT INTO weight_log (user_id, weight_kg) VALUES (?, ?)";
        $wStmt = $conn->prepare($wSql);
        $wStmt->bind_param("id", $userId, $weight);
        $wStmt->execute();
        $uSql = "UPDATE users SET weight_kg = ? WHERE id = ?";
        $uStmt = $conn->prepare($uSql);
        $uStmt->bind_param("di", $weight, $userId);
        $uStmt->execute();
    }
    sendJsonResponse('success', null, 'Progress saved successfully!');
} else {
    sendJsonResponse('error', null, 'Database error: ' . $stmt->error);
}
?>