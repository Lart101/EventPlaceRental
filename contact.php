<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Unknown error'];

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message']) && isset($_POST['rating'])) {
            
            $name = $_POST['name'];
            $email = $_POST['email'];
            $message = $_POST['message'];
            $rating = $_POST['rating'];

            $mail = new PHPMailer(true);

           
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'boardmart020@gmail.com';
            $mail->Password   = 'wojvwvhystherxdb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

           
            $mail->setFrom('boardmart020@gmail.com', 'Board Mart');
            $mail->addAddress('boardmart020@gmail.com', 'Recipient Name');

           
            $mail->isHTML(true);
            $mail->Subject = 'New Contact Form Submission';
            $mail->Body    = "<p>Name: $name</p><p>Email: $email</p><p>Message: $message</p><p>Rating: $rating</p>";
            $mail->AltBody = "Name: $name\nEmail: $email\nMessage: $message\nRating: $rating";

            
            $mail->send();
            $response = ['status' => 'success', 'message' => 'Your message and review have been sent successfully!'];
        } else {
            $response['message'] = 'Please fill in all fields.';
        }
    } else {
        $response['message'] = 'Invalid request method.';
    }
} catch (Exception $e) {
    $response['message'] = "Mailer Error: {$e->getMessage()}";
}

echo json_encode($response);
?>
