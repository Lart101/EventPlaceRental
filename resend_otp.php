<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Autoload the PHPMailer classes

$conn = new mysqli("localhost", "root", "", "event_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = ['status' => 'error', 'message' => 'Unknown error'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $email = 'user_email@example.com'; // Replace with the actual email fetching logic

    $sql = "SELECT email FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();

    if ($email) {
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'boardmart020@gmail.com';
            $mail->Password = 'wojvwvhystherxdb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('boardmart020@gmail.com', 'Board Mart');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "Your OTP for password reset is $otp.";
            $mail->AltBody = "Your OTP for password reset is $otp.";

            $mail->send();
            $response = ['status' => 'success', 'message' => 'OTP resent to your email.'];
        } catch (Exception $e) {
            $response['message'] = "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $response['message'] = 'Failed to retrieve user email.';
    }

    $stmt->close();
}

$conn->close();
echo json_encode($response);
?>
