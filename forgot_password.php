<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="login.css">
    <style>
        /* styles.css */
      
        h1 {
            text-align: center;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        #response {
            text-align: center;
            margin-top: 20px;
        }

        .notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            color: white;
            background-color: #4CAF50; /* Green */
            border-radius: 5px;
            z-index: 1000;
        }

        .loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            color: white;
            border-radius: 8px;
        }
        .back-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px;
            cursor: pointer;
            margin-bottom: 20px;
            width: 20%;
        }

        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    
    <form id="forgot-password-form" action="send_otp.php" method="POST">
    <button class="back-btn" onclick="window.history.back();">Back</button>
    <h2>Forgot Password</h2>
 
        <label for="email">Enter your email:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Send OTP</button>
    </form>
    

    <div id="response"></div>
    <div id="loading" class="loading">Sending OTP...</div>
    <div id="notification" class="notification">OTP sent successfully! Redirecting...</div>

    <div class='box'>
    <div class='wave -one'></div>
    <div class='wave -two'></div>
    <div class='wave -three'></div>
</div>
    <script>
        document.getElementById('forgot-password-form').addEventListener('submit', function(event) {
            event.preventDefault();
            var form = event.target;
            var loadingDiv = document.getElementById('loading');
            var notificationDiv = document.getElementById('notification');

            loadingDiv.style.display = 'block';

            fetch(form.action, {
                method: form.method,
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                loadingDiv.style.display = 'none';
                document.getElementById('response').innerText = data.message;

                if (data.status === 'success') {
                    notificationDiv.style.display = 'block';
                    setTimeout(function() {
                        window.location.href = 'reset_password.php';
                    }, 1000); // Redirect after 1 second
                }
            })
            .catch(error => {
                loadingDiv.style.display = 'none';
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
