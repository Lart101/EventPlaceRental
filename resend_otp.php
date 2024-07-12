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
    $email = ''; // Actual email fetching logic will go here

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
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'boardmart020@gmail.com';
            $mail->Password = 'wojvwvhystherxdb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email content
            $mail->setFrom('boardmart020@gmail.com', 'Board Mart');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "<p>Dear User,</p><p>Your OTP for password reset is: <strong>$otp</strong>.</p><p>If you did not request this OTP, please ignore this email.</p>";
            $mail->AltBody = "Your OTP for password reset is $otp.";

            // Send email
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
