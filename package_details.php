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
    $end_date = isset($_POST['end_date']) ? htmlspecialchars(mysqli_real_escape_string($conn, $_POST['end_date'])) : null;

    // Fetch package type
    $stmt_type = $conn->prepare("SELECT package_type FROM swimming_packages WHERE id = ?");
    $stmt_type->bind_param("i", $packageId);
    $stmt_type->execute();
    $stmt_type->bind_result($package_type);
    $stmt_type->fetch();
    $stmt_type->close();

    // Validate dates based on package type
    if ($package_type == 'Combo' && empty($end_date)) {
        $_SESSION['reservation_error'] = "Please select an end date for Combo packages.";
        header("Location: package_details.php?id=$packageId");
        exit();
    }

    // Set end date for "Day" or "Overnight" packages
    if ($package_type == 'Day' || $package_type == 'Overnight') {
        $end_date = $start_date; // Assuming end date is the same as start date for these packages
    }

    $startTimestamp = strtotime($start_date);
    $endTimestamp = $end_date ? strtotime($end_date) : null;

    // Only validate end date for Combo packages
    if ($package_type == 'Combo' && $end_date && $endTimestamp <= $startTimestamp) {
        $_SESSION['reservation_error'] = "End date must be after start date.";
        header("Location: package_details.php?id=$packageId");
        exit();
    }

    // Check for overlapping reservations
    $overlap = false;
    if ($package_type == 'Day') {
        $reservedSql = "SELECT * FROM package_reservations WHERE package_id = ? AND status != 'cancelled' AND 
                       (start_date = ?)";
        $stmt_overlap = $conn->prepare($reservedSql);
        $stmt_overlap->bind_param("is", $packageId, $start_date);
    } else {
        $reservedSql = "SELECT * FROM package_reservations WHERE package_id = ? AND status != 'cancelled' AND 
                       ((start_date <= ? AND end_date >= ?) OR (start_date <= ? AND end_date >= ?) OR (start_date >= ? AND end_date <= ?))";
        $stmt_overlap = $conn->prepare($reservedSql);
        $stmt_overlap->bind_param("issssss", $packageId, $start_date, $end_date, $start_date, $end_date, $start_date, $end_date);
    }

    $stmt_overlap->execute();
    $stmt_overlap->store_result();
    if ($stmt_overlap->num_rows > 0) {
        $overlap = true;
    }
    $stmt_overlap->close();

    if ($overlap) {
        $_SESSION['reservation_error'] = "Selected dates overlap with existing reservations.";
        header("Location: package_details.php?id=$packageId");
        exit();
    }

    // Calculate total price based on package type and add-ons
    $sql_price = "SELECT price FROM swimming_packages WHERE id = ?";
    $stmt_price = $conn->prepare($sql_price);
    $stmt_price->bind_param("i", $packageId);
    $stmt_price->execute();
    $stmt_price->bind_result($base_price);
    $stmt_price->fetch();
    $stmt_price->close();

    $totalPrice = $base_price;

    if ($package_type == 'Combo') {
        $totalPrice *= ceil(($endTimestamp - $startTimestamp) / (60 * 60 * 24));
    }

    $totalPrice += $base_price * 0.05; // Reservation fee

    // Insert reservation with status 'pending'
    if ($package_type == 'Day') {
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

    if ($result) {
        $_SESSION['reservation_success'] = true;
    } else {
        $_SESSION['reservation_success'] = false;
    }

    header("Location: package_details.php?id=$packageId");
    exit();
}

// Fetch package details
$packageId = htmlspecialchars(mysqli_real_escape_string($conn, $_GET['id']));
$sql_package = "SELECT package_name, inclusions, price, duration, max_pax, multiple_images, package_type FROM swimming_packages WHERE id = ?";
$stmt_package = $conn->prepare($sql_package);
$stmt_package->bind_param("i", $packageId);
$stmt_package->execute();
$stmt_package->bind_result($package_name, $inclusions, $price, $duration, $max_pax, $multiple_images, $package_type);
$stmt_package->fetch();
$stmt_package->close();

$packageImages = explode(",", $multiple_images);

// Fetch reserved dates
$reservedDates = [];
$sql_reserved = "SELECT start_date, end_date FROM package_reservations WHERE package_id = ? AND status != 'cancelled'";
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
                    foreach ($packageImages as $image) {
                        echo '<img src="' . $image . '" class="img-fluid" alt="' . $package_name . '" onclick="openImageViewer(' . $counter . ')">';
                        $counter++;
                    }
                    ?>
                    <span id="showMoreBtn" class="text-primary" onclick="showAllImages()">View More</span>
                </div>
                <div class="image-viewer" id="imageViewer">
                    <span class="close-btn" onclick="closeImageViewer()">&times;</span>
                    <?php
                    foreach ($packageImages as $image) {
                        echo '<img src="' . $image . '" class="img-fluid" alt="' . $package_name . '">';
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <h1><?php echo $package_name; ?></h1>
                <p><?php echo $inclusions; ?></p>
                <p><strong>Price:</strong> <?php echo $price; ?></p>
                <p><strong>Duration:</strong> <?php echo $duration; ?></p>
                <p><strong>Max Pax:</strong> <?php echo $max_pax; ?></p>
                <form method="POST" action="package_details.php">
                    <input type="hidden" name="package_id" value="<?php echo $packageId; ?>">
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
                    <button type="submit" class="btn btn-primary">Reserve Now</button>
                </form>
                <?php if (isset($_SESSION['reservation_error'])): ?>
                    <div class="alert alert-danger mt-3">
                        <?php
                        echo $_SESSION['reservation_error'];
                        unset($_SESSION['reservation_error']);
                        ?>
                    </div>
                <?php elseif (isset($_SESSION['reservation_success']) && $_SESSION['reservation_success']): ?>
                    <div class="alert alert-success mt-3">
                        Reservation successful. Your reservation is now pending for admin review.
                        <?php unset($_SESSION['reservation_success']); ?>
                    </div>
                <?php endif; ?>
                <div class="reserved-dates">
                    <h5>Reserved Dates:</h5>
                    <ul>
                        <?php foreach ($reservedDates as $dateRange): ?>
                            <li>
                                <?php echo $dateRange['start_date']; ?>
                                <?php if ($dateRange['end_date']): ?>
                                    - <?php echo $dateRange['end_date']; ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script>
        function openImageViewer(index) {
            var viewer = document.getElementById('imageViewer');
            var images = viewer.getElementsByTagName('img');
            for (var i = 0; i < images.length; i++) {
                images[i].style.display = 'none';
            }
            images[index].style.display = 'block';
            viewer.style.display = 'block';
        }

        function closeImageViewer() {
            document.getElementById('imageViewer').style.display = 'none';
        }

        function showAllImages() {
            var collage = document.getElementById('imageCollage');
            var images = collage.getElementsByTagName('img');
            for (var i = 0; i < images.length; i++) {
                images[i].style.display = 'inline-block';
            }
            document.getElementById('showMoreBtn').style.display = 'none';
        }
    </script>
</body>
</html>
