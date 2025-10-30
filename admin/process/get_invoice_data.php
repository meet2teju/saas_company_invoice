<?php
header('Content-Type: application/json');
include '../../config/config.php'; // DB connection

$timeframe = $_POST['timeframe'] ?? 'Monthly';

// Set date range
$startDate = '';
$endDate = date('Y-m-d');

switch($timeframe) {
    case 'Weekly':
        $startDate = date('Y-m-d', strtotime('monday this week'));
        break;
    case 'Monthly':
        $startDate = date('Y-m-01');
        break;
    case 'Yearly':
        $startDate = date('Y-01-01');
        break;
    default:
        $startDate = date('Y-m-01');
        break;
}

// Total invoiced (all invoices in range)
$query_invoiced = "SELECT SUM(total_amount) AS total_invoiced 
                   FROM invoice 
                   WHERE invoice_date BETWEEN '$startDate' AND '$endDate'";
$res_invoiced = mysqli_query($conn, $query_invoiced);
$row_invoiced = mysqli_fetch_assoc($res_invoiced);
$total_invoiced = $row_invoiced['total_invoiced'] ?? 0;

// Total received (status = paid)
$query_received = "SELECT SUM(total_amount) AS total_received 
                   FROM invoice 
                   WHERE status='paid' AND invoice_date BETWEEN '$startDate' AND '$endDate'";
$res_received = mysqli_query($conn, $query_received);
$row_received = mysqli_fetch_assoc($res_received);
$total_received = $row_received['total_received'] ?? 0;

// Total pending (status = unpaid)
$query_pending = "SELECT SUM(total_amount) AS total_pending 
                  FROM invoice 
                  WHERE status='unpaid' AND invoice_date BETWEEN '$startDate' AND '$endDate'";
$res_pending = mysqli_query($conn, $query_pending);
$row_pending = mysqli_fetch_assoc($res_pending);
$total_pending = $row_pending['total_pending'] ?? 0;

// Return JSON
echo json_encode([
    'invoiced' => (float)$total_invoiced,
    'received' => (float)$total_received,
    'pending'  => (float)$total_pending
]);
exit;
