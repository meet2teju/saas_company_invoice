<?php
include '../../config/config.php';
session_start(); // Required for session messaging

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['quotation_ids']) && is_array($_POST['quotation_ids'])) {
        $quotation_ids = array_map('intval', $_POST['quotation_ids']);
        $ids_string = implode(',', $quotation_ids);

        // Delete from related tables first
        $deleteDocuments = mysqli_query($conn, "DELETE FROM quotation_document WHERE quotation_id IN ($ids_string)");
        $deleteItems     = mysqli_query($conn, "DELETE FROM quotation_item WHERE quotation_id IN ($ids_string)");
        $deleteMain      = mysqli_query($conn, "DELETE FROM quotation WHERE id IN ($ids_string)");

        if ($deleteMain) {
            $_SESSION['message'] = "Selected quotations and their related data deleted successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting quotations: " . mysqli_error($conn);
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "No quotations selected.";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: ../quotations.php");
    exit;
}
?>
