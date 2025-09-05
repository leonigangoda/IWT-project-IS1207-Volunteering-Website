<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header("Location: log.php");
    exit();
}

$org_id = $_SESSION['org_id'];
include "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $eventName = trim($_POST['eventName']);
    $eventTime = trim($_POST['eventTime']);
    $eventVenue = trim($_POST['eventVenue']);
    $category  = trim($_POST['category']);
    $description = trim($_POST['description']);

    // Validation
    if(empty($eventName) || empty($eventTime) || empty($eventVenue)){
        $_SESSION['error'] = "Please fill all required fields.";
        header("Location: org_dashboard.php");
        exit();
    }

    // Convert datetime-local value to MySQL DATETIME
    $eventTime = str_replace("T", " ", $eventTime) . ":00";

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO Event (org_id, event_name, event_time, event_venue, category, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $org_id, $eventName, $eventTime, $eventVenue, $category, $description);

    if($stmt->execute()){
        $_SESSION['success'] = "Event created successfully!";
    } else {
        $_SESSION['error'] = "Error creating event: ".$conn->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to dashboard
    header("Location: org_dashboard.php");
    exit();
}
?>
