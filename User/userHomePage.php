<?php

include '../conn/connection.php';
session_start();
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

function getPaginatedBooks($conn, $limit = 6) {
  try {
    $query = "SELECT book_id, book_image, book_file, book_title, book_author, book_description FROM books LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $books = [];

    while($row = $result->fetch_assoc()) {
      $books[] = $row;
    }

    return $books;
  } catch (Exception $e) {
    error_log("Database Query Error: ".$e->getMessage(), 3, "../Admin/logs/error.log");
    exit("An error occurred while fetching book details. Please try again later.");
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

$conn = OpenCon();
$bookCovers = getPaginatedBooks($conn, 6);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../Styles/userHome.css">
  <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
  <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.js'></script>
  <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.js'></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
  <title>Panlalawigang Aklatan ng Bulacan</title>
</head>

<body>
  <header>
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
      <a href="../User/userBookViewing.php">Catalog</a>
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
              <span class="link">contact us</span>
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

  <section class="home" id="home">
    <div class="content">
      <div class="single-image">
        <img src="../images/gallery image/facade.jpg" alt="Your Single Image" width="1335" height="443px">
      </div>
      <div class="featured-books-container">
        <div class="featured-books-text">Featured Books</div>
        <div class="search-box">
          <a href="../User/userBookViewing.php" class="search-button">More</a>
        </div>
        <div class="book-container" id="book-container">
          <?php foreach($bookCovers as $cover): ?>
            <div class="book-cover" data-book-id="<?= $cover['book_id']; ?>"
              data-title="<?= htmlspecialchars($cover['book_title']); ?>"
              data-author="<?= htmlspecialchars($cover['book_author']); ?>"
              data-description="<?= ($cover['book_description']); ?>" data-file="<?= ($cover['book_file']); ?>">
              <img src="../books/uploadedCovers/<?= htmlspecialchars($cover['book_image']); ?>" alt="Book Cover">
              <button class="view-button">View</button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- About Us Section -->
  <section class="about-us" id="about">
    <div class="about-content">
      <div class="about-text">
        <h2>About Us</h2>
        <p>Welcome to Panlalawigang Aklatan ng Bulacan, your gateway to a world of knowledge and exploration. We are
          dedicated to providing a diverse collection of books and resources to enrich the lives of our community.</p>
        <ul>
          <li>Over 50,000 books available</li>
          <li>State-of-the-art facilities</li>
          <li>Experienced and friendly staff</li>
        </ul>
        <p>Our mission is to promote literacy and lifelong learning. Join us on this journey to discover, learn, and
          grow together.</p>
        <a href="userAboutUs.php" class="cta-button">Learn More</a>
      </div>
      <div class="about-image">
        <div class="image-container">
          <img src="../images/libraryimage/library-img1.jpg" alt="Library Image 1">
        </div>
        <div class="image-container">
          <img src="../images/libraryimage/library-img2.jpg" alt="Library Image 2">
        </div>
        <div class="image-container">
          <img src="../images/libraryimage/library-img3.jpg" alt="Library Image 4">
        </div>
      </div>
    </div>
  </section>

  <!-- Gallery Section -->
  <section class="gallery" id="gallery">
    <div class="gallery-heading">
      <h2>Gallery</h2>
      <p>Explore our library through photos</p>
    </div>
    <div class="image-grid">
      <div class="gallery-item">
        <img src="../images/gallery image/gallery1.png" alt="Library Photo 1">
      </div>
      <div class="gallery-item">
        <img src="../images/gallery image/gallery2.png" alt="Library Photo 2">
      </div>
      <div class="gallery-item">
        <img src="../images/gallery image/gallery3.png" alt="Library Photo 3">
      </div>
      <div class="gallery-item">
        <img src="../images/gallery image/gallery4.png" alt="Library Photo 4">
      </div>
      <div class="gallery-item">
        <img src="../images/gallery image/gallery5.jpg" alt="Library Photo 5">
      </div>
      <div class="gallery-item">
        <img src="../images/gallery image/gallery6.jpg" alt="Library Photo 6">
      </div>
      <div class="gallery-item">
        <img src="../images/gallery image/gallery7.jpg" alt="Library Photo 7">
      </div>
      <div class="gallery-item">
        <img src="../images/gallery image/gallery8.jpg" alt="Library Photo 8">
      </div>
      <div class="gallery-item">
        <img src="../images/gallery image/gallery9.jpg" alt="Library Photo 8">
      </div>
      <div class="gallery-item">
        <img src="../images/gallery image/gallery10.jpg" alt="Library Photo 8">
      </div>
    </div>
  </section>

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

  <div class="modal-container" id="bookDetailsModal">
    <div class="modal-content2">
      <span class="close-book-modal" id="close-book-modal">&times;</span>
      <div class="modal-left">
        <img id="modalImage" alt="Book Cover">
      </div>
      <hr class="vertical-line2">
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

</body>


<script src="../Visitor/Script/vlanding.js"></script>
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

  document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: '',
        center: 'title',
        right: ''
      },
    });
    calendar.render();
  });

  // Get the button element
  var backToTopButton = document.getElementById("back-to-top-btn");

  // Show the button when the user scrolls down 20px from the top of the document
  window.onscroll = function () {
    scrollFunction();
  };

  function scrollFunction() {
    if (document.body.scrollTop > 1500 || document.documentElement.scrollTop > 1500) {
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

</html>