<?php
include '../conn/connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize user inputs
    $userId = $_POST['userId']; // Assuming you have a hidden input in your form to store the user ID
    $editFirstName = htmlspecialchars($_POST['editFirstName']);
    $editLastName = htmlspecialchars($_POST['editLastName']);
    $editAddress = htmlspecialchars($_POST['editAddress']);
    $editContactNumber = htmlspecialchars($_POST['editContactNumber']);
    $editUsername = htmlspecialchars($_POST['editUsername']);
    $editEmail = htmlspecialchars($_POST['editEmail']);
    $editGender = $_POST['editGender'];

    // Handle user image upload
    $targetDir = "../images/profileupload/"; // Adjust the upload directory as needed
    $userImage = ''; // Variable to store the filename

    if (!empty($_FILES['editUserImage']['name'])) {
        $userImage = $targetDir . basename($_FILES['editUserImage']['name']);
        move_uploaded_file($_FILES['editUserImage']['tmp_name'], $userImage);
    }

    // Perform database update (adapt this to your database structure)
    // Use prepared statements to prevent SQL injection

    $query = "UPDATE `user profile` SET ";
    $params = array();
    $types = "";

    if (!empty($editFirstName)) {
        $query .= "firstname=?, ";
        $params[] = $editFirstName;
        $types .= "s";
    }

    if (!empty($editLastName)) {
        $query .= "lastname=?, ";
        $params[] = $editLastName;
        $types .= "s";
    }

    if (!empty($editAddress)) {
        $query .= "address=?, ";
        $params[] = $editAddress;
        $types .= "s";
    }

    if (!empty($editContactNumber)) {
        $query .= "cnumber=?, ";
        $params[] = $editContactNumber;
        $types .= "s";
    }

    if (!empty($editUsername)) {
        $query .= "username=?, ";
        $params[] = $editUsername;
        $types .= "s";
    }

    if (!empty($editEmail)) {
        $query .= "email=?, ";
        $params[] = $editEmail;
        $types .= "s";
    }

    if (!empty($editGender)) {
        $query .= "gender=?, ";
        $params[] = $editGender;
        $types .= "s";
    }

    if (!empty($userImage)) {
        $query .= "user_image=?, ";
        $params[] = $userImage;
        $types .= "s";
    }

    // Remove the trailing comma and space
    $query = rtrim($query, ", ");

    $query .= " WHERE user_id=?";
    $types .= "i";
    $params[] = $userId;

    // Prepare and bind the parameters
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        // Update successful
        header("Location: adminUserManagement.php");
        exit();
    } else {
        // Handle update failure
        echo "Error updating user: " . $stmt->error;
    }

    // Close your prepared statement
    $stmt->close();
}

mysqli_close($conn);
?>
