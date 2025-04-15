<?php
include '../../conn/connection.php';

// Check if the book_id parameter is set in the URL
if (isset($_GET['book_id'])) {
    $bookIdToRestore = $_GET['book_id'];

    // Move the book back to the original table
    $moveToOriginalSql = "INSERT INTO `books` SELECT * FROM `archived_books` WHERE `book_id` = $bookIdToRestore";

    if ($conn->query($moveToOriginalSql) === TRUE) {
        // Delete the book from the archived_books table
        $deleteFromArchivedSql = "DELETE FROM `archived_books` WHERE `book_id` = $bookIdToRestore";

        if ($conn->query($deleteFromArchivedSql) === TRUE) {
            // Redirect back to the page displaying archived books
            header("Location: ../adminBooksPage.php");
            exit();
        } else {
            // Handle the case where deletion from archived_books fails
            echo "Error deleting book from archived_books: " . $conn->error;
        }
    } else {
        // Handle the case where moving to the original table fails
        echo "Error moving book back to the original table: " . $conn->error;
    }
} else {
    // Redirect to an error page or handle the case where book_id is not set
    header("Location: error.php");
    exit();
}

// Close the database connection
$conn->close();
?>
