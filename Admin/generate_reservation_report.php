<?php
include '../conn/connection.php';

function getReservationsByDateWithDetails($conn) {
    $sql = "SELECT id, user_id, section, reservation_date, start_time, end_time, seat_number, status, reservation_code FROM reservations";
    $result = $conn->query($sql);

    $data = [];
    $labels = [];
    $reservationDetails = [];

    // Fetch data and labels from the database
    while($row = $result->fetch_assoc()) {
        $labels[] = date('Y-m-d', strtotime($row['reservation_date'])); // Format the date as needed
        $data[] = $row['id']; // Change this line if you want a different count metric
        $reservationDetails[] = $row; // Store the complete reservation details
    }

    return ['labels' => $labels, 'data' => $data, 'reservationDetails' => $reservationDetails];
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

function generatePDFReportWithDetails($labels, $data, $reservationDetails, $websiteName, $websiteLocation, $websiteLogoPath) {
    require_once('C:\xampp\htdocs\caps2\Admin\TCPDF-main\tcpdf.php');

    $pdf = new TCPDF();

    // Set document properties
    $pdf->SetCreator('Your Creator');
    $pdf->SetAuthor('Your Author');
    $pdf->SetTitle('Chart Data Report');
    $pdf->SetSubject('Chart Data Report');
    $pdf->SetKeywords('Chart, Data, Report');

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 12);

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
    $pdf->Cell(0, 0, 'Reservations Data Report', 0, 1, 'C'); // Center-align the report title
    $pdf->Ln(10); // Add space between title and table

    // Center the table on the page
    $tableWidth = 180; // Set the width of the table
    $tableX = ($pdf->getPageWidth() - $tableWidth) / 2;
    $pdf->SetX($tableX);

    // Display a table with reservation details
    $pdf->SetFont('helvetica', 'B', 6);
    $pdf->SetFillColor(17, 43, 60); // Header background color
    $pdf->SetTextColor(255); // Header text color
    $pdf->SetDrawColor(0); // Header border color

    // Output table headers for reservation details
    $pdf->Cell(20, 8, 'ID', 1, 0, 'C', 1);
    $pdf->Cell(20, 8, 'User ID', 1, 0, 'C', 1);
    $pdf->Cell(20, 8, 'Section', 1, 0, 'C', 1);
    $pdf->Cell(20, 8, 'Reservation Date', 1, 0, 'C', 1);
    $pdf->Cell(20, 8, 'Start Time', 1, 0, 'C', 1);
    $pdf->Cell(20, 8, 'End Time', 1, 0, 'C', 1);
    $pdf->Cell(20, 8, 'Seat Number', 1, 0, 'C', 1);
    $pdf->Cell(20, 8, 'Status', 1, 0, 'C', 1);
    $pdf->Cell(20, 8, 'Reservation Code', 1, 1, 'C', 1);

    $tableBodyWidth = 180; // Set the width of the table body
    $tableBodyX = ($pdf->getPageWidth() - $tableBodyWidth) / 2;
    $pdf->SetX($tableBodyX);

    // Set table body styling
    $pdf->SetFont('helvetica', '', 6);
    $pdf->SetFillColor(235, 235, 235); // Body background color
    $pdf->SetTextColor(0); // Body text color
    $pdf->SetDrawColor(0); // Body border color

    // Output reservation details
    foreach($reservationDetails as $reservation) {
        $pdf->SetX($tableX);
        $pdf->Cell(20, 8, $reservation['id'], 1, 0, 'C');
        $pdf->Cell(20, 8, $reservation['user_id'], 1, 0, 'C');
        $pdf->Cell(20, 8, $reservation['section'], 1, 0, 'C');
        $pdf->Cell(20, 8, $reservation['reservation_date'], 1, 0, 'C');
        $pdf->Cell(20, 8, $reservation['start_time'], 1, 0, 'C');
        $pdf->Cell(20, 8, $reservation['end_time'], 1, 0, 'C');
        $pdf->Cell(20, 8, $reservation['seat_number'], 1, 0, 'C');
        $pdf->Cell(20, 8, $reservation['status'], 1, 0, 'C');
        $pdf->Cell(20, 8, $reservation['reservation_code'], 1, 1, 'C');
    }

    // Save PDF to a file or stream it to the browser
    $pdf->Output('C:\xampp\htdocs\caps2\Admin\chart_data_report_with_details.pdf', 'D'); // 'D' for force download
}

$currentWebsiteSettings = getWebsiteSettings($conn);
$reservationsByDateDataWithDetails = getReservationsByDateWithDetails($conn);

$websiteName = $currentWebsiteSettings['website_name'];
$websiteLocation = "Malolos, Bulacan"; // Add the location information
$websiteLogo = $currentWebsiteSettings['website_logo'];

generatePDFReportWithDetails($reservationsByDateDataWithDetails['labels'], $reservationsByDateDataWithDetails['data'], $reservationsByDateDataWithDetails['reservationDetails'], $websiteName, $websiteLocation, $websiteLogo);
?>