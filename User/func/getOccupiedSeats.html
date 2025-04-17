<?php
session_start();
include '../../conn/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Assuming you have variables $selectedDate, $startTime, and $endTime
    $selectedDate = mysqli_real_escape_string($conn, $_GET['selectedDate']);
    $startTime = mysqli_real_escape_string($conn, $_GET['startTime']);
    $endTime = mysqli_real_escape_string($conn, $_GET['endTime']);

    error_log('Selected Date: ' . $selectedDate);
    error_log('Start Time: ' . $startTime);
    error_log('End Time: ' . $endTime);

    // Adjust the SQL query to consider both date and time range
    $query = "SELECT reservation_date, start_time, end_time, seat_number FROM reservations WHERE reservation_date = '$selectedDate' AND ((start_time <= '$startTime' AND end_time >= '$startTime') OR (start_time <= '$endTime' AND end_time >= '$endTime'))";

    $result = mysqli_query($conn, $query);

    if ($result) {
        $occupiedSeats = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $occupiedSeats[] = array(
                'date' => $row['reservation_date'],
                'startTime' => $row['start_time'],
                'endTime' => $row['end_time'],
                'seatNumber' => $row['seat_number'],
            );
        }

        echo json_encode(array('occupiedSeats' => $occupiedSeats));
    } else {
        echo json_encode(array('error' => 'Error fetching occupied seats'));
    }
} else {
    echo json_encode(array('error' => 'Invalid request method'));
}
?>