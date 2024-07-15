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
            background-color: #FFFFFF;
background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='540' height='450' viewBox='0 0 1080 900'%3E%3Cg fill-opacity='.1'%3E%3Cpolygon fill='%23444' points='90 150 0 300 180 300'/%3E%3Cpolygon points='90 150 180 0 0 0'/%3E%3Cpolygon fill='%23AAA' points='270 150 360 0 180 0'/%3E%3Cpolygon fill='%23DDD' points='450 150 360 300 540 300'/%3E%3Cpolygon fill='%23999' points='450 150 540 0 360 0'/%3E%3Cpolygon points='630 150 540 300 720 300'/%3E%3Cpolygon fill='%23DDD' points='630 150 720 0 540 0'/%3E%3Cpolygon fill='%23444' points='810 150 720 300 900 300'/%3E%3Cpolygon fill='%23FFF' points='810 150 900 0 720 0'/%3E%3Cpolygon fill='%23DDD' points='990 150 900 300 1080 300'/%3E%3Cpolygon fill='%23444' points='990 150 1080 0 900 0'/%3E%3Cpolygon fill='%23DDD' points='90 450 0 600 180 600'/%3E%3Cpolygon points='90 450 180 300 0 300'/%3E%3Cpolygon fill='%23666' points='270 450 180 600 360 600'/%3E%3Cpolygon fill='%23AAA' points='270 450 360 300 180 300'/%3E%3Cpolygon fill='%23DDD' points='450 450 360 600 540 600'/%3E%3Cpolygon fill='%23999' points='450 450 540 300 360 300'/%3E%3Cpolygon fill='%23999' points='630 450 540 600 720 600'/%3E%3Cpolygon fill='%23FFF' points='630 450 720 300 540 300'/%3E%3Cpolygon points='810 450 720 600 900 600'/%3E%3Cpolygon fill='%23DDD' points='810 450 900 300 720 300'/%3E%3Cpolygon fill='%23AAA' points='990 450 900 600 1080 600'/%3E%3Cpolygon fill='%23444' points='990 450 1080 300 900 300'/%3E%3Cpolygon fill='%23222' points='90 750 0 900 180 900'/%3E%3Cpolygon points='270 750 180 900 360 900'/%3E%3Cpolygon fill='%23DDD' points='270 750 360 600 180 600'/%3E%3Cpolygon points='450 750 540 600 360 600'/%3E%3Cpolygon points='630 750 540 900 720 900'/%3E%3Cpolygon fill='%23444' points='630 750 720 600 540 600'/%3E%3Cpolygon fill='%23AAA' points='810 750 720 900 900 900'/%3E%3Cpolygon fill='%23666' points='810 750 900 600 720 600'/%3E%3Cpolygon fill='%23999' points='990 750 900 900 1080 900'/%3E%3Cpolygon fill='%23999' points='180 0 90 150 270 150'/%3E%3Cpolygon fill='%23444' points='360 0 270 150 450 150'/%3E%3Cpolygon fill='%23FFF' points='540 0 450 150 630 150'/%3E%3Cpolygon points='900 0 810 150 990 150'/%3E%3Cpolygon fill='%23222' points='0 300 -90 450 90 450'/%3E%3Cpolygon fill='%23FFF' points='0 300 90 150 -90 150'/%3E%3Cpolygon fill='%23FFF' points='180 300 90 450 270 450'/%3E%3Cpolygon fill='%23666' points='180 300 270 150 90 150'/%3E%3Cpolygon fill='%23222' points='360 300 270 450 450 450'/%3E%3Cpolygon fill='%23FFF' points='360 300 450 150 270 150'/%3E%3Cpolygon fill='%23444' points='540 300 450 450 630 450'/%3E%3Cpolygon fill='%23222' points='540 300 630 150 450 150'/%3E%3Cpolygon fill='%23AAA' points='720 300 630 450 810 450'/%3E%3Cpolygon fill='%23666' points='720 300 810 150 630 150'/%3E%3Cpolygon fill='%23FFF' points='900 300 810 450 990 450'/%3E%3Cpolygon fill='%23999' points='900 300 990 150 810 150'/%3E%3Cpolygon points='0 600 -90 750 90 750'/%3E%3Cpolygon fill='%23666' points='0 600 90 450 -90 450'/%3E%3Cpolygon fill='%23AAA' points='180 600 90 750 270 750'/%3E%3Cpolygon fill='%23444' points='180 600 270 450 90 450'/%3E%3Cpolygon fill='%23444' points='360 600 270 750 450 750'/%3E%3Cpolygon fill='%23999' points='360 600 450 450 270 450'/%3E%3Cpolygon fill='%23666' points='540 600 630 450 450 450'/%3E%3Cpolygon fill='%23222' points='720 600 630 750 810 750'/%3E%3Cpolygon fill='%23FFF' points='900 600 810 750 990 750'/%3E%3Cpolygon fill='%23222' points='900 600 990 450 810 450'/%3E%3Cpolygon fill='%23DDD' points='0 900 90 750 -90 750'/%3E%3Cpolygon fill='%23444' points='180 900 270 750 90 750'/%3E%3Cpolygon fill='%23FFF' points='360 900 450 750 270 750'/%3E%3Cpolygon fill='%23AAA' points='540 900 630 750 450 750'/%3E%3Cpolygon fill='%23FFF' points='720 900 810 750 630 750'/%3E%3Cpolygon fill='%23222' points='900 900 990 750 810 750'/%3E%3Cpolygon fill='%23222' points='1080 300 990 450 1170 450'/%3E%3Cpolygon fill='%23FFF' points='1080 300 1170 150 990 150'/%3E%3Cpolygon points='1080 600 990 750 1170 750'/%3E%3Cpolygon fill='%23666' points='1080 600 1170 450 990 450'/%3E%3Cpolygon fill='%23DDD' points='1080 900 1170 750 990 750'/%3E%3C/g%3E%3C/svg%3E");
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
            cursor: pointer;
        }

        .amenity-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
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

        .modal img {
            width: 100%;
            height: auto;
        }
       /* Styles for the card */
.amenity-item {
    position: relative; /* Ensure relative positioning for overlay */
    margin-bottom: 50px;
    border-radius: 10px;
    overflow: hidden;
}

/* Overlay for darkening effect */
.amenity-item::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Adjust opacity (0.5 is semi-transparent) */
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 10px; /* Ensure same border-radius as the card */
}

/* Hover effect */
.amenity-item:hover::before {
    opacity: 1;
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
                    <div class="amenity-item" data-bs-toggle="modal" data-bs-target="#amenityModal"
                        data-images="img/profile/ameneties/pool1.png,img/profile/ameneties/pool2.jpg,img/profile/ameneties/pool3.png">
                        <img src="img/profile/ameneties/pool1.png" alt="Pool">
                        <div class="amenity-content">
                            <h3>Pool</h3>
                            <p>Our facility features a beautifully maintained pool, perfect for a refreshing swim or a
                                relaxing day by the water.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="amenity-item" data-bs-toggle="modal" data-bs-target="#amenityModal"
                        data-images="img/profile/ameneties/suite1.jpg">
                        <img src="img/profile/ameneties/suite1.jpg" alt="Suite Room">
                        <div class="amenity-content">
                            <h3>Suite Room</h3>
                            <p>For those seeking an extra touch of luxury, our suite rooms offer additional space and
                                upscale amenities.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="amenity-item" data-bs-toggle="modal" data-bs-target="#amenityModal"
                        data-images="img/profile/ameneties/dorm1.jpg,img/profile/ameneties/dorm2.jpg">
                        <img src="img/profile/ameneties/dorm1.jpg" alt="Dormitories">
                        <div class="amenity-content">
                            <h3>Dormitories</h3>
                            <p>Ideal for groups or budget-conscious travelers, our dormitories provide a comfortable and
                                communal lodging experience.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="amenity-item" data-bs-toggle="modal" data-bs-target="#amenityModal"
                        data-images="img/profile/ameneties/PoolLounge.jpg">
                        <img src="img/profile/ameneties/PoolLounge.jpg" alt="Pool Lounge">
                        <div class="amenity-content">
                            <h3>Pool Lounge</h3>
                            <p>Adjacent to the pool, the lounge offers a relaxing atmosphere with comfortable seating
                                and refreshments.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="amenity-item" data-bs-toggle="modal" data-bs-target="#amenityModal"
                        data-images="img/profile/ameneties/functionhall.jpg">
                        <img src="img/profile/ameneties/functionhall.jpg" alt="First Function Hall">
                        <div class="amenity-content">
                            <h3>First Function Hall</h3>
                            <p>Our first function hall is a versatile space perfect for hosting various events, from
                                conferences to social gatherings.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="amenity-item" data-bs-toggle="modal" data-bs-target="#amenityModal"
                        data-images="img/profile/ameneties/functionhall2.jpg,img/profile/ameneties/functionhall2(2).jpg,img/profile/ameneties/functionhall2(3).jpg">
                        <img src="img/profile/ameneties/functionhall2.jpg" alt="Second Function Hall">
                        <div class="amenity-content">
                            <h3>Second Function Hall</h3>
                            <p>The second function hall provides additional space for events, equipped with modern
                                facilities to cater to your needs.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="amenity-item" data-bs-toggle="modal" data-bs-target="#amenityModal"
                        data-images="img/profile/ameneties/garden1.jpg,img/profile/ameneties/garden2.jpg,img/profile/ameneties/garden3.jpg,img/profile/ameneties/garden4.jpg">
                        <img src="img/profile/ameneties/garden1.jpg" alt="Garden">
                        <div class="amenity-content">
                            <h3>Garden</h3>
                            <p>Enjoy a serene environment in our well-maintained garden, perfect for a peaceful stroll
                                or an outdoor event.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="amenity-item" data-bs-toggle="modal" data-bs-target="#amenityModal"
                        data-images="img/profile/ameneties/boardgames1.jpg,img/profile/ameneties/boardgames2.jpg,img/profile/ameneties/boardgames3.jpg">
                        <img src="img/profile/ameneties/boardgames1.jpg" alt="Board Games">
                        <div class="amenity-content">
                            <h3>Board Games</h3>
                            <p>Spend quality time with loved ones by enjoying a variety of board games available for all
                                guests.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="amenity-item" data-bs-toggle="modal" data-bs-target="#amenityModal"
                        data-images="img/profile/ameneties/karaoke.jpg">
                        <img src="img/profile/ameneties/karaoke.jpg" alt="Karaoke Room">
                        <div class="amenity-content">
                            <h3>Karaoke Room</h3>
                            <p>Unleash your inner star in our karaoke room, where you can sing along to your favorite
                                tunes with friends and family.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <!-- Modal -->
    <div class="modal fade" id="amenityModal" tabindex="-1" aria-labelledby="amenityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="amenityModalLabel">Amenity Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators" id="carousel-indicators"></div>
                        <div class="carousel-inner" id="carousel-inner"></div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var amenityModal = document.getElementById('amenityModal');
        amenityModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var images = button.getAttribute('data-images').split(',');
            var carouselIndicators = document.getElementById('carousel-indicators');
            var carouselInner = document.getElementById('carousel-inner');

            carouselIndicators.innerHTML = '';
            carouselInner.innerHTML = '';

            images.forEach((image, index) => {
                var indicator = document.createElement('button');
                indicator.type = 'button';
                indicator.setAttribute('data-bs-target', '#carouselExampleIndicators');
                indicator.setAttribute('data-bs-slide-to', index);
                if (index === 0) indicator.classList.add('active');
                carouselIndicators.appendChild(indicator);

                var carouselItem = document.createElement('div');
                carouselItem.classList.add('carousel-item');
                if (index === 0) carouselItem.classList.add('active');

                var img = document.createElement('img');
                img.src = image;
                img.classList.add('d-block', 'w-100');

                carouselItem.appendChild(img);
                carouselInner.appendChild(carouselItem);
            });
        });
    </script>
</body>

</html>