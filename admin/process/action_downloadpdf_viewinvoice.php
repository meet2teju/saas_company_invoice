<?php
require_once('../../config/config.php');
require_once '../../tcpdf/tcpdf.php';

$invoice_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($invoice_id <= 0) {
    die('Invalid Invoice ID!');
}

// Invoice
$invoice = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT i.*, l.name AS salesperson_name
    FROM invoice i
    LEFT JOIN login l ON i.user_id = l.id
    WHERE i.id = $invoice_id AND i.is_deleted = 0
"));
if (!$invoice) {
    die('Invoice not found!');
}

$client = $invoice['client_id'] ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM client WHERE id = {$invoice['client_id']}")) : null;
$bank   = $invoice['bank_id']   ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bank WHERE id = {$invoice['bank_id']}")) : null;

$items  = mysqli_query($conn, "
    SELECT ii.*, p.name AS product_name, t.name AS tax_name, u.name AS unit_name
    FROM invoice_item ii
    LEFT JOIN product p ON p.id = ii.product_id
    LEFT JOIN units u ON u.id = ii.unit_id
    LEFT JOIN tax t ON t.id = ii.tax_id
    WHERE ii.invoice_id = $invoice_id AND ii.is_deleted = 0
");

$client_address = $invoice['client_id'] ? mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT ca.*, co.name AS country_name, s.name AS state_name, ci.name AS city_name
    FROM client_address ca
    LEFT JOIN countries co ON co.id = ca.billing_country
    LEFT JOIN states s ON s.id = ca.billing_state
    LEFT JOIN cities ci ON ci.id = ca.billing_city
    WHERE ca.client_id = {$invoice['client_id']}
    LIMIT 1
")) : null;

// ✅ TCPDF setup
$pdf = new TCPDF();
$pdf->SetCreator('Kanakku');
$pdf->SetAuthor('Kanakku Invoice');
$pdf->SetTitle('Invoice #' . $invoice['invoice_id']);
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// ✅ Map status badge color
$status = strtolower($invoice['status'] ?? 'pending');
$badgeColor = match($status) {
    'paid'          => 'green',
    'unpaid'        => 'orange',
    'cancelled'     => 'red',
    'partially paid'=> 'purple',
    'uncollectable' => 'brown',
    default         => 'gray'
};

// ✅ Build PDF content (copied from invoice-details.php structure)
$html = '
<style>
h4,h6 { margin: 0; }
.table { border-collapse: collapse; width: 100%; }
.table th, .table td { border: 1px solid #333; padding: 6px; font-size: 10px; }
.table th { background: #eee; }
.summary td { border: none; padding: 4px; font-size: 11px; }
</style>

<h4>Invoice</h4>
<hr>

<table width="100%">
    <tr>
        <td width="50%">
            <h6>Invoice Details</h6>
            <p>Invoice Number: <strong>' . htmlspecialchars($invoice['invoice_id']) . '</strong></p>
            <p>Issued On: ' . htmlspecialchars($invoice['invoice_date']) . '</p>
            <p>Due Date: ' . htmlspecialchars($invoice['due_date']) . '</p>
            <p>Reference Name: ' . htmlspecialchars($invoice['reference_name']) . '</p>
            <p>Sales Person: ' . htmlspecialchars($invoice['salesperson_name'] ?? 'N/A') . '</p>
            <p>Order Number: ' . htmlspecialchars($invoice['order_number']) . '</p>
            <span style="background:' . $badgeColor . ';color:#fff;padding:3px 8px;border-radius:4px;">' . ucfirst($status) . '</span>
        </td>
        <td width="50%">
            <h6>Billing From</h6>
            <p><strong>' . htmlspecialchars($client['first_name'] ?? '') . '</strong></p>
            <p>' . htmlspecialchars($client_address['billing_address1'] ?? '') . '</p>
            <p>' . htmlspecialchars($client_address['city_name'] ?? '') . ', ' . htmlspecialchars($client_address['state_name'] ?? '') . '</p>
            <p>' . htmlspecialchars($client_address['country_name'] ?? '') . ' - ' . htmlspecialchars($client_address['billing_pincode'] ?? '') . '</p>
            <p>Phone: ' . htmlspecialchars($client['phone_number'] ?? '') . '</p>
            <p>Email: ' . htmlspecialchars($client['email'] ?? '') . '</p>
        </td>
    </tr>
</table>

<br><h6>Product / Service Items</h6>
<table class="table">
<thead>
<tr>
    <th>#</th>
    <th>Product/Service</th>
    <th>Quantity</th>
    <th>Unit</th>
    <th>Selling Price</th>
    <th>Tax (%)</th>
    <th>Amount</th>
</tr>
</thead>
<tbody>';

$i = 1;
while ($item = mysqli_fetch_assoc($items)) {
    $html .= '
    <tr>
        <td>' . $i++ . '</td>
        <td>' . htmlspecialchars($item['product_name']) . '</td>
        <td>' . $item['quantity'] . '</td>
        <td>' . htmlspecialchars($item['unit_name']) . '</td>
        <td>' . number_format($item['selling_price'], 2) . '</td>
        <td>' . htmlspecialchars($item['tax_name']) . '</td>
        <td>' . number_format($item['amount'], 2) . '</td>
    </tr>';
}
$html .= '</tbody></table>';

$html .= '
<br><table width="100%">
<tr>
    <td width="50%">
        <h6>Bank Details</h6>
        <p>Bank Name: ' . htmlspecialchars($bank['bank_name'] ?? '') . '</p>
        <p>Account Number: ' . htmlspecialchars($bank['account_number'] ?? '') . '</p>
        <p>IFSC Code: ' . htmlspecialchars($bank['ifsc_code'] ?? '') . '</p>
    </td>
    <td width="50%">
        <table class="summary" align="right">
            <tr><td>Sub Amount:</td><td align="right">' . number_format($invoice['amount'], 2) . '</td></tr>
            <tr><td>Tax Amount:</td><td align="right">' . number_format($invoice['tax_amount'], 2) . '</td></tr>
            <tr><td>Shipping:</td><td align="right">' . number_format($invoice['shipping_charge'], 2) . '</td></tr>
            <tr><td><strong>Total:</strong></td><td align="right"><strong>' . number_format($invoice['total_amount'], 2) . '</strong></td></tr>
        </table>
    </td>
</tr>
</table>

<br>
<h6>Terms & Conditions</h6>
<p>' . nl2br(htmlspecialchars($invoice['description'])) . '</p>

<h6>Notes</h6>
<p>' . nl2br(htmlspecialchars($invoice['invoice_note'])) . '</p>
';

// ✅ Output
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Invoice_' . $invoice['invoice_id'] . '.pdf', 'D');
exit;
?>
