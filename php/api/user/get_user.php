<?php
define('FLEXZONE_APP', true);
require_once '../../config/db_connection.php';
if (isLoggedIn()) {
    sendJsonResponse('success', [
        'userid' => $_SESSION['userid'],
        'username' => $_SESSION['username']
    ]);
} else {
    sendJsonResponse('error', null, 'No user logged in');
}
?>