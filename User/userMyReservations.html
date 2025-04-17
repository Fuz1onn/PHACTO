<?php
session_start();
include '../conn/connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Visitor/visitorLandingPage.php");
    exit();
}

// Get user ID from the session
$user_id = $_SESSION['user_id'];

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

// Retrieve user's reservations from the database
$sql = "SELECT * FROM reservations WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch reservations
$reservations = [];
while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
}

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/userMyReservation.css">
    <link rel="stylesheet" href="../Styles/bootstrap.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <title>Panlalawigang Aklatan ng Bulacan</title>
</head>

<body>
    <header>
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
            <a href="../User/userReservation.php">Seat Reservation</a>
            <a href="../User/userFeedback.php">Feedback</a>
        </div>
        <!-- Secondary Navigation for Login and Sign Up -->
        <div class="navbar2">
            <div class="dropdown">
                <a href="#" class="account-link dropdown-toggle" id="accountDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    My Account
                </a>
                <div class="dropdown-menu dropdown-menu-right p-2" aria-labelledby="accountDropdown">
                    <a class="dropdown-item pt-2" href="../User/userProfile.php">
                        <i class="fas fa-user-circle mr-2"></i> My Profile
                    </a>
                    <a class="dropdown-item pt-2 pb-2" href="../User/userReservation.php">
                        <i class="fas fa-calendar-alt mr-2"></i> My Reservations
                    </a>
                </div>
            </div>
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
                        <a href="../Visitor/visitorLandingPage.php#home" class="nav-link">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="link">home</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Visitor/visitorLandingPage.php#about" class="nav-link">
                            <i class='bx bx-info-circle icon'></i>
                            <span class="link">about</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Visitor/visitorLandingPage.php#gallery" class="nav-link">
                            <i class='bx bx-photo-album icon'></i>
                            <span class="link">Gallery</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Visitor/visitorBookViewing.php" class="nav-link">
                            <i class='bx bx-book-content icon'></i>
                            <span class="link">Catalog</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Visitor/visitorReservation.php" class="nav-link">
                            <i class='bx bx-calendar-check icon'></i>
                            <span class="link">Reservation</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="#" class="nav-link">
                            <i class='bx bx-mail-send icon'></i>
                            <span class="link">contact us</span>
                        </a>
                    </li>
                </ul>
                <div class="bottom-content">
                    <li class="list">
                        <a href="#" class="nav-link">
                            <i class="bx bx-cog icon"></i>
                            <span class="link">Settings</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Processes/userLogin.php" class="nav-link">
                            <i class='bx bx-log-in icon'></i>
                            <span class="link">Login / Sign Up</span>
                        </a>
                    </li>
                </div>
            </div>
        </div>
        <!-- Overlay for darkening the background -->
        <div class="overlay" id="overlay"></div>
    </header>

    <section>
        <div class="container-fluid mt-4 p-5" style="background-color: #fff; min-height: 100vh; box-shadow: 0 4px 4px rgba(246, 107, 14, 0.8);">
            <h2 class="mb-4">My Reservations</h2>

            <?php if (empty($reservations)): ?>
                <p>No reservations found.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Section</th>
                            <th>Reservation Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Seat Number</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td>
                                    <?php echo $reservation['id']; ?>
                                </td>
                                <td>
                                    <?php echo $reservation['section']; ?>
                                </td>
                                <td>
                                    <?php echo $reservation['reservation_date']; ?>
                                </td>
                                <td>
                                    <?php echo $reservation['start_time']; ?>
                                </td>
                                <td>
                                    <?php echo $reservation['end_time']; ?>
                                </td>
                                <td>
                                    <?php echo $reservation['seat_number']; ?>
                                </td>
                                <td>
                                    <?php echo $reservation['status']; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
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
            <a href="../User/userContactUs.php">Contact Us</a>
        </div>
        <div class="social-icons">
            <a href="https://www.facebook.com/profile.php?id=100086755154439&mibextid=ZbWKwL" class="social-icon"><i
                    class="fab fa-facebook-f"></i></a>
        </div>
    </footer>

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


</script>

</html>