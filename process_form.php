<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'vendor\phpmailer\phpmailer\src\Exception.php';
require 'vendor\phpmailer\phpmailer\src\PHPMailer.php';
require 'vendor\phpmailer\phpmailer\src\SMTP.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Instantiate PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                    
        $mail->Host       = 'smtp.gmail.com';                
        $mail->SMTPAuth   = true;                          
        $mail->Username   = 'boardmart020@gmail.com';        
        $mail->Password   = 'wojvwvhystherxdb';                 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   
        $mail->Port       = 587;                             

        // Recipients
        $mail->setFrom('boardmart020@gmail.com', 'Board Mart');  
        $mail->addAddress('boardmart020@gmail.com', 'Recipient Name'); 

        // Content
        $mail->isHTML(true);                                  
        $mail->Subject = 'New Contact Form Submission';
        $mail->Body    = "<p>Name: $name</p><p>Email: $email</p><p>Message: $message</p>";
        $mail->AltBody = "Name: $name\nEmail: $email\nMessage: $message";

        // Send email
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
