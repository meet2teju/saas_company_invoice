<?php
session_start();
include '../../config/config.php';

// Use Composer autoloader
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Add the numberToWords function
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

function sendQuotationMail($clientEmail, $clientName, $quotation, $items, $company) {
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

        // Recipients
        $mail->setFrom('maniyamansioe@gmail.com', 'Quotation System');
        $mail->addAddress($clientEmail, $clientName);
        $mail->addReplyTo('maniyamansioe@gmail.com', 'Quotation System');

        // Build items table HTML
        $items_html = '';
        $subtotal = 0;
        $taxSummary = [];
        
        while ($row = mysqli_fetch_assoc($items)) {
            $subtotal += $row['amount'];
            
            // Calculate tax for summary
            if (!empty($row['tax_rate'])) {
                $lineTax = ($row['amount'] * $row['tax_rate']) / 100;
                $taxKey = $row['tax_name'] . ' (' . $row['tax_rate'] . '%)';
                
                if (!isset($taxSummary[$taxKey])) {
                    $taxSummary[$taxKey] = 0;
                }
                $taxSummary[$taxKey] += $lineTax;
            }
            
            $items_html .= "
            <tr>
                <td style='padding: 10px; border: 1px solid #ddd;'>{$row['product_name']}</td>
                <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>{$row['code']}</td>
                <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>{$row['quantity']}</td>
                <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>{$row['unit_name']}</td>
                <td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>$" . number_format($row['selling_price'], 2) . "</td>
                <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>{$row['tax_name']}" . (!empty($row['tax_rate']) ? " ({$row['tax_rate']}%)" : "") . "</td>
                <td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>$" . number_format($row['amount'], 2) . "</td>
            </tr>";
        }

        // Reset items pointer for plain text version
        mysqli_data_seek($items, 0);

        // Build tax summary HTML
        $tax_html = '';
        $totalTax = 0;
        foreach ($taxSummary as $taxLabel => $taxAmount) {
            $totalTax += $taxAmount;
            $tax_html .= "
            <div class='total-row'>
                <span>{$taxLabel}:</span>
                <span>$" . number_format($taxAmount, 2) . "</span>
            </div>";
        }

        // Build shipping charge HTML if exists
        $shipping_html = '';
        if (!empty($quotation['shipping_charge']) && $quotation['shipping_charge'] > 0) {
            $shipping_html = "
            <div class='total-row'>
                <span>Shipping Charge:</span>
                <span>$" . number_format($quotation['shipping_charge'], 2) . "</span>
            </div>";
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Quotation #' . $quotation['quotation_id'] . ' - ' . $company['name'];
        $mail->Body    = "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        line-height: 1.6; 
                        color: #333; 
                        background-color: #f4f4f4;
                        margin: 0;
                        padding: 20px;
                    }
                    .container {
                        max-width: 800px;
                        margin: 0 auto;
                        background: white;
                        padding: 30px;
                        border-radius: 10px;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    }
                    .header {
                        text-align: center;
                        border-bottom: 2px solid #007bff;
                        padding-bottom: 20px;
                        margin-bottom: 30px;
                    }
                    .header h1 {
                        color: #007bff;
                        margin: 0;
                    }
                    .quotation-info {
                        background: #f8f9fa;
                        padding: 20px;
                        border-radius: 5px;
                        margin-bottom: 20px;
                    }
                    .section {
                        margin-bottom: 25px;
                    }
                    .section h3 {
                        color: #007bff;
                        border-bottom: 1px solid #dee2e6;
                        padding-bottom: 10px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 15px 0;
                    }
                    table th {
                        background: #007bff;
                        color: white;
                        padding: 12px;
                        text-align: left;
                    }
                    table td {
                        padding: 12px;
                        border-bottom: 1px solid #dee2e6;
                    }
                    table tr:nth-child(even) {
                        background: #f8f9fa;
                    }
                    .total-section {
                        background: #e9ecef;
                        padding: 20px;
                        border-radius: 5px;
                        margin-top: 20px;
                    }
                    .total-row {
                        display: flex;
                        justify-content: space-between;
                        margin: 5px 0;
                    }
                    .total-amount {
                        font-size: 1.2em;
                        font-weight: bold;
                        color: #007bff;
                        border-top: 2px solid #007bff;
                        padding-top: 10px;
                    }
                    .footer {
                        text-align: center;
                        margin-top: 30px;
                        padding-top: 20px;
                        border-top: 1px solid #dee2e6;
                        color: #6c757d;
                        font-size: 0.9em;
                    }
                    .status-draft { color: #6c757d; font-weight: bold; }
                    .status-sent { color: #17a2b8; font-weight: bold; }
                    .status-accepted { color: #28a745; font-weight: bold; }
                    .status-rejected { color: #dc3545; font-weight: bold; }
                    .status-expired { color: #ffc107; font-weight: bold; }
                    .billing-section {
                        display: flex;
                        justify-content: space-between;
                        flex-wrap: wrap;
                        gap: 20px;
                    }
                    .billing-box {
                        flex: 1;
                        min-width: 250px;
                        background: white;
                        padding: 15px;
                        border-radius: 5px;
                        border: 1px solid #dee2e6;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>QUOTATION</h1>
                        <p>Quotation #{$quotation['quotation_id']}</p>
                    </div>

                    <div class='quotation-info'>
                        <div class='billing-section'>
                            <div class='billing-box'>
                                <h3>Billing From:</h3>
                                <p><strong>{$company['name']}</strong><br>
                                {$company['address']}<br>
                                {$company['city_name']}, {$company['state_name']}, {$company['country_name']} {$company['zipcode']}<br>
                                Phone: {$company['mobile_number']}<br>
                                Email: {$company['email']}</p>
                            </div>
                            <div class='billing-box'>
                                <h3>Billing To:</h3>
                                <p><strong>{$clientName}</strong><br>
                                {$clientEmail}</p>
                            </div>
                        </div>
                    </div>

                    <div class='section'>
                        <h3>Quotation Details</h3>
                        <div style='display: flex; justify-content: space-between; flex-wrap: wrap;'>
                            <div>
                                <p><strong>Quotation Number:</strong> {$quotation['quotation_id']}</p>
                                <p><strong>Issued On:</strong> " . date('F j, Y', strtotime($quotation['quotation_date'])) . "</p>
                            </div>
                            <div>
                                <p><strong>Expiry Date:</strong> " . date('F j, Y', strtotime($quotation['expiry_date'])) . "</p>
                                <p><strong>Status:</strong> <span class='status-{$quotation['status']}'>" . ucfirst($quotation['status']) . "</span></p>
                            </div>
                        </div>
                        <p><strong>Reference:</strong> {$quotation['reference_name']}</p>
                    </div>

                    <div class='section'>
                        <h3>Product/Service Items</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>HSN Code</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Unit Price</th>
                                    <th>Tax</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                {$items_html}
                            </tbody>
                        </table>
                    </div>

                    <div class='total-section'>
                        <div class='total-row'>
                            <span>Sub Amount:</span>
                            <span>$" . number_format($subtotal, 2) . "</span>
                        </div>
                        {$tax_html}
                        {$shipping_html}
                        <div class='total-row total-amount'>
                            <span>Total Amount:</span>
                            <span>$" . number_format($quotation['total_amount'], 2) . "</span>
                        </div>
                    </div>

                    <div class='section'>
                        <h3>Total In Words</h3>
                        <p><em>" . numberToWords($quotation['total_amount']) . " Dollars</em></p>
                    </div>

                    " . (!empty($quotation['quotation_note']) ? "
                    <div class='section'>
                        <h3>Notes</h3>
                        <p>" . nl2br(htmlspecialchars($quotation['quotation_note'])) . "</p>
                    </div>
                    " : "") . "

                    <div class='footer'>
                        <p>This quotation is valid until: " . date('F j, Y', strtotime($quotation['expiry_date'])) . "</p>
                        <p>Thank you for your business!</p>
                        <p>If you have any questions about this quotation, please contact us.</p>
                        <p>&copy; " . date('Y') . " {$company['name']}. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        // Build plain text version
        $plainText = "QUOTATION #{$quotation['quotation_id']}\n\n";
        $plainText .= "From: {$company['name']}\n";
        $plainText .= "To: {$clientName}\n";
        $plainText .= "Email: {$clientEmail}\n\n";
        $plainText .= "Quotation Details:\n";
        $plainText .= "- Quotation Number: {$quotation['quotation_id']}\n";
        $plainText .= "- Issued On: " . date('F j, Y', strtotime($quotation['quotation_date'])) . "\n";
        $plainText .= "- Expiry Date: " . date('F j, Y', strtotime($quotation['expiry_date'])) . "\n";
        $plainText .= "- Status: " . ucfirst($quotation['status']) . "\n";
        $plainText .= "- Reference: {$quotation['reference_name']}\n\n";
        
        $plainText .= "Items:\n";
        $plainText .= "---------------------------------------------------------------------\n";
        while ($row = mysqli_fetch_assoc($items)) {
            $plainText .= "{$row['product_name']} | HSN: {$row['code']} | Qty: {$row['quantity']} {$row['unit_name']} | Price: $" . number_format($row['selling_price'], 2) . " | Tax: {$row['tax_name']}" . (!empty($row['tax_rate']) ? " ({$row['tax_rate']}%)" : "") . " | Amount: $" . number_format($row['amount'], 2) . "\n";
        }
        $plainText .= "---------------------------------------------------------------------\n\n";
        
        $plainText .= "Summary:\n";
        $plainText .= "Sub Amount: $" . number_format($subtotal, 2) . "\n";
        
        foreach ($taxSummary as $taxLabel => $taxAmount) {
            $plainText .= "{$taxLabel}: $" . number_format($taxAmount, 2) . "\n";
        }
        
        if (!empty($quotation['shipping_charge']) && $quotation['shipping_charge'] > 0) {
            $plainText .= "Shipping Charge: $" . number_format($quotation['shipping_charge'], 2) . "\n";
        }
        
        $plainText .= "Total Amount: $" . number_format($quotation['total_amount'], 2) . "\n\n";
        
        $plainText .= "Total in Words: " . numberToWords($quotation['total_amount']) . " Dollars\n\n";
        
        if (!empty($quotation['quotation_note'])) {
            $plainText .= "Notes:\n" . $quotation['quotation_note'] . "\n\n";
        }
        
        $plainText .= "This quotation is valid until: " . date('F j, Y', strtotime($quotation['expiry_date'])) . "\n\n";
        $plainText .= "Thank you for your business!\n\n";
        $plainText .= "If you have any questions about this quotation, please contact us.\n";
        $plainText .= "© " . date('Y') . " {$company['name']}. All rights reserved.";

        $mail->AltBody = $plainText;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Quotation Mailer Error: " . $e->getMessage());
        return false;
    }
}

if ((isset($_GET['quotation_id']) || isset($_POST['quotation_id']))) {
    $quotation_id = isset($_POST['quotation_id']) ? (int)$_POST['quotation_id'] : (int)$_GET['quotation_id'];

    // Validate quotation ID
    if ($quotation_id <= 0) {
        $_SESSION['message'] = 'Invalid quotation ID';
        $_SESSION['message_type'] = 'error';
        header('Location: ../view-quotation.php');
        exit;
    }

    // Fetch quotation with prepared statement
    $query = "SELECT q.*, c.first_name, c.last_name, c.email, c.phone_number 
              FROM quotation q 
              LEFT JOIN client c ON q.client_id = c.id 
              WHERE q.id = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $quotation_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $quotation = mysqli_fetch_assoc($result);
        
        if (!$quotation) {
            $_SESSION['message'] = 'Quotation not found';
            $_SESSION['message_type'] = 'error';
            header('Location: ../view-quotation.php');
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
                          LIMIT 1";
        $company_result = mysqli_query($conn, $company_query);
        $company = mysqli_fetch_assoc($company_result);

        if (!$company) {
            $_SESSION['message'] = 'Company information not found';
            $_SESSION['message_type'] = 'error';
            header('Location: ../view-quotation.php?id=' . $quotation_id);
            exit;
        }

        if (empty($quotation['email'])) {
            $_SESSION['message'] = 'Client email address not found';
            $_SESSION['message_type'] = 'error';
            header('Location: ../view-quotation.php?id=' . $quotation_id);
            exit;
        }

        $clientEmail = $quotation['email'];
        $clientName = $quotation['first_name'] . ' ' . $quotation['last_name'];

        // Get quotation items with prepared statement
        $items_query = "SELECT qi.*, p.name AS product_name, p.code, 
                               t.name AS tax_name, u.name AS unit_name, t.rate AS tax_rate
                        FROM quotation_item qi
                        LEFT JOIN product p ON p.id = qi.product_id
                        LEFT JOIN units u ON u.id = qi.unit_id
                        LEFT JOIN tax t ON t.id = qi.tax_id
                        WHERE qi.quotation_id = ? AND qi.is_deleted = 0";
        
        $items_stmt = mysqli_prepare($conn, $items_query);
        
        if ($items_stmt) {
            mysqli_stmt_bind_param($items_stmt, "i", $quotation_id);
            mysqli_stmt_execute($items_stmt);
            $items = mysqli_stmt_get_result($items_stmt);

            if (sendQuotationMail($clientEmail, $clientName, $quotation, $items, $company)) {
                $_SESSION['message'] = 'Quotation has been sent successfully to ' . $clientEmail . '!';
                $_SESSION['message_type'] = 'success';
                
                // Update quotation status to "sent"
                $update_query = "UPDATE quotation SET status = 'sent' WHERE id = ?";
                $update_stmt = mysqli_prepare($conn, $update_query);
                if ($update_stmt) {
                    mysqli_stmt_bind_param($update_stmt, "i", $quotation_id);
                    mysqli_stmt_execute($update_stmt);
                    mysqli_stmt_close($update_stmt);
                }
                
                // Log the email sending
                error_log("Quotation email sent to: " . $clientEmail . " for quotation #" . $quotation['quotation_id'] . " at " . date('Y-m-d H:i:s'));
            } else {
                $_SESSION['message'] = 'Failed to send quotation email. Please try again later.';
                $_SESSION['message_type'] = 'danger';
                
                // Log the email failure
                error_log("Failed to send quotation email to: " . $clientEmail . " for quotation #" . $quotation['quotation_id']);
            }
            
            mysqli_stmt_close($items_stmt);
        } else {
            $_SESSION['message'] = 'Database preparation error for items.';
            $_SESSION['message_type'] = 'danger';
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = 'Database connection error.';
        $_SESSION['message_type'] = 'danger';
    }

    header('Location: ../view-quotation.php?id=' . $quotation_id);
    exit;
} else {
    $_SESSION['message'] = 'Invalid request - No quotation ID provided';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../view-quotation.php');
    exit;
}
?>