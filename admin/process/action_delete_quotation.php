<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $quotationId = intval($_POST['id']);

    mysqli_query($conn, "DELETE FROM quotation_document WHERE quotation_id = $quotationId");
    mysqli_query($conn, "DELETE FROM quotation_item WHERE quotation_id = $quotationId");


    $deletequotation = mysqli_query($conn, "DELETE FROM quotation WHERE id = $quotationId");

    if ($deletequotation) {
        $_SESSION['message'] = "quotation and related data deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to delete quotation.";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: ../quotations.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../quotations.php");
    exit();
}

?>
