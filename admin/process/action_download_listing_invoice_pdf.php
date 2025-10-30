<?php
require '../../tcpdf/tcpdf.php'; // Adjust if path differs
require '../../config/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Invoice ID is required.');
}

$invoice_id = (int) $_GET['id'];

// Fetch invoice
$invoice_query = "SELECT * FROM invoice WHERE id = $invoice_id AND is_deleted = 0";
$invoice_result = mysqli_query($conn, $invoice_query);
$invoice = mysqli_fetch_assoc($invoice_result);
if (!$invoice) {
    die('Invoice not found.');
}

// Fetch client
$client_query = "SELECT * FROM client WHERE id = {$invoice['client_id']}";
$client_result = mysqli_query($conn, $client_query);
$client = mysqli_fetch_assoc($client_result);

// Fetch invoice items
$item_query = "
    SELECT ii.*, p.name AS product_name, u.name AS unit_name, t.name AS tax_name 
    FROM invoice_item ii
    LEFT JOIN product p ON p.id = ii.product_id
    LEFT JOIN units u ON u.id = ii.unit_id
    LEFT JOIN tax t ON t.id = ii.tax_id
    WHERE ii.invoice_id = $invoice_id AND ii.is_deleted = 0";
$item_result = mysqli_query($conn, $item_query);

// Create PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// PDF setup
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Invoice CRM');
$pdf->SetTitle("Invoice #{$invoice['invoice_id']}");
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

// ✅ Use a Unicode font that supports ₹ symbol
$pdf->SetFont('dejavusans', '', 10);

// Build HTML content
$html = "<h2>Invoice #{$invoice['invoice_id']}</h2>
<p><strong>Client:</strong> {$client['first_name']} {$client['last_name']} ({$client['email']})</p>
<p><strong>Invoice Date:</strong> {$invoice['invoice_date']}</p>
<p><strong>Due Date:</strong> {$invoice['due_date']}</p>
<p><strong>Status:</strong> {$invoice['status']}</p>
<hr>
<h4>Invoice Items</h4>
<table border=\"1\" cellpadding=\"4\" cellspacing=\"0\">
    <thead>
        <tr style='background-color:#f0f0f0;'>
            <th><strong>Product</strong></th>
            <th><strong>Quantity</strong></th>
            <th><strong>Unit</strong></th>
            <th><strong>Price</strong></th>
            <th><strong>Tax</strong></th>
            <th><strong>Total</strong></th>
        </tr>
    </thead>
    <tbody>";

while ($row = mysqli_fetch_assoc($item_result)) {
    $html .= "<tr>
        <td>{$row['product_name']}</td>
        <td>{$row['quantity']}</td>
        <td>{$row['unit_name']}</td>
        <td>&#8377;{$row['selling_price']}</td>
        <td>{$row['tax_name']}</td>
        <td>&#8377;{$row['amount']}</td>
    </tr>";
}

$html .= "</tbody></table><br><br>
<p><strong>Sub Total:</strong> &#8377;{$invoice['amount']}</p>
<p><strong>Tax:</strong> &#8377;{$invoice['tax_amount']}</p>
<p><strong>Shipping:</strong> &#8377;{$invoice['shipping_charge']}</p>
<p><strong>Total:</strong> &#8377;{$invoice['total_amount']}</p>";

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF to browser (change 'I' to 'D' to force download)
$pdf->Output("Invoice_{$invoice['invoice_id']}.pdf", 'I');
