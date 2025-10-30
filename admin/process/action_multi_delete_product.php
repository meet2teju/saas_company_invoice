<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['product_ids'])) {
    $product_ids = array_map('intval', $_POST['product_ids']);
    $ids_string = implode(',', $product_ids);

    if (mysqli_query($conn, "DELETE FROM product WHERE id IN ($ids_string)")) {
        $_SESSION['message'] = "Selected Products deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting Products.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "No products selected.";
    $_SESSION['message_type'] = "warning";
}

// Redirect back to products page
header("Location: ../products.php"); // change to your product page
exit();
?>
