<?php
// Include your database connection file here
include '../../conn/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    // Get the feedback ID from the URL
    $feedbackId = $_GET['id'];

    // Retrieve the feedback data before deletion
    $sqlSelect = "SELECT * FROM `feedback` WHERE `feed_id` = $feedbackId";
    $resultSelect = $conn->query($sqlSelect);

    if ($resultSelect->num_rows > 0) {
        // Fetch the feedback data
        $feedbackData = $resultSelect->fetch_assoc();

        // Archive the feedback data
        $sqlArchive = "INSERT INTO `archive_feedback` 
                       (`feed_id`, `feed_name`, `feed_email`, `feed_comment`, `feed_rate`, `feed_date`) 
                       VALUES 
                       ('{$feedbackData['feed_id']}', '{$feedbackData['feed_name']}', '{$feedbackData['feed_email']}', 
                        '{$feedbackData['feed_comment']}', '{$feedbackData['feed_rate']}', '{$feedbackData['feed_date']}')";
        $resultArchive = $conn->query($sqlArchive);

        // Delete the feedback from the original table
        $sqlDelete = "DELETE FROM `feedback` WHERE `feed_id` = $feedbackId";
        $resultDelete = $conn->query($sqlDelete);

        // Redirect back to the feedback page after deletion
        header("Location: ../adminFeedback.php");
        exit();
    }
}
?>
