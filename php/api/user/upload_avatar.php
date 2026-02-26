<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse('error', null, 'Invalid request method');
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    sendJsonResponse('error', null, 'No file uploaded or upload error');
}

$file = $_FILES['avatar'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

if (!in_array($file['type'], $allowedTypes)) {
    sendJsonResponse('error', null, 'Invalid file type. Allowed: JPG, PNG, WEBP, GIF');
}

if ($file['size'] > 2 * 1024 * 1024) {
    sendJsonResponse('error', null, 'File too large (Max 2MB)');
}

try {
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
    $uploadDir = __DIR__ . '/../../../assets/';
    $destPath = $uploadDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $destPath)) {
        $sql = "UPDATE users SET avatar = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newFileName, $userId);
        
        if ($stmt->execute()) {
            sendJsonResponse('success', ['avatar' => $newFileName], 'Avatar updated successfully');
        } else {
            throw new Exception("Database update failed");
        }
    } else {
        throw new Exception("Failed to move uploaded file");
    }
} catch (Exception $e) {
    error_log("Upload avatar error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to update avatar');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
