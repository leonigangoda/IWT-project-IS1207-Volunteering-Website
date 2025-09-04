<?php
session_start();
include("db_connection.php");

if (isset($_SESSION['volunteer_id'])) {
    $vol_id = $_SESSION['volunteer_id'];
    
    // Update activity status to inactive in user_login table
    $update_query = "UPDATE user_login SET activity_status='inactive' WHERE volunteer_id='$vol_id' AND activity_status='active'";
    $conn->query($update_query);
    
    // Destroy the session
    session_destroy();
}

// Redirect to login page
header("Location: log.php");
exit();
?>