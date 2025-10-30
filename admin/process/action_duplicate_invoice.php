<?php
session_start();
include '../../config/config.php';

$currentUserId = $_SESSION['user_id'] ?? 1;
$orgId = $_SESSION['org_id'] ?? 1;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $originalInvoiceId = intval($_GET['id']);

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // 1. Get the original invoice
        $invoiceQuery = mysqli_query($conn, "SELECT * FROM invoice WHERE id = $originalInvoiceId AND is_deleted = 0");
        if (!$invoiceQuery || mysqli_num_rows($invoiceQuery) === 0) {
            throw new Exception("Invoice not found or already deleted.");
        }
        $invoice = mysqli_fetch_assoc($invoiceQuery);

        // 2. Generate new invoice_id like "INV-0001"
        $query = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES 
                  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoice'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);

        if ($row && isset($row['AUTO_INCREMENT'])) {
            $nextId = $row['AUTO_INCREMENT'];
            $new_invoice_code = 'INV-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        } else {
            $new_invoice_code = 'INV-0001';
        }

        // 3. Insert duplicated invoice (set status = Draft)
        $insertInvoice = "INSERT INTO invoice (
            client_id, invoice_id, reference_name, invoice_date, due_date,
            order_number, item_type, user_id, bank_id,
            invoice_note, description, amount, tax_amount, shipping_charge, total_amount,
            status,
            org_id, is_deleted, created_by, updated_by
        ) VALUES (
            '{$invoice['client_id']}', '$new_invoice_code', '{$invoice['reference_name']}', NOW(), '{$invoice['due_date']}',
            '{$invoice['order_number']}', '{$invoice['item_type']}', '{$invoice['user_id']}', '{$invoice['bank_id']}',
            '{$invoice['invoice_note']}', '{$invoice['description']}', '{$invoice['amount']}', '{$invoice['tax_amount']}',
            '{$invoice['shipping_charge']}', '{$invoice['total_amount']}',
            'Draft',
            '$orgId', 0, '$currentUserId', '$currentUserId'
        )";

        if (!mysqli_query($conn, $insertInvoice)) {
            throw new Exception("Invoice insert failed: " . mysqli_error($conn));
        }

        $newInvoiceId = mysqli_insert_id($conn);

        // 4. Clone invoice items
        $itemQuery = mysqli_query($conn, "SELECT * FROM invoice_item WHERE invoice_id = $originalInvoiceId AND is_deleted = 0");
        while ($item = mysqli_fetch_assoc($itemQuery)) {
            $insertItem = "INSERT INTO invoice_item (
                invoice_id, quantity, product_id, unit_id, selling_price,
                tax_id, amount, org_id, is_deleted, created_by, updated_by
            ) VALUES (
                '$newInvoiceId', '{$item['quantity']}', '{$item['product_id']}', '{$item['unit_id']}', '{$item['selling_price']}',
                '{$item['tax_id']}', '{$item['amount']}', '$orgId', 0, '$currentUserId', '$currentUserId'
            )";
            if (!mysqli_query($conn, $insertItem)) {
                throw new Exception("Invoice item insert failed: " . mysqli_error($conn));
            }
        }

        // 5. Clone invoice documents
        $docQuery = mysqli_query($conn, "SELECT * FROM invoice_document WHERE invoice_id = $originalInvoiceId");
        while ($doc = mysqli_fetch_assoc($docQuery)) {
            $insertDoc = "INSERT INTO invoice_document (
                invoice_id, document, created_by, updated_by
            ) VALUES (
                '$newInvoiceId', '{$doc['document']}', '$currentUserId', '$currentUserId'
            )";
            if (!mysqli_query($conn, $insertDoc)) {
                throw new Exception("Invoice document insert failed: " . mysqli_error($conn));
            }
        }

        // Commit all changes
        mysqli_commit($conn);

        $_SESSION['message'] = "Invoice duplicated successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: ../invoices.php?id=$newInvoiceId");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error'] = "Duplication failed: " . $e->getMessage();
        header("Location: ../invoices.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid invoice ID.";
    header("Location: ../invoices.php");
    exit();
}
?>
