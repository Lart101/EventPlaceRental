<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_store";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$error = false;

$username = $password = $first_name = $middle_name = $last_name = $email = $date_of_birth = $gender = $contact_number = $barangay = $city = $blk = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $first_name = $_POST["first_name"];
    $middle_name = $_POST["middle_name"];
    $last_name = $_POST["last_name"];
    $full_name = trim($first_name . ' ' . $middle_name . ' ' . $last_name);
    $email = $_POST["email"];
    $date_of_birth = $_POST["date_of_birth"];
    $gender = $_POST["gender"];
    $contact_number = $_POST["contact_number"];
    $barangay = $_POST["barangay"];
    $city = $_POST["city"];
    $blk = $_POST["blk"];

    $dob = new DateTime($date_of_birth);
    $now = new DateTime();
    $age = $now->diff($dob)->y;


    if ($age < 18) {
        $message = "You must be at least 18 years old to register.";
        $error = true;
    } else {

        $check_username_stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $check_username_stmt->bind_param("s", $username);
        $check_username_stmt->execute();
        $result_username = $check_username_stmt->get_result();

        if ($result_username->num_rows > 0) {
            $message = "Username '$username' already exists. Please choose a different username.";
            $error = true;
        }


        $check_email_stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check_email_stmt->bind_param("s", $email);
        $check_email_stmt->execute();
        $result_email = $check_email_stmt->get_result();

        if ($result_email->num_rows > 0) {
            $message .= "<br>Email '$email' already exists. Please use a different email address.";
            $error = true;
        }

        if (!$error) {

            // Concatenate address parts into a single variable
            $address = "$barangay, $city, $blk";
            // Insert new record into database
            $insert_stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, date_of_birth, gender, age, contact_number, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("ssssssiss", $username, $password, $full_name, $email, $date_of_birth, $gender, $age, $contact_number, $address);

            if ($insert_stmt->execute()) {
                $message = "User registered successfully";

                $username = $password = $first_name = $middle_name = $last_name = $email = $date_of_birth = $gender = $contact_number = $barangay = $city = $blk = "";
            } else {
                $message = "Error: " . $insert_stmt->error;
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

        .error-message {
            color: red;
            display: none;
        }

        .field-group {
            margin-bottom: 15px;
        }

        #gender {
            display: inline-block;
        }

        #gender label {
            display: inline-block;
            margin-right: 20px;
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="form-left">
            <div class="title">Registration</div>
            <form id="registrationForm" method="post" action="" onsubmit="return validateForm()">
                <fieldset>
                    <legend>Account Information</legend>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required placeholder="Enter your username"
                        value="<?php echo htmlspecialchars($username); ?>">
                    <span id="usernameError" class="error-message"></span>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password"
                        value="<?php echo htmlspecialchars($password); ?>">
                    <span id="passwordError" class="error-message"></span>

                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required
                        placeholder="Confirm your password" value="<?php echo htmlspecialchars($password); ?>">
                    <span id="confirmPasswordError" class="error-message"></span>
                </fieldset>

                <fieldset>
                    <legend>Personal Information</legend>
                    <div class="field-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" required
                            placeholder="Enter your first name" value="<?php echo htmlspecialchars($first_name); ?>">
                        <span id="firstNameError" class="error-message"></span>

                        <label for="middle_name">Middle Name (Optional):</label>
                        <input type="text" id="middle_name" name="middle_name" placeholder="Enter your middle name"
                            value="<?php echo htmlspecialchars($middle_name); ?>">
                        <span id="middleNameError" class="error-message"></span>

                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" required placeholder="Enter your last name"
                            value="<?php echo htmlspecialchars($last_name); ?>">
                        <span id="lastNameError" class="error-message"></span>
                    </div>

                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" required placeholder="Enter your email"
                        value="<?php echo htmlspecialchars($email); ?>">
                    <span id="emailError" class="error-message"></span>

                    <label for="date_of_birth">Date of Birth:</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" onchange="calculateAge()" required
                        placeholder="Select your date of birth" value="<?php echo htmlspecialchars($date_of_birth); ?>">
                    <span id="dobError" class="error-message"></span>

                    <label for="gender">Gender:</label>
                    <fieldset id="gender" name="gender" required>
                        <label><input type="radio" name="gender" value="male" <?php echo ($gender === 'male') ? 'checked' : ''; ?> required> Male</label>
                        <label><input type="radio" name="gender" value="female" <?php echo ($gender === 'female') ? 'checked' : ''; ?> required> Female</label>
                        <label><input type="radio" name="gender" value="other" <?php echo ($gender === 'other') ? 'checked' : ''; ?> required> Other</label>
                    </fieldset>

                    <span id="genderError" class="error-message"></span>

                    <label for="age">Age:</label>
                    <input type="text" id="age" name="age" readonly
                        value="<?php echo isset($age) ? htmlspecialchars($age) : ''; ?>">

                    <span id="ageError" class="error-message"></span>

                    <label for="contact_number">Contact Number:(+63)</label>
                    <input type="tel" id="contact_number" name="contact_number" required
                        placeholder="Enter your contact number"
                        value="<?php echo htmlspecialchars($contact_number); ?>">
                    <span id="contactNumberError" class="error-message"></span>

                    <div class="field-group">
                        <label for="barangay">Barangay:</label>
                        <input type="text" id="barangay" name="barangay" required placeholder="Enter your barangay"
                            value="<?php echo htmlspecialchars($barangay); ?>">
                        <span id="barangayError" class="error-message"></span>

                        <label for="city">City:</label>
                        <input type="text" id="city" name="city" required placeholder="Enter your city"
                            value="<?php echo htmlspecialchars($city); ?>">
                        <span id="cityError" class="error-message"></span>

                        <label for="blk">Block/Street:</label>
                        <input type="text" id="blk" name="blk" required placeholder="Enter your block/street"
                            value="<?php echo htmlspecialchars($blk); ?>">
                        <span id="blkError" class="error-message"></span>
                    </div>
                </fieldset>
                <label>
                    <label>
                        <input type="checkbox" id="terms" name="terms" required>
                        I agree to the <a href="#" id="termsLink">Terms and Conditions</a>
                    </label>
                    <span id="termsError" class="error-message"></span>

                    <!-- Modal -->
                    <div id="termsModal" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2>Terms and Conditions</h2>
                            <h3>Privacy Rights</h3>
                            <p>Your privacy is important to us. This section outlines the privacy rights of our users.
                            </p>
                            <p><strong>Collection of Information:</strong> We collect information you provide directly
                                to us when you create an account, update your profile, participate in interactive
                                features of our services, request customer support, or otherwise communicate with us.
                                This information may include your name, email address, phone number, date of birth,
                                gender, and any other information you choose to provide.</p>
                            <p><strong>Use of Information:</strong> We use the information we collect to provide,
                                maintain, and improve our services, such as processing transactions, sending you
                                technical notices, updates, security alerts, and support and administrative messages.
                            </p>
                            <p><strong>Sharing of Information:</strong> We do not share your personal information with
                                third parties except as necessary to provide our services or as required by law. This
                                may include sharing information with service providers who perform services on our
                                behalf, such as payment processing, data analysis, email delivery, hosting services, and
                                customer service.</p>
                            <p><strong>Security:</strong> We take reasonable measures to help protect information about
                                you from loss, theft, misuse, and unauthorized access, disclosure, alteration, and
                                destruction.</p>
                            <p><strong>Your Rights:</strong> You have the right to access, update, correct, or delete
                                your personal information. You can do this by logging into your account or by contacting
                                us directly.</p>
                            <p><strong>Changes to this Policy:</strong> We may change this Privacy Policy from time to
                                time. If we make changes, we will notify you by revising the date at the top of the
                                policy and, in some cases, we may provide you with additional notice (such as adding a
                                statement to our homepage or sending you a notification).</p>
                            <p>By using our services, you agree to the collection and use of your information in
                                accordance with this Privacy Policy. If you have any questions about this policy, please
                                contact us.</p>
                        </div>
                    </div>
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

        var notification = document.getElementById('notification');
        notification.style.display = 'block';
        setTimeout(function () {
            notification.style.display = 'none';
        }, 5000);



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
        document.getElementById('termsLink').addEventListener('click', function (event) {
            event.preventDefault();
            document.getElementById('termsModal').style.display = 'block';
        });

        document.querySelector('.modal .close').addEventListener('click', function () {
            document.getElementById('termsModal').style.display = 'none';
        });

        window.onclick = function (event) {
            if (event.target == document.getElementById('termsModal')) {
                document.getElementById('termsModal').style.display = 'none';
            }
        };


        function validateForm() {


            var firstName = document.getElementById('first_name').value;
            var middleName = document.getElementById('middle_name').value;
            var lastName = document.getElementById('last_name').value;
      

            var firstNameError = document.getElementById('firstNameError');
            var middleNameError = document.getElementById('middleNameError');
            var lastNameError = document.getElementById('lastNameError');
            



            var age = parseInt(document.getElementById('age').value);
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirmPassword').value;
            var email = document.getElementById('email').value;
            var contactNumber = document.getElementById('contact_number').value;
            var city = document.getElementById('city').value;

            var passwordError = document.getElementById('passwordError');
            var confirmPasswordError = document.getElementById('confirmPasswordError');
            var emailError = document.getElementById('emailError');
            var ageError = document.getElementById('ageError');
            var contactNumberError = document.getElementById('contactNumberError');
            var cityError = document.getElementById('cityError');

            var isValid = true;

            var namePattern = /^[A-Za-z\s]+$/;

            if (!namePattern.test(firstName)) {
                firstNameError.textContent = 'First name must contain only letters and spaces.';
                firstNameError.style.display = 'block';
                isValid = false;
            } else {
                firstNameError.style.display = 'none';
            }

            if (!namePattern.test(middleName) && middleName !== '') {
                middleNameError.textContent = 'Middle name must contain only letters and spaces.';
                middleNameError.style.display = 'block';
                isValid = false;
            } else {
                middleNameError.style.display = 'none';
            }

            if (!namePattern.test(lastName)) {
                lastNameError.textContent = 'Last name must contain only letters and spaces.';
                lastNameError.style.display = 'block';
                isValid = false;
            } else {
                lastNameError.style.display = 'none';
            }




            var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{6,14}$/;
            if (!passwordPattern.test(password)) {
                passwordError.textContent = 'Password must be between 6 and 14 characters, and include at least one uppercase letter and one special character.';
                passwordError.style.display = 'block';
                isValid = false;
            } else {
                passwordError.style.display = 'none';
            }

            if (password !== confirmPassword) {
                confirmPasswordError.textContent = 'Passwords do not match.';
                confirmPasswordError.style.display = 'block';
                isValid = false;
            } else {
                confirmPasswordError.style.display = 'none';
            }

            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (!emailPattern.test(email)) {
                emailError.textContent = 'Please enter a valid email address.';
                emailError.style.display = 'block';
                isValid = false;
            } else {
                emailError.style.display = 'none';
            }



            if (age < 18) {
                ageError.textContent = 'You must be at least 18 years old to register.';
                ageError.style.display = 'block';
                isValid = false;
            } else {
                ageError.style.display = 'none';
            }



            var contactNumberPattern = /^\d{10}$/;
            if (!contactNumberPattern.test(contactNumber)) {
                contactNumberError.textContent = 'Contact number must be 10 digits. and number only';
                contactNumberError.style.display = 'block';
                isValid = false;
            } else {
                contactNumberError.style.display = 'none';
            }


            if (!namePattern.test(city)) {
                cityError.textContent = 'City must contain only letters and spaces.';
                cityError.style.display = 'block';
                isValid = false;
            } else {
                cityError.style.display = 'none';
            }

            var terms = document.getElementById('terms').checked;
            var termsError = document.getElementById('termsError');

            if (!terms) {
                termsError.textContent = 'You must agree to the terms and conditions.';
                termsError.style.display = 'block';
                isValid = false;
            } else {
                termsError.style.display = 'none';
            }



            return isValid;
        }
    </script>
   


</body >

</html >