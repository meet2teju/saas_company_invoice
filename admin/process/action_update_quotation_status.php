<?php
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quotation_id = intval($_POST['quotation_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Validate status
    $allowed_statuses = ['draft', 'sent', 'accepted', 'rejected', 'expired','cancel','convert'];
    if (!in_array($status, $allowed_statuses)) {
        $_SESSION['message'] = "Invalid status selected.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../quotations.php");
        exit();
    }
    
    // If status is 'convert', convert quotation to invoice
    if ($status === 'convert') {
        // Fetch quotation data
        $quotation_sql = "SELECT * FROM quotation WHERE id = $quotation_id";
        $quotation_result = mysqli_query($conn, $quotation_sql);
        $quotation = mysqli_fetch_assoc($quotation_result);
        
        if ($quotation) {
            // Generate unique invoice ID
            $invoice_id = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
            
            // Insert into invoice table
            $insert_invoice_sql = "INSERT INTO invoice (
                client_id, invoice_id, reference_name, invoice_date, due_date, 
                user_id, description, amount, shipping_charge, tax_amount, 
                total_amount, status, org_id, created_by, created_at, updated_at
            ) VALUES (
                '{$quotation['client_id']}', 
                '$invoice_id', 
                '{$quotation['reference_name']}', 
                '{$quotation['quotation_date']}', 
                '{$quotation['expiry_date']}', 
                '{$quotation['user_id']}', 
                '{$quotation['description']}', 
                '{$quotation['amount']}', 
                '{$quotation['shipping_charge']}', 
                '{$quotation['tax_amount']}', 
                '{$quotation['total_amount']}', 
                'unpaid', 
                '{$quotation['org_id']}', 
                '{$quotation['created_by']}', 
                NOW(), 
                NOW()
            )";
            
            if (mysqli_query($conn, $insert_invoice_sql)) {
                $new_invoice_id = mysqli_insert_id($conn);
                
                // Copy quotation items to invoice items
                $copy_items_sql = "INSERT INTO invoice_item (
                    invoice_id, product_id, quantity, unit_id, selling_price, 
                    tax_id, amount, created_at, updated_at
                ) 
                SELECT 
                    $new_invoice_id, product_id, quantity, unit_id, selling_price, 
                    tax_id, amount, NOW(), NOW()
                FROM quotation_item 
                WHERE quotation_id = $quotation_id AND is_deleted = 0";
                
                mysqli_query($conn, $copy_items_sql);
                
                // Copy documents to invoice documents (if you have invoice_document table)
                $copy_docs_sql = "INSERT INTO invoice_document (
                    invoice_id, document, created_at, updated_at
                ) 
                SELECT 
                    $new_invoice_id, document, NOW(), NOW()
                FROM quotation_document 
                WHERE quotation_id = $quotation_id";
                
                mysqli_query($conn, $copy_docs_sql);
                
                // Remove quotation record (soft delete by setting is_deleted = 1)
                $delete_quotation_sql = "UPDATE quotation SET is_deleted = 1 WHERE id = $quotation_id";
                mysqli_query($conn, $delete_quotation_sql);
                
                $_SESSION['message'] = "Quotation converted to invoice successfully! Invoice ID: $invoice_id";
                $_SESSION['message_type'] = "success";
                
                // Redirect to quotations list page
                header("Location: ../quotations.php");
                exit();
            } else {
                $_SESSION['message'] = "Error creating invoice: " . mysqli_error($conn);
                $_SESSION['message_type'] = "danger";
                header("Location: ../view-quotation.php?id=" . $quotation_id);
                exit();
            }
        }
    } else {
        // For other status updates, proceed as normal
        $update_sql = "UPDATE quotation SET status = '$status' WHERE id = $quotation_id";
        
        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['message'] = "Quotation status updated successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating quotation status: " . mysqli_error($conn);
            $_SESSION['message_type'] = "danger";
        }
        
        // Redirect back to the view quotation page
        header("Location: ../view-quotation.php?id=" . $quotation_id);
        exit();
    }
} else {
    $_SESSION['message'] = "Invalid request method.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../quotations.php");
    exit();
}
?>