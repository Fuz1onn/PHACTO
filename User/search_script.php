<?php
// Include database connection file
include '../conn/connection.php';

// Get the search term and filter from the AJAX request
$searchTerm = '%' . $_GET['search'] . '%';
$filter = $_GET['filter']; // Assuming 'all' or 'digitized'

// Initialize variables to store results
$regularBooks = [];
$libraryBooks = [];

// Search for regular books if the filter is 'digitized'
if ($filter === 'digitized') {
    $queryRegularBooks = "SELECT book_id, book_image, book_file, book_title, book_author, book_description 
                          FROM books 
                          WHERE book_title LIKE ? OR book_author LIKE ?
                          LIMIT 10";

    $stmtRegularBooks = $conn->prepare($queryRegularBooks);
    $stmtRegularBooks->bind_param('ss', $searchTerm, $searchTerm);
    $stmtRegularBooks->execute();
    $resultRegularBooks = $stmtRegularBooks->get_result();
    $stmtRegularBooks->close();

    // Fetch the search results for regular books as an associative array
    $regularBooks = $resultRegularBooks->fetch_all(MYSQLI_ASSOC);
}

// Search for library books if the filter is 'all' or not specified
if ($filter === 'all' || empty($filter)) {
    $queryLibraryBooks = "SELECT book_id, publisher, title, author, description, section, year_published
                          FROM library_books 
                          WHERE title LIKE ? OR author LIKE ? OR year_published LIKE ?
                          LIMIT 10";

    $stmtLibraryBooks = $conn->prepare($queryLibraryBooks);
    $stmtLibraryBooks->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
    $stmtLibraryBooks->execute();
    $resultLibraryBooks = $stmtLibraryBooks->get_result();
    $stmtLibraryBooks->close();

    // Fetch the search results for library books as an associative array
    $libraryBooks = $resultLibraryBooks->fetch_all(MYSQLI_ASSOC);
}

// Output the search results for regular books
if (($filter === 'digitized') && !empty($regularBooks)) {
    foreach ($regularBooks as $book) {
        echo '<div class="book-cover" data-book-id="' . $book['book_id'] . '"
              data-title="' . htmlspecialchars($book['book_title']) . '"
              data-author="' . htmlspecialchars($book['book_author']) . '"
              data-description="' . htmlspecialchars($book['book_description']) . '"
              data-file="' . htmlspecialchars($book['book_file']) . '">
              <img src="../books/uploadedCovers/' . htmlspecialchars($book['book_image']) . '" alt="Book Cover">
              <button class="view-button">View</button>
              </div>';
    }
} elseif (($filter === 'digitized') && empty($regularBooks)) {
    echo '<p class="no-results">No results found.</p>';
}

// Output the search results for library books
if (($filter === 'all' || empty($filter)) && !empty($libraryBooks)) {
    echo '<div class="library-books-container">';
    foreach ($libraryBooks as $book) {
        echo '<div class="library-book" data-book-id="' . $book['book_id'] . '"
              data-title="' . htmlspecialchars($book['title']) . '"
              data-year="' . htmlspecialchars($book['year_published']) . '"
              data-author="' . htmlspecialchars($book['author']) . '"
              data-description="' . htmlspecialchars($book['description']) . '"
              data-publisher="' . htmlspecialchars($book['publisher']) . '"
              data-section="' . htmlspecialchars($book['section']) . '">
              <h3>' . htmlspecialchars($book['title']) . ' (' . htmlspecialchars($book['year_published']) . ')</h3>
              </div>';
    }
    echo '</div>';
} elseif (($filter === 'all' || empty($filter)) && empty($libraryBooks)) {
    echo '<p class="no-results">No results found.</p>';
}
?>
