<?php
function establishDatabaseConnection()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "phacto"; // Replace with your actual database name

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Handle database connection error
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Database connection error']);
        exit();
    }
}

// Establish database connection (assuming you have a function like establishDatabaseConnection())
$pdo = establishDatabaseConnection();

// Retrieve book ID from the query parameter
$bookId = $_GET['book_id'];

try {
    // Prepare a SQL query to fetch the file path based on the provided book ID
    $query = "SELECT book_file FROM books WHERE book_id = :bookId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':bookId', $bookId, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the file path from the database
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the result is not empty
    if ($result) {
        $filePath = $result['book_file'];

        // Return file path as JSON response
        header('Content-Type: application/json');
        echo json_encode(['filePath' => $filePath]);
    } else {
        // If the book ID is not found, return an error response
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Book not found']);
    }
} catch (PDOException $e) {
    // Handle database errors if any
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error']);
}
?>
