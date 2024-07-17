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

                <?php
                require 'config.php';

                $sql = "SELECT * FROM gallery";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $id = $row['id'];
                        $title = $row['title'];
                        $description = $row['description'];
                        $imageData = base64_encode($row['image']);
                        $imageSrc = 'data:image/jpeg;base64,' . $imageData;

                        echo '<div class="col-md-4" style="margin-bottom: 10px;">';  
                        echo '<div class="amenity-item" data-bs-toggle="modal" data-bs-target="#amenityModal" data-id="' . $id . '" data-title="' . htmlspecialchars($title, ENT_QUOTES) . '" data-images="' . htmlspecialchars($imageSrc, ENT_QUOTES) . '">';
                        echo '<img src="' . $imageSrc . '" alt="' . htmlspecialchars($title, ENT_QUOTES) . '" class="img-fluid">';
                        echo '<div class="amenity-content">';
                        echo '<h3>' . htmlspecialchars($title, ENT_QUOTES) . '</h3>';
                        echo '<p>' . htmlspecialchars($description, ENT_QUOTES) . '</p>';
                        echo '</div></div></div>';
                    }
                } else {
                    echo '<p>No gallery items found.</p>';
                }

                mysqli_close($conn); 
                ?>

            </div>
        </div>
    </section>

    <!-- Modal Structure -->
    <div class="modal fade" id="amenityModal" tabindex="-1" aria-labelledby="amenityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="amenityModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="" alt="" class="img-fluid" id="modalImage">

                </div>
            </div>
        </div>
    </div>
    <script>document.addEventListener('DOMContentLoaded', function () {
            var amenityModal = document.getElementById('amenityModal');
            amenityModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; 
                var title = button.getAttribute('data-title');
                var image = button.getAttribute('data-images');
                var description = button.querySelector('.amenity-content p').textContent;

                var modalTitle = amenityModal.querySelector('.modal-title');
                var modalImage = amenityModal.querySelector('#modalImage');
                var modalDescription = amenityModal.querySelector('#modalDescription');

                modalTitle.textContent = title;
                modalImage.src = image;
                modalDescription.textContent = description;
            });
        });
    </script>






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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>

</body>
<?php include 'footer.php'; ?>

</html>