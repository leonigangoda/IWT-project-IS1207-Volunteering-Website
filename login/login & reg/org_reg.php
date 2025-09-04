<?php
session_start();
include("db_connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ---- Registration ----
    if (isset($_POST['submit'])) {
        $orgname = $_POST['orgname'];
        $orgreg = $_POST['orgreg'];
        $cpname = $_POST['cpname'];
        $cpphone = $_POST['cpphone'];
        $cpemail = $_POST['cpemail'];
        $service_type = $_POST['service_type'];
        $orgpw = $_POST['orgpw'];
        $orgconpw = $_POST['orgconpw'];
        
        // Check if passwords match
        if ($orgpw != $orgconpw) {
            echo "Passwords do not match.";
        } else {
            // Check if email already exists
            $check_query = "SELECT * FROM organization WHERE org_reg_no='$orgreg'";
            $result = $conn->query($check_query);
            
            if ($result->num_rows > 0) {
                echo "Account already exists.";
            } else {
                // Insert new record - FIXED THE VARIABLE NAME ERROR
                $insert_query = "INSERT INTO organization(org_reg_no,org_name,contact_person_name,contact_person_email,contact_person_phone,service_type,org_password) 
                VALUES ('$orgreg','$orgname','$cpname','$cpemail','$cpphone','$service_type','$orgpw')";
                
                if ($conn->query($insert_query) === TRUE) {
                    echo "Registration successful!";
                } else {
                    echo "Error: " . $conn->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Volunteer Managemant Platform</title>
        <link rel="stylesheet" href="reg_style.css">
    </head>
 
    <body>
        <div class="container">
            <div class="form-box login">
                <form action="" method="post">
                    <h1>Registration</h1>
                    <div class="input-box">
                        <input type="text" name="orgname" placeholder="Organization Name" required>
                    </div>
                    <div class="input-box">
                        <input type="text" name="orgreg" placeholder="Organization Registeration Number" required>
                    </div>
                    <div class="input-box">
                        <input type="text" name="cpname" placeholder="Contact Person's Name" required>
                    </div>
                    <div class="input-box">
                        <input type="text" name="cpphone" placeholder="Contact Person's Phone Number" required>
                    </div>
                    <div class="input-box">
                        <input type="text" name="cpemail" placeholder="Contact Person's Email" required>
                    </div>
                    <div class="input-box">
                        <select id="service" name="service_type">
                            <option value="" disabled selected hidden>-- Service Type --</option>
                            <option value="healthcare">Healthcare</option>
                            <option value="arts">Arts</option>
                            <option value="education">Education</option>
                            <option value="environment">Environment</option>
                        </select>
                    </div>
                    <div class="input-box">
                        <input type="password" name="orgpw" placeholder="Password" required>
                    </div>
                    <div class="input-box">
                        <input type="password" name="orgconpw" placeholder="Confirm Paasword" required>
                    </div>
                    <button type="submit" name="submit" class="btn">Register</button>

                </form>
            </div>
            <div class="toggle-box">
                <div class="toggle-panel toggle-left">
                    <div class="welcome-msg">
                        <div class="banner">
                            <h1>Welcome Back!</h1>
                            <p>To keep connected with us please log in with your info.</p>
                        </div>
                    </div>
                    <div class="btn-group">
                    <button class="btn register-btn" onclick="location.href='log.php'">Log in</a></button>
                    </div>
                </div>
                

            </div>
        </div>
    </body>
</html>