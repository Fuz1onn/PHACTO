<?php
session_start();
include '../conn/connection.php';

// Function to get the total number of members
function getTotalMembers($conn) {
    $sql = "SELECT COUNT(*) as total_members FROM `user profile`";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_members'];
}

// Function to get the total number of books
function getTotalBooks($conn) {
    $sql = "SELECT COUNT(*) as total_books FROM books";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_books'];
}

// Function to get the total number of new members (you'll need to define how 'new' is determined)
function getNewMembers($conn) {
    // Get the current date
    $currentDate = date('Y-m-d');

    // Count new members registered today where registration_date matches last_reset_date
    $sql = "SELECT COUNT(*) as new_members FROM `user profile` WHERE `registration_date` = '$currentDate' AND `last_reset_date` = '$currentDate'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['new_members'];
}

function getNewMembersWeekly($conn) {
    // Get the current date
    $currentDate = date('Y-m-d');

    // Calculate the first day of the week (Monday)
    $firstDayOfWeek = date('Y-m-d', strtotime('last Monday', strtotime($currentDate)));

    // Calculate the last day of the week (Sunday)
    $lastDayOfWeek = date('Y-m-d', strtotime('next Sunday', strtotime($currentDate)));

    // Count new members for the current week
    $sql = "SELECT COUNT(*) as new_members FROM `user profile` WHERE `registration_date` BETWEEN '$firstDayOfWeek' AND '$lastDayOfWeek'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row['new_members'];
}

function countLoggedInUsers($conn) {
    // Assuming you have a sessions table with fields like user_id, session_id, login_time, last_activity_time
    $sessionTimeout = 60 * 15; // 15 minutes timeout

    $currentTime = time();
    $activeUsersQuery = "SELECT COUNT(DISTINCT user_id) as logged_in_users FROM sessions WHERE last_activity_time > ($currentTime - $sessionTimeout)";
    $result = $conn->query($activeUsersQuery);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['logged_in_users'];
    }

    return 0;
}


// Function to get the total number of seats
function getTotalSeats($conn) {
    return 100;
}

// Function to get the total number of occupied seats
function getOccupiedSeats($conn) {
    $sql = "SELECT COUNT(*) as total_reservations FROM reservations";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_reservations'];
}

// Function to get the total number of available seats
function getAvailableSeats($conn) {
    // Calculate available seats as the difference between total seats and occupied seats
    return getTotalSeats($conn) - getOccupiedSeats($conn);
}

// Function to get the total number of reservations
function getTotalReservations($conn) {
    $sql = "SELECT COUNT(*) as total_reservations FROM reservations";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_reservations'];
}

// Function to get the total number of new users
function getNewUsers($conn) {
    $sql = "SELECT WEEK(registration_date) as week, COUNT(*) as new_users FROM `user profile` WHERE registration_date >= '2023-01-01' GROUP BY WEEK(registration_date)";
    $result = $conn->query($sql);

    $data = [];
    $labels = [];

    // Fetch data and labels from the database
    while($row = $result->fetch_assoc()) {
        $labels[] = 'Week '.$row['week'];
        $data[] = $row['new_users'];
    }

    return ['labels' => $labels, 'data' => $data];
}


// Function to get the total number of reservations per month
function getReservationsWeekly($conn) {
    $sql = "SELECT WEEK(reservation_date) as week, COUNT(*) as reservations FROM reservations GROUP BY WEEK(reservation_date)";
    $result = $conn->query($sql);

    $data = [];
    $labels = [];

    // Fetch data and labels from the database
    while($row = $result->fetch_assoc()) {
        $labels[] = 'Week '.$row['week'];
        $data[] = $row['reservations'];
    }

    return ['labels' => $labels, 'data' => $data];
}

// Function to get a minimum of 5 new books
function getNewBooks($conn) {
    $sql = "SELECT book_title FROM books WHERE publication_date >= '2023-01-01' LIMIT 5";
    $result = $conn->query($sql);

    $books = [];
    while($row = $result->fetch_assoc()) {
        $books[] = $row['book_title'];
    }

    return $books;
}

// Function to get a minimum of 5 new members
function getNewMembersList($conn) {
    $sql = "SELECT username FROM `user profile` WHERE registration_date >= '2023-01-01' LIMIT 5";
    $result = $conn->query($sql);

    $members = [];
    while($row = $result->fetch_assoc()) {
        $members[] = $row['username'];
    }

    return $members;
}

function updateAccountSettings($conn, $newUsername, $newPassword, $verifyPassword) {
    // Verify the current password before proceeding with updates
    $currentUsername = getCurrentUsername($conn);
    $sql = "SELECT l_password FROM `librarian profile` WHERE l_username = '$currentUsername'";
    $result = $conn->query($sql);

    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentPasswordHash = $row['l_password'];

        // Verify the entered password against the stored hash
        if(password_verify($verifyPassword, $currentPasswordHash)) {
            // Password verification successful, proceed with updates

            error_log("Password verification successful. Proceeding with updates.");

            // Sample code to update username in the database
            $updateUsernameQuery = "UPDATE `librarian profile` SET l_username = '$newUsername' WHERE librarian_id = 1";
            $conn->query($updateUsernameQuery);

            // Sample code to update password in the database
            if(!empty($newPassword)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updatePasswordQuery = "UPDATE `librarian profile` SET l_password = '$hashedPassword' WHERE librarian_id = 1";
                $conn->query($updatePasswordQuery);

                error_log("Password updated successfully.");
                $_SESSION['passwordChanged'] = true; // Set a session variable
            } else {
                error_log("No new password provided.");
            }

        } else {
            // Password verification failed, handle accordingly (e.g., display an error message)
            error_log("Password verification failed.");
            $_SESSION['passwordChanged'] = false; // Set a session variable to false
        }
    }
}

// Function to update website settings
function updateWebsiteSettings($conn, $newWebsiteName, $newWebsiteLogo) {
    // File upload handling
    $targetDirectory = "../images/uploadedLogo/"; // Change this to the directory where you want to store the uploaded logo

    // Delete all existing files in the directory
    $files = glob($targetDirectory.'*'); // Get all file names in the directory
    foreach($files as $file) {
        if(is_file($file)) {
            unlink($file); // Delete each file
        }
    }

    // Move the file to the target directory
    $targetFile = $targetDirectory.basename($_FILES["newWebsiteLogo"]["name"]);
    if(move_uploaded_file($_FILES["newWebsiteLogo"]["tmp_name"], $targetFile)) {
        // File upload successful

        // Debugging statements for file upload
        error_log("Move Uploaded File - Target File: ".$targetFile);

        if(file_exists($targetFile)) {
            error_log("File exists at target location");
        } else {
            error_log("File does not exist at target location");
        }

        // Check if a record already exists in the table
        $checkQuery = "SELECT * FROM website_settings WHERE setting_id = 1";
        $checkResult = $conn->query($checkQuery);

        if($checkResult && $checkResult->num_rows > 0) {
            // If a record exists, perform an update
            $updateQuery = "UPDATE website_settings SET website_name = ?, website_logo = ? WHERE setting_id = 1";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ss", $newWebsiteName, $targetFile);

            if($stmt->execute()) {
                // Update successful
                error_log("Update successful.");
                return true;
            } else {
                // Update failed
                error_log("Update failed: ".$stmt->error);
                return false;
            }
        } else {
            // If no record exists, perform an insert
            $insertQuery = "INSERT INTO website_settings (setting_id, website_name, website_logo) VALUES (1, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ss", $newWebsiteName, $targetFile);

            if($stmt->execute()) {
                // Insert successful
                error_log("Insert successful.");
                return true;
            } else {
                // Insert failed
                error_log("Insert failed: ".$stmt->error);
                return false;
            }
        }
    } else {
        // File upload failed
        error_log("File upload failed.");
        return false;
    }
}

function getWebsiteSettings($conn) {
    $sql = "SELECT * FROM website_settings WHERE setting_id = 1";
    $result = $conn->query($sql);

    if(!$result) {
        // Handle the error, log it, or display an error message
        die("Error fetching website settings: ".$conn->error);
    }

    if($result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}

function getCurrentUsername($conn) {
    $sql = "SELECT l_username FROM `librarian profile` WHERE librarian_id = 1";
    $result = $conn->query($sql);

    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['l_username'];
    }

    return null; // Return null if username is not found
}

// Function to handle SweetAlert display based on the result
function handleResult($result, $settingsType) {
    if($result) {
        $_SESSION['swal_message'] = [
            'type' => 'success',
            'title' => 'Settings Updated',
            'text' => $settingsType.' updated successfully!',
        ];
    } else {
        $_SESSION['swal_message'] = [
            'type' => 'error',
            'title' => 'Update Failed',
            'text' => 'Failed to update '.$settingsType.'. Please try again.',
        ];
    }
}

// Check if the form is submitted
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['accountSettingsSubmit'])) {
        // Handle account settings form submission
        $newUsername = $_POST['newUsername'];
        $newPassword = $_POST['newPassword'];
        $verifyPassword = $_POST['verifyPassword'];

        // Update account settings and get the result
        updateAccountSettings($conn, $newUsername, $newPassword, $verifyPassword);
    } elseif(isset($_POST['generalSettingsSubmit'])) {
        // Handle general settings form submission
        $newWebsiteName = $_POST['newWebsiteName'];
        // Process and store the uploaded logo file (you may need additional logic here)
        $newWebsiteLogo = $_FILES['newWebsiteLogo']['name'];

        // Update or insert website settings and get the result
        $updateResult = updateWebsiteSettings($conn, $newWebsiteName, $newWebsiteLogo);

        // Handle the result
        handleResult($updateResult, "Website settings");
    }

    // Redirect to refresh the data
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

$currentUsername = getCurrentUsername($conn);
$currentWebsiteSettings = getWebsiteSettings($conn);

// If data is fetched successfully, use it; otherwise, provide default values
if($currentWebsiteSettings) {
    $websiteName = $currentWebsiteSettings['website_name'];
    $websiteLogo = $currentWebsiteSettings['website_logo'];
} else {
    // Default values (adjust as needed)
    $websiteName = "Default Website Name";
    $websiteLogo = "default_logo.png";
}

// Call the function
$loggedInUsersCount = countLoggedInUsers($conn);
$totalSeats = getTotalSeats($conn);
$occupiedSeats = getOccupiedSeats($conn);
$availableSeats = getAvailableSeats($conn);
$totalReservations = getTotalReservations($conn);
$newUsersCount = getNewUsers($conn);
$reservationsWeeklyData = getReservationsWeekly($conn);
$newBooks = getNewBooks($conn);
$newMembersList = getNewMembersList($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/adminDashboard.css">
    <link rel="stylesheet" href="../Styles/bootstrap.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <title>Panlalawigang Aklatan ng Bulacan</title>
</head>

<body>

    <section>
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
                        <a href="#home" class="nav-link" id="nav-link-active">
                            <i class='bx bxs-dashboard icon'></i>
                            <span class="link">Dashboard</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Admin/adminBooksPage.php" class="nav-link">
                            <i class='bx bxs-book-content icon'></i>
                            <span class="link">Catalog</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Admin/adminUserManagement.php" class="nav-link">
                            <i class='bx bxs-user icon'></i>
                            <span class="link">User Management</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Admin/adminReservation.php" class="nav-link">
                            <i class='bx bx-calendar-check icon'></i>
                            <span class="link">Reservations</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Admin/adminFeedback.php" class="nav-link">
                            <i class='bx bx-message-rounded-dots icon'></i>
                            <span class="link">Feedback</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Admin/adminContactUs.php" class="nav-link">
                            <i class='bx bx-message-alt-detail icon'></i>
                            <span class="link">Contact Us</span>
                        </a>
                    </li>
                </ul>
                <div class="bottom-content">
                    <li class="list">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#librarianSettingsModal" class="nav-link">
                            <i class='bx bx-cog icon'></i>
                            <span class="link">Settings</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="../Visitor/visitorLandingPage.php" class="nav-link">
                            <i class='bx bx-log-out icon'></i>
                            <span class="link">Logout</span>
                        </a>
                    </li>
                </div>
            </div>
        </div>
    </section>

    <section class="overview-section">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users icon"></i>Total Users</h5>
                        <h3 class="card-text">
                            <?php echo getTotalMembers($conn); ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-book icon"></i>Total Catalogs</h5>
                        <h3 class="card-text">
                            <?php echo getTotalBooks($conn); ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title"><i class="fas fa-user-plus icon"></i>New Users</h5>
                            <form method="get">
                                <select id="timeframe" name="timeframe" onchange="this.form.submit()">
                                    <option value="today" <?php echo (isset($_GET['timeframe']) && $_GET['timeframe'] === 'today') ? 'selected' : ''; ?>>Today</option>
                                    <option value="weekly" <?php echo (isset($_GET['timeframe']) && $_GET['timeframe'] === 'weekly') ? 'selected' : ''; ?>>Weekly</option>
                                </select>
                            </form>
                        </div>
                        <h5 class="card-text" style="margin-bottom: 2px;">
                        <?php
                        $selectedTimeframe = isset($_GET['timeframe']) ? $_GET['timeframe'] : 'today';
                        if($selectedTimeframe === 'today') {
                            $count = getNewMembers($conn);
                            echo "<p class='card-text'>+{$count} new users today</p>";
                        } elseif($selectedTimeframe === 'weekly') {
                            $count = getNewMembersWeekly($conn);
                            echo "<p class='card-text'>+{$count} new users this week</p>";
                        } else {
                            echo "<p class='card-text'>Invalid timeframe</p>";
                        }
                        ?>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-chair icon"></i>Total Seats</h5>
                        <h3 class="card-text">
                            <?php echo getTotalSeats($conn); ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-user icon"></i>Occupied Seats</h5>
                        <h3 class="card-text">
                            <?php echo getOccupiedSeats($conn); ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-chair icon"></i>Available Seats</h5>
                        <h3 class="card-text">
                            <?php echo getAvailableSeats($conn); ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-bookmark icon"></i>Total Reservations</h5>
                        <h3 class="card-text">
                            <?php echo getTotalReservations($conn); ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card" id="newUserCard">
                    <div class="card-body" style="position: relative;">
                        <div style="position: absolute; top: 18px; right: 10px;">
                            <a href="generate_user_report.php" class="button new">Generate Report</a>
                        </div>
                        <canvas id="newUserChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card" id="reservationCard">
                    <div class="card-body" style="position: relative;">
                        <div style="position: absolute; top: 18px; right: 10px;">
                            <a href="generate_reservation_report.php" class="button new">Generate Report</a>
                        </div>
                        <canvas id="reservationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card" id="newBooks">
                    <div class="card-body">
                        <h5 class="card-title">Recently Added Materials</h5>
                        <ul class="list-group">
                            <?php foreach($newBooks as $book): ?>
                                <li class="list-group-item"><i class="bx bx-book icon"></i>
                                    <?php echo $book; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card" id="newMembers">
                    <div class="card-body">
                        <h5 class="card-title">New Users</h5>
                        <ul class="list-group">
                            <?php foreach($newMembersList as $member): ?>
                                <li class="list-group-item"><i class="bx bx-user icon"></i>
                                    <?php echo $member; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal for Librarian Settings -->
    <div class="modal fade" id="librarianSettingsModal" tabindex="-1" role="dialog"
        aria-labelledby="librarianSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="librarianSettingsModalLabel">Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="librarianSettingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general" role="tab"
                                aria-controls="general" aria-selected="true">General</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="account-tab" data-bs-toggle="tab" href="#account" role="tab"
                                aria-controls="account" aria-selected="false">Account</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content mt-3">
                        <!-- General Tab Content -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel"
                            aria-labelledby="general-tab">
                            <form id="generalSettingsForm" method="POST" enctype="multipart/form-data">
                                <!-- Display current website name -->
                                <div class="mb-3">
                                    <label for="currentWebsiteName" class="form-label">Current Website Name</label>
                                    <input type="text" class="form-control" id="currentWebsiteName"
                                        name="currentWebsiteName"
                                        value="<?php echo $currentWebsiteSettings['website_name']; ?>" disabled>
                                </div>

                                <!-- Display current website logo -->
                                <div class="mb-3">
                                    <label for="currentWebsiteLogo" class="form-label">Current Website Logo</label>
                                    <img src="<?php echo $currentWebsiteSettings['website_logo']; ?>"
                                        alt="Current Website Logo" class="img-thumbnail"
                                        style="max-width: 100px; max-height: 100px;">
                                </div>

                                <!-- Allow users to change website name -->
                                <div class="mb-3">
                                    <label for="newWebsiteName" class="form-label">New Website Name</label>
                                    <input type="text" class="form-control" id="newWebsiteName" name="newWebsiteName"
                                        placeholder="Enter new website name">
                                </div>

                                <!-- Allow users to upload a new website logo -->
                                <div class="mb-3">
                                    <label for="newWebsiteLogo" class="form-label">New Website Logo</label>
                                    <input type="file" class="form-control" id="newWebsiteLogo" name="newWebsiteLogo">
                                </div>

                                <button type="submit" name="generalSettingsSubmit" class="button save">Save
                                    Changes</button>
                            </form>
                        </div>

                        <!-- Account Tab Content -->
                        <div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="account-tab">
                            <form id="accountSettingsForm" method="POST">
                                <!-- Display current username -->
                                <div class="mb-3">
                                    <label for="currentUsername" class="form-label">Current Username</label>
                                    <input type="text" class="form-control" id="currentUsername" name="currentUsername"
                                        value="<?php echo $currentUsername; ?>" disabled>
                                </div>

                                <!-- Allow users to change username -->
                                <div class="mb-3">
                                    <label for="newUsername" class="form-label">New Username</label>
                                    <input type="text" class="form-control" id="newUsername" name="newUsername"
                                        placeholder="Enter new username">
                                </div>

                                <!-- Password verification -->
                                <div class="mb-3">
                                    <label for="verifyPassword" class="form-label">Verify Current Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="verifyPassword"
                                            name="verifyPassword" placeholder="Enter current password">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="toggleVerifyPassword">
                                            <i class="fas fa-eye" id="verify-eye-icon" style="font-size: 1rem;"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Allow users to change password -->
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label">New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="newPassword" name="newPassword"
                                            placeholder="Enter new password">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                            <i class="fas fa-eye" id="eye-icon" style="font-size: 1rem;"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" name="accountSettingsSubmit" class="button save">Save
                                    Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Check for the session variable and display the popup if it is set
    if(isset($_SESSION['passwordChanged'])) {
        if($_SESSION['passwordChanged']) {
            echo '<script>
            Swal.fire({
                icon: "success",
                title: "Password Changed",
                text: "Password changed successfully!",
            });
        </script>';
        } else {
            echo '<script>
            Swal.fire({
                icon: "error",
                title: "Password Change Failed",
                text: "Password change failed. Please try again.",
            });
        </script>';
        }
        unset($_SESSION['passwordChanged']); // Clear the session variable
    } else if(isset($_SESSION['swal_message'])) {
        $swalMessage = $_SESSION['swal_message'];
        echo '<script>
            Swal.fire({
                icon: "'.$swalMessage['type'].'",
                title: "'.$swalMessage['title'].'",
                text: "'.$swalMessage['text'].'",
            });
        </script>';

        // Clear the session variable to avoid displaying the same message on subsequent page loads
        unset($_SESSION['swal_message']);
    }
    ?>

    <script>
        document.getElementById('toggleVerifyPassword').addEventListener('click', function () {
            togglePasswordVisibility('verifyPassword', 'verify-eye-icon');
        });

        document.getElementById('toggleNewPassword').addEventListener('click', function () {
            togglePasswordVisibility('newPassword', 'new-eye-icon');
        });

        function togglePasswordVisibility(passwordFieldId, eyeIconId) {
            var passwordField = document.getElementById(passwordFieldId);
            var eyeIcon = document.getElementById(eyeIconId);

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.className = 'fas fa-eye-slash';
            } else {
                passwordField.type = 'password';
                eyeIcon.className = 'fas fa-eye';
            }
        }
    </script>

    <script>
        var newUserCtx = document.getElementById('newUserChart').getContext('2d');
        var newUserChart = new Chart(newUserCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($newUsersCount['labels']); ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?php echo json_encode($newUsersCount['data']); ?>,
                    backgroundColor: '#007bff',
                    borderWidth: 2,
                    barThickness: 60, // Adjust as needed
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Time Period' // Your X-axis label
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Users' // Your Y-axis label
                        },
                        suggestedMin: 0,
                        suggestedMax: 10,
                        beginAtZero: true
                    }
                }
            }
        });

        // Sample data for Chart.js (reservations per month)
        var reservationMonthlyCtx = document.getElementById('reservationChart').getContext('2d');
        var reservationMonthlyChart = new Chart(reservationMonthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($reservationsWeeklyData['labels']); ?>,
                datasets: [{
                    label: 'Reservations',
                    data: <?php echo json_encode($reservationsWeeklyData['data']); ?>,
                    borderColor: '#007bff',
                    borderWidth: 2,
                    pointBackgroundColor: '#007bff',
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: {
                        target: 'origin',
                        above: 'rgba(75, 192, 192, 0.4)'
                    },
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Time Period' // Your X-axis label
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Reservations' // Your Y-axis label
                        },
                        suggestedMin: 0,
                        suggestedMax: 10,
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>