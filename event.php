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
    <title>Event Venue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="event.css" rel="stylesheet">
</head>
<body>

<section class="bg" id="hero">
    <div class="container-lg" style="margin-bottom: 20%;">
        <div class="row align-items-center">
            <div class="col-sm-6 fade-in">
                <h1 class="display- fw-bold" style="color: #FFFAB7;">Event Venue Rental</h1>
                <p style="color: whitesmoke;">Providing high-end places for your event!</p>
                <a class="btn btn-outline-light btn-lg" href="#products">Reserve NOW</a>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <section id="products" class="py-5 fade-in">
        <div class="container">
            <h1 class="my-4 text-center">Our Venue</h1>
            <div class="row">
                <?php
                // Database connection
                $conn = new mysqli("localhost", "root", "", "event_store");

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch event places
                $sql = "SELECT id, name, description, location, price_per_day, image FROM event_places";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '
                        <div class="col-md-4">
                            <div class="card">
                                <img src="' . $row["image"] . '" class="card-img-top" alt="' . $row["name"] . '">
                                <div class="card-body">
                                    <h5 class="card-title">' . $row["name"] . '</h5>
                                    <p class="card-text">' . $row["description"] . '</p>
                                    <a href="event_place.php?id=' . $row["id"] . '" class="btn btn-outline-dark btn-buy">Reserve Now</a>
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    echo "<p class='text-center'>No event places found.</p>";
                }

                $conn->close();
                ?>
            </div>
        </div>
    </section>
</div>

<div id="notification" class="notification"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>