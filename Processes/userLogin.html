<?php

session_start();
include '../config.php';
if (isset($_POST['login'])) {
    extract($_POST);

    $conn = OpenCon();
    $sql_user = mysqli_query($conn, "SELECT * FROM `user profile` where username='$username' and password='$password'");
    $row_user = mysqli_fetch_array($sql_user);

    $sql_librarian = mysqli_query($conn, "SELECT * FROM `librarian profile` where l_username ='$username' and l_password='$password'");
    $row_librarian = mysqli_fetch_array($sql_librarian);

    if (is_array($row_user)) {
        $_SESSION["username"] = $row_user['username'];
        $_SESSION["password"] = $row_user['password'];
        $_SESSION['user_id'] = $row_user['user_id'];
        header("Location: ../User/userHomePage.php");
    } else if (is_array($row_librarian)) {

        $_SESSION["username"] = $row_librarian['username'];
        $_SESSION["password"] = $row_librarian['password'];
        $_SESSION['librarian_id'] = $row_librarian['librarian_id'];

        header("Location: ../Admin/adminDashboard.php");
    } else {
        $message[] = 'Incorrect Username or Password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Panlalawigang Aklatan ng Bulacan</title>
    <link rel="stylesheet" href="../Styles/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="login-container">
        <div class="close-button">
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
                        <a href="../Processes/forgotPassword.php">Forgot password?</a>
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
                    <span>Not a member? <a href="../Processes/userRegistration.php">Sign Up</a></span>
                </div>
            </form>
        </div>
        <div class="line-between"></div>
        <div class="logo">
            <img src="../images/logo/logo.png" alt="Website Logo">
            <p>PHACTO</p>
        </div>
    </div>

    <script src="../Processes/Script/login.js"></script>
</body>

</html>