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

// Function to get company currency from your currency table
function getCompanyCurrency($conn, $org_id) {
    // First, get the currency_id from company_info for this org
    $sql = "SELECT currency_symbol_id FROM company_info WHERE org_id = '$org_id' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $companyInfo = mysqli_fetch_assoc($result);
        $currency_id = $companyInfo['currency_symbol_id'] ?? null;
        
        if ($currency_id) {
            // Get currency details from currency table
            $sql = "SELECT currency_symbol, currency_name, isocode 
                    FROM currency 
                    WHERE id = '$currency_id' 
                    LIMIT 1";
            $result = mysqli_query($conn, $sql);
            
            if ($result && mysqli_num_rows($result) > 0) {
                return mysqli_fetch_assoc($result);
            }
        }
    }
    
    // If no currency found, get default (first currency)
    $sql = "SELECT currency_symbol, currency_name, isocode 
            FROM currency 
            LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    // Default fallback
    return [
        'currency_symbol' => '$',
        'currency_name' => 'US Dollar',
        'isocode' => 'USD'
    ];
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
$org_id = $invoice['org_id'] ?? 1; // Get organization ID

// Get company currency using the same function as quotation file
$companyCurrency = getCompanyCurrency($conn, $org_id);
$currencySymbol = $companyCurrency['currency_symbol'] ?? '$';
$currencyName = $companyCurrency['currency_name'] ?? 'US Dollar';

// Get tax_type from invoice
$taxType = $invoice['tax_type'] ?? 'non_gst';

// Check GST type
$gstType = $invoice['gst_type'] ?? 'gst';
$showGSTColumn = ($gstType !== 'non_gst' && $gstType !== null);

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

// Fetch items - UPDATED QUERY TO GET CGST/SGST/IGST RATES
$items_result = mysqli_query($conn, "
    SELECT ii.*, 
           p.name AS product_name,
           p.code AS product_code,
           s.name AS service_product_name,
           s.code AS service_code,
           COALESCE(p.code, s.code) AS code,
           t.name AS tax_name, 
           t.rate AS tax_rate,
           ii.cgst_rate,
           ii.sgst_rate,
           ii.igst_rate,
           u.name AS unit_name
    FROM invoice_item ii
    LEFT JOIN product p ON p.id = ii.product_id
    LEFT JOIN product s ON s.id = ii.service_id
    LEFT JOIN units u ON u.id = ii.unit_id
    LEFT JOIN tax t ON t.id = ii.tax_id
    WHERE ii.invoice_id = $invoice_id AND ii.is_deleted = 0
");

// Check quantity column and other column data
$hasItems = false;
$itemCount = 0;

// Store items in array first and check for data
$items = [];
$hasUnitData = false;
$hasQuantityData = false;
$hasTaxData = false;
$hasHsnCodeData = false;
$hasSellingPriceData = false;

// Calculate totals and tax summary with CGST/SGST/IGST logic
$taxSummary = [];
$subtotal = 0;
$totalTax = 0;

while ($item = mysqli_fetch_assoc($items_result)) {
    $items[] = $item;
    $itemCount++;
    
    // Check unit column
    if (!empty($item['unit_name']) && trim($item['unit_name']) !== '') {
        $hasUnitData = true;
    }
    
    // Check quantity column (if quantity > 0)
    if (!is_null($item['quantity']) && $item['quantity'] > 0) {
        $hasQuantityData = true;
    }
    
    // Check tax column (for GST-enabled invoices)
    if ($showGSTColumn && !empty($item['tax_name']) && trim($item['tax_name']) !== '') {
        $hasTaxData = true;
    }
    
    // Check HSN code column
    if (!empty($item['code']) && trim($item['code']) !== '' && strtoupper($item['code']) !== 'N/A') {
        $hasHsnCodeData = true;
    }
    
    // Check selling price column
    if (!is_null($item['selling_price']) && $item['selling_price'] > 0) {
        $hasSellingPriceData = true;
    }
    
    // Calculate tax summary for this item
    $itemAmount = $item['amount'];
    
    // Calculate tax for this item only if GST type is not non_gst
    if ($showGSTColumn) {
        $effectiveTaxRate = $item['tax_rate'] ?? 0;
        $taxName = $item['tax_name'] ?? 'Tax';
        
        // For Non-GST invoices, tax should be 0
        if ($gstType === 'non_gst') {
            $effectiveTaxRate = 0;
            $lineTax = 0;
            $baseAmount = $itemAmount; // For non-GST, amount is already without tax
        } else {
            // Calculate base amount (without tax) from the total amount
            if ($effectiveTaxRate > 0) {
                $baseAmount = $itemAmount / (1 + ($effectiveTaxRate / 100));
                $lineTax = $itemAmount - $baseAmount;
            } else {
                $baseAmount = $itemAmount;
                $lineTax = 0;
            }
        }
    } else {
        $baseAmount = $itemAmount;
        $lineTax = 0;
    }
    
    // Add base amount to subtotal (without tax)
    $subtotal += $baseAmount;
    
    // Build tax label based on tax_type
    if ($showGSTColumn && $effectiveTaxRate > 0) {
        if ($taxType === 'cgst_sgst') {
            // For CGST+SGST, show combined rate but store separately for summary
            $cgstRate = $item['cgst_rate'] > 0 ? $item['cgst_rate'] : ($effectiveTaxRate / 2);
            $sgstRate = $item['sgst_rate'] > 0 ? $item['sgst_rate'] : ($effectiveTaxRate / 2);
            
            $cgstKey = "CGST (" . number_format($cgstRate, 2) . "%)";
            $sgstKey = "SGST (" . number_format($sgstRate, 2) . "%)";
            $cgstAmount = $lineTax / 2;
            $sgstAmount = $lineTax / 2;
            
            // Add to summary
            if (!isset($taxSummary[$cgstKey])) {
                $taxSummary[$cgstKey] = 0;
            }
            if (!isset($taxSummary[$sgstKey])) {
                $taxSummary[$sgstKey] = 0;
            }
            $taxSummary[$cgstKey] += $cgstAmount;
            $taxSummary[$sgstKey] += $sgstAmount;
            
            $totalTax += $lineTax;
            
        } elseif ($taxType === 'igst') {
            $igstRate = $item['igst_rate'] > 0 ? $item['igst_rate'] : $effectiveTaxRate;
            $igstKey = "IGST (" . number_format($igstRate, 2) . "%)";
            
            // Add to summary
            if (!isset($taxSummary[$igstKey])) {
                $taxSummary[$igstKey] = 0;
            }
            $taxSummary[$igstKey] += $lineTax;
            
            $totalTax += $lineTax;
        } else {
            // For other GST types or manual GST
            $taxKey = $taxName . " (" . number_format($effectiveTaxRate, 2) . "%)";
            if (!isset($taxSummary[$taxKey])) {
                $taxSummary[$taxKey] = 0;
            }
            $taxSummary[$taxKey] += $lineTax;
            
            $totalTax += $lineTax;
        }
    }
}

$hasItems = ($itemCount > 0);

// Helper functions for column width calculation
function calculateProductColumnWidth($hasHsnCode, $hasQuantity, $hasUnit, $hasTax, $hasSellingPrice, $item_type) {
    $baseWidth = 35; // Reduced base width for product column
    
    if (!$hasHsnCode) $baseWidth += 8;
    if (!$hasQuantity) $baseWidth += 6;
    if (!$hasUnit || $item_type != 1) $baseWidth += 6;
    if (!$hasTax) $baseWidth += 8;
    if (!$hasSellingPrice) $baseWidth += 8;
    
    return $baseWidth;
}

function calculateAmountColumnWidth($hasHsnCode, $hasQuantity, $hasUnit, $hasTax, $hasSellingPrice, $item_type) {
    $baseWidth = 12; // Base width for amount column
    
    if (!$hasHsnCode) $baseWidth += 8;
    if (!$hasQuantity) $baseWidth += 6;
    if (!$hasUnit || $item_type != 1) $baseWidth += 6;
    if (!$hasTax) $baseWidth += 8;
    if (!$hasSellingPrice) $baseWidth += 8;
    
    return $baseWidth;
}

function calculateSellingPriceColumnWidth() {
    return 12; // Fixed width for selling price column
}

// Calculate column count dynamically
$colCount = 2; // Minimum columns: # and Product/Service

if ($hasHsnCodeData) {
    $colCount++; // HSN Code column
}

if ($hasQuantityData) {
    $colCount++; // QTY/Hours column
}

if ($hasUnitData && $item_type == 1) {
    $colCount++; // Unit column only for products
}

if ($hasSellingPriceData) {
    $colCount++; // Selling Price column
}

if ($hasTaxData && $showGSTColumn) {
    $colCount++; // Tax column
}

$colCount++; // Amount column (always shown)

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
    WHERE ci.org_id = '$org_id'
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

        .bold-text {
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

        .subtotal-box {
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

if (!empty($company['email'])) {
    $html .= '<div class="address-deatils-box"><span class="bold-text">Email:</span> ' . htmlspecialchars($company['email'] ?? '') . '</div>';
}

$html .= '</div>
                <div class="billing-to">
                    <div class="billing-title text-right">Billing To</div>';

// Client name/company name
if (!empty($client['company_name'])) {
    $html .= '<div class="to-title text-right">' . htmlspecialchars($client['company_name']) . '</div>';
}

// Client personal name
$client_name = '';
if (!empty($client['first_name']) || !empty($client['last_name'])) {
    $client_name = trim(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? ''));
}
if (!empty($client_name)) {
    $html .= '<div class="address-deatils-box text-right mb-0"><span class="bold-text">Client:</span> ' . htmlspecialchars($client_name) . '</div>';
}
                    
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
                    <tr style="background-color: #000; color: white;">
                        <th width="5%">#</th>
                        <th width="' . calculateProductColumnWidth($hasHsnCodeData, $hasQuantityData, $hasUnitData, $hasTaxData, $hasSellingPriceData, $item_type) . '%">Product/Service</th>';

// Only show HSN Code column if there\'s data
if ($hasHsnCodeData) {
    $html .= '<th width="8%">HSN Code</th>';
}

// Only show quantity column if there\'s data
if ($hasQuantityData) {
    $html .= '<th width="6%" class="text-center">' . ($item_type == 1 ? 'QTY' : 'Hours') . '</th>';
}

// Only show unit column if there\'s data AND item_type is product (1)
if ($hasUnitData && $item_type == 1) {
    $html .= '<th width="6%">Unit</th>';
}

// Only show selling price column if there\'s data
if ($hasSellingPriceData) {
    $html .= '<th width="' . calculateSellingPriceColumnWidth() . '%">Selling Price</th>';
}

// Only show tax column if GST is enabled
if ($showGSTColumn) {
    $html .= '<th width="8%">Tax</th>';
}

$html .= '<th width="' . calculateAmountColumnWidth($hasHsnCodeData, $hasQuantityData, $hasUnitData, $hasTaxData, $hasSellingPriceData, $item_type) . '%">Amount</th>
                    </tr>
                </thead>
                <tbody>';

// Add items to table
$i = 1;
foreach ($items as $item) {
    // Determine item name
    if (!empty($item['service_id'])) {
        $productName = !empty($item['service_product_name']) ? $item['service_product_name'] : '';
        $serviceName = !empty($item['service_name']) ? $item['service_name'] : '';
        $itemName = $productName . ($serviceName ? ' - ' . $serviceName : '');
    } else {
        $itemName = !empty($item['product_name']) ? $item['product_name'] : 'Product';
    }
    
    // Calculate amounts for display
    $itemAmount = $item['amount'];
    $effectiveTaxRate = $item['tax_rate'] ?? 0;
    
    if ($showGSTColumn) {
        if ($gstType === 'non_gst') {
            $effectiveTaxRate = 0;
            $baseAmount = $itemAmount; // For non-GST, amount is already without tax
        } else {
            if ($effectiveTaxRate > 0) {
                $baseAmount = $itemAmount / (1 + ($effectiveTaxRate / 100));
            } else {
                $baseAmount = $itemAmount;
            }
        }
    } else {
        $baseAmount = $itemAmount;
    }
    
    // Build tax display based on tax_type
    $taxDisplay = '';
    if ($showGSTColumn) {
        if ($gstType === 'non_gst') {
            $taxDisplay = 'Non-GST';
        } elseif ($effectiveTaxRate > 0) {
            if ($taxType === 'cgst_sgst') {
                $cgstRate = $item['cgst_rate'] > 0 ? $item['cgst_rate'] : ($effectiveTaxRate / 2);
                $sgstRate = $item['sgst_rate'] > 0 ? $item['sgst_rate'] : ($effectiveTaxRate / 2);
                $taxDisplay = "CGST " . number_format($cgstRate, 2) . "% + SGST " . number_format($sgstRate, 2) . "%";
            } elseif ($taxType === 'igst') {
                $igstRate = $item['igst_rate'] > 0 ? $item['igst_rate'] : $effectiveTaxRate;
                $taxDisplay = "IGST " . number_format($igstRate, 2) . "%";
            } else {
                $taxDisplay = ($item['tax_name'] ?? 'Tax') . " " . number_format($effectiveTaxRate, 2) . "%";
            }
        } else {
            $taxDisplay = 'No Tax';
        }
    }
    
    $html .= '<tr>
        <td>' . $i++ . '</td>
        <td>' . htmlspecialchars($itemName) . '</td>';
    
    // Only show HSN Code column if there\'s data
    if ($hasHsnCodeData) {
        $html .= '<td>' . htmlspecialchars($item['code'] ?? 'N/A') . '</td>';
    }
    
    // Only show quantity column if there\'s data
    if ($hasQuantityData) {
        $html .= '<td class="text-center">' . ($item['quantity'] ?? '0') . '</td>';
    }
    
    // Only show unit column if there\'s data AND item_type is product (1)
    if ($hasUnitData && $item_type == 1) {
        $html .= '<td>' . htmlspecialchars($item['unit_name'] ?? '') . '</td>';
    }
    
    // Only show selling price column if there\'s data
    if ($hasSellingPriceData) {
        $html .= '<td>' . htmlspecialchars($currencySymbol) . ' ' . number_format($item['selling_price'], 2) . '</td>';
    }
    
    // Only show tax column if GST is enabled
    if ($showGSTColumn) {
        $html .= '<td>' . $taxDisplay . '</td>';
    }
    
    $html .= '<td>' . htmlspecialchars($currencySymbol) . ' ' . number_format($baseAmount, 2) . '</td>
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
                <td class="subtotal-title">Subtotal:</td>
                <td class="subtotal-amount">' . htmlspecialchars($currencySymbol) . ' ' . number_format($subtotal, 2) . '</td>
            </tr>';
            
// Show tax rows only if GST is enabled and there are taxes
if ($showGSTColumn && !empty($taxSummary)) {
    foreach ($taxSummary as $taxLabel => $taxAmount) {
        if ($taxAmount > 0) {
            $html .= '<tr class="subtotal-box">
                        <td class="subtotal-title">' . $taxLabel . ':</td>
                        <td class="subtotal-amount">' . htmlspecialchars($currencySymbol) . ' ' . number_format($taxAmount, 2) . '</td>
                    </tr>';
        }
    }
}

// Show shipping charge if exists
if (!empty($invoice['shipping_charge']) && $invoice['shipping_charge'] > 0) {
    $html .= '<tr class="subtotal-box">
                <td class="subtitle-title">Shipping Charge:</td>
                <td class="subtotal-amount">' . htmlspecialchars($currencySymbol) . ' ' . number_format($invoice['shipping_charge'], 2) . '</td>
            </tr>';
}

$html .= '<tr class="subtotal-box">
                <td class="subtotal-title">Total:</td>
                <td class="subtotal-amount">' . htmlspecialchars($currencySymbol) . ' ' . number_format($invoice['total_amount'], 2) . '</td>
            </tr>
        </table>

        <div class="address-deatils-box text-right">
                <span class="bold-text">Total In Words:</span>
                 ' . numberToWords($invoice['total_amount']) . ' ' . htmlspecialchars($currencyName) . '
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