<?php
// Include the database connection file
include '../conn/connection.php';

// Assuming the reservation status is stored in the reservations table
$action = $_POST['action'];
$newStatus = ($action === 'checkIn') ? 'Checked in' : 'Checked out';

// Assuming the reservation code is stored in the reservations table
$enteredCode = $_POST['code'];

// Use prepared statements to prevent SQL injection
$sqlSelect = "SELECT status FROM reservations WHERE reservation_code = ?";
$stmtSelect = $conn->prepare($sqlSelect);

// Check for errors in preparing the SELECT statement
if (!$stmtSelect) {
    error_log('Database Error (SELECT): ' . $conn->error);
    echo 'error';
    exit;
}

// Bind parameter for SELECT statement
$stmtSelect->bind_param('s', $enteredCode);

// Execute the SELECT statement
$stmtSelect->execute();

// Bind result variable
$stmtSelect->bind_result($currentStatus);

// Fetch the result
$stmtSelect->fetch();

// Close the SELECT statement
$stmtSelect->close();

// Check the current status and update accordingly
if ($currentStatus === 'pending') {
    echo 'error';
} else {
    // Use prepared statements to prevent SQL injection
    $sqlUpdate = "UPDATE reservations SET status = ? WHERE reservation_code = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);

    // Check for errors in preparing the UPDATE statement
    if (!$stmtUpdate) {
        error_log('Database Error (UPDATE): ' . $conn->error);
        echo 'error';
        exit;
    }

    // Bind parameters for UPDATE statement
    $stmtUpdate->bind_param('ss', $newStatus, $enteredCode);

    // Execute the UPDATE statement
    if ($stmtUpdate->execute()) {
        echo 'success';
    } else {
        error_log('Database Error (UPDATE): ' . $stmtUpdate->error);
        echo 'error';
    }

    // Close the UPDATE statement
    $stmtUpdate->close();
}

// Close the database connection
$conn->close();
?>
