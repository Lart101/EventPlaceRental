<?php
session_start();

// Database connection parameters
$host = 'localhost';
$dbname = 'event_store';
$username = 'root';
$password = '';

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust the path as needed

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
        $packageName= $_POST['package_name'] ?? null;
        $inclusions= $_POST['inclusions'] ?? null;
        $addOns= $_POST['add_ons[]'] ?? null;
        $reservationFee= $_POST['reservationFee'] ?? null;
        $price= $_POST['price'] ?? null;



        if ($endDate === null) {
            // If end date is null, set it to start date
            $endDate = $_POST['start_date'] ?? null;
        }
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
                // Read file contents
                $fileContent = file_get_contents($uploadPath);
                // Store file contents in $proofOfPayment variable
                $proofOfPayment = $fileContent;
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

        // Fetch user email
        $stmtUser = $pdo->prepare("SELECT email FROM users WHERE id = ?");
        $stmtUser->execute([$userId]);
        $userEmail = $stmtUser->fetchColumn();
        
        // Send email receipt
        if ($userEmail) {
            $mail = new PHPMailer(true);
            
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'boardmart020@gmail.com';
            $mail->Password   = 'wojvwvhystherxdb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('boardmart020@gmail.com', 'Board Mart');
            
            // Recipient
            $mail->addAddress($userEmail);
            
            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Reservation Receipt';
            
            // Construct email body
            $mailBody = "<p>Dear Customer,</p>";
            $mailBody .= "<p>Thank you for your reservation with Board Mart!</p>";
            $mailBody .= "<p>Reservation Details:</p>";
            $mailBody .= "<ul>";
            $mailBody .= "<li>Package Name: $packageName</li>";
            $mailBody .= "<li>Inclusions: $inclusions</li>";
          
            $mailBody .= "<li>Start Date: $startDate</li>";
            $mailBody .= "<li>End Date: $endDate</li>";
            if (is_array($addOns) && empty($addOns)) {
                $mailBody .= '<li>No add ons</li>';
            } elseif (is_array($addOns)) {
               
                $mailBody .= '<li>Add Ons: ' . implode(', ', $addOns) . '</li>'; 
            } else {
                
                $mailBody .= '<li>Add Ons: ' . $addOns . '</li>';
            }
            $mailBody .= "<li>Package Price: $price</li>";
            $mailBody .= "<li>Reservation Fee: $reservationFee</li>";
            $mailBody .= "<li>Total Price: ₱$totalPrice</li>";
            
            $mailBody .= "</ul>";
            $mailBody .= "<p>Thank you for choosing Board Mart. If you have any questions, feel free to contact us.</p>";
            $mailBody .= "<p>Best Regards,<br>Board Mart Team</p>";
            
            $mail->Body    = $mailBody;
            
            $mail->send();
        } else {
            echo "User email not found.";
        }

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
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}
?>
