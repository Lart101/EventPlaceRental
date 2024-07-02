<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "event_store");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch user profile information
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

// Fetch user's reservation history
$reservations = [];
$stmt = $conn->prepare("SELECT r.id, e.name, r.start_date, r.end_date, r.total_price, r.reservation_fee, r.cancelled FROM reservations r
                        INNER JOIN event_places e ON r.event_place_id = e.id
                        WHERE r.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($reservation_id, $event_name, $start_date, $end_date, $total_price, $reservation_fee, $cancelled);

while ($stmt->fetch()) {
    $reservations[] = [
        'id' => $reservation_id,
        'event_name' => $event_name,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'total_price' => $total_price,
        'reservation_fee' => $reservation_fee,
        'cancelled' => $cancelled
    ];
}
$stmt->close();

// Function to cancel reservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
    $reservation_id = $_POST['reservation_id'];

    // Check if reservation can be cancelled (not already cancelled)
    $stmt = $conn->prepare("UPDATE reservations SET cancelled = 1 WHERE id = ? AND user_id = ? AND cancelled = 0");
    $stmt->bind_param("ii", $reservation_id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Reservation cancelled successfully
        echo '<script>alert("Reservation cancelled."); window.location.replace("profile.php");</script>';
        exit();
    } else {
        // Display a message that reservation cannot be cancelled
        echo '<script>alert("Cannot cancel reservation. Please contact support for assistance.");</script>';
    }
    $stmt->close();
}
// Fetch user profile information including password
$stmt = $conn->prepare("SELECT username, email, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $current_password);
$stmt->fetch();
$stmt->close();

// Function to update user profile and change password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password']; // Assuming you have a password field

    // Check if the new username already exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $new_username, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Username already exists for another user
        echo '<script>alert("Username already exists. Please choose a different username.");</script>';
    } else {
        // Proceed with updating user information
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssi", $new_username, $new_email, $new_password, $user_id);
        $stmt->execute();

        // Check if update was successful
        if ($stmt->affected_rows > 0) {
            // Update session variables if necessary
            $_SESSION['username'] = $new_username;
            $_SESSION['email'] = $new_email;

            // Optionally, you can redirect or display a success message here
            echo '<script>alert("Profile updated."); window.location.replace("profile.php");</script>';
            exit();
        } else {
            
        }
    }
    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }

        .card {
            margin-bottom: 20px;
        }

        .reservation-card {
            padding: 20px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">User Profile</h2>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-edit-profile" data-bs-toggle="modal"
                            data-bs-target="#editProfileModal">
                            Edit Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Reservation History</h3>
                        <?php if (empty($reservations)): ?>
                            <p>No reservations found.</p>
                        <?php else: ?>
                            <?php foreach ($reservations as $reservation): ?>
                                <div class="reservation-card">
                                    <div class="reservation-details">
                                        <h5><strong><?php echo htmlspecialchars($reservation['event_name']); ?></strong></h5>
                                        <p><strong>Date:</strong>
                                            <?php echo date('M d, Y', strtotime($reservation['start_date'])) . ' to ' . date('M d, Y', strtotime($reservation['end_date'])); ?>
                                        </p>
                                        <p><strong>Total Price:</strong>
                                            ₱<?php echo htmlspecialchars($reservation['total_price']); ?></p>
                                        <p><strong>Reservation Fee:</strong>
                                            ₱<?php echo htmlspecialchars($reservation['reservation_fee']); ?></p>
                                        <?php if ($reservation['cancelled'] == 0): ?>
                                            <form action="profile.php" method="POST"
                                                onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                                                <input type="hidden" name="reservation_id"
                                                    value="<?php echo $reservation['id']; ?>">
                                                <button type="submit" class="btn-cancel" name="cancel_reservation">Cancel
                                                    Reservation</button>
                                            </form>
                                        <?php else: ?>
                                            <p class="cancelled">Cancelled</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="profile.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
    <div class="mb-3">
        <label for="editUsername" class="form-label">Username</label>
        <input type="text" class="form-control" id="editUsername" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
    </div>
    <div class="mb-3">
        <label for="editEmail" class="form-label">Email</label>
        <input type="email" class="form-control" id="editEmail" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
    </div>
    <div class="mb-3">
        <label for="editPassword" class="form-label">New Password</label>
        <div class="input-group">
            <input type="password" class="form-control" id="editPassword" name="password" 
                   pattern="^(?=.*[a-z])(?=.*[A-Z]).{6,14}$"
                   title="Password must be between 6 and 14 characters long and include at least one uppercase and one lowercase letter."
                   value="<?php echo isset($current_password) ? htmlspecialchars($current_password) : ''; ?>"
                   required>
           <!-- Example usage -->
<button class="btn btn-outline-secondary" type="button" id="togglePassword" onclick="togglePasswordVisibility()">
    <svg id="toggleIcon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
    </svg>
</button>

        </div>
        <div class="invalid-feedback">
            Please provide a valid password.
        </div>
    </div>
</div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_profile">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
        <script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('editPassword');
        const icon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>


</body>

</html>