<?php
session_start();
include '../../config/config.php';

// Use Composer autoloader
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendInvoiceMail($clientEmail, $clientName, $invoice, $items) {
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
        $mail->setFrom('maniyamansioe@gmail.com', 'Invoice System');
        $mail->addAddress($clientEmail, $clientName);
        $mail->addReplyTo('maniyamansioe@gmail.com', 'Invoice System');

        // Build items table HTML
        $items_html = '';
        $total_items = 0;
        while ($row = mysqli_fetch_assoc($items)) {
            $items_html .= "
            <tr>
                <td style='padding: 10px; border: 1px solid #ddd;'>{$row['product_name']}</td>
                <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>{$row['quantity']}</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>{$row['unit_name']}</td>
                <td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₹" . number_format($row['selling_price'], 2) . "</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>{$row['tax_name']}</td>
                <td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₹" . number_format($row['amount'], 2) . "</td>
            </tr>";
            $total_items++;
        }

        // Reset items pointer for plain text version
        mysqli_data_seek($items, 0);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Invoice #' . $invoice['invoice_id'] . ' - Invoice System';
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
                    .invoice-info {
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
                    .status-paid { color: #28a745; font-weight: bold; }
                    .status-pending { color: #ffc107; font-weight: bold; }
                    .status-overdue { color: #dc3545; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>INVOICE</h1>
                        <p>Invoice #{$invoice['invoice_id']}</p>
                    </div>

                    <div class='invoice-info'>
                        <div style='display: flex; justify-content: space-between; flex-wrap: wrap;'>
                            <div>
                                <h3>Billing From:</h3>
                                <p><strong>Invoice System</strong><br>
                                maniyamansioe@gmail.com</p>
                            </div>
                            <div>
                                <h3>Billing To:</h3>
                                <p><strong>{$clientName}</strong><br>
                                {$clientEmail}</p>
                            </div>
                        </div>
                    </div>

                    <div class='section'>
                        <h3>Invoice Details</h3>
                        <div style='display: flex; justify-content: space-between; flex-wrap: wrap;'>
                            <div>
                                <p><strong>Invoice Number:</strong> {$invoice['invoice_id']}</p>
                                <p><strong>Issued On:</strong> " . date('F j, Y', strtotime($invoice['invoice_date'])) . "</p>
                            </div>
                            <div>
                                <p><strong>Due Date:</strong> " . date('F j, Y', strtotime($invoice['due_date'])) . "</p>
                                <p><strong>Status:</strong> <span class='status-{$invoice['status']}'>{$invoice['status']}</span></p>
                            </div>
                        </div>
                    </div>

                    <div class='section'>
                        <h3>Product/Service Items</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Item</th>
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
                            <span>₹" . number_format($invoice['amount'], 2) . "</span>
                        </div>
                        <div class='total-row'>
                            <span>Tax Amount:</span>
                            <span>₹" . number_format($invoice['tax_amount'], 2) . "</span>
                        </div>
                        <div class='total-row'>
                            <span>Shipping Charge:</span>
                            <span>₹" . number_format($invoice['shipping_charge'], 2) . "</span>
                        </div>
                        <div class='total-row total-amount'>
                            <span>Total Amount:</span>
                            <span>₹" . number_format($invoice['total_amount'], 2) . "</span>
                        </div>
                    </div>

                    <div class='footer'>
                        <p>Thank you for your business!</p>
                        <p>If you have any questions about this invoice, please contact us.</p>
                        <p>&copy; " . date('Y') . " Invoice System. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        // Build plain text version
        $plainText = "INVOICE #{$invoice['invoice_id']}\n\n";
        $plainText .= "Billing To: {$clientName}\n";
        $plainText .= "Email: {$clientEmail}\n\n";
        $plainText .= "Invoice Details:\n";
        $plainText .= "- Invoice Number: {$invoice['invoice_id']}\n";
        $plainText .= "- Issued On: " . date('F j, Y', strtotime($invoice['invoice_date'])) . "\n";
        $plainText .= "- Due Date: " . date('F j, Y', strtotime($invoice['due_date'])) . "\n";
        $plainText .= "- Status: {$invoice['status']}\n\n";
        
        $plainText .= "Items:\n";
        $plainText .= "----------------------------------------\n";
        while ($row = mysqli_fetch_assoc($items)) {
            $plainText .= "{$row['product_name']} | Qty: {$row['quantity']} {$row['unit_name']} | Price: ₹" . number_format($row['selling_price'], 2) . " | Tax: {$row['tax_name']} | Amount: ₹" . number_format($row['amount'], 2) . "\n";
        }
        $plainText .= "----------------------------------------\n\n";
        
        $plainText .= "Summary:\n";
        $plainText .= "Sub Amount: ₹" . number_format($invoice['amount'], 2) . "\n";
        $plainText .= "Tax Amount: ₹" . number_format($invoice['tax_amount'], 2) . "\n";
        $plainText .= "Shipping Charge: ₹" . number_format($invoice['shipping_charge'], 2) . "\n";
        $plainText .= "Total Amount: ₹" . number_format($invoice['total_amount'], 2) . "\n\n";
        
        $plainText .= "Thank you for your business!\n\n";
        $plainText .= "If you have any questions about this invoice, please contact us.\n";
        $plainText .= "© " . date('Y') . " Invoice System. All rights reserved.";

        $mail->AltBody = $plainText;

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

    // Fetch invoice with prepared statement
    $query = "SELECT * FROM invoice WHERE id = ? AND is_deleted = 0";
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

            $clientEmail = $client['email'];
            $clientName = $client['first_name'] . ' ' . $client['last_name'];

            // Get invoice items with prepared statement
            $items_query = "SELECT ii.*, p.name AS product_name, t.name AS tax_name, u.name AS unit_name
                FROM invoice_item ii
                LEFT JOIN product p ON p.id = ii.product_id
                LEFT JOIN units u ON u.id = ii.unit_id
                LEFT JOIN tax t ON t.id = ii.tax_id
                WHERE ii.invoice_id = ? AND ii.is_deleted = 0";
            
            $items_stmt = mysqli_prepare($conn, $items_query);
            
            if ($items_stmt) {
                mysqli_stmt_bind_param($items_stmt, "i", $invoice_id);
                mysqli_stmt_execute($items_stmt);
                $items = mysqli_stmt_get_result($items_stmt);

                if (sendInvoiceMail($clientEmail, $clientName, $invoice, $items)) {
                    $_SESSION['message'] = 'Invoice has been sent successfully to ' . $clientEmail . '!';
                    $_SESSION['message_type'] = 'success';
                    
                    // Log the email sending
                    error_log("Invoice email sent to: " . $clientEmail . " for invoice #" . $invoice['invoice_id'] . " at " . date('Y-m-d H:i:s'));
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