<?php 
  session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amenities - Board Mart Event Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="contact.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .amenities {
            padding: 60px 0;
        }

        .amenities-title {
            font-size: 2.5rem;
            margin-bottom: 40px;
            text-align: center;
        }

        .amenity-item {
            margin-bottom: 50px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .amenity-item img {
            width: 100%;
            height: auto;
        }

        .amenity-content {
            padding: 20px;
            background-color: #fff;
            text-align: center;
        }

        .amenity-content h3 {
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .amenity-content p {
            font-size: 1.1rem;
            line-height: 1.8;
        }
    </style>
</head>
<body>

    <?php include 'user_navbar.php'; ?>

    <section class="amenities">
        <div class="container">
            <h2 class="amenities-title">Our Amenities</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="amenity-item">
                        <img src="img/amenities/pool.jpg" alt="Swimming Pools">
                        <div class="amenity-content">
                            <h3>Swimming Pools</h3>
                            <p>Relax and unwind in our luxurious swimming pools. Perfect for a refreshing dip or lounging by the poolside.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="amenity-item">
                        <img src="img/amenities/room.jpg" alt="Rooms">
                        <div class="amenity-content">
                            <h3>Rooms</h3>
                            <p>Our comfortable and well-appointed rooms are designed to provide a restful retreat for our guests.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="amenity-item">
                        <img src="img/amenities/function_hall.jpg" alt="Function Hall">
                        <div class="amenity-content">
                            <h3>Function Hall</h3>
                            <p>Our spacious function hall is ideal for weddings, corporate events, and social gatherings.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="amenity-item">
                        <img src="img/amenities/videoke_room.jpg" alt="Videoke Room">
                        <div class="amenity-content">
                            <h3>Videoke Room</h3>
                            <p>Sing your heart out in our private videoke room, equipped with the latest sound system and a vast selection of songs.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="amenity-item">
                        <img src="img/amenities/grill_lechon.jpg" alt="Grill Lechon">
                        <div class="amenity-content">
                            <h3>Grill Lechon</h3>
                            <p>Enjoy delicious grilled lechon prepared to perfection, a highlight of our culinary offerings.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
