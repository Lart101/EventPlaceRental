<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get error message if it exists
$error_message = isset($_SESSION['reservation_error']) ? $_SESSION['reservation_error'] : 'An unknown error occurred. Please try again later.';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Failed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link href="default.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
        }

        .container {
            margin-top: 50px;
            text-align: center;
        }

        .card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }

        .btn-back {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-back:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>

<body>
<?php include 'user_navbar.php'; ?>
    <div class="container">
        <div class="card" style="margin-bottom:10%;">
            <h1 class="card-title">Reservation Failed</h1>
            <p class="card-text"><?php echo $error_message; ?></p>
            <a href="swimming_packages.php" class="btn btn-lg btn-primary btn-back">Back to Package</a>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>

<?php
// Clear the error message after displaying it
unset($_SESSION['reservation_error']);
?>
