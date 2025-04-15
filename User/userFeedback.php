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

if (isset($_POST["feed_submit"])) {
    $feed_rate = $_POST["feed_rate"];
    $feed_name = $_POST["feed_name"];
    $feed_email = $_POST["feed_email"];
    $feed_comment = $_POST["feed_comment"];

    // Assuming $conn is your database connection

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO feedback (feed_rate, feed_name, feed_email, feed_comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $feed_rate, $feed_name, $feed_email, $feed_comment);

    if ($stmt->execute()) {
        // Echo a success message to be read by JavaScript
        echo '<script>
                var successMessage = "Feedback submitted successfully!";
            </script>';
    } else {
        echo "Error: " . $stmt->error;
    }
    // Close the database connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/feedback.css">
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
            <a href="../User/userReservationOption.php">Seat Reservation</a>
            <a href="#feedback">Feedback</a>
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

    <div class="container feedback-container px-4">
        <h2 class="text-center mb-4" style="color: var(--header);">Provide Feedback</h2>
        <form action="" method="POST">
            <div class="form-group mb-3">
                <label for="rating" class="mb-1 text-none">How likely you would like to recommend us to your
                    friends?</label>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="feed_rate" id="rating1" value="1" style="display: none;">1
                    </label>
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="feed_rate" id="rating2" value="2" style="display: none;">2
                    </label>
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="feed_rate" id="rating3" value="3" style="display: none;">3
                    </label>
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="feed_rate" id="rating4" value="4" style="display: none;">4
                    </label>
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="feed_rate" id="rating5" value="5" style="display: none;">5
                    </label>
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="feed_rate" id="rating6" value="6" style="display: none;">6
                    </label>
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="feed_rate" id="rating7" value="7" style="display: none;">7
                    </label>
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="feed_rate" id="rating8" value="8" style="display: none;">8
                    </label>
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="feed_rate" id="rating9" value="9" style="display: none;">9
                    </label>

                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="feed_rate" id="rating10" value="10" style="display: none;">10
                    </label>
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="feed_name" name="feed_name" placeholder="Enter your name"
                    required>
            </div>
            <div class="form-group mb-3">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="feed_email" name="feed_email"
                    placeholder="Enter your email" required>
            </div>
            <div class="form-group mb-3">
                <label for="feedback">Feedback:</label>
                <textarea class="form-control" id="feed_comment" name="feed_comment" rows="4"
                    placeholder="Enter your feedback" required></textarea>
            </div>
            <button type="submit" id="submitFeedback" name="feed_submit" class="btn btn-primary" style="width:100%"
                data-toggle="modal" data-target="#successModal">Submit Feedback</button>
        </form>
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

    <!-- Bootstrap Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Feedback Submitted Successfully!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Thank you for your feedback!
                </div>
                <div class="modal-footer">
                    <button type="button" class="button cancel" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript to add and remove a class on button click
        document.addEventListener('DOMContentLoaded', function () {
            let btnGroup = document.querySelector('.btn-group-toggle');

            btnGroup.addEventListener('click', function (event) {
                // Remove focus class from all buttons in the group
                btnGroup.querySelectorAll('.btn').forEach(function (btn) {
                    btn.classList.remove('btn-focus');
                });

                // Add focus class to the clicked button
                let clickedBtn = event.target.closest('.btn');
                if (clickedBtn) {
                    clickedBtn.classList.add('btn-focus');
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            // Check if the successMessage is set
            if (typeof successMessage !== 'undefined') {
                // Show the modal
                $('#successModal').modal('show');
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


</script>

</html>