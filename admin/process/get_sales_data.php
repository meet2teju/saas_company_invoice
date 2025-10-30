<?php
header('Content-Type: application/json');
include '../../config/config.php'; // DB connection

$timeframe = $_POST['timeframe'] ?? 'Monthly';

$categories = [];
$paid = [];
$unpaid = [];
$totalPaid = 0;
$totalUnpaid = 0;

switch($timeframe) {
    case 'Weekly':
        // 7 days of this week
        for ($i=0; $i<7; $i++) {
            $date = date('Y-m-d', strtotime("monday this week +$i day"));
            $categories[] = date('D', strtotime($date)); // Mon, Tue, etc.

            // Paid
            $resPaid = mysqli_query($conn, "SELECT SUM(total_amount) AS paid FROM invoice WHERE DATE(invoice_date)='$date' AND status='paid'");
            $rowPaid = mysqli_fetch_assoc($resPaid);
            $paidAmount = $rowPaid['paid'] ?? 0;
            $paid[] = $paidAmount;

            // Unpaid
            $resUnpaid = mysqli_query($conn, "SELECT SUM(total_amount) AS unpaid FROM invoice WHERE DATE(invoice_date)='$date' AND status='unpaid'");
            $rowUnpaid = mysqli_fetch_assoc($resUnpaid);
            $unpaidAmount = $rowUnpaid['unpaid'] ?? 0;
            $unpaid[] = $unpaidAmount;

            $totalPaid += $paidAmount;
            $totalUnpaid += $unpaidAmount;
        }
        break;

    case 'Monthly':  // Show Jan-Dec
    case 'Yearly':   // Show Jan-Dec
        for ($m=1; $m<=12; $m++) {
            $categories[] = date('M', mktime(0,0,0,$m,1));

            // Paid
            $resPaid = mysqli_query($conn, "SELECT SUM(total_amount) AS paid FROM invoice WHERE MONTH(invoice_date)=$m AND YEAR(invoice_date)=".date('Y')." AND status='paid'");
            $rowPaid = mysqli_fetch_assoc($resPaid);
            $paidAmount = $rowPaid['paid'] ?? 0;
            $paid[] = $paidAmount;

            // Unpaid
            $resUnpaid = mysqli_query($conn, "SELECT SUM(total_amount) AS unpaid FROM invoice WHERE MONTH(invoice_date)=$m AND YEAR(invoice_date)=".date('Y')." AND status='unpaid'");
            $rowUnpaid = mysqli_fetch_assoc($resUnpaid);
            $unpaidAmount = $rowUnpaid['unpaid'] ?? 0;
            $unpaid[] = $unpaidAmount;

            $totalPaid += $paidAmount;
            $totalUnpaid += $unpaidAmount;
        }
        break;
}

echo json_encode([
    'categories' => $categories,
    'paid' => $paid,
    'unpaid' => $unpaid,
    'totalPaid' => $totalPaid,
    'totalUnpaid' => $totalUnpaid
]);
exit;
?>
