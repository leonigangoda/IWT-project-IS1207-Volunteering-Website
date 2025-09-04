<?php
session_start();
include("db_connection.php");

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Build query based on role
    if ($role === "volunteer") {
        $sql = "SELECT volunteer_id AS id, first_name, email, password AS pass 
                FROM Volunteer WHERE email = '$email'";
    } elseif ($role === "organization") {
        $sql = "SELECT org_id AS id, org_name, contact_person_email AS email, org_password AS pass 
                FROM Organization WHERE contact_person_email = '$email'";
    } elseif ($role === "admin") {
        $sql = "SELECT admin_id AS id, admin_email AS email, password AS pass 
                FROM Admin WHERE admin_email = '$email'";
    } else {
        $error_message = "Invalid role selected.";
        $sql = "";
    }

    if (!empty($sql)) {
        $result = $conn->query($sql);

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Plain password check (no hashing)
            if ($password === $row['pass']) {

                // Volunteer login
                if ($role === "volunteer") {
                    $_SESSION['volunteer_id'] = $row['id'];
                    $_SESSION['first_name'] = $row['first_name'];

                    $conn->query("INSERT INTO user_login (volunteer_id, activity_status) 
                                  VALUES ('{$row['id']}', 'active')");

                    header("Location: volunteer_dashboard.php");
                    exit();
                }

                // Organization login
                if ($role === "organization") {
                    $_SESSION['org_id'] = $row['id'];
                    $_SESSION['org_name'] = $row['org_name'];

                    $conn->query("INSERT INTO user_login (org_id, activity_status) 
                                  VALUES ('{$row['id']}', 'active')");

                    header("Location: org_dashboard.php");
                    exit();
                }

                // Admin login
                if ($role === "admin") {
                    $_SESSION['admin_id'] = $row['id'];
                    $_SESSION['admin_email'] = $row['email'];

                    $conn->query("INSERT INTO user_login (admin_id, activity_status) 
                                  VALUES ('{$row['id']}', 'active')");

                    header("Location: admin.php");
                    exit();
                }

            } else {
                $error_message = "Invalid password!";
            }
        } else {
            $error_message = "No account found with this email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Volunteer Management Platform</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-box login">
            <form action="" method="post">
                <h1>Login</h1>

                <?php if ($error_message != ""): ?>
                    <div style="color: red; margin-bottom: 20px; padding: 10px; background: #f8d7da; border-radius: 5px;">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-box">
                    <select name="role" required>
                        <option value="volunteer">Volunteer</option>
                        <option value="organization">Organization</option>
                        <option value="admin">Admin</option>
                    </select><br>
                </div>
                <button type="submit" name="login" class="btn">Login</button>
            </form>
        </div>
        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <div class="welcome-msg">
                    <div class="banner">
                        <h1>connect.</h1><h1>volunteer.</h1><h1>transform.</h1>
                        <p>Connect organizations needing help with<br>volunteers ready to contribute.</p>
                    </div>
                </div>
                <div class="btn-group">
                    <button class="btn register-btn" onclick="location.href='vol_.php'">For Volunteers</button>
                    <button class="btn register-btn" onclick="location.href='org_reg.php'">For Organizations</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
