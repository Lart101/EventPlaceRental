<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    Reservation Receipt
                </div>
                <div class="card-body">
                    <?php
                    $eventPlaceId = $_GET['event_place_id'];
                    $startDate = $_GET['start_date'];
                    $endDate = $_GET['end_date'];
                    $totalCost = $_GET['total_cost'];

                    // Database connection
                    $conn = new mysqli("localhost", "root", "", "event_store");

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Fetch event place details
                    $sql = "SELECT name, location, price_per_day FROM event_places WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $eventPlaceId);
                    $stmt->execute();
                    $stmt->bind_result($name, $location, $pricePerDay);
                    $stmt->fetch();
                    $stmt->close();
                    $conn->close();
                    ?>

                    <h5 class="card-title">Event Place: <?php echo $name; ?></h5>
                    <p class="card-text"><strong>Location:</strong> <?php echo $location; ?></p>
                    <p class="card-text"><strong>Price per day:</strong> ₱<?php echo $pricePerDay; ?></p>
                    <p class="card-text"><strong>Start Date:</strong> <?php echo $startDate; ?></p>
                    <p class="card-text"><strong>End Date:</strong> <?php echo $endDate; ?></p>
                    <p class="card-text"><strong>Total Cost:</strong> ₱<?php echo $totalCost; ?></p>
                </div>
                <div class="card-footer">
                    <a href="event.php" class="btn btn-primary">Back to Events</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
