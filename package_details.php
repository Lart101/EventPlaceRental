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

// Fetch package details
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid package ID.');
}
$packageId = intval($_GET['id']);

$sql_package = "SELECT package_name, inclusions, price, duration, max_pax, multiple_images, package_type FROM swimming_packages WHERE id = ?";
$stmt_package = $conn->prepare($sql_package);
$stmt_package->bind_param("i", $packageId);
$stmt_package->execute();
$stmt_package->bind_result($package_name, $inclusions, $price, $duration, $max_pax, $multiple_images, $package_type);
$stmt_package->fetch();
$stmt_package->close();

if (!$package_name) {
    die('Package not found.');
}

$packageImages = explode(",", $multiple_images);

// Fetch reserved dates
$reservedDates = [];
$sql_reserved = "SELECT start_date, end_date FROM package_reservations WHERE package_id = ? AND status = 'Accepted'";
$stmt_reserved = $conn->prepare($sql_reserved);
$stmt_reserved->bind_param("i", $packageId);
$stmt_reserved->execute();
$stmt_reserved->bind_result($start_date, $end_date);
while ($stmt_reserved->fetch()) {
    $reservedDates[] = ['start_date' => $start_date, 'end_date' => $end_date];
}
$stmt_reserved->close();

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <style>
        .readonly-input {
        background-color: #f8f9fa; /* Light gray background */
        cursor: not-allowed; /* Show disabled cursor */
    }
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
                <h1><?php echo htmlspecialchars($package_name); ?></h1>
                <p><?php echo htmlspecialchars($inclusions); ?></p>
                <p><strong>Price:</strong> <?php echo htmlspecialchars($price); ?></p>
                <p><strong>Duration:</strong> <?php echo htmlspecialchars($duration); ?></p>
                <p><strong>Max Pax:</strong> <?php echo htmlspecialchars($max_pax); ?></p>
                <form method="POST" action="showinfo.php">

                    <input type="hidden" name="package_id" value="<?php echo $packageId; ?>">
                    <input type="hidden" name="package_name" value="<?php echo htmlspecialchars($package_name); ?>">
                    <input type="hidden" name="inclusions" value="<?php echo htmlspecialchars($inclusions); ?>">
                    <input type="hidden" name="price" value="<?php echo $price; ?>">
                    <input type="hidden" name="duration" value="<?php echo htmlspecialchars($duration); ?>">
                    <input type="hidden" name="max_pax" value="<?php echo htmlspecialchars($max_pax); ?>">

                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>

                    <?php if ($package_type == 'Combo'): ?>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    <?php endif; ?>

                    <!-- Add-ons section -->
                    <div class="mb-3">
                        <label class="form-label">Add-ons</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extendedStay" name="add_ons[]"
                                value="Extended stay" onchange="calculateTotalPrice()">
                            <label class="form-check-label" for="extendedStay">Extended stay (₱1,000 per hour)</label>
                            <input type="number" id="extendedStayHours" name="extended_stay_hours"
                                class="form-control mt-2" min="0" value="0" onchange="calculateTotalPrice()">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="familyRoom" name="add_ons[]"
                                value="Family Room" onchange="calculateTotalPrice()">
                            <label class="form-check-label" for="familyRoom">Family Room (₱3,000)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="suiteRoom" name="add_ons[]"
                                value="Suite Room" onchange="calculateTotalPrice()">
                            <label class="form-check-label" for="suiteRoom">Suite Room (₱5,000)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="videokeGameRoom" name="add_ons[]"
                                value="Videoke and Game Room" onchange="calculateTotalPrice()">
                            <label class="form-check-label" for="videokeGameRoom">Videoke and Game Room (₱3,000)</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <p><strong>Reservation Fee:</strong> 5000</p>
                        <label for="totalPrice" class="form-label">Total Price</label>
                        <input type="text" id="totalPrice" class="form-control" readonly>
                    </div>

                    <button type="submit" class="btn btn-primary">Pay Now</button>
                </form>

                <div class="reserved-dates">
                    <h5>Reserved Dates:</h5>
                    <ul>
                        <?php foreach ($reservedDates as $dateRange): ?>
                            <li>
                                <?php echo htmlspecialchars($dateRange['start_date']); ?>
                                <?php if ($dateRange['end_date']): ?>
                                    - <?php echo htmlspecialchars($dateRange['end_date']); ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
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

    <div class="image-viewer" id="imageViewer">
        <?php foreach ($packageImages as $index => $image): ?>
            <img src="<?php echo $image; ?>" class="img-fluid hidden-image" alt="<?php echo $package_name; ?>">
        <?php endforeach; ?>
        <span class="close-btn" onclick="closeImageViewer()">&times;</span>
    </div>
    <script>
        document.getElementById('add_on1').addEventListener('change', function () {
            var extendedStayHoursInput = document.getElementById('extended_stay_hours');
            if (this.checked) {
                extendedStayHoursInput.setAttribute('required', 'required');
            } else {
                extendedStayHoursInput.removeAttribute('required');
            }
        });
    </script>

    <script>
        function calculateTotalPrice() {
            let basePrice = <?php echo $price; ?>;
            let reservationFee = 5000;
            let totalPrice = basePrice;

            <?php if ($package_type == 'Combo'): ?>
                let startDate = new Date(document.getElementById('start_date').value);
                let endDate = new Date(document.getElementById('end_date').value);
                let days = (endDate - startDate) / (1000 * 60 * 60 * 24);
                totalPrice *= Math.ceil(days);
            <?php endif; ?>

            let extendedStayChecked = document.getElementById('extendedStay').checked;
            if (extendedStayChecked) {
                let extendedStayHours = parseInt(document.getElementById('extendedStayHours').value, 10);
                totalPrice += 1000 * extendedStayHours;
            }

            let familyRoomChecked = document.getElementById('familyRoom').checked;
            if (familyRoomChecked) {
                totalPrice += 3000;
            }

            let suiteRoomChecked = document.getElementById('suiteRoom').checked;
            if (suiteRoomChecked) {
                totalPrice += 5000;
            }

            let videokeGameRoomChecked = document.getElementById('videokeGameRoom').checked;
            if (videokeGameRoomChecked) {
                totalPrice += 3000;
            }

            totalPrice += reservationFee;

            document.getElementById('totalPrice').value = '₱' + totalPrice.toLocaleString();
        }

        // Event listeners for date inputs
        document.getElementById('start_date').addEventListener('change', calculateTotalPrice);
        document.getElementById('end_date').addEventListener('change', calculateTotalPrice);

        // Initial calculation on page load
        document.addEventListener('DOMContentLoaded', function () {
            calculateTotalPrice();
        });
    </script>

<div class="mb-3">
    <label for="start_date" class="form-label">Start Date</label>
    <input type="date" class="form-control" id="start_date" name="start_date" required>
</div>

<?php if ($package_type == 'Combo'): ?>
    <div class="mb-3">
        <label for="end_date" class="form-label">End Date</label>
        <input type="date" class="form-control" id="end_date" name="end_date" required disabled>
    </div>
<?php endif; ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        // Function to enable/disable end date based on start date selection
        function toggleEndDate() {
            if (startDateInput.value) {
                endDateInput.disabled = false; // Enable end date input
                endDateInput.min = startDateInput.value; // Set minimum date for end date
            } else {
                endDateInput.disabled = true; // Disable end date input
                endDateInput.value = ''; // Reset end date value
            }
        }

        // Event listener on start date input
        startDateInput.addEventListener('change', function() {
            toggleEndDate();
        });

        // Event listener on end date input to prevent selecting a date before start date
        endDateInput.addEventListener('change', function() {
            if (endDateInput.value < startDateInput.value) {
                endDateInput.value = startDateInput.value;
            }
        });

        // Ensure start date cannot be a past date
        const today = new Date().toISOString().split('T')[0];
        startDateInput.setAttribute('min', today);

        // Additional event listener to clear end date if start date changes
        startDateInput.addEventListener('input', function() {
            endDateInput.value = ''; // Clear end date value
            toggleEndDate(); // Update end date input state
        });

        // Initial setup based on start date value
        toggleEndDate();
    });
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