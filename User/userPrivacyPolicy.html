<?php

include '../conn/connection.php';
session_start();
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

$conn = OpenCon();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/userPrivacyPolicy.css">
    <link rel="stylesheet" href="../Styles/bootstrap.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
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
            <a href="../User/userHomePage.php#gallery">gallery</a>
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

    <section id="privacy">
        <div class="container-fluid mt-3 p-5"
            style="background-color: #fff; box-shadow: 0 4px 4px rgba(246, 107, 14, 0.8);">
            <h2 class="mb-4">Privacy Policy</h2>
            <p class="mb-4"><em>Last Updated: November 15, 2023</em></p>

            <p>Welcome to <strong>Panlalawigang Aklatan ng Bulacan</strong>. This Privacy Policy outlines how we
                collect, use, disclose,
                and protect your information when you use our online catalog viewing system with seat reservation
                through our
                web application.</p>

            <h3 class="mt-4">1. Information We Collect</h3>
            <p class="mb-2">a. User-Provided Information</p>
            <p>When using our web application, you may provide us with the following information:</p>
            <ul>
                <li><strong>Personal Information:</strong> Your name, email address, phone number, and other relevant
                    details required for seat reservations.</li>
                <li><strong>Transaction Information:</strong> Details about seat reservations, including date,
                    time, and any other transaction-related information.</li>
            </ul>

            <p class="mt-3 mb-2">b. Automatically Collected Information</p>
            <p>We may automatically collect certain information about your device and usage of the web application,
                including:</p>
            <ul>
                <li><strong>IP Address</strong></li>
                <li><strong>Browser Type</strong></li>
                <li><strong>Device Type</strong></li>
                <li><strong>Operating System</strong></li>
                <li><strong>Pages Viewed</strong></li>
                <li><strong>Usage Patterns</strong></li>
            </ul>

            <h3 class="mt-4">2. How We Use Your Information</h3>
            <p>We use the collected information for the following purposes:</p>
            <ul>
                <li><strong>Seat Reservations:</strong> To process and manage seat reservations.</li>
                <li><strong>Communication:</strong> To send confirmation emails, updates, and relevant information
                    about your reservations.</li>
                <li><strong>Improvement:</strong> To analyze user behavior and preferences, enabling us to enhance
                    and optimize our web application.</li>
                <li><strong>Marketing:</strong> To send promotional materials, with your consent.</li>
            </ul>

            <h3 class="mt-4">3. Data Security</h3>
            <p>We prioritize the security of your information and employ reasonable measures to protect against
                unauthorized access, alteration, disclosure, or destruction of data. However, no method of
                transmission over the internet or electronic storage is entirely secure.</p>

            <h3 class="mt-4">4. Sharing of Information</h3>
            <p>We may share your information in the following situations:</p>
            <ul>
                <li><strong>Consent:</strong> With your explicit consent for specific purposes.</li>
                <li><strong>Service Providers:</strong> With trusted third-party service providers involved in
                    facilitating our web application and services.</li>
                <li><strong>Legal Compliance:</strong> To comply with legal obligations, respond to legal requests, or
                    protect our rights.</li>
            </ul>

            <h3 class="mt-4">5. Your Choices</h3>
            <p>You have the right to:</p>
            <ul>
                <li><strong>Access and Update:</strong> Access, update, or correct your personal information through
                    your account settings.</li>
                <li><strong>Opt-Out:</strong> Opt-out of promotional communications by following the instructions in
                    the emails we send.</li>
            </ul>

            <h3 class="mt-4">6. Changes to This Privacy Policy</h3>
            <p>We may update this Privacy Policy periodically. Changes will be posted on this page, and the
                "<strong>Last
                    Updated</strong>"
                date will reflect the latest revision.</p>

            <h3 class="mt-4">7. Contact Us</h3>
            <p>If you have questions or concerns about this Privacy Policy, please contact us at <a
                    href="mailto:bulacanprovlib2020@gmail.com">bulacanprovlib2020@gmail.com</a></p>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha384-9a8e0050f6b6b5fe95a81b6e63d99b83cdcc6ab0b8a86c8f33a5f8ccf32a4c87"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy/Z1AtUR5CG7QpGxsIqV6U/DJ4oU" crossorigin="anonymous"></script>

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