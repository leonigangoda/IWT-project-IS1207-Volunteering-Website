<?php
session_start();
include("db_connection.php");


if (!isset($_SESSION['volunteer_id'])) {
    $_SESSION['volunteer_id'] = 1; 
}

$vol_id = $_SESSION['volunteer_id'];

// Volunteer details
$query = "SELECT * FROM volunteer WHERE volunteer_id='$vol_id'";
$result = $conn->query($query);
$volunteer = $result->fetch_assoc();

// Available events 
$available_sql = "
    SELECT e.event_id, e.event_name, e.event_venue, e.event_time, e.description, e.category, o.org_name
    FROM event e
    JOIN organization o ON e.org_id = o.org_id
    WHERE e.event_id NOT IN (
        SELECT event_id FROM event_assignment WHERE volunteer_id='$vol_id'
    )";
$available_events = $conn->query($available_sql);

// Enrolled events
$enrolled_sql = "
    SELECT e.event_name, ea.activity_status 
    FROM event_assignment ea
    JOIN event e ON ea.event_id = e.event_id
    WHERE ea.volunteer_id='$vol_id'";
$enrolled_events = $conn->query($enrolled_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Functionalities Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>


    <section class="dashboard">
        <div class="available-events">
            <h2>Available Events</h2>
            <div class="event-list">
                <?php
                if ($available_events->num_rows > 0) {
                    while($row = $available_events->fetch_assoc()) {
                ?>
                <div class="event-card">
                    <h3><?php echo htmlspecialchars($row['event_name']); ?></h3>
                    <p><b>Organization:</b> <?php echo htmlspecialchars($row['org_name']); ?></p>
                    <p><b>Date:</b> <?php echo date('Y-m-d', strtotime($row['event_time'])); ?> | <b>Time:</b> <?php echo date('h:i A', strtotime($row['event_time'])); ?></p>
                    <p><b>Location:</b> <?php echo htmlspecialchars($row['event_venue']); ?></p>
                    <p><b>Category:</b> <?php echo htmlspecialchars($row['category']); ?></p>
                    <form method="POST" action="join_event.php">
                        <input type="hidden" name="event_id" value="<?php echo $row['event_id']; ?>">
                        <button type="submit">Join</button>
                    </form>
                </div>
                <?php
                    }
                } else {
                    echo "<p>No available projects at the moment.</p>";
                }
                ?>
            </div>
        </div>

        <div class="enrolled-events">
            <h2>Enrolled Events</h2>
            <div class="event-list">
                <?php
                if ($enrolled_events->num_rows > 0) {
                    while($row = $enrolled_events->fetch_assoc()) {
                ?>
                <div class="event-card">
                    <h3><?php echo htmlspecialchars($row['event_name']); ?></h3>
                    <p><b>Status:</b><?php echo htmlspecialchars($row['activity_status']); ?></p>
                 </div>
                <?php
                    }
                } else {
                    echo "<p>You are not enrolled in any projects yet.</p>";
                }
                ?>
            </div>
        </div>
    </section>
</body>
</html>