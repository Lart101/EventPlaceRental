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
// Session check and database connection code
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_SESSION['reservation_success']) && $_SESSION['reservation_success']) {
    // Display JavaScript alert for success notification
    echo '<script>alert("Reservation successful!");</script>';

    // Unset the session variable to avoid displaying the alert on page refresh
    unset($_SESSION['reservation_success']);
}

$conn = new mysqli("localhost", "root", "", "event_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$eventPlaceId = $_GET['id'];

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
            <img src="<?php echo $image; ?>" class="img-fluid" alt="<?php echo $name; ?>">
        </div>
        <div class="col-md-6">
            <h1><?php echo $name; ?></h1>
            <p><?php echo $description; ?></p>
            <p><strong>Location:</strong> <?php echo $location; ?></p>
            <p><strong>Price per day:</strong> ₱<?php echo $price_per_day; ?></p>
            <form id="reservationForm" method="POST" action="reserve.php" onsubmit="return showReservationModal()">
                <input type="hidden" name="event_place_id" value="<?php echo $eventPlaceId; ?>">
                <input type="hidden" id="price_per_day" value="<?php echo $price_per_day; ?>">
                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="end_date" class="form-label">End Date:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" disabled required>
                </div>
                <div class="mb-3">
                    <label for="total_price" class="form-label">Total Price:</label>
                    <input type="text" id="total_price" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label for="reservation_fee" class="form-label">Reservation Fee (5%):</label>
                    <input type="text" id="reservation_fee" class="form-control" readonly>
                </div>
                <button type="submit" class="btn btn-primary">Reserve Now</button>
            </form>
        </div>
    </div>
</div>

<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reservationModalLabel">Reservation Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-7">
                            <h5>Total Price: <span id="modalTotalPrice"></span></h5>
                            <h5>Reservation Fee: <span id="modalReservationFee"></span></h5>
                        </div>
                        <div class="col-md-5">
                            <img src="<?php echo $image; ?>" class="img-fluid" alt="<?php echo $name; ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmReservation()">Confirm Reservation</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script async data-id="9364713225" id="chatling-embed-script" type="text/javascript" src="https://chatling.ai/js/embed.js"></script>
<script>
    function calculatePrices() {
        const pricePerDay = parseFloat(document.getElementById('price_per_day').value);
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(document.getElementById('end_date').value);

        if (startDate && endDate && endDate >= startDate) {
            const days = (endDate - startDate) / (1000 * 60 * 60 * 24) + 1; // Include end date
            const totalPrice = days * pricePerDay;
            const reservationFee = totalPrice * 0.05;

            document.getElementById('total_price').value = '₱' + totalPrice.toFixed(2);
            document.getElementById('reservation_fee').value = '₱' + reservationFee.toFixed(2);
        }
    }

    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = new Date(this.value);
        const endDateInput = document.getElementById('end_date');

        endDateInput.disabled = false; // Enable end date input
        endDateInput.min = this.value; // Set min date for end date based on start date

        // Clear end date value if it's before the start date
        if (endDateInput.value && new Date(endDateInput.value) < startDate) {
            endDateInput.value = '';
            calculatePrices(); // Recalculate prices if end date is cleared
        }
    });

    document.getElementById('end_date').addEventListener('change', calculatePrices);

    function showReservationModal() {
        calculatePrices(); // Calculate prices before showing modal
        const totalPrice = document.getElementById('total_price').value;
        const reservationFee = document.getElementById('reservation_fee').value;

        document.getElementById('modalTotalPrice').textContent = totalPrice;
        document.getElementById('modalReservationFee').textContent = reservationFee;

        var myModal = new bootstrap.Modal(document.getElementById('reservationModal'));
        myModal.show();

        return false; // Prevent form submission for now
    }

    function confirmReservation() {
        document.getElementById('reservationForm').submit();
    }
</script>

</body>

</html>
