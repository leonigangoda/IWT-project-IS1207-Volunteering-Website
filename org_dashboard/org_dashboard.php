<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header("Location: log.php");
    exit();
}
$org_id = $_SESSION['org_id'];

include "db_connection.php";

//Fetch events
$sql = "SELECT * FROM Event WHERE org_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $org_id);
$stmt->execute();
$result = $stmt->get_result();

//Flash message
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
    a {text-decoration: none;}
    .projects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px; }
    .project-card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; background: #fff; }
    .btn-all { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; background: lightblue; color: #000;}
    .event-all { display: none; position: fixed; z-index: 1000; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
    .event-content { background: #fff; margin: 5% auto; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px; max-height: 80vh; overflow-y: auto; }
    .close { float: right; cursor: pointer; font-size: 20px; }
    input, select, textarea { width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; }

    /* Navigation */
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #333;
      padding: 10px 20px;
    }

    nav .logo {
      color: #fff;
      font-weight: bold;
      font-size: 18px;
    }

    nav ul {
      list-style: none;
      display: flex;
      margin: 0;
      padding: 0;
    }

    nav ul li {
      margin-left: 20px;
    }

    nav ul li a {
      color: #fff;
      text-decoration: none;
      font-size: 14px;
    }

    nav ul li a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
    <nav>
        <div class="logo">Vollie</div>
        <ul>
            <li><a href="home.html">Home</a></li>
            <li><a href="explore events.html">Explore Events</a></li>
            <li><a href="org_dashboard.php">My Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
<div class="dashboard-container">
  <h2>My Events</h2>
  <?php echo $message; ?>
  <div class="projects-grid">
    <!-- Create New Event-->
    <div class="project-card" style="border:2px dashed #007bff; color:#007bff; cursor:pointer;" onclick="openCreateEvent()">
      <h3>+ Create New Event</h3>
    </div>

    <!-- Show Created Events -->
    <?php while($row = $result->fetch_assoc()) { ?>
      <div class="project-card">
        <h3><?php echo htmlspecialchars($row['event_name']); ?></h3>
        <p><strong>Date/Time:</strong> <?php echo htmlspecialchars($row['event_time']); ?></p>
        <p><strong>Venue:</strong> <?php echo htmlspecialchars($row['event_venue']); ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
        <p><?php echo htmlspecialchars($row['description']); ?></p>

        <!-- View event Details -->
        <a href="org_event_details.php?event_id=<?php echo $row['event_id']; ?>" class="btn-all" style="background: #17a2b8; color: white; margin-right: 5px;">View Details</a>
        
        <!-- Edit a Created Event -->
        <button class="btn-all" onclick="openEditEvent(
          '<?php echo $row['event_id']; ?>',
          '<?php echo htmlspecialchars($row['event_name']); ?>',
          '<?php echo date('Y-m-d\TH:i', strtotime($row['event_time'])); ?>',
          '<?php echo htmlspecialchars($row['event_venue']); ?>',
          '<?php echo htmlspecialchars($row['category']); ?>',
          '<?php echo htmlspecialchars($row['description']); ?>' )">Edit</button>

        <!-- Delete a Created Event-->
        <a href="org_delete_event.php?id=<?php echo $row['event_id']; ?>" class="btn-all" onclick="return confirm('Delete this event?')">Delete</a>
      </div>
    <?php } ?>
  </div>
</div>

<!-- Form for Create an Event -->
<div id="createEvent" class="event-all">
  <div class="event-content">
    <span class="close" onclick="closeCreateEvent()">&times;</span>
    <h2>Create a New Event</h2>
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
      <textarea name="description" maxlength="255"></textarea>
      <button type="submit" class="btn-all">Create</button>
    </form>
  </div>
</div>

<!-- Edit an Event-->
<div id="editEvent" class="event-all">
  <div class="event-content">
    <span class="close" onclick="closeEditEvent()">&times;</span>
    <h2>Edit your Event</h2>
    <form id="editForm" action="org_edit_event.php" method="POST">
      <input type="hidden" name="event_id" id="edit_event_id">

      <label>Event Name*</label>
      <input type="text" name="eventName" id="edit_eventName" required>

      <label>Date & Time*</label>
      <input type="datetime-local" name="eventTime" id="edit_eventTime" required>

      <label>Venue*</label>
      <input type="text" name="eventVenue" id="edit_eventVenue" required>

      <label>Category</label>
      <select name="category" id="edit_category">
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
      <textarea name="description" id="edit_description" maxlength="255"></textarea>

      <button type="submit" class="btn-all">Update</button>
    </form>
  </div>
</div>


<script>

//Create Event form
function openCreateEvent(){ document.getElementById("createEvent").style.display="block"; }
function closeCreateEvent(){ document.getElementById("createEvent").style.display="none"; }

//Edit Event Form
function openEditEvent(id, name, time, venue, category, desc){
  document.getElementById("edit_event_id").value = id;
  document.getElementById("edit_eventName").value = name;
  document.getElementById("edit_eventTime").value = time;
  document.getElementById("edit_eventVenue").value = venue;
  const categorySelect = document.getElementById("edit_category");
  categorySelect.value = category;
  document.getElementById("edit_description").value = desc;

  document.getElementById("editEvent").style.display = "block";
}
function closeEditEvent(){
  document.getElementById("editEvent").style.display = "none";
}

//Close forms when clicking outside of it
window.onclick = function(event) {
    const createEvent = document.getElementById("createEvent");
    const editEvent = document.getElementById("editEvent");
    
    if (event.target === createEvent) {
        createEvent.style.display = "none";
    }
    if (event.target === editEvent) {
        editEvent.style.display = "none";
    }
}
</script>
</body>
</html>
