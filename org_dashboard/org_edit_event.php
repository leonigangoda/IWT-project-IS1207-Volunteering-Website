<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header("Location: log.php");
    exit();
}
$org_id = $_SESSION['org_id'];
include "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['event_id'];
    $name = trim($_POST['eventName']);
    $time = trim($_POST['eventTime']);
    $venue = trim($_POST['eventVenue']);
    $category = trim($_POST['category']);
    $desc = trim($_POST['description']);

    
    //Validation
    if (empty($name) || empty($time) || empty($venue)) {
        $_SESSION['error'] = "Please fill all required fields.";
        header("Location: org_dashboard.php");
        exit();
    }
    if (!strtotime($time)) {
        $_SESSION['error'] = "Invalid date/time format.";
        header("Location: org_dashboard.php");
        exit();
    }

    // Format datetime
    $time = str_replace("T", " ", $time);
    if (strlen($time) == 16) { 
        $time .= ":00"; 
    }

    //Update query
    $sql = "UPDATE Event 
            SET event_name=?, event_time=?, event_venue=?, description=?, category=? 
            WHERE event_id=? AND org_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssii", $name, $time, $venue, $desc, $category, $id, $org_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Event updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating event.";
    }
    header("Location: org_dashboard.php");
    exit();
}
?>
