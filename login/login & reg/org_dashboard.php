<?php
session_start();
/*if (!isset($_SESSION['org_id'])) {
    header("Location: log.php");
    exit();
}*/
//$org_id = $_SESSION['org_id'];

include "db_connection.php";

// Fetch events for this organization
$sql = "SELECT * FROM Event WHERE org_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $org_id);
$stmt->execute();
$result = $stmt->get_result();

// Check for flash messages
$message = '';
if (isset($_SESSION['success'])) {
    $message = '<div style="padding:10px;background:#d4edda;color:#155724;border-radius:5px;margin-bottom:15px;">'.$_SESSION['success'].'</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    $message = '<div style="padding:10px;background:#f8d7da;color:#721c24;border-radius:5px;margin-bottom:15px;">'.$_SESSION['error'].'</div>';
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Organization Dashboard</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
    .dashboard-container { max-width: 1000px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; }
    h2 { text-align: center; margin-bottom: 20px; }
    .projects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px; }
    .project-card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; background: #fff; }
    .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; }
    .btn-primary { background: #007bff; color: #fff; }
    .btn-danger { background: #dc3545; color: #fff; }
    .btn-warning { background: #ffc107; color: #000; }
    .modal { display: none; position: fixed; z-index: 1000; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
    .modal-content { background: #fff; margin: 5% auto; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px; max-height: 80vh; overflow-y: auto; }
    .close { float: right; cursor: pointer; font-size: 20px; }
    input, select, textarea { width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; }
  </style>
</head>
<body>
<div class="dashboard-container">
  <h2>My Events</h2>
  <?php echo $message; ?>
  <div class="projects-grid">
    <!-- Create New -->
    <div class="project-card" style="border:2px dashed #007bff; color:#007bff; cursor:pointer;" onclick="openCreateModal()">
      <h3>+ Create New Event</h3>
    </div>

    <!-- Show Existing Events -->
    <?php while($row = $result->fetch_assoc()) { ?>
      <div class="project-card">
        <h3><?php echo htmlspecialchars($row['event_name']); ?></h3>
        <p><strong>Date/Time:</strong> <?php echo htmlspecialchars($row['event_time']); ?></p>
        <p><strong>Venue:</strong> <?php echo htmlspecialchars($row['event_venue']); ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
        <p><?php echo htmlspecialchars($row['description']); ?></p>
        <a href="org_edit_event.php?id=<?php echo $row['event_id']; ?>" class="btn btn-warning">Edit</a>
        <a href="org_delete_event.php?id=<?php echo $row['event_id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this event?')">Delete</a>
      </div>
    <?php } ?>
  </div>
</div>

<!-- Modal for Create -->
<div id="createModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeCreateModal()">&times;</span>
    <h2>Create Event</h2>
    <form action="org_create_event.php" method="POST">
      <label>Event Name*</label>
      <input type="text" name="eventName" required>
      <label>Date & Time*</label>
      <input type="datetime-local" name="eventTime" required>
      <label>Venue*</label>
      <input type="text" name="eventVenue" required>
      <label>Category</label>
      <select name="category">
        <option value="">Select Category</option>
        <option value="Education">Education</option>
        <option value="Environment">Environment</option>
        <option value="Healthcare">Healthcare</option>
        <option value="Community Service">Community Service</option>
        <option value="Disaster Relief">Disaster Relief</option>
        <option value="Animal Welfare">Animal Welfare</option>
        <option value="Other">Other</option>
      </select>
      <label>Description</label>
      <textarea name="description"></textarea>
      <button type="submit" class="btn btn-primary">Create</button>
    </form>
  </div>
</div>

<script>
function openCreateModal(){ document.getElementById("createModal").style.display="block"; }
function closeCreateModal(){ document.getElementById("createModal").style.display="none"; }
</script>
</body>
</html>
