<?php
// print-invoice.php
session_start();
include '../config/config.php';

// Get current organization ID
$org_id = $_SESSION['org_id'] ?? 1;

// Function to get company currency - EXACTLY like your reference code
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

$companyCurrency = getCompanyCurrency($conn, $org_id);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Invalid Invoice ID.');
}

$invoiceId = intval($_GET['id']);

// Fetch invoice data - UPDATED QUERY TO GET tax_type
$invoice_result = mysqli_query($conn, "
    SELECT i.*, l.name AS salesperson_name
    FROM invoice i
    LEFT JOIN login l ON i.user_id = l.id
    WHERE i.id = $invoiceId AND i.is_deleted = 0 AND i.org_id = '$org_id'
");

$invoice = mysqli_fetch_assoc($invoice_result);

if (!$invoice) {
    die('Invoice not found.');
}

$client_id = $invoice['client_id'];
$bank_id = $invoice['bank_id'];
$item_type = $invoice['item_type'];

// Get tax_type from invoice
$taxType = $invoice['tax_type'] ?? 'non_gst';

// Check GST type
$gstType = $invoice['gst_type'] ?? 'gst';
$showGSTColumn = ($gstType !== 'non_gst' && $gstType !== null);

// Get currency symbol and name
$currencySymbol = $companyCurrency['currency_symbol'] ?? '$';
$currencyName = $companyCurrency['currency_name'] ?? 'US Dollar';

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

// Fetch items with CGST/SGST/IGST rates - UPDATED QUERY LIKE QUOTATION
$items_result = mysqli_query($conn, "
    SELECT ii.*, 
           p.name AS product_name,
           p.code AS product_code,
           s.name AS service_product_name,
           s.code AS service_code,
           COALESCE(p.code, s.code) AS code,
           t.name AS tax_name, 
           t.rate AS rate,
           u.name AS unit_name,
           ii.rate AS item_tax_rate,
           ii.cgst_rate,
           ii.sgst_rate,
           ii.igst_rate
    FROM invoice_item ii
    LEFT JOIN product p ON p.id = ii.product_id
    LEFT JOIN product s ON s.id = ii.service_id
    LEFT JOIN units u ON u.id = ii.unit_id
    LEFT JOIN tax t ON t.id = ii.tax_id
    WHERE ii.invoice_id = $invoiceId AND ii.is_deleted = 0
");

// Check column data
$hasItems = false;
$itemCount = 0;

// Store items in array first and check for data
$items = [];
$hasUnitData = false;
$hasQuantityData = false;
$hasTaxData = false;
$hasHsnCodeData = false;

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
    if ($showGSTColumn && (!empty($item['tax_name']) && trim($item['tax_name']) !== '') || 
        ($item['item_tax_rate'] > 0) || 
        ($item['cgst_rate'] > 0) || 
        ($item['sgst_rate'] > 0) || 
        ($item['igst_rate'] > 0)) {
        $hasTaxData = true;
    }
    
    // Check HSN code column
    if (!empty($item['code']) && trim($item['code']) !== '' && strtoupper($item['code']) !== 'N/A') {
        $hasHsnCodeData = true;
    }
}

$hasItems = ($itemCount > 0);

// Calculate subtotal and tax summary with CGST/SGST/IGST logic - UPDATED LIKE QUOTATION
$subtotal = 0;
$taxSummary = [];
$totalTax = 0;

foreach ($items as $item) {
    $itemAmount = $item['amount'];
    
    // Calculate tax for this item only if GST type is not non_gst
    if ($showGSTColumn) {
        $effectiveTaxRate = $item['item_tax_rate'] ?? $item['rate'] ?? 0;
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

// Reset items pointer for display
reset($items);

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

if ($showGSTColumn) {
    $colCount++; // Tax column
}

$colCount++; // Selling Price column
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
    $client_address_result = mysqli_query($conn, $client_address_query);
    $client_address = mysqli_fetch_assoc($client_address_result);
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

// Get logo path
$logoPath = '';
if (!empty($company['invoice_logo']) && file_exists('../uploads/' . $company['invoice_logo'])) {
    $logoPath = '../uploads/' . $company['invoice_logo'];
} elseif (!empty($company['company_logo']) && file_exists('../uploads/' . $company['company_logo'])) {
    $logoPath = '../uploads/' . $company['company_logo'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?= htmlspecialchars($invoice['invoice_id']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    <style>
        /* A4 Size and Margins - Exact PDF design */
        @page {
            size: A4;
            margin: 20mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #fff;
            font-family: "Instrument Sans", sans-serif;
            color: #000;
            font-size: 14px;
            line-height: 1.4;
            width: 210mm;
            min-height: 297mm;
            padding: 0;
            margin: 0;
        }

        .main-body {
            background-color: #fff;
            border: 1px solid #ccc;
            max-width: 170mm;
            margin: 0 auto;
            padding: 15px;
            min-height: 257mm;
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
        
        /* Print Specific Styles */
        @media print {
            body {
                width: 210mm;
                height: 297mm;
                padding: 0;
                margin: 0;
                background: white;
            }
            
            .main-body {
                border: none;
                padding: 0;
                max-width: 170mm;
                margin: 0 auto;
                min-height: auto;
            }
            
            .no-print {
                display: none !important;
            }
        }
        
        /* Screen styles */
        @media screen {
            body {
                background-color: #f5f5f5;
                padding: 20px;
            }
            
            .main-body {
                background: white;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                margin: 20px auto;
            }
            
            .print-button {
                display: block;
                margin: 20px auto;
                padding: 10px 20px;
                background: #007bff;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
                font-family: "Instrument Sans", sans-serif;
            }
        }
        
        /* Ensure table fits in A4 */
        .table {
            font-size: 12px;
        }
        
        .table th, .table td {
            padding: 4px;
        }
        
        /* Adjust column widths for new Selling Price column */
        .table th:nth-child(1) { width: 5%; } /* # */
        .table th:nth-child(2) { width: 25%; } /* Product/Service */
        .table th:nth-child(3) { width: 10%; } /* HSN Code */
        .table th:nth-child(4) { width: 8%; } /* QTY/Hours */
        .table th:nth-child(5) { width: 8%; } /* Unit */
        .table th:nth-child(6) { width: 15%; } /* Tax */
        .table th:nth-child(7) { width: 15%; } /* Selling Price */
        .table th:nth-child(8) { width: 14%; } /* Amount */
    </style>
    <script>
        // Auto-print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
        
        // Manual print button
        function manualPrint() {
            window.print();
        }
        
        // Close window after print
        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
            }, 1000);
        };
    </script>
</head>
<body>
    <div class="main-body">
        <table class="header-table">
            <tr>
                <td width="60%" style="vertical-align: middle;">
                    <?php if (!empty($logoPath)): ?>
                        <img src="<?= htmlspecialchars($logoPath) ?>" class="logo" alt="logo">
                    <?php else: ?>
                        <h2 style="margin:0; color: #2c3e50; font-family: 'Instrument Sans', sans-serif;">
                            <?= htmlspecialchars($company['name'] ?? 'Company Name') ?>
                        </h2>
                    <?php endif; ?>
                </td>
                <td width="40%" class="text-right" style="vertical-align: middle;">
                    <h1 class="invoice-title">INVOICE</h1>
                    
                </td>
            </tr>
        </table>

        <div class="invoice-top">
            <table width="100%">
                <tr>
                    <td width="50%">
                        <div class="tittle-text">Date:</div>
                        <div class="address-deatils-box"><?= htmlspecialchars($invoice['invoice_date']) ?></div>
                    </td>
                    <td width="50%" class="text-right">
                        <div class="tittle-text">Invoice No:</div>
                        <div class="address-deatils-box"><?= htmlspecialchars($invoice['invoice_id']) ?></div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="billing-container">
            <div class="billing-row">
                <div class="billing-from">
                    <div class="billing-title">Billing From</div>
                    <div class="to-title"><?= htmlspecialchars($company['name'] ?? '') ?></div>
                    
                    <?php if (!empty($company['address'])): ?>
                        <div class="address-deatils-box mb-0"><?= htmlspecialchars($company['address'] ?? '') ?></div>
                    <?php endif; ?>

                    <?php if (!empty($company['city_name']) || !empty($company['state_name']) || !empty($company['country_name']) || !empty($company['zipcode'])): ?>
                        <div class="address-deatils-box">
                            <?= htmlspecialchars($company['city_name'] ?? '') ?>, 
                            <?= htmlspecialchars($company['state_name'] ?? '') ?>, 
                            <?= htmlspecialchars($company['country_name'] ?? '') ?>, 
                            <?= htmlspecialchars($company['zipcode'] ?? '') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($company['email'])): ?>
                        <div class="address-deatils-box">
                            <span class="bold-text">Email:</span> <?= htmlspecialchars($company['email'] ?? '') ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="billing-to">
                    <div class="billing-title text-right">Billing To</div>
                    
                    <?php if (!empty($client['company_name'])): ?>
                        <div class="to-title text-right"><?= htmlspecialchars($client['company_name']) ?></div>
                    <?php endif; ?>

                    <?php 
                    $client_name = '';
                    if (!empty($client['first_name']) || !empty($client['last_name'])) {
                        $client_name = trim(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? ''));
                    }
                    if (!empty($client_name)): ?>
                        <div class="address-deatils-box text-right mb-0">
                            <span class="bold-text">Client:</span> <?= htmlspecialchars($client_name) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($client_address['billing_address1'])): ?>
                        <div class="address-deatils-box text-right mb-0">
                            <?= htmlspecialchars($client_address['billing_address1'] ?? '') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($client_address['city_name']) || !empty($client_address['state_name']) || !empty($client_address['country_name']) || !empty($client_address['billing_pincode'])): ?>
                        <div class="address-deatils-box text-right">
                            <?= htmlspecialchars($client_address['city_name'] ?? '') ?>, 
                            <?= htmlspecialchars($client_address['state_name'] ?? '') ?>, 
                            <?= htmlspecialchars($client_address['country_name'] ?? '') ?>, 
                            <?= htmlspecialchars($client_address['billing_pincode'] ?? '') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($client['email'])): ?>
                        <div class="address-deatils-box text-right">
                            <span class="bold-text">Email:</span> <?= htmlspecialchars($client['email'] ?? '') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <h4 class="billing-title">Product / Service Items</h4>
            <table class="table">
                <thead>
                    <tr style="background-color: #000; color: white;">
                        <th>#</th>
                        <th>Product/Service</th>
                        <th>HSN Code</th>
                        
                        <?php if ($hasQuantityData): ?>
                            <th class="text-center"><?= ($item_type == 1) ? 'QTY' : 'Hours' ?></th>
                        <?php endif; ?>
                        
                        <?php if ($hasUnitData && $item_type == 1): ?>
                            <th>Unit</th>
                        <?php endif; ?>
                        <th>Selling Price</th>
                        <?php if ($showGSTColumn): ?>
                            <th>Tax</th>
                        <?php endif; ?>
                        
                        
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($hasItems): ?>
                        <?php
                        $i = 1;
                        foreach ($items as $item):
                            // Determine item name
                            if (!empty($item['service_id'])) {
                                $productName = !empty($item['service_product_name']) ? $item['service_product_name'] : '';
                                $serviceName = !empty($item['service_name']) ? $item['service_name'] : '';
                                $itemName = $productName . ($serviceName ? ' - ' . $serviceName : '');
                            } else {
                                $itemName = !empty($item['product_name']) ? $item['product_name'] : 'Product';
                            }
                            
                            $effectiveTaxRate = $item['item_tax_rate'] ?? $item['rate'] ?? 0;
                            $taxName = $item['tax_name'] ?? 'Tax';
                            
                            // Get selling price from database
                            $sellingPrice = $item['selling_price'] ?? 0;
                            
                            // Calculate base amount (without tax) from the total amount
                            $itemAmount = $item['amount'];
                            
                            if ($showGSTColumn) {
                                if ($gstType === 'non_gst') {
                                    $effectiveTaxRate = 0;
                                    $baseAmount = $itemAmount; // For non-GST, amount is already without tax
                                } else {
                                    // Calculate base amount (without tax) from the total amount
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
                                        $taxDisplay = $taxName . " " . number_format($effectiveTaxRate, 2) . "%";
                                    }
                                } else {
                                    $taxDisplay = 'No Tax';
                                }
                            }
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($itemName) ?></td>
                            <td><?= htmlspecialchars($item['code'] ?? 'N/A') ?></td>
                            
                            <?php if ($hasQuantityData): ?>
                                <td class="text-center"><?= $item['quantity'] ?? '0' ?></td>
                            <?php endif; ?>
                            
                            <?php if ($hasUnitData && $item_type == 1): ?>
                                <td><?= htmlspecialchars($item['unit_name'] ?? '') ?></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($currencySymbol) ?> <?= number_format($sellingPrice, 2) ?></td>
                            
                            <?php if ($showGSTColumn): ?>
                                <td><?= $taxDisplay ?></td>
                            <?php endif; ?>
                            
                            <td><?= htmlspecialchars($currencySymbol) ?> <?= number_format($baseAmount, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $colCount ?>" style="text-align: center; padding: 20px;">No items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 15px;">
            <table width="100%">
                <tr>
                    <?php if ($showBankDetails): ?>
                        <td width="50%">
                            <div class="bank-details-section">
                                <h5 class="terms-conditions-title">Bank Details</h5>
                                
                                <?php if (!empty($bank['bank_name'])): ?>
                                    <div class="address-deatils-box">
                                        <span class="bold-text">Bank Name:</span>
                                        <?= htmlspecialchars($bank['bank_name']) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($bank['account_number'])): ?>
                                    <div class="address-deatils-box">
                                        <span class="bold-text">A/C No:</span>
                                        <?= htmlspecialchars($bank['account_number']) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($bank['ifsc_code'])): ?>
                                    <div class="address-deatils-box">
                                        <span class="bold-text">IFSC Code:</span>
                                        <?= htmlspecialchars($bank['ifsc_code']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <?php $totalsWidth = '50%'; ?>
                    <?php else: ?>
                        <?php $totalsWidth = '100%'; ?>
                    <?php endif; ?>
                    
                    <td width="<?= $totalsWidth ?>" style="vertical-align: top; text-align: right;">
                        <table style="width:100%;">
                            <tr class="subtotal-box">
                                <td class="subtotal-title">Subtotal:</td>
                                <td class="subtotal-amount"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($subtotal, 2) ?></td>
                            </tr>
                            
                            <?php if ($showGSTColumn && !empty($taxSummary)): ?>
                                <?php foreach ($taxSummary as $taxLabel => $taxAmount): ?>
                                    <?php if ($taxAmount > 0): ?>
                                        <tr class="subtotal-box">
                                            <td class="subtotal-title"><?= $taxLabel ?>:</td>
                                            <td class="subtotal-amount"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($taxAmount, 2) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (!empty($invoice['shipping_charge']) && $invoice['shipping_charge'] > 0): ?>
                                <tr class="subtotal-box">
                                    <td class="subtotal-title">Shipping Charge:</td>
                                    <td class="subtotal-amount"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($invoice['shipping_charge'], 2) ?></td>
                                </tr>
                            <?php endif; ?>

                            <tr class="subtotal-box">
                                <td class="subtotal-title">Total:</td>
                                <td class="subtotal-amount"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($invoice['total_amount'], 2) ?></td>
                            </tr>
                        </table>

                        <div class="address-deatils-box text-right">
                            <span class="bold-text">Total In Words:</span>
                            <?= numberToWords($invoice['total_amount']) ?> <?= htmlspecialchars($currencyName) ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <?php if ($showNotes): ?>
            <div class="terms-conditions">
                <p class="terms-conditions-title">Notes:</p>
                <p><?= nl2br(htmlspecialchars($invoice['invoice_note'])) ?></p>
            </div>
        <?php endif; ?>
                    
        <?php if ($showTerms): ?>
            <div class="terms-conditions" style="margin-top: <?= $showNotes ? '10' : '0' ?>px;">
                <p class="terms-conditions-title">Terms & Conditions:</p>
                <p><?= nl2br(htmlspecialchars($invoice['description'])) ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Print Button (for manual printing) -->
        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button class="print-button" onclick="manualPrint()">Print Invoice</button>
            <button class="print-button" onclick="window.close()" style="background: #6c757d; margin-left: 10px;">Close Window</button>
        </div>
    </div>
</body>
</html>