<?php
session_start();

// Check if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require 'config.php';


function ordinal_suffix($num)
{
    $suffix = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if (($num % 100) >= 11 && ($num % 100) <= 13) {
        return $num . 'th';
    } else {
        return $num . $suffix[$num % 10];
    }
}

// Fetch pending reservations with start date and end date
$sql = "SELECT pr.*, u.username, sp.package_name, sp.package_type, pr.start_date, pr.end_date
        FROM package_reservations pr
        JOIN users u ON pr.user_id = u.id
        JOIN swimming_packages sp ON pr.package_id = sp.id
        WHERE pr.status = 'Pending'
        ORDER BY pr.start_date ASC, pr.created_at ASC";
$result = $conn->query($sql);

// Fetch accepted reservations
$sql_accepted = "SELECT pr.*, u.username, sp.package_name, sp.package_type, pr.start_date, pr.end_date
        FROM package_reservations pr
        JOIN users u ON pr.user_id = u.id
        JOIN swimming_packages sp ON pr.package_id = sp.id
        WHERE pr.status = 'Accepted'
        ORDER BY pr.start_date ASC, pr.created_at ASC";
$result_accepted = $conn->query($sql_accepted);

// Fetch denied reservations
$sql_denied = "SELECT pr.*, u.username, sp.package_name, sp.package_type, pr.start_date, pr.end_date
        FROM package_reservations pr
        JOIN users u ON pr.user_id = u.id
        JOIN swimming_packages sp ON pr.package_id = sp.id
        WHERE pr.status = 'Denied'
        ORDER BY pr.start_date ASC, pr.created_at ASC";
$result_denied = $conn->query($sql_denied);


if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "SELECT proof_of_payment FROM package_reservations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($proof_of_payment);
    $stmt->fetch();
    $stmt->close();

    header("Content-Type: image/jpeg");
    echo $proof_of_payment;
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style2.css">
</head>
<style>
    button {
        margin-right: 10px;
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
</style>

<body>
<?php include 'admin_navbar.php'; ?>
    <script>


        function confirmLogout() {
            return confirm('Are you sure you want to logout?');
        }
    </script>

    <div class="container mt-5"
        style="max-width: 1200px; width: 100%; margin: 10 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);margin-top:5%">
        <h1>Manage Reservations</h1>





        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                    type="button" role="tab" aria-controls="pending" aria-selected="true">Pending Reservations</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="accepted-tab" data-bs-toggle="tab" data-bs-target="#accepted" type="button"
                    role="tab" aria-controls="accepted" aria-selected="false">Accepted Reservations</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="denied-tab" data-bs-toggle="tab" data-bs-target="#denied" type="button"
                    role="tab" aria-controls="denied" aria-selected="false">Denied Reservations</button>
            </li>
        </ul>



        <!-- Tab panes -->
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Username</th>
                            <th>Package</th>
                            <th>Package Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Proof of Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Initialize variables for grouping by start date
                        $groupedReservations = [];

                        // Group reservations by start date
                        while ($row = $result->fetch_assoc()) {
                            $startDate = $row['start_date'];

                            if (!isset($groupedReservations[$startDate])) {
                                $groupedReservations[$startDate] = [];
                            }

                            // Add reservation to grouped array
                            $groupedReservations[$startDate][] = $row;
                        }

                        // Display reservations grouped by start date with ranking
                        foreach ($groupedReservations as $startDate => $reservations) {
                            $rank = 1;
                            foreach ($reservations as $reservation) {
                                echo '<tr>';
                                echo '<td>' . ordinal_suffix($rank) . '</td>'; // Display ranking as ordinal (1st, 2nd, 3rd, etc.)
                                echo '<td>' . htmlspecialchars($reservation['username']) . '</td>';
                                echo '<td>' . htmlspecialchars($reservation['package_name']) . '</td>';
                                echo '<td>' . htmlspecialchars($reservation['package_type']) . '</td>';
                                echo '<td>' . date('Y-m-d', strtotime($reservation['start_date'])) . '</td>';
                                echo '<td>' . date('Y-m-d', strtotime($reservation['end_date'])) . '</td>';
                                echo '<td><a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-id="' . $reservation['id'] . '">View</a></td>';
                                echo '<td>';
                                echo '<form action="update_reservation.php" method="POST" style="display:inline;">';
                                echo '<input type="hidden" name="reservation_id" value="' . $reservation['id'] . '">';
                                echo '<button type="submit" name="action" value="accept" class="btn btn-success" onclick="return confirm(\'Are you sure you want to accept this reservation? All other reservations with the same start date will be denied.\')">Accept</button>';
                                echo '</form>';
                                echo '<form action="update_reservation.php" method="POST" style="display:inline;">';
                                echo '<input type="hidden" name="reservation_id" value="' . $reservation['id'] . '">';
                                echo '<button type="submit" name="action" value="deny" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to deny this reservation?\')">Deny</button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';
                                $rank++;
                            }
                        }

                        // Function to convert number to ordinal suffix (1st, 2nd, 3rd, etc.)
                        
                        ?>
                    </tbody>
                </table>
            </div>



            <div class="tab-pane fade" id="accepted" role="tabpanel" aria-labelledby="accepted-tab">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Username</th>
                            <th>Package</th>
                            <th>Package Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Proof of Payment</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rank = 1;
                        while ($row = $result_accepted->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . ordinal_suffix($rank) . '</td>'; // Display ranking as ordinal (1st, 2nd, 3rd, etc.)
                            echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['package_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['package_type']) . '</td>';
                            echo '<td>' . date('Y-m-d', strtotime($row['start_date'])) . '</td>';
                            echo '<td>' . date('Y-m-d', strtotime($row['end_date'])) . '</td>';
                            echo '<td><a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-id="' . $row['id'] . '">View</a></td>';
                            echo '<td>';

                            echo '</td>';
                            echo '</tr>';
                            $rank++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="denied" role="tabpanel" aria-labelledby="denied-tab">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Username</th>
                            <th>Package</th>
                            <th>Package Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Proof of Payment</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rank = 1;
                        while ($row = $result_denied->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . ordinal_suffix($rank) . '</td>'; // Display ranking as ordinal (1st, 2nd, 3rd, etc.)
                            echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['package_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['package_type']) . '</td>';
                            echo '<td>' . date('Y-m-d', strtotime($row['start_date'])) . '</td>';
                            echo '<td>' . date('Y-m-d', strtotime($row['end_date'])) . '</td>';
                            echo '<td><a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-id="' . $row['id'] . '">View</a></td>';
                            echo '<td>';

                            echo '</td>';
                            echo '</tr>';
                            $rank++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>



        </div>







        <!-- Modal -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Proof of Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img src="" id="proofImage" class="img-fluid" alt="Proof of Payment">
                    </div>
                </div>
            </div>
        </div>

        <script>
            var imageModal = document.getElementById('imageModal');
            imageModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var modalImage = document.getElementById('proofImage');
                modalImage.src = "?id=" + id;
            });
        </script>
    </div>
   
</body>

</html>