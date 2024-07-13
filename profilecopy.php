<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli("localhost", "root", "", "event_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$user_id = $_SESSION['user_id'];
if (isset($_POST['edit_profile'])) {
    // Retrieve form data
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmNewPassword = $_POST['confirm_new_password'];
    
    // Validate if new password and confirm new password match
    if ($newPassword !== $confirmNewPassword) {
        // Handle password mismatch error
        $_SESSION['error'] = "New password and confirm password do not match.";
        header("Location: profilecopy.php");
        exit;
    }
    
    // Retrieve current password from database for the user
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($currentPassword);
    $stmt->fetch();
    $stmt->close();
    
    if ($oldPassword === $currentPassword) {
        // Passwords match, proceed to update the password
        // Update the password in the database
        $updateSql = "UPDATE users SET password = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("si", $newPassword, $user_id);
        $updateStmt->execute();
        $updateStmt->close();
        
        $_SESSION['success'] = "Password updated successfully.";
        header("Location: profilecopy.php");
        exit;
    } else {
        // Old password is incorrect
        $_SESSION['error'] = "Old password is incorrect.";
        header("Location: profilecopy.php");
        exit;
    }
}

// Handle reservation cancellation
if (isset($_POST['cancel_reservation'])) {
    $reservation_id = $_POST['reservation_id'];

    // Prepare statement to update reservation status to 'Cancelled'
    $stmt = $conn->prepare("UPDATE package_reservations SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $reservation_id, $user_id);

    if ($stmt->execute()) {
        // Successful update
        // Optionally, you can redirect the user to a success page or reload the profile page
        header('Location: profilecopy.php');
        exit();
    } else {
        // Handle update failure
        echo "Failed to cancel reservation. Please try again.";
    }

    $stmt->close();
}

// Fetch user details
$stmt = $conn->prepare("SELECT username, email,full_name,age,contact_number,address,gender FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email,$full_name,$age,$contact_number,$address,$gender);
$stmt->fetch();
$stmt->close();

// Fetch all reservations for the user
$stmt = $conn->prepare("SELECT pr.id, sp.package_name, pr.start_date, pr.end_date, pr.total_price, pr.status
                       FROM package_reservations pr
                       INNER JOIN swimming_packages sp ON pr.package_id = sp.id
                       WHERE pr.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reservations = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link href="default.css" rel="stylesheet">

    <style>
        body {

            background-color: #f8f9fa;
        }

        .card {
            margin-bottom: 20px;
        }

        .footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
        }

        .footer a {
            color: #ffffff;
        }

        .footer a:hover {
            text-decoration: none;
            color: #ffc107;
        }


        .reservation-card {
            padding: 20px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .reservation-card .reservation-details {
            margin-bottom: 10px;
        }

        .reservation-card .btn-cancel {
            color: #dc3545;
            border: none;
            background: none;
            cursor: pointer;
        }

        .btn-cancel:hover {
            text-decoration: underline;
        }

        .cancelled {
            color: #dc3545;
            font-weight: bold;
        }

        .btn-edit-profile {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-edit-profile:hover {
            background-color: #0056b3;
            text-decoration: none;
        }
    </style>
</head>

<body>
<?php include 'user_navbar.php'; ?>
    
    <div class="container mt-5">
          <!-- Display Alerts for Password Change Status -->
          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">User Profile</h2>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
                        <p><strong>Gender:</strong> <?php echo htmlspecialchars($gender); ?></p>
                        <p><strong>Age:</strong> <?php echo htmlspecialchars($age); ?></p>
                        <p><strong>Contact Number (+63):</strong> <?php echo htmlspecialchars($contact_number); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>


                        <button type="button" class="btn btn-edit-profile" data-bs-toggle="modal"
                            data-bs-target="#editProfileModal">
                            Change Password
                        </button>
                    </div>
                </div>
            </div>
            <div class="card">
            <div class="card-body">
                <h3 class="card-title">Reservation History</h3>

                <!-- Tabs for different reservation statuses -->
                <ul class="nav nav-tabs" id="reservationTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                            type="button" role="tab" aria-controls="pending" aria-selected="true">Pending</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="accepted-tab" data-bs-toggle="tab" data-bs-target="#accepted"
                            type="button" role="tab" aria-controls="accepted" aria-selected="false">Accepted</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled"
                            type="button" role="tab" aria-controls="cancelled" aria-selected="false">Cancelled</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="denied-tab" data-bs-toggle="tab" data-bs-target="#denied"
                            type="button" role="tab" aria-controls="denied" aria-selected="false">Denied</button>
                    </li>
                </ul>

                <!-- Tab content panes -->
                <div class="tab-content" id="reservationTabsContent">
                    <!-- Pending reservations -->
                    <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                        <?php displayReservationsByStatus($reservations, 'Pending'); ?>
                    </div>

                    <!-- Accepted reservations -->
                    <div class="tab-pane fade" id="accepted" role="tabpanel" aria-labelledby="accepted-tab">
                        <?php displayReservationsByStatus($reservations, 'Accepted'); ?>
                    </div>

                    <!-- Cancelled reservations -->
                    <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                        <?php displayReservationsByStatus($reservations, 'Cancelled'); ?>
                    </div>

                    <!-- Denied reservations -->
                    <div class="tab-pane fade" id="denied" role="tabpanel" aria-labelledby="denied-tab">
                        <?php displayReservationsByStatus($reservations, 'Denied'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>

    </div>

    <?php
// Function to display reservations based on status
function displayReservationsByStatus($reservations, $status) {
    echo '<div class="mt-3">';

    if (!empty($reservations)) {
        foreach ($reservations as $reservation) {
            if ($reservation['status'] === $status) {
                echo '<div class="reservation-card">';
                echo '<div class="reservation-details">';
                echo '<h5><strong>' . htmlspecialchars($reservation['package_name']) . '</strong></h5>';
                echo '<p><strong>Date:</strong> ' . date('M d, Y', strtotime($reservation['start_date'])) . ' to ' . date('M d, Y', strtotime($reservation['end_date'])) . '</p>';
                echo '<p><strong>Total Price:</strong> ₱' . htmlspecialchars($reservation['total_price']) . '</p>';
                echo '<p><strong>Status:</strong> ';

                switch ($status) {
                    case 'Pending':
                        echo '<span class="badge bg-primary">' . $status . '</span>';
                        break;
                    case 'Accepted':
                        echo '<span class="badge bg-success">' . $status . '</span>';
                        break;
                    case 'Denied':
                    case 'Cancelled':
                        echo '<span class="badge bg-danger">' . $status . '</span>';
                        break;
                    default:
                        echo '<span class="badge bg-secondary">' . $status . '</span>';
                        break;
                }

                echo '</p>';

               // Check if reservation is not Cancelled or Denied to show cancellation button
               if ($reservation['status'] !== 'Cancelled' && $reservation['status'] !== 'Denied') {
                echo '<form action="profilecopy.php" method="POST" onsubmit="return confirmCancellation();">';
                echo '<input type="hidden" name="reservation_id" value="' . $reservation['id'] . '">';
                echo '<button type="submit" class="btn btn-danger" name="cancel_reservation">Cancel Reservation</button>';
                echo '</form>';
            } else {
                echo '<button type="button" class="btn btn-secondary" disabled>Cancelled</button>';
            }

            echo '</div></div>';
        }
    }
    } else {
        echo '<div class="reservation-card">';
        echo '<div class="reservation-details text-center">';
        echo '<h5 class="text-danger">No ' . strtolower($status) . ' reservations found</h5>';
        echo '<p class="text-muted">You have no ' . strtolower($status) . ' reservations.</p>';
        echo '</div></div>';
    }

    echo '</div>';
}
?>
    <script>
        function confirmCancellation() {
            return confirm("Are you sure you want to cancel this reservation? Please note that cancelling your reservation means you will not receive any refund.");
        }
    </script>


<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="profilecopy.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="oldPassword" class="form-label">Old Password</label>
                        <input type="password" class="form-control" id="oldPassword" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="newPassword" name="new_password"
                                pattern="^(?=.*[a-z])(?=.*[A-Z]).{6,14}$"
                                title="Password must be between 6 and 14 characters long and include at least one uppercase and one lowercase letter."
                                required>
                            
                        </div>
                        <div id="passwordHelpBlock" class="form-text">
                            Your password must be between 6 and 14 characters long and contain at least one uppercase
                            and one lowercase letter.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmNewPassword"
                            name="confirm_new_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="edit_profile">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
        // Auto-close alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var alertInstance = new bootstrap.Alert(alert);
                alertInstance.close();
            });
        }, 5000);
    </script>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
    

        function confirmLogout() {
            return confirm('Are you sure you want to logout?');
        }
    </script>
   <?php include 'footer.php'; ?>
</body>

</html>