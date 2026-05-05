<?php
require_once '../includes/db.php';

// Set headers to force download
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=applications_export.csv');

$output = fopen('php://output', 'w');

// Output CSV column headings
fputcsv($output, ['Full Name', 'Email', 'Phone', 'Program', 'Date Applied']);

// Fetch applications
$query = "SELECT full_name, email, phone, program, submitted_at FROM applications ORDER BY submitted_at DESC";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    // Format phone number to preserve formatting in Excel
    $safe_phone = "\t" . $row['phone'];

    fputcsv($output, [
        $row['full_name'],
        $row['email'],
        $safe_phone,
        $row['program'],
        $row['submitted_at']
    ]);
}

fclose($output);
exit;
?>
