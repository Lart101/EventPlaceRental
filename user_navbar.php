<?php 

require 'config.php'; 
$username = 'Guest';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
 
    $sql = "SELECT username FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = ucfirst($row['username']);
    }
    
    $stmt->close();
}


$conn->close();

?>

<style>


    /* NavBar to Repa */

    .navbar {
        background-color: whitesmoke;

    }

    .navbar-nav .nav-link {
        color: black;
        font-size: 1.1rem;
    }

    .navbar-nav .nav-item {
        padding: 0 1rem;
    }

    .navbar .navbar-nav .nav-item {
        position: relative;
    }

    .navbar .navbar-nav .nav-item::after {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        content: '';
        background-color: black;
        width: 0%;
        height: 4px;
        transition: 500ms;
    }

    .navbar .navbar-nav .nav-item:hover:after {
        width: 100%;
    }

    .nav-item.logout button {
        color: #dc3545;
        background-color: transparent;
        border: none;
        cursor: pointer;
    }
    .nav-item.login {
            margin-left: auto; 
        }

        .nav-item.login a {
            color: #007bff;
        }

    .nav-item.logout button:hover {
        color: #fff;
        background-color: #dc3545;
    }
</style>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-lg">
        <a class="navbar-brand" href="index.html">
            <img src="img\profile\logo.jpg" alt="Logo" width="30" class="d-inline-block align-text-top">
            Board Mart Event Place
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="mx-auto">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index1.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="aboutus.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="swimming_packages.php">Packages</a>
                    </li>
                   
                    <li class="nav-item">
                        <a class="nav-link" href="profilecopy.php">Profile</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            More
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="reviews.php">Customer Reviews</a></li>
                            <li><a class="dropdown-item" href="support.php">Support</a></li>
                            <li><a class="dropdown-item" href="career.php">Career</a></li>
                            <li><a class="dropdown-item" href="gallery.php">Gallery</a></li>
                            <li><a class="dropdown-item" href="ameneties.php">Amenities</a></li>
                            <li><a class="dropdown-item" href="contactmain.php">Contact</a></li>
                            <li><a class="dropdown-item" href="feedback.php">Feedback</a></li>
                            
                           
                        </ul>
                    </li>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <li class="nav-item login">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php else: ?>
                        
                        <li class="nav-item logout">
                            <form action="logout.php" method="POST">
                                <button type="submit" class="nav-link btn btn-link" onclick="return confirmLogout()">Logout</button>
                            </form>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link" >Welcome, <?php echo htmlspecialchars($username); ?>!</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</nav>

    <script>
    

    function confirmLogout() {
        return confirm('Are you sure you want to logout?');
    }
</script>