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
    $newFileName = 'avatar_' . $userId . '_' . time() . '.webp';
    $uploadDir = __DIR__ . '/../../../assets/';
    $destPath = $uploadDir . $newFileName;

    $sourcePath = $file['tmp_name'];
    $info = getimagesize($sourcePath);
    $success = false;
    
    if ($info !== false) {
        $width = $info[0];
        $height = $info[1];
        $mime = $info['mime'];
        
        $maxWidth = 400;
        $maxHeight = 400;
        
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = $ratio < 1 ? floor($width * $ratio) : $width;
        $newHeight = $ratio < 1 ? floor($height * $ratio) : $height;
        
        $srcImage = null;
        switch ($mime) {
            case 'image/jpeg': $srcImage = imagecreatefromjpeg($sourcePath); break;
            case 'image/png': $srcImage = imagecreatefrompng($sourcePath); break;
            case 'image/webp': $srcImage = imagecreatefromwebp($sourcePath); break;
            case 'image/gif': $srcImage = imagecreatefromgif($sourcePath); break;
        }
        
        if ($srcImage) {
            $destImage = imagecreatetruecolor((int)$newWidth, (int)$newHeight);
            
            if ($mime === 'image/png' || $mime === 'image/webp') {
                imagealphablending($destImage, false);
                imagesavealpha($destImage, true);
                $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
                imagefilledrectangle($destImage, 0, 0, (int)$newWidth, (int)$newHeight, $transparent);
            }
            
            imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, (int)$newWidth, (int)$newHeight, $width, $height);
            
            $success = imagewebp($destImage, $destPath, 80);
            
            imagedestroy($srcImage);
            imagedestroy($destImage);
        } else {
            $success = move_uploaded_file($sourcePath, $destPath);
        }
    } else {
        $success = move_uploaded_file($sourcePath, $destPath);
    }

    if ($success) {
        $sql = "UPDATE users SET avatar = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newFileName, $userId);
        
        if ($stmt->execute()) {
            sendJsonResponse('success', ['avatar' => $newFileName], 'Avatar updated successfully');
        } else {
            throw new Exception("Database update failed");
        }
    } else {
        throw new Exception("Failed to process and upload image");
    }
    error_log("Upload avatar error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to update avatar');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
