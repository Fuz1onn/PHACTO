<?php
include '../../conn/connection.php';

// Get the book ID from the query string
$bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Select book details from the main table
$sqlSelectBook = "SELECT * FROM books WHERE book_id = ?";
$stmtSelectBook = $conn->prepare($sqlSelectBook);
$stmtSelectBook->bind_param('i', $bookId);

if ($stmtSelectBook->execute()) {
    $result = $stmtSelectBook->get_result();

    // Check if the book exists
    if ($result->num_rows > 0) {
        $bookData = $result->fetch_assoc();

        // Insert the book details into the archive table
        $sqlArchiveBook = "INSERT INTO archived_books (book_id, book_title, book_image, book_file, book_author, book_description) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtArchiveBook = $conn->prepare($sqlArchiveBook);
        $stmtArchiveBook->bind_param('isssss', $bookData['book_id'], $bookData['book_title'], $bookData['book_image'], $bookData['book_file'], $bookData['book_author'], $bookData['book_description']);

        if ($stmtArchiveBook->execute()) {
            // Book details successfully inserted into the archive table

            // Now, delete the book from the main table
            $sqlDeleteBook = "DELETE FROM books WHERE book_id = ?";
            $stmtDeleteBook = $conn->prepare($sqlDeleteBook);
            $stmtDeleteBook->bind_param('i', $bookId);

            if ($stmtDeleteBook->execute()) {
                // Book deleted successfully from the main table

                // You can also perform additional actions here if needed

                header('Location: ../adminBooksPage.php'); // Redirect back to the main books page
            } else {
                echo "Error deleting book: " . $stmtDeleteBook->error;
            }

            $stmtDeleteBook->close();
        } else {
            echo "Error archiving book: " . $stmtArchiveBook->error;
        }

        $stmtArchiveBook->close();
    } else {
        echo "Book not found.";
    }
} else {
    echo "Error selecting book: " . $stmtSelectBook->error;
}

// Close the prepared statements and the database connection
$stmtSelectBook->close();
$conn->close();
?>
