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
        $fetchSql = "SELECT start_date, package_id FROM package_reservations WHERE id = ?";
        $stmt = $conn->prepare($fetchSql);
        $stmt->bind_param('i', $reservationId);
        $stmt->execute();
        $stmt->bind_result($startDate, $packageId);
        $stmt->fetch();
        $stmt->close();

        // Determine package type for the accepted reservation
        $packageTypeSql = "SELECT package_type FROM swimming_packages WHERE id = ?";
        $stmt = $conn->prepare($packageTypeSql);
        $stmt->bind_param('i', $packageId);
        $stmt->execute();
        $stmt->bind_result($packageType);
        $stmt->fetch();
        $stmt->close();

        if ($packageType === 'Combo') {
            // Case 1: If the accepted reservation is Combo, deny all other pending reservations with the same start date
            $denySql = "UPDATE package_reservations SET status = 'Denied' WHERE start_date = ? AND status = 'Pending' AND id != ?";
            $stmt = $conn->prepare($denySql);
            $stmt->bind_param('si', $startDate, $reservationId);
            $stmt->execute();
            $stmt->close();
        } else {
            // Case 2: If the accepted reservation is Day or Overnight, deny all Combo reservations with the same start date
            if ($packageType === 'Day' || $packageType === 'Overnight') {
                $denyComboSql = "UPDATE package_reservations pr 
                                 INNER JOIN swimming_packages sp ON pr.package_id = sp.id 
                                 SET pr.status = 'Denied' 
                                 WHERE pr.start_date = ? AND sp.package_type = 'Combo' AND pr.status = 'Pending'";
                $stmt = $conn->prepare($denyComboSql);
                $stmt->bind_param('s', $startDate);
                $stmt->execute();
                $stmt->close();
            }

            // Case 3: Deny all reservations with the same start date and same package type as the accepted reservation
            $denySameTypeSql = "UPDATE package_reservations pr 
                                INNER JOIN swimming_packages sp ON pr.package_id = sp.id 
                                SET pr.status = 'Denied' 
                                WHERE pr.start_date = ? AND sp.package_type = ? AND pr.status = 'Pending' AND pr.id != ?";
            $stmt = $conn->prepare($denySameTypeSql);
            $stmt->bind_param('ssi', $startDate, $packageType, $reservationId);
            $stmt->execute();
            $stmt->close();
        }

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
} else {
    $_SESSION['message'] = 'Invalid request.';
}

header("Location: admin_reservations.php");
exit;
?>
