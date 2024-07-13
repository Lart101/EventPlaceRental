<?php Session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="aboutStyle.css">
  <title>Job Openings - Board Marts EventPlace</title>
  <style>
    body {
      background-color: #ffffff;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25'%3E%3Cdefs%3E%3ClinearGradient id='a' gradientUnits='userSpaceOnUse' x1='0' x2='0' y1='0' y2='100%25' gradientTransform='rotate(240)'%3E%3Cstop offset='0' stop-color='%23ffffff'/%3E%3Cstop offset='1' stop-color='%23B5FFE2'/%3E%3C/linearGradient%3E%3Cpattern patternUnits='userSpaceOnUse' id='b' width='540' height='450' x='0' y='0' viewBox='0 0 1080 900'%3E%3Cg fill-opacity='0.1'%3E%3Cpolygon fill='%23444' points='90 150 0 300 180 300'/%3E%3Cpolygon points='90 150 180 0 0 0'/%3E%3Cpolygon fill='%23AAA' points='270 150 360 0 180 0'/%3E%3Cpolygon fill='%23DDD' points='450 150 360 300 540 300'/%3E%3Cpolygon fill='%23999' points='450 150 540 0 360 0'/%3E%3Cpolygon points='630 150 540 300 720 300'/%3E%3Cpolygon fill='%23DDD' points='630 150 720 0 540 0'/%3E%3Cpolygon fill='%23444' points='810 150 720 300 900 300'/%3E%3Cpolygon fill='%23FFF' points='810 150 900 0 720 0'/%3E%3Cpolygon fill='%23DDD' points='990 150 900 300 1080 300'/%3E%3Cpolygon fill='%23444' points='990 150 1080 0 900 0'/%3E%3Cpolygon fill='%23DDD' points='90 450 0 600 180 600'/%3E%3Cpolygon points='90 450 180 300 0 300'/%3E%3Cpolygon fill='%23666' points='270 450 180 600 360 600'/%3E%3Cpolygon fill='%23AAA' points='270 450 360 300 180 300'/%3E%3Cpolygon fill='%23DDD' points='450 450 360 600 540 600'/%3E%3Cpolygon fill='%23999' points='450 450 540 300 360 300'/%3E%3Cpolygon fill='%23999' points='630 450 540 600 720 600'/%3E%3Cpolygon fill='%23FFF' points='630 450 720 300 540 300'/%3E%3Cpolygon points='810 450 720 600 900 600'/%3E%3Cpolygon fill='%23DDD' points='810 450 900 300 720 300'/%3E%3Cpolygon fill='%23AAA' points='990 450 900 600 1080 600'/%3E%3Cpolygon fill='%23444' points='990 450 1080 300 900 300'/%3E%3Cpolygon fill='%23222' points='90 750 0 900 180 900'/%3E%3Cpolygon points='270 750 180 900 360 900'/%3E%3Cpolygon fill='%23DDD' points='270 750 360 600 180 600'/%3E%3Cpolygon points='450 750 540 600 360 600'/%3E%3Cpolygon points='630 750 540 900 720 900'/%3E%3Cpolygon fill='%23444' points='630 750 720 600 540 600'/%3E%3Cpolygon fill='%23AAA' points='810 750 720 900 900 900'/%3E%3Cpolygon fill='%23666' points='810 750 900 600 720 600'/%3E%3Cpolygon fill='%23999' points='990 750 900 900 1080 900'/%3E%3Cpolygon fill='%23999' points='180 0 90 150 270 150'/%3E%3Cpolygon fill='%23444' points='360 0 270 150 450 150'/%3E%3Cpolygon fill='%23FFF' points='540 0 450 150 630 150'/%3E%3Cpolygon points='900 0 810 150 990 150'/%3E%3Cpolygon fill='%23222' points='0 300 -90 450 90 450'/%3E%3Cpolygon fill='%23FFF' points='0 300 90 150 -90 150'/%3E%3Cpolygon fill='%23FFF' points='180 300 90 450 270 450'/%3E%3Cpolygon fill='%23666' points='180 300 270 150 90 150'/%3E%3Cpolygon fill='%23222' points='360 300 270 450 450 450'/%3E%3Cpolygon fill='%23FFF' points='360 300 450 150 270 150'/%3E%3Cpolygon fill='%23444' points='540 300 450 450 630 450'/%3E%3Cpolygon fill='%23222' points='540 300 630 150 450 150'/%3E%3Cpolygon fill='%23AAA' points='720 300 630 450 810 450'/%3E%3Cpolygon fill='%23666' points='720 300 810 150 630 150'/%3E%3Cpolygon fill='%23FFF' points='900 300 810 450 990 450'/%3E%3Cpolygon fill='%23999' points='900 300 990 150 810 150'/%3E%3Cpolygon points='0 600 -90 750 90 750'/%3E%3Cpolygon fill='%23666' points='0 600 90 450 -90 450'/%3E%3Cpolygon fill='%23AAA' points='180 600 90 750 270 750'/%3E%3Cpolygon fill='%23444' points='180 600 270 450 90 450'/%3E%3Cpolygon fill='%23444' points='360 600 270 750 450 750'/%3E%3Cpolygon fill='%23999' points='360 600 450 450 270 450'/%3E%3Cpolygon fill='%23666' points='540 600 630 450 450 450'/%3E%3Cpolygon fill='%23222' points='720 600 630 750 810 750'/%3E%3Cpolygon fill='%23FFF' points='900 600 810 750 990 750'/%3E%3Cpolygon fill='%23222' points='900 600 990 450 810 450'/%3E%3Cpolygon fill='%23DDD' points='0 900 90 750 -90 750'/%3E%3Cpolygon fill='%23444' points='180 900 270 750 90 750'/%3E%3Cpolygon fill='%23FFF' points='360 900 450 750 270 750'/%3E%3Cpolygon fill='%23AAA' points='540 900 630 750 450 750'/%3E%3Cpolygon fill='%23FFF' points='720 900 810 750 630 750'/%3E%3Cpolygon fill='%23222' points='900 900 990 750 810 750'/%3E%3Cpolygon fill='%23222' points='1080 300 990 450 1170 450'/%3E%3Cpolygon fill='%23FFF' points='1080 300 1170 150 990 150'/%3E%3Cpolygon points='1080 600 990 750 1170 750'/%3E%3Cpolygon fill='%23666' points='1080 600 1170 450 990 450'/%3E%3Cpolygon fill='%23DDD' points='1080 900 1170 750 990 750'/%3E%3C/g%3E%3C/pattern%3E%3C/defs%3E%3Crect x='0' y='0' fill='url(%23a)' width='100%25' height='100%25'/%3E%3Crect x='0' y='0' fill='url(%23b)' width='100%25' height='100%25'/%3E%3C/svg%3E");
      background-attachment: fixed;
      background-size: cover;
    }

    .job-opening {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 20px;
      margin-bottom: 20px;
      padding: 20px;
    }

    .job-opening h3 {
      font-size: 1.5rem;
      color: #343a40;
    }

    .job-opening h4 {
      font-size: 1.2rem;
      color: #6c757d;
      margin-top: 10px;
    }

    .job-opening p,
    .job-opening ul {
      font-size: 1.1rem;
      color: #343a40;
    }

    .job-opening ul {
      margin-top: 10px;
    }

    .job-opening ul li {
      margin-bottom: 5px;
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

  <div class="container mt-5">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <h1 class="text-center mb-4 fade-in" style="padding-top: 10px;">Job Openings</h1>

        <div class="job-opening fade-in">
          <h3 class="text-center">Position: Janitor</h3>
          <h4 class="text-center">Location: BoardMart’s Event Place, 1043 Mendoza, Valenzuela, Metro Manila</h4>
          <h4 class="text-center">Employment Type: Full-time</h4>

          <h3 class="text-center" style="padding-top: 30px;">Job Description:</h3>
          <p class="text-center">The Janitor will be responsible for maintaining cleanliness and tidiness throughout the
            venue, including restrooms, event spaces, and common areas.</p>

          <h3 class="text-center">Responsibilities:</h3>
          <ul>
            <li>Performing cleaning duties such as sweeping, mopping, and dusting.</li>
            <li>Emptying trash containers and ensuring proper disposal of waste.</li>
            <li>Restocking supplies in restrooms and other areas as needed.</li>
            <li>Assisting in setup and cleanup for events.</li>
            <li>Following all safety and sanitation guidelines.</li>
          </ul>

          <h3 class="text-center">Requirements:</h3>
          <ul>
            <li>High school diploma or equivalent.</li>
            <li>Prior experience in janitorial work preferred.</li>
            <li>Ability to handle cleaning chemicals and operate cleaning equipment.</li>
            <li>Good physical health and stamina.</li>
            <li>Reliability and punctuality.</li>
            <li>Ability to work independently and as part of a team.</li>
          </ul>
        </div>

        <hr>

        <div class="job-opening fade-in">
          <h3 class="text-center">Position: Security Guard</h3>
          <h4 class="text-center">Location: BoardMart’s Event Place, 1043 Mendoza, Valenzuela, Metro Manila</h4>
          <h4 class="text-center">Employment Type: Full-time</h4>

          <h3 class="text-center" style="padding-top: 30px;">Job Description:</h3>
          <p class="text-center">The Security Guard will ensure the safety and security of the venue, guests, and staff
            by patrolling and monitoring premises and responding to incidents as needed.</p>

          <h3 class="text-center">Responsibilities:</h3>
          <ul>
            <li>Patrolling assigned areas to deter and detect signs of intrusion and ensure security of doors, windows,
              and gates.</li>
            <li>Monitoring surveillance cameras and alarms.</li>
            <li>Responding to emergencies and alarms swiftly and appropriately.</li>
            <li>Maintaining a secure environment by enforcing access control and following procedures.</li>
            <li>Providing assistance and guidance to guests and employees.</li>
          </ul>

          <h3 class="text-center">Requirements:</h3>
          <ul>
            <li>High school diploma or equivalent.</li>
            <li>Prior experience as a security guard preferred.</li>
            <li>Ability to handle emergency situations calmly and effectively.</li>
            <li>Excellent observational and communication skills.</li>
            <li>Physical fitness and ability to stand and walk for extended periods.</li>
            <li>Ability to work various shifts, including nights, weekends, and holidays.</li>
          </ul>
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