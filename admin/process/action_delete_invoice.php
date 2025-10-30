<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invoice_id'])) {
    $invoiceId = intval($_POST['invoice_id']);

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Delete related records first
        mysqli_query($conn, "DELETE FROM invoice_document WHERE invoice_id = $invoiceId");
        mysqli_query($conn, "DELETE FROM invoice_item WHERE invoice_id = $invoiceId");
        
        // Then delete the invoice
        $deleteInvoice = mysqli_query($conn, "DELETE FROM invoice WHERE id = $invoiceId");

        if ($deleteInvoice) {
            mysqli_commit($conn);
            $_SESSION['message'] = "Invoice deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            throw new Exception("Failed to delete invoice.");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }

    header("Location: ../invoices.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../invoices.php");
    exit();
}
?>