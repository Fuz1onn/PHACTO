<?php
session_start();
include '../conn/connection.php';
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
  header('location: ../Visitor/visitorLandingPage.php');
}
;

if (isset($_GET['logout'])) {
  unset($user_id);
  session_destroy();
  header('location: ../Visitor/visitorLandingPage.php');
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

// Check if the form is submitted
if (isset($_POST['contact_submit'])) {
  // Process and sanitize form data
  $name = htmlspecialchars($_POST['contact_name']);
  $email = filter_var($_POST['contact_email'], FILTER_SANITIZE_EMAIL);
  $message = htmlspecialchars($_POST['contact_message']);

  // Validate email address
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email address.";
    exit; // Stop execution if the email is invalid
  }

  // Prepare and execute the SQL query to insert data into the database
  $stmt = $conn->prepare("INSERT INTO contact_us (contact_name, contact_email, contact_message) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $message);

  if ($stmt->execute()) {
    // Data inserted successfully
    echo '<script>
              var contactSuccessMessage = "Message sent successfully!";
            </script>';
  } else {
    // Error occurred while inserting data
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../Styles/contactus.css">
  <link rel="stylesheet" href="../Styles/bootstrap.css">
  <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
  <title>Panlalawigang Aklatan ng Bulacan</title>
</head>

<body>
  <header id="home">
  <div class="logo-container">
      <img src="<?php echo $currentWebsiteSettings['website_logo']; ?>" alt="Logo">
      <a href="#" class="logo-name1">
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

  <section class="contact" id="contact">
    <div class="contact-container">
      <h2>Contact Us</h2>
      <p>If you have any questions or need assistance, feel free to get in touch with us using the contact form or
        contact details below.</p>

      <!-- Contact Form -->
      <div class="contact-form">
        <form action="" method="POST">
          <label for="name">Name:</label>
          <input type="text" placeholder="Enter your name" id="contact_name" name="contact_name" required>

          <label for="email">Email:</label>
          <input type="email" placeholder="Enter your email" id="contact_email" name="contact_email" required>

          <label for="message">Message:</label>
          <textarea id="message" placeholder="Enter your message" name="contact_message" rows="4" required></textarea>

          <button type="submit" name="contact_submit" data-toggle="modal"
            data-target="#contactSuccessModal">Send</button>
        </form>
      </div>

      <!-- Contact Information -->
      <div class="contact-info">
        <h3>Contact Information</h3>
        <p><strong>Address:</strong> Provincial Capitol Compound Malolos, Bulacan 3000</p>
        <p><strong>Email:</strong> <a href="bulacanprovlib2020@gmail.com">bulacanprovlib2020@gmail.com</a></p>
        <p><strong>Phone:</strong> +1 (123) 456-7890</p>
      </div>
    </div>
  </section>

  <footer class="modern-footer">
    <div class="footer-content">
      <div class="footer-logo">
      <img src="<?php echo $currentWebsiteSettings['website_logo']; ?>" alt="Logo">
      </div>
      <div class="footer-text">
        <h2><?php echo $currentWebsiteSettings['website_name']; ?></h2>
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
      <a href="../User/userContactUs.php#contact">Contact Us</a>
    </div>
    <div class="social-icons">
      <a href="https://www.facebook.com/profile.php?id=100086755154439&mibextid=ZbWKwL" class="social-icon"><i
          class="fab fa-facebook-f"></i></a>
    </div>
  </footer>

  <div class="modal fade" id="contactSuccessModal" tabindex="-1" role="dialog"
    aria-labelledby="contactSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="contactSuccessModalLabel">Message Sent Successfully!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Thank you for contacting us! We will get back to you soon.
        </div>
        <div class="modal-footer">
          <button type="button" class="button cancel" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function () {
      // Check if the contactSuccessMessage is set
      if (typeof contactSuccessMessage !== 'undefined') {
        // Show the contact success modal
        $('#contactSuccessModal').modal('show');
      }
    });
  </script>

</body>

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


</script>

</html>