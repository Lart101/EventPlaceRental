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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $eventPlaceId = $_POST['event_place_id'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // Fetch price per day from event_places table
    $stmt = $conn->prepare("SELECT price_per_day FROM event_places WHERE id = ?");
    $stmt->bind_param("i", $eventPlaceId);
    $stmt->execute();
    $stmt->bind_result($pricePerDay);
    $stmt->fetch();
    $stmt->close();

    // Calculate total price and reservation fee
    $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;
    $totalPrice = $days * $pricePerDay;
    $reservationFee = $totalPrice * 0.05;

    // Insert reservation into reservations table
    $stmt = $conn->prepare("INSERT INTO reservations (user_id, event_place_id, start_date, end_date, total_price, reservation_fee) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissdd", $userId, $eventPlaceId, $startDate, $endDate, $totalPrice, $reservationFee);
    if ($stmt->execute()) {
        echo "Reservation successful!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
