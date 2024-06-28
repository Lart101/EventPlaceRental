<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   
</head>
<body>

<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "event_store");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get event place ID from URL
$eventPlaceId = $_GET['id'];

// Fetch event place details
$sql = "SELECT name, description, location, price_per_day, image FROM event_places WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventPlaceId);
$stmt->execute();
$stmt->bind_result($name, $description, $location, $price_per_day, $image);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<nav aria-label="breadcrumb" style="padding-top: 5%; margin-left: 20px;">
    <ol class="breadcrumb fade-in">
        <li class="breadcrumb-item"><a href="event.php">Event</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo $name; ?></li>
    </ol>
</nav>

<div class="container mt-5 pt-5 fade-in">
  <div class="row">
      <div class="col-md-6">
          <img src="img/products/<?php echo $image; ?>" class="img-fluid" alt="<?php echo $name; ?>">
      </div>
      <div class="col-md-6">
          <h1><?php echo $name; ?></h1>
          <p><?php echo $description; ?></p>
          <p><strong>Location:</strong> <?php echo $location; ?></p>
          <p><strong>Price per day:</strong> ₱<?php echo $price_per_day; ?></p>
          <form id="reservationForm" method="POST" action="reserve.php">
              <input type="hidden" name="event_place_id" value="<?php echo $eventPlaceId; ?>">
              <div class="mb-3">
                  <label for="start_date" class="form-label">Start Date:</label>
                  <input type="date" id="start_date" name="start_date" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label for="end_date" class="form-label">End Date:</label>
                  <input type="date" id="end_date" name="end_date" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary">Reserve Now</button>
          </form>
      </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script async data-id="9364713225" id="chatling-embed-script" type="text/javascript" src="https://chatling.ai/js/embed.js"></script>
</body>
</html>
