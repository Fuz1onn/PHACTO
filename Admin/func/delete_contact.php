<?php
// Include your database connection file here
include '../../conn/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    // Get the contact ID from the URL
    $contactId = $_GET['id'];

    // Retrieve the contact data before deletion
    $sqlSelect = "SELECT * FROM `contact_us` WHERE `c_id` = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    $stmtSelect->bind_param('i', $contactId);
    $stmtSelect->execute();
    $resultSelect = $stmtSelect->get_result();

    if ($resultSelect->num_rows > 0) {
        // Fetch the contact data
        $contactData = $resultSelect->fetch_assoc();

        // Archive the contact data
        $sqlArchive = "INSERT INTO `archive_contact_us` 
                       (`c_id`, `contact_name`, `contact_email`, `contact_message`, `c_date`) 
                       VALUES 
                       (?, ?, ?, ?, ?)";
        $stmtArchive = $conn->prepare($sqlArchive);
        $stmtArchive->bind_param('issss', $contactData['c_id'], $contactData['contact_name'], $contactData['contact_email'], $contactData['contact_message'], $contactData['c_date']);
        $stmtArchive->execute();

        // Delete the contact from the original table
        $sqlDelete = "DELETE FROM `contact_us` WHERE `c_id` = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param('i', $contactId);
        $stmtDelete->execute();

        // Redirect back to the contact us page after deletion
        header("Location: ../adminContactUs.php");
        exit();
    }
}
?>
