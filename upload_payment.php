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
    $reservationId = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['reservation_id']));
    $targetDir = "uploads/";
    $fileName = basename($_FILES["proof_of_payment"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Allow certain file formats
    $allowTypes = array('jpg', 'png', 'jpeg', 'pdf');
    if (in_array($fileType, $allowTypes)) {
        if (move_uploaded_file($_FILES["proof_of_payment"]["tmp_name"], $targetFilePath)) {
            // Insert file name into database
            $stmt = $conn->prepare("UPDATE package_reservations SET proof_of_payment = ?, status = 'pending' WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sii", $fileName, $reservationId, $_SESSION['user_id']);
            if ($stmt->execute()) {
                $_SESSION['upload_success'] = true;
            } else {
                $_SESSION['upload_success'] = false;
            }
            $stmt->close();
        } else {
            $_SESSION['upload_success'] = false;
        }
    } else {
        $_SESSION['upload_success'] = false;
    }
}

header('Location: payment_confirmation.php');
exit();
?>
