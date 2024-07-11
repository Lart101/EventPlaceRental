<?php
session_start();

$conn = new mysqli("localhost", "root", "", "event_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$reservationId = null;
$package_name = "";
$price = 0;
$inclusions = "";
$totalPrice = 0;
$reservationFee = 5000;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $packageId = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['package_id']));
    $packageType = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['package_type']));
    $start_date = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['start_date']));
    $end_date = isset($_POST['end_date']) ? htmlspecialchars(mysqli_real_escape_string($conn, $_POST['end_date'])) : null;

    // Fetch package details
    $stmt = $conn->prepare("SELECT package_name, price, inclusions FROM swimming_packages WHERE id = ?");
    $stmt->bind_param("i", $packageId);
    $stmt->execute();
    $stmt->bind_result($package_name, $price, $inclusions);
    $stmt->fetch();
    $stmt->close();

    // Calculate total price based on package type
    $totalPrice = $price;
    if ($packageType == 'Combo') {
        $startTimestamp = strtotime($start_date);
        $endTimestamp = strtotime($end_date);
        $totalPrice *= ceil(($endTimestamp - $startTimestamp) / (60 * 60 * 24));
    }

    // Calculate reservation fee
    $reservationFeeAmount = $reservationFee;
    $totalPrice += $reservationFeeAmount; // Adding reservation fee to total price

    // Insert reservation with status 'pending'
    if ($packageType == 'Day' || $packageType == 'Overnight') {
        $insertSql = "INSERT INTO package_reservations (package_id, user_id, start_date, total_price, status) VALUES (?, ?, ?, ?, 'pending')";
        $stmt_insert = $conn->prepare($insertSql);
        $stmt_insert->bind_param("iiss", $packageId, $_SESSION['user_id'], $start_date, $totalPrice);
        
    } else {
        $insertSql = "INSERT INTO package_reservations (package_id, user_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt_insert = $conn->prepare($insertSql);
        $stmt_insert->bind_param("iisss", $packageId, $_SESSION['user_id'], $start_date, $end_date, $totalPrice);
    }

    if ($stmt_insert === false) {
        die('Failed to prepare statement: ' . htmlspecialchars($conn->error));
    }

    $result = $stmt_insert->execute();
    $reservationId = $stmt_insert->insert_id;
    $stmt_insert->close();

    if ($result) {
        $_SESSION['reservation_success'] = true;
    } else {
        $_SESSION['reservation_success'] = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Method: GCash</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <link href="default.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
</head>
<style>
    
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
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
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

    <div class="container mt-5 pt-5">
        <h1>Payment Method: GCash</h1>
        <div class="card mt-4">
            <div class="card-body">
                <h2>Receipt</h2>
                <p><strong>Package Name:</strong> <?php echo $package_name; ?></p>
                <p><strong>Price:</strong> <?php echo $price; ?></p>
                <p><strong>Inclusions:</strong> <?php echo $inclusions; ?></p>
                <p><strong>Start Date:</strong> <?php echo $start_date; ?></p>
                <?php if ($end_date !== null): ?>
                    <p><strong>End Date:</strong> <?php echo $end_date; ?></p>
                <?php else: ?>
                    <p><strong>End Date:</strong> <?php echo $start_date; ?></p>
                <?php endif; ?>
                <p><strong>Reservation Fee:</strong> <?php echo $reservationFeeAmount; ?></p>
                <p><strong>Total Price:</strong> <?php echo $totalPrice; ?></p>
                <p class="text-muted">Note: The reservation fee is the amount you need to pay now. The remaining balance should be paid at the venue.</p>

                <form method="POST" action="upload_payment.php" enctype="multipart/form-data">
                    <input type="hidden" name="reservation_id" value="<?php echo $reservationId; ?>">
                    <div class="mb-3">
                        <label for="proof_of_payment" class="form-label">Upload Proof of Payment</label>
                        <input type="file" class="form-control" id="proof_of_payment" name="proof_of_payment" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">I agree to the <a href="#" id="termsLink">Terms and Conditions</a></label>
                    </div>

    <div id="termsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 style="text-align:center;">Terms and Conditions</h2>
            <br>
            <br>
            <h3>Reservation Terms and Conditions</h3>
            <p>By submitting this reservation, you agree to the following terms and conditions:</p>
            <ul>
                <li>All payments are non-refundable unless explicitly stated otherwise.</li>
                <li>The total price includes the reservation fee and any applicable taxes.</li>
                <li>You agree to pay the remaining balance upon arrival at the venue.</li>
                <li>Failure to pay the remaining balance may result in cancellation of your reservation.</li>
                <li>The venue reserves the right to cancel or amend reservations at its discretion.</li>
                <li>Any disputes arising from this reservation will be governed by the laws of Philippines.</li>
            </ul>
        </div>
    </div>

                    <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to submit the payment?');">Submit Payment</button>
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
</body>
<script>
        // Modal functionality
        var modal = document.getElementById("termsModal");
        var link = document.getElementById("termsLink");
        var close = document.getElementsByClassName("close")[0];

        link.onclick = function () {
            modal.style.display = "block";
        }

        close.onclick = function () {
            modal.style.display = "none";
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</html>
