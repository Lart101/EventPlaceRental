<?php
session_start();

$conn = new mysqli("localhost", "root", "", "event_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = ['status' => 'error', 'message' => 'Unknown error'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['otp'], $_POST['new_password'], $_POST['confirm_password'])) {
    $otp = $_POST['otp'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $response['message'] = 'Passwords do not match!';
    } else {
        if ($otp == $_SESSION['otp']) {
            $userId = $_SESSION['user_id'];
            $hashedPassword = $newPassword;

            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $hashedPassword, $userId);

            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Password successfully reset. Redirecting to login...'];
            } else {
                $response['message'] = 'Failed to reset password.';
            }

            $stmt->close();
        } else {
            $response['message'] = 'Invalid OTP.';
        }
    }
}

$conn->close();
echo json_encode($response);
?>
