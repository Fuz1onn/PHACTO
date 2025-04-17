<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $address = $_POST["address"];
    $cnumber = $_POST["cnumber"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];
    $gender = $_POST["gender"];

    // Create a database connection
    $conn = new mysqli("localhost", "root", "", "phacto");

    // Check if the connection was successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the username or email is already registered
    $checkQuery = "SELECT * FROM registration WHERE username = '$username' OR email = '$email'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        echo "Username or email already exists. Please choose another.";
    } else {
        // Insert user data into the database
        $insertQuery = "INSERT INTO Registration (firstname, lastname, address, cnumber, username, email, password, cpassword, gender)
        VALUES('$firstname', '$lastname', '$address', '$cnumber', '$username', '$email', '$password', '$cpassword', '$gender')";

        if ($conn->query($insertQuery) === TRUE) {
            echo "Registration successful. You can now <a href='../User/userlogin.php'>login</a>.";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>PHACTO</title>
    <link rel="stylesheet" href="../Styles/registration.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>
  
  <section class="home" id="home">

    <div class="container">
      <div class="close-button">
        <button type="button" id="closeButton">&times;</button>
      </div>
      <div class="content">
      <form action="" method="post" enctype="multipart/form-data">
        <div class="topic">Create an <span>Account</span></div>
        <div class="user-details">
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
            <input type="password" placeholder="Enter Your Password" name="password" class="password" id="password" required>
            <i class="toggle-password bx bx-hide" id="togglePasswordBtn"></i>
          </div>
          <div class="input-box">
            <label>Confirm Password</label>
            <input type="password" placeholder="Confirm Your Password" name="cpassword" class="password" id="cpassword" required>
            <i class="toggle-password bx bx-hide" id="toggleConfirmPasswordBtn"></i>
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
        <div class="button">
          <input type="submit" name="continue" value="Continue">
        </div>
    
        <div class="Login">
          <span>Already have an account?</span><a href="../Processes/userLogin.php">Login</a>
        </div>
      </form>
    </div>
    </div>
    
</section>

<script src="../Processes/Script/registration.js"></script>

