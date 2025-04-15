<?php
session_start();
$user_id = $_SESSION['user_id'];
include '../conn/connection.php';

require 'C:\xampp\htdocs\caps2\PHPMailer\src\Exception.php';
require 'C:\xampp\htdocs\caps2\PHPMailer\src\PHPMailer.php';
require 'C:\xampp\htdocs\caps2\PHPMailer\src\SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Retrieve email address from command line argument
$to = $argv[1];

$mail = new PHPMailer(true);

try {
    // Configure PHPMailer for email sending
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'khalilacebuche17@gmail.com'; // Your Gmail email address
    $mail->Password = 'qtdx gmwl tfmk rabg'; // Your Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('khalilacebuche17@gmail.com', 'Khalil');
    $mail->addAddress($to);

    // Content
    $mail->isHTML(true);  // Set email format to HTML
    $mail->Subject = 'Reservation Confirmation';
    $mail->Body = 'Thank you for your reservation!'; // You can customize the message

    $mail->send();
} catch (Exception $e) {
    echo "Error sending email: {$mail->ErrorInfo}";
}
