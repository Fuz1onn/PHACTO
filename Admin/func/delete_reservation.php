<?php
include '../../conn/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $reservationId = intval($_GET['id']);

    // Retrieve reservation details
    $selectSql = "SELECT * FROM reservations WHERE id = ?";
    $selectStmt = $conn->prepare($selectSql);
    $selectStmt->bind_param("i", $reservationId);
    $selectStmt->execute();
    $reservationDetails = $selectStmt->get_result()->fetch_assoc();
    $selectStmt->close();

    if (!$reservationDetails) {
        // Reservation not found
        header("Location: ../adminReservation.php");
        exit();
    }

    // Check if user exists in the user profile table
    $checkUserSql = "SELECT * FROM `user profile` WHERE user_id = ?";
    $checkUserStmt = $conn->prepare($checkUserSql);
    $checkUserStmt->bind_param("i", $reservationDetails['user_id']);
    $checkUserStmt->execute();
    $userExists = $checkUserStmt->fetch();
    $checkUserStmt->close();

    if (!$userExists) {
        // User does not exist, handle the situation (redirect, show an error, etc.)
        // For example:
        echo "Error: User does not exist.";
        exit();
    }

    // Insert into archived_reservations with original end_time
    $insertSql = "INSERT INTO archived_reservations (id, user_id, section, reservation_date, start_time, end_time, seat_number, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);

    // Set end_time to the original value
    $originalEndTime = $reservationDetails['end_time'];
    $reservationDetails['end_time'] = $originalEndTime;

    $insertStmt->bind_param("iisssiss",
        $reservationDetails['id'],
        $reservationDetails['user_id'],
        $reservationDetails['section'],
        $reservationDetails['reservation_date'],
        $reservationDetails['start_time'],
        $reservationDetails['end_time'],
        $reservationDetails['seat_number'],
        $reservationDetails['status']
    );

    if (!$insertStmt->execute()) {
        // Output debug information
        echo "Error inserting into archived_reservations: " . $insertStmt->error;
        echo "Values used during insertion:";
        print_r($reservationDetails);
    }

    $insertStmt->close();

    // Delete from reservations
    $deleteSql = "DELETE FROM reservations WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $reservationId);

    if ($deleteStmt->execute()) {
        // Deletion successful
        header("Location: ../adminReservation.php");
        exit();
    } else {
        // Handle deletion failure
        echo "Error deleting reservation: " . $deleteStmt->error;
    }

    $deleteStmt->close();
}

mysqli_close($conn);
?>
