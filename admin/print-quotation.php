<?php
// print-quotation.php
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
        'currency_symbol' => '€',
        'currency_name' => 'Euro',
        'isocode' => 'EUR'
    ];
}

$companyCurrency = getCompanyCurrency($conn, $org_id);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Invalid Quotation ID.');
}

$quotationId = intval($_GET['id']);

// Fetch quotation info
$sql = "SELECT q.*, c.first_name, c.last_name, c.email, c.customer_image, c.company_name, c.phone_number 
        FROM quotation q
        LEFT JOIN client c ON q.client_id = c.id
        WHERE q.id = $quotationId AND q.org_id = '$org_id'";
$result = mysqli_query($conn, $sql);
$quotation = mysqli_fetch_assoc($result);

if (!$quotation) {
    die('Quotation not found.');
}

// Check GST type
$gstType = $quotation['gst_type'] ?? 'gst';
$showGSTColumn = ($gstType !== 'non_gst' && $gstType !== null);

// Fetch items
$items_result = mysqli_query($conn, "
    SELECT 
        qi.*, 
        p.name AS product_name,
        p.code AS product_code,
        s.name AS service_name_from_product,
        s.code AS service_code,
        COALESCE(p.code, s.code) AS code,
        t.name AS tax_name, 
        t.rate AS tax_rate,
        qi.service_name,
        qi.rate AS item_tax_rate
    FROM quotation_item qi
    LEFT JOIN product p ON p.id = qi.product_id
    LEFT JOIN product s ON s.id = qi.service_id
    LEFT JOIN tax t ON t.id = qi.tax_id
    WHERE qi.quotation_id = $quotationId AND qi.is_deleted = 0
");

// Check if any item has quantity value
$showQuantityColumn = false;
mysqli_data_seek($items_result, 0);
while ($item = mysqli_fetch_assoc($items_result)) {
    if (!is_null($item['quantity']) && $item['quantity'] > 0) {
        $showQuantityColumn = true;
        break;
    }
}
mysqli_data_seek($items_result, 0);

// Check if notes are available
$showNotes = !empty($quotation['client_note']);
$showTerms = !empty($quotation['description']);

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

// Fetch client address
$client_address = null;
if (!empty($quotation['client_id'])) {
    $client_address_query = "
        SELECT ca.*, 
               co.name AS country_name, 
               s.name AS state_name, 
               ci.name AS city_name
        FROM client_address ca
        LEFT JOIN countries co ON co.id = ca.billing_country
        LEFT JOIN states s ON s.id = ca.billing_state
        LEFT JOIN cities ci ON ci.id = ca.billing_city
        WHERE ca.client_id = {$quotation['client_id']}
        LIMIT 1
    ";
    $client_address_result = mysqli_query($conn, $client_address_query);
    $client_address = mysqli_fetch_assoc($client_address_result);
}

// Get currency symbol and name
$currencySymbol = $companyCurrency['currency_symbol'] ?? '€';
$currencyName = $companyCurrency['currency_name'] ?? 'Euro';

// Calculate subtotal and tax summary
$subtotal = 0;
$taxSummary = [];
mysqli_data_seek($items_result, 0);
while ($item = mysqli_fetch_assoc($items_result)) {
    $itemAmount = $item['amount'];
    $subtotal += $itemAmount;
    
    // Calculate tax for this item only if GST type is not non_gst
    if ($showGSTColumn) {
        $effectiveTaxRate = $item['item_tax_rate'] ?? $item['tax_rate'] ?? 0;
        $taxName = $item['tax_name'] ?? 'Tax';
        
        if ($effectiveTaxRate > 0) {
            $lineTax = ($itemAmount * $effectiveTaxRate) / 100;
            $taxKey = $taxName . ' (' . $effectiveTaxRate . '%)';
            
            // Add to summary
            if (!isset($taxSummary[$taxKey])) {
                $taxSummary[$taxKey] = 0;
            }
            $taxSummary[$taxKey] += $lineTax;
        }
    }
}
mysqli_data_seek($items_result, 0);

$totalTax = array_sum($taxSummary);

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
    <title>Quotation - <?= htmlspecialchars($quotation['quotation_id']) ?></title>
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
                    <h1 class="invoice-title">QUOTATION</h1>
                </td>
            </tr>
        </table>

        <div class="invoice-top">
            <table width="100%">
                <tr>
                    <td width="50%">
                        <div class="tittle-text">Date:</div>
                        <div class="address-deatils-box"><?= htmlspecialchars($quotation['quotation_date']) ?></div>
                    </td>
                    <td width="50%" class="text-right">
                        <div class="tittle-text">Quotation No:</div>
                        <div class="address-deatils-box"><?= htmlspecialchars($quotation['quotation_id']) ?></div>
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
                    
                    <?php if (!empty($quotation['company_name'])): ?>
                        <div class="to-title text-right"><?= htmlspecialchars($quotation['company_name']) ?></div>
                    <?php endif; ?>

                    <?php 
                    $client_name = '';
                    if (!empty($quotation['first_name']) || !empty($quotation['last_name'])) {
                        $client_name = trim(($quotation['first_name'] ?? '') . ' ' . ($quotation['last_name'] ?? ''));
                    }
                    if (!empty($client_name)): ?>
                        <div class="address-deatils-box text-right">
                            <span class="bold-text">Client:</span> <?= htmlspecialchars($client_name) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($client_address['billing_address1'])): ?>
                        <div class="address-deatils-box mb-0 text-right">
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

                    <?php if (!empty($quotation['email'])): ?>
                        <div class="address-deatils-box text-right">
                            <span class="bold-text">Email:</span> <?= htmlspecialchars($quotation['email'] ?? '') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mb-3">
           <h4 class="billing-title">Product / Service Items</h4>
            <table class="table">
                <thead>
                    <tr class="bg-light">
                        <th width="5%">#</th>
                        <th width="35%">Product/Service</th>
                        <th width="15%">HSN Code</th>
                        
                        <?php if ($showQuantityColumn): ?>
                            <th width="10%" class="text-center">QTY</th>
                        <?php endif; ?>

                        <?php if ($showGSTColumn): ?>
                            <th width="15%">Tax</th>
                        <?php endif; ?>
                        
                        <th width="15%">Selling Price</th>
                        <th width="10%">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    mysqli_data_seek($items_result, 0);
                    while ($item = mysqli_fetch_assoc($items_result)) {
                        // Combine product name (from product table) and service name (from quotation_item)
                        if (!empty($item['service_id'])) {
                            $productName = !empty($item['service_name_from_product']) ? $item['service_name_from_product'] : '';
                            $serviceName = !empty($item['service_name']) ? $item['service_name'] : '';
                            $itemName = $productName . ($serviceName ? ' - ' . $serviceName : '');
                        } else {
                            $itemName = !empty($item['product_name']) ? $item['product_name'] : 'Product';
                        }
                    ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($itemName) ?></td>
                        <td><?= htmlspecialchars($item['code'] ?? 'N/A') ?></td>
                        
                        <?php if ($showQuantityColumn): ?>
                            <td class="text-center"><?= $item['quantity'] ?></td>
                        <?php endif; ?>
                        
                        <?php if ($showGSTColumn): ?>
                            <?php
                            $effectiveTaxRate = $item['item_tax_rate'] ?? $item['tax_rate'] ?? 0;
                            $taxName = $item['tax_name'] ?? 'Tax';
                            ?>
                            <td><?= $taxName ?><?= $effectiveTaxRate > 0 ? ' (' . $effectiveTaxRate . '%)' : '' ?></td>
                        <?php endif; ?>
                        
                        <td><?= htmlspecialchars($currencySymbol) ?> <?= number_format($item['selling_price'], 2) ?></td>
                        <td><?= htmlspecialchars($currencySymbol) ?> <?= number_format($item['amount'], 2) ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div style="border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 15px;">
            <table width="100%">
                <tr>
                    <td width="50%" style="vertical-align: top;"></td>
                    <td width="50%" style="vertical-align: top; text-align: right;">
                        <table style="width:100%;">
                            <tr class="subtotal-box">
                                <td class="subtotal-title">Sub Amount:</td>
                                <td class="subtotal-amount"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($subtotal, 2) ?></td>
                            </tr>
                            
                            <?php if ($showGSTColumn): ?>
                                <?php if (!empty($taxSummary)): ?>
                                    <?php foreach ($taxSummary as $taxLabel => $taxAmount): ?>
                                        <tr class="subtotal-box">
                                            <td class="subtotal-title"><?= $taxLabel ?>:</td>
                                            <td class="subtotal-amount"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($taxAmount, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (!empty($quotation['shipping_charge']) && $quotation['shipping_charge'] > 0): ?>
                                <tr class="subtotal-box">
                                    <td class="subtotal-title">Shipping Charge:</td>
                                    <td class="subtotal-amount"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($quotation['shipping_charge'], 2) ?></td>
                                </tr>
                            <?php endif; ?>

                            <tr class="subtotal-box">
                                <td class="subtotal-title">Total:</td>
                                <td class="subtotal-amount"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($quotation['total_amount'], 2) ?></td>
                            </tr>
                        </table>

                        <div class="address-deatils-box text-right">
                            <span class="bold-text">Total In Words:</span>
                            <?= numberToWords($quotation['total_amount']) ?> <?= htmlspecialchars($currencyName) ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <?php if ($showNotes): ?>
            <div class="terms-conditions">
                <p class="terms-conditions-title">Notes:</p>
                <p><?= nl2br(htmlspecialchars($quotation['client_note'])) ?></p>
            </div>
        <?php endif; ?>
                    
        <?php if ($showTerms): ?>
            <div class="terms-conditions">
                <p class="terms-conditions-title">Terms & Conditions</p>
                <p><?= nl2br(htmlspecialchars($quotation['description'])) ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Print Button (for manual printing) -->
        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button class="print-button" onclick="manualPrint()">Print Quotation</button>
            <button class="print-button" onclick="window.close()" style="background: #6c757d; margin-left: 10px;">Close Window</button>
        </div>
    </div>
</body>
</html>