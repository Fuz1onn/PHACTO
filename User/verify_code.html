<?php
// Include the database connection file
include '../conn/connection.php';

// Assuming the reservation code is stored in the reservations table
$enteredCode = $_POST['code'];
$action = $_POST['action'];

// Use prepared statements to prevent SQL injection
$sql = "SELECT * FROM reservations WHERE reservation_code = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $enteredCode);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Check if the code is valid
if ($result->num_rows > 0) {
    echo 'valid';
} else {
    echo 'invalid';
}
?>
