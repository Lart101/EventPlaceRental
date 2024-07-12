<?php
session_start();

// Database connection parameters
$host = 'localhost';
$dbname = 'event_store';
$username = 'root';
$password = '';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $packageId = $_POST['package_id'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;
        $addOns = isset($_POST['add_ons']) ? $_POST['add_ons'] : [];
        $extendedStayHours = isset($_POST['extended_stay_hours']) ? $_POST['extended_stay_hours'] : 0;
        $totalPrice = $_POST['total_price'] ?? 0;

        // Handle file upload for proof of payment
        $proofOfPayment = null;
        if (isset($_FILES['proof_of_payment'])) {
            $uploadDir = 'uploads/'; // Directory to store uploaded files
            $fileName = basename($_FILES['proof_of_payment']['name']);
            $uploadPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $uploadPath)) {
                $proofOfPayment = $uploadPath;
            } else {
                echo "Failed to upload file.";
            }
        }

        // Set default status
        $status = 'Pending'; // Adjust as per your application's logic

        // Insert reservation into database
        $stmt = $pdo->prepare("INSERT INTO package_reservations (package_id, user_id, start_date, end_date, add_ons, extend_stay, total_price, proof_of_payment, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$packageId, $userId, $startDate, $endDate, json_encode($addOns), $extendedStayHours, $totalPrice, $proofOfPayment, $status]);

        $reservationId = $pdo->lastInsertId();

        // Redirect to success page after successful reservation
        header('Location: reservation_success.php');
        exit();
    } else {
        // Redirect if accessed directly without form submission
        header('Location: index.php');
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
