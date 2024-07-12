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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .receipt {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .receipt h1 {
            text-align: center;
        }
        .receipt p {
            margin-bottom: 10px;
        }
        .receipt strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <h1>Receipt</h1>
        <p><strong>Package Name:</strong> <?php echo htmlspecialchars($packageName); ?></p>
        <p><strong>Inclusions:</strong> <?php echo htmlspecialchars($inclusions); ?></p>
        <p><strong>Price:</strong> ₱<?php echo $price; ?></p>
        <p><strong>Duration:</strong> <?php echo htmlspecialchars($duration); ?></p>
        <p><strong>Max Pax:</strong> <?php echo htmlspecialchars($maxPax); ?></p>
        <p><strong>Start Date:</strong> <?php echo $startDate; ?></p>
        <?php if ($endDate) : ?>
            <p><strong>End Date:</strong> <?php echo $endDate; ?></p>
        <?php endif; ?>
        <?php if (!empty($addOns)) : ?>
            <p><strong>Add-ons:</strong> <?php echo implode(", ", $addOns); ?></p>
        <?php endif; ?>
        <p><strong>Total Price:</strong> ₱<?php echo number_format($totalPrice, 2); ?></p>
    </div>
</body>
</html>
