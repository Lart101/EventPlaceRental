<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require "config.php";
// Prepare SQL query to fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $email);
$stmt->fetch();

// Close statement
$stmt->close();
$conn->close();
?>

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Board Mart Event Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="contact.css">
    <style>
        body {
            background-color: #FFF6E9;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='540' height='450' viewBox='0 0 1080 900'%3E%3Cg fill-opacity='.1'%3E%3Cpolygon fill='%23444' points='90 150 0 300 180 300'/%3E%3Cpolygon points='90 150 180 0 0 0'/%3E%3Cpolygon fill='%23AAA' points='270 150 360 0 180 0'/%3E%3Cpolygon fill='%23DDD' points='450 150 360 300 540 300'/%3E%3Cpolygon fill='%23999' points='450 150 540 0 360 0'/%3E%3Cpolygon points='630 150 540 300 720 300'/%3E%3Cpolygon fill='%23DDD' points='630 150 720 0 540 0'/%3E%3Cpolygon fill='%23444' points='810 150 720 300 900 300'/%3E%3Cpolygon fill='%23FFF' points='810 150 900 0 720 0'/%3E%3Cpolygon fill='%23DDD' points='990 150 900 300 1080 300'/%3E%3Cpolygon fill='%23444' points='990 150 1080 0 900 0'/%3E%3Cpolygon fill='%23DDD' points='90 450 0 600 180 600'/%3E%3Cpolygon points='90 450 180 300 0 300'/%3E%3Cpolygon fill='%23666' points='270 450 180 600 360 600'/%3E%3Cpolygon fill='%23AAA' points='270 450 360 300 180 300'/%3E%3Cpolygon fill='%23DDD' points='450 450 360 600 540 600'/%3E%3Cpolygon fill='%23999' points='450 450 540 300 360 300'/%3E%3Cpolygon fill='%23999' points='630 450 540 600 720 600'/%3E%3Cpolygon fill='%23FFF' points='630 450 720 300 540 300'/%3E%3Cpolygon points='810 450 720 600 900 600'/%3E%3Cpolygon fill='%23DDD' points='810 450 900 300 720 300'/%3E%3Cpolygon fill='%23AAA' points='990 450 900 600 1080 600'/%3E%3Cpolygon fill='%23444' points='990 450 1080 300 900 300'/%3E%3Cpolygon fill='%23222' points='90 750 0 900 180 900'/%3E%3Cpolygon points='270 750 180 900 360 900'/%3E%3Cpolygon fill='%23DDD' points='270 750 360 600 180 600'/%3E%3Cpolygon points='450 750 540 600 360 600'/%3E%3Cpolygon points='630 750 540 900 720 900'/%3E%3Cpolygon fill='%23444' points='630 750 720 600 540 600'/%3E%3Cpolygon fill='%23AAA' points='810 750 720 900 900 900'/%3E%3Cpolygon fill='%23666' points='810 750 900 600 720 600'/%3E%3Cpolygon fill='%23999' points='990 750 900 900 1080 900'/%3E%3Cpolygon fill='%23999' points='180 0 90 150 270 150'/%3E%3Cpolygon fill='%23444' points='360 0 270 150 450 150'/%3E%3Cpolygon fill='%23FFF' points='540 0 450 150 630 150'/%3E%3Cpolygon points='900 0 810 150 990 150'/%3E%3Cpolygon fill='%23222' points='0 300 -90 450 90 450'/%3E%3Cpolygon fill='%23FFF' points='0 300 90 150 -90 150'/%3E%3Cpolygon fill='%23FFF' points='180 300 90 450 270 450'/%3E%3Cpolygon fill='%23666' points='180 300 270 150 90 150'/%3E%3Cpolygon fill='%23222' points='360 300 270 450 450 450'/%3E%3Cpolygon fill='%23FFF' points='360 300 450 150 270 150'/%3E%3Cpolygon fill='%23444' points='540 300 450 450 630 450'/%3E%3Cpolygon fill='%23222' points='540 300 630 150 450 150'/%3E%3Cpolygon fill='%23AAA' points='720 300 630 450 810 450'/%3E%3Cpolygon fill='%23666' points='720 300 810 150 630 150'/%3E%3Cpolygon fill='%23FFF' points='900 300 810 450 990 450'/%3E%3Cpolygon fill='%23999' points='900 300 990 150 810 150'/%3E%3Cpolygon points='0 600 -90 750 90 750'/%3E%3Cpolygon fill='%23666' points='0 600 90 450 -90 450'/%3E%3Cpolygon fill='%23AAA' points='180 600 90 750 270 750'/%3E%3Cpolygon fill='%23444' points='180 600 270 450 90 450'/%3E%3Cpolygon fill='%23444' points='360 600 270 750 450 750'/%3E%3Cpolygon fill='%23999' points='360 600 450 450 270 450'/%3E%3Cpolygon fill='%23666' points='540 600 630 450 450 450'/%3E%3Cpolygon fill='%23222' points='720 600 630 750 810 750'/%3E%3Cpolygon fill='%23FFF' points='900 600 810 750 990 750'/%3E%3Cpolygon fill='%23222' points='900 600 990 450 810 450'/%3E%3Cpolygon fill='%23DDD' points='0 900 90 750 -90 750'/%3E%3Cpolygon fill='%23444' points='180 900 270 750 90 750'/%3E%3Cpolygon fill='%23FFF' points='360 900 450 750 270 750'/%3E%3Cpolygon fill='%23AAA' points='540 900 630 750 450 750'/%3E%3Cpolygon fill='%23FFF' points='720 900 810 750 630 750'/%3E%3Cpolygon fill='%23222' points='900 900 990 750 810 750'/%3E%3Cpolygon fill='%23222' points='1080 300 990 450 1170 450'/%3E%3Cpolygon fill='%23FFF' points='1080 300 1170 150 990 150'/%3E%3Cpolygon points='1080 600 990 750 1170 750'/%3E%3Cpolygon fill='%23666' points='1080 600 1170 450 990 450'/%3E%3Cpolygon fill='%23DDD' points='1080 900 1170 750 990 750'/%3E%3C/g%3E%3C/svg%3E");
        }

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

        .star-rating input[type="radio"]:checked~label,
        .star-rating input[type="radio"]:checked~label~label {
            color: #ffc700;
        }

        .star-rating label:hover,
        .star-rating label:hover~label {
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

    <section id="contact" class="py-5 fade-in" style="margin-top:5%;">
        <div class="container">
            <h1 class="text-center">WE APPRECIATE YOUR REVIEW!</h1>
            <div class="row justify-content-center mt-5">
                <div class="col-md-8 col-lg-6 mb-4">
                    <form id="contact-form" action="contact.php" method="POST">
                    <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($full_name); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
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
                            <span id="rating-error" class="error-message" style="display: none;color:red;">Please rate
                                our services.</span>
                        </div>

                        <button type="submit" class="btn btn-primary d-block mx-auto"
                            onclick="return validateForm()">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Wait for the DOM to fully load
        document.addEventListener('DOMContentLoaded', function () {
            // Add event listener for form submission
            document.getElementById('contact-form').addEventListener('submit', function (event) {
                var ratingInputs = document.querySelectorAll('input[type="radio"][name="rating"]');
                var ratingSelected = false;

                // Check if any rating option is selected
                ratingInputs.forEach(function (input) {
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
            ratingInputs.forEach(function (input) {
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