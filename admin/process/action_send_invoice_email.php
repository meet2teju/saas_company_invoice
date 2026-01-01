<?php
session_start();
include '../../config/config.php';

// Use Composer autoloader
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

// Add the numberToWords function (same as quotation)
function numberToWords($number) {
    $ones = array(
        0 => "Zero", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four",
        5 => "Five", 6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine",
        10 => "Ten", 11 => "Eleven", 12 => "Twelve", 13 => "Thirteen", 
        14 => "Fourteen", 15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen", 
        18 => "Eighteen", 19 => "Nineteen"
    );
    
    $tens = array(
        2 => "Twenty", 3 => "Thirty", 4 => "Forty", 5 => "Fifty",
        6 => "Sixty", 7 => "Seventy", 8 => "Eighty", 9 => "Ninety"
    );
    
    if ($number < 20) {
        return $ones[$number];
    }
    
    if ($number < 100) {
        return $tens[floor($number / 10)] . (($number % 10 != 0) ? " " . $ones[$number % 10] : "");
    }
    
    if ($number < 1000) {
        return $ones[floor($number / 100)] . " Hundred" . (($number % 100 != 0) ? " " . numberToWords($number % 100) : "");
    }
    
    if ($number < 100000) {
        return numberToWords(floor($number / 1000)) . " Thousand" . (($number % 1000 != 0) ? " " . numberToWords($number % 1000) : "");
    }
    
    if ($number < 10000000) {
        return numberToWords(floor($number / 100000)) . " Lakh" . (($number % 100000 != 0) ? " " . numberToWords($number % 100000) : "");
    }
    
    return numberToWords(floor($number / 10000000)) . " Crore" . (($number % 10000000 != 0) ? " " . numberToWords($number % 10000000) : "");
}

// Function to get company currency
function getCompanyCurrency($conn, $org_id) {
    $sql = "SELECT currency_symbol_id FROM company_info WHERE org_id = '$org_id' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $companyInfo = mysqli_fetch_assoc($result);
        $currency_id = $companyInfo['currency_symbol_id'] ?? null;
        
        if ($currency_id) {
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
    
    $sql = "SELECT currency_symbol, currency_name, isocode 
            FROM currency 
            LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return [
        'currency_symbol' => '$',
        'currency_name' => 'US Dollar',
        'isocode' => 'USD'
    ];
}

// Function to generate PDF - WITH GST LOGIC INTEGRATED AND SELLING PRICE COLUMN
function generateInvoicePDF($conn, $invoice_id, $invoice, $company, $client_address, $items, $client) {
    // Get organization ID from invoice
    $org_id = $invoice['org_id'] ?? 1;
    
    // Get company currency
    $companyCurrency = getCompanyCurrency($conn, $org_id);
    $currencySymbol = $companyCurrency['currency_symbol'] ?? '$';
    $currencyName = $companyCurrency['currency_name'] ?? 'US Dollar';
    
    // Get tax_type from invoice (similar to quotation)
    $taxType = $invoice['tax_type'] ?? 'non_gst';
    
    // Check GST type
    $gstType = $invoice['gst_type'] ?? 'gst';
    $showGSTColumn = ($gstType !== 'non_gst' && $gstType !== null);
    
    // Check item type
    $item_type = $invoice['item_type'];
    
    // Fetch bank details
    $bank = null;
    $showBankDetails = false;
    if (!empty($invoice['bank_id'])) {
        $bank_result = mysqli_query($conn, "SELECT * FROM bank WHERE id = {$invoice['bank_id']}");
        $bank = mysqli_fetch_assoc($bank_result);
        if ($bank && (!empty($bank['bank_name']) || !empty($bank['account_number']) || !empty($bank['ifsc_code']))) {
            $showBankDetails = true;
        }
    }
    
    // Check various column data
    $hasQuantityData = false;
    $hasUnitData = false;
    $hasHsnCodeData = false;
    $hasTaxData = false;
    
    // Store items in array first to check for data and calculate taxes with GST logic
    $items_array = [];
    $taxSummary = [];
    $subtotal = 0;
    $totalTax = 0;
    
    mysqli_data_seek($items, 0);
    while ($item = mysqli_fetch_assoc($items)) {
        $items_array[] = $item;
        
        if (!is_null($item['quantity']) && $item['quantity'] > 0) {
            $hasQuantityData = true;
        }
        
        if (!empty($item['unit_name']) && trim($item['unit_name']) !== '') {
            $hasUnitData = true;
        }
        
        if (!empty($item['code']) && trim($item['code']) !== '' && strtoupper($item['code']) !== 'N/A') {
            $hasHsnCodeData = true;
        }
        
        if ($showGSTColumn && !empty($item['tax_name']) && trim($item['tax_name']) !== '') {
            $hasTaxData = true;
        }
        
        // GST LOGIC: Calculate tax for this item based on GST type
        $itemAmount = $item['amount']; // This is the total amount including tax
        
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
        
        // Store base amount (without tax) for display in amount column
        $items_array[count($items_array) - 1]['base_amount'] = $baseAmount;
        $items_array[count($items_array) - 1]['line_tax'] = $lineTax;
        
        // Add base amount to subtotal (without tax)
        $subtotal += $baseAmount;
        
        // Build tax label based on tax_type (GST LOGIC)
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
    mysqli_data_seek($items, 0);
    
    // Check if we should show notes and terms
    $showNotes = !empty($invoice['invoice_note']);
    $showTerms = !empty($invoice['description']);
    
    // Get absolute path for logo
    function getAbsolutePath($relativePath) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $protocol . "://" . $host;
        
        $currentDir = dirname($_SERVER['SCRIPT_NAME']);
        return $baseUrl . $currentDir . '/' . $relativePath;
    }
    
    // Get logo path
    $logoPath = '';
    if (!empty($company['invoice_logo']) && file_exists('../../uploads/' . $company['invoice_logo'])) {
        $logoPath = getAbsolutePath('../../uploads/' . $company['invoice_logo']);
    } elseif (!empty($company['company_logo']) && file_exists('../../uploads/' . $company['company_logo'])) {
        $logoPath = getAbsolutePath('../../uploads/' . $company['company_logo']);
    }
    
    // Build client name
    $client_name = '';
    if (!empty($client['first_name']) || !empty($client['last_name'])) {
        $client_name = trim(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? ''));
    }
    
    // Calculate column widths dynamically
    function calculateProductColumnWidth($hasHsnCode, $hasQuantity, $hasUnit, $hasTax, $item_type, $showGSTColumn) {
        $baseWidth = 35;
        if (!$hasHsnCode) $baseWidth += 10;
        if (!$hasQuantity) $baseWidth += 8;
        if (!$hasUnit || $item_type != 1) $baseWidth += 8;
        if (!$hasTax && $showGSTColumn) $baseWidth += 12;
        return $baseWidth;
    }
    
    function calculateAmountColumnWidth($hasHsnCode, $hasQuantity, $hasUnit, $hasTax, $item_type, $showGSTColumn) {
        $baseWidth = 15;
        if (!$hasHsnCode) $baseWidth += 10;
        if (!$hasQuantity) $baseWidth += 8;
        if (!$hasUnit || $item_type != 1) $baseWidth += 8;
        if (!$hasTax && $showGSTColumn) $baseWidth += 12;
        return $baseWidth;
    }
    
    // Start building HTML - WITH SELLING PRICE COLUMN
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
    
    if (!empty($company['email'])) {
        $html .= '<div class="address-deatils-box"><span class="bold-text">Email:</span> ' . htmlspecialchars($company['email'] ?? '') . '</div>';
    }
    
    $html .= '</div>
                    <div class="billing-to">
                        <div class="billing-title text-right">Billing To</div>';
                        
    if (!empty($client['company_name'])) {
        $html .= '<div class="to-title text-right">' . htmlspecialchars($client['company_name']) . '</div>';
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
                            <th width="' . calculateProductColumnWidth($hasHsnCodeData, $hasQuantityData, $hasUnitData, $hasTaxData, $item_type, $showGSTColumn) . '%">Product/Service</th>';
    
    if ($hasHsnCodeData) {
        $html .= '<th width="10%">HSN Code</th>';
    }
    
    if ($hasQuantityData) {
        $html .= '<th width="8%" class="text-center">' . ($item_type == 1 ? 'QTY' : 'Hours') . '</th>';
    }
    
    if ($hasUnitData && $item_type == 1) {
        $html .= '<th width="8%">Unit</th>';
    }
    
    $html .= '<th width="15%">Selling Price</th>';
    
    if ($hasTaxData && $showGSTColumn) {
        $html .= '<th width="12%">Tax</th>';
    }
    
    $html .= '<th width="' . calculateAmountColumnWidth($hasHsnCodeData, $hasQuantityData, $hasUnitData, $hasTaxData, $item_type, $showGSTColumn) . '%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>';
    
    $i = 1;
    foreach ($items_array as $item) {
        // Determine item name
        if (!empty($item['service_id'])) {
            $productName = !empty($item['service_product_name']) ? $item['service_product_name'] : '';
            $serviceName = !empty($item['service_name']) ? $item['service_name'] : '';
            $itemName = $productName . ($serviceName ? ' - ' . $serviceName : '');
        } else {
            $itemName = !empty($item['product_name']) ? $item['product_name'] : 'Product';
        }
        
        // Calculate unit rate (selling price per unit)
        $baseAmount = $item['base_amount'] ?? $item['amount'];
        
        if ($hasQuantityData && !empty($item['quantity']) && $item['quantity'] > 0) {
            $unitRate = $baseAmount / $item['quantity'];
        } else {
            $unitRate = $baseAmount;
        }
        
        // GST LOGIC: Build tax display based on tax_type
        $taxDisplay = '';
        if ($hasTaxData && $showGSTColumn) {
            $effectiveTaxRate = $item['tax_rate'] ?? 0;
            $taxName = $item['tax_name'] ?? 'Tax';
            
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
        
        $html .= '<tr>
            <td>' . $i++ . '</td>
            <td>' . htmlspecialchars($itemName) . '</td>';
        
        if ($hasHsnCodeData) {
            $html .= '<td>' . htmlspecialchars($item['code'] ?? 'N/A') . '</td>';
        }
        
        if ($hasQuantityData) {
            $html .= '<td class="text-center">' . ($item['quantity'] ?? '0') . '</td>';
        }
        
        if ($hasUnitData && $item_type == 1) {
            $html .= '<td>' . htmlspecialchars($item['unit_name'] ?? '') . '</td>';
        }
        
        // Rate column (price per unit BEFORE tax)
        $html .= '<td>' . htmlspecialchars($currencySymbol) . ' ' . number_format($unitRate, 2) . '</td>';
        
        if ($hasTaxData && $showGSTColumn) {
            $html .= '<td>' . $taxDisplay . '</td>';
        }
        
        // Amount column (total BEFORE tax)
        $html .= '<td>' . htmlspecialchars($currencySymbol) . ' ' . number_format($baseAmount, 2) . '</td>
        </tr>';
    }
    
    $html .= '</tbody>
                </table>
            </div>

            <div style="border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 15px;">
                <table width="100%">
                    <tr>';

    if ($showBankDetails) {
        $html .= '<td width="50%">
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
        $html .= '<td width="50%"></td>';
    }

    $html .= '<td width="' . $totalsWidth . '" style="vertical-align: top; text-align: right;">
            <table style="width:100%;">
                <tr class="subtotal-box">
                    <td class="subtotal-title">Subtotal:</td>
                    <td class="subtotal-amount">' . htmlspecialchars($currencySymbol) . ' ' . number_format($subtotal, 2) . '</td>
                </tr>';
                
    // GST LOGIC: Show tax rows only if GST is enabled and there are taxes
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

    if (!empty($invoice['shipping_charge']) && $invoice['shipping_charge'] > 0) {
        $html .= '<tr class="subtotal-box">
                    <td class="subtotal-title">Shipping Charge:</td>
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
    
    // Add Notes section if exists
    if ($showNotes) {
        $html .= '<div class="terms-conditions">
                <p class="terms-conditions-title">Notes:</p>
                <p>' . htmlspecialchars($invoice['invoice_note']) . '</p>
            </div>';
    }
    
    // Add Terms & Conditions section if exists
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
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    
    $dompdf = new Dompdf($options);
    
    // Load HTML content
    $dompdf->loadHtml($html);
    
    // Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait');
    
    // Render PDF
    $dompdf->render();
    
    // Return PDF content
    return $dompdf->output();
}

// Function to generate invoice email template
function generateInvoiceEmailTemplate($clientName, $company, $invoice, $invoiceDate) {
    // Get company contact details
    $companyPhone = !empty($company['mobile_number']) ? $company['mobile_number'] : '';
    $companyWebsite = !empty($company['website']) ? $company['website'] : '';
    $companyEmail = !empty($company['email']) ? $company['email'] : 'info@' . strtolower(str_replace(' ', '', $company['name'])) . '.com';
    
    // Get primary contact person
    $contactPerson = $company['name'] . ' Team';
    $designation = 'Accounts Department';
    
    // Prepare logo HTML
    $logoHtml = '';
    if (!empty($company['company_logo']) && file_exists('../../uploads/' . $company['company_logo'])) {
        $logoPath = '../../uploads/' . $company['company_logo'];
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoMime = mime_content_type($logoPath);
        $logoHtml = '<img src="data:' . $logoMime . ';base64,' . $logoData . '" alt="' . htmlspecialchars($company['name']) . ' Logo" width="150" style="display:block;">';
    } else {
        $logoHtml = '<h2 style="margin:0; font-size:24px; color:#000000;">' . htmlspecialchars($company['name']) . '</h2>';
    }
    
    // EMAIL TEMPLATE
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8">
    <title>Invoice #' . htmlspecialchars($invoice['invoice_id']) . ' from ' . htmlspecialchars($company['name']) . '</title>
    <style>
        body { 
            margin:0; 
            padding:0; 
            background-color:#f4f6f8; 
            font-family: Arial, Helvetica, sans-serif;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .content {
            padding: 0 40px 20px;
            font-size: 16px;
            color: #333333;
            line-height: 1.6;
        }
        .footer {
            padding: 20px 40px;
            font-size: 14px;
            color: #666666;
            border-top: 1px solid #e5e7eb;
            background-color: #f8f9fa;
        }
        .attachment-box {
            background-color: #f0f9ff;
            border-radius: 6px;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
    </head>
    <body>
     
      <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:40px 0;">
    <tr>
    <td align="center">
     
            <!-- Email Container -->
    <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
     
              <!-- Logo Section -->
    <tr>
    <td align="center" style="padding:30px 40px 10px;">
    ' . $logoHtml . '
    </td>
    </tr>
     
              <!-- Divider -->
    <tr>
    <td style="padding:15px 40px;">
    <hr style="border:none; border-top:1px solid #e5e7eb;">
    </td>
    </tr>
     
              <!-- Main Content -->
    <tr>
    <td style="padding:0 40px 20px; font-size:16px; color:#333333; line-height:1.6;">
                  Dear ' . htmlspecialchars($clientName) . ',<br><br>
                  
                  I hope you are doing well.<br><br>
                  
                  Please find attached the invoice PDF for your reference and payment. The invoice includes complete details of the products/services provided.<br><br>
                  
                  Kindly review the attached invoice and proceed with the payment as per the terms. Please let us know if you have any questions or require clarification.<br><br>
                  
                  Looking forward to your payment confirmation.<br><br>
    </td>
    </tr>

              <!-- Footer Section - INCLUDING BEST REGARDS -->
    <tr>
    <td style="padding:20px 40px 30px; font-size:14px; color:#666666; background-color:#f8f9fa;">
    <strong>Best regards,</strong><br>
    ' . htmlspecialchars($contactPerson) . '<br>
    ' . htmlspecialchars($designation) . '<br>
    <strong>' . htmlspecialchars($company['name']) . '</strong><br>';
    
    if (!empty($companyPhone)) {
        $html .= 'Phone: ' . htmlspecialchars($companyPhone) . '<br>';
    }
    
    if (!empty($companyWebsite)) {
        $html .= 'Website: <a href="' . htmlspecialchars($companyWebsite) . '" style="color:#2563eb; text-decoration:none;">' . htmlspecialchars($companyWebsite) . '</a><br>';
    }
    
    $html .= 'Email: <a href="mailto:' . htmlspecialchars($companyEmail) . '" style="color:#2563eb; text-decoration:none;">' . htmlspecialchars($companyEmail) . '</a><br><br>
    
    <div style="color:#9ca3af; font-size:13px; border-top:1px solid #e5e7eb; padding-top:10px;">
    © ' . date('Y') . ' ' . htmlspecialchars($company['name']) . '. All rights reserved.
    </div>
    </td>
    </tr>
     
            </table>
    <!-- End Container -->
     
          </td>
    </tr>
    </table>
     
    </body>
    </html>';
    
    return $html;
}

// Main function to send invoice email with PDF attachment
function sendInvoiceMail($conn, $clientEmail, $clientName, $invoice, $items, $company, $client_address, $client) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'daxachudasmaoe@gmail.com';
        $mail->Password   = 'jhkg aneq xyhh emfm';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Format date
        $invoiceDate = date('d F Y', strtotime($invoice['invoice_date']));

        // Recipients
        $mail->setFrom('maniyamansioe@gmail.com', $company['name']);
        $mail->addAddress($clientEmail, $clientName);
        $mail->addReplyTo('maniyamansioe@gmail.com', $company['name'] . ' Support');

        // Generate PDF - WITH GST LOGIC INTEGRATED AND SELLING PRICE COLUMN
        $pdfContent = generateInvoicePDF($conn, $invoice['id'], $invoice, $company, $client_address, $items, $client);
        
        // Add PDF attachment
        $mail->addStringAttachment($pdfContent, 'Invoice_' . $invoice['invoice_id'] . '.pdf', 'base64', 'application/pdf');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Invoice #' . $invoice['invoice_id'] . ' from ' . $company['name'];
        
        // Generate email body
        $mail->Body = generateInvoiceEmailTemplate($clientName, $company, $invoice, $invoiceDate);
        
        // Plain text version
        $mail->AltBody = "Dear " . $clientName . ",\n\n" .
                        "I hope you are doing well.\n\n" .
                        "Please find attached the invoice PDF for your reference and payment. The invoice includes complete details of the products/services provided.\n\n" .
                        "Kindly review the attached invoice and proceed with the payment as per the terms. Please let us know if you have any questions or require clarification.\n\n" .
                        "Looking forward to your payment confirmation.\n\n" .
                        "Best regards,\n" .
                        $company['name'] . " Team\n" .
                        "Accounts Department\n" .
                        (!empty($company['mobile_number']) ? "Phone: " . $company['mobile_number'] . "\n" : "") .
                        (!empty($company['email']) ? "Email: " . $company['email'] . "\n" : "");

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Invoice Mailer Error: " . $e->getMessage());
        return false;
    }
}

if ((isset($_GET['invoice_id']) || isset($_POST['invoice_id']))) {
    $invoice_id = isset($_POST['invoice_id']) ? (int)$_POST['invoice_id'] : (int)$_GET['invoice_id'];

    // Validate invoice ID
    if ($invoice_id <= 0) {
        $_SESSION['message'] = 'Invalid invoice ID';
        $_SESSION['message_type'] = 'error';
        header('Location: ../invoice-details.php');
        exit;
    }

    // Fetch invoice with prepared statement - INCLUDING GST FIELDS
    $query = "SELECT i.*, i.tax_type, i.gst_type FROM invoice i WHERE i.id = ? AND i.is_deleted = 0";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $invoice_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $invoice = mysqli_fetch_assoc($result);
        
        if (!$invoice) {
            $_SESSION['message'] = 'Invoice not found or has been deleted';
            $_SESSION['message_type'] = 'error';
            header('Location: ../invoice-details.php');
            exit;
        }

        // Fetch company info
        $company_query = "SELECT ci.*, 
                                 co.name AS country_name,
                                 s.name AS state_name,
                                 c.name AS city_name
                          FROM company_info ci
                          LEFT JOIN countries co ON co.id = ci.country_id
                          LEFT JOIN states s ON s.id = ci.state_id
                          LEFT JOIN cities c ON c.id = ci.city_id
                          WHERE ci.org_id = '{$invoice['org_id']}'
                          LIMIT 1";
        $company_result = mysqli_query($conn, $company_query);
        $company = mysqli_fetch_assoc($company_result);

        if (!$company) {
            $_SESSION['message'] = 'Company information not found';
            $_SESSION['message_type'] = 'error';
            header('Location: ../invoice-details.php?id=' . $invoice_id);
            exit;
        }

        // Fetch client with prepared statement
        $client_query = "SELECT * FROM client WHERE id = ?";
        $client_stmt = mysqli_prepare($conn, $client_query);
        
        if ($client_stmt) {
            mysqli_stmt_bind_param($client_stmt, "i", $invoice['client_id']);
            mysqli_stmt_execute($client_stmt);
            $client_result = mysqli_stmt_get_result($client_stmt);
            $client = mysqli_fetch_assoc($client_result);
            
            if (!$client) {
                $_SESSION['message'] = 'Client not found';
                $_SESSION['message_type'] = 'error';
                header('Location: ../invoice-details.php?id=' . $invoice_id);
                exit;
            }

            if (empty($client['email'])) {
                $_SESSION['message'] = 'Client email address not found';
                $_SESSION['message_type'] = 'error';
                header('Location: ../invoice-details.php?id=' . $invoice_id);
                exit;
            }

            // Fetch client address
            $client_address = null;
            if (!empty($invoice['client_id'])) {
                $client_address_query = "
                    SELECT ca.*, 
                           co.name AS country_name, 
                           s.name AS state_name, 
                           ci.name AS city_name
                    FROM client_address ca
                    LEFT JOIN countries co ON co.id = ca.billing_country
                    LEFT JOIN states s ON s.id = ca.billing_state
                    LEFT JOIN cities ci ON ci.id = ca.billing_city
                    WHERE ca.client_id = {$invoice['client_id']}
                    LIMIT 1
                ";
                $client_address_result = mysqli_query($conn, $client_address_query);
                $client_address = mysqli_fetch_assoc($client_address_result);
            }

            $clientEmail = $client['email'];
            $clientName = $client['first_name'] . ' ' . $client['last_name'];

            // Get invoice items with prepared statement - INCLUDING CGST/SGST/IGST FIELDS
            $items_query = "SELECT ii.*, 
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
                            WHERE ii.invoice_id = ? AND ii.is_deleted = 0";
            
            $items_stmt = mysqli_prepare($conn, $items_query);
            
            if ($items_stmt) {
                mysqli_stmt_bind_param($items_stmt, "i", $invoice_id);
                mysqli_stmt_execute($items_stmt);
                $items = mysqli_stmt_get_result($items_stmt);

                if (sendInvoiceMail($conn, $clientEmail, $clientName, $invoice, $items, $company, $client_address, $client)) {
                    $_SESSION['message'] = 'Invoice has been sent successfully to ' . $clientEmail . ' with PDF attachment!';
                    $_SESSION['message_type'] = 'success';
                    
                    // Update invoice status to "sent" if not already
                    if ($invoice['status'] == 'draft') {
                        $update_query = "UPDATE invoice SET status = 'pending' WHERE id = ?";
                        $update_stmt = mysqli_prepare($conn, $update_query);
                        if ($update_stmt) {
                            mysqli_stmt_bind_param($update_stmt, "i", $invoice_id);
                            mysqli_stmt_execute($update_stmt);
                            mysqli_stmt_close($update_stmt);
                        }
                    }
                    
                    // Log the email sending
                    error_log("Invoice email with PDF sent to: " . $clientEmail . " for invoice #" . $invoice['invoice_id'] . " at " . date('Y-m-d H:i:s'));
                } else {
                    $_SESSION['message'] = 'Failed to send invoice email. Please try again later.';
                    $_SESSION['message_type'] = 'danger';
                    
                    // Log the email failure
                    error_log("Failed to send invoice email to: " . $clientEmail . " for invoice #" . $invoice['invoice_id']);
                }
                
                mysqli_stmt_close($items_stmt);
            } else {
                $_SESSION['message'] = 'Database preparation error for items.';
                $_SESSION['message_type'] = 'danger';
            }
            
            mysqli_stmt_close($client_stmt);
        } else {
            $_SESSION['message'] = 'Database preparation error for client.';
            $_SESSION['message_type'] = 'danger';
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = 'Database connection error.';
        $_SESSION['message_type'] = 'danger';
    }

    header('Location: ../invoice-details.php?id=' . $invoice_id);
    exit;
} else {
    $_SESSION['message'] = 'Invalid request - No invoice ID provided';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../invoice-details.php');
    exit;
}
?>