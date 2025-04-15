<?php
session_start();
include '../conn/connection.php';

// Fetch user accounts from the database with pagination
function getUsers($conn, $page, $perPage)
{
    $start = ($page - 1) * $perPage;
    $sql = "SELECT * FROM `user profile` LIMIT $start, $perPage";
    $result = $conn->query($sql);

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    return $users;
}

// Get the total number of users for pagination
/**
 * Summary of getTotalUsers
 * @param mixed $conn
 * @return mixed
 */
function getTotalUsers($conn)
{
    $sql = "SELECT COUNT(*) as total FROM `user profile`";
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
$totalUsers = getTotalUsers($conn);
$totalPages = ceil($totalUsers / $perPage);

// Get the current page from the query string, default to 1
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$users = getUsers($conn, $page, $perPage);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/adminUserManagement.css">
    <link rel="stylesheet" href="../Styles/bootstrap.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                        <a href="../Admin/adminUserManagement.php" class="nav-link" id="nav-link-active">
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

    <section>
        <div class="container2">
            <h1>User Management</h1>
            <div class="d-flex justify-content-around">
                <div class="input-group me-5">
                    <input type="text" class="form-control form-control-sm px-3" placeholder="Search..."
                        id="searchInput" style="width: 500px;">
                    <button class="button add me-5" type="button" id="searchButton">Search</button>
                </div>
                <div class="button-group">
                    <button type="button" class="button add" data-bs-toggle="modal" data-bs-target="#addUserModal">Add
                        User</button>
                    <button type="button" class="button archive" data-bs-toggle="modal"
                        data-bs-target="#archiveUsersModal">Archive</button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Registration Date</th>
                        <th id="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <!-- View User Modal -->
                        <div class="modal fade" id="viewUserModal_<?php echo $user['user_id']; ?>" tabindex="-1"
                            aria-labelledby="viewUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content p-3">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <!-- Display user image or default avatar -->
                                        <?php $userImage = $user['user_image'] ? $user['user_image'] : '../images/default-avatar.png'; ?>
                                        <img src="<?php echo htmlspecialchars($userImage); ?>" alt="User Image"
                                            class="img-fluid rounded-circle mb-3"
                                            style="width: 150px; height: 150px; object-fit: cover;">

                                        <!-- Personal Information -->
                                        <div class="personal-info mt-3">
                                            <h6><strong>Personal Information</strong></h6>
                                            <p><strong>User ID:</strong>
                                                <?php echo htmlspecialchars($user['user_id']); ?>
                                            </p>
                                            <p><strong>Name:</strong>
                                                <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?>
                                            </p>
                                            <p><strong>Address:</strong>
                                                <?php echo htmlspecialchars($user['address']); ?>
                                            </p>
                                            <p><strong>Contact Number:</strong>
                                                <?php echo htmlspecialchars($user['cnumber']); ?>
                                            </p>
                                            <p><strong>Gender:</strong>
                                                <?php echo htmlspecialchars($user['gender']); ?>
                                            </p>
                                        </div>

                                        <!-- Account Information -->
                                        <div class="account-info mt-4">
                                            <h6><strong>Account Information</strong></h6>
                                            <p><strong>Username:</strong>
                                                <?php echo htmlspecialchars($user['username']); ?>
                                            </p>
                                            <p><strong>Email:</strong>
                                                <?php echo htmlspecialchars($user['email']); ?>
                                            </p>
                                            <p><strong>Registration Date:</strong>
                                                <?php echo date('F j, Y', strtotime($user['registration_date'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit User Modal -->
                        <div class="modal fade" id="editUserModal_<?php echo $user['user_id']; ?>" tabindex="-1"
                            aria-labelledby="editUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content p-3">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="../Admin/edit_user.php" method="POST" enctype="multipart/form-data"
                                            class="needs-validation" novalidate onsubmit="return validateEditUserForm()">
                                            <input type="hidden" name="userId" value="<?php echo $user['user_id']; ?>">
                                            <div class="text-center mb-3">
                                                <label for="editUserImage_<?php echo $user['user_id']; ?>"
                                                    class="cursor-pointer">
                                                    <?php $userImage = $user['user_image'] ? $user['user_image'] : '../images/default-avatar.png'; ?>
                                                    <img src="<?php echo $userImage; ?>" alt="User Image"
                                                        class="img-fluid rounded-circle mb-3"
                                                        style="width: 150px; height: 150px; object-fit: cover;"
                                                        id="editUserImageDisplay_<?php echo $user['user_id']; ?>">
                                                    <input type="file" class="form-control visually-hidden"
                                                        id="editUserImage_<?php echo $user['user_id']; ?>"
                                                        name="editUserImage" accept="image/*"
                                                        onchange="displaySelectedImage(this, '<?php echo $user['user_id']; ?>')">
                                                </label>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <h6>Personal Information</h6>
                                                    <div class="mb-3">
                                                        <label for="editFirstName" class="form-label">First Name</label>
                                                        <input type="text" class="form-control" id="editFirstName"
                                                            name="editFirstName" placeholder="Enter your first name"
                                                            value="<?php echo htmlspecialchars($user['firstname']); ?>"
                                                            required>
                                                        <div class="invalid-feedback">Please enter your first name.</div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="editLastName" class="form-label">Last Name</label>
                                                        <input type="text" class="form-control" id="editLastName"
                                                            name="editLastName" placeholder="Enter your last name"
                                                            value="<?php echo htmlspecialchars($user['lastname']); ?>"
                                                            required>
                                                        <div class="invalid-feedback">Please enter your last name.</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Contact Information</h6>
                                                    <div class="mb-3">
                                                        <label for="editAddress" class="form-label">Address</label>
                                                        <input type="text" class="form-control" id="editAddress"
                                                            name="editAddress" placeholder="Enter your address"
                                                            value="<?php echo htmlspecialchars($user['address']); ?>"
                                                            required>
                                                        <div class="invalid-feedback">Please enter your address.</div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="editContactNumber" class="form-label">Contact
                                                            Number</label>
                                                        <input type="tel" class="form-control" id="editContactNumber"
                                                            name="editContactNumber" placeholder="Enter your contact number"
                                                            value="<?php echo htmlspecialchars($user['cnumber']); ?>"
                                                            required>
                                                        <div class="invalid-feedback">Please enter your contact number.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <h6>User Account Information</h6>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="editUsername" class="form-label">Username</label>
                                                        <input type="text" class="form-control" id="editUsername"
                                                            name="editUsername" placeholder="Enter a username"
                                                            value="<?php echo htmlspecialchars($user['username']); ?>"
                                                            required>
                                                        <div class="invalid-feedback">Please enter a username.</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="editEmail" class="form-label">Email</label>
                                                        <input type="email" class="form-control" id="editEmail"
                                                            name="editEmail" placeholder="Enter a valid email address"
                                                            value="<?php echo htmlspecialchars($user['email']); ?>"
                                                            required>
                                                        <div class="invalid-feedback">Please enter a valid email address.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <h6>Gender Information</h6>
                                                    <div class="mb-3">
                                                        <label for="editGender" class="form-label">Gender</label>
                                                        <select class="form-select" id="editGender" name="editGender"
                                                            required>
                                                            <option value="" disabled selected>Select your gender</option>
                                                            <option value="male" <?php if ($user['gender'] === 'male')
                                                                echo 'selected'; ?>>Male</option>
                                                            <option value="female" <?php if ($user['gender'] === 'female')
                                                                echo 'selected'; ?>>Female</option>
                                                            <option value="other" <?php if ($user['gender'] === 'other')
                                                                echo 'selected'; ?>>Other</option>
                                                        </select>
                                                        <div class="invalid-feedback">Please select your gender.</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-save-changes mt-3">Save
                                                Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <?php echo $user['custom_id']; ?>
                            </td>
                            <td>
                                <?php echo $user['firstname']; ?>
                            </td>
                            <td>
                                <?php echo $user['lastname']; ?>
                            </td>
                            <td>
                                <?php echo $user['address']; ?>
                            </td>
                            <td>
                                <?php echo $user['cnumber']; ?>
                            </td>
                            <td>
                                <?php echo $user['username']; ?>
                            </td>
                            <td>
                                <?php echo $user['email']; ?>
                            </td>
                            <td>
                                <?php echo $user['gender']; ?>
                            </td>
                            <td>
                                <?php echo $user['registration_date']; ?>
                            </td>
                            <td id="actions">
                                <!-- View button -->
                                <a href="#" class="button view" data-bs-toggle="modal"
                                    data-bs-target="#viewUserModal_<?php echo $user['user_id']; ?>"
                                    data-user-id="<?php echo $user['user_id']; ?>">View</a>
                                <!-- Delete button -->
                                <a href="../Admin/func/delete_user.php?id=<?php echo $user['user_id']; ?>"
                                    class="button delete"
                                    onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
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

    <?php
    $archiveSql = "SELECT * FROM `archived_users`";
    $result = $conn->query($archiveSql); ?>

    <!-- Archive Users Modal -->
    <div class="modal fade" id="archiveUsersModal" tabindex="-1" aria-labelledby="archiveUsersModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="archiveUsersModalLabel">Archive Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php
                    if ($result->num_rows > 0) {
                        // Display table headers
                        echo '<table>';
                        echo '<tr><th>User ID</th><th>First Name</th><th>Last Name</th><th>Address</th><th>Contact Number</th><th>Username</th><th>Email</th><th>Gender</th><th>Registration Date</th><th>Actions</th></tr>';

                        // Display archived users
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['custom_id'] . '</td>';
                            echo '<td>' . $row['firstname'] . '</td>';
                            echo '<td>' . $row['lastname'] . '</td>';
                            echo '<td>' . $row['address'] . '</td>';
                            echo '<td>' . $row['cnumber'] . '</td>';
                            echo '<td>' . $row['username'] . '</td>';
                            echo '<td>' . $row['email'] . '</td>';
                            echo '<td>' . $row['gender'] . '</td>';
                            echo '<td>' . $row['registration_date'] . '</td>';
                            echo '<td><a href="../Admin/func/restore_user.php?user_id=' . $row['user_id'] . '" class="button restore" onclick="return confirm(\'Are you sure you want to restore this reservation?\')">Restore</a></td>';
                            echo '</tr>';
                        }

                        echo '</table>';
                    } else {
                        echo 'No archived users.';
                    }
                    ?>
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

    <!-- Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3">
                <div class="modal-header p-l">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../Admin/func/add_user.php" method="POST" class="needs-validation" novalidate
                        onsubmit="return validateAddUserForm()">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6>Personal Information</h6>
                                <div class="mb-3">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname"
                                        placeholder="Enter your first name" required>
                                    <div class="invalid-feedback">Please enter your first name.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="lastname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname"
                                        placeholder="Enter your last name" required>
                                    <div class="invalid-feedback">Please enter your last name.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Contact Information</h6>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        placeholder="Enter your address" required>
                                    <div class="invalid-feedback">Please enter your address.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="cnumber" class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" id="cnumber" name="cnumber"
                                        placeholder="Enter your contact number" required>
                                    <div class="invalid-feedback">Please enter your contact number.</div>
                                </div>
                            </div>
                        </div>
                        <h6>User Account Information</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Enter a username" required>
                                    <div class="invalid-feedback">Please enter a username.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Enter a valid email address" required>
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Enter a password" required>
                                        <div class="input-group-text">
                                            <i class="bi bi-eye" id="togglePassword"></i>
                                        </div>
                                        <div class="invalid-feedback">Please enter a password.</div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirmPassword"
                                            name="confirmPassword" placeholder="Confirm your password" required>
                                        <div class="input-group-text">
                                            <i class="bi bi-eye" id="toggleConfirmPassword"></i>
                                        </div>
                                        <div class="invalid-feedback">Please confirm your password.</div>
                                    </div>
                                </div>
                            </div>
                            <h6>Gender</h6>
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="" disabled selected>Select your gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                <div class="invalid-feedback">Please select your gender.</div>
                            </div>
                            <button type="submit" class="button view py-3">Add User</button>
                    </form>
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

    <script src="../Admin/script/adminUserManagement.js"></script>
</body>

</html>