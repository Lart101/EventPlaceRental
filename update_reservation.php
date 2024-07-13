<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reservation_id'], $_POST['action'])) {
    $reservationId = intval($_POST['reservation_id']);
    $action = $_POST['action'];

    if ($action === 'accept') {
        // Update the selected reservation to 'Accepted'
        $updateSql = "UPDATE package_reservations SET status = 'Accepted' WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param('i', $reservationId);
        $stmt->execute();
        $stmt->close();

        // Fetch the reservation details
        $fetchSql = "SELECT start_date FROM package_reservations WHERE id = ?";
        $stmt = $conn->prepare($fetchSql);
        $stmt->bind_param('i', $reservationId);
        $stmt->execute();
        $stmt->bind_result($startDate);
        $stmt->fetch();
        $stmt->close();

        // Automatically deny other reservations with the same start date
        $denySql = "UPDATE package_reservations SET status = 'Denied' WHERE start_date = ? AND status = 'Pending' AND id != ?";
        $stmt = $conn->prepare($denySql);
        $stmt->bind_param('si', $startDate, $reservationId);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = 'Reservation accepted successfully. Conflicting reservations with the same start date have been automatically denied.';
    } elseif ($action === 'deny') {
        // Update the selected reservation to 'Denied'
        $updateSql = "UPDATE package_reservations SET status = 'Denied' WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param('i', $reservationId);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = 'Reservation denied successfully.';
    } else {
        $_SESSION['message'] = 'Invalid action.';
    }

    header("Location: admin_reservations.php");
    exit;
} else {
    $_SESSION['message'] = 'Invalid request.';
    header("Location: admin_reservations.php");
    exit;
}
?>
