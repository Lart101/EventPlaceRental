<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor\phpmailer\phpmailer\src\Exception.php';
require 'vendor\phpmailer\phpmailer\src\PHPMailer.php';
require 'vendor\phpmailer\phpmailer\src\SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $rating = isset($_POST['rating']) ? $_POST['rating'] : '';
    
  
    $mail = new PHPMailer(true);

    try {
       
        $mail->isSMTP();                                      
        $mail->Host       = 'smtp.gmail.com';                  
        $mail->SMTPAuth   = true;                             
        $mail->Username   = 'boardmart020@gmail.com';         
        $mail->Password   = 'wojvwvhystherxdb';                   
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   
        $mail->Port       = 587;                              

        
        $mail->setFrom('boardmart020@gmail.com', 'Board Mart');
        $mail->addAddress('boardmart020@gmail.com', 'Feedback'); 

       
        $mail->isHTML(true);                                  
        $mail->Subject = 'New Review and Feedback Submission';
        $mail->Body    = "<p>Rating: $rating</p>";
        $mail->AltBody = "Rating: $rating";

       
        $mail->send();
        echo 'Review submitted successfully. Thank you!';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
