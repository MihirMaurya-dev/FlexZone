<?php
define('FLEXZONE_APP', true);
require_once 'php/config/db_connection.php';

$conn = getDbConnection();
if (!$conn) {
    die("Connection failed");
}

$sql = "UPDATE badges SET icon = 'bxs-bolt' WHERE icon = 'bx-fire' OR icon = 'bx-bolt'";
if ($conn->query($sql) === TRUE) {
    echo "Database updated successfully! You can now delete this file and refresh your profile page.";
} else {
    echo "Error updating database: " . $conn->error;
}

$conn->close();
?>