<?php
session_start();
include "db_connection.php";

// Access control
if (!isset($_SESSION['admin_id'])) {
    header("Location: log.php"); // redirect to login if not admin
    exit();
}

// Handle Delete
if (isset($_POST['delete_user'])) {
    $type = $_POST['user_type'];
    $id = intval($_POST['user_id']);
    if ($type === "volunteer") {
        $conn->query("DELETE FROM Volunteer WHERE volunteer_id = $id");
    } elseif ($type === "organization") {
        $conn->query("DELETE FROM Organization WHERE org_id = $id");
    }
}

// Handle Update
if (isset($_POST['update_user'])) {
    $type = $_POST['user_type'];
    $id = intval($_POST['user_id']);
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    if ($type === "volunteer") {
        $conn->query("UPDATE Volunteer SET email='$email', phone='$phone' WHERE volunteer_id=$id");
    } elseif ($type === "organization") {
        $conn->query("UPDATE Organization SET contact_person_email='$email', contact_person_phone='$phone' WHERE org_id=$id");
    }
}

// Handle Search
$search_result = null;
if (isset($_POST['search_user'])) {
    $email = $_POST['search_email'];
    $search_result = $conn->query("
        SELECT 'Volunteer' as type, volunteer_id as id, first_name as name, email, phone 
        FROM Volunteer WHERE email LIKE '%$email%'
        UNION
        SELECT 'Organization' as type, org_id as id, org_name as name, contact_person_email as email, contact_person_phone as phone 
        FROM Organization WHERE contact_person_email LIKE '%$email%'
    ");
}

// Reports
$report_result = null;
if (isset($_POST['report_org'])) {
    $org_id = intval($_POST['org_id']);
    $report_result = $conn->query("SELECT COUNT(*) as total_events FROM Event WHERE org_id=$org_id");
}
if (isset($_POST['report_volunteer'])) {
    $volunteer_id = intval($_POST['volunteer_id']);
    $report_result = $conn->query("SELECT COUNT(*) as total_participations FROM event_assignment WHERE volunteer_id=$volunteer_id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Volunteer Coordination Platform</title>
   <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        h2 { color: #333; }
        .card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        input, button { padding: 8px; margin: 5px 0; }
        button { cursor: pointer; background: #007BFF; color: white; border: none; border-radius: 4px; }
        button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #007BFF; color: white; }
    </style> 
</head>
<body>
    <h1>Admin Panel</h1>

    <!-- Search Section -->
    <div class="card">
        <h2>Search User by Email</h2>
        <form method="post">
            <input type="text" name="search_email" placeholder="Enter email">
            <button type="submit" name="search_user">Search</button>
        </form>
        <?php if ($search_result && $search_result->num_rows > 0): ?>
            <table>
                <tr><th>Type</th><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr>
                <?php while($row = $search_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['type'] ?></td>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['phone'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>
    </div>

    <!-- Update/Delete Section -->
    <div class="card">
        <h2>Update/Delete User</h2>
        <form method="post">
            <select name="user_type">
                <option value="volunteer">Volunteer</option>
                <option value="organization">Organization</option>
            </select><br>
            <input type="number" name="user_id" placeholder="User ID" required><br>
            <input type="email" name="email" placeholder="New Email"><br>
            <input type="text" name="phone" placeholder="New Phone"><br>
            <button type="submit" name="update_user">Update User</button>
            <button type="submit" name="delete_user">Delete User</button>
        </form>
    </div>

    <!-- Reports Section -->
    <div class="card">
        <h2>Reports</h2>
        <form method="post">
            <input type="number" name="org_id" placeholder="Organization ID">
            <button type="submit" name="report_org">Get Org Events</button>
        </form>
        <form method="post">
            <input type="number" name="volunteer_id" placeholder="Volunteer ID">
            <button type="submit" name="report_volunteer">Get Volunteer Participation</button>
        </form>
        <?php if ($report_result && $report_result->num_rows > 0): 
            $row = $report_result->fetch_assoc(); ?>
            <p><b>Result:</b> <?= implode(" - ", $row) ?></p>
        <?php endif; ?>
        <div style="padding: 40px; text-align: center;">
                <div style="margin-top: 30px;">
                <button class="btn" onclick="location.href='logout.php'" style="width: 200px; margin: 10px; background: #dc3545;">Logout</button>
            </div>
        </div>
    </div>
</body>
</html>
