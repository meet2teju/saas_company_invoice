<?php
session_start(); // Required to use $_SESSION
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);

    $query = "DELETE FROM product WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Product deleted successfully'; 
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error deleting product: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    header('Location: ../products.php');
    exit();
}
?>
