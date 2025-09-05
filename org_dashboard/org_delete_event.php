<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header("Location: log.php");
    exit();
}
$org_id = $_SESSION['org_id'];
include "db_connection.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM Event WHERE event_id=? AND org_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $org_id);
    if($stmt->execute()){
        $_SESSION['success'] = "Event deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting event.";
    }
}
header("Location: org_dashboard.php");
exit();
?>
