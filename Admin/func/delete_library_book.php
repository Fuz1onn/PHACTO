<?php
include '../../conn/connection.php';

// Get the library book ID from the query string
$bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Delete the library book from the main table
$sqlDeleteLibraryBook = "DELETE FROM library_books WHERE book_id = $bookId";
if ($conn->query($sqlDeleteLibraryBook) === TRUE) {
    // Library book deleted successfully, now insert into the archive table
    $sqlArchiveLibraryBook = "INSERT INTO archived_library_books SELECT * FROM library_books WHERE book_id = $bookId";
    $conn->query($sqlArchiveLibraryBook);

    // You can also perform additional actions here if needed

    header('Location: ../adminBooksPage.php'); // Redirect back to the main books page
} else {
    echo "Error deleting library book: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
