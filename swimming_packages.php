<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swimming Packages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="event.css" rel="stylesheet">
</head>
<body>

<section class="bg" id="hero">
    <div class="container-lg" style="margin-bottom: 20%;">
        <div class="row align-items-center">
            <div class="col-sm-6 fade-in">
                <h1 class="display- fw-bold" style="color: #FFFAB7;">Swimming Packages</h1>
                <p style="color: whitesmoke;">Choose your preferred swimming package!</p>
                <!-- Link to reserve or manage packages if needed -->
            </div>
        </div>
    </div>
</section>

<div class="container">
    <section id="packages" class="py-5 fade-in">
        <div class="container">
            <h1 class="my-4 text-center">Our Packages</h1>
            <div class="row">
                <?php
                // Database connection
                $conn = new mysqli("localhost", "root", "", "event_store");

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch swimming packages
                $sql = "SELECT id, package_name, price, duration, max_pax, profile_image FROM swimming_packages";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '
                        <div class="col-md-4">
                            <div class="card">
                                <img src="' . $row["profile_image"] . '" class="card-img-top" alt="' . $row["package_name"] . '">
                                <div class="card-body">
                                    <h5 class="card-title">' . $row["package_name"] . '</h5>
                                    <p class="card-text">Price: $' . $row["price"] . '<br>
                                    Duration: ' . $row["duration"] . '<br>
                                    Max Pax: ' . $row["max_pax"] . '</p>
                                    <a href="package_details.php?id=' . $row["id"] . '" class="btn btn-outline-dark">See Details</a>
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    echo "<p class='text-center'>No swimming packages found.</p>";
                }

                $conn->close();
                ?>
            </div>
        </div>
    </section>
</div>

<div id="notification" class="notification"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
