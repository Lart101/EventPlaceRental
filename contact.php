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
        if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message'])) {
            // Process Contact Form
            $name = $_POST['name'];
            $email = $_POST['email'];
            $message = $_POST['message'];

            // Instantiate PHPMailer for contact form
            $mailContact = new PHPMailer(true);

            // Server settings
            $mailContact->isSMTP();
            $mailContact->Host       = 'smtp.gmail.com';
            $mailContact->SMTPAuth   = true;
            $mailContact->Username   = 'boardmart020@gmail.com';
            $mailContact->Password   = 'wojvwvhystherxdb';
            $mailContact->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mailContact->Port       = 587;

            // Recipients
            $mailContact->setFrom('boardmart020@gmail.com', 'Board Mart');
            $mailContact->addAddress('boardmart020@gmail.com', 'Recipient Name');

            // Content
            $mailContact->isHTML(true);
            $mailContact->Subject = 'New Contact Form Submission';
            $mailContact->Body    = "<p>Name: $name</p><p>Email: $email</p><p>Message: $message</p>";
            $mailContact->AltBody = "Name: $name\nEmail: $email\nMessage: $message";

            // Send email
            $mailContact->send();
            $response = ['status' => 'success', 'message' => 'Contact message has been sent successfully!'];
        } elseif (isset($_POST['rating'])) {
            // Process Review Form
            $rating = $_POST['rating'];

            // Instantiate PHPMailer for review form
            $mailReview = new PHPMailer(true);

            // Server settings
            $mailReview->isSMTP();
            $mailReview->Host       = 'smtp.gmail.com';
            $mailReview->SMTPAuth   = true;
            $mailReview->Username   = 'boardmart020@gmail.com';
            $mailReview->Password   = 'wojvwvhystherxdb';
            $mailReview->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mailReview->Port       = 587;

            // Recipients
            $mailReview->setFrom('boardmart020@gmail.com', 'Board Mart');
            $mailReview->addAddress('boardmart020@gmail.com', 'Feedback');

            // Content
            $mailReview->isHTML(true);
            $mailReview->Subject = 'New Review and Feedback Submission';
            $mailReview->Body    = "<p>Rating: $rating</p>";
            $mailReview->AltBody = "Rating: $rating";

            // Send email
            $mailReview->send();
            $response = ['status' => 'success', 'message' => 'Review submitted successfully. Thank you!'];
        } else {
            $response['message'] = 'Invalid form submission.';
        }
    } else {
        $response['message'] = 'Invalid request method.';
    }
} catch (Exception $e) {
    $response['message'] = "Mailer Error: {$e->getMessage()}";
}

echo json_encode($response);
?>
