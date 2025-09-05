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

    // Use a prepared statement to delete the record
    $sql = "DELETE FROM event_assignment WHERE volunteer_id = ? AND event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $vol_id, $event_id);

    if ($stmt->execute()) {
        header("Location: functionalities.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}
?>