<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Redirect to profilecopy.php if reservation ID is not provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Reservation ID is missing.";
    header('Location: profilecopy.php');
    exit();
}

// Get reservation ID from URL
$reservation_id = $_GET['id'];

// Database connection
$conn = new mysqli("localhost", "root", "", "event_store");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL query to fetch reservation details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT pr.id, sp.package_name, sp.price, pr.start_date, pr.end_date, pr.created_at, pr.total_price, pr.proof_of_payment, pr.status, pr.add_ons, pr.extend_stay
                       FROM package_reservations pr
                       INNER JOIN swimming_packages sp ON pr.package_id = sp.id
                       WHERE pr.id = ? AND pr.user_id = ?");
$stmt->bind_param("ii", $reservation_id, $user_id);
$stmt->execute();

// Get result
$result = $stmt->get_result();

// Initialize variables
$reservation_id = $package_name = $price = $start_date = $end_date = $created_at = $total_price = $proof_of_payment = $status = $add_ons = $extend_stay = "";

// Check if reservation exists and fetch data
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $reservation_id = htmlspecialchars($row['id']);
    $package_name = htmlspecialchars($row['package_name']);
    $price = htmlspecialchars($row['price']);
    $start_date = htmlspecialchars($row['start_date']);
    $end_date = htmlspecialchars($row['end_date']);
    $created_at = htmlspecialchars($row['created_at']);
    $total_price = htmlspecialchars($row['total_price']);
    $proof_of_payment = base64_encode($row['proof_of_payment']);
    $status = htmlspecialchars($row['status']);
    $add_ons = htmlspecialchars($row['add_ons']);
    $extend_stay = htmlspecialchars($row['extend_stay']);
} else {
    $_SESSION['error'] = "Reservation not found or unauthorized access.";
    header('Location: profilecopy.php');
    exit();
}

// Close statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .receipt {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .receipt-details {
            margin-top: 20px;
        }
        .receipt-details p {
            margin-bottom: 10px;
        }
        .back-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'user_navbar.php'; ?>

    <div class="container">
        <div class="receipt">
            <div class="receipt-header">
                <h2>Reservation Receipt</h2>
                <p>Thank you for your reservation!</p>
            </div>
            <div class="receipt-details">
                <!-- Display reservation details -->
                <p><strong>Reservation ID:</strong> <?php echo $reservation_id; ?></p>
                <p><strong>Package Name:</strong> <?php echo $package_name; ?></p>
                <p><strong>Price per Pax:</strong> PHP <?php echo $price; ?></p>
                <p><strong>Start Date:</strong> <?php echo $start_date; ?></p>
                <p><strong>End Date:</strong> <?php echo $end_date; ?></p>
                <p><strong>Created At:</strong> <?php echo $created_at; ?></p>
                <p><strong>Total Price:</strong> PHP <?php echo $total_price; ?></p>
                <p><strong>Status:</strong> <?php echo $status; ?></p>
                <p><strong>Add-ons:</strong> <?php echo $add_ons; ?></p>
                <p><strong>Extend Stay:</strong> <?php echo $extend_stay; ?></p>
                <p><strong>Proof of Payment:</strong></p>
                <?php if (!empty($proof_of_payment)): ?>
                    <!-- Button to trigger modal -->
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#proofModal">View</button>

                    <!-- Modal -->
                    <div class="modal fade" id="proofModal" tabindex="-1" aria-labelledby="proofModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="proofModalLabel">Proof of Payment</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <img src="data:image/jpeg;base64,<?php echo $proof_of_payment; ?>" alt="Proof of Payment" class="img-fluid">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <p>No proof of payment provided.</p>
                <?php endif; ?>
            </div>
            <a href="profilecopy.php" class="btn btn-primary back-button">Back to Profile</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
