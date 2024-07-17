<?php
session_start();

// Database connection
require 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data for summaries
$packageSummary = $conn->query("SELECT COUNT(*) as count, package_type FROM swimming_packages GROUP BY package_type");

// Update the reservation summary to include specific statuses
$reservationSummary = $conn->query("
    SELECT 
        SUM(CASE WHEN status = 'Accepted' THEN 1 ELSE 0 END) as accepted,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'Denied' THEN 1 ELSE 0 END) as denied,
        SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM package_reservations
")->fetch_assoc();

// Update the reviews summary to include specific statuses
$reviewsSummary = $conn->query("
    SELECT 
        SUM(CASE WHEN status = 'Accepted' THEN 1 ELSE 0 END) as accepted,
        SUM(CASE WHEN status = 'Denied' THEN 1 ELSE 0 END) as denied,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending
    FROM reviews
")->fetch_assoc();

$usersSummary = $conn->query("SELECT COUNT(*) as count, gender FROM users GROUP BY gender");

// Sales summary queries
$weeklySales = $conn->query("SELECT SUM(total_price) as total FROM package_reservations WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)")->fetch_assoc();
$monthlySales = $conn->query("SELECT SUM(total_price) as total FROM package_reservations WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())")->fetch_assoc();
$yearlySales = $conn->query("SELECT SUM(total_price) as total FROM package_reservations WHERE YEAR(created_at) = YEAR(CURDATE())")->fetch_assoc();

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Report Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style2.css">
</head>
<style>
    body {
        background-color: #f8f9fa;
    }
    .container {
        margin-top: 30px;
    }
    h1, h3 {
        color: #343a40;
    }
    .card {
        margin-bottom: 20px;
        border-radius: 10px;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .card-header {
        background-color: #343a40;
        color: white;
        font-weight: bold;
        text-align: center;
        border-bottom: none;
    }
    .card-body {
        padding: 20px;
    }
</style>
<body>
    <?php include 'admin_navbar.php'; ?>
    <div class="container" style="margin-top:5%;">
        <h1 class="text-center mb-4">Report Summary</h1>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Package Summary</div>
                    <div class="card-body">
                        <canvas id="packageChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Reservation Summary</div>
                    <div class="card-body">
                        <canvas id="reservationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Users Summary</div>
                    <div class="card-body">
                        <canvas id="usersChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Reviews Summary</div>
                    <div class="card-body">
                        <canvas id="reviewsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Summary Section -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Sales Summary</div>
                    <div class="card-body">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // Package Summary Chart
        var packageCtx = document.getElementById('packageChart').getContext('2d');
        var packageLabels = [];
        var packageData = [];
        <?php while($row = $packageSummary->fetch_assoc()) { ?>
            packageLabels.push('<?php echo $row["package_type"]; ?>');
            packageData.push('<?php echo $row["count"]; ?>');
        <?php } ?>
        var packageChartData = {
            labels: packageLabels,
            datasets: [{
                label: 'Packages',
                data: packageData,
                backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 159, 64, 0.2)', 'rgba(255, 205, 86, 0.2)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 159, 64, 1)', 'rgba(255, 205, 86, 1)'],
                borderWidth: 1
            }]
        };
        var packageChart = new Chart(packageCtx, {
            type: 'bar',
            data: packageChartData
        });

        // Reservation Summary Chart
        var reservationCtx = document.getElementById('reservationChart').getContext('2d');
        var reservationData = {
            labels: ['Accepted', 'Pending', 'Denied', 'Cancelled'],
            datasets: [{
                label: 'Reservations',
                data: [
                    <?php echo $reservationSummary['accepted']; ?>,
                    <?php echo $reservationSummary['pending']; ?>,
                    <?php echo $reservationSummary['denied']; ?>,
                    <?php echo $reservationSummary['cancelled']; ?>
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        };
        var reservationChart = new Chart(reservationCtx, {
            type: 'bar',
            data: reservationData
        });

        // Users Summary Chart
        var usersCtx = document.getElementById('usersChart').getContext('2d');
        var usersLabels = [];
        var usersData = [];
        <?php while($row = $usersSummary->fetch_assoc()) { ?>
            usersLabels.push('<?php echo $row["gender"]; ?>');
            usersData.push('<?php echo $row["count"]; ?>');
        <?php } ?>
        var usersChartData = {
            labels: usersLabels,
            datasets: [{
                label: 'Users',
                data: usersData,
                backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)'],
                borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)'],
                borderWidth: 1
            }]
        };
        var usersChart = new Chart(usersCtx, {
            type: 'pie',
            data: usersChartData
        });

        // Reviews Summary Chart
        var reviewsCtx = document.getElementById('reviewsChart').getContext('2d');
        var reviewsData = {
            labels: ['Accepted', 'Denied', 'Pending'],
            datasets: [{
                label: 'Reviews',
                data: [
                    <?php echo $reviewsSummary['accepted']; ?>,
                    <?php echo $reviewsSummary['denied']; ?>,
                    <?php echo $reviewsSummary['pending']; ?>
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 205, 86, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 205, 86, 1)'
                ],
                borderWidth: 1
            }]
        };
        var reviewsChart = new Chart(reviewsCtx, {
            type: 'doughnut',
            data: reviewsData
        });

        // Sales Summary Chart
        var salesCtx = document.getElementById('salesChart').getContext('2d');
        var salesData = {
            labels: ['Weekly Sales', 'Monthly Sales', 'Yearly Sales'],
            datasets: [{
                label: 'Sales (PHP)',
                data: [
                    <?php echo $weeklySales['total']; ?>,
                    <?php echo $monthlySales['total']; ?>,
                    <?php echo $yearlySales['total']; ?>
                ],
                backgroundColor: [
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        };
        var salesChart = new Chart(salesCtx, {
            type: 'bar',
            data: salesData
        });
    });
    </script>
</body>
</html>
