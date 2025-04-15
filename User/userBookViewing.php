<?php
session_start();
include '../conn/connection.php';
$user_id = $_SESSION['user_id'];

if(!isset($user_id)) {
  header('location: ../Visitor/visitorLandingPage.php');
}
;

if(isset($_GET['logout'])) {
  unset($user_id);
  session_destroy();
  header('location: ../Visitor/visitorLandingPage.php');
}

// Function to get paginated books from the "books" table
function getPaginatedBooks($mysqli, $offset, $limit, $search = null, $filter = null) {
  try {
    $query = "SELECT book_id, book_image, book_file, book_title, book_author, book_description 
              FROM books ";

    // Add a WHERE clause only if a search term is provided
    if($search !== null) {
      $query .= "WHERE book_title LIKE ? OR book_author LIKE ? ";
    }

    // Add a filter condition if a filter is provided
    if($filter === 'digitized') {
      $query .= "WHERE book_digitized = 1 ";
    }

    $query .= "LIMIT ? OFFSET ?";

    $stmt = $mysqli->prepare($query);

    // If a search term is provided, bind the parameter
    if($search !== null) {
      $searchTerm = '%'.$search.'%';
      $stmt->bind_param('ssii', $searchTerm, $searchTerm, $limit, $offset);
    } else {
      $stmt->bind_param('ii', $limit, $offset);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    // Fetch the results as an associative array
    $books = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    return $books;
  } catch (mysqli_sql_exception $e) {
    // Handle the exception
    error_log("Database Query Error: ".$e->getMessage(), 3, "../Admin/logs/error.log");
    exit("An error occurred while fetching book details. Please try again later.");
  }
}

function getPaginatedLibraryBooks($mysqli, $offset, $limit, $search = null) {
  try {
    $query = "SELECT book_id, title, year_published, author, description, publisher, section 
                  FROM library_books ";

    // Add a WHERE clause only if a search term is provided
    if($search !== null) {
      $query .= "WHERE title LIKE ? OR author LIKE ? OR year_published LIKE ? ";
    }

    $query .= "LIMIT ? OFFSET ?";

    $stmt = $mysqli->prepare($query);

    // If a search term is provided, bind the parameter
    if($search !== null) {
      $searchTerm = '%'.$search.'%';
      $stmt->bind_param('sssii', $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
    } else {
      $stmt->bind_param('ii', $limit, $offset);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    // Fetch the results as an associative array
    $books = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    return $books;
  } catch (mysqli_sql_exception $e) {
    // Handle the exception
    error_log("Database Query Error: ".$e->getMessage(), 3, "../Admin/logs/error.log");
    exit("An error occurred while fetching book details. Please try again later.");
  }
}

// Function to get the total count of books from the "books" table
function getTotalBooksCount($conn) {
  try {
    $query = "SELECT COUNT(*) FROM books";
    $result = $conn->query($query);
    $count = $result->fetch_row()[0];
    $result->close();
    return $count;
  } catch (mysqli_sql_exception $e) {
    error_log("Database Query Error: ".$e->getMessage(), 3, "../Admin/logs/error.log");
    exit("An error occurred while fetching total book count. Please try again later.");
  }
}

function getTotalLibraryBooksCount($conn) {
  try {
    $query = "SELECT COUNT(*) FROM library_books";
    $result = $conn->query($query);
    $count = $result->fetch_row()[0];
    $result->close();
    return $count;
  } catch (mysqli_sql_exception $e) {
    error_log("Database Query Error: ".$e->getMessage(), 3, "../Admin/logs/error.log");
    exit("An error occurred while fetching total book count. Please try again later.");
  }
}

function getWebsiteSettings($conn) {
  $sql = "SELECT * FROM website_settings WHERE setting_id = 1";
  $result = $conn->query($sql);

  if(!$result) {
    // Handle the error, log it, or display an error message
    die("Error fetching website settings: ".$conn->error);
  }

  if($result->num_rows > 0) {
    return $result->fetch_assoc();
  }

  return null;
}

$currentWebsiteSettings = getWebsiteSettings($conn);

// If data is fetched successfully, use it; otherwise, provide default values
if($currentWebsiteSettings) {
  $websiteName = $currentWebsiteSettings['website_name'];
  $websiteLogo = $currentWebsiteSettings['website_logo'];
} else {
  // Default values (adjust as needed)
  $websiteName = "Default Website Name";
  $websiteLogo = "default_logo.png";
}

$pdo = OpenCon();
$limit = 10;

// Get search term
$search = isset($_GET['search']) ? trim($_GET['search']) : null;
$search = ($search === '') ? null : $search;

// Pagination for book covers
$totalBooks = getTotalBooksCount($pdo);

// Pagination for library books
$totalLibraryBooks = getTotalLibraryBooksCount($pdo);
$totalPagesLibraryBooks = ceil($totalLibraryBooks / $limit);

// Calculate total items based on the selected filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

if($filter == 'all') {
  $totalItems = $totalLibraryBooks;
} elseif($filter == 'digitized') {
  $totalItems = $totalBooks;
} else {
  $totalItems = $totalLibraryBooks;
}

// Calculate total pages for pagination
$totalPages = ceil($totalItems / $limit); // Calculate total pages here
$page = isset($_GET['page']) ? max(1, min((int)$_GET['page'], $totalPages)) : 1;
$offset = ($page - 1) * $limit;
$bookCovers = getPaginatedBooks($pdo, $offset, $limit, $search);
$allBookData = getPaginatedLibraryBooks($pdo, $offset, $limit, $search);

$start = ($page - 1) * $limit;
$end = $start + $limit - 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../Styles/userBooks.css">
  <link rel="stylesheet" href="../Styles/bootstrap.css">
  <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
  <title>Panlalawigang Aklatan ng Bulacan</title>
</head>

<body>
  <header id="home">
    <div class="logo-container">
      <img src="<?php echo $currentWebsiteSettings['website_logo']; ?>" alt="Logo">
      <a href="#" class="logo">
        <?php echo $currentWebsiteSettings['website_name']; ?>
      </a>
    </div>
    <!-- Mobile Menu Icon -->
    <div class="mobile-menu" id="mobile-menu">
      <i class="fas fa-bars menu-icon"></i>
    </div>
    <!-- Navigation Menu -->
    <div class="navbar">
      <a href="../User/userHomePage.php#home">home</a>
      <a href="../User/userAboutUs.php">about</a>
      <a href="../User/userHomePage.php#gallery">Gallery</a>
      <a href="#books">Catalog</a>
      <a href="../User/userReservationOption.php">Seat Reservation</a>
      <a href="../User/userFeedback.php">Feedback</a>
    </div>
    <!-- Secondary Navigation for Login and Sign Up -->
    <div class="navbar2">
      <a href="../User/userProfile.php">My Account</a>
      <span>|</span>
      <a href="../Visitor/visitorLandingPage.php">Logout</a>
    </div>
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
            <a href="../User/userHomePage.php#home" class="nav-link">
              <i class="bx bx-home-alt icon"></i>
              <span class="link">home</span>
            </a>
          </li>
          <li class="list">
            <a href="../User/userHomePage.php#about" class="nav-link">
              <i class='bx bx-info-circle icon'></i>
              <span class="link">about</span>
            </a>
          </li>
          <li class="list">
            <a href="../User/userHomePage.php#gallery" class="nav-link">
              <i class='bx bx-photo-album icon'></i>
              <span class="link">Gallery</span>
            </a>
          </li>
          <li class="list">
            <a href="../User/userBookViewing.php" class="nav-link">
              <i class='bx bx-book-content icon'></i>
              <span class="link">Catalog</span>
            </a>
          </li>
          <li class="list">
            <a href="#" class="nav-link">
              <i class='bx bx-calendar-check icon'></i>
              <span class="link">Reservation</span>
            </a>
          </li>
          <li class="list">
            <a href="../User/userContactUs.php" class="nav-link">
              <i class='bx bx-mail-send icon'></i>
              <span class="link">Contact us</span>
            </a>
          </li>
        </ul>
        <div class="bottom-content">
          <li class="list">
            <a href="../User/userProfile.php" class="nav-link">
              <i class="bx bx-cog icon"></i>
              <span class="link">My Account</span>
            </a>
          </li>
          <li class="list">
            <a href="../Visitor/visitorLandingPage.php" class="nav-link">
              <i class='bx bx-log-in icon'></i>
              <span class="link">Log-out</span>
            </a>
          </li>
        </div>
      </div>
    </div>
    <!-- Overlay for darkening the background -->
    <div class="overlay" id="overlay"></div>
  </header>

  <section class="books" id="books">
    <div class="featured-books-container">
      <div class="featured-books-text">Catalog</div>
      <div class="search-box d-flex justify-content-around w-100">
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="GET" id="uniqueForm" class="form-inline w-50">
          <div class="input-group">
            <input type="text" name="search" id="search" placeholder="Search..." class="form-control"
              value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" oninput="performSearch()">
            <input type="hidden" name="filter"
              value="<?= isset($_GET['filter']) ? htmlspecialchars($_GET['filter']) : 'all' ?>">
            <button type="submit" class="btn btn-primary" style="border: none;"><i class="bx bx-search"></i></button>
          </div>
        </form>
        <div class="dropdown align-self-center">
          <button class="btn btn-primary btn-sm px-4 py-2 dropdown-toggle" type="button" id="searchFilterDropdown"
            data-bs-toggle="dropdown" aria-expanded="false">
            Filter
          </button>
          <ul class="dropdown-menu" aria-labelledby="searchFilterDropdown">
            <li><a class="dropdown-item filter-option" href="#" data-filter="all">All</a></li>
            <li><a class="dropdown-item filter-option" href="#" data-filter="digitized">Digitized</a></li>
          </ul>
        </div>
      </div>

      <!-- Book Covers -->
      <div class="book-container" id="book-container">
        <?php if(isset($_GET['filter']) && $_GET['filter'] === 'digitized'): ?>
          <?php if(empty($bookCovers)): ?>
            <p class="no-results">No results found.</p>
          <?php else: ?>
            <?php foreach($bookCovers as $cover): ?>
              <div class="book-cover" data-book-id="<?= $cover['book_id']; ?>"
                data-title="<?= htmlspecialchars($cover['book_title']); ?>"
                data-author="<?= htmlspecialchars($cover['book_author']); ?>"
                data-description="<?= htmlspecialchars($cover['book_description']); ?>"
                data-file="<?= ($cover['book_file']); ?>">
                <img src="../books/uploadedCovers/<?= htmlspecialchars($cover['book_image']); ?>" alt="Book Cover">
                <button class="view-button">View</button>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php elseif(isset($_GET['filter']) && $_GET['filter'] === 'all'): ?>
          <?php if(!empty($allBookData)): ?>
            <div class="library-books-container">
              <?php foreach($allBookData as $book): ?>
                <div class="library-book" data-title="<?= htmlspecialchars($book['title']); ?>"
                  data-year="<?= htmlspecialchars($book['year_published']); ?>"
                  data-author="<?= htmlspecialchars($book['author']); ?>"
                  data-description="<?= htmlspecialchars($book['description']); ?>"
                  data-publisher="<?= htmlspecialchars($book['publisher']); ?>"
                  data-section="<?= htmlspecialchars($book['section']); ?>">
                  <?= htmlspecialchars($book['title']); ?> (<?= htmlspecialchars($book['year_published']); ?>)
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="no-results">No results found.</p>
          <?php endif; ?>
        <?php else: ?>
          <!-- Display the library books container when no filter is set -->
          <div class="library-books-container">
            <?php foreach($allBookData as $book): ?>
              <div class="library-book" data-title="<?= htmlspecialchars($book['title']); ?>"
                data-year="<?= htmlspecialchars($book['year_published']); ?>"
                data-author="<?= htmlspecialchars($book['author']); ?>"
                data-description="<?= htmlspecialchars($book['description']); ?>"
                data-publisher="<?= htmlspecialchars($book['publisher']); ?>"
                data-section="<?= htmlspecialchars($book['section']); ?>">
                <?= htmlspecialchars($book['title']); ?> (<?= htmlspecialchars($book['year_published']); ?>)
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="pagination" id="pagination">
        <?php for($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?page=<?= $i ?>&filter=<?= isset($_GET['filter']) ? $_GET['filter'] : 'all' ?>"
            class="pagination-button <?= $i == $page ? 'active' : '' ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>
      </div>

    </div>
  </section>

  <div class="modal-container" id="bookDetailsModal">
    <div class="modal-content2">
      <span class="close-book-modal" id="close-book-modal">&times;</span>
      <div class="modal-left">
        <img id="modalImage" alt="Book Cover">
      </div>
      <div class="vertical-line2"></div>
      <div class="modal-right">
        <h2 id="modalTitle"></h2>
        <p><span id="modalAuthor"></span></p>
        <hr> <!-- Add a line between the sections -->
        <p><span id="modalDescription"></span></p>
      </div>
      <div class="modal-buttons">
        <button class="view-book-button" id="view-book-button">View</button>
        <button class="cancel-button" id="cancel-button">Cancel</button>
      </div>
    </div>
  </div>

  <!-- Modal for displaying title information -->
  <div class="modal fade" id="titleInfoModal" tabindex="-1" aria-labelledby="titleInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content px-2">
        <div class="modal-header">
          <h5 class="modal-title" id="titleInfoModalLabel"><strong>Explore Book Details</strong></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <h5 class="mb-3"><strong>Title:</strong> <span id="modalTitleInfo" class="fw-bold"></span></h5>
              <p class="mb-1"><strong>Author:</strong> <span id="modalAuthorInfo"></span></p>
              <p class="mb-1"><strong>Publication Year:</strong> <span id="modalYear"></span></p>
              <p class="mb-1"><strong>Publisher:</strong> <span id="modalPublisher"></span></p>
              <p class="mb-1"><strong>Section:</strong> <span id="modalSection"></span></p>
              <hr>
              <p class="mb-0"><strong>Synopsis:</strong> <span id="modalDescriptionInfo"></span></p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="button close" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <footer class="modern-footer">
    <div class="footer-content">
      <div class="footer-logo">
        <img src="<?php echo $currentWebsiteSettings['website_logo']; ?>" alt="Logo">
      </div>
      <div class="footer-text">
        <h2>
          <?php echo $currentWebsiteSettings['website_name']; ?>
        </h2>
        <p>&copy; 2023 All rights reserved</p>
        <p>Email: <a href="bulacanprovlib2020@gmail.com">bulacanprovlib2020@gmail.com</a></p>
        <p>Phone: +123-456-7890</p>
      </div>
      <div class="footer-logo2">
        <img src="../images/logo/logo2.png" alt="Footer Logo">
      </div>
    </div>
    <div class="footer-links">
      <a href="../User/userPrivacyPolicy.php">Privacy Policy</a>
      <a href="../User/userTermsConditions.php">Terms & Conditions</a>
      <a href="../User/userContactUs.php">Contact Us</a>
    </div>
    <div class="social-icons">
      <a href="https://www.facebook.com/profile.php?id=100086755154439&mibextid=ZbWKwL" class="social-icon"><i
          class="fab fa-facebook-f"></i></a>
    </div>
  </footer>

  <button id="back-to-top-btn">
    <div class="back-to-top-icon">
      <i class="fas fa-arrow-up"></i>
    </div>
  </button>

  <script>
    $(document).ready(function () {
      // Set the initial filter value (can be retrieved from the URL or a default value)
      var initialFilter = "<?php echo isset($_GET['filter']) ? $_GET['filter'] : 'all'; ?>";

      // Set the dropdown button text to the initial filter value
      $("#searchFilterDropdown").text(initialFilter.charAt(0).toUpperCase() + initialFilter.slice(1));

      // Handle filter option clicks
      $(".filter-option").on("click", function (e) {
        e.preventDefault();

        // Get the selected filter value
        var selectedFilter = $(this).data("filter");

        // Update the dropdown button text with the selected filter
        $("#searchFilterDropdown").text(selectedFilter.charAt(0).toUpperCase() + selectedFilter.slice(1));

        // Redirect to the current page with the selected filter as a query parameter
        window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>?filter=" + selectedFilter;
      });
    });
  </script>

  <script>
    var filterValue = "<?php echo isset($_GET['filter']) ? $_GET['filter'] : 'all'; ?>";
    // Function to perform search
    function performSearch() {
      // Get the search input value
      var searchValue = document.getElementById('search').value;

      // Execute the search dynamically
      fetchResults(searchValue, filterValue);
    }

    // Function to fetch search results
    function fetchResults(searchTerm, filter) {
      $.ajax({
        url: 'search_script.php',  // Replace with the actual endpoint for searching
        method: 'GET',
        data: { search: searchTerm, filter: filter },
        success: function (data) {
          // Update the search results container with the data
          document.getElementById('book-container').innerHTML = data;

          // Attach event listeners after updating results
          attachEventListeners();
        },
        error: function (error) {
          console.error('Error fetching search results:', error);
        }
      });
    }

    // Event listener for changes in the search input
    document.getElementById('search').addEventListener('input', function () {
      // Trigger the search while typing
      performSearch();
    });

    // Event listener for dropdown filter
    document.querySelectorAll('.filter-option').forEach(function (option) {
      option.addEventListener('click', function (event) {
        // Prevent the default behavior of the anchor element
        event.preventDefault();

        // Update filterValue with the selected filter
        filterValue = this.getAttribute('data-filter');

        // Log the filterValue for debugging
        console.log('Filter Value:', filterValue);

        // Optionally, update the UI to reflect the selected filter
        document.getElementById('searchFilterDropdown').innerText = 'Filter: ' + filterValue;

        // Trigger the search with the new filter value
        performSearch();
      });
    });


    // Function to attach event listeners to search results
    function attachEventListeners() {
      // Event listener for book covers
      const bookCovers = document.querySelectorAll('.book-cover');

      bookCovers.forEach((cover) => {
        cover.addEventListener('click', (event) => {
          // Check if the click event occurred on the view button or book image
          const clickedElement = event.target;
          const isViewButton = clickedElement.classList.contains('view-button');
          const isBookImage = clickedElement.tagName === 'IMG';

          if (isViewButton || isBookImage) {
            bookId = cover.getAttribute('data-book-id');
            const title = cover.getAttribute('data-title');
            const author = cover.getAttribute('data-author');
            const description = cover.getAttribute('data-description');
            const imagePath = cover.querySelector('img').getAttribute('src'); // Get the book cover image source

            // Populate modal with book details and image
            document.getElementById("modalTitle").textContent = title;
            document.getElementById("modalAuthor").textContent = author;
            document.getElementById("modalDescription").textContent = description;
            document.getElementById("modalImage").src = imagePath; // Set the book cover image source

            // Display the modal
            bookDetailsModal.style.display = "block";
          }
        });
      });

      // Event listener for the "View" button inside the modal
      const viewBookButtonModal = document.getElementById("view-book-button");
      viewBookButtonModal.addEventListener('click', () => {
        // Use the bookId variable here
        if (bookId) {
          fetchFileForBookId(bookId).then((response) => {
            if (response.ok) {
              return response.json();
            } else {
              throw new Error('Network response was not ok.');
            }
          }).then((data) => {
            const filePath = data.filePath;
            // Open the file in a new tab/window
            window.location.href = '../books/uploadedBooks/' + filePath;
          }).catch((error) => {
            console.error('There has been a problem with your fetch operation:', error);
          });
        }

        // Optionally, close the modal after clicking "View"
        document.getElementById("bookDetailsModal").style.display = "none";
      });

      // Function to fetch file path for a specific book ID from the server
      function fetchFileForBookId(bookId) {
        return fetch('../Admin/auth/fetch_file.php?book_id=' + bookId)
          .then((response) => response);
      }

      // Event listener for the close button
      closeModalButton.addEventListener("click", () => {
        bookDetailsModal.style.display = "none"; // Close the modal by hiding it

        // Reset modal content if needed
        document.getElementById("modalTitle").textContent = "";
        document.getElementById("modalAuthor").textContent = "";
        document.getElementById("modalDescription").textContent = "";
      });

      cancelModalButton.addEventListener("click", () => {
        bookDetailsModal.style.display = "none"; // Close the modal by hiding it

        // Reset modal content if needed
        document.getElementById("modalTitle").textContent = "";
        document.getElementById("modalAuthor").textContent = "";
        document.getElementById("modalDescription").textContent = "";
      });

      // Close modal if user clicks outside the modal content
      window.addEventListener("click", (event) => {
        if (event.target === bookDetailsModal) {
          bookDetailsModal.style.display = "none";

          // Reset modal content if needed
          document.getElementById("modalTitle").textContent = "";
          document.getElementById("modalAuthor").textContent = "";
          document.getElementById("modalDescription").textContent = "";
        }
      });

      const titleInfoModal = new bootstrap.Modal(document.getElementById('titleInfoModal'));
      const bookTitles = document.querySelectorAll('.library-book');
      const modalTitle = document.getElementById('modalTitleInfo');
      const modalAuthor = document.getElementById('modalAuthorInfo');
      const modalYear = document.getElementById('modalYear');
      const modalPublisher = document.getElementById('modalPublisher');
      const modalSection = document.getElementById('modalSection');
      const modalDescription = document.getElementById('modalDescriptionInfo');

      document.querySelectorAll('.library-book').forEach(function (book) {
        book.addEventListener('click', () => {
          modalTitle.innerText = book.getAttribute('data-title');
          modalAuthor.innerText = book.getAttribute('data-author');
          modalYear.innerText = book.getAttribute('data-year');
          modalPublisher.innerText = book.getAttribute('data-publisher');
          modalSection.innerText = book.getAttribute('data-section');
          modalDescription.innerText = book.getAttribute('data-description');

          titleInfoModal.show(); // Use 'show' to display the modal
        });
      });
    }

    // Initial attachment of event listeners
    attachEventListeners();
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const titleInfoModal = new bootstrap.Modal(document.getElementById('titleInfoModal'));
      const modalTitle = document.getElementById('modalTitleInfo');
      const modalAuthor = document.getElementById('modalAuthorInfo');
      const modalYear = document.getElementById('modalYear');
      const modalPublisher = document.getElementById('modalPublisher');
      const modalSection = document.getElementById('modalSection');
      const modalDescription = document.getElementById('modalDescriptionInfo');

      // Event listener for library books
      document.querySelectorAll('.library-book').forEach(function (book) {
        book.addEventListener('click', function () {
          modalTitle.innerText = book.getAttribute('data-title');
          modalAuthor.innerText = book.getAttribute('data-author');
          modalYear.innerText = book.getAttribute('data-year');
          modalPublisher.innerText = book.getAttribute('data-publisher');
          modalSection.innerText = book.getAttribute('data-section');
          modalDescription.innerText = book.getAttribute('data-description');

          titleInfoModal.show(); // Use 'show' to display the modal
        });
      });
    });
  </script>

  <script>
    // Get all book covers
    const bookCovers = document.querySelectorAll('.book-cover');

    // Get modal elements
    const bookDetailsModal = document.getElementById("bookDetailsModal");
    const cancelModalButton = document.getElementById("cancel-button");
    const viewBookButtonModal = document.getElementById("view-book-button");
    const closeModalButton = document.querySelector(".close-book-modal");
    const modalTitle = document.getElementById("modalTitle");
    const modalAuthor = document.getElementById("modalAuthor");
    const modalDescription = document.getElementById("modalDescription");

    let bookId;

    // Event listener for book covers
    bookCovers.forEach((cover) => {
      cover.addEventListener('click', (event) => {
        // Check if the click event occurred on the view button or book image
        const clickedElement = event.target;
        const isViewButton = clickedElement.classList.contains('view-button');
        const isBookImage = clickedElement.tagName === 'IMG';

        if (isViewButton || isBookImage) {
          bookId = cover.getAttribute('data-book-id');
          const title = cover.getAttribute('data-title');
          const author = cover.getAttribute('data-author');
          const description = cover.getAttribute('data-description');
          const imagePath = cover.querySelector('img').getAttribute('src'); // Get the book cover image source

          // Populate modal with book details and image
          document.getElementById("modalTitle").textContent = title;
          document.getElementById("modalAuthor").textContent = author;
          document.getElementById("modalDescription").textContent = description;
          document.getElementById("modalImage").src = imagePath; // Set the book cover image source

          // Display the modal
          bookDetailsModal.style.display = "block";
        }
      });
    });

    // Event listener for the "View" button inside the modal
    viewBookButtonModal.addEventListener('click', () => {
      // Use the bookId variable here
      if (bookId) {
        fetchFileForBookId(bookId).then((response) => {
          if (response.ok) {
            return response.json();
          } else {
            throw new Error('Network response was not ok.');
          }
        }).then((data) => {
          const filePath = data.filePath;
          // Open the file in a new tab/window
          window.location.href = '../books/uploadedBooks/' + filePath;
        }).catch((error) => {
          console.error('There has been a problem with your fetch operation:', error);
        });
      }

      // Optionally, close the modal after clicking "View"
      document.getElementById("bookDetailsModal").style.display = "none";
    });

    // Function to fetch file path for a specific book ID from the server
    function fetchFileForBookId(bookId) {
      return fetch('../Admin/auth/fetch_file.php?book_id=' + bookId)
        .then((response) => response);
    }

    // Event listener for the close button
    closeModalButton.addEventListener("click", () => {
      bookDetailsModal.style.display = "none"; // Close the modal by hiding it

      // Reset modal content if needed
      document.getElementById("modalTitle").textContent = "";
      document.getElementById("modalAuthor").textContent = "";
      document.getElementById("modalDescription").textContent = "";
    });

    cancelModalButton.addEventListener("click", () => {
      bookDetailsModal.style.display = "none"; // Close the modal by hiding it

      // Reset modal content if needed
      document.getElementById("modalTitle").textContent = "";
      document.getElementById("modalAuthor").textContent = "";
      document.getElementById("modalDescription").textContent = "";
    });

    // Close modal if user clicks outside the modal content
    window.addEventListener("click", (event) => {
      if (event.target === bookDetailsModal) {
        bookDetailsModal.style.display = "none";

        // Reset modal content if needed
        document.getElementById("modalTitle").textContent = "";
        document.getElementById("modalAuthor").textContent = "";
        document.getElementById("modalDescription").textContent = "";
      }
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const mobileMenu = document.querySelector('.mobile-menu');
      const sidebar = document.querySelector('.sidebar');
      const overlay = document.querySelector('.overlay');

      // Function to open the sidebar
      function openSidebar() {
        sidebar.style.right = '0';
        overlay.style.display = 'block'; // Show the overlay
      }

      // Function to close the sidebar
      function closeSidebar() {
        sidebar.style.right = '-260px';
        overlay.style.display = 'none'; // Hide the overlay
      }

      mobileMenu.addEventListener('click', function (event) {
        event.stopPropagation(); // Prevent the click event from propagating to the document
        openSidebar();
      });

      document.addEventListener('click', function (event) {
        if (
          event.target !== mobileMenu && // Clicked outside the mobile menu icon
          !sidebar.contains(event.target) // Clicked outside the sidebar
        ) {
          closeSidebar();
        }
      });

      // Prevent clicks inside the sidebar from closing it
      sidebar.addEventListener('click', function (event) {
        event.stopPropagation();
      });

      // Add a resize event listener to close the sidebar when the window size changes
      window.addEventListener('resize', function () {
        if (window.innerWidth > 1081) {
          closeSidebar();
        }
      });
    });

    // Get the button element
    var backToTopButton = document.getElementById("back-to-top-btn");

    // Show the button when the user scrolls down 20px from the top of the document
    window.onscroll = function () {
      scrollFunction();
    };

    function scrollFunction() {
      if (document.body.scrollTop > 1000 || document.documentElement.scrollTop > 1000) {
        backToTopButton.style.display = "block";
      } else {
        backToTopButton.style.display = "none";
      }
    }

    // Scroll to the top of the document when the button is clicked
    backToTopButton.addEventListener("click", function () {
      // Use the scrollIntoView method with behavior set to 'smooth'
      window.scrollTo({
        top: 0,
        behavior: "smooth"
      });
    });

  </script>

</body>

</html>