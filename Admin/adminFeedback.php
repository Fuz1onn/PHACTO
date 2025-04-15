<?php
session_start();
include '../conn/connection.php';

// Function to get feedbacks with pagination
function getFeedbacks($conn, $page, $perPage)
{
    $start = ($page - 1) * $perPage;
    $sql = "SELECT * FROM feedback LIMIT $start, $perPage";
    $result = $conn->query($sql);

    $feedbacks = [];
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }

    return $feedbacks;
}

function getArchivedFeedback($conn)
{
    $sql = "SELECT * FROM archive_feedback";
    $result = $conn->query($sql);

    $archivedFeedbacks = [];
    while ($row = $result->fetch_assoc()) {
        $archivedFeedbacks[] = $row;
    }

    return $archivedFeedbacks;
}

// Function to get the total number of feedbacks for pagination
function getTotalFeedbacks($conn)
{
    $sql = "SELECT COUNT(*) as total FROM feedback";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row['total'];
}

function updateAccountSettings($conn, $newUsername, $newPassword, $verifyPassword)
{
    // Verify the current password before proceeding with updates
    $currentUsername = getCurrentUsername($conn);
    $sql = "SELECT l_password FROM `librarian profile` WHERE l_username = '$currentUsername'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentPasswordHash = $row['l_password'];

        // Verify the entered password against the stored hash
        if (password_verify($verifyPassword, $currentPasswordHash)) {
            // Password verification successful, proceed with updates

            error_log("Password verification successful. Proceeding with updates.");

            // Sample code to update username in the database
            $updateUsernameQuery = "UPDATE `librarian profile` SET l_username = '$newUsername' WHERE librarian_id = 1";
            $conn->query($updateUsernameQuery);

            // Sample code to update password in the database
            if (!empty($newPassword)) {
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
function updateWebsiteSettings($conn, $newWebsiteName, $newWebsiteLogo)
{
    // File upload handling
    $targetDirectory = "../images/uploadedLogo/"; // Change this to the directory where you want to store the uploaded logo

    // Delete all existing files in the directory
    $files = glob($targetDirectory . '*'); // Get all file names in the directory
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file); // Delete each file
        }
    }

    // Move the file to the target directory
    $targetFile = $targetDirectory . basename($_FILES["newWebsiteLogo"]["name"]);
    if (move_uploaded_file($_FILES["newWebsiteLogo"]["tmp_name"], $targetFile)) {
        // File upload successful

        // Debugging statements for file upload
        error_log("Move Uploaded File - Target File: " . $targetFile);

        if (file_exists($targetFile)) {
            error_log("File exists at target location");
        } else {
            error_log("File does not exist at target location");
        }

        // Check if a record already exists in the table
        $checkQuery = "SELECT * FROM website_settings WHERE setting_id = 1";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult && $checkResult->num_rows > 0) {
            // If a record exists, perform an update
            $updateQuery = "UPDATE website_settings SET website_name = ?, website_logo = ? WHERE setting_id = 1";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ss", $newWebsiteName, $targetFile);

            if ($stmt->execute()) {
                // Update successful
                error_log("Update successful.");
                return true;
            } else {
                // Update failed
                error_log("Update failed: " . $stmt->error);
                return false;
            }
        } else {
            // If no record exists, perform an insert
            $insertQuery = "INSERT INTO website_settings (setting_id, website_name, website_logo) VALUES (1, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ss", $newWebsiteName, $targetFile);

            if ($stmt->execute()) {
                // Insert successful
                error_log("Insert successful.");
                return true;
            } else {
                // Insert failed
                error_log("Insert failed: " . $stmt->error);
                return false;
            }
        }
    } else {
        // File upload failed
        error_log("File upload failed.");
        return false;
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

function getCurrentUsername($conn)
{
    $sql = "SELECT l_username FROM `librarian profile` WHERE librarian_id = 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['l_username'];
    }

    return null; // Return null if username is not found
}

// Function to handle SweetAlert display based on the result
function handleResult($result, $settingsType)
{
    if ($result) {
        $_SESSION['swal_message'] = [
            'type' => 'success',
            'title' => 'Settings Updated',
            'text' => $settingsType . ' updated successfully!',
        ];
    } else {
        $_SESSION['swal_message'] = [
            'type' => 'error',
            'title' => 'Update Failed',
            'text' => 'Failed to update ' . $settingsType . '. Please try again.',
        ];
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accountSettingsSubmit'])) {
        // Handle account settings form submission
        $newUsername = $_POST['newUsername'];
        $newPassword = $_POST['newPassword'];
        $verifyPassword = $_POST['verifyPassword'];

        // Update account settings and get the result
        updateAccountSettings($conn, $newUsername, $newPassword, $verifyPassword);
    } elseif (isset($_POST['generalSettingsSubmit'])) {
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
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$currentUsername = getCurrentUsername($conn);
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

$perPage = 10;
$totalFeedbacks = getTotalFeedbacks($conn);
$totalPages = ceil($totalFeedbacks / $perPage);

// Get the current page from the query string, default to 1
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$feedbacks = getFeedbacks($conn, $page, $perPage);
$archivedFeedbacks = getArchivedFeedback($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/adminUserManagement.css">
    <link rel="stylesheet" href="../Styles/bootstrap.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                        <a href="../Admin/adminDashboard.php" class="nav-link">
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
                        <a href="../Admin/adminFeedback.php" class="nav-link" id="nav-link-active">
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

    <section>
        <div class="container2">
            <h1>User Feedbacks</h1>
            <div class="d-flex justify-content-around">
                <div class="input-group me-5">
                    <input type="text" class="form-control form-control-sm px-3" placeholder="Search..."
                        id="searchInput" style="width: 500px;">
                    <button class="button add me-5" type="button" id="searchButton">Search</button>
                </div>
                <a href="#" class="button archive" onclick="showArchivedFeedback()">
                    Archive
                </a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Feedback ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Comment</th>
                        <th>Rate</th>
                        <th>Date Posted</th>
                        <th id="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($feedbacks as $feedback): ?>
                        <div class="modal fade" id="viewFeedbackModal_<?php echo $feedback['feed_id']; ?>" tabindex="-1"
                            role="dialog" aria-labelledby="viewFeedbackModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewFeedbackModalLabel">View Feedback</h5>
                                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Feedback ID:</strong>
                                            <?php echo $feedback['feed_id']; ?>
                                        </p>
                                        <p><strong>Name:</strong>
                                            <?php echo $feedback['feed_name']; ?>
                                        </p>
                                        <p><strong>Email:</strong>
                                            <?php echo $feedback['feed_email']; ?>
                                        </p>
                                        <p><strong>Comment:</strong>
                                            <?php echo $feedback['feed_comment']; ?>
                                        </p>
                                        <p><strong>Rate:</strong>
                                            <?php echo $feedback['feed_rate']; ?>
                                        </p>
                                        <p><strong>Date Posted:</strong>
                                            <?php echo $feedback['feed_date']; ?>
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="button view" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <tr>
                            <td>
                                <?php echo $feedback['feed_id']; ?>
                            </td>
                            <td>
                                <?php echo $feedback['feed_name']; ?>
                            </td>
                            <td>
                                <?php echo $feedback['feed_email']; ?>
                            </td>
                            <td>
                                <?php echo $feedback['feed_comment']; ?>
                            </td>
                            <td>
                                <?php echo $feedback['feed_rate']; ?>
                            </td>
                            <td>
                                <?php echo $feedback['feed_date']; ?>
                            </td>
                            <td id="actions">
                                <!-- View button -->
                                <a href="#" class="button view" data-toggle="modal"
                                    data-target="#viewFeedbackModal_<?php echo $feedback['feed_id']; ?>">
                                    View
                                </a>
                                <!-- Delete button -->
                                <a href="../Admin/func/delete_feedback.php?id=<?php echo $feedback['feed_id']; ?>"
                                    class="button delete"
                                    onclick="return confirm('Are you sure you want to delete this feedback?')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" <?php if ($i === $page)
                           echo 'class="active"'; ?>>
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <div class="modal fade" id="archivedFeedbackModal" tabindex="-1" aria-labelledby="archivedFeedbackModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archivedFeedbackModalLabel">Archived Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (empty($archivedFeedbacks)): ?>
                        <p>No data available in the archived Feedback.</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Feedback ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Comment</th>
                                    <th>Rate</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($archivedFeedbacks as $archivedFeedback): ?>
                                    <tr>
                                        <td>
                                            <?php echo $archivedFeedback['feed_id']; ?>
                                        </td>
                                        <td>
                                            <?php echo $archivedFeedback['feed_name']; ?>
                                        </td>
                                        <td>
                                            <?php echo $archivedFeedback['feed_email']; ?>
                                        </td>
                                        <td>
                                            <?php echo $archivedFeedback['feed_comment']; ?>
                                        </td>
                                        <td>
                                            <?php echo $archivedFeedback['feed_rate']; ?>
                                        </td>
                                        <td>
                                            <?php echo $archivedFeedback['feed_date']; ?>
                                        </td>
                                        <td>
                                            <a href="../Admin/func/restore_feedback.php?id=<?php echo $archivedFeedback['feed_id']; ?>"
                                                class="button restore"
                                                onclick="return confirm('Are you sure you want to restore this feedback?')">Restore</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button view" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

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
    if (isset($_SESSION['passwordChanged'])) {
        if ($_SESSION['passwordChanged']) {
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
    } else if (isset($_SESSION['swal_message'])) {
        $swalMessage = $_SESSION['swal_message'];
        echo '<script>
            Swal.fire({
                icon: "' . $swalMessage['type'] . '",
                title: "' . $swalMessage['title'] . '",
                text: "' . $swalMessage['text'] . '",
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
        function showArchivedFeedback() {
            $('#archivedFeedbackModal').modal('show');
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Function to perform search
            function searchTable() {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById("searchInput");
                filter = input.value.toUpperCase();
                table = document.querySelector("table");
                tr = table.getElementsByTagName("tr");

                var noResultsRow = document.getElementById("noResultsRow");

                // Remove the "No results" row if it exists
                if (noResultsRow) {
                    noResultsRow.remove();
                }

                var resultsFound = false; // Flag to check if any matching rows are found

                for (i = 0; i < tr.length; i++) {
                    var found = false; // Flag to check if at least one field matches the search criteria

                    // Skip the header row
                    if (i === 0) {
                        continue;
                    }

                    // Check each column in a row for a match
                    for (var j = 0; j < tr[i].cells.length; j++) {
                        td = tr[i].cells[j];
                        if (td) {
                            txtValue = td.textContent || td.innerText;

                            // Check if the current column contains the search criteria
                            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                found = true;
                                break; // Exit the loop if a match is found in any column
                            }
                        }
                    }

                    // Show or hide the row based on the search result
                    if (found) {
                        tr[i].style.display = "";
                        resultsFound = true;
                    } else {
                        tr[i].style.display = "none";
                    }
                }

                // Display "No results" message if no matching rows are found
                if (!resultsFound) {
                    var noResultsRow = table.insertRow(-1);
                    var noResultsCell = noResultsRow.insertCell(0);
                    noResultsCell.colSpan = tr[0].cells.length; // Span the cell across all columns
                    noResultsCell.innerHTML = "No results";
                    noResultsRow.id = "noResultsRow";
                    noResultsCell.style.textAlign = "center"; // Center the text
                    noResultsCell.style.fontWeight = "bold"; // Make the text bold
                    noResultsCell.style.color = "gray"; // Set the text color to red
                }
            }

            // Attach the search function to both button click and Enter key press
            document.getElementById("searchButton").addEventListener("click", function () {
                searchTable();
            });

            document.getElementById("searchInput").addEventListener("keyup", function (event) {
                if (event.key === "Enter") {
                    searchTable();
                }
            });
        });
    </script>


</body>

</html>