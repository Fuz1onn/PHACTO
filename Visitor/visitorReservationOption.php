<?php
session_start();
include '../conn/connection.php';

function openDatabaseConnection()
{
    $conn = OpenCon();
    return $conn;
}

function loginUser($conn, $username, $password)
{
    // Hash the entered password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Query for user profile
    $sql_user = mysqli_query($conn, "SELECT * FROM `user profile` WHERE username='$username'");
    $row_user = mysqli_fetch_array($sql_user);

    // Query for librarian profile
    $sql_librarian = mysqli_query($conn, "SELECT * FROM `librarian profile` WHERE l_username ='$username'");
    $row_librarian = mysqli_fetch_array($sql_librarian);

    if (is_array($row_user) && password_verify($password, $row_user['password'])) {
        $_SESSION["username"] = $row_user['username'];
        $_SESSION["password"] = $row_user['password']; // Note: You might consider not storing the password in the session.
        $_SESSION['user_id'] = $row_user['user_id'];
        header("Location: ../User/userHomePage.php");
        exit();
    } else if (is_array($row_librarian) && $row_librarian['l_password']) {
        $_SESSION["username"] = $row_librarian['l_username'];
        $_SESSION["password"] = $row_librarian['l_password']; // Note: You might consider not storing the password in the session.
        $_SESSION['librarian_id'] = $row_librarian['librarian_id'];
        header("Location: ../Admin/adminDashboard.php");
        exit();
    } else {
        return 'Incorrect Username or Password!';
    }
}

function registerUser($conn, $firstname, $lastname, $address, $cnumber, $username, $email, $password, $cpassword, $gender)
{
    // Check for existing username or email
    $checkQuery = "SELECT * FROM `user profile` WHERE username = '$username' OR email = '$email'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        return "Username or email already exists. Please choose another.";
    } elseif ($password !== $cpassword) {
        return "Passwords do not match.";
    } else {
        // Passwords match, proceed with registration

        // Hash the password before storing it in the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Generate a unique custom ID
        $startNumber = 700;
        $currentYear = date("Y");

        // Fetch the maximum user ID in the current year
        $sqlMaxID = "SELECT MAX(SUBSTRING_INDEX(custom_id, '-', -1)) as maxID FROM `user profile` WHERE YEAR(NOW()) = ?";
        $stmtMaxID = $conn->prepare($sqlMaxID);
        $stmtMaxID->bind_param('s', $currentYear);
        $stmtMaxID->execute();
        $resultMaxID = $stmtMaxID->get_result();
        $rowMaxID = $resultMaxID->fetch_assoc();
        $maxID = $rowMaxID['maxID'];
        $stmtMaxID->close();

        // Calculate the new numeric part of the custom ID
        $newNumericPart = max($startNumber, $maxID + 1);

        // Calculate the new custom ID
        $newCustomID = $currentYear . "-" . sprintf("%03d", $newNumericPart);

        // Insert user details into the database
        $insertQuery = "INSERT INTO `user profile` (custom_id, firstname, lastname, address, cnumber, username, email, password, gender)
                    VALUES('$newCustomID', '$firstname', '$lastname', '$address', '$cnumber', '$username', '$email', '$hashedPassword', '$gender')";

        if ($conn->query($insertQuery) === TRUE) {
            return true; // Registration success
        } else {
            return "Error: " . $conn->error;
        }
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

$conn = OpenCon();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);
    $conn = openDatabaseConnection();

    if (isset($_POST['login'])) {
        $loginMessage = loginUser($conn, $username, $password);
        if ($loginMessage) {
            $message[] = $loginMessage;
        }
    } elseif (isset($_POST['register'])) {
        $registrationMessage = registerUser($conn, $firstname, $lastname, $address, $cnumber, $username, $email, $password, $cpassword, $gender);
        if ($registrationMessage === true) {
            $registrationSuccess = true;
            $openLoginModal = true; // Set a flag to indicate successful registration
        } else {
            $message[] = $registrationMessage;
        }
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/visitorMyReservationOption.css">
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
            <a href="../Visitor/visitorLandingPage.php">home</a>
            <a href="../Visitor/visitorAboutUs.php">about</a>
            <a href="../Visitor/visitorLandingPage.php#gallery">Gallery</a>
            <a href="../Visitor/visitorBookViewing.php">Catalog</a>
            <a href="#Option">Seat Reservation</a>
            <a href="../Visitor/visitorFeedback.php">Feedback</a>
        </div>
        <!-- Secondary Navigation for Login and Sign Up -->
        <div class="navbar2">
            <a href="#" onclick="openLoginModal()">Login</a>
            <span>|</span>
            <a href="#" onclick="openRegistrationModal()">Sign Up</a>
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
                        <a href="../Visitor/visitorLandingPage.html" class="nav-link">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="link">home</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Visitor/visitorLandingPage.html" class="nav-link">
                            <i class='bx bx-info-circle icon'></i>
                            <span class="link">about</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Visitor/visitorLandingPage.html" class="nav-link">
                            <i class='bx bx-photo-album icon'></i>
                            <span class="link">Gallery</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="#home" class="nav-link">
                            <i class='bx bx-book-content icon'></i>
                            <span class="link">Books</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="#home" class="nav-link">
                            <i class='bx bx-calendar-check icon'></i>
                            <span class="link">Reservation</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Visitor/visitorContactUs.html" class="nav-link">
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
                        <a href="../Processes/userLogin.html" class="nav-link">
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
                            <a href="visitorReservation.php" class="button2 check" id="reserveButton">Reserve</a>
                            <div class="ml-3">
                                <p class="mb-0">Make a new reservation</p>
                                <p class="text-muted small">Plan your visit and secure your seat in advance.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="d-flex justify-content-start align-items-center">
                            <button class="button2 check" id="checkInButton" onclick="openLoginModal()">Check
                                In</button>
                            <div class="ml-3">
                                <p class="mb-0">Check in for your seat</p>
                                <p class="text-muted small">Arrive on time and confirm your attendance.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-start align-items-center">
                            <button class="button2 check" id="checkOutButton" onclick="openLoginModal()">Check
                                Out</button>
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
            <a href="../Visitor/visitorPrivacyPolicy.php">Privacy Policy</a>
            <a href="../Visitor/visitorTermsConditions.php">Terms & Conditions</a>
            <a href="../Visitor/visitorContactUs.php">Contact Us</a>
        </div>
        <div class="social-icons">
            <a href="https://www.facebook.com/profile.php?id=100086755154439&mibextid=ZbWKwL" class="social-icon"><i
                    class="fab fa-facebook-f"></i></a>
        </div>
    </footer>

    <div class="modal" id="loginModal">
        <div class="login-container">
            <div class="close-button" onclick="closeLoginModal()">
                <button type="button" id="closeButton">&times;</button>
            </div>
            <div class="user-details">
                <h2>LOGIN</h2>
                <?php
                if (isset($message)) {
                    foreach ($message as $message) {
                        echo '<div class="message">' . $message . '</div>';
                    }
                }
                ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="input-box">
                        <input type="text" placeholder="Enter Your Username" id="username" name="username" required>
                    </div>
                    <div class="input-box">
                        <input type="password" placeholder="Enter Your Password" id="password" name="password" required>
                        <i class="toggle-password bx bx-hide"></i>
                    </div>
                    <div class="forgot-remember">
                        <div class="remember-me">
                            <input type="checkbox" id="rememberMe" name="rememberMe">
                            <label for="rememberMe">Remember me</label>
                        </div>
                        <div class="forgot-password">
                            <a href="#" onclick="openForgotPasswordModal()">Forgot password?</a>
                        </div>
                    </div>
                    <div class="button">
                        <input type="submit" name="login" value="Login">
                    </div>
                    <div class="line"></div>
                    <a href="#" class="social-button facebook">
                        <i class="fab fa-facebook-f"></i> Sign In with Facebook
                    </a>
                    <a href="#" class="social-button google">
                        <i class="fab fa-google"></i> Sign In with Google
                    </a>
                    <div class="register">
                        <span>Create an Account? <a href="#" onclick="openRegistrationModal()">Sign Up</a></span>
                    </div>
                </form>
            </div>
            <div class="line-between"></div>
            <div class="logo">
                <img src="../images/logo/logo.png" alt="Website Logo">
                <p>PHACTO</p>
            </div>
        </div>

    </div>

    <div class="modal" id="registrationModal">
        <div class="registration-container">
            <div class="close-button" onclick="closeRegistrationModal()">
                <button type="button" id="closeButton">&times;</button>
            </div>
            <div class="content">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="topic">Create an <span>Account</span></div>
                    <div class="user-details2">
                        <div class="input-box">
                            <label>First Name</label>
                            <input type="text" placeholder="Enter Your First Name" name="firstname" required>
                        </div>
                        <div class="input-box">
                            <label>Last Name</label>
                            <input type="text" placeholder="Enter Your Last Name" name="lastname" required>
                        </div>
                        <div class="input-box">
                            <label>Address</label>
                            <input type="text" placeholder="Enter Your Address" name="address" required>
                        </div>
                        <div class="input-box">
                            <label>Contact Number</label>
                            <input type="text" placeholder="Enter Your Contact Number" name="cnumber" required>
                        </div>
                        <div class="input-box">
                            <label>Username</label>
                            <input type="text" placeholder="Enter Your Username" name="username" required>
                        </div>
                        <div class="input-box">
                            <label>Email</label>
                            <input type="email" placeholder="Enter Your Email" name="email" required>
                        </div>
                        <div class="input-box">
                            <label>Password</label>
                            <input type="password" placeholder="Enter Your Password" name="password" class="password"
                                id="password2" required>
                            <i class="toggle-password2 bx bx-hide" id="togglePasswordBtn"></i>
                        </div>
                        <div class="input-box">
                            <label>Confirm Password</label>
                            <input type="password" placeholder="Confirm Your Password" name="cpassword" class="password"
                                id="cpassword" required>
                            <i class="toggle-password2 bx bx-hide" id="toggleConfirmPasswordBtn"></i>
                        </div>
                        <div class="input-box">
                            <label>Gender</label>
                            <div class="radio-buttons">
                                <label>
                                    <input type="radio" name="gender" value="male" required>
                                    Male
                                </label>
                                <label>
                                    <input type="radio" name="gender" value="female" required>
                                    Female
                                </label>
                                <label>
                                    <input type="radio" name="gender" value="other" required>
                                    Other
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="button2">
                        <input type="submit" name="continue" value="Continue">
                    </div>

                    <div class="Login">
                        <span>Already have an account?</span><a href="#" onclick="openLoginModal()">Login</a>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="modal" id="forgotPasswordModal">
        <div class="forgot-container">
            <div class="close-button" onclick="closeForgotPasswordModal()">
                <button type="button" id="closeButton">&times;</button>
            </div>
            <div class="details">
                <h2>Forgot <span>Password</span></h2>
                <p>You can reset your password here</p>
                <form action="" method="POST" enctype="multipart/form-data" class="form2">
                    <div class="input-box2">
                        <input type="email" placeholder="Enter Your Email" id="email" name="email" required>
                    </div>
                    <div class="button3">
                        <input type="submit" name="continue" value="Continue">
                    </div>
                    <div class="Login2">
                        <a href="#" onclick="openLoginModal()"><i class="fas fa-arrow-left"></i>Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        <?php if (isset($registrationSuccess) && $registrationSuccess === true) { ?>
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('loginModal').style.display = 'block';
            });
        <?php } ?>
    </script>

    <script>
        function openLoginModal() {
            closeRegistrationModal();
            closeForgotPasswordModal();
            document.getElementById("loginModal").style.display = "block";
        }

        function closeLoginModal() {
            document.getElementById("loginModal").style.display = "none";
        }

        function openRegistrationModal() {
            closeLoginModal();
            document.getElementById("registrationModal").style.display = "block";
        }

        function closeRegistrationModal() {
            document.getElementById("registrationModal").style.display = "none";
        }

        function openForgotPasswordModal() {
            closeLoginModal();
            document.getElementById("forgotPasswordModal").style.display = "block";
        }

        function closeForgotPasswordModal() {
            document.getElementById("forgotPasswordModal").style.display = "none";
        }

        window.onclick = function (event) {
            var loginModal = document.getElementById("loginModal");
            var registrationModal = document.getElementById("registrationModal");
            var forgotPasswordModal = document.getElementById("forgotPasswordModal");

            // Close the login modal if clicked outside
            if (event.target == loginModal) {
                loginModal.style.display = "none";
            }

            // Close the registration modal if clicked outside
            if (event.target == registrationModal) {
                registrationModal.style.display = "none";
            }

            // Close the forgot password modal if clicked outside
            if (event.target == forgotPasswordModal) {
                forgotPasswordModal.style.display = "none";
            }
        }
    </script>

    <script src="../Processes/Script/login.js"></script>
    <script src="../Processes/Script/registration.js"></script>

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