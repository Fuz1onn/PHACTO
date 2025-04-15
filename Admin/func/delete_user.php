<?php
include '../../conn/connection.php';

// Check if user_id is set and is a valid integer
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    // Move user to the archive table
    $moveToArchiveSql = "INSERT INTO `archived_users` SELECT * FROM `user profile` WHERE `user_id` = $user_id";
    if ($conn->query($moveToArchiveSql) === TRUE) {
        // Delete user from the original table
        $deleteSql = "DELETE FROM `user profile` WHERE `user_id` = $user_id";
        if ($conn->query($deleteSql) === TRUE) {
            // Redirect back to the user management page after successful deletion
            header("Location: ../adminUserManagement.php");
            exit();
        } else {
            // Handle the case where deletion fails
            echo "Error deleting user: " . $conn->error;
        }
    } else {
        // Handle the case where moving to archive fails
        echo "Error moving user to archive: " . $conn->error;
    }
} else {
    // Handle the case where user_id is not set or is not a valid integer
    echo "Invalid user ID.";
}

mysqli_close($conn);
?>