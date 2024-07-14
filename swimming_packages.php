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
    <title>Swimming Packages - Board Mart Event Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link href="default.css" rel="stylesheet">
    <style>
        .bg {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/profile/bg.jpg');
            background-size: cover;
            background-position: center;
            padding: 20px;
            padding-top: 20%;
            color: whitesmoke;
            height: 800px;
        }
        .card {
            margin-bottom: 20px;
        }
        
        .footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
        }

        .footer a {
            color: #ffffff;
        }

        .footer a:hover {
            text-decoration: none;
            color: #ffc107;
        }

    </style>
</head>
<body>
<?php include 'user_navbar.php'; ?>
    <section class="bg" id="hero">
        <div class="container-lg">
            <div class="row align-items-center">
                <div class="col-sm-6 fade-in">
                    <h1 class="display fw-bold" style="color: #FFFAB7;">Swimming Packages</h1>
                    <p>Choose your preferred swimming package!</p>
                    <a class="btn btn-outline-light btn-lg" href="#nature-of-business">Reserve Now</a>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <section id="packages" class="py-5 fade-in">
            <div class="container">
                <h1 class="my-4 text-center">Our Packages</h1>
                <?php
                // Database connection
                $conn = new mysqli("localhost", "root", "", "event_store");

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch swimming packages grouped by type
                $sql = "SELECT id, package_name, price, duration, max_pax, profile_image, package_type FROM swimming_packages ORDER BY FIELD(package_type, 'Day', 'Overnight', 'Combo')";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $current_type = "";
                    while ($row = $result->fetch_assoc()) {
                        if ($row['package_type'] != $current_type) {
                            if ($current_type != "") {
                                echo '</div>'; // Close the previous type's container
                            }
                            $current_type = $row['package_type'];
                            echo '<div class="row">
                                    <div class="col-12">
                                        <h2 class="my-4">' . $current_type . ' Packages</h2>
                                    </div>
                                  </div>
                                  <div class="row row-cols-1 row-cols-md-3 g-4">'; // Start new row for packages
                        }
                        echo '<div class="col">
                                <div class="card">
                                    <img src="' . $row["profile_image"] . '" class="card-img-top" alt="' . $row["package_name"] . '">
                                    <div class="card-body">
                                        <h5 class="card-title">' . $row["package_name"] . '</h5>
                                        <p class="card-text">Price: ₱' . $row["price"] . '<br>
                                        Duration: ' . $row["duration"] . '<br>
                                        Max Pax: ' . $row["max_pax"] . '</p>
                                        <a href="package_details.php?id=' . $row["id"] . '" class="btn btn-outline-dark">See Details</a>
                                    </div>
                                </div>
                              </div>';
                    }
                    echo '</div>'; // Close the last row
                } else {
                    echo "<p class='text-center'>No swimming packages found.</p>";
                }

                $conn->close();
                ?>
            </div>
        </section>
      
    </div>
    <?php include 'footer.php'; ?>

    <div id="notification" class="notification"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

