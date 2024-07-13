<?php
session_start();
// Check if user is not logged in or username is not "@@@@"
if (!isset($_SESSION['user_id'])) {
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

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style2.css">
</head>
<style>
    button{
        margin-right:10px ;
    }
    
</style>
<body>
  
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container mt-5">
        <h1>Manage Reviews</h1>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">Pending Reviews</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="accepted-tab" data-bs-toggle="tab" data-bs-target="#accepted" type="button" role="tab" aria-controls="accepted" aria-selected="false">Accepted Reviews</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="denied-tab" data-bs-toggle="tab" data-bs-target="#denied" type="button" role="tab" aria-controls="denied" aria-selected="false">Denied Reviews</button>
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
                            echo '<td>' . htmlspecialchars($row['message']) . '</td>';
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
                            echo '<td>' . htmlspecialchars($row['message']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['rating']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['status']) . '</td>';
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
                            echo '<td>' . htmlspecialchars($row['message']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['rating']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['status']) . '</td>';
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
