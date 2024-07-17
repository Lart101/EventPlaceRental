<?php require 'config.php';

$username = '';

// Check if admin_id is set in session
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}


$sql = "SELECT username FROM adminaccount WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {

    $stmt->bind_param("i", $_SESSION['admin_id']);


    $stmt->execute();


    $stmt->bind_result($username);


    $stmt->fetch();


    $stmt->close();
}

?>
<style>
    .collage {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }

    .collage img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .collage img:hover {
        transform: scale(1.1);
    }

    .image-viewer {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 999;
        text-align: center;
        overflow-y: auto;
    }

    .image-viewer img {
        max-width: 90%;
        max-height: 90%;
        margin: 20px auto;
        display: block;
    }


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

    .nav-item.logout button:hover {
        color: #fff;
        background-color: #dc3545;
    }
</style>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-lg">
        <a class="navbar-brand" style=" color: black;">
            <img src="img\profile\logo.jpg" alt="Logo" width="30" class="d-inline-block align-text-top">
            Board Mart Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="mx-auto">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_package.php">Package</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="admin_reservations.php">Reservation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="report.php">Report</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            More
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="admin_reviews.php">Reviews</a></li>
                            <li><a class="dropdown-item" href="AdminUserAccPanel.php">User</a></li>
                            <li><a class="dropdown-item" href="admin_account.php">Admin</a></li>
                           
                            <li><a class="dropdown-item" href="admin_gallery.php">Gallery</a></li>
                    


                        </ul>
                    </li>





                    <?php

                    if (!isset($_SESSION['admin_id'])):
                        ?>

                        <li class="nav-item login">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php else: ?>

                        <li class="nav-item logout">
                            <form action="logout.php" method="POST">
                                <button type="submit" class="nav-link btn btn-link"
                                    onclick="return confirmLogout()">Logout</button>
                            </form>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link">Welcome, <?php echo htmlspecialchars($username); ?>!</span>
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