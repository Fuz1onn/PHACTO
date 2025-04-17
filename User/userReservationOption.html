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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/userMyReservationOption.css">
    <link rel="stylesheet" href="../Styles/bootstrap.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
            <a href="#Option">Seat Reservation</a>
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

    <section id="Option">
        <div class="container mt-5 p-5" style="background-color: #fff; box-shadow: 0 4px 4px rgba(246, 107, 14, 0.8);">
            <h2 class="mb-4">Seat Reservation</h2>

            <!-- Heading and additional text before the buttons -->
            <div class="mb-4">
                <p>What would you like to do today?</p>
                <p>Explore the options below and manage your reservations accordingly.</p>
            </div>

            <!-- Buttons arranged in a column with extended text descriptions -->
            <div class="buttons-container">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <div class="d-flex justify-content-start align-items-center">
                            <a href="userReservation.php" class="button check" id="reserveButton">Reserve</a>
                            <div class="ml-3">
                                <p class="mb-0">Make a new reservation</p>
                                <p class="text-muted small">Plan your visit and secure your seat in advance.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="d-flex justify-content-start align-items-center">
                            <button class="button check" id="checkInButton">Check In</button>
                            <div class="ml-3">
                                <p class="mb-0">Check in for your seat</p>
                                <p class="text-muted small">Arrive on time and confirm your attendance.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-start align-items-center">
                            <button class="button check" id="checkOutButton">Check Out</button>
                            <div class="ml-3">
                                <p class="mb-0">Check out after your visit</p>
                                <p class="text-muted small">Complete your visit and free up the seat for others.</p>
                            </div>
                        </div>
                    </div>
                </div>
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

    <div class="modal fade" tabindex="-1" role="dialog" id="verificationModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content p-2">
                <div class="modal-header">
                    <h5 class="modal-title">Enter Verification Code</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="verificationInstructions" class="mt-2 mb-4"></div>
                    <label for="verificationCode">Verification Code:</label>
                    <input type="text" class="form-control" id="verificationCode" placeholder="Enter your code">
                </div>
                <div class="modal-footer">
                    <button type="button" class="button check" data-dismiss="modal">Close</button>
                    <div id="verificationButtonContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkInButton = document.getElementById('checkInButton');
            const checkOutButton = document.getElementById('checkOutButton');
            const verificationModal = document.getElementById('verificationModal');
            const verificationInstructions = document.getElementById('verificationInstructions');
            const verificationButtonContainer = document.getElementById('verificationButtonContainer');

            function openVerificationModal(instructions, action) {
                $('#verificationModal').modal('show');
                verificationInstructions.innerHTML = instructions;

                // Dynamically create the verification button inside the modal body
                const verificationButton = document.createElement('button');
                verificationButton.className = 'button check';
                verificationButton.innerHTML = action === 'checkIn' ? 'Check In' : 'Check Out';
                verificationButton.addEventListener('click', function () {
                    const enteredCode = document.getElementById('verificationCode').value;
                    if (enteredCode.trim() !== "") {
                        // Call a function to verify the entered code
                        verifyCode(enteredCode, action);
                    }
                });

                // Clear and append the button to the container
                verificationButtonContainer.innerHTML = '';
                verificationButtonContainer.appendChild(verificationButton);
            }

            function closeVerificationModal() {
                $('#verificationModal').modal('hide');
            }

            checkInButton.addEventListener('click', function () {
                openVerificationModal("Enter the Check In code that was emailed to you when you created your booking.", 'checkIn');
            });

            checkOutButton.addEventListener('click', function () {
                openVerificationModal("To Check Out, you need to enter the Check In code that was emailed to you when you created your booking.", 'checkOut');
            });

            function verifyCode(enteredCode, action) {
                // AJAX request to the server to verify the code
                $.ajax({
                    type: 'POST',
                    url: 'verify_code.php', // Replace with the actual server-side script for verification
                    data: { code: enteredCode, action: action },
                    success: function (response) {
                        // The response from the server will indicate whether the code is valid
                        if (response === 'valid') {
                            // Update the reservation status based on the action (check-in or check-out)
                            updateReservationStatus(action, enteredCode);
                        } else if (response === 'invalid') {
                            // Display a SweetAlert notification for an invalid code
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Invalid verification code. Please try again.',
                                showConfirmButton: true
                            });
                        } else {
                            // Display a SweetAlert notification for other errors
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred during verification. Please try again.',
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred during verification. Please try again.',
                            showConfirmButton: true
                        });
                    }
                });
            }

            function updateReservationStatus(action, enteredCode) {
                // AJAX request to the server to update the reservation status
                $.ajax({
                    type: 'POST',
                    url: 'update_status.php', // Replace with the actual server-side script for status update
                    data: { action: action, code: enteredCode }, // Include the 'code' parameter
                    success: function (response) {
                        // Handle the response if needed
                        if (response === 'success') {

                            // Close the verification modal
                            $('#verificationModal').modal('hide');

                            // Display a SweetAlert notification
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: (action === 'checkIn') ? 'Check-in successful!' : 'Check-out successful!',
                                showConfirmButton: false,
                                timer: 2000 // Close after 2 seconds
                            });
                        } else if (response === 'invalid') {
                            // Display a SweetAlert notification for an invalid code
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Invalid verification code. Please try again.',
                                showConfirmButton: true
                            });
                        } else {
                            // Display a SweetAlert notification with an error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update reservation status. Please try again.',
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function () {
                        // Display a SweetAlert notification for general errors
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred during status update. Please try again.',
                            showConfirmButton: true
                        });
                    }
                });
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