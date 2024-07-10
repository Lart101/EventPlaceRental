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


$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();


$reservations_ongoing = [];
$stmt = $conn->prepare("SELECT r.id, p.package_name, r.start_date, r.end_date, r.total_price, r.cancelled 
                        FROM package_reservations r
                        INNER JOIN swimming_packages p ON r.package_id = p.id
                        WHERE r.user_id = ? AND (r.cancelled IS NULL OR r.cancelled = 0)");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($reservation_id, $package_name, $start_date, $end_date, $total_price, $cancelled);

while ($stmt->fetch()) {
    $reservations_ongoing[] = [
        'id' => $reservation_id,
        'package_name' => $package_name,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'total_price' => $total_price,
        'cancelled' => $cancelled
    ];
}
$stmt->close();


$reservations_cancelled = [];
$stmt = $conn->prepare("SELECT r.id, p.package_name, r.start_date, r.end_date, r.total_price, r.cancelled 
                        FROM package_reservations r
                        INNER JOIN swimming_packages p ON r.package_id = p.id
                        WHERE r.user_id = ? AND r.cancelled = 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($reservation_id, $package_name, $start_date, $end_date, $total_price, $cancelled);

while ($stmt->fetch()) {
    $reservations_cancelled[] = [
        'id' => $reservation_id,
        'package_name' => $package_name,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'total_price' => $total_price,
        'cancelled' => $cancelled
    ];
}
$stmt->close();





if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
    $reservation_id = $_POST['reservation_id'];
    $current_time = time();
    
   
    $stmt = $conn->prepare("SELECT start_date FROM package_reservations WHERE id = ?");
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $stmt->bind_result($start_date);
    $stmt->fetch();
    $stmt->close();
    
    $start_timestamp = strtotime($start_date);
    $difference_hours = ($current_time - $start_timestamp) / 3600;
    
    if ($difference_hours <= 24) {
     
        
        if ($stmt->affected_rows > 0) {
            
            $stmt = $conn->prepare("DELETE FROM package_reservations WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $reservation_id, $user_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                echo '<script>alert("Reservation cancelled."); window.location.replace("profilecopy.php");</script>';
                exit();
            } else {
                echo '<script>alert("Failed to cancel reservation. Please try again later or contact support.");</script>';
            }
        } else {
            echo '<script>alert("Failed to cancel reservation. Please try again later or contact support.");</script>';
        }
        $stmt->close();
    } else {
        echo '<script>alert("You can only cancel reservations within 1 day of start time.");</script>';
    }
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    
    <link href="default.css" rel="stylesheet">
    
    <style>
        body {
            padding: 20px;
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
<nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-lg">
        <a class="navbar-brand" href="index.html">
            <img src="img\profile\logo.jpg" alt="Logo" width="30" class="d-inline-block align-text-top">
            Board Mart Event Place
        </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="mx-auto">
                    <ul class="navbar-nav">
                    <li class="nav-item">
                            <a class="nav-link" href="index1.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="swimming_packages.php">Packages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contactmain.php">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profilecopy.php">Profile</a>
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
                                    <button type="submit" class="nav-link btn btn-link" onclick="return confirmLogout()">Logout</button>
                                </form>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>



    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">User Profile</h2>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    
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
                        
                        
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="ongoing-tab" data-bs-toggle="tab" data-bs-target="#ongoing" type="button" role="tab" aria-controls="ongoing" aria-selected="true">Ongoing</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="finished-tab" data-bs-toggle="tab" data-bs-target="#finished" type="button" role="tab" aria-controls="finished" aria-selected="false">Finished</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab" aria-controls="cancelled" aria-selected="false">Cancelled</button>
                            </li>
                        </ul>
                        
                       
                        <div class="tab-content mt-3" id="myTabContent">
                          
                            <div class="tab-pane fade show active" id="ongoing" role="tabpanel" aria-labelledby="ongoing-tab">
                                <?php if (empty($reservations_ongoing)): ?>
                                    <p>No ongoing reservations found.</p>
                                <?php else: ?>
                                    <?php foreach ($reservations_ongoing as $reservation): ?>
                                        <div class="reservation-card">
                                            <div class="reservation-details">
                                                <h5><strong><?php echo htmlspecialchars($reservation['package_name']); ?></strong></h5>
                                                <p><strong>Date:</strong>
                                                    <?php echo date('M d, Y', strtotime($reservation['start_date'])) . ' to ' . date('M d, Y', strtotime($reservation['end_date'])); ?>
                                                </p>
                                                <p><strong>Total Price:</strong>
                                                    ₱<?php echo htmlspecialchars($reservation['total_price']); ?></p>
                                                <form action="profilecopy.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                                                    <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                                    <button type="submit" class="btn-cancel" name="cancel_reservation">Cancel Reservation</button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                           
                            <div class="tab-pane fade" id="finished" role="tabpanel" aria-labelledby="finished-tab">
                                <?php if (empty($reservations_finished)): ?>
                                    <p>No finished reservations found.</p>
                                <?php else: ?>
                                    <?php foreach ($reservations_finished as $reservation): ?>
                                        <div class="reservation-card">
                                            <div class="reservation-details">
                                                <h5><strong><?php echo htmlspecialchars($reservation['package_name']); ?></strong></h5>
                                                <p><strong>Date:</strong>
                                                    <?php echo date('M d, Y', strtotime($reservation['start_date'])) . ' to ' . date('M d, Y', strtotime($reservation['end_date'])); ?>
                                                </p>
                                                <p><strong>Total Price:</strong>
                                                    ₱<?php echo htmlspecialchars($reservation['total_price']); ?></p>
                                                <p class="cancelled">Finished</p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Cancelled tab -->
                            <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                                <?php if (empty($reservations_cancelled)): ?>
                                    <p>No cancelled reservations found.</p>
                                <?php else: ?>
                                    <?php foreach ($reservations_cancelled as $reservation): ?>
                                        <div class="reservation-card">
                                            <div class="reservation-details">
                                                <h5><strong><?php echo htmlspecialchars($reservation['package_name']); ?></strong></h5>
                                                <p><strong>Date:</strong>
                                                    <?php echo date('M d, Y', strtotime($reservation['start_date'])) . ' to ' . date('M d, Y', strtotime($reservation['end_date'])); ?>
                                                </p>
                                                <p><strong>Total Price:</strong>
                                                    ₱<?php echo htmlspecialchars($reservation['total_price']); ?></p>
                                                <p class="cancelled">Cancelled</p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="profilecopy.php" method="POST">
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
                                <input type="password" class="form-control" id="editPassword" name="password" pattern="^(?=.*[a-z])(?=.*[A-Z]).{6,14}$" title="Password must be between 6 and 14 characters long and include at least one uppercase and one lowercase letter." required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" onclick="togglePasswordVisibility()">
                                    <i class="bi bi-eye"></i>
                                </button>
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
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>&copy; 2024 Board Mart Event Place. All Rights Reserved.</p>
                    <div class="mt-4">
                        <h3>Follow Us on:</h3>
                        <ul class="list-inline">
                            <li class="list-inline-item">
                                <a href="https://www.facebook.com/BoardMartsEventPlace" target="_blank">
                                    <i class="bi bi-facebook" style="font-size: 1rem; margin-right: 10px;"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://www.instagram.com/boardmarseventplace" target="_blank">
                                    <i class="bi bi-instagram" style="font-size: 1rem; margin-right: 10px;"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://x.com/Boardmart" target="_blank">
                                    <i class="bi bi-twitter" style="font-size: 1rem; margin-right: 10px;"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('editPassword');
            const icon = document.querySelector('#togglePassword i');
            
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
