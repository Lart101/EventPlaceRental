<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Board Mart Event Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="contact.css">
    <style>
        body {
            background-color: #DBF8FF;
background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 1000'%3E%3Cg fill-opacity='0.27'%3E%3Ccircle fill='%23DBF8FF' cx='50' cy='0' r='50'/%3E%3Cg fill='%23cff4ff' %3E%3Ccircle cx='0' cy='50' r='50'/%3E%3Ccircle cx='100' cy='50' r='50'/%3E%3C/g%3E%3Ccircle fill='%23c3f0fe' cx='50' cy='100' r='50'/%3E%3Cg fill='%23b7ebff' %3E%3Ccircle cx='0' cy='150' r='50'/%3E%3Ccircle cx='100' cy='150' r='50'/%3E%3C/g%3E%3Ccircle fill='%23ace7ff' cx='50' cy='200' r='50'/%3E%3Cg fill='%23a1e2ff' %3E%3Ccircle cx='0' cy='250' r='50'/%3E%3Ccircle cx='100' cy='250' r='50'/%3E%3C/g%3E%3Ccircle fill='%2396ddff' cx='50' cy='300' r='50'/%3E%3Cg fill='%238bd8ff' %3E%3Ccircle cx='0' cy='350' r='50'/%3E%3Ccircle cx='100' cy='350' r='50'/%3E%3C/g%3E%3Ccircle fill='%2381d3ff' cx='50' cy='400' r='50'/%3E%3Cg fill='%2378ceff' %3E%3Ccircle cx='0' cy='450' r='50'/%3E%3Ccircle cx='100' cy='450' r='50'/%3E%3C/g%3E%3Ccircle fill='%236fc8ff' cx='50' cy='500' r='50'/%3E%3Cg fill='%2368c2ff' %3E%3Ccircle cx='0' cy='550' r='50'/%3E%3Ccircle cx='100' cy='550' r='50'/%3E%3C/g%3E%3Ccircle fill='%2362bdff' cx='50' cy='600' r='50'/%3E%3Cg fill='%235eb6ff' %3E%3Ccircle cx='0' cy='650' r='50'/%3E%3Ccircle cx='100' cy='650' r='50'/%3E%3C/g%3E%3Ccircle fill='%235cb0ff' cx='50' cy='700' r='50'/%3E%3Cg fill='%235ca9ff' %3E%3Ccircle cx='0' cy='750' r='50'/%3E%3Ccircle cx='100' cy='750' r='50'/%3E%3C/g%3E%3Ccircle fill='%235ea3ff' cx='50' cy='800' r='50'/%3E%3Cg fill='%23629bff' %3E%3Ccircle cx='0' cy='850' r='50'/%3E%3Ccircle cx='100' cy='850' r='50'/%3E%3C/g%3E%3Ccircle fill='%236794ff' cx='50' cy='900' r='50'/%3E%3Cg fill='%236e8cff' %3E%3Ccircle cx='0' cy='950' r='50'/%3E%3Ccircle cx='100' cy='950' r='50'/%3E%3C/g%3E%3Ccircle fill='%237584FF' cx='50' cy='1000' r='50'/%3E%3C/g%3E%3C/svg%3E");
background-attachment: fixed;
background-size: contain;
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
            display: flex;
            flex-direction: column;
            height: 100%;
            background-color: #fff;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            margin-top: 20px;

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
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .amenity-content h3 {
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .amenity-content p {
            font-size: 1.1rem;
            line-height: 1.8;
            margin: 0;
        }

        .modal img {
            width: 100%;
            height: auto;
        }

        /* Styles for the card */
        .amenity-item {
            position: relative;
            /* Ensure relative positioning for overlay */
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
            background-color: rgba(0, 0, 0, 0.5);
            /* Adjust opacity (0.5 is semi-transparent) */
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 10px;
            /* Ensure same border-radius as the card */
        }

        /* Hover effect */
        .amenity-item:hover::before {
            opacity: 1;
        }

        .video-thumbnail {
            position: relative;
            cursor: pointer;
        }

        .video-thumbnail img {
            width: 100%;
            height: auto;
        }

        .video-play-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 4rem;
            color: rgba(255, 255, 255, 0.7);
            pointer-events: none;
        }
    </style>
</head>

<body>

    <?php include 'user_navbar.php'; ?>

    <section class="amenities">
        <div class="container">
            <h2 class="amenities-title">Our Gallery</h2>
            <div class="row">

                <div class="col-md-4">
                    <div class="amenity-item" data-bs-toggle="modal" data-bs-target="#amenityModal"
                        data-images="img/profile/gallery/familygathering.jpg,img/profile/gallery/familygathering1.jpg,img/profile/gallery/familygathering2.jpg,img/profile/gallery/familygathering3.jpg">
                        <img src="img/profile/gallery/familygathering.jpg" alt="Family Gathering">
                        <div class="amenity-content">
                            <h3>Experience Unforgettable Family Gatherings</h3>
                            <p>Discover the perfect venue for your next family gathering at BoardMart's Event Place. Our
                                serene
                                surroundings and meticulously maintained pool create an ideal setting for creating
                                lasting memories
                                with your loved ones.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="amenity-item">
                        <div class="video-thumbnail" data-bs-toggle="modal" data-bs-target="#videoModal">
                            <img src="img/profile/gallery/photoshoot-thumbnail.png" alt="Video Thumbnail"
                                class="img-fluid">
                            <i class="bi bi-play video-play-icon"></i>
                        </div>
                        <div class="amenity-content">
                            <h3>Stunning Photoshoot Moments</h3>
                            <p>Discover the perfect spots for your photoshoots at our venue. Our beautiful landscapes
                                and elegant interiors provide the ideal backdrop for your photography needs.</p>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="videoModalLabel">Stunning Photoshoot Moments</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <video width="100%" controls>
                                    <source src="img/profile/gallery/photoshoot.mp4" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="amenity-item">
                        <div class="video-thumbnail" data-bs-toggle="modal" data-bs-target="#videoModal2">
                            <img src="img/profile/gallery/thumbnail.png" alt="Video Thumbnail" class="img-fluid">
                            <i class="bi bi-play video-play-icon"></i>
                        </div>
                        <div class="amenity-content">
                            <h3>Elegant Wedding Moments</h3>
                            <p>Experience the magic of our venue where unforgettable weddings come to life. Watch how we
                                transform dreams into reality with our expert services and stunning event spaces.</p>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="videoModal2" tabindex="-1" aria-labelledby="videoModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="videoModalLabel">Elegant Wedding Moments</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <video width="100%" controls>
                                    <source src="img/profile/gallery/wedding.mp4" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4" style=" margin-top: 20px;">
                    <div class="amenity-item" data-bs-toggle="modal" data-bs-target="#amenityModal"
                        data-images="img/profile/gallery/birthday.jpg">
                        <img src="img/profile/gallery/birthday.jpg" alt="Birthday Celebration">
                        <div class="amenity-content">
                            <h3>Celebrate Birthdays in Style</h3>
                            <p>Host your next birthday celebration at BoardMart's Event Place. Our inviting atmosphere
                                and
                                well-maintained facilities are perfect for creating cherished memories with your loved
                                ones.</p>
                        </div>
                    </div>
                </div>


                <div class="col-md-4" style=" margin-top: 20px;">
                    <div class="amenity-item" data-bs-toggle="modal" data-bs-target="#amenityModal"
                        data-images="img/profile/gallery/teachers.jpg,img/profile/gallery/teachers1.jpg,img/profile/gallery/teachers2.jpg">
                        <img src="img/profile/gallery/teachers.jpg" alt="National Teachers' Day">
                        <div class="amenity-content">
                            <h3>Celebrate National Teachers' Day</h3>
                            <p>Join us in honoring educators on National Teachers' Day at BoardMart's Event Place. Our
                                tranquil
                                environment and excellent amenities provide the perfect setting to show appreciation for
                                our
                                dedicated teachers.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4" style=" margin-top: 20px;">
    <div class="amenity-item">
        <div class="video-thumbnail" data-bs-toggle="modal" data-bs-target="#videoModal3">
            <img src="img/profile/gallery/reunion.png" alt="Reunion Thumbnail" class="img-fluid">
            <i class="bi bi-play video-play-icon"></i>
        </div>
        <div class="amenity-content">
            <h3>Capture Memorable Reunion Moments</h3>
            <p>Rediscover cherished memories with loved ones at our venue. Our scenic surroundings and welcoming
                atmosphere create the perfect setting for your reunion.</p>
        </div>
    </div>
</div>

<div class="modal fade" id="videoModal3" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoModalLabel">Capture Memorable Reunion Moments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <video width="100%" controls>
                    <source src="img/profile/gallery/reunion.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
</div>






            </div>
        </div>
    </section>
    <!-- Modal -->
    <div class="modal fade" id="amenityModal" tabindex="-1" aria-labelledby="amenityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="amenityModalLabel">Galley Images</h5>
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
<?php include 'footer.php'; ?>

</html>