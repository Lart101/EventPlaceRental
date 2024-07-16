<?php
session_start();
// Check if user is not logged in or username is not "@@@@"
// Check if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require 'config.php';








// Fetch pending reviews
$sql_pending = "SELECT * FROM reviews WHERE status = 'Pending' ORDER BY created_at ASC";
$result_pending = $conn->query($sql_pending);

// Fetch accepted reviews
$sql_accepted = "SELECT * FROM reviews WHERE status = 'Accepted' ORDER BY created_at ASC";
$result_accepted = $conn->query($sql_accepted);
if (!$result_accepted) {
    echo "Error fetching accepted reviews: " . $conn->error;
}

// Fetch denied reviews
$sql_denied = "SELECT * FROM reviews WHERE status = 'Denied' ORDER BY created_at ASC";
$result_denied = $conn->query($sql_denied);
if (!$result_denied) {
    echo "Error fetching denied reviews: " . $conn->error;
}

// Function to convert number to ordinal suffix (1st, 2nd, 3rd, etc.)
function ordinal_suffix($num)
{
    $suffix = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if (($num % 100) >= 11 && ($num % 100) <= 13) {
        return $num . 'th';
    } else {
        return $num . $suffix[$num % 10];
    }
}

// Handle actions (Accept or Deny review)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['review_id'])) {
    $review_id = intval($_POST['review_id']);
    $action = $_POST['action']; // 'accept' or 'deny'

    // Update review status
    $update_sql = "UPDATE reviews SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);

    if ($stmt) {
        $stmt->bind_param("si", $action, $review_id);
        $stmt->execute();
        $stmt->close();

        // Redirect to refresh page after action
        header('Location: admin_reviews.php');
        exit();
    } else {
        echo "Failed to prepare SQL statement: " . $conn->error;
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['review_id'])) {
    $reviewId = $_POST['review_id'];
    
    $sql = "UPDATE reviews SET status = 'Denied' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $reviewId);
        if ($stmt->execute()) {
            // Update successful
            echo '<div class="alert alert-success" role="alert">Review deleted successfully!</div>';
            // Optionally, you can reload the page or update the UI dynamically
        } else {
            echo '<div class="alert alert-danger" role="alert">Failed to delete review: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    } else {
        echo '<div class="alert alert-danger" role="alert">Failed to prepare SQL statement: ' . $conn->error . '</div>';
    }
   
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style2.css">
</head>
<style>
    button {
        margin-right: 10px;
    }
</style>

<body>

    <?php include 'admin_navbar.php'; ?>

    <div class="container mt-5">
        <h1>Manage Reviews</h1>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                    type="button" role="tab" aria-controls="pending" aria-selected="true">Pending Reviews</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="accepted-tab" data-bs-toggle="tab" data-bs-target="#accepted" type="button"
                    role="tab" aria-controls="accepted" aria-selected="false">Accepted Reviews</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="denied-tab" data-bs-toggle="tab" data-bs-target="#denied" type="button"
                    role="tab" aria-controls="denied" aria-selected="false">Denied Reviews</button>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content" id="myTabContent">
            <!-- Pending Reviews -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rank = 1;
                        while ($row = $result_pending->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . ordinal_suffix($rank) . '</td>';
                            echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                            echo '<td style="word-wrap: break-word; max-width: 200px;">';
                            $message = htmlspecialchars($row['message']);
                            if (strlen($message) > 200) {
                                $shortened_message = substr($message, 0, 200);
                                echo '<span class="truncate">' . $shortened_message . '</span>';
                                echo '<span class="full-text" style="display: none;">' . substr($message, 200) . '</span>';
                                echo ' <a href="#" class="toggle-text">See more</a>';
                            } else {
                                echo $message;
                            }
                            echo '</td>';

                            echo '<td>' . htmlspecialchars($row['rating']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                            echo '<td>';
                            echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST" style="display:inline;">';
                            echo '<input type="hidden" name="review_id" value="' . $row['id'] . '">';
                            echo '<button type="submit" name="action" value="accepted" class="btn btn-success" onclick="return confirm(\'Are you sure you want to accept this review?\')">Accept</button>';
                            echo '</form>';
                            echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST" style="display:inline;">';
                            echo '<input type="hidden" name="review_id" value="' . $row['id'] . '">';
                            echo '<button type="submit" name="action" value="denied" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to deny this review?\')">Deny</button>';
                            echo '</form>';
                            echo '</td>';
                            echo '</tr>';
                            $rank++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const toggleTextLinks = document.querySelectorAll('.toggle-text');

                    toggleTextLinks.forEach(function (link) {
                        link.addEventListener('click', function (event) {
                            event.preventDefault();
                            const parentCell = event.target.parentElement;
                            const truncateSpan = parentCell.querySelector('.truncate');
                            const fullTextSpan = parentCell.querySelector('.full-text');

                            if (truncateSpan.style.display !== 'none') {
                                truncateSpan.style.display = 'none';
                                fullTextSpan.style.display = 'inline';
                                link.textContent = 'See less';
                            } else {
                                truncateSpan.style.display = 'inline';
                                fullTextSpan.style.display = 'none';
                                link.textContent = 'See more';
                            }
                        });
                    });
                });
            </script>

            <!-- Accepted Reviews -->
            <div class="tab-pane fade" id="accepted" role="tabpanel" aria-labelledby="accepted-tab">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rank = 1;
            while ($row = $result_accepted->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . ordinal_suffix($rank) . '</td>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td style="word-wrap: break-word; max-width: 200px;">';
                $message = htmlspecialchars($row['message']);
                if (strlen($message) > 200) {
                    $shortened_message = substr($message, 0, 200);
                    echo '<span class="truncate">' . $shortened_message . '</span>';
                    echo '<span class="full-text" style="display: none;">' . substr($message, 200) . '</span>';
                    echo ' <a href="#" class="toggle-text">See more</a>';
                } else {
                    echo $message;
                }
                echo '</td>';

                echo '<td>' . htmlspecialchars($row['rating']) . '</td>';
                echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                
                // Action column with Delete button
                echo '<td>';
                echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST" style="display:inline;">';
                echo '<input type="hidden" name="review_id" value="' . $row['id'] . '">';
                echo '<button type="submit" name="action" value="denied" class="btn btn-danger btn-sm delete-btn" onclick="return confirm(\'Are you sure you want to delete this review?\')">Delete</button>';
                echo '</form>';
                echo '</td>';
                
                echo '</tr>';
                $rank++;
            }
            ?>
        </tbody>
    </table>
</div>


            <!-- Denied Reviews -->
            <div class="tab-pane fade" id="denied" role="tabpanel" aria-labelledby="denied-tab">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rank = 1;
            while ($row = $result_denied->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . ordinal_suffix($rank) . '</td>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td style="word-wrap: break-word; max-width: 200px;">';
                $message = htmlspecialchars($row['message']);
                if (strlen($message) > 200) {
                    $shortened_message = substr($message, 0, 200);
                    echo '<span class="truncate">' . $shortened_message . '</span>';
                    echo '<span class="full-text" style="display: none;">' . substr($message, 200) . '</span>';
                    echo ' <a href="#" class="toggle-text">See more</a>';
                } else {
                    echo $message;
                }
                echo '</td>';

                echo '<td>' . htmlspecialchars($row['rating']) . '</td>';
                echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                
                // Action column with Delete button
                echo '<td>';
                echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST" style="display:inline;">';
                echo '<input type="hidden" name="review_id" value="' . $row['id'] . '">';
                echo '<button type="submit" name="action" value="Pending" class="btn btn-danger btn-sm delete-btn" onclick="return confirm(\'Are you sure you want to undo this review?\')">Undo</button>';
                echo '</form>';
                echo '</td>';
                
                echo '</tr>';
                $rank++;
            }
            ?>
        </tbody>
    </table>
</div>

        </div>
    </div>

</body>

</html>