<?php
include '../../conn/connection.php';

if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];

    // Example query using prepared statement
    $query = "SELECT * FROM `user_profile` WHERE user_id = ?";
    
    // Assume $conn is your database connection (replace with your actual connection)
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId); // Assuming user_id is an integer, adjust if it's another type
    $stmt->execute();
    
    // Fetch the user details
    $result = $stmt->get_result();
    
    // Check if a user was found
    if ($result->num_rows > 0) {
        $userDetails = $result->fetch_assoc();
        // Return the user details as JSON
        echo json_encode($userDetails);
    } else {
        // No user found with the provided ID
        echo json_encode(['error' => 'User not found']);
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
}
?>
