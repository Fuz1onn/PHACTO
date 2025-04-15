<?php
include '../../conn/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user data from the form
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $cnumber = $_POST['cnumber'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $gender = $_POST['gender'];

    // Generate a unique user ID starting from "2023-700"
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

    // Calculate the new numeric part of the user ID
    $newNumericPart = max($startNumber, $maxID + 1);

    // Calculate the new user ID
    $newUserID = $currentYear . "-" . sprintf("%03d", $newNumericPart);

    // Prepare and execute the SQL query to insert the user into the database
    $sql = "INSERT INTO `user profile` (custom_id, firstname, lastname, address, cnumber, username, email, password, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssssss', $newUserID, $firstname, $lastname, $address, $cnumber, $username, $email, $password, $gender);

    if ($stmt->execute()) {
        // User added successfully
        header('Location: ../adminUserManagement.php'); // Redirect to a success page
        exit();
    } else {
        // Error occurred while adding the user
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>