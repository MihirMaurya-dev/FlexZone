<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';

$conn = getVerifiedConnection();
$userId = getRequiredUserId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse('error', null, 'Invalid request method');
}

// Data mapping from onboarding and profile forms
$height = isset($_POST['height_cm']) ? sanitizeInput($_POST['height_cm'], 'float') : null;
$weight = isset($_POST['weight_kg']) ? sanitizeInput($_POST['weight_kg'], 'float') : null;
$age = isset($_POST['age']) ? sanitizeInput($_POST['age'], 'int') : null;
$goal = isset($_POST['goal']) ? sanitizeInput($_POST['goal']) : null;
$gender = isset($_POST['gender']) ? sanitizeInput($_POST['gender']) : null;
$activityLevel = isset($_POST['activity']) ? sanitizeInput($_POST['activity']) : null;

// Validations
if ($height !== null && ($height < 50 || $height > 250)) {
    sendJsonResponse('error', null, 'Invalid height');
}
if ($weight !== null && ($weight < 20 || $weight > 300)) {
    sendJsonResponse('error', null, 'Invalid weight');
}

try {
    // If age is provided but DOB isn't in DB, we can estimate DOB or just ignore if schema only has DOB
    // For now, let's update what we have in the schema
    $sql = "UPDATE users SET 
                height_cm = COALESCE(?, height_cm), 
                weight_kg = COALESCE(?, weight_kg), 
                fitness_goal = COALESCE(?, fitness_goal), 
                gender = COALESCE(?, gender), 
                activity_level = COALESCE(?, activity_level) 
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddsssi", $height, $weight, $goal, $gender, $activityLevel, $userId);
    
    if ($stmt->execute()) {
        $stmt->close();
        
        // Also log weight if provided
        if ($weight) {
            $wSql = "INSERT INTO weight_log (user_id, weight_kg) VALUES (?, ?)";
            $wStmt = $conn->prepare($wSql);
            $wStmt->bind_param("id", $userId, $weight);
            $wStmt->execute();
            $wStmt->close();
        }
        
        sendJsonResponse('success', null, 'Profile updated successfully');
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }
} catch (Exception $e) {
    error_log("Update profile error: " . $e->getMessage());
    sendJsonResponse('error', null, 'Failed to update profile');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
