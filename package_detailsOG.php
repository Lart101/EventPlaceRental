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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $packageId = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['package_id']));
    $start_date = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['start_date']));
    $end_date = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['end_date']));

    // Validate end date selection
    if (empty($end_date)) {
        $_SESSION['reservation_error'] = "Please select an end date.";
        header("Location: package_details.php?id=$packageId");
        exit();
    }

    // Check date validation
    $startTimestamp = strtotime($start_date);
    $endTimestamp = strtotime($end_date);
    if ($endTimestamp <= $startTimestamp) {
        $_SESSION['reservation_error'] = "End date must be after start date.";
        header("Location: package_details.php?id=$packageId");
        exit();
    }

    // Check for overlapping reservations
    $overlap = false;
    $reservedSql = "SELECT * FROM package_reservations WHERE package_id = ? AND ((start_date <= ? AND end_date >= ?) OR (start_date <= ? AND end_date >= ?) OR (start_date >= ? AND end_date <= ?))";
    $stmt = $conn->prepare($reservedSql);
    $stmt->bind_param("ississs", $packageId, $start_date, $start_date, $end_date, $end_date, $start_date, $end_date);

    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $overlap = true;
    }
    $stmt->close();

    if ($overlap) {
        $_SESSION['reservation_error'] = "Selected date range overlaps with existing reservations. Please choose another date range.";
        header("Location: package_details.php?id=$packageId");
        exit();
    }

    // Calculate total price
    $sql = "SELECT price FROM swimming_packages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $packageId);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();

    $totalPrice = ($price * (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24)) + $price * 0.05;

    // Insert reservation
    $insertSql = "INSERT INTO package_reservations (package_id, user_id, start_date, end_date, total_price) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("iisss", $packageId, $_SESSION['user_id'], $start_date, $end_date, $totalPrice);
    $result = $stmt->execute();
    $stmt->close();

    $_SESSION['reservation_success'] = $result;

    header("Location: package_details.php?id=$packageId");
    exit();
}

// Fetch package details
$packageId = htmlspecialchars(mysqli_real_escape_string($conn, $_GET['id']));
$sql = "SELECT package_name, inclusions, price, duration, max_pax, multiple_images FROM swimming_packages WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $packageId);
$stmt->execute();
$stmt->bind_result($package_name, $inclusions, $price, $duration, $max_pax, $multiple_images);
$stmt->fetch();
$stmt->close();


$packageImages = explode(",", $multiple_images);

// Fetch reserved dates
$reservedDates = [];
$reservedSql = "SELECT start_date, end_date FROM package_reservations WHERE package_id = ?";
$stmt = $conn->prepare($reservedSql);
$stmt->bind_param("i", $packageId);
$stmt->execute();
$stmt->bind_result($start_date, $end_date);
while ($stmt->fetch()) {
    $reservedDates[] = ['start_date' => $start_date, 'end_date' => $end_date];
}
$stmt->close();

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swimming Package Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link href="default.css" rel="stylesheet">


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <style>
        .reserved-dates {
            margin-top: 20px;
        }

        .reserved-dates ul {
            list-style-type: none;
            padding: 0;
        }

        .reserved-dates ul li {
            display: inline-block;
            margin-right: 10px;
            background-color: #f0f0f0;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .image-collage {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .image-collage img {
            width: calc(50% - 5px);
            height: auto;
            cursor: pointer;

        }

        .image-viewer {
            display: none;
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;

        }

        .image-viewer img {
            max-width: 100%;
            max-height: 80vh;
            display: block;
            margin: 0 auto;
            margin-top: 10px;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            color: #fff;
            font-size: 24px;
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
                            <a class="nav-link" href="index1.html">Home</a>
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




    <nav aria-label="breadcrumb" style="padding-top: 5%; margin-left: 20px;">
        <ol class="breadcrumb fade-in">
            <li class="breadcrumb-item"><a href="swimming.php">Swimming Packages</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $package_name; ?></li>
        </ol>
    </nav>
    <div class="container mt-5 pt-5 fade-in">
        <div class="row">
            <div class="col-md-6">
                <div class="image-collage" id="imageCollage">
                    <?php
                    $counter = 0;
                    foreach ($packageImages as $index => $image) {
                        if ($counter < 4) {
                            echo '<img src="' . $image . '" class="img-fluid" alt="' . $package_name . '" onclick="openImageViewer(' . $index . ')">';
                        } else {
                            echo '<span id="showMoreBtn" class="btn btn-link text-decoration-none" onclick="showAllImages()">Show More</span>';
                            break;
                        }
                        $counter++;
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <h1><?php echo $package_name; ?></h1>
                <p><strong>Inclusions:</strong> <?php echo $inclusions; ?></p>
                <p><strong>Price:</strong> ₱<?php echo $price; ?></p>
                <p><strong>Duration:</strong> <?php echo $duration; ?> hours</p>
                <p><strong>Maximum Participants:</strong> <?php echo $max_pax; ?></p>
                <form id="reservationForm" method="POST" action="package_details.php?id=<?php echo $packageId; ?>"
                    onsubmit="return validateReservation()">
                    <input type="hidden" name="package_id" value="<?php echo $packageId; ?>">
                    <input type="hidden" id="base_price" value="<?php echo $price; ?>">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" class="form-control"
                            min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date:</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" disabled required>
                    </div>
                    <div class="mb-3">
                        <label for="total_price" class="form-label">Total Price:</label>
                        <input type="text" id="total_price" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="reservation_fee" class="form-label">Reservation Fee (5%):</label>
                        <span class="text-danger">You can only use the reservation within 5 hours</span>
                        <input type="text" id="reservation_fee" class="form-control" readonly>
                    </div>
                    <button type="botton" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal"
                        onclick="showPaymentModal()">Reserve Now</button>

                </form>

                <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="paymentModalLabel">Payment Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Receipt</h5>
                                        <ul class="list-group">
                                            <li class="list-group-item"><strong>Package:</strong> <span
                                                    id="receiptPackageName"></span></li>
                                            <li class="list-group-item"><strong>Duration:</strong> <span
                                                    id="receiptDuration"></span> hours</li>
                                            <li class="list-group-item"><strong>Price per Day:</strong> ₱<span
                                                    id="receiptPricePerDay"></span></li>
                                            <li class="list-group-item"><strong>Start Date:</strong> <span
                                                    id="receiptStartDate"></span></li>
                                            <li class="list-group-item"><strong>End Date:</strong> <span
                                                    id="receiptEndDate"></span></li>
                                            <li class="list-group-item"><strong>Total Price:</strong> ₱<span
                                                    id="receiptTotalPrice"></span></li>
                                            <li class="list-group-item"><strong>Reservation Fee (5%):</strong> ₱<span
                                                    id="receiptReservationFee"></span></li>
                                            <li class="list-group-item"><strong>Grand Total:</strong> ₱<span
                                                    id="receiptGrandTotal"></span></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <form id="paymentForm" action="process_payment.php" method="POST"
                                            onsubmit="return validatePaymentForm()">
                                            <div class="mb-3">
                                                <label for="card_number" class="form-label">Card Number</label>
                                                <input type="text" class="form-control" id="card_number"
                                                    name="card_number" required>
                                                <div class="invalid-feedback">
                                                    Please enter a valid card number.
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                                <input type="text" class="form-control" id="expiry_date"
                                                    name="expiry_date" placeholder="MM/YY" required>
                                                <div class="invalid-feedback">
                                                    Please enter a valid expiry date in MM/YY format.
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="cvv" class="form-label">CVV</label>
                                                <input type="text" class="form-control" id="cvv" name="cvv" required>
                                                <div class="invalid-feedback">
                                                    Please enter a valid CVV.
                                                </div>
                                            </div>
                                            <input type="hidden" id="hiddenTotalAmount" name="total_amount">
                                            <button type="submit" class="btn btn-primary">Submit Payment</button>
                                        </form>

                                      

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="container mt-5 reserved-dates">
        <h2>Already Reserved Dates</h2>
        <ul>
            <?php
            if (!empty($reservedDates)) {
                foreach ($reservedDates as $reserved) {
                    echo '<li>' . date('M d, Y', strtotime($reserved['start_date'])) . ' - ' . date('M d, Y', strtotime($reserved['end_date'])) . '</li>';
                }
            } else {
                echo '<p>No dates have been reserved yet for this swimming package.</p>';
            }
            ?>
        </ul>
    </div>
    <!-- Image Viewer -->
    <div class="image-viewer" id="imageViewer">
        <?php foreach ($packageImages as $index => $image): ?>
            <img src="<?php echo $image; ?>" class="img-fluid <?php if ($index >= 4)
                   echo 'hidden-image'; ?>"
                alt="<?php echo $package_name; ?>">
        <?php endforeach; ?>
        <span class="close-btn" onclick="closeImageViewer()">&times;</span>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        function calculatePrices() {
            const basePrice = parseFloat(document.getElementById('base_price').value);
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);

            if (startDate && endDate && endDate >= startDate) {
                const days = (endDate - startDate) / (1000 * 60 * 60 * 24) + 1;
                let totalPrice = days * basePrice;





                const reservationFee = totalPrice * 0.05;


                document.getElementById('total_price').value = '₱' + totalPrice.toFixed(2);
                document.getElementById('reservation_fee').value = '₱' + reservationFee.toFixed(2);
            }
        }

        document.getElementById('start_date').addEventListener('change', function () {
            const startDate = new Date(this.value);
            const endDateInput = document.getElementById('end_date');

            endDateInput.disabled = false;
            endDateInput.min = this.value;

            if (endDateInput.value && new Date(endDateInput.value) < startDate) {
                endDateInput.value = '';
                calculatePrices();
            }

            disableReservedDates();
        });

        document.getElementById('end_date').addEventListener('change', calculatePrices);

        function disableReservedDates() {
            const reservedDates = <?php echo json_encode($reservedDates); ?>;
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            for (let i = 0; i < reservedDates.length; i++) {
                const reservedStart = new Date(reservedDates[i]['start_date']);
                const reservedEnd = new Date(reservedDates[i]['end_date']);

                if (startDate && endDate) {
                    if (!(endDate < reservedStart || startDate > reservedEnd)) {
                 
                        return;
                    }
                }
            }
        }

        function validateReservation() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            if (!endDateInput.value) {
                alert('Please select an end date.');
                return false;
            }

      
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            if (endDate <= startDate) {
                alert('End date must be after start date.');
                return false;
            }

        
            const reservedDates = <?php echo json_encode($reservedDates); ?>;
            for (let i = 0; i < reservedDates.length; i++) {
                const reservedStart = new Date(reservedDates[i]['start_date']);
                const reservedEnd = new Date(reservedDates[i]['end_date']);

                if (!(endDate < reservedStart || startDate > reservedEnd)) {
                    alert('This date range includes already reserved dates. Please select another range.');
                    return false;
                }
            }

            // Proceed to form submission or modal opening
            return true;
        }
    </script>

    <script>
        function showPaymentModal() {
            const packageName = "<?php echo $package_name; ?>";
            const duration = "<?php echo $duration; ?>";
            const pricePerDay = parseFloat(document.getElementById('base_price').value);
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const totalPrice = parseFloat(document.getElementById('total_price').value.replace('₱', ''));
            const reservationFee = totalPrice * 0.05;
            const grandTotal = totalPrice + reservationFee;

            document.getElementById('receiptPackageName').innerText = packageName;
            document.getElementById('receiptDuration').innerText = duration;
            document.getElementById('receiptPricePerDay').innerText = pricePerDay.toFixed(2);
            document.getElementById('receiptStartDate').innerText = startDate;
            document.getElementById('receiptEndDate').innerText = endDate;
            document.getElementById('receiptTotalPrice').innerText = totalPrice.toFixed(2);
            document.getElementById('receiptReservationFee').innerText = reservationFee.toFixed(2);
            document.getElementById('receiptGrandTotal').innerText = grandTotal.toFixed(2);

            document.getElementById('hiddenTotalAmount').value = grandTotal.toFixed(2);
        }

        document.getElementById('paymentForm').addEventListener('submit', function (event) {
            event.preventDefault();
            if (validatePaymentForm()) {
                document.getElementById('reservationForm').submit();
            }
        });
        function validatePaymentForm() {
    const cardNumber = document.getElementById('card_number').value.trim();
    const expiryDate = document.getElementById('expiry_date').value.trim();
    const cvv = document.getElementById('cvv').value.trim();

    let isValid = true;


    if (!validateCardNumber(cardNumber)) {
        document.getElementById('card_number').classList.add('is-invalid');
        isValid = false;
    } else {
        document.getElementById('card_number').classList.remove('is-invalid');
    }

   
    if (!validateExpiryDate(expiryDate)) {
        document.getElementById('expiry_date').classList.add('is-invalid');
        isValid = false;
    } else {
        document.getElementById('expiry_date').classList.remove('is-invalid');
    }

 
    if (!validateCVV(cvv)) {
        document.getElementById('cvv').classList.add('is-invalid');
        isValid = false;
    } else {
        document.getElementById('cvv').classList.remove('is-invalid');
    }

    return isValid;
}

function validateCardNumber(number) {
 
    number = number.replace(/\D/g, '');

   
    return /^\d{16}$/.test(number);
}

function validateExpiryDate(date) {
    
    if (!/^\d{2}\/\d{2}$/.test(date)) {
        return false;
    }

    const [month, year] = date.split('/').map(Number);

 
    const currentDate = new Date();
    const currentYear = currentDate.getFullYear() % 100;
    const currentMonth = currentDate.getMonth() + 1;

  
    return !(year < currentYear || (year === currentYear && month < currentMonth));
}

function validateCVV(cvv) {
    
    return /^\d{3,4}$/.test(cvv);
}

        
    </script>


    <script>
        document.getElementById('showMoreBtn').addEventListener('click', function () {
            const hiddenImages = document.querySelectorAll('.hidden-image');
            hiddenImages.forEach(function (image) {
                image.classList.remove('hidden-image');
            });
            document.getElementById('imageViewer').style.display = 'block';
        });

        function closeImageViewer() {
            const imageViewer = document.getElementById('imageViewer');
            imageViewer.style.display = 'none';

            // Hide all images again when closing viewer
            const hiddenImages = document.querySelectorAll('.hidden-image');
            hiddenImages.forEach(function (image) {
                image.classList.add('hidden-image');
            });
        }
    </script>


</body>

</html>