<?php
require_once '../vendor/autoload.php';
include '../config/config.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Start output buffering
ob_start();

$invoice_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($invoice_id <= 0) {
    die('Invalid Invoice ID!');
}

// Fetch invoice data
$invoice_result = mysqli_query($conn, "
    SELECT i.*, l.name AS salesperson_name
    FROM invoice i
    LEFT JOIN login l ON i.user_id = l.id
    WHERE i.id = $invoice_id AND i.is_deleted = 0
");

$invoice = mysqli_fetch_assoc($invoice_result);

if (!$invoice) {
    die('Invoice not found!');
}

$invoiceId = $invoice['id'];
$client_id = $invoice['client_id'];
$bank_id = $invoice['bank_id'];
$item_type = $invoice['item_type'];

// Fetch client
$client = null;
if (!empty($client_id)) {
    $client = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM client WHERE id = $client_id"));
}

// Fetch bank
$bank = null;
if (!empty($bank_id)) {
    $bank = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bank WHERE id = $bank_id"));
}

// Fetch items
$items_result = mysqli_query($conn, "
    SELECT ii.*, 
           p.name AS product_name,
           p.code AS product_code,
           s.name AS service_product_name,
           s.code AS service_code,
           COALESCE(p.code, s.code) AS code,
           t.name AS tax_name, 
           u.name AS unit_name
    FROM invoice_item ii
    LEFT JOIN product p ON p.id = ii.product_id
    LEFT JOIN product s ON s.id = ii.service_id
    LEFT JOIN units u ON u.id = ii.unit_id
    LEFT JOIN tax t ON t.id = ii.tax_id
    WHERE ii.invoice_id = $invoice_id AND ii.is_deleted = 0
");

// Check quantity column
$showQuantityColumn = false;
$hasItems = false;
$itemCount = 0;

// Store items in array first
$items = [];
while ($item = mysqli_fetch_assoc($items_result)) {
    $items[] = $item;
    if (!is_null($item['quantity']) && $item['quantity'] > 0) {
        $showQuantityColumn = true;
    }
    $itemCount++;
}
$hasItems = ($itemCount > 0);

// Calculate column count
$colCount = 6; // #, Product/Service, HSN Code, Selling Price, Tax, Amount
if ($showQuantityColumn) {
    $colCount = 7; // Add QTY/Hours column
}

// Fetch client address
$client_address = null;
if (!empty($client_id)) {
    $client_address_query = "
        SELECT ca.*, 
               co.name AS country_name, 
               s.name AS state_name, 
               ci.name AS city_name
        FROM client_address ca
        LEFT JOIN countries co ON co.id = ca.billing_country
        LEFT JOIN states s ON s.id = ca.billing_state
        LEFT JOIN cities ci ON ci.id = ca.billing_city
        WHERE ca.client_id = $client_id
        LIMIT 1
    ";
    $client_address = mysqli_fetch_assoc(mysqli_query($conn, $client_address_query));
}

// Fetch company info
$company = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT ci.*, 
           co.name AS country_name,
           s.name AS state_name,
           c.name AS city_name
    FROM company_info ci
    LEFT JOIN countries co ON co.id = ci.country_id
    LEFT JOIN states s ON s.id = ci.state_id
    LEFT JOIN cities c ON c.id = ci.city_id
    LIMIT 1
"));

// Check other conditions
$showNotes = !empty($invoice['invoice_note']);
$showTerms = !empty($invoice['description']);
$showBankDetails = $bank && (!empty($bank['bank_name']) || !empty($bank['account_number']) || !empty($bank['ifsc_code']));

// Function to convert number to words
function numberToWords($number) {
    $ones = array(
        0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
        5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
        14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
        18 => 'Eighteen', 19 => 'Nineteen'
    );
    
    $tens = array(
        2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
        6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
    );
    
    if ($number < 20) {
        return $ones[$number];
    }
    
    if ($number < 100) {
        return $tens[(int)($number / 10)] . ($number % 10 != 0 ? ' ' . $ones[$number % 10] : '');
    }
    
    if ($number < 1000) {
        return $ones[(int)($number / 100)] . ' Hundred' . ($number % 100 != 0 ? ' ' . numberToWords($number % 100) : '');
    }
    
    if ($number < 100000) {
        return numberToWords((int)($number / 1000)) . ' Thousand' . ($number % 1000 != 0 ? ' ' . numberToWords($number % 1000) : '');
    }
    
    if ($number < 10000000) {
        return numberToWords((int)($number / 100000)) . ' Lakh' . ($number % 100000 != 0 ? ' ' . numberToWords($number % 100000) : '');
    }
    
    return numberToWords((int)($number / 10000000)) . ' Crore' . ($number % 10000000 != 0 ? ' ' . numberToWords($number % 10000000) : '');
}

// Get absolute path for logo
function getAbsolutePath($relativePath) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . "://" . $host;
    
    // Get the directory of the current script
    $currentDir = dirname($_SERVER['SCRIPT_NAME']);
    
    return $baseUrl . $currentDir . '/' . $relativePath;
}

// Get logo path
$logoPath = '';
if (!empty($company['invoice_logo']) && file_exists('../uploads/' . $company['invoice_logo'])) {
    $logoPath = getAbsolutePath('../uploads/' . $company['invoice_logo']);
} elseif (!empty($company['company_logo']) && file_exists('../uploads/' . $company['company_logo'])) {
    $logoPath = getAbsolutePath('../uploads/' . $company['company_logo']);
}

// Start building HTML
$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice ' . htmlspecialchars($invoice['invoice_id']) . '</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        ol, ul, dl {
            margin-bottom: 0px;
        }

        body {
            background: #fff;
            font-family: "Instrument Sans", sans-serif;
            color: #000;
            font-size: 14px;
            line-height: 1.4;
        }

        .main-body {
            background-color: #fff;
            border: 1px solid #ccc;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .logo {
            max-width: 150px;
            max-height: 60px;
        }

        .invoice-title {
        font-family: "Instrument Sans", sans-serif;
            color: #000;
            font-weight: bold;
            font-size: 24px;
            margin: 0;
        }

        .invoice-top {
            border-top: 1px solid #cfcfcf;
            border-bottom: 1px solid #cfcfcf;
            padding: 10px;
            margin: 10px 0;
        }

        .tittle-text {
        font-family: "Instrument Sans", sans-serif;
            color: #000;
            font-weight: bold;
            font-size: 16px;
        }

        .to-title {
        font-family: "Instrument Sans", sans-serif;
            color: #000;
            font-weight: 600;
            font-size: 14px;
        }

        .bold-text{
            color: #000;
            font-weight: 600;
        }

        .address-deatils-box {
        font-family: "Instrument Sans", sans-serif;
            color: #5d6772;
            font-weight: 500;
            font-size: 14px;
        }

        .bank-deatils-title {
        font-family: "Instrument Sans", sans-serif;
            color: #000;
            font-weight: 500;
            font-size: 16px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .table th, .table td {
        font-family: "Instrument Sans", sans-serif;
            padding: 6px;
            font-size: 14px;
            border: 1px solid #cfcfcf;
        }

        .table .bg-light {
            background-color: #000 !important;
            color: white !important;
        }

        .bank-details-ul {
        font-family: "Instrument Sans", sans-serif;
            list-style: none;
            padding-left: 0;
            font-size: 14px;
        }

        .bank-details-ul li {
            margin-bottom: 4px;
        }

        .subtotal-box{
           text-align: right;
        }

        .subtotal-box .subtotal-title {
         font-family: "Instrument Sans", sans-serif;
            color: #000;
            font-weight: 500;
            margin-bottom: 0;
            text-align: right;
        }

        .subtotal-box .subtotal-amount {
         font-family: "Instrument Sans", sans-serif;
            color: #5d6772;
            font-weight: 500;
            margin-bottom: 0;
            text-align: right;
        }

        .terms-conditions-title {
        font-family: "Instrument Sans", sans-serif;
            color: #000;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .terms-conditions {
        font-family: "Instrument Sans", sans-serif;
            background-color: #ddeeff;
            border-radius: 4px;
            padding: 10px;
            margin-top: 15px;
            font-size: 14px;
        }
        
        .gst-badge {
        font-family: "Instrument Sans", sans-serif;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 600;
        }
        .gst-badge.gst {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        .gst-badge.non-gst {
            background-color: #fff3cd;
            color: #664d03;
            border: 1px solid #ffecb5;
        }

        .billing-container {
            width: 100%;
            margin-bottom: 15px;
        }

        .billing-row {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
        }

        .billing-from, .billing-to {
            display: table-cell;
            vertical-align: top;
        }

        .billing-title {
        font-family: "Instrument Sans", sans-serif;
            color: #000;
            font-weight: 700;
            font-size: 18px;
        }

        .header-table {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        .mt-3 { margin-top: 15px; }
        
        
        .bank-detail-row {
        font-family: "Instrument Sans", sans-serif;
            display: flex;
            margin-bottom: 4px;
            font-size: 14px;
        }
        
        .bank-detail-label {
            font-weight: 600;
            color: #000;
            min-width: 100px;
        }
        
        .bank-detail-value {
            color: #5d6772;
            font-weight: 500;
        }
        
        .totals-section {
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .total-row {
        font-family: "Instrument Sans", sans-serif;
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 14px;
        }
        
        .total-label {
            font-weight: 600;
            color: #000;
        }
        
        .total-value {
            color: #5d6772;
            font-weight: 500;
        }
        
        .total-main {
        font-family: "Instrument Sans", sans-serif;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            margin-top: 8px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .words-section {
        font-family: "Instrument Sans", sans-serif;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #ddd;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="main-body">
        <table class="header-table">
            <tr>
                <td width="60%" vertical-align: middle;>';
                
if (!empty($logoPath)) {
    $html .= '<img src="' . $logoPath . '" class="logo" alt="logo">';
} else {
    $html .= '<h2 style="margin:0; color: #000;">' . htmlspecialchars($company['name'] ?? 'Company Name') . '</h2>';
}

$html .= '</td>
                <td width="40%" class="text-right" vertical-align: middle;>
                    <h1 class="invoice-title">INVOICE</h1>
                </td>
            </tr>
        </table>

        <div class="invoice-top">
            <table width="100%">
                <tr>
                    <td width="50%">
                        <div class="tittle-text">Date:</div>
                        <div class="address-deatils-box">' . htmlspecialchars($invoice['invoice_date']) . '</div>
                    </td>
                    <td width="50%" class="text-right">
                        <div class="tittle-text">Invoice No:</div>
                        <div class="address-deatils-box">' . htmlspecialchars($invoice['invoice_id']) . '</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="billing-container">
            <div class="billing-row">
                <div class="billing-from">
                    <div class="billing-title">Billing From</div>
                    <div class="to-title">' . htmlspecialchars($company['name'] ?? '') . '</div>';
                    
if (!empty($company['address'])) {
    $html .= '<div class="address-deatils-box mb-0">' . htmlspecialchars($company['address'] ?? '') . '</div>';
}

if (!empty($company['city_name']) || !empty($company['state_name']) || !empty($company['country_name']) || !empty($company['zipcode'])) {
    $html .= '<div class="address-deatils-box">' . 
        htmlspecialchars($company['city_name'] ?? '') . ', ' . 
        htmlspecialchars($company['state_name'] ?? '') . ', ' . 
        htmlspecialchars($company['country_name'] ?? '') . ', ' . 
        htmlspecialchars($company['zipcode'] ?? '') . 
    '</div>';
}

if (!empty($company['mobile_number'])) {
    $html .= '<div class="address-deatils-box"><span class="bold-text">Phone:</span> ' . htmlspecialchars($company['mobile_number'] ?? '') . '</div>';
}

if (!empty($company['email'])) {
    $html .= '<div class="address-deatils-box"><span class="bold-text">Email:</span> ' . htmlspecialchars($company['email'] ?? '') . '</div>';
}

$html .= '</div>
                <div class="billing-to">
                    <div class="billing-title text-right">Billing To</div>
                    <div class="to-title text-right">' . htmlspecialchars($client['first_name'] ?? '') . '</div>';
                    
if (!empty($client_address['billing_address1'])) {
    $html .= '<div class="address-deatils-box text-right mb-0">' . htmlspecialchars($client_address['billing_address1'] ?? '') . '</div>';
}

if (!empty($client_address['city_name']) || !empty($client_address['state_name']) || !empty($client_address['country_name']) || !empty($client_address['billing_pincode'])) {
    $html .= '<div class="address-deatils-box text-right">' . 
        htmlspecialchars($client_address['city_name'] ?? '') . ', ' . 
        htmlspecialchars($client_address['state_name'] ?? '') . ', ' . 
        htmlspecialchars($client_address['country_name'] ?? '') . ', ' . 
        htmlspecialchars($client_address['billing_pincode'] ?? '') . 
    '</div>';
}

if (!empty($client['phone_number'])) {
    $html .= '<div class="address-deatils-box text-right"><span class="bold-text">Phone:</span> ' . htmlspecialchars($client['phone_number'] ?? '') . '</div>';
}

if (!empty($client['email'])) {
    $html .= '<div class="address-deatils-box text-right"><span class="bold-text">Email:</span> ' . htmlspecialchars($client['email'] ?? '') . '</div>';
}

$html .= '</div>
            </div>
        </div>

        <div class="mb-3">
            <h4 class="billing-title">Product / Service Items</h4>
            <table class="table">
                <thead>
                    <tr style="background-color: #000; color: white;">';

// Build table headers with consistent structure
$html .= '<th width="5%">#</th>
          <th width="' . ($showQuantityColumn ? '30%' : '35%') . '">Product/Service</th>
          <th width="15%">HSN Code</th>';

if ($showQuantityColumn) {
    $html .= '<th width="10%" class="text-center">' . ($item_type == 1 ? 'QTY' : 'Hours') . '</th>';
}

$html .= '<th width="15%">Selling Price</th>
          <th width="10%">Tax</th>
          <th width="' . ($showQuantityColumn ? '10%' : '15%') . '">Amount</th>';

$html .= '</tr>
                </thead>
                <tbody>';

// Add items to table
$i = 1;
foreach ($items as $item) {
    if (!empty($item['service_id'])) {
        $productName = !empty($item['service_product_name']) ? $item['service_product_name'] : '';
        $serviceName = !empty($item['service_name']) ? $item['service_name'] : '';
        $itemName = $productName . ($serviceName ? ' - ' . $serviceName : '');
    } else {
        $itemName = !empty($item['product_name']) ? $item['product_name'] : 'Product';
    }
    
    $html .= '<tr>
        <td>' . $i++ . '</td>
        <td>' . htmlspecialchars($itemName) . '</td>
        <td>' . htmlspecialchars($item['code'] ?? 'N/A') . '</td>';
    
    if ($showQuantityColumn) {
        $html .= '<td class="text-center">' . ($item['quantity'] ?? '0') . '</td>';
    }
    
    $html .= '<td>$' . number_format($item['selling_price'], 2) . '</td>
        <td>';
    
    if (($invoice['gst_type'] ?? 'gst') === 'non_gst') {
        $html .= 'Non-GST';
    } else {
        $html .= htmlspecialchars($item['tax_name'] ?? '');
    }
    
    $html .= '</td>
        <td>$' . number_format($item['amount'], 2) . '</td>
    </tr>';
}

// If no items, add a placeholder row with correct colspan
if (!$hasItems) {
    $html .= '<tr><td colspan="' . $colCount . '" style="text-align: center; padding: 20px;">No items found</td></tr>';
}

$html .= '</tbody>
            </table>
        </div>

        <div style="border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 15px;">
            <table width="100%">
                <tr>';

if ($showBankDetails) {
    $html .= '<td>
        <div class="bank-details-section">
            <h5 class="terms-conditions-title">Bank Details</h5>';
    
    if (!empty($bank['bank_name'])) {
        $html .= '<div class="address-deatils-box">
                <span class="bold-text">Bank Name:</span>
                 '. htmlspecialchars($bank['bank_name']) .'
            </div>';
    }
    if (!empty($bank['account_number'])) {
        $html .= '<div class="address-deatils-box">
                <span class="bold-text">A/C No:</span>
                 '. htmlspecialchars($bank['account_number']) .'
            </div>';
    }
    if (!empty($bank['ifsc_code'])) {
        $html .= '<div class="address-deatils-box">
                <span class="bold-text">IFSC Code:</span>
                 '. htmlspecialchars($bank['ifsc_code']) .'
            </div>';
    }
    
    $html .= '</div></td>';
    $totalsWidth = '50%';
} else {
    $totalsWidth = '100%';
}

$html .= '<td width="' . $totalsWidth . '" style="vertical-align: top; text-align: right;">
        <table style="width:100%;">
            <tr class="subtotal-box">
                <td class="subtotal-title">Sub Amount:</td>
                <td class="subtotal-amount">$' . number_format($invoice['amount'], 2) . '</td>
            </tr>';
            
if (($invoice['gst_type'] ?? 'gst') === 'non_gst') {
    $html .= '<tr class="subtotal-box">
                <td class="subtotal-title">Tax (Non-GST):</td>
                <td class="subtotal-amount">$0.00</td>
            </tr>';
} else {
    $html .= '<tr class="subtotal-box">
                <td class="subtotal-title">Tax Amount:</td>
                <td class="subtotal-amount">$' . number_format($invoice['tax_amount'], 2) . '</td>
            </tr>';
}

if (!empty($invoice['shipping_charge']) && $invoice['shipping_charge'] > 0) {
    $html .= '<tr class="subtotal-box">
                <td class="subtotal-title">Shipping Charge:</td>
                <td class="subtotal-amount">$' . number_format($invoice['shipping_charge'], 2) . '</td>
            </tr>';
}

$html .= '<tr class="subtotal-box">
                <td class="subtotal-title">Total:</td>
                <td class="subtotal-amount">$' . number_format($invoice['total_amount'], 2) . '</td>
            </tr>
            
           
        </table>

        <div class="address-deatils-box text-right">
                <span class="bold-text">Total In Words:</span>
                 ' . numberToWords($invoice['total_amount']) . ' Dollars
            </div>
        
    </td>';

$html .= '</tr>
            </table>
        </div>';

if ($showNotes) {
    $html .= '<div class="terms-conditions">
            <p class="terms-conditions-title">Notes:</p>
            <p>' . htmlspecialchars($invoice['invoice_note']) . '</p>
        </div>';
}

if ($showTerms) {
    $html .= '<div class="terms-conditions" style="margin-top: ' . ($showNotes ? '10' : '0') . 'px;">
            <p class="terms-conditions-title">Terms & Conditions:</p>
            <p>' . htmlspecialchars($invoice['description']) . '</p>
        </div>';
}

$html .= '</div>
</body>
</html>';

// Configure DomPDF
try {
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', false);
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('debugLayout', false);
    $options->set('debugLayoutLines', false);
    $options->set('debugLayoutBlocks', false);
    $options->set('debugLayoutInline', false);
    $options->set('debugLayoutPaddingBox', false);

    $dompdf = new Dompdf($options);
    
    // Set time limit for PDF generation
    set_time_limit(120);
    
    // Load HTML content with UTF-8 encoding
    $dompdf->loadHtml(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    
    // Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait');
    
    // Render PDF
    $dompdf->render();
    
    // Clear output buffer
    if (ob_get_length()) {
        ob_clean();
    }
    
    // Output the PDF
    $dompdf->stream('Invoice_' . $invoice['invoice_id'] . '.pdf', [
        'Attachment' => true,
        'compress' => true
    ]);
    
} catch (Exception $e) {
    // Handle DomPDF errors gracefully
    ob_clean();
    echo "<h3>Error Generating PDF</h3>";
    echo "<p>An error occurred while generating the PDF. Please try again.</p>";
    echo "<p>Error details: " . htmlspecialchars($e->getMessage()) . "</p>";
    error_log("Dompdf Error: " . $e->getMessage());
    exit;
}

exit;
?>