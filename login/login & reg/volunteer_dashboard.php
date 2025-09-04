<?php
session_start();
include("db_connection.php");

// Check if user is logged in
if (!isset($_SESSION['volunteer_id'])) {
    header("Location: log.php");
    exit();
}

// Get volunteer details
$vol_id = $_SESSION['volunteer_id'];
$query = "SELECT * FROM volunteer WHERE volunteer_id='$vol_id'";
$result = $conn->query($query);
$volunteer = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard - Volunteer Management Platform</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div style="padding: 40px; text-align: center;">
            <h1>Welcome, <?php echo $volunteer['first_name']; ?>!</h1>
            <br>
            <p><strong>Email:</strong> <?php echo $volunteer['email']; ?></p>
            <p><strong>Interest:</strong> <?php echo $volunteer['interest']; ?></p>
            <p><strong>Phone:</strong> <?php echo $volunteer['phone']; ?></p>
            <br>
            <div style="margin-top: 30px;">
                <button class="btn" onclick="location.href='events.php'" style="width: 200px; margin: 10px;">View Events</button><br>
                <button class="btn" onclick="location.href='edit.php'" style="width: 200px; margin: 10px;">My Profile</button><br>
                <button class="btn" onclick="location.href='logout.php'" style="width: 200px; margin: 10px; background: #dc3545;">Logout</button>
            </div>
        </div>
    </div>
</body>
</html>