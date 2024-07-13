<?php session_start() ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="aboutStyle.css">
    <title>Technical Support - Swimming Packages</title>
    <style>
        body {
            background-color: #BCEEBF;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 2000 1500'%3E%3Cdefs%3E%3CradialGradient id='a' gradientUnits='objectBoundingBox'%3E%3Cstop offset='0' stop-color='%23FFFDE9'/%3E%3Cstop offset='1' stop-color='%23BCEEBF'/%3E%3C/radialGradient%3E%3ClinearGradient id='b' gradientUnits='userSpaceOnUse' x1='0' y1='750' x2='1550' y2='750'%3E%3Cstop offset='0' stop-color='%23def6d4'/%3E%3Cstop offset='1' stop-color='%23BCEEBF'/%3E%3C/linearGradient%3E%3Cpath id='s' fill='url(%23b)' d='M1549.2 51.6c-5.4 99.1-20.2 197.6-44.2 293.6c-24.1 96-57.4 189.4-99.3 278.6c-41.9 89.2-92.4 174.1-150.3 253.3c-58 79.2-123.4 152.6-195.1 219c-71.7 66.4-149.6 125.8-232.2 177.2c-82.7 51.4-170.1 94.7-260.7 129.1c-90.6 34.4-184.4 60-279.5 76.3C192.6 1495 96.1 1502 0 1500c96.1-2.1 191.8-13.3 285.4-33.6c93.6-20.2 185-49.5 272.5-87.2c87.6-37.7 171.3-83.8 249.6-137.3c78.4-53.5 151.5-114.5 217.9-181.7c66.5-67.2 126.4-140.7 178.6-218.9c52.3-78.3 96.9-161.4 133-247.9c36.1-86.5 63.8-176.2 82.6-267.6c18.8-91.4 28.6-184.4 29.6-277.4c0.3-27.6 23.2-48.7 50.8-48.4s49.5 21.8 49.2 49.5c0 0.7 0 1.3-0.1 2L1549.2 51.6z'/%3E%3Cg id='g'%3E%3Cuse href='%23s' transform='scale(0.12) rotate(60)'/%3E%3Cuse href='%23s' transform='scale(0.2) rotate(10)'/%3E%3Cuse href='%23s' transform='scale(0.25) rotate(40)'/%3E%3Cuse href='%23s' transform='scale(0.3) rotate(-20)'/%3E%3Cuse href='%23s' transform='scale(0.4) rotate(-30)'/%3E%3Cuse href='%23s' transform='scale(0.5) rotate(20)'/%3E%3Cuse href='%23s' transform='scale(0.6) rotate(60)'/%3E%3Cuse href='%23s' transform='scale(0.7) rotate(10)'/%3E%3Cuse href='%23s' transform='scale(0.835) rotate(-40)'/%3E%3Cuse href='%23s' transform='scale(0.9) rotate(40)'/%3E%3Cuse href='%23s' transform='scale(1.05) rotate(25)'/%3E%3Cuse href='%23s' transform='scale(1.2) rotate(8)'/%3E%3Cuse href='%23s' transform='scale(1.333) rotate(-60)'/%3E%3Cuse href='%23s' transform='scale(1.45) rotate(-30)'/%3E%3Cuse href='%23s' transform='scale(1.6) rotate(10)'/%3E%3C/g%3E%3C/defs%3E%3Cg transform='rotate(0 0 0)'%3E%3Cg transform='rotate(0 0 0)'%3E%3Ccircle fill='url(%23a)' r='3000'/%3E%3Cg opacity='0.5'%3E%3Ccircle fill='url(%23a)' r='2000'/%3E%3Ccircle fill='url(%23a)' r='1800'/%3E%3Ccircle fill='url(%23a)' r='1700'/%3E%3Ccircle fill='url(%23a)' r='1651'/%3E%3Ccircle fill='url(%23a)' r='1450'/%3E%3Ccircle fill='url(%23a)' r='1250'/%3E%3Ccircle fill='url(%23a)' r='1175'/%3E%3Ccircle fill='url(%23a)' r='900'/%3E%3Ccircle fill='url(%23a)' r='750'/%3E%3Ccircle fill='url(%23a)' r='500'/%3E%3Ccircle fill='url(%23a)' r='380'/%3E%3Ccircle fill='url(%23a)' r='250'/%3E%3C/g%3E%3Cg transform='rotate(0 0 0)'%3E%3Cuse href='%23g' transform='rotate(10)'/%3E%3Cuse href='%23g' transform='rotate(120)'/%3E%3Cuse href='%23g' transform='rotate(240)'/%3E%3C/g%3E%3Ccircle fill-opacity='0.1' fill='url(%23a)' r='3000'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            background-attachment: fixed;
            background-size: cover;
        }

        .review-card {
            border: 1px solid #dee2e6;
            border-radius: 20px;
            margin-bottom: 20px;
            padding: 20px;
        }

        .review-card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            padding: 10px;
            border: black solid 1px;
        }

        .review-card-body {
            border: black solid 1px;
        }

        .star-rating {
            color: #FFD700;
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .fade-in.fade-in-visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <?php include 'user_navbar.php'; ?>

    <div class="container mt-5 fade-in">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h1 class="text-center mb-4" style="padding-top: 10px;">Technical Support and Assistance</h1>

                <div class="support-info">
                    <h3 class="text-center">Technical Support Hotline</h3>
                    <p class="text-center">For technical assistance, please contact our hotline:</p>
                    <p class="text-center"><strong>+63 915 528 5651</strong></p>
                    <hr>
                    <h3 class="text-center">Contact and Assistance Procedure</h3>
                    <p class="text-center">If you require support or assistance, please follow the steps below:</p>
                    <ol>
                        <li>Contact our technical support hotline.</li>
                        <li>Provide details of your issue or inquiry.</li>
                        <li>Our support team will guide you through troubleshooting steps or provide necessary
                            assistance.</li>
                        <li>If required, further actions such as account adjustments or service arrangements will be
                            provided.</li>
                    </ol>
                    <hr>

                    <h3 class="text-center">Frequently Asked Questions (FAQ)</h3>
                    <div class="accordion" id="faqAccordion" style="padding-bottom: 100px;">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingBooking">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseBooking" aria-expanded="true"
                                    aria-controls="collapseBooking">
                                    How do I book a swimming package?
                                </button>
                            </h2>
                            <div id="collapseBooking" class="accordion-collapse collapse show"
                                aria-labelledby="headingBooking" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    You can book a swimming package by visiting our <a
                                        href="swimming_packages.php">package page</a> and selecting your preferred
                                    package. Follow the on-screen instructions to complete your booking.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCancellation">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseCancellation" aria-expanded="false"
                                    aria-controls="collapseCancellation">
                                    How can I cancel my reservation?
                                </button>
                            </h2>
                            <div id="collapseCancellation" class="accordion-collapse collapse"
                                aria-labelledby="headingCancellation" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    To cancel your reservation, please visit your <a href="profilecopy.php">profile
                                        page</a>. Cancellations must be made at least 24 hours in advance.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingAddons">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseAddons" aria-expanded="false"
                                    aria-controls="collapseAddons">
                                    Can I add additional services to my package?
                                </button>
                            </h2>
                            <div id="collapseAddons" class="accordion-collapse collapse" aria-labelledby="headingAddons"
                                data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, you can add additional services during the booking process on the <a
                                        href="swimming_packages.php">package page</a> by selecting the add-ons you want.
                                    If you need to add services after booking, please contact our support team.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingAccount">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseAccount" aria-expanded="false"
                                    aria-controls="collapseAccount">
                                    How do I manage my account?
                                </button>
                            </h2>
                            <div id="collapseAccount" class="accordion-collapse collapse"
                                aria-labelledby="headingAccount" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    You can manage your account by visiting your <a href="profilecopy.php">profile
                                        page</a>. Here, you can update your personal information, view your booking
                                    history, and manage your subscriptions.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            var fadeElems = document.querySelectorAll('.fade-in');

            var fadeCallback = function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in-visible');
                        observer.unobserve(entry.target);
                    }
                });
            };

            var fadeObserver = new IntersectionObserver(fadeCallback, {
                rootMargin: '0px',
                threshold: 0.2
            });

            fadeElems.forEach(function (elem) {
                fadeObserver.observe(elem);
            });
        });
    </script>
    <script async data-id="9364713225" id="chatling-embed-script" type="text/javascript"
        src="https://chatling.ai/js/embed.js"></script>
</body>

</html>