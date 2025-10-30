<?php
include '../../config/config.php';
session_start(); // Required for session messaging

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['invoice_ids']) && is_array($_POST['invoice_ids'])) {
        $invoice_ids = array_map('intval', $_POST['invoice_ids']);
        $ids_string = implode(',', $invoice_ids);

        // Delete from related tables first
        $deleteDocuments = mysqli_query($conn, "DELETE FROM invoice_document WHERE invoice_id IN ($ids_string)");
        $deleteItems     = mysqli_query($conn, "DELETE FROM invoice_item WHERE invoice_id IN ($ids_string)");
        $deleteMain      = mysqli_query($conn, "DELETE FROM invoice WHERE id IN ($ids_string)");

        if ($deleteMain) {
            $_SESSION['message'] = "Selected invoices and their related data deleted successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting invoices: " . mysqli_error($conn);
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "No invoices selected.";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: ../invoices.php");
    exit;
}
?>
