
<?php session_start()?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOME - Board Mart Event Place</title>
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
        /*Hero to Repa*/

section.hero4{
    background-color: #F6FDC3;
    padding-top: 72px;
    text-align: justify;
  
}

section.hero4 h1{
text-align: center;
  
}
.hero4 .col-sm-12 {
    text-align: center;
  }


  .hero4 .col-sm-6 {
    margin-bottom: 15px;
  }
  

section.hero2{
    background-color: #F5DAD2;
    padding-top: 72px;
    text-align: justify;
}
section.hero3{
    background-color: #756AB6;
    padding-top: 72px;
    text-align: justify;
}

.hero2 .col-sm-6 {
    margin-bottom: 30px; 
  }
  .hero3 .col-sm-12 {
    text-align: center;
  }

.hero2 h1 {
    margin-bottom: 30px; 
    text-align: center;
}

section.hero{
    background-color: #ffdfc0;
    padding-top: 72px;
    text-align: justify;
}

section.hero1{
    background-color: #BED7DC;
    padding-top: 72px;
    text-align: justify;
}
section.hero1 img {
   width: 50px;
   display: block;
   margin: 0 auto; /* Set margin to auto to center horizontally */
}



section.hero img{
    background-color: #ffdfc0;
    padding-top: 52px;
}



/*Card to Repa */
.grid{
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 30px;

}
.card{
    grid-column: span 4;
    margin-bottom: 60px;
    transition: 300ms;
    cursor: pointer;
    border: solid black;
}

.card:hover{
    transform: scale(1.03);
}

.img-container {
    overflow: hidden;
    height: 200px; /* Adjust as needed */
  }
  
  .img-container img {
    width: 100%;
    height: auto;
  }
  
  .btn-buy {
    display: block;
    margin-top: 10px;
    text-align: center;
  }
  
  .btn-buy:hover {
    background-color: gold;
    border-color: gold;
    color: black;
  }
  
  
  

@media screen and (min-width:1024px) {
    section[class^="container"]{
        padding: 4rem 2rem;
    }

    nav [class^="container"]{
        padding: 0 4rem;
        color: white;

    }
  
    
    
}
@media screen and (max-width:960px) {
    .timeline{
        grid-column: 1 / span 12;
    }
    .timeline .checkpoint{
        grid-column: 1 / span 12;
        width: 100%;
        transform: none;
        padding-left: 0;
        padding-right: 0;
        border: none;
    }
    .timeline .checkpoint::before{
        grid-column: 1 / span 12;
      
    }
    .timeline .checkpoint div ::before{
        grid-column: 1 / span 12;
      
    }


}
@media screen and (max-width: 576px) {
    .navbar-nav .nav-item{
        padding: 0 1rem;
    }
    section.hero{
        text-align: center;
    }
    section.hero img{
        width: 70%;
    }
    section.hero1{
        text-align: center;
    }
  
    .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
        margin: 15px;
            text-align: center;
      }
}
    
.bg {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/profile/bg.jpg');
            background-size: cover;
            background-position: center;
            padding: 20px;
            padding-top: 20%;
            color: whitesmoke;
            height: 800px;
        }
    </style>
</head>
<body>

    <?php include 'user_navbar.php'; ?>

    <section class="bg" id="hero">
  <div class="container-lg fade-in" style="margin-bottom: 20%;">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h1 class="display- fw-bold" style="color: #FFFAB7;">Board Mart Event Place</h1>
        <p style="color: whitesmoke;">Experience the best event venue in Arkong Bato Valenzuela City. From swimming packages to elegant setups, we have everything to make your event memorable!</p>
        <a class="btn btn-outline-light btn-lg" href="#nature-of-business">Read more</a>
      </div>
    </div>
  </div>
</section>


<section id="nature-of-business" class="py-5 fade-in">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <h2>Nature of Business</h2>
        <p>Board Mart Event Place specializes in providing top-notch venues and packages for all kinds of events. Whether you're planning a birthday party, wedding, or corporate gathering, we ensure a seamless and enjoyable experience for all our clients.</p>
      </div>
      <div class="col-md-6 d-flex justify-content-center">
        <img src="img\profile\1.jpg" style="width: 500px; border-radius: 20px;" class="img-fluid" alt="Event Place">
      </div>
    </div>
  </div>
</section>


<section id="history" class="bg-light py-5 fade-in" style="background-color: #76F3FF;
  background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100%25\' height=\'100%25\' viewBox=\'0 0 1600 800\'%3E%3Cg %3E%3Cpath fill=\'%2388f5ff\' d=\'M486 705.8c-109.3-21.8-223.4-32.2-335.3-19.4C99.5 692.1 49 703 0 719.8V800h843.8c-115.9-33.2-230.8-68.1-347.6-92.2C492.8 707.1 489.4 706.5 486 705.8z\'/%3E%3Cpath fill=\'%2399f7ff\' d=\'M1600 0H0v719.8c49-16.8 99.5-27.8 150.7-33.5c111.9-12.7 226-2.4 335.3 19.4c3.4 0.7 6.8 1.4 10.2 2c116.8 24 231.7 59 347.6 92.2H1600V0z\'/%3E%3Cpath fill=\'%23abf9ff\' d=\'M478.4 581c3.2 0.8 6.4 1.7 9.5 2.5c196.2 52.5 388.7 133.5 593.5 176.6c174.2 36.6 349.5 29.2 518.6-10.2V0H0v574.9c52.3-17.6 106.5-27.7 161.1-30.9C268.4 537.4 375.7 554.2 478.4 581z\'/%3E%3Cpath fill=\'%23bcfbff\' d=\'M0 0v429.4c55.6-18.4 113.5-27.3 171.4-27.7c102.8-0.8 203.2 22.7 299.3 54.5c3 1 5.9 2 8.9 3c183.6 62 365.7 146.1 562.4 192.1c186.7 43.7 376.3 34.4 557.9-12.6V0H0z\'/%3E%3Cpath fill=\'%23CEFCFF\' d=\'M181.8 259.4c98.2 6 191.9 35.2 281.3 72.1c2.8 1.1 5.5 2.3 8.3 3.4c171 71.6 342.7 158.5 531.3 207.7c198.8 51.8 403.4 40.8 597.3-14.8V0H0v283.2C59 263.6 120.6 255.7 181.8 259.4z\'/%3E%3Cpath fill=\'%23d5fff0\' d=\'M1600 0H0v136.3c62.3-20.9 127.7-27.5 192.2-19.2c93.6 12.1 180.5 47.7 263.3 89.6c2.6 1.3 5.1 2.6 7.7 3.9c158.4 81.1 319.7 170.9 500.3 223.2c210.5 61 430.8 49 636.6-16.6V0z\'/%3E%3Cpath fill=\'%23dcffe5\' d=\'M454.9 86.3C600.7 177 751.6 269.3 924.1 325c208.6 67.4 431.3 60.8 637.9-5.3c12.8-4.1 25.4-8.4 38.1-12.9V0H288.1c56 21.3 108.7 50.6 159.7 82C450.2 83.4 452.5 84.9 454.9 86.3z\'/%3E%3Cpath fill=\'%23e8ffe4\' d=\'M1600 0H498c118.1 85.8 243.5 164.5 386.8 216.2c191.8 69.2 400 74.7 595 21.1c40.8-11.2 81.1-25.2 120.3-41.7V0z\'/%3E%3Cpath fill=\'%23f7ffeb\' d=\'M1397.5 154.8c47.2-10.6 93.6-25.3 138.6-43.8c21.7-8.9 43-18.8 63.9-29.5V0H643.4c62.9 41.7 129.7 78.2 202.1 107.4C1020.4 178.1 1214.2 196.1 1397.5 154.8z\'/%3E%3Cpath fill=\'%23FFFFF2\' d=\'M1315.3 72.4c75.3-12.6 148.9-37.1 216.8-72.4h-723C966.8 71 1144.7 101 1315.3 72.4z\'/%3E%3C/g%3E%3C/svg%3E');
  background-attachment: fixed;
  background-size: cover;">
  <div class="container">
    <h2>History</h2>
    <p>Board Mart Event Place was established in 2020 during the pandemic to provide a safe and reliable venue for various events. Our commitment to quality and customer satisfaction has helped us quickly become a preferred choice for event hosting in Arkong Bato Valenzuela City.</p>
    <p>Today, we continue to uphold our founding principles while adapting to the changing needs of our clients, ensuring a memorable experience for every event we host.</p>
  </div>
</section>

<section id="environment" class="py-5 fade-in">
  <div class="container">
    <div class="row">
      <div class="col-md-6" style="margin-bottom: 10px;">
        <div class="card h-100">
          <div class="card-body">
            <h2 class="card-title">Environment</h2>
            <p class="card-text">At Board Mart Event Place, we are committed to environmental sustainability. We work closely with our partners to minimize our carbon footprint and promote eco-friendly practices throughout our operations. From implementing efficient energy solutions to supporting local communities, we strive to make a positive impact on the environment.</p>
          </div>
        </div>
      </div>
      <div class="col-md-6" style="margin-bottom: 10px;">
        <div class="card h-100">
          <div class="card-body">
            <h2 class="card-title">Corporate Responsibility</h2>
            <p class="card-text">Corporate responsibility is at the heart of everything we do at Board Mart Event Place. We believe in giving back to the community and continuously seek ways to improve the well-being of our employees, customers, and the society we serve. Our initiatives include local charity events, employee wellness programs, and partnerships with non-profit organizations.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="swimming-packages" class="py-5 fade-in" style="background-color: #f8f9fa;">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6">
        <h2>Swimming Packages</h2>
        <p>Discover our range of swimming packages designed to suit every need. Whether you're planning a family day out or a private swim session, we have the perfect package for you.</p>
        <a href="swimming_packages.php" class="btn btn-primary mt-3">View Packages</a>
      </div>
      <div class="col-md-6 text-center">
        <img src="img\profile\3.jpg" alt="Swimming Pool" class="img-fluid" style="border-radius: 20px;">
      </div>
    </div>
  </div>
</section>




  





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
