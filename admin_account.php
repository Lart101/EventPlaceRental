<?php
session_start();

// Check if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
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

    // Check if the email already exists
    $check_email_sql = "SELECT * FROM adminaccount WHERE email = '$email'";
    $result = $conn->query($check_email_sql);

    if ($result->num_rows > 0) {
        $message = "Email '$email' already exists. Please choose a different email.";
    } else {
        // Insert the admin account if email does not exist
        $sql = "INSERT INTO adminaccount (username, password, full_name, email)
                VALUES ('$username', '$password', '$full_name', '$email')";

        if ($conn->query($sql) === TRUE) {
            $message = "Admin account created successfully.";
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
    $password = $_POST["password"];

    $sql = "UPDATE adminaccount SET username='$username', password='$password', full_name='$full_name', email='$email' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        $message = "Admin account updated successfully.";
    } else {
        $message = "Error updating admin account: " . $conn->error;
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM adminaccount WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $message = "Admin account deleted successfully.";
    } else {
        $message = "Error deleting admin account: " . $conn->error;
    }
}

$admin_accounts = $conn->query("SELECT * FROM adminaccount");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
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
        <a class="navbar-brand" href="admin_account.php">
            <img src="img\profile\logo.jpg" alt="Logo" width="30" class="d-inline-block align-text-top">
            Board Mart Admin Account
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
                    <?php if (!isset($_SESSION['user_id'])): ?>
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
            <div class="title">Admin Account Management</div>
            <?php if ($message != "") { ?>
                <div class='message'><?php echo $message; ?></div>
            <?php } ?>
            <div class="admin-account-list">
                <div class="search-create-container row mb-3">
                    <div class="search-container col-md-8 d-flex">
                        <input type="text" id="searchInput" class="form-control mr-2" placeholder="Search for admin accounts...">
                    </div>
                    <div class="col-md-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-success btn-custom-large" data-toggle="modal"
                            data-target="#createAdminAccountModal">Create Admin Account</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="adminAccountTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Password</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($admin_account = $admin_accounts->fetch_assoc()) { ?>
                                <tr data-username="<?php echo strtolower($admin_account['username']); ?>">
                                    <td><?php echo $admin_account['id']; ?></td>
                                    <td><?php echo $admin_account['username']; ?></td>
                                    <td><?php echo $admin_account['full_name']; ?></td>
                                    <td><?php echo $admin_account['password']; ?></td>
                                    <td><?php echo $admin_account['email']; ?></td>
                                    <td>
                                        <a href="#" class="edit-admin-account btn btn-primary" data-id="<?php echo $admin_account['id']; ?>"
                                            data-username="<?php echo $admin_account['username']; ?>"
                                            data-full_name="<?php echo $admin_account['full_name']; ?>"
                                            data-password="<?php echo $admin_account['password']; ?>"
                                            data-email="<?php echo $admin_account['email']; ?>" data-toggle="modal"
                                            data-target="#editAdminAccountModal">Edit</a>
                                        <a href="#" class="delete-admin-account btn btn-danger btn-sm"
                                            data-id="<?php echo $admin_account['id']; ?>">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const searchInput = document.getElementById('searchInput');
                    const adminAccountTable = document.getElementById('adminAccountTable');
                    const adminAccountRows = adminAccountTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                    searchInput.addEventListener('keyup', function () {
                        const searchTerm = searchInput.value.toLowerCase();
                        Array.from(adminAccountRows).forEach(function (row) {
                            const userName = row.getAttribute('data-username');
                            if (userName.includes(searchTerm)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });

                    document.querySelectorAll('.delete-admin-account').forEach(function (deleteButton) {
                        deleteButton.addEventListener('click', function () {
                            const accountId = this.getAttribute('data-id');
                            if (confirm('Are you sure you want to delete this admin account?')) {
                                window.location.href = 'admin_account.php?delete=' + accountId;
                            }
                        });
                    });

                    document.querySelectorAll('.edit-admin-account').forEach(function (editButton) {
                        editButton.addEventListener('click', function () {
                            const accountId = this.getAttribute('data-id');
                            const username = this.getAttribute('data-username');
                            const fullName = this.getAttribute('data-full_name');
                            const password = this.getAttribute('data-password');
                            const email = this.getAttribute('data-email');

                            document.getElementById('edit_id').value = accountId;
                            document.getElementById('edit_username').value = username;
                            document.getElementById('edit_full_name').value = fullName;
                            document.getElementById('edit_password').value = password;
                            document.getElementById('edit_email').value = email;
                        });
                    });
                });
            </script>

            <!-- Create Admin Account Modal -->
            <div class="modal fade" id="createAdminAccountModal" tabindex="-1" role="dialog"
                aria-labelledby="createAdminAccountModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createAdminAccountModalLabel">Create Admin Account</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="admin_account.php">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="form-group">
                                    <label for="full_name">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <button type="submit" class="btn btn-primary" name="create">Create</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Admin Account Modal -->
            <div class="modal fade" id="editAdminAccountModal" tabindex="-1" role="dialog"
                aria-labelledby="editAdminAccountModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editAdminAccountModalLabel">Edit Admin Account</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="admin_account.php">
                                <input type="hidden" id="edit_id" name="id">
                                <div class="form-group">
                                    <label for="edit_username">Username</label>
                                    <input type="text" class="form-control" id="edit_username" name="username" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_password">Password</label>
                                    <input type="text" class="form-control" id="edit_password" name="password" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_full_name">Full Name</label>
                                    <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_email">Email</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>
                                <button type="submit" class="btn btn-primary" name="update">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .form-left {
                    background: #f8f9fa;
                    padding: 30px;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }

                .form-left .title {
                    font-size: 24px;
                    font-weight: bold;
                    margin-bottom: 20px;
                    text-align: center;
                }

                .admin-account-list .table th, .admin-account-list .table td {
                    vertical-align: middle;
                }

                .admin-account-list .btn-sm {
                    font-size: 14px;
                    padding: 5px 10px;
                }
            </style>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
