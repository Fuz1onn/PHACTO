<?php
session_start();
$user_id = $_SESSION['user_id'];
include '../conn/connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!isset($user_id)) {
    header('location: ../Visitor/visitorLandingPage.php');
}
;

if (isset($_GET['logout'])) {
    unset($user_id);
    session_destroy();
    header('location: ../Visitor/visitorLandingPage.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $reservationCountQuery = "SELECT COUNT(*) AS reservation_count FROM reservations WHERE user_id = '$user_id'";
    $reservationCountResult = mysqli_query($conn, $reservationCountQuery);
    $row = mysqli_fetch_assoc($reservationCountResult);
    $reservationCount = $row['reservation_count'];

    if ($reservationCount < 3) {

        $selectedSection = mysqli_real_escape_string($conn, trim($_POST['selectedSection']));
        $selectedDate = date('Y-m-d', strtotime($_POST['selectedDate']));
        $selectedStartTime = date('H:i:s', strtotime($_POST['selectedStartTime']));
        $selectedEndTime = date('H:i:s', strtotime($_POST['selectedEndTime']));
        $selectedSeatsArray = explode(',', $_POST['selectedSeats']);
        $selectedSeats = implode(',', array_map(function ($seat) use ($conn) {
            return mysqli_real_escape_string($conn, trim($seat));
        }, $selectedSeatsArray));

        if (!empty($selectedDate) && !empty($selectedStartTime) && !empty($selectedEndTime) && $selectedStartTime < $selectedEndTime) {
            // Generate a verification code
            function generateVerificationCode()
            {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $code = '';
                $length = 10; // You can adjust the length as needed

                for ($i = 0; $i < $length; $i++) {
                    $code .= $characters[rand(0, strlen($characters) - 1)];
                }

                return $code;
            }

            $verificationCode = generateVerificationCode();

            $sql = "INSERT INTO reservations (user_id, section, reservation_date, start_time, end_time, seat_number, reservation_code) VALUES ('$user_id', '$selectedSection', '$selectedDate', '$selectedStartTime', '$selectedEndTime', '$selectedSeats', '$verificationCode')";

            if (mysqli_query($conn, $sql)) {
                // Reservation was successful

                // Fetch the user's email from the database
                $getUserEmailQuery = "SELECT email FROM `user profile` WHERE user_id = '$user_id'";
                $userEmailResult = mysqli_query($conn, $getUserEmailQuery);

                if ($userEmailResult && mysqli_num_rows($userEmailResult) > 0) {
                    $userData = mysqli_fetch_assoc($userEmailResult);
                    $to = $userData['email'];

                    $reservationDetails = "Section: $selectedSection<br>
                      Date: $selectedDate<br>
                      Start Time: $selectedStartTime<br>
                      End Time: $selectedEndTime<br>
                      Seats: $selectedSeats<br>Verification Code: $verificationCode";

                    // Send reservation confirmation email
                    $subject = 'Reservation Confirmation';
                    $message = "<html>
                        <head>
                            <style>
                                body {
                                    font-family: 'Arial', sans-serif;
                                    background-color: #f4f4f4;
                                    color: #333;
                                }
                                .container {
                                    max-width: 600px;
                                    margin: 0 auto;
                                    padding: 20px;
                                    background-color: #fff;
                                    border-radius: 5px;
                                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                                }
                                h2 {
                                    color: #3498db;
                                }
                                p {
                                    line-height: 1.6;
                                }
                                strong {
                                    color: #e74c3c;
                                }
                            </style>
                        </head>
                        <body>
                            <div class='container'>
                                <h2>Reservation Confirmation</h2>
                                <p>Thank you for your reservation! Your reservation details:</p>
                                <p>$reservationDetails</p>
                            </div>
                        </body>
                    </html>";


                    // Initialize PHPMailer
                    require 'C:\xampp\htdocs\caps2\PHPMailer\src\Exception.php';
                    require 'C:\xampp\htdocs\caps2\PHPMailer\src\PHPMailer.php';
                    require 'C:\xampp\htdocs\caps2\PHPMailer\src\SMTP.php';

                    $mail = new PHPMailer(true);

                    try {
                        //Server settings
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'khalilacebuche17@gmail.com'; // Your Gmail email address
                        $mail->Password = 'qtdx gmwl tfmk rabg'; // Your Gmail App Password
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = 587;

                        //Recipients
                        $mail->setFrom('khalilacebuche17@gmail.com', 'Khalil');
                        $mail->addAddress($to);

                        //Content
                        $mail->isHTML(true);  // Set email format to HTML
                        $mail->Subject = $subject;
                        $mail->Body = $message;

                        $mail->send();

                        // Respond to the client
                        echo json_encode(array("success" => true));
                        exit;
                    } catch (Exception $e) {
                        // Error sending email
                        echo json_encode(array("success" => false, "message" => "Error sending email: {$mail->ErrorInfo}"));
                        exit;
                    }
                } else {
                    // User email not found in the database
                    echo json_encode(array("success" => false, "message" => "Error: User email not found"));
                    exit;
                }
            } else {
                // Reservation failed
                echo json_encode(array("success" => false, "message" => "Error: " . mysqli_error($conn)));
                exit;
            }
        } else {
            echo json_encode(array("success" => false, "message" => "Invalid date, start time, or end time.", "invalidDateTime" => true));
            exit;
        }
    } else {
        echo json_encode(array("success" => false, "message" => "You have reached the maximum reservation limit of 3.", "maxReservationReached" => true));
        exit;
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
    <link rel="stylesheet" href="../Styles/userReservation.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
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

    <div class="container-wrapper" id="home">
        <div class="container-large">
            <h3>Reservation Guidelines</h3>
            <p>
                <li>Seats can be reserved <strong>on the day of your visit or in the next 30 days</strong>, on a
                    first-come, first-served basis.</li>
                <li>Seats can be booked for a <strong>minimum of 1 hour to a maximum of 8 hours</strong>, within the
                    service hours of the Libraries.</li>
                <li>If you have not checked in <strong>15 minutes</strong> after your reservation time, your reservation
                    will automatically expire.</li>
                <li>If you finish early, check out to make way for the next available booking.</li>
            </p>
            <h3>Reservation Instructions</h3>
            <ul>
                <li>Choose a section.</li>
                <li>Select preferred date.</li>
                <li>Select your preferred start time and end time.</li>
                <li>Choose a seat. <strong>NOTE: Seat number must be followed.</strong>You may not change seats even if
                    it is vacant.</li>
                <li>Click <strong>'Continue.'</strong> Then confirm your reservation.</li>
                <li><strong>Maximum reservation limit of 3.</strong></li>
            </ul>
            <div class="title">
                <h2>Reserve your<span> seat</span></h2>
            </div>
            <div class="dropdown dropdown-room">
                <div class="dropdown-button" id="room-dropdown">
                    <span class="dropdown-icon"><i class="fas fa-caret-down"></i></span>
                    <span id="selected-section">Select a section</span>
                </div>
                <div class="dropdown-content" id="room-options">
                    <div class="dropdown-item">Tech4ed Center</div>
                    <div class="dropdown-item">Circulation Section</div>
                    <div class="dropdown-item">Reference Section</div>
                    <div class="dropdown-item">Bulacaniana Section</div>
                    <div class="dropdown-item">Filipiniana Section</div>
                </div>
            </div>
            <div class="dropdown-container">
                <div class="dropdown dropdown-date">
                    <div class="dropdown-button" id="date-dropdown">
                        <span class="dropdown-icon"><i class="fas fa-caret-down"></i></span>
                        <input type="text" id="date-picker" placeholder="Select a date">
                    </div>
                </div>
                <div class="dropdown dropdown-start-time">
                    <div class="dropdown-button" id="start-time-dropdown">
                        <span class="dropdown-icon"><i class="fas fa-caret-down"></i></span>
                        Select start time
                    </div>
                    <div class="dropdown-content" id="start-time-options">
                        <!-- Start time options will be added dynamically using JavaScript -->
                    </div>
                </div>
                <div class="dropdown dropdown-end-time">
                    <div class="dropdown-button" id="end-time-dropdown">
                        <span class="dropdown-icon"><i class="fas fa-caret-down"></i></span>
                        Select end time
                    </div>
                    <div class="dropdown-content" id="end-time-options">
                        <!-- End time options will be added dynamically using JavaScript -->
                    </div>
                </div>
            </div>
            <div class="dining-tables-container" id="dining-tables-container">
                <!-- Dining tables will be dynamically added here -->
            </div>
            <div class="availability-container">
                <div class="availability-guide">
                    <div class="availability-item">
                        <div class="availability-indicator available"></div>
                        <span>Available</span>
                    </div>
                    <div class="availability-item">
                        <div class="availability-indicator selected"></div>
                        <span>Your Booking</span>
                    </div>
                    <div class="availability-item">
                        <div class="availability-indicator taken"></div>
                        <span>Occupied</span>
                    </div>
                </div>
                <form id="reservation-form" action="" method="post">
                    <input type="hidden" name="selectedSection" id="selected-section-input">
                    <input type="hidden" name="selectedDate" id="selected-date-input">
                    <input type="hidden" name="selectedStartTime" id="selected-start-time-input">
                    <input type="hidden" name="selectedEndTime" id="selected-end-time-input">
                    <input type="hidden" name="selectedSeats" id="selected-seats-input">
                    <button type="submit" id="continue-button" class="continue-button">Continue</button>
                </form>
            </div>
        </div>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="section-header">Reservation<span> Details<span></h2>
            <p id="reservation-details"></p>
            <button id="confirm-reservation-btn">Confirm Reservation</button>
        </div>
    </div>

    <div id="notification" class="notification">
        <span id="notification-message"></span>
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

    <script>
        // Pass the PHP user ID to the JavaScript variable
        const userId = <?php echo json_encode($user_id, JSON_HEX_TAG); ?>;

    </script>

    <script src="../User/Script/userReservation.js"></script>

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