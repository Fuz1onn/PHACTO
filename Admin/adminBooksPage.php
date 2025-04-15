<?php
session_start();
include '../conn/connection.php';

// Function to get books with pagination
function getBooks($conn, $page, $perPage)
{
    $start = ($page - 1) * $perPage;
    $sql = "SELECT * FROM books LIMIT $start, $perPage";
    $result = $conn->query($sql);

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }

    return $books;
}

function getLibraryBooks($conn, $page, $perPage)
{
    $start = ($page - 1) * $perPage;
    $sql = "SELECT * FROM library_books LIMIT $start, $perPage";
    $result = $conn->query($sql);

    $libraryBooks = [];
    while ($row = $result->fetch_assoc()) {
        $libraryBooks[] = $row;
    }

    return $libraryBooks;
}

// Function to get the total number of books for pagination
function getTotalBooks($conn)
{
    $sql = "SELECT COUNT(*) as total FROM books";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row['total'];
}

// Function to get the total number of library books for pagination
function getTotalLibraryBooks($conn)
{
    $sql = "SELECT COUNT(*) as total FROM library_books";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row['total'];
}

function handleBookUpload($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $uploadCoverDirectory = "../books/uploadedCovers/";
        $uploadFileDirectory = "../books/uploadedBooks/";

        // Validate and sanitize user input
        $bookTitle = filter_input(INPUT_POST, 'book_title', FILTER_SANITIZE_STRING);
        $bookAuthor = filter_input(INPUT_POST, 'book_author', FILTER_SANITIZE_STRING);
        $bookDescription = filter_input(INPUT_POST, 'book_description', FILTER_SANITIZE_STRING);

        // Validate and sanitize file names for book cover
        $bookImage = $_FILES["book_image"]["name"];
        $bookImageTmp = $_FILES["book_image"]["tmp_name"];

        // Validate and sanitize file names for book file
        $bookFile = $_FILES["book_file"]["name"];
        $bookFileTmp = $_FILES["book_file"]["tmp_name"];

        // Check if both files are uploaded successfully
        if (is_uploaded_file($bookImageTmp) && is_uploaded_file($bookFileTmp)) {
            // Move uploaded files to the desired directories
            $coverFilePath = $uploadCoverDirectory . basename($bookImage);
            $fileFilePath = $uploadFileDirectory . basename($bookFile);
            if (move_uploaded_file($bookImageTmp, $coverFilePath) && move_uploaded_file($bookFileTmp, $fileFilePath)) {
                // Insert book details into the database using MySQLi
                $sql = "INSERT INTO books (book_title, book_image, book_file, book_author, book_description) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sssss', $bookTitle, $bookImage, $bookFile, $bookAuthor, $bookDescription);
                $stmt->execute();

                // After handling the form submission, redirect the user to prevent form resubmission
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "Error uploading files.";
            }
        } else {
            echo "Invalid file type. Both an image for the cover and a book file are required.";
        }
    }
}

function handleCatalogUpload($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        // Validate and sanitize user input
        $bookTitle = filter_input(INPUT_POST, 'book_title', FILTER_SANITIZE_STRING);
        $yearPublished = filter_input(INPUT_POST, 'year_published', FILTER_SANITIZE_NUMBER_INT);
        $bookAuthor = filter_input(INPUT_POST, 'book_author', FILTER_SANITIZE_STRING);
        $bookDescription = filter_input(INPUT_POST, 'book_description', FILTER_SANITIZE_STRING);
        $publisher = filter_input(INPUT_POST, 'publisher', FILTER_SANITIZE_STRING);
        $section = filter_input(INPUT_POST, 'section', FILTER_SANITIZE_STRING);

        // Insert book details into the library_books table
        $sql = "INSERT INTO library_books (title, year_published, author, description, publisher, section) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('isssss', $bookTitle, $yearPublished, $bookAuthor, $bookDescription, $publisher, $section);

        if ($stmt->execute()) {
            // Book details successfully added to the library_books table

            // You can perform additional actions here if needed
            echo "Book successfully added to the library_books table.";

        } else {
            echo "Error adding book to the library_books table: " . $stmt->error;
        }

        $stmt->close();
    }
}

function updateAccountSettings($conn, $newUsername, $newPassword, $verifyPassword)
{
    // Verify the current password before proceeding with updates
    $currentUsername = getCurrentUsername($conn);
    $sql = "SELECT l_password FROM `librarian profile` WHERE l_username = '$currentUsername'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentPasswordHash = $row['l_password'];

        // Verify the entered password against the stored hash
        if (password_verify($verifyPassword, $currentPasswordHash)) {
            // Password verification successful, proceed with updates

            error_log("Password verification successful. Proceeding with updates.");

            // Sample code to update username in the database
            $updateUsernameQuery = "UPDATE `librarian profile` SET l_username = '$newUsername' WHERE librarian_id = 1";
            $conn->query($updateUsernameQuery);

            // Sample code to update password in the database
            if (!empty($newPassword)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updatePasswordQuery = "UPDATE `librarian profile` SET l_password = '$hashedPassword' WHERE librarian_id = 1";
                $conn->query($updatePasswordQuery);

                error_log("Password updated successfully.");
                $_SESSION['passwordChanged'] = true; // Set a session variable
            } else {
                error_log("No new password provided.");
            }

        } else {
            // Password verification failed, handle accordingly (e.g., display an error message)
            error_log("Password verification failed.");
            $_SESSION['passwordChanged'] = false; // Set a session variable to false
        }
    }
}

// Function to update website settings
function updateWebsiteSettings($conn, $newWebsiteName, $newWebsiteLogo)
{
    // File upload handling
    $targetDirectory = "../images/uploadedLogo/"; // Change this to the directory where you want to store the uploaded logo

    // Delete all existing files in the directory
    $files = glob($targetDirectory . '*'); // Get all file names in the directory
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file); // Delete each file
        }
    }

    // Move the file to the target directory
    $targetFile = $targetDirectory . basename($_FILES["newWebsiteLogo"]["name"]);
    if (move_uploaded_file($_FILES["newWebsiteLogo"]["tmp_name"], $targetFile)) {
        // File upload successful

        // Debugging statements for file upload
        error_log("Move Uploaded File - Target File: " . $targetFile);

        if (file_exists($targetFile)) {
            error_log("File exists at target location");
        } else {
            error_log("File does not exist at target location");
        }

        // Check if a record already exists in the table
        $checkQuery = "SELECT * FROM website_settings WHERE setting_id = 1";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult && $checkResult->num_rows > 0) {
            // If a record exists, perform an update
            $updateQuery = "UPDATE website_settings SET website_name = ?, website_logo = ? WHERE setting_id = 1";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ss", $newWebsiteName, $targetFile);

            if ($stmt->execute()) {
                // Update successful
                error_log("Update successful.");
                return true;
            } else {
                // Update failed
                error_log("Update failed: " . $stmt->error);
                return false;
            }
        } else {
            // If no record exists, perform an insert
            $insertQuery = "INSERT INTO website_settings (setting_id, website_name, website_logo) VALUES (1, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ss", $newWebsiteName, $targetFile);

            if ($stmt->execute()) {
                // Insert successful
                error_log("Insert successful.");
                return true;
            } else {
                // Insert failed
                error_log("Insert failed: " . $stmt->error);
                return false;
            }
        }
    } else {
        // File upload failed
        error_log("File upload failed.");
        return false;
    }
}


function getWebsiteSettings($conn)
{
    $sql = "SELECT * FROM website_settings WHERE setting_id = 1";
    $result = $conn->query($sql);

    if (!$result) {
        // Handle the error, log it, or display an error message
        die("Error fetching website settings: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}

function getCurrentUsername($conn)
{
    $sql = "SELECT l_username FROM `librarian profile` WHERE librarian_id = 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['l_username'];
    }

    return null; // Return null if username is not found
}

// Function to handle SweetAlert display based on the result
function handleResult($result, $settingsType)
{
    if ($result) {
        $_SESSION['swal_message'] = [
            'type' => 'success',
            'title' => 'Settings Updated',
            'text' => $settingsType . ' updated successfully!',
        ];
    } else {
        $_SESSION['swal_message'] = [
            'type' => 'error',
            'title' => 'Update Failed',
            'text' => 'Failed to update ' . $settingsType . '. Please try again.',
        ];
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accountSettingsSubmit'])) {
        // Handle account settings form submission
        $newUsername = $_POST['newUsername'];
        $newPassword = $_POST['newPassword'];
        $verifyPassword = $_POST['verifyPassword'];

        // Update account settings and get the result
        updateAccountSettings($conn, $newUsername, $newPassword, $verifyPassword);
    } elseif (isset($_POST['generalSettingsSubmit'])) {
        // Handle general settings form submission
        $newWebsiteName = $_POST['newWebsiteName'];
        // Process and store the uploaded logo file (you may need additional logic here)
        $newWebsiteLogo = $_FILES['newWebsiteLogo']['name'];

        // Update or insert website settings and get the result
        $updateResult = updateWebsiteSettings($conn, $newWebsiteName, $newWebsiteLogo);

        // Handle the result
        handleResult($updateResult, "Website settings");
    }

    // Redirect to refresh the data
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$currentUsername = getCurrentUsername($conn);
$currentWebsiteSettings = getWebsiteSettings($conn);

// If data is fetched successfully, use it; otherwise, provide default values
if ($currentWebsiteSettings) {
    $websiteName = $currentWebsiteSettings['website_name'];
    $websiteLogo = $currentWebsiteSettings['website_logo'];
} else {
    // Default values (adjust as needed)
    $websiteName = "Default Website Name";
    $websiteLogo = "default_logo.png";
}

$perPage = 6;
$totalBooks = getTotalBooks($conn);
$totalLibraryBooks = getTotalLibraryBooks($conn);
$totalPages = ceil($totalBooks / $perPage);
$totalLibraryPages = ceil($totalLibraryBooks / $perPage);

// Get the current page from the query string, default to 1
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$libraryPage = isset($_GET['library_page']) ? max(1, intval($_GET['library_page'])) : 1;

$books = getBooks($conn, $page, $perPage);
$libraryBooks = getLibraryBooks($conn, $libraryPage, $perPage);

handleBookUpload($conn)
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/adminUserManagement.css">
    <link rel="stylesheet" href="../Styles/bootstrap.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Panlalawigang Aklatan ng Bulacan</title>
</head>

<body>

    <section>
        <div class="sidebar" id="sidebar">
            <div class="logo-container">
                <img src="<?php echo $currentWebsiteSettings['website_logo']; ?>" alt="Logo">
                <a href="#" class="logo-name">
                    <?php echo $currentWebsiteSettings['website_name']; ?>
                </a>
            </div>
            <div class="sidebar-content">
                <ul class="lists">
                    <li class="list">
                        <a href="../Admin/adminDashboard.php" class="nav-link">
                            <i class='bx bxs-dashboard icon'></i>
                            <span class="link">Dashboard</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="#books" class="nav-link" id="nav-link-active">
                            <i class='bx bxs-book-content icon'></i>
                            <span class="link">Catalog</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Admin/adminUserManagement.php" class="nav-link">
                            <i class='bx bxs-user icon'></i>
                            <span class="link">User Management</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Admin/adminReservation.php" class="nav-link">
                            <i class='bx bx-calendar-check icon'></i>
                            <span class="link">Reservations</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Admin/adminFeedback.php" class="nav-link">
                            <i class='bx bx-message-rounded-dots icon'></i>
                            <span class="link">Feedback</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Admin/adminContactUs.php" class="nav-link">
                            <i class='bx bx-message-alt-detail icon'></i>
                            <span class="link">Contact Us</span>
                        </a>
                    </li>
                </ul>
                <div class="bottom-content">
                    <li class="list">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#librarianSettingsModal" class="nav-link">
                            <i class='bx bx-cog icon'></i>
                            <span class="link">Settings</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Visitor/visitorLandingPage.php" class="nav-link">
                            <i class='bx bx-log-out icon'></i>
                            <span class="link">Logout</span>
                        </a>
                    </li>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container2">
            <h1>Digitized Books</h1>
            <div class="button-group">
                <div class="d-flex justify-content-around">
                    <div class="input-group me-5">
                        <input type="text" class="form-control form-control-sm px-3" placeholder="Search..."
                            id="searchInputRegular" style="width: 500px;">
                        <button class="button add me-5" type="button" id="searchButtonRegular">Search</button>
                    </div>
                    <button class="add-book-button">Add Book</button>
                    <a href="#" class="button archive" data-toggle="modal"
                        data-target="#archiveRegularModal">Archive</a>
                </div>
            </div>
            <table class="table" id="regularBooksTable">
                <thead>
                    <tr>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Image</th>
                        <th>File</th>
                        <th>Author</th>
                        <th>Description</th>
                        <th>Publication Date</th>
                        <th id="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td>
                                <?php echo $book['book_id']; ?>
                            </td>
                            <td>
                                <?php echo $book['book_title']; ?>
                            </td>
                            <td>
                                <?php echo $book['book_image']; ?>
                            </td>
                            <td>
                                <?php echo $book['book_file']; ?>
                            </td>
                            <td>
                                <?php echo $book['book_author']; ?>
                            </td>
                            <td>
                                <?php
                                // Truncate the description to a certain length (e.g., 100 characters)
                                $truncatedDescription = substr($book['book_description'], 0, 20);
                                echo $truncatedDescription;
                                ?>

                                <?php if (strlen($book['book_description']) > 50): ?>
                                    <a href="#" data-toggle="modal"
                                        data-target="#descriptionModal<?php echo $book['book_id']; ?>">
                                        ... Read more
                                    </a>

                                    <!-- Modal for full description -->
                                    <div class="modal fade" id="descriptionModal<?php echo $book['book_id']; ?>" tabindex="-1"
                                        role="dialog" aria-labelledby="descriptionModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="descriptionModalLabel">Full Description</h5>
                                                    <button type="button" class="btn-close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <?php echo $book['book_description']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $book['publication_date']; ?>
                            </td>
                            <td id="actions">
                                <a href="#" class="button view" data-toggle="modal"
                                    data-target="#viewModal<?php echo $book['book_id']; ?>">View</a>
                                <a href="../Admin/edit_book.php?id=<?php echo $book['book_id']; ?>"
                                    class="button edit">Edit</a>
                                <a href="../Admin/func/delete_book.php?id=<?php echo $book['book_id']; ?>"
                                    class="button delete"
                                    onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                            </td>
                        </tr>
                        <div class="modal fade" id="viewModal<?php echo $book['book_id']; ?>" tabindex="-1" role="dialog"
                            aria-labelledby="viewModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewModalLabel">View Book Details</h5>
                                        <button type="button" class="btn-close" data-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h2>
                                            <?php echo $book['book_title']; ?>
                                        </h2>
                                        <p><strong>Author:</strong>
                                            <?php echo $book['book_author']; ?>
                                        </p>
                                        <p><strong>Description:</strong>
                                            <?php echo $book['book_description']; ?>
                                        </p>
                                        <p><strong>Publication Date:</strong>
                                            <?php echo $book['publication_date']; ?>
                                        </p>
                                        <p><strong>Image:</strong> <img src="<?php echo $book['book_image']; ?>"
                                                alt="Book Image"></p>
                                        <button type="button" class="button view" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="pagination2 mb-4">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" <?php if ($i === $page)
                           echo 'class="active"'; ?>>
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>

            <h1>Library Books</h1>
            <div class="d-flex justify-content-around">
                <div class="input-group me-5">
                    <input type="text" class="form-control form-control-sm px-3" placeholder="Search..."
                        id="searchInputLibrary" style="width: 500px;">
                    <button class="button add me-5" type="button" id="searchButtonLibrary">Search</button>
                </div>
                <div class="button-group">
                    <button type="button" class="button view" data-toggle="modal" data-target="#addBookModal">
                        Add Book
                    </button>
                    <a href="#" class="button archive" data-toggle="modal"
                        data-target="#archiveLibraryModal">Archive</a>
                </div>
            </div>
            <table class="table" id="libraryBooksTable">
                <thead>
                    <tr>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Year Published</th>
                        <th>Author</th>
                        <th>Description</th>
                        <th>Publisher</th>
                        <th>Section</th>
                        <th id="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($libraryBooks as $libraryBook): ?>
                        <tr>
                            <td>
                                <?php echo $libraryBook['book_id']; ?>
                            </td>
                            <td>
                                <?php echo $libraryBook['title']; ?>
                            </td>
                            <td>
                                <?php echo $libraryBook['year_published']; ?>
                            </td>
                            <td>
                                <?php echo $libraryBook['author']; ?>
                            </td>
                            <td>
                                <?php
                                // Truncate the description to a certain length (e.g., 100 characters)
                                $truncatedDescription = substr($libraryBook['description'], 0, 20);
                                echo $truncatedDescription;
                                ?>

                                <?php if (strlen($libraryBook['description']) > 50): ?>
                                    <a href="#" data-toggle="modal"
                                        data-target="#libraryDescriptionModal<?php echo $libraryBook['book_id']; ?>">
                                        ... Read more
                                    </a>

                                    <!-- Modal for full description -->
                                    <div class="modal fade" id="libraryDescriptionModal<?php echo $libraryBook['book_id']; ?>"
                                        tabindex="-1" role="dialog" aria-labelledby="libraryDescriptionModalLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="libraryDescriptionModalLabel">Full Description
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <?php echo $libraryBook['description']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $libraryBook['publisher']; ?>
                            </td>
                            <td>
                                <?php echo $libraryBook['section']; ?>
                            </td>
                            <td id="actions">
                                <a href="#" class="button view" data-toggle="modal"
                                    data-target="#viewLibraryModal<?php echo $libraryBook['book_id']; ?>">View</a>
                                <a href="../Admin/edit_library_book.php?id=<?php echo $libraryBook['book_id']; ?>"
                                    class="button edit">Edit</a>
                                <a href="../Admin/func/delete_library_book.php?id=<?php echo $libraryBook['book_id']; ?>"
                                    class="button delete"
                                    onclick="return confirm('Are you sure you want to delete this library book?')">Delete</a>
                            </td>
                        </tr>
                        <div class="modal fade" id="viewLibraryModal<?php echo $libraryBook['book_id']; ?>" tabindex="-1"
                            role="dialog" aria-labelledby="viewLibraryModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewLibraryModalLabel">View Library Book Details</h5>
                                        <button type="button" class="btn-close" data-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h2>
                                            <?php echo $libraryBook['title']; ?>
                                        </h2>
                                        <p><strong>Author:</strong>
                                            <?php echo $libraryBook['author']; ?>
                                        </p>
                                        <p><strong>Description:</strong>
                                            <?php echo $libraryBook['description']; ?>
                                        </p>
                                        <p><strong>Year Published:</strong>
                                            <?php echo $libraryBook['year_published']; ?>
                                        </p>
                                        <p><strong>Publisher:</strong>
                                            <?php echo $libraryBook['publisher']; ?>
                                        </p>
                                        <p><strong>Section:</strong>
                                            <?php echo $libraryBook['section']; ?>
                                        </p>
                                        <!-- Add more details as needed -->

                                        <!-- Close button -->
                                        <button type="button" class="button view" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination2">
                <?php for ($i = 1; $i <= $totalLibraryPages; $i++): ?>
                    <a href="?library_page=<?php echo $i; ?>" <?php if ($i === $libraryPage)
                           echo 'class="active"'; ?>>
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <form action="" method="POST" enctype="multipart/form-data">
        <div id="addBooksModal" class="modal2">
            <div class="modal-content3">
                <div class="modal-left">
                    <div class="preview-container" id="previewContainer">
                        Click to add a cover photo
                        <img id="previewImage" alt="">
                    </div>
                </div>
                <div class="vertical-line"></div>
                <div class="modal-right">
                    <label for="bookTitle">Title:</label>
                    <input type="text" id="bookTitle" name="book_title" placeholder="Enter book title">

                    <label for="bookAuthor">Author:</label>
                    <input type="text" id="bookAuthor" name="book_author" placeholder="Enter author name">

                    <label for="bookDescription">Description:</label>
                    <textarea id="bookDescription" name="book_description" placeholder="Enter book description"
                        rows="10" maxlength="700"></textarea>

                    <label for="bookImage">Upload Book Cover:</label>
                    <input type="file" id="bookImageInput" name="book_image" accept="image/*">

                    <label for="bookFile">Upload Book:</label>
                    <input type="file" id="bookFileInput" name="book_file" accept=".pdf, .epub">

                    <label for="addBookBtn" class="button-style">Add Book</label>
                    <input type="submit" id="addBookBtn" class="hidden-input">

                    <label for="cancelBtn" class="button-style cancelBtn">Cancel</label>
                    <input type="button" id="cancelBtn" class="hidden-input">

                </div>
                <span class="close-modal">&times;</span>
            </div>
        </div>
    </form>

    <!-- For Regular Books -->
    <div class="modal fade" id="archiveRegularModal" tabindex="-1" role="dialog"
        aria-labelledby="archiveRegularModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archiveRegularModalLabel">Archived Regular Books</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php
                    // Fetch and display archived regular books
                    $sqlArchiveRegularBooks = "SELECT * FROM archived_books";
                    $resultArchiveRegularBooks = $conn->query($sqlArchiveRegularBooks);

                    if ($resultArchiveRegularBooks->num_rows > 0) {
                        // Display the table if there is data
                        echo "<table class='table'>";
                        echo "<thead>
                            <tr>
                                <th>Book ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Description</th>
                                <th>Publication Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>";

                        while ($row = $resultArchiveRegularBooks->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['book_id']}</td>";
                            echo "<td>{$row['book_title']}</td>";
                            echo "<td>{$row['book_author']}</td>";
                            echo "<td>{$row['book_description']}</td>";
                            echo "<td>{$row['publication_date']}</td>";
                            echo '<td><a href="../Admin/func/restore_book.php?book_id=' . $row['book_id'] . '" class="button restore" onclick="return confirm(\'Are you sure you want to restore this reservation?\')">Restore</a></td>';
                            echo "</tr>";
                        }

                        echo "</tbody></table>";
                    } else {
                        // Display a message if there is no data
                        echo "<p>No data available.</p>";
                    }
                    ?>

                    <!-- Close button -->
                    <button type="button" class="button view" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- For Library Books -->
    <div class="modal fade" id="archiveLibraryModal" tabindex="-1" role="dialog"
        aria-labelledby="archiveLibraryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archiveLibraryModalLabel">Archived Library Books</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php
                    // Fetch and display archived library books
                    $sqlArchiveLibraryBooks = "SELECT * FROM archived_library_books";
                    $resultArchiveLibraryBooks = $conn->query($sqlArchiveLibraryBooks);

                    if ($resultArchiveLibraryBooks->num_rows > 0) {
                        // Display the table if there is data
                        echo "<table class='table'>";
                        echo "<thead>
                            <tr>
                                <th>Book ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Description</th>
                                <th>Year Published</th>
                                <th>Publisher</th>
                                <th>Section</th>
                                <!-- Add other columns as needed -->
                            </tr>
                        </thead>
                        <tbody>";

                        while ($row = $resultArchiveLibraryBooks->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['book_id']}</td>";
                            echo "<td>{$row['title']}</td>";
                            echo "<td>{$row['author']}</td>";
                            echo "<td>{$row['description']}</td>";
                            echo "<td>{$row['year_published']}</td>";
                            echo "<td>{$row['publisher']}</td>";
                            echo "<td>{$row['section']}</td>";
                            // Add other columns as needed
                            echo "</tr>";
                        }

                        echo "</tbody></table>";
                    } else {
                        // Display a message if there is no data
                        echo "<p>No data available.</p>";
                    }
                    ?>

                    <!-- Close button -->
                    <button type="button" class="button view" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="addBookModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content px-3">
                <div class="modal-header">
                    <h5 class="modal-title">Add Book</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <!-- Your form goes here -->
                    <form action="processAddBook.php" method="post" id="addBookForm">
                        <div class="form-group">
                            <label for="book_title">Book Title:</label>
                            <input type="text" class="form-control" id="book_title" name="book_title" required>
                        </div>

                        <div class="form-group">
                            <label for="year_published">Year Published:</label>
                            <input type="text" class="form-control" id="year_published" name="year_published" required>
                        </div>

                        <div class="form-group">
                            <label for="book_author">Book Author:</label>
                            <input type="text" class="form-control" id="book_author" name="book_author" required>
                        </div>

                        <div class="form-group">
                            <label for="book_description">Book Description:</label>
                            <textarea class="form-control" id="book_description" name="book_description" rows="4"
                                required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="publisher">Publisher:</label>
                            <input type="text" class="form-control" id="publisher" name="publisher" required>
                        </div>

                        <div class="form-group">
                            <label for="section">Section:</label>
                            <input type="text" class="form-control" id="section" name="section" required>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="button view" data-dismiss="modal">Close</button>
                    <button type="submit" form="addBookForm" class="button view">Add Book</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Librarian Settings -->
    <div class="modal fade" id="librarianSettingsModal" tabindex="-1" role="dialog"
        aria-labelledby="librarianSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="librarianSettingsModalLabel">Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="librarianSettingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general" role="tab"
                                aria-controls="general" aria-selected="true">General</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="account-tab" data-bs-toggle="tab" href="#account" role="tab"
                                aria-controls="account" aria-selected="false">Account</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content mt-3">
                        <!-- General Tab Content -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel"
                            aria-labelledby="general-tab">
                            <form id="generalSettingsForm" method="POST" enctype="multipart/form-data">
                                <!-- Display current website name -->
                                <div class="mb-3">
                                    <label for="currentWebsiteName" class="form-label">Current Website Name</label>
                                    <input type="text" class="form-control" id="currentWebsiteName"
                                        name="currentWebsiteName"
                                        value="<?php echo $currentWebsiteSettings['website_name']; ?>" disabled>
                                </div>

                                <!-- Display current website logo -->
                                <div class="mb-3">
                                    <label for="currentWebsiteLogo" class="form-label">Current Website Logo</label>
                                    <img src="<?php echo $currentWebsiteSettings['website_logo']; ?>"
                                        alt="Current Website Logo" class="img-thumbnail"
                                        style="max-width: 100px; max-height: 100px;">
                                </div>

                                <!-- Allow users to change website name -->
                                <div class="mb-3">
                                    <label for="newWebsiteName" class="form-label">New Website Name</label>
                                    <input type="text" class="form-control" id="newWebsiteName" name="newWebsiteName"
                                        placeholder="Enter new website name">
                                </div>

                                <!-- Allow users to upload a new website logo -->
                                <div class="mb-3">
                                    <label for="newWebsiteLogo" class="form-label">New Website Logo</label>
                                    <input type="file" class="form-control" id="newWebsiteLogo" name="newWebsiteLogo">
                                </div>

                                <button type="submit" name="generalSettingsSubmit" class="button save">Save
                                    Changes</button>
                            </form>
                        </div>

                        <!-- Account Tab Content -->
                        <div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="account-tab">
                            <form id="accountSettingsForm" method="POST">
                                <!-- Display current username -->
                                <div class="mb-3">
                                    <label for="currentUsername" class="form-label">Current Username</label>
                                    <input type="text" class="form-control" id="currentUsername" name="currentUsername"
                                        value="<?php echo $currentUsername; ?>" disabled>
                                </div>

                                <!-- Allow users to change username -->
                                <div class="mb-3">
                                    <label for="newUsername" class="form-label">New Username</label>
                                    <input type="text" class="form-control" id="newUsername" name="newUsername"
                                        placeholder="Enter new username">
                                </div>

                                <!-- Password verification -->
                                <div class="mb-3">
                                    <label for="verifyPassword" class="form-label">Verify Current Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="verifyPassword"
                                            name="verifyPassword" placeholder="Enter current password">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="toggleVerifyPassword">
                                            <i class="fas fa-eye" id="verify-eye-icon" style="font-size: 1rem;"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Allow users to change password -->
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label">New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="newPassword" name="newPassword"
                                            placeholder="Enter new password">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                            <i class="fas fa-eye" id="eye-icon" style="font-size: 1rem;"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" name="accountSettingsSubmit" class="button save">Save
                                    Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Check for the session variable and display the popup if it is set
    if (isset($_SESSION['passwordChanged'])) {
        if ($_SESSION['passwordChanged']) {
            echo '<script>
            Swal.fire({
                icon: "success",
                title: "Password Changed",
                text: "Password changed successfully!",
            });
        </script>';
        } else {
            echo '<script>
            Swal.fire({
                icon: "error",
                title: "Password Change Failed",
                text: "Password change failed. Please try again.",
            });
        </script>';
        }
        unset($_SESSION['passwordChanged']); // Clear the session variable
    } else if (isset($_SESSION['swal_message'])) {
        $swalMessage = $_SESSION['swal_message'];
        echo '<script>
            Swal.fire({
                icon: "' . $swalMessage['type'] . '",
                title: "' . $swalMessage['title'] . '",
                text: "' . $swalMessage['text'] . '",
            });
        </script>';

        // Clear the session variable to avoid displaying the same message on subsequent page loads
        unset($_SESSION['swal_message']);
    }
    ?>

    <script>
        document.getElementById('toggleVerifyPassword').addEventListener('click', function () {
            togglePasswordVisibility('verifyPassword', 'verify-eye-icon');
        });

        document.getElementById('toggleNewPassword').addEventListener('click', function () {
            togglePasswordVisibility('newPassword', 'new-eye-icon');
        });

        function togglePasswordVisibility(passwordFieldId, eyeIconId) {
            var passwordField = document.getElementById(passwordFieldId);
            var eyeIcon = document.getElementById(eyeIconId);

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.className = 'fas fa-eye-slash';
            } else {
                passwordField.type = 'password';
                eyeIcon.className = 'fas fa-eye';
            }
        }
    </script>

    <script>
        const modal = document.getElementById("addBooksModal");
        const closeModalBtn = document.querySelector(".close-modal");
        const bookImageInputModal = document.getElementById("bookImageInput");
        const previewImageModal = document.getElementById("previewImage");
        const bookTitleInput = document.getElementById("bookTitle");
        const bookAuthorInput = document.getElementById("bookAuthor");
        const bookDescriptionInput = document.getElementById("bookDescription");
        const bookFileInput = document.getElementById("bookFileInput");
        const addBookBtn = document.getElementById("addBookBtn");
        const cancelBtn = document.getElementById("cancelBtn");
        const previewContainer = document.getElementById("previewContainer");
        const bookImageInput = document.getElementById("bookImageInput");

        // Show the modal when clicking the "Add Books" button
        document.querySelector(".add-book-button").addEventListener("click", () => {
            modal.style.display = "block";
        });

        // Close the modal and reset inputs when clicking the close button
        closeModalBtn.addEventListener("click", () => {
            closeModal();
        });

        // Close the modal and reset inputs if the user clicks outside of the modal content
        window.addEventListener("click", (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        // Reset inputs and preview image when clicking the cancel button
        cancelBtn.addEventListener("click", () => {
            closeModal();
        });

        function closeModal() {
            modal.style.display = "none";
            resetModalInputs();
            resetPreviewContainer();
        }

        function resetPreviewContainer() {
            previewContainer.style.backgroundImage = ""; // Clear the background image
            previewContainer.innerHTML = "Click to add a cover photo"; // Add the placeholder text
        }


        function resetModalInputs() {
            bookTitleInput.value = "";
            bookAuthorInput.value = "";
            bookDescriptionInput.value = "";
            bookImageInputModal.value = "";
            bookFileInput.value = "";
            previewImageModal.src = "";
            previewContainer.innerHTML = "Click to add a cover photo";
        }

        // Preview the selected image before uploading (for the modal)
        bookImageInputModal.addEventListener("change", (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onloadend = () => {
                    previewImageModal.src = reader.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Function to handle file selection and update preview (for the preview container)
        function handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onloadend = () => {
                    previewContainer.style.backgroundImage = `url(${reader.result})`;
                    previewContainer.innerHTML = ""; // Clear any previous content
                };
                reader.readAsDataURL(file);
            }
        }

        // Add event listener to the hidden file input (for the preview container)
        bookImageInput.addEventListener("change", handleImageUpload);

        // Add click event to the preview container to trigger file input click (for the preview container)
        previewContainer.addEventListener("click", () => {
            bookImageInput.click();
        });

        document.addEventListener("DOMContentLoaded", function () {
            // Function to perform search
            function searchTable(inputId, tableId, noResultsRowId) {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById(inputId);
                filter = input.value.toUpperCase();
                table = document.getElementById(tableId);
                tr = table.getElementsByTagName("tr");

                var noResultsRow = document.getElementById(noResultsRowId);

                // Remove the "No results" row if it exists
                if (noResultsRow) {
                    noResultsRow.remove();
                }

                var resultsFound = false; // Flag to check if any matching rows are found

                for (i = 0; i < tr.length; i++) {
                    var found = false; // Flag to check if at least one field matches the search criteria

                    // Skip the header row
                    if (i === 0) {
                        continue;
                    }

                    // Check each column in a row for a match
                    for (var j = 0; j < tr[i].cells.length; j++) {
                        td = tr[i].cells[j];
                        if (td) {
                            txtValue = td.textContent || td.innerText;

                            // Check if the current column contains the search criteria
                            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                found = true;
                                break; // Exit the loop if a match is found in any column
                            }
                        }
                    }

                    // Show or hide the row based on the search result
                    if (found) {
                        tr[i].style.display = "";
                        resultsFound = true;
                    } else {
                        tr[i].style.display = "none";
                    }
                }

                // Display "No results" message if no matching rows are found
                if (!resultsFound) {
                    var noResultsRow = table.insertRow(-1);
                    var noResultsCell = noResultsRow.insertCell(0);
                    noResultsCell.colSpan = tr[0].cells.length; // Span the cell across all columns
                    noResultsCell.innerHTML = "No results";
                    noResultsRow.id = noResultsRowId;
                    noResultsCell.style.textAlign = "center"; // Center the text
                    noResultsCell.style.fontWeight = "bold"; // Make the text bold
                    noResultsCell.style.color = "gray"; // Set the text color to red
                }
            }

            // Attach the search function to both button click and Enter key press
            document.getElementById("searchButtonRegular").addEventListener("click", function () {
                searchTable("searchInputRegular", "regularBooksTable", "noResultsRowRegular");
            });

            document.getElementById("searchInputRegular").addEventListener("keyup", function (event) {
                if (event.key === "Enter") {
                    searchTable("searchInputRegular", "regularBooksTable", "noResultsRowRegular");
                }
            });

            // Attach the search function to library books section
            document.getElementById("searchButtonLibrary").addEventListener("click", function () {
                searchTable("searchInputLibrary", "libraryBooksTable", "noResultsRowLibrary");
            });

            document.getElementById("searchInputLibrary").addEventListener("keyup", function (event) {
                if (event.key === "Enter") {
                    searchTable("searchInputLibrary", "libraryBooksTable", "noResultsRowLibrary");
                }
            });
        });


    </script>
</body>

</html>