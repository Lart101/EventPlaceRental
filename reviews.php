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
        body {
            background: #f8f9fa;
            font-family: 'Comic Sans MS', cursive, sans-serif;
        }
        .container {
            margin-bottom: 20%;
        }
        h1 {
            color: #ff5722;
            text-align: center;
            font-size: 3rem;
        }
        .review-card {
            border: 2px solid #ffeb3b;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .review-card:hover {
            transform: scale(1.05);
        }
        .review-card h4 {
            color: #ff5722;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .review-card h4::before {
            content: "⭐ ";
            font-size: 1.5rem;
        }
        .review-card p {
            margin-bottom: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        .review-card p.full-text {
            -webkit-line-clamp: unset;
        }
        .review-card .meta-info {
            color: #6c757d;
            font-size: 14px;
        }
        .read-more {
            cursor: pointer;
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include 'user_navbar.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">🎉 Reviews 🎉</h1>

    <?php
    if ($result_accepted->num_rows > 0) {
        while ($row = $result_accepted->fetch_assoc()) {
            echo '<div class="review-card">';
            echo '<h4>' . htmlspecialchars($row['name']) . '</h4>';
            echo '<p class="review-text">' . htmlspecialchars($row['message']) . '</p>';
            echo '<div class="meta-info">';
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const readMoreBtns = document.querySelectorAll('.read-more');
        readMoreBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const reviewText = this.previousElementSibling.previousElementSibling;
                if (reviewText.classList.contains('full-text')) {
                    reviewText.classList.remove('full-text');
                    this.textContent = 'Read more';
                } else {
                    reviewText.classList.add('full-text');
                    this.textContent = 'Read less';
                }
            });
        });
    });
</script>
</body>
</html>
