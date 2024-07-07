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

    
    $sql = "SELECT price FROM swimming_packages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $packageId);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();

    $totalPrice = ($price * (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24)) + $price * 0.05;

    



    $insertSql = "INSERT INTO package_reservations (package_id, user_id, start_date, end_date, total_price) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("iisss", $packageId, $_SESSION['user_id'], $start_date, $end_date, $totalPrice);
    $result = $stmt->execute();
    $stmt->close();


    $_SESSION['reservation_success'] = $result;

 
    header("Location: package_details.php?id=$packageId");
    exit();
}


$packageId = htmlspecialchars(mysqli_real_escape_string($conn, $_GET['id']));
$sql = "SELECT package_name, inclusions, price, duration, max_pax, multiple_images FROM swimming_packages WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $packageId);
$stmt->execute();
$stmt->bind_result($package_name, $inclusions, $price, $duration, $max_pax, $multiple_images);
$stmt->fetch();
$stmt->close();


$packageImages = explode(",", $multiple_images);

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
            <a class="navbar-brand" href="#">
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
            <form id="reservationForm" method="POST" action="package_details.php?id=<?php echo $packageId; ?>" onsubmit="return validateReservation()">
                <input type="hidden" name="package_id" value="<?php echo $packageId; ?>">
                <input type="hidden" id="base_price" value="<?php echo $price; ?>">
                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
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

                <button type="submit" class="btn btn-primary">Reserve Now</button>
            </form>
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
        <img src="<?php echo $image; ?>" class="img-fluid <?php if ($index >= 4) echo 'hidden-image'; ?>" alt="<?php echo $package_name; ?>">
    <?php endforeach; ?>
    <span class="close-btn" onclick="closeImageViewer()">&times;</span>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    function calculatePrices() {
        const basePrice = parseFloat(document.getElementById('base_price').value);
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(document.getElementById('end_date').value);

        if (startDate && endDate && endDate >= startDate) {
            const days = (endDate - startDate) / (1000 * 60 * 60 * 24) + 1;
            let totalPrice = days * basePrice;

            

           

            const reservationFee = totalPrice * 0.05;

            // Display calculated prices
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
                    endDateInput.value = ''; // Clear end date if overlap found
                    alert('This date range includes already reserved dates. Please select another range.');
                    return;
                }
            }
        }
    }

    function validateReservation() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        if (endDateInput.disabled) {
            alert('Please select valid dates.');
            return false;
        }

        // Check for overlapping dates
        disableReservedDates();

        // If no overlap, proceed with form submission
        return true;
    }
</script>
<script>
    document.getElementById('showMoreBtn').addEventListener('click', function() {
        const hiddenImages = document.querySelectorAll('.hidden-image');
        hiddenImages.forEach(function(image) {
            image.classList.remove('hidden-image');
        });
        document.getElementById('imageViewer').style.display = 'block';
    });

    function closeImageViewer() {
        const imageViewer = document.getElementById('imageViewer');
        imageViewer.style.display = 'none';

        // Hide all images again when closing viewer
        const hiddenImages = document.querySelectorAll('.hidden-image');
        hiddenImages.forEach(function(image) {
            image.classList.add('hidden-image');
        });
    }
</script>


</body>

</html>