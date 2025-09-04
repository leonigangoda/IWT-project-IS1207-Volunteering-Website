<?php
session_start();
include("db_connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ---- Registration ----
    if (isset($_POST['submit'])) {
        $fname = $_POST['fName'];
        $lname = $_POST['lName'];
        $nic = $_POST['nic'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $dob = $_POST['dob'];
        $interest = $_POST['interest'];
        $password = $_POST['password'];
        $con_password = $_POST['con_password'];
        
        // Check if passwords match
        if ($password != $con_password) {
            echo "Passwords do not match.";
        } else {
            // Check if email already exists
            $check_query = "SELECT * FROM volunteer WHERE email='$email'";
            $result = $conn->query($check_query);
            
            if ($result->num_rows > 0) {
                echo "Account already exists.";
            } else {
                // Insert new record - THIS WAS MISSING IN YOUR CODE
                $insert_query = "INSERT INTO volunteer(first_name,last_name,nic,phone,email,vol_address,DoB,interest,password) VALUES ('$fname','$lname','$nic','$phone','$email','$address','$dob','$interest','$password')";
                
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
            <div class="form-box login" >
                <form action="" method="post">
                    <h1>Registration</h1>
                    <div class="input-box">
                        <input type="text" name="fName" placeholder="First Name" required>
                    </div>
                    <div class="input-box">
                        <input type="text" name="lName" placeholder="Last Name" required>
                    </div>
                    <div class="input-box">
                        <input type="text" name="nic" placeholder="NIC Number" required>
                    </div>
                    <div class="input-box">
                        <input type="text" name="phone" placeholder="Phone Number" required>
                    </div>
                    <div class="input-box">
                        <input type="text" name="email" placeholder="Email" required>
                    </div>
                    <div class="input-box">
                        <input type="text" name="address" placeholder="Address" required>
                    </div>
                    <div class="input-box">
                    <input type="text" name="dob" placeholder="Date of Birth" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                    </div>
                    <div class="input-box">
                        <select id="interest" name="interest">
                            <option value="" disabled selected hidden>-- Field of Interest --</option>
                            <option value="healthcare">Healthcare</option>
                            <option value="arts">Arts</option>
                            <option value="education">Education</option>
                            <option value="environment">Environment</option>
                        </select>
                    </div>
                    <div class="input-box">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="input-box">
                        <input type="password" name="con_password" placeholder="Confirm Paasword" required>
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