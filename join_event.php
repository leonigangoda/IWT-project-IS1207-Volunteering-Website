<?php
session_start();
include("db_connection.php");

if (!isset($_SESSION['volunteer_id'])) {
    header("Location: log.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id'])) {
    $vol_id = $_SESSION['volunteer_id'];
    $event_id = $_POST['event_id'];

    // Get org_id for this event
    $org_query = "SELECT org_id FROM event WHERE event_id='$event_id'";
    $org_result = $conn->query($org_query);
    $org = $org_result->fetch_assoc();
    $org_id = $org['org_id'];
    
    // Insert into event_assignment
    $sql = "INSERT INTO event_assignment (volunteer_id, org_id, event_id, activity_status)
            VALUES ('$vol_id', '$org_id', '$event_id', 'Active')";

    if ($conn->query($sql) === TRUE) {
        header("Location: functionalities.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
