<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: swimming_packages.php');
    exit();
}if (isset($_SESSION['is_admin'])) {
    header('Location: admin_package.php');
    exit();
}


require 'config.php';


$error = '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $isAdmin = isset($_POST['is_admin']);

    if ($isAdmin) {
       
        $sql = "SELECT id, password FROM adminaccount WHERE username = ?";
    } else {
      
        $sql = "SELECT id, password FROM users WHERE username = ?";
    }
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($userId, $storedPassword);
    
        if ($stmt->fetch()) {
          
            if ($password === $storedPassword) {
                if ($isAdmin) {
                    $_SESSION['admin_id'] = $userId;
                    header('Location: admin_package.php');
                } else {
                    $_SESSION['user_id'] = $userId;
                    header('Location: swimming_packages.php');
                }
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    
        $stmt->close();
    } else {
        $error = "Failed to prepare the SQL statement: " . $conn->error;
    }
    
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
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

        .hidden-admin {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h1>Login</h1>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="hidden-admin">
                    <input type="checkbox" id="is_admin" name="is_admin">
                    <label for="is_admin"></label>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div>
            <div class="forgot-password-link">
                <p>Forgot your password? <a href="forgot_password.php">Reset Password</a></p>
            </div>
        </div>
    </div>
</div>

<div id="notification" class="notification error"><?php echo $error; ?></div>

<div class='box'>
    <div class='wave -one'></div>
    <div class='wave -two'></div>
    <div class='wave -three'></div>
</div>

<script>
    var message = "<?php echo $error; ?>";
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
