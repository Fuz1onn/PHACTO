<?php
include '../conn/connection.php';
function getNewUsersWithUserDetails($conn) {
    $sql = "SELECT
                DATE(registration_date) as day,
                COUNT(*) as new_users,
                GROUP_CONCAT(username) as usernames,
                GROUP_CONCAT(custom_id) as custom_ids,
                GROUP_CONCAT(firstname) as firstnames,
                GROUP_CONCAT(lastname) as lastnames,
                GROUP_CONCAT(address) as addresses,
                GROUP_CONCAT(cnumber) as cnumbers,
                GROUP_CONCAT(email) as emails,
                GROUP_CONCAT(gender) as genders,
                GROUP_CONCAT(registration_date) as registration_dates
            FROM `user profile`
            WHERE registration_date >= '2023-01-01'
            GROUP BY DATE(registration_date)";

    $result = $conn->query($sql);

    $data = [];
    $labels = [];
    $usernames = [];
    $customIds = [];
    $firstnames = [];
    $lastnames = [];
    $addresses = [];
    $cnumbers = [];
    $emails = [];
    $genders = [];
    $registrationDates = [];

    // Fetch data, labels, and user details from the database
    while($row = $result->fetch_assoc()) {
        $labels[] = ''.$row['day'];
        $data[] = $row['new_users'];
        $usernames[] = explode(',', $row['usernames']);
        $customIds[] = explode(',', $row['custom_ids']);
        $firstnames[] = explode(',', $row['firstnames']);
        $lastnames[] = explode(',', $row['lastnames']);
        $addresses[] = explode(',', $row['addresses']);
        $cnumbers[] = explode(',', $row['cnumbers']);
        $emails[] = explode(',', $row['emails']);
        $genders[] = explode(',', $row['genders']);
        $registrationDates[] = explode(',', $row['registration_dates']);
    }

    return [
        'labels' => $labels,
        'data' => $data,
        'usernames' => $usernames,
        'custom_ids' => $customIds,
        'firstnames' => $firstnames,
        'lastnames' => $lastnames,
        'addresses' => $addresses,
        'cnumbers' => $cnumbers,
        'emails' => $emails,
        'genders' => $genders,
        'registration_dates' => $registrationDates
    ];
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

$newUsersWithUserDetails = getNewUsersWithUserDetails($conn);

// Include TCPDF library
require_once('C:\xampp\htdocs\caps2\Admin\TCPDF-main\tcpdf.php');

// Function to generate PDF report
function generatePDFReport($labels, $data, $usernames, $customIds, $firstnames, $lastnames, $addresses, $cnumbers, $emails, $genders, $registrationDates, $websiteName, $websiteLocation, $websiteLogoPath) {
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    // Set document properties
    $pdf->SetCreator('Your Creator');
    $pdf->SetAuthor('Your Author');
    $pdf->SetTitle('Chart Data Report');
    $pdf->SetSubject('Chart Data Report');
    $pdf->SetKeywords('Chart, Data, Report');

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 8);

    // Get image dimensions
    list($logoWidth, $logoHeight) = getimagesize($websiteLogoPath);

    // Calculate the positioning to center the logo
    $maxLogoWidth = 150; // Maximum allowed width
    $maxLogoHeight = 150; // Maximum allowed height
    $logoScale = min($maxLogoWidth / $logoWidth, $maxLogoHeight / $logoHeight);
    $scaledLogoWidth = $logoWidth * $logoScale;
    $scaledLogoHeight = $logoHeight * $logoScale;
    $logoX = ($pdf->getPageWidth() - $scaledLogoWidth) / 2;
    $logoY = ($pdf->getPageHeight() - $scaledLogoHeight) / 2;

    // Set the logo as a background with transparency
    $pdf->setAlpha(0.1); // Set the transparency level (0 to 1)
    $pdf->Image($websiteLogoPath, $logoX, $logoY, $scaledLogoWidth, $scaledLogoHeight, 'PNG', '', '', false, 300, '', false, false, 0);
    $pdf->setAlpha(1); // Reset the transparency to normal

    // Output website name and location on top of the background
    $pdf->Ln(10); // Add some space between logo and website name
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, ucwords($websiteName), 0, 1, 'C'); // Center-align the website name
    $pdf->Cell(0, 0, $websiteLocation, 0, 1, 'C'); // Center-align the website location

    // Output chart data
    $pdf->Ln(10); // Add space between header and chart data
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 0, 'New Users Data Report', 0, 1, 'C'); // Center-align the report title
    $pdf->Ln(10); // Add space between title and table

    // Center the table on the page
    $tableWidth = 195; // Set the width of the table
    $tableX = ($pdf->getPageWidth() - $tableWidth) / 2;
    $pdf->SetX($tableX);

    // Set table header styling
    $pdf->SetFont('helvetica', 'B', 5);
    $pdf->SetFillColor(17, 43, 60); // Header background color
    $pdf->SetTextColor(255); // Header text color
    $pdf->SetDrawColor(0); // Header border color

    // Output table headers
    $pdf->Cell(12, 8, 'Date', 1, 0, 'C', 1);
    $pdf->Cell(12, 8, 'New Users', 1, 0, 'C', 1);
    $pdf->Cell(20, 8, 'Usernames', 1, 0, 'C', 1);
    $pdf->Cell(12, 8, 'Custom IDs', 1, 0, 'C', 1);
    $pdf->Cell(12, 8, 'First Names', 1, 0, 'C', 1);
    $pdf->Cell(12, 8, 'Last Names', 1, 0, 'C', 1);
    $pdf->Cell(30, 8, 'Addresses', 1, 0, 'C', 1);
    $pdf->Cell(20, 8, 'Contact Numbers', 1, 0, 'C', 1);
    $pdf->Cell(30, 8, 'Emails', 1, 0, 'C', 1);
    $pdf->Cell(8, 8, 'Genders', 1, 0, 'C', 1);
    $pdf->Cell(30, 8, 'Registration Dates', 1, 1, 'C', 1);

    // Center the table body on the page
    $tableBodyWidth = 500; // Set the width of the table body
    $tableBodyX = ($pdf->getPageWidth() - $tableBodyWidth) / 2;
    $pdf->SetX($tableBodyX);

    // Set table body styling
    $pdf->SetFont('helvetica', '', 4);
    $pdf->SetFillColor(235, 235, 235); // Body background color
    $pdf->SetTextColor(0); // Body text color
    $pdf->SetDrawColor(0); // Body border color

    foreach ($labels as $key => $label) {
        $pdf->SetX($tableX);
        $pdf->Cell(12, 8, $label, 1, 0, 'C');
        $pdf->Cell(12, 8, $data[$key], 1, 0, 'C');
        $pdf->Cell(20, 8, implode(', ', $usernames[$key]), 1, 0, 'C');
        $pdf->Cell(12, 8, implode(', ', $customIds[$key]), 1, 0, 'C');
        $pdf->Cell(12, 8, implode(', ', $firstnames[$key]), 1, 0, 'C');
        $pdf->Cell(12, 8, implode(', ', $lastnames[$key]), 1, 0, 'C');
        $pdf->Cell(30, 8, implode(', ', $addresses[$key]), 1, 0, 'C');
        $pdf->Cell(20, 8, implode(', ', $cnumbers[$key]), 1, 0, 'C');
        $pdf->Cell(30, 8, implode(', ', $emails[$key]), 1, 0, 'C');
        $pdf->Cell(8, 8, implode(', ', $genders[$key]), 1, 0, 'C');
        $pdf->Cell(30, 8, implode(', ', $registrationDates[$key]), 1, 1, 'C');
    }

    // Save PDF to a file or stream it to the browser
    $pdf->Output('C:\xampp\htdocs\caps2\Admin\chart_data_report.pdf', 'D'); // 'D' for force download
}


$currentWebsiteSettings = getWebsiteSettings($conn);

$websiteName = $currentWebsiteSettings['website_name'];
$websiteLocation = "Malolos, Bulacan"; // Add the location information
$websiteLogo = $currentWebsiteSettings['website_logo'];
generatePDFReport(
    $newUsersWithUserDetails['labels'],
    $newUsersWithUserDetails['data'],
    $newUsersWithUserDetails['usernames'],
    $newUsersWithUserDetails['custom_ids'],
    $newUsersWithUserDetails['firstnames'],
    $newUsersWithUserDetails['lastnames'],
    $newUsersWithUserDetails['addresses'],
    $newUsersWithUserDetails['cnumbers'],
    $newUsersWithUserDetails['emails'],
    $newUsersWithUserDetails['genders'],
    $newUsersWithUserDetails['registration_dates'],
    $websiteName,
    $websiteLocation,
    $websiteLogo
);

?>