<?php
include '../../conn/connection.php';

// Check if the user_id parameter is set in the URL
if (isset($_GET['user_id'])) {
    $userIdToRestore = $_GET['user_id'];

    // Move the user back to the original table
    $moveToOriginalSql = "INSERT INTO `user profile` SELECT * FROM `archived_users` WHERE `user_id` = $userIdToRestore";
    
    if ($conn->query($moveToOriginalSql) === TRUE) {
        // Delete the user from the archived_users table
        $deleteFromArchivedSql = "DELETE FROM `archived_users` WHERE `user_id` = $userIdToRestore";
        if ($conn->query($deleteFromArchivedSql) === TRUE) {
            // Redirect back to the page displaying archived users
            header("Location: ../adminUserManagement.php");
            exit();
        } else {
            // Handle the case where deletion from archived_users fails
            echo "Error deleting user from archived_users: " . $conn->error;
        }
    } else {
        // Handle the case where moving to the original table fails
        echo "Error moving user back to the original table: " . $conn->error;
    }
} else {
    // Redirect to an error page or handle the case where user_id is not set
    header("Location: error.php");
    exit();
}

// Close the database connection
$conn->close();
?>
