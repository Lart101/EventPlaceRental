<?php 
  session_start();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Board Mart Event Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="contact.css">
    <style>
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            padding-right: 0;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            padding: 0 5px;
        }

        .star-rating input[type="radio"]:checked ~ label,
        .star-rating input[type="radio"]:checked ~ label ~ label {
            color: #ffc700;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffc700;
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

        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 10px 20px;
            border-radius: 5px;
            display: none;
            z-index: 9999;
        }

        .notification.alert-success {
            background-color: #4CAF50;
            color: white;
        }

        .notification.alert-danger {
            background-color: #f44336;
            color: white;
        }

        .loading-spinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
            z-index: 9999;
        }
        .nav-item.login {
            margin-left: auto; 
        }

        .nav-item.login a {
            color: #007bff;
        }

        .nav-item.logout button {
            color: #dc3545; 
            background-color: transparent;
            border: none;
            cursor: pointer;
        }

        .nav-item.logout button:hover {
            color: #fff;
            background-color: #dc3545;
        }
    </style>
</head>
<body>

    <div id="notification-container" class="notification"></div>
    <div id="loading-spinner" class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <?php include 'user_navbar.php'; ?>



    <section id="map" class="py-5 fade-in">
        <div class="container" style="padding-top: 5%;">
            <h1 class="text-center">CONTACT US</h1>
            <div class="row mt-5">
                <div class="col-md-6 mb-4">
                    <div class="map-responsive">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3859.220601286267!2d120.94302827868145!3d14.700113149179494!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b3834c74aa8f%3A0xe7db541cdacd2c0d!2sBoardMart%E2%80%99s%20Event%20Place!5e0!3m2!1sen!2sph!4v1720175652644!5m2!1sen!2sph"
                            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <ul class="list-unstyled">
                        <li><i class="bi bi-geo-alt-fill"></i> <strong>Location:</strong>BoardMart’s Event Place, 1043 Mendoza, Valenzuela, Metro Manila</li>
                        <li><i class="bi bi-envelope-fill"></i> <strong>Email:</strong> boardmarteventplace@yahoo.com</li>
                    </ul>
                    <div class="mt-4">
                        <h3>Follow Us on:</h3>
                        <ul class="list-inline">
                            <li class="list-inline-item">
                                <a href="https://www.facebook.com/BoardMartsEventPlace" target="_blank">
                                    <i class="bi bi-facebook" style="font-size: 2rem; margin-right: 10px;"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://www.instagram.com/boardmarseventplace" target="_blank">
                                    <i class="bi bi-instagram" style="font-size: 2rem; margin-right: 10px;"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://x.com/Boardmart" target="_blank">
                                    <i class="bi bi-twitter" style="font-size: 2rem; margin-right: 10px;"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    

    <section id="contact" class="py-5 fade-in">
    <div class="container">
        <h1 class="text-center">WE APPRECIATE YOUR REVIEW!</h1>
        <div class="row justify-content-center mt-5">
            <div class="col-md-8 col-lg-6 mb-4">
                <form id="contact-form" action="contact.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required pattern="[A-Za-z]+" title="Please enter letters only">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <h3>Rate our services!</h3>
                        <div class="star-rating">
                            <input type="radio" id="5-stars" name="rating" value="5" required>
                            <label for="5-stars" class="bi bi-star-fill"></label>
                            <input type="radio" id="4-stars" name="rating" value="4" required>
                            <label for="4-stars" class="bi bi-star-fill"></label>
                            <input type="radio" id="3-stars" name="rating" value="3" required>
                            <label for="3-stars" class="bi bi-star-fill"></label>
                            <input type="radio" id="2-stars" name="rating" value="2" required>
                            <label for="2-stars" class="bi bi-star-fill"></label>
                            <input type="radio" id="1-star" name="rating" value="1" required>
                            <label for="1-star" class="bi bi-star-fill"></label>
                        </div>
                        <span id="rating-error" class="error-message" style="display: none;color:red;">Please rate our services.</span>
                    </div>

                    <button type="submit" class="btn btn-primary d-block mx-auto" onclick="return validateForm()">Submit</button>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    // Wait for the DOM to fully load
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listener for form submission
        document.getElementById('contact-form').addEventListener('submit', function(event) {
            var ratingInputs = document.querySelectorAll('input[type="radio"][name="rating"]');
            var ratingSelected = false;

            // Check if any rating option is selected
            ratingInputs.forEach(function(input) {
                if (input.checked) {
                    ratingSelected = true;
                }
            });

            // Display or hide error message based on rating selection
            if (!ratingSelected) {
                document.getElementById('rating-error').style.display = 'block';
                event.preventDefault(); // Prevent form submission
            } else {
                document.getElementById('rating-error').style.display = 'none';
            }
        });
    });

    // Function to validate the entire form
    function validateForm() {
        var isValid = true;

        // Validate other form fields as per your existing validation logic
        // Add your existing validation code here
        
        // Check if any rating option is selected
        var ratingInputs = document.querySelectorAll('input[type="radio"][name="rating"]');
        var ratingSelected = false;
        ratingInputs.forEach(function(input) {
            if (input.checked) {
                ratingSelected = true;
            }
        });

        // Display or hide error message based on rating selection
        if (!ratingSelected) {
            document.getElementById('rating-error').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('rating-error').style.display = 'none';
        }

        return isValid;
    }
</script>




    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>&copy; 2024 Board Mart Event Place. All Rights Reserved.</p>
                    <div class="mt-4">
                        <h3>Follow Us on:</h3>
                        <ul class="list-inline">
                            <li class="list-inline-item">
                                <a href="https://www.facebook.com/BoardMartsEventPlace" target="_blank">
                                    <i class="bi bi-facebook" style="font-size: 1rem; margin-right: 10px;"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://www.instagram.com/boardmarseventplace" target="_blank">
                                    <i class="bi bi-instagram" style="font-size: 1rem; margin-right: 10px;"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://x.com/Boardmart" target="_blank">
                                    <i class="bi bi-twitter" style="font-size: 1rem; margin-right: 10px;"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showNotification(type, message) {
            const notificationContainer = document.getElementById('notification-container');
            notificationContainer.className = `notification alert-${type}`;
            notificationContainer.textContent = message;
            notificationContainer.style.display = 'block';
            setTimeout(() => {
                notificationContainer.style.display = 'none';
            }, 5000);
        }

        function showLoadingSpinner(show) {
            const loadingSpinner = document.getElementById('loading-spinner');
            loadingSpinner.style.display = show ? 'block' : 'none';
        }

        document.getElementById('contact-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            showLoadingSpinner(true);
            fetch('contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showLoadingSpinner(false);
                showNotification(data.status === 'success' ? 'success' : 'danger', data.message);
                if (data.status === 'success') {
                    this.reset();
                }
            })
            .catch(error => {
                showLoadingSpinner(false);
                showNotification('danger', 'An error occurred while sending your message.');
            });
        });
    </script>

<script>
    function confirmLogout() {
        return confirm('Are you sure you want to logout?');
    }
    </script>
</body>
</html>
