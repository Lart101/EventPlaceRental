<?php

session_start();


// Check if user is not logged in or username is not "@@@@"
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}



include 'config.php';

$message = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $date_of_birth = $_POST["date_of_birth"];
    $gender = $_POST["gender"];
    $contact_number = $_POST["contact_number"];
    $address = $_POST["address"];


    $dob = new DateTime($date_of_birth);
    $now = new DateTime();
    $age = $now->diff($dob)->y;


    $check_username_sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($check_username_sql);

    if ($result->num_rows > 0) {
        $message = "Username '$username' already exists. Please choose a different username.";
    } else {
        $sql = "INSERT INTO users (username, password, full_name, email, date_of_birth, gender, age, contact_number, address)
                VALUES ('$username', '$password', '$full_name', '$email', '$date_of_birth', '$gender', '$age', '$contact_number', '$address')";

        if ($conn->query($sql) === TRUE) {
            $message = "New user created successfully";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = $_POST["id"];
    $username = $_POST["username"];
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $date_of_birth = $_POST["date_of_birth"];
    $gender = $_POST["gender"];
    $contact_number = $_POST["contact_number"];
    $address = $_POST["address"];
    $password = $_POST["password"];

    $dob = new DateTime($date_of_birth);
    $now = new DateTime();
    $age = $now->diff($dob)->y;

    // Corrected SQL query string
    $sql = "UPDATE users SET username='$username', password='$password', full_name='$full_name', email='$email', date_of_birth='$date_of_birth', gender='$gender', age='$age', contact_number='$contact_number', address='$address' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        $message = "User updated successfully";
    } else {
        $message = "Error updating user: " . $conn->error;
    }
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $message = "User deleted successfully";
    } else {
        $message = "Error deleting user: " . $conn->error;
    }
}

$users = $conn->query("SELECT * FROM users");

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<link rel="stylesheet" href="style2.css">
<style>
    .modal-body form {
        width: 100%;
    }

    .modal-body form .form-group {
        margin-bottom: 15px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    /* NavBar to Repa */

    .navbar {
        background-color: whitesmoke;
    }

    .navbar-nav .nav-link {
        color: black;
        font-size: 1.1rem;
    }

    .navbar-nav .nav-item {
        padding: 0 1rem;
    }

    .navbar .navbar-nav .nav-item {
        position: relative;
    }

    .navbar .navbar-nav .nav-item::after {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        content: '';
        background-color: black;
        width: 0%;
        height: 4px;
        transition: 500ms;
    }

    .btn {
        margin-bottom: 10px;
    }


    .navbar .navbar-nav .nav-item:hover:after {
        width: 100%;
    }

    .nav-item.logout button {
        color: #dc3545;
        background-color: transparent;
        border: none;
        cursor: pointer;
    }

    .nav-item.logout button:hover {
        color: #fff;
        background-color: #dc3545;
    }
</style>
</head>

<body>
    <?php include 'admin_navbar.php'; ?>
    <div class="container-lg">
        <a class="navbar-brand" href="AdminUserAccPanel.php">
            <img src="img\profile\logo.jpg" alt="Logo" width="30" class="d-inline-block align-text-top">
            Board Mart User Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="mx-auto">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_package.php">Package</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="admin_reservations.php">Reservation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="AdminUserAccPanel.php">User</a>
                    </li>
                    <?php

                    if (!isset($_SESSION['user_id'])):
                        ?>

                        <li class="nav-item login">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php else: ?>

                        <li class="nav-item logout">
                            <form action="logout.php" method="POST">
                                <button type="submit" class="nav-link btn btn-link"
                                    onclick="return confirmLogout()">Logout</button>
                            </form>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    </nav>
    <script>


        function confirmLogout() {
            return confirm('Are you sure you want to logout?');
        }
    </script>


    <div class="container" style="margin-top:5%">
        <div class="form-left">
            <div class="title">Admin Panel</div>

            <?php if ($message != "") { ?>
                <div class='message'><?php echo $message; ?></div>
            <?php } ?>

            <div class="user-list">
                <div class="search-create-container row mb-3">
                    <div class="search-container col-md-8 d-flex">
                        <input type="text" id="searchInput" class="form-control mr-2" placeholder="Search for users...">
                        <button type="button" id="searchButton" class="btn btn-primary btn-custom-large">Search</button>
                    </div>
                    <div class="col-md-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-success btn-custom-large" data-toggle="modal"
                            data-target="#createUserModal">Create User</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table" id="userTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Password</th>
                                <th>Email</th>
                                <th>Date of Birth</th>
                                <th>Gender</th>
                                <th>Age</th>
                                <th>Contact Number</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo $user['full_name']; ?></td>
                                    <td><?php echo $user['password']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td><?php echo $user['date_of_birth']; ?></td>
                                    <td><?php echo $user['gender']; ?></td>
                                    <td><?php echo $user['age']; ?></td>
                                    <td><?php echo $user['contact_number']; ?></td>
                                    <td><?php echo $user['address']; ?></td>
                                    <td>
                                        <a href="#" class="edit-user btn btn-primary" data-id="<?php echo $user['id']; ?>"
                                            data-username="<?php echo $user['username']; ?>"
                                            data-full_name="<?php echo $user['full_name']; ?>"
                                            data-password="<?php echo $user['password']; ?>"
                                            data-email="<?php echo $user['email']; ?>"
                                            data-date_of_birth="<?php echo $user['date_of_birth']; ?>"
                                            data-gender="<?php echo $user['gender']; ?>"
                                            data-contact_number="<?php echo $user['contact_number']; ?>"
                                            data-address="<?php echo $user['address']; ?>" data-toggle="modal"
                                            data-target="#editUserModal">Edit</a>
                                        <a href="#" class="delete-user btn btn-danger btn-sm"
                                            data-id="<?php echo $user['id']; ?>">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Create User Modal -->
            <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog"
                aria-labelledby="createUserModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="createUserForm" method="post" action="">
                                <input type="hidden" name="create" value="1">

                                <div class="form-group">
                                    <label for="username">Username:</label>
                                    <input type="text" id="username" name="username" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="password">Password:</label>
                                    <input type="password" id="password" name="password" class="form-control"
                                            pattern="^(?=.*[a-z])(?=.*[A-Z]).{6,}$"
                                            title="Password must contain at least one uppercase letter, one lowercase letter, and be at least 6 characters long."
                                            required>
                                </div>

                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password:</label>
                                    <input type="password" id="confirm_password" name="confirm_password"
                                        class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="full_name">Full Name:</label>
                                    <input type="text" id="full_name" name="full_name" class="form-control" required
                                    pattern="[A-Za-z\s]+" title="Please enter letters only">
                                </div>

                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" id="email" name="email" class="form-control" required
                                        pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}"
                                        title="Please enter a valid email address containing at least '.' or '@'"
                                        oninput="validateEmail(this)">
                                </div>
                                

                                <div class="form-group">
                                    <label for="date_of_birth">Date of Birth:</label>
                                     <input type="date" id="date_of_birth" name="date_of_birth" class="form-control"
                                        required max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>"
                                        onchange="calculateAge('editUserForm', 'editDateOfBirth', 'editAge')"
                                        title="Must be at least 18 years old.">
                                </div>

                                <div class="form-group">
                                    <label for="gender">Gender:</label>
                                    <select id="gender" name="gender" class="form-control" required>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="contact_number">Contact Number:</label>
                                     <input type="tel" id="contact_number" name="contact_number" class="form-control"
                                        required pattern="[0-9]{10}"
                                        title="Please enter exactly 10 digits and Numbers Only">
                                </div>

                                <div class="form-group">
                                    <label for="address">Address:</label>
                                    <textarea id="address" name="address" class="form-control" required></textarea>
                                </div>

                                <input type="hidden" id="age" name="age">

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Create User</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Edit User Modal -->
            <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editUserForm" method="post" action="">
                                <input type="hidden" name="update" value="1">
                                <input type="hidden" id="editUserId" name="id">

                                <div class="form-group">
                                    <label for="editUsername">Username:</label>
                                    <input type="text" id="editUsername" name="username" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="editFullName">Full Name:</label>
                                    <input type="text" id="editFullName" name="full_name" class="form-control" required
                                        pattern="[A-Za-z\s]+" title="Please enter letters only">

                                </div>


                                <div class="form-group">
                                    <label for="editEmail">Email:</label>
                                    <input type="email" id="editEmail" name="email" class="form-control" required
                                        pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}"
                                        title="Please enter a valid email address containing at least '.' or '@'"
                                        oninput="validateEmail(this)">

                                </div>

                                <script>
                                    function validateEmail(input) {

                                        if (!input.value.includes('.') && !input.value.includes('@')) {
                                            input.setCustomValidity("Please enter a valid email address containing at least '.' or '@'");
                                        } else {
                                            input.setCustomValidity("");
                                        }
                                    }
                                </script>


                                <div class="form-group">
                                    <label for="editDateOfBirth">Date of Birth:</label>
                                    <input type="date" id="editDateOfBirth" name="date_of_birth" class="form-control"
                                        required max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>"
                                        onchange="calculateAge('editUserForm', 'editDateOfBirth', 'editAge')"
                                        title="Must be at least 18 years old.">

                                </div>

                                <div class="form-group">
                                    <label for="editGender">Gender:</label>
                                    <select id="editGender" name="gender" class="form-control" required>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="editContactNumber">Contact Number: (+63)</label>
                                    <input type="tel" id="editContactNumber" name="contact_number" class="form-control"
                                        required pattern="[0-9]{10}"
                                        title="Please enter exactly 10 digits and Numbers Only">

                                </div>


                                <div class="form-group">
                                    <label for="editAddress">Address:</label>
                                    <textarea id="editAddress" name="address" class="form-control" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="editPassword">New Password:</label>
                                    <div class="input-group">
                                        <input type="password" id="editPassword" name="password" class="form-control"
                                            pattern="^(?=.*[a-z])(?=.*[A-Z]).{6,}$"
                                            title="Password must contain at least one uppercase letter, one lowercase letter, and be at least 6 characters long."
                                            required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="toggleEditPassword">
                                                <i id="toggleEditIcon" class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">
                               
                                    <label for="editConfirmPassword">Confirm New Password:</label>
                                    <div class="input-group">
                                    <input type="password" id="editConfirmPassword" name="confirm_password"
                                        class="form-control" required>
                                        <button class="btn btn-outline-secondary" type="button"
                                                id="toggleEditConfirmPassword">
                                                <i id="toggleEditIcon" class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                </div>

                                <input type="hidden" id="editAge" name="age">

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" >Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
   
    function comparePasswords(event) {
        var password = document.getElementById("editPassword").value;
        var confirmPassword = document.getElementById("editConfirmPassword").value;

        if (password !== confirmPassword) {
            
            alert("Passwords do not match!");
            event.preventDefault(); 
        }
      
    }

  
    document.getElementById("editUserModal").addEventListener("submit", comparePasswords);
</script>
<script>
   
   function comparePasswords(event) {
       var password = document.getElementById("password").value;
       var confirmPassword = document.getElementById("confirm_password").value;

       if (password !== confirmPassword) {
           
           alert("Passwords do not match!");
           event.preventDefault(); 
       }
     
   }

 
   document.getElementById("createUserForm").addEventListener("submit", comparePasswords);
</script>



    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>

        $('#toggleEditPassword').on('click', function () {
            var passwordField = $('#editPassword');
            var icon = $('#toggleEditIcon');

            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fas fa-eye').addClass('fas fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fas fa-eye-slash').addClass('fas fa-eye');
            }
        });
      
         $('#toggleEditConfirmPassword').on('click', function () {
            var passwordField = $('#editConfirmPassword');
            var icon = $('#toggleEditIcon');

            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fas fa-eye').addClass('fas fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fas fa-eye-slash').addClass('fas fa-eye');
            }
        });
    </script>
    <script>
        function calculateAge(formId, dobId, ageId) {
            var dob = document.getElementById(dobId).value;
            var today = new Date();
            var birthDate = new Date(dob);
            var age = today.getFullYear() - birthDate.getFullYear();
            var m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            document.getElementById(ageId).value = age;
        }

        $(document).ready(function () {
            // Edit user modal
            $('.edit-user').on('click', function () {
                var id = $(this).data('id');
                var username = $(this).data('username');
                var full_name = $(this).data('full_name');
                var email = $(this).data('email');
                var date_of_birth = $(this).data('date_of_birth');
                var gender = $(this).data('gender');
                var contact_number = $(this).data('contact_number');
                var address = $(this).data('address');
                var password = $(this).data('password');

                $('#editUserId').val(id);
                $('#editUsername').val(username);
                $('#editFullName').val(full_name);
                $('#editEmail').val(email);
                $('#editDateOfBirth').val(date_of_birth);
                $('#editGender').val(gender);
                $('#editContactNumber').val(contact_number);
                $('#editAddress').val(address);
                $('#editPassword').val(password);
                $('#editConfirmPassword').val(password);


                // Calculate age
                calculateAge('editUserForm', 'editDateOfBirth', 'editAge');
            });

            // Delete user
            $('.delete-user').on('click', function () {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this user?')) {
                    window.location.href = '?delete=' + id;
                }
            });

            // Search functionality
            $('#searchButton').on('click', function () {
                var value = $('#searchInput').val().toLowerCase();
                $("#userTable tbody tr").each(function () {
                    var username = $(this).find('td:nth-child(2)').text().toLowerCase();
                    var fullName = $(this).find('td:nth-child(3)').text().toLowerCase();
                    if (username.includes(value) || fullName.includes(value)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>

</body>

</html>