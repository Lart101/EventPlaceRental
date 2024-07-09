<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="login.css">
    <style>
        /* styles.css */
        body {
            font-family: Arial, sans-serif;
           
            padding: 20px;
        }

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

        .otp-container {
            display: flex;
            align-items: center;
        }

        .otp-container input {
            flex: 1;
            margin-right: 10px;
        }

        .resend-btn {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px;
            cursor: pointer;
            width: 70px;
        }

        .resend-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
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
    <form id="reset-password-form" action="verify_otp.php" method="POST">
        <button class="back-btn" onclick="window.history.back();">Back</button>
        <h2>Reset Password</h2>
        <div class="otp-container">
            <label for="otp">Enter OTP:</label>
            <input type="text" id="otp" name="otp" required>
            <button type="button" class="resend-btn" id="resend-btn" disabled>Resend OTP</button>
        </div>
        <label for="new-password">New Password:</label>
<input type="password" id="new-password" name="new_password" required
       pattern="(?=.*[a-z])(?=.*[A-Z]).{6,}"
       title="Password must contain at least one uppercase letter, one lowercase letter, and be at least 6 characters long."
       placeholder="At least 1 uppercase, 1 lowercase, min 6 characters">

        <label for="confirm-password">Confirm Password:</label>
        <input type="password" id="confirm-password" name="confirm_password" required>
        <button type="submit">Reset Password</button>
    </form>

    <div class='box'>
        <div class='wave -one'></div>
        <div class='wave -two'></div>
        <div class='wave -three'></div>
    </div>

    <div id="response"></div>

    <script>
        document.getElementById('reset-password-form').addEventListener('submit', function(event) {
            event.preventDefault();
            var form = event.target;
            var newPassword = document.getElementById('new-password').value;
            var confirmPassword = document.getElementById('confirm-password').value;

            if (newPassword !== confirmPassword) {
                document.getElementById('response').innerText = 'Passwords do not match!';
                return;
            }

            fetch(form.action, {
                method: form.method,
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('response').innerText = data.message;
                if (data.status === 'success') {
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 1000); // Redirect after 1 second
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        function enableResendButton() {
            var resendButton = document.getElementById('resend-btn');
            resendButton.disabled = false;
            resendButton.innerText = 'Resend OTP';
        }

        function disableResendButton() {
            var resendButton = document.getElementById('resend-btn');
            resendButton.disabled = true;
            var count = 30; // Countdown time in seconds
            resendButton.innerText = 'Resend OTP (' + count + 's)';
            var countdown = setInterval(function() {
                count--;
                resendButton.innerText = 'Resend OTP (' + count + 's)';
                if (count === 0) {
                    clearInterval(countdown);
                    enableResendButton();
                }
            }, 1000); // Update every second
        }

        disableResendButton(); // Initially disable the resend button

        document.getElementById('resend-btn').addEventListener('click', function() {
            fetch('resend_otp.php', {
                method: 'POST',
                body: new URLSearchParams({ email: 'your_user_email' }) // Replace with actual user email if needed
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('response').innerText = data.message;
                disableResendButton(); // Disable the resend button again
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
