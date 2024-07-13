<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Success</title>
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
<nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-lg">
            <a class="navbar-brand" href="index.html">
                <img src="img\profile\logo.jpg" alt="Logo" width="30" class="d-inline-block align-text-top">
                Board Mart Event Place
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="mx-auto">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index1.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="swimming_packages.php">Packages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contactmain.php">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profilecopy.php">Profile</a>
                        </li>
                        <?php

                        if (!isset($_SESSION['user_id'])):
                            ?>

                            <li class="nav-item login">
                                <a class="nav-link" href="login.php">Login</a>
                            </li>
                        <?php else: ?>

                            <li class="nav-item logout">
                                <form action="logout.php" method="POST">
                                    <button type="submit" class="nav-link btn btn-link"
                                        onclick="return confirmLogout()">Logout</button>
                                </form>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="card">
            <h1 class="card-title">Reservation Successful!</h1>
            <p class="card-text">Thank you for making a reservation with us.</p>
            <a href="index1.php" class="btn btn-lg btn-primary btn-back">Back to Home</a>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>
