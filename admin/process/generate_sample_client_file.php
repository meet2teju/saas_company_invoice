<?php
// Set headers for CSV file download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="client_import_template.csv"');
header('Cache-Control: max-age=0');

// Create sample data with headers
$data = [
    ['First Name', 'Email', 'Company Name', 'Phone Number', 'Balance', 'Country'],
    ['John Doe', 'john@example.com', 'ABC Company', '123-456-7890', '1000.00', 'United States'],
    ['Jane Smith', 'jane@example.com', 'XYZ Corp', '098-765-4321', '2500.50', 'Canada'],
    ['Bob Johnson', 'bob@example.com', 'Acme Inc', '555-123-4567', '500.00', 'United Kingdom'],
    ['Maria Garcia', 'maria@example.com', 'Garcia Enterprises', '444-555-6666', '1500.75', 'Spain'],
    ['', 'invalid@example', '', '', 'not-a-number', ''], // Example of invalid data
];

// Output the data as CSV
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility
fputs($output, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF));

foreach ($data as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit;