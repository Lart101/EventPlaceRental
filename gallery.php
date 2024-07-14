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
            background-color: #f8f9fa;
        }

        .gallery {
            padding: 60px 0;
        }

        .gallery-title {
            font-size: 2.5rem;
            margin-bottom: 40px;
            text-align: center;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .gallery-item img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-caption {
            position: absolute;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            width: 100%;
            text-align: center;
            padding: 10px;
            font-size: 1.1rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-item:hover .gallery-caption {
            opacity: 1;
        }
    </style>
</head>
<body>

    <?php include 'user_navbar.php'; ?>

    <section class="gallery">
        <div class="container">
            <h2 class="gallery-title">Our Event Gallery</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-image="img/gallery/event1.jpg" data-bs-caption="Wedding Celebration">
                        <img src="img/gallery/event1.jpg" alt="Wedding Celebration">
                        <div class="gallery-caption">Wedding Celebration</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-image="img/gallery/event2.jpg" data-bs-caption="Corporate Gathering">
                        <img src="img/gallery/event2.jpg" alt="Corporate Gathering">
                        <div class="gallery-caption">Corporate Gathering</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-image="img/gallery/event3.jpg" data-bs-caption="Birthday Party">
                        <img src="img/gallery/event3.jpg" alt="Birthday Party">
                        <div class="gallery-caption">Birthday Party</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-image="img/gallery/event4.jpg" data-bs-caption="Anniversary Celebration">
                        <img src="img/gallery/event4.jpg" alt="Anniversary Celebration">
                        <div class="gallery-caption">Anniversary Celebration</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-image="img/gallery/event5.jpg" data-bs-caption="Product Launch">
                        <img src="img/gallery/event5.jpg" alt="Product Launch">
                        <div class="gallery-caption">Product Launch</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-image="img/gallery/event6.jpg" data-bs-caption="Community Event">
                        <img src="img/gallery/event6.jpg" alt="Community Event">
                        <div class="gallery-caption">Community Event</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="" id="modalImage" class="img-fluid" alt="">
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var imageModal = document.getElementById('imageModal');
            imageModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var imageSrc = button.getAttribute('data-bs-image');
                var imageCaption = button.getAttribute('data-bs-caption');
                var modalImage = document.getElementById('modalImage');
                var modalTitle = imageModal.querySelector('.modal-title');
                
                modalImage.src = imageSrc;
                modalTitle.textContent = imageCaption;
            });
        });
    </script>
</body>
</html>
