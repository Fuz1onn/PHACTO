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
    <link rel="stylesheet" href="../Styles/userTermsConditions.css">
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

    <main>
        <section id="terms">
            <div class="container-fluid mt-3 p-5"
                style="background-color: #fff; box-shadow: 0 4px 4px rgba(246, 107, 14, 0.8);">
                <h2 class="mb-4">Terms & Conditions</h2>
                <p>By accessing and using our online catalog viewing system with seat reservation through our web
                    application, you agree to comply with and be bound by the following terms and conditions. If you do
                    not agree with these terms, please refrain from using our services.</p>

                <h3 class="mt-4">1. Acceptance of Terms</h3>
                <p>By using our web application, you acknowledge and agree to these terms and conditions. We reserve the
                    right to modify or amend these terms at any time without notice. It is your responsibility to review
                    these terms regularly.</p>

                <h3 class="mt-4">2. Use of Services</h3>
                <p>You agree to use our web application and services for lawful purposes and in accordance with these
                    terms. You must not use our services in a way that may cause harm to the web application, other
                    users, or violate any applicable laws or regulations.</p>

                <h3 class="mt-4">3. Seat Reservations</h3>
                <p>Our web application allows users to make seat reservations. By making a reservation, you agree to
                    provide accurate and complete information. We reserve the right to cancel or refuse any reservation
                    at our discretion.</p>

                <h3 class="mt-4">4. User Accounts</h3>
                <p>To use certain features of our web application, you may be required to create a user account. You are
                    responsible for maintaining the confidentiality of your account information and for all activities
                    that occur under your account.</p>

                <h3 class="mt-4">5. Intellectual Property</h3>
                <p>All content and materials available on our web application, including but not limited to text,
                    graphics, logos, button icons, images, audio clips, and software, are the property of Panlalawigang
                    Aklatan ng Bulacan or its licensors and are protected by copyright and other intellectual property
                    laws.</p>

                <h3 class="mt-4">6. Privacy</h3>
                <p>Your use of our web application is also governed by our <a
                        href="../User/userPrivacyPolicy.php">Privacy
                        Policy</a>. By using our services, you consent to the collection, use, and sharing of
                    information as described in the Privacy Policy.</p>

                <h3 class="mt-4">7. Limitation of Liability</h3>
                <p>To the fullest extent permitted by applicable law, the library shall not be liable for any indirect,
                    incidental, special, consequential, or punitive damages, or any loss of profits or revenues, whether
                    incurred directly or indirectly, or any loss of data, use, goodwill, or other intangible losses.</p>

                <h3 class="mt-4">8. Governing Law</h3>
                <p>These terms and conditions are governed by and construed in accordance with the laws of the
                    Philippines. Any disputes arising under or in connection with these terms shall be subject to the
                    exclusive jurisdiction of the courts in the Philippines. </p>

                <h3 class="mt-4">9. Contact Us</h3>
                <p>If you have any questions or concerns about these terms and conditions, please contact us at <a
                        href="mailto:bulacanprovlib2020@gmail.com">bulacanprovlib2020@gmail.com</a></p>
            </div>
        </section>
    </main>

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