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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userId);
    $stmt->fetch();

    if ($userId) {
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['user_id'] = $userId;

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
            $response = ['status' => 'success', 'message' => 'OTP sent to your email.'];
        } catch (Exception $e) {
            $response['message'] = "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $response['message'] = 'No user found with this email.';
    }

    $stmt->close();
}

$conn->close();
echo json_encode($response);
?>
