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
    <link rel="stylesheet" href="../Styles/userAboutUs.css">
    <link rel="stylesheet" href="../Styles/bootstrap.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <title>Panlalawigang Aklatan ng Bulacan - About Us</title>
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
            <a href="#about">about</a>
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
        <section id="about">
            <div class="container-fluid mt-3 p-5"
                style="background-color: #fff; box-shadow: 0 4px 4px rgba(246, 107, 14, 0.8);">
                <h1 class="mb-4">About Us</h1>
                <h4 class="mt-4">BPL Vision</h4>
                <p>A public library that serves as a source of references through the collection and preservation of
                    the province's historical and cultural heritage, and the development and utilization of library
                    information technologies for the research needs and linkages of individual and institution
                    customers
                    both inside and outside the province.</p>

                <h4 class="mt-4">BPL Mission</h4>
                <ul>
                    <li>To facilitate informal education</li>
                    <li>To support and complement research in all fields of endeavor</li>
                    <li>To provide bibliographical access to the country's information resources</li>
                    <li>To provide wholesome recreation and beneficial use of leisure time</li>
                </ul>

                <h4 class="mt-4">Values and Positive Atmosphere in the Library</h4>
                <p>Library users are expected to observe the BPL's rules and regulations as well as its
                    governing
                    policies. The library is not only responsible for the development of the user's
                    cognitive and
                    psychomotor domains, but also the importance of affective domain takes into
                    consideration.
                    Therefore, it only implies that proper norms and values applicable, tested by the
                    Filipino
                    tradition and custom, and ideological, philosophical and religious principles & beliefs
                    are evident
                    in the library's set-up. What is normally "positive attitudes and virtues" should be
                    exemplified not
                    only in the respective schools, school libraries, academic libraries and special
                    libraries but also
                    in the public library.</p>

                <p>Courtesy and discipline to the Provincial library's materials and manpower should be
                    given emphasis,
                    in accordance with the set standards of the Public Library as a whole.</p>

                <!-- Image Gallery -->
                <div class="row mt-5">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="image-container">
                            <img src="../images/gallery image/gallery8.jpg" class="img-fluid rounded about-us-image"
                                alt="Gallery Image 1">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="image-container">
                            <img src="../images/libraryimage/library-img3.jpg" class="img-fluid rounded about-us-image"
                                alt="Gallery Image 2">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="image-container">
                            <img src="../images/libraryimage/library-img4.jpg" class="img-fluid rounded about-us-image"
                                alt="Gallery Image 3">
                        </div>
                    </div>
                </div>
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