<?php
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $packageId = $_POST['package_id'];
    $packageName = $_POST['package_name'];
    $inclusions = $_POST['inclusions'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $maxPax = $_POST['max_pax'];
    $startDate = $_POST['start_date'];
    $endDate = isset($_POST['end_date']) ? $_POST['end_date'] : null;  // Optional if combo package
    $addOns = isset($_POST['add_ons']) ? $_POST['add_ons'] : [];
    $extendedStayHours = isset($_POST['extended_stay_hours']) ? $_POST['extended_stay_hours'] : 0;

    // Calculate total price
    $basePrice = floatval($price);
    $reservationFee = 5000;
    $totalPrice = $basePrice;

    if ($endDate) {
        // Calculate duration for combo package
        $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
        $totalPrice *= ceil($days);
    }

    // Add-ons prices
    if (in_array('Extended stay', $addOns)) {
        $extendedStayPrice = 1000 * intval($extendedStayHours);
        $totalPrice += $extendedStayPrice;
    }
    if (in_array('Family Room', $addOns)) {
        $totalPrice += 3000;
    }
    if (in_array('Suite Room', $addOns)) {
        $totalPrice += 5000;
    }
    if (in_array('Videoke and Game Room', $addOns)) {
        $totalPrice += 3000;
    }

    // Add reservation fee
    $totalPrice += $reservationFee;

} else {
    // Redirect if accessed directly without form submission
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link href="default.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
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

    <div class="container pt-5">


        <div class="card mt-4">
            <div class="card-body">
                <h1>Payment Method: GCash</h1>
                <div class="receipt mt-4">
                    <h1>Receipt</h1>
                    <div class="mb-3">
                        <p><strong>Package Name:</strong> <?php echo htmlspecialchars($packageName); ?></p>
                        <p><strong>Inclusions:</strong> <?php echo htmlspecialchars($inclusions); ?></p>
                        <p><strong>Price:</strong> ₱<?php echo $price; ?></p>
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($duration); ?></p>
                        <p><strong>Max Pax:</strong> <?php echo htmlspecialchars($maxPax); ?></p>
                        <p><strong>Start Date:</strong> <?php echo $startDate; ?></p>
                        <?php if ($endDate): ?>
                            <p><strong>End Date:</strong> <?php echo $endDate; ?></p>
                        <?php endif; ?>
                        <?php if (!empty($addOns)): ?>
                            <p><strong>Add-ons:</strong> <?php echo implode(", ", $addOns); ?></p>
                        <?php endif; ?>
                        <p><strong>Total Price:</strong> ₱<?php echo number_format($totalPrice, 2); ?></p>
                        <p><em>(Reservation fee only. Remaining fee to be paid at the venue)</em></p>

                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <form action="insert_reservation.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($packageId); ?>">
                    <input type="hidden" name="package_name" value="<?php echo htmlspecialchars($packageName); ?>">
                    <input type="hidden" name="inclusions" value="<?php echo htmlspecialchars($inclusions); ?>">
                    <input type="hidden" name="price" value="<?php echo $price; ?>">
                    <input type="hidden" name="duration" value="<?php echo htmlspecialchars($duration); ?>">
                    <input type="hidden" name="max_pax" value="<?php echo htmlspecialchars($maxPax); ?>">
                    <input type="hidden" name="start_date" value="<?php echo $startDate; ?>">
                    <?php if ($endDate): ?>
                        <input type="hidden" name="end_date" value="<?php echo $endDate; ?>">
                    <?php endif; ?>
                    <?php if (!empty($addOns)): ?>
                        <?php foreach ($addOns as $addOn): ?>
                            <input type="hidden" name="add_ons[]" value="<?php echo htmlspecialchars($addOn); ?>">
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <input type="hidden" name="extended_stay_hours" value="<?php echo $extendedStayHours; ?>">
                    <input type="hidden" name="total_price" value="<?php echo $totalPrice; ?>">
                    <input type="hidden" name="status" value="Pending">

                    <div class="gcash-instructions">
                        <h2>GCash Payment Instructions</h2>
                        <p>GCASH NUMBER:+63 915 528 5651</p>
                        <p>Once you have made the payment, please upload the proof of payment below.</p>
                    </div>

                    <div class="mb-3">
                        <label for="proof_of_payment" class="form-label">Upload Proof of Payment</label>
                        <input type="file" class="form-control" id="proof_of_payment" name="proof_of_payment" required>
                    </div>

                    <div class="mb-3">
                        <input type="checkbox" id="terms" name="terms" required>
                        I agree to the <a href="#" id="termsLink">Terms and Conditions</a>
                    </div>

                    <!-- Modal -->
                    <div id="termsModal" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2>Terms and Conditions</h2>
                            <h3>Transactions and Payment Terms</h3>
                            <p>This section outlines the terms and conditions related to transactions and payments.</p>
                            <p><strong>Payment:</strong> By making a payment on our platform, you agree to pay for the
                                services or products as specified.</p>
                            <p><strong>No Refund Policy:</strong> All payments are non-refundable. Once a transaction is
                                completed, no refunds will be issued.</p>
                            <p><strong>Payment Processing:</strong> Payments are processed securely through our
                                designated payment gateway.</p>
                            <p><strong>Transaction Currency:</strong> All transactions are processed in Peso. Any
                                currency conversion costs or fees are the responsibility of the payer.</p>
                            <p><strong>Transaction Errors:</strong> In case of any payment discrepancies or errors,
                                please contact our customer support immediately for assistance.</p>
                            <p><strong>Changes to Payment Terms:</strong> We reserve the right to modify these payment
                                terms from time to time. Changes will be effective upon posting and your continued use
                                of our services constitutes acceptance of these changes.</p>
                            <h3>General Terms</h3>
                            <p>By using our services, you agree to abide by these terms and conditions. If you have any
                                questions about this policy, please contact us.</p>
                        </div>
                    </div>

                    <input type="hidden" name="reservation_id" value="<?php echo $reservationId; ?>">
                    <button type="submit" class="btn btn-primary"
                        onclick="return confirm('Are you sure you want to submit the payment?');">Submit
                        Payment</button>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
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
    </script>
</body>

</html>