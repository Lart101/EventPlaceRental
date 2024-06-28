<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_store";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $date_of_birth = $_POST["date_of_birth"];
    $gender = $_POST["gender"];
    $contact_number = $_POST["contact_number"];
    $address = $_POST["address"];

    // Calculate age from date of birth
    $dob = new DateTime($date_of_birth);
    $now = new DateTime();
    $age = $now->diff($dob)->y;

    // Validate age
    if ($age < 18) {
        $message = "You must be at least 18 years old to register.";
        $error = true;
    } else {
        // Check if username already exists
        $check_username_sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($check_username_sql);

        if ($result->num_rows > 0) {
            $message = "Username '$username' already exists. Please choose a different username.";
            $error = true;
        } else {
            // Insert new record into database
            $sql = "INSERT INTO users (username, password, full_name, email, date_of_birth, gender, age, contact_number, address)
                    VALUES ('$username', '$password', '$full_name', '$email', '$date_of_birth', '$gender', '$age', '$contact_number', '$address')";

            if ($conn->query($sql) === TRUE) {
                $message = "New record created successfully";
                $error = false;
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
                $error = true;
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="style1.css">
    <style>
        .notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            z-index: 1000;
            border-radius: 5px;
        }

        .notification.error {
            background-color: #f44336;
            color: white;
        }

        .notification.success {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-left">
            <div class="title">Registration</div>
            <form id="registrationForm" method="post" action="" onsubmit="return validateAge()">
                <!-- Account Information -->
                <fieldset>
                    <legend>Account Information</legend>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>

                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                </fieldset>

                <!-- Personal Information -->
                <fieldset>
                    <legend>Personal Information</legend>
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="date_of_birth">Date of Birth:</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" onchange="calculateAge()" required>

                    <label for="gender">Gender:</label>
                    <fieldset id="gender" name="gender" required>
                        <label><input type="radio" name="gender" value="male" required> Male</label>
                        <label><input type="radio" name="gender" value="female" required> Female</label>
                        <label><input type="radio" name="gender" value="other" required> Other</label>
                    </fieldset>

                    <label for="age">Age:</label>
                    <input type="text" id="age" name="age" readonly>
                    <span id="ageMessage" style="display: none; color: red;"></span>

                    <label for="contact_number">Contact Number:</label>
                    <input type="tel" id="contact_number" name="contact_number" required>

                    <label for="address">Address:</label>
                    <textarea id="address" name="address" required></textarea>
                </fieldset>

                <div class="buttons">
                    <button type="submit">Register</button>
                </div>
            </form>

            <div class="register-link">
                <p>Already have an account? <a href="login.php">Log In</a></p>
            </div>
        </div>
    </div>
    <div class='box'>
        <div class='wave -one'></div>
        <div class='wave -two'></div>
        <div class='wave -three'></div>
    </div>

    <div id="notification" class="notification <?php echo $error ? 'error' : 'success'; ?>">
        <?php echo $message; ?>
    </div>

    <script>
        // Display notification
        var notification = document.getElementById('notification');
        notification.style.display = 'block';
        setTimeout(function () {
            notification.style.display = 'none';
        }, 5000);

        // Calculate age based on date of birth
        function calculateAge() {
            var dob = document.getElementById('date_of_birth').value;
            var today = new Date();
            var birthDate = new Date(dob);
            var age = today.getFullYear() - birthDate.getFullYear();
            var m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            document.getElementById('age').value = age;
        }

        // Validate age on form submission
        function validateAge() {
            var age = parseInt(document.getElementById('age').value);
            var messageElement = document.getElementById('ageMessage');

            if (age < 18) {
                messageElement.textContent = 'You must be at least 18 years old to register.';
                messageElement.style.display = 'block';
                return false;
            } else {
                messageElement.style.display = 'none';
                return true;
            }
        }
    </script>
</body>

</html>
