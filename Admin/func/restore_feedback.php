<?php
include '../../conn/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $feedbackId = intval($_GET['id']);

    // Retrieve feedback details from archived_feedback table
    $selectSql = "SELECT * FROM archive_feedback WHERE feed_id = ?";
    $selectStmt = $conn->prepare($selectSql);
    $selectStmt->bind_param("i", $feedbackId);
    $selectStmt->execute();
    $archivedFeedbackDetails = $selectStmt->get_result()->fetch_assoc();
    $selectStmt->close();

    if (!$archivedFeedbackDetails) {
        // Archived feedback not found
        header("Location: ../adminFeedback.php");
        exit();
    }

    // Insert into feedback table
    $insertSql = "INSERT INTO feedback (feed_id, feed_name, feed_email, feed_comment, feed_rate, feed_date)
                  VALUES (?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("isssis",
        $archivedFeedbackDetails['feed_id'],
        $archivedFeedbackDetails['feed_name'],
        $archivedFeedbackDetails['feed_email'],
        $archivedFeedbackDetails['feed_comment'],
        $archivedFeedbackDetails['feed_rate'],
        $archivedFeedbackDetails['feed_date']
    );
    
    if ($insertStmt->execute()) {
        // Delete from archived_feedback table
        $deleteSql = "DELETE FROM archive_feedback WHERE feed_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $feedbackId);

        if ($deleteStmt->execute()) {
            // Restoration successful
            header("Location: ../adminFeedback.php");
            exit();
        } else {
            // Handle deletion failure
            echo "Error deleting from archived_feedback: " . $deleteStmt->error;
        }

        $deleteStmt->close();
    } else {
        // Handle insertion failure
        echo "Error inserting into feedback: " . $insertStmt->error;
    }

    $insertStmt->close();
}

mysqli_close($conn);
?>
