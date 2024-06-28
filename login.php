<?php
error_reporting(0); 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "client_records";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM clients WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            session_start();
            setcookie("username", $username, time() + (86400 * 30), "/"); // Set cookie for 30 days
            $_SESSION['login_time'] = time(); // Store the login time in session
            $_SESSION['message'] = "Access Granted";
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            $message = "Password is incorrect";
        }
    } else {
        $message = "Username is incorrect";
    }
}

if (isset($_COOKIE['username'])) {
    session_start();
    if (!isset($_SESSION['page_count'])) {
        $_SESSION['page_count'] = 0;
        $_SESSION['creation_time'] = time();
    } else {
        $_SESSION['page_count']++;
    }
    $_SESSION['last_access'] = time();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style1.css">
    <style>
        .notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            color: white;
            z-index: 1000;
            border-radius: 5px;
        }
        
        .notification.success {
            background-color: #4CAF50; 
        }
        
        .notification.error {
            background-color: #f44336;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-left">
            <div class="title">Login</div>
            <form id="loginForm" method="post" action="">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <div class="buttons">
                    <button type="submit">Login</button>
                </div>
            </form>
           
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Sign Up</a></p>
            </div>
        </div>
    </div>
    <div class='box'>
        <div class='wave -one'></div>
        <div class='wave -two'></div>
        <div class='wave -three'></div>
    </div>

    <script>
        var message = "<?php echo $message; ?>";
        if (message.trim() !== "") {
            var notification = document.getElementById('notification');
            notification.style.display = 'block';
            setTimeout(function() {
                notification.style.display = 'none';
            }, 5000); 
        }
    </script>
</body>
</html>
