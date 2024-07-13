<?php 
session_start();

require 'config.php';

// Fetch accepted reviews
$sql_accepted = "SELECT * FROM reviews WHERE status = 'Accepted' ORDER BY created_at DESC";
$result_accepted = $conn->query($sql_accepted);

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accepted Reviews - Board Mart Event Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="contact.css">
    <style>
        /* Additional styles can be added here */
        .review-card {
            border: 1px solid #e1e1e1;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
        }
        .review-card h4 {
            color: #007bff;
            margin-bottom: 10px;
        }
        .review-card p {
            margin-bottom: 5px;
        }
        .review-card .meta-info {
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>

<?php include 'user_navbar.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">Accepted Reviews</h1>

    <?php
    if ($result_accepted->num_rows > 0) {
        while ($row = $result_accepted->fetch_assoc()) {
            echo '<div class="review-card">';
            echo '<h4>' . htmlspecialchars($row['name']) . '</h4>';
            echo '<p>' . htmlspecialchars($row['message']) . '</p>';
            echo '<div class="meta-info">';
            echo '<p><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</p>';
            echo '<p><strong>Rating:</strong> ' . htmlspecialchars($row['rating']) . '</p>';
            echo '<p><strong>Posted on:</strong> ' . date('F j, Y', strtotime($row['created_at'])) . '</p>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No reviews found.</p>';
    }
    ?>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
