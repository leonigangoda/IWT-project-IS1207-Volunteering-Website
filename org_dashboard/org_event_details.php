<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header("Location: log.php");
    exit();
}
$org_id = $_SESSION['org_id'];

include "db_connection.php";

if (!isset($_GET['event_id'])) {
    header("Location: org_dashboard.php");
    exit();
}

$event_id = $_GET['event_id'];

//Event details
$event_sql = "SELECT e.*, o.org_name 
              FROM Event e 
              JOIN Organization o ON e.org_id = o.org_id 
              WHERE e.event_id = ? AND e.org_id = ?";
$event_stmt = $conn->prepare($event_sql);
$event_stmt->bind_param("ii", $event_id, $org_id);
$event_stmt->execute();
$event_result = $event_stmt->get_result();

if ($event_result->num_rows == 0) {
    header("Location: org_dashboard.php");
    exit();
}

$event = $event_result->fetch_assoc();

//Show enrolled volunteers
$volunteers_sql = "SELECT v.first_name, v.last_name, v.nic, v.phone, v.email 
                   FROM event_assignment ea 
                   JOIN Volunteer v ON ea.volunteer_id = v.volunteer_id 
                   WHERE ea.event_id = ?";
$volunteers_stmt = $conn->prepare($volunteers_sql);
$volunteers_stmt->bind_param("i", $event_id);
$volunteers_stmt->execute();
$volunteers_result = $volunteers_stmt->get_result();

//Number of Enrollments
$enrollment_count = $volunteers_result->num_rows;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .back-button {
            background-color: #6c757d;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .back-button:hover {
            background-color: #545b62;
        }
        
        .event-title {
            color: #000000;
            font-size: 28px;
            margin-bottom: 20px;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
        }
        
        .event-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .detail-row {
            margin-bottom: 15px;
            display: flex;
        }
        
        .detail-label {
            font-weight: bold;
            width: 120px;
            color: #495057;
        }
        
        .detail-value {
            color: #212529;
        }
        
        .description-section {
            margin-top: 15px;
        }
        
        .description-text {
            background-color: white;
            padding: 15px;
        }
        
        .enrollment-section {
            margin-top: 30px;
        }
        
        .enrollment-header {
            background-color: #6c757d;
            color: white;
            padding: 15px 20px;
            border-radius: 5px 5px 0 0;
            font-size: 20px;
            display: flex;
            justify-content: space-between;
        }
        
        .enrollment-count {
            padding: 5px 15px;
            font-size: 16px;
        }
        
        .volunteers-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        
        .volunteers-table th {
            background-color: #e9ecef;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #dee2e6;
        }
        
        .volunteers-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .no-volunteers {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            background-color: #f8f9fa;
            border-radius: 0 0 5px 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="org_dashboard.php" class="back-button">← Back to Dashboard</a>
        
        <h1 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h1>
        
        <div class="event-details">
            <div class="detail-row">
                <div class="detail-label">Date & Time:</div>
                <div class="detail-value"><?php echo date('F j, Y \a\t g:i A', strtotime($event['event_time'])); ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Venue:</div>
                <div class="detail-value"><?php echo htmlspecialchars($event['event_venue']); ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Category:</div>
                <div class="detail-value"><?php echo htmlspecialchars($event['category']); ?></div>
            </div>
            
            <?php if (!empty($event['description'])): ?>
            <div class="description-section">
                <div class="detail-label">Description:</div>
                <div class="description-text">
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="enrollment-section">
            <div class="enrollment-header">
                <span>Enrolled Volunteers:</span>
                <span class="enrollment-count"><?php echo $enrollment_count; ?> Enrolled</span>
            </div>
            
            <?php if ($enrollment_count > 0): ?>
                <table class="volunteers-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>NIC</th>
                            <th>Phone</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($volunteer = $volunteers_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($volunteer['first_name'] . ' ' . $volunteer['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($volunteer['nic']); ?></td>
                            <td><?php echo htmlspecialchars($volunteer['phone']); ?></td>
                            <td><?php echo htmlspecialchars($volunteer['email']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-volunteers">
                    <h3>No volunteers enrolled yet!</h3>
                    <p>This event currently has no volunteer enrollments.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>