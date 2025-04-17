<?php
session_start();
include '../conn/connection.php';

$conn = OpenCon();
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

// Define the folder where profile pictures will be stored
$uploadDirectory = '../images/profileupload/';

?>

<?php
$select = mysqli_query($conn, "SELECT * FROM `user profile` WHERE user_id = '$user_id'") or die('query failed');
if (mysqli_num_rows($select) > 0) {
    $fetch = mysqli_fetch_assoc($select);
}
?>

<?php
if (isset($_POST['Save'])) {
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $cnumber = mysqli_real_escape_string($conn, $_POST['cnumber']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    // Handle profile picture upload
    if (isset($_FILES['user_image']) && !empty($_FILES['user_image']['name'])) {
        $imageFileType = strtolower(pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "jpeg", "png", "gif");

        // Check if the uploaded file is an image and its extension is allowed
        if (in_array($imageFileType, $allowedExtensions)) {
            $targetFilePath = $uploadDirectory . basename($_FILES["user_image"]["name"]);

            // Move the uploaded image to the target directory
            if (move_uploaded_file($_FILES["user_image"]["tmp_name"], $targetFilePath)) {
                // Update the 'user_image' column in the database with the new file path
                mysqli_query($conn, "UPDATE `user profile` SET user_image = '$targetFilePath' WHERE user_id = '$user_id'") or die('query failed');
                $message[] = 'Profile picture updated successfully!';
            } else {
                $message[] = 'Sorry, there was an error uploading your profile picture.';
            }
        } else {
            $message[] = 'Invalid file format. Please upload a valid image (JPG, JPEG, PNG, GIF).';
        }
    }

    // Update other user information in the database
    mysqli_query($conn, "UPDATE `user profile` SET firstname= '$firstname', lastname= '$lastname', email = '$email', cnumber= '$cnumber', address= '$address', username= '$username' WHERE user_id = '$user_id'") or die('query failed');

    $current_password = $fetch['password'];
    $update_password = mysqli_real_escape_string($conn, $_POST['update_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check if the entered current password matches the stored password
    if (!password_verify($update_password, $current_password)) {
        $message[] = 'Current password did not match!';
    } elseif ($new_password != $confirm_password) {
        $message[] = 'Confirm password did not match!';
    } else {
        // Update the password in the database
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE `user profile` SET password = '$hashed_password' WHERE user_id = '$user_id'")
            or die('query failed');
        $message[] = 'Password changed successfully!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/userProfile.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <title>Panlalawigang Aklatan ng Bulacan</title>
</head>

<body>
    <style>
        .message {
            margin-top: 10px;
            font-size: 14px;
            text-align: center;
            color: red;
        }
    </style>
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
            <a href="#home">My Account</a>
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
        <div class="container">
            <div class="content">
                <form action="../User/userProfile.php" method="post" enctype="multipart/form-data">
                    <div class="topic">Update Your <span>Account</span></div>
                    <?php
                    if (isset($message)) {
                        foreach ($message as $message) {
                            echo '<div class="message">' . $message . '</div>';
                        }
                    }
                    ?>
                    <div class="user-details">
                        <div class="input-box">
                            <label>First Name</label>
                            <input value="<?php echo $fetch['firstname']; ?>" type="text"
                                placeholder="Enter Your First Name" name="firstname" id="firstname" required>
                        </div>
                        <div class="input-box">
                            <label>Last Name</label>
                            <input value="<?php echo $fetch['lastname']; ?>" type="text"
                                placeholder="Enter Your Last Name" name="lastname" required>
                        </div>
                        <div class="input-box">
                            <label>Address</label>
                            <input value="<?php echo $fetch['address']; ?>" type="text" placeholder="Enter Your Address"
                                name="address" required>
                        </div>
                        <div class="input-box">
                            <label>Contact Number</label>
                            <input value="<?php echo $fetch['cnumber']; ?>" type="text"
                                placeholder="Enter Your Contact Number" name="cnumber" required>
                        </div>
                        <div class="input-box">
                            <label>Username</label>
                            <input value="<?php echo $fetch['username']; ?>" type="text"
                                placeholder="Enter Your Username" name="username" required>
                        </div>
                        <div class="input-box">
                            <label>Email</label>
                            <input value="<?php echo $fetch['email']; ?>" type="email" placeholder="Enter Your Email"
                                name="email" required>
                        </div>
                        <div class="input-box">
                            <input type="hidden" name="current_password" value="<?php echo $fetch['password']; ?>">
                            <label>Current Password</label>
                            <input type="password" placeholder="Enter Current Password" name="update_password"
                                class="password">
                            <i class="toggle-password bx bx-hide" id="toggleCurrentPasswordBtn"></i>
                        </div>
                        <div class="input-box">
                            <label>New Password</label>
                            <input type="password" placeholder="Enter Your New Password" name="new_password"
                                class="password" id="toggleNewPasswordBtn" required>
                            <i class="toggle-password bx bx-hide" id="toggleNewPasswordBtn"></i>
                        </div>
                        <div class="input-box">
                            <label>Confirm New Password</label>
                            <input type="password" placeholder="Confirm Your New Password" name="confirm_password"
                                class="password" id="toggleConfirmPasswordBtn" required>
                            <i class="toggle-password bx bx-hide" id="toggleConfirmPasswordBtn"></i>
                        </div>
                        <div class="input-box">
                            <label>Profile Picture</label>
                            <input type="file" name="user_image" accept="image/*">
                        </div>
                        <div class="input-box">
                            <label>Gender</label>
                            <div class="radio-buttons">
                                <label>
                                    <input value="<?php echo $fetch['gender']; ?>" type="radio" name="gender"
                                        value="male" required>
                                    Male
                                </label>
                                <label>
                                    <input value="<?php echo $fetch['gender']; ?>" type="radio" name="gender"
                                        value="female" required>
                                    Female
                                </label>
                                <label>
                                    <input value="<?php echo $fetch['gender']; ?>" type="radio" name="gender"
                                        value="other" required>
                                    Other
                                </label>
                            </div>
                        </div>
                        <div class="action-buttons">
                            <input type="button" class="activity-log" id="activity-log-button" value="Activity Log">
                            <input type="submit" class="save-button" name="Save" value="Save">
                            <input type="button" class="cancel-button" value="Cancel">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="container2">
            <div class="profile-picture">
                <?php
                $userImage = $fetch['user_image'];
                if (!empty($userImage)) {
                    echo '<img src="' . $userImage . '" alt="Profile Picture">';
                } else {
                    // Display default avatar if user image is empty
                    echo '<img src="../images/default-avatar.png" alt="Default Avatar">';
                }
                ?>
            </div>
            <div class="user-info">
                <h2 class="profile-name">
                    <?php echo $fetch['firstname']; ?>
                    <?php echo $fetch['lastname']; ?>
                </h2>
                <p class="id-number">ID:
                    <?php echo $fetch['custom_id']; ?>
                </p>
                <p class="contact-number">
                    <?php echo $fetch['cnumber']; ?>
                </p>
                <p class="email">
                    <?php echo $fetch['email']; ?>
                </p>
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
            <a href="../User/userContactUs.php">Contact Us</a>
        </div>
        <div class="social-icons">
            <a href="https://www.facebook.com/profile.php?id=100086755154439&mibextid=ZbWKwL" class="social-icon"><i
                    class="fab fa-facebook-f"></i></a>
        </div>
    </footer>

    <!-- The Modal -->
    <div class="modal" id="activity-log-modal">
        <div class="modal-content">
            <span class="close" id="close-activity-log">&times;</span>
            <div class="activity-log-content">
                <!-- Activity log content goes here -->
                <!-- Example: -->
                <p>No Activity</p>
                <!-- Add your activity log entries here -->
            </div>
        </div>
    </div>

    <script>
        // Function to toggle password visibility
        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bx-hide');
                toggleIcon.classList.add('bx-show');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bx-show');
                toggleIcon.classList.add('bx-hide');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const toggleCurrentPasswordBtn = document.getElementById('toggleCurrentPasswordBtn');
            const toggleNewPasswordBtn = document.getElementById('toggleNewPasswordBtn');
            const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPasswordBtn');

            toggleCurrentPasswordBtn.addEventListener('click', function () {
                togglePasswordVisibility('update_password', 'toggleCurrentPasswordBtn');
            });

            toggleNewPasswordBtn.addEventListener('click', function () {
                togglePasswordVisibility('new_password', 'toggleNewPasswordBtn');
            });

            toggleConfirmPasswordBtn.addEventListener('click', function () {
                togglePasswordVisibility('confirm_password', 'toggleConfirmPasswordBtn');
            });
        });
    </script>

    <script>
        // Get references to the button and modal
        const activityLogButton = document.getElementById('activity-log-button');
        const activityLogModal = document.getElementById('activity-log-modal');
        const closeActivityLogButton = document.getElementById('close-activity-log');

        // Function to open the modal
        function openActivityLogModal() {
            activityLogModal.style.display = 'block';
        }

        // Function to close the modal
        function closeActivityLogModal() {
            activityLogModal.style.display = 'none';
        }

        // Attach click event listeners
        activityLogButton.addEventListener('click', openActivityLogModal);
        closeActivityLogButton.addEventListener('click', closeActivityLogModal);
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