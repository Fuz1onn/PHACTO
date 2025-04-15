<?php
include '../../conn/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $reservationId = intval($_GET['id']);

    // Retrieve reservation details
    $selectSql = "SELECT * FROM archived_reservations WHERE id = ?";
    $selectStmt = $conn->prepare($selectSql);
    $selectStmt->bind_param("i", $reservationId);
    $selectStmt->execute();
    $archivedReservationDetails = $selectStmt->get_result()->fetch_assoc();
    $selectStmt->close();

    if (!$archivedReservationDetails) {
        // Archived reservation not found
        header("Location: ../adminReservation.php");
        exit();
    }

    // Insert into reservations
    $insertSql = "INSERT INTO reservations (id, user_id, section, reservation_date, start_time, end_time, seat_number, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("iisssiss",
        $archivedReservationDetails['id'],
        $archivedReservationDetails['user_id'],
        $archivedReservationDetails['section'],
        $archivedReservationDetails['reservation_date'],
        $archivedReservationDetails['start_time'],
        $archivedReservationDetails['end_time'],
        $archivedReservationDetails['seat_number'],
        $archivedReservationDetails['status']
    );
    $insertStmt->execute();
    $insertStmt->close();

    // Delete from archived_reservations
    $deleteSql = "DELETE FROM archived_reservations WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $reservationId);

    if ($deleteStmt->execute()) {
        // Restoration successful
        header("Location: ../adminReservation.php");
        exit();
    } else {
        // Handle restoration failure
        echo "Error restoring reservation: " . $deleteStmt->error;
    }

    $deleteStmt->close();
}

mysqli_close($conn);
?>
