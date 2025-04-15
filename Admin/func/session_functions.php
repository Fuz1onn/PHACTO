<?php

function startSession() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectToLogin() {
    header('Location: ../Visitor/visitorLandingPage.php');
    exit();
}

function logout() {
    startSession();
    unset($_SESSION['user_id']);
    session_destroy();
    redirectToLogin();
}

?>
