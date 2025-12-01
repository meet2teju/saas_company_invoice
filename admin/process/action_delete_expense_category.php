<?php
session_start();
include '../../config/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Soft delete by setting is_deleted = 1
    $query = "UPDATE expense_category SET is_deleted = 1 WHERE id = $id";
    $delete = mysqli_query($conn, $query);

    if ($delete) {
        $_SESSION['message'] = 'Expense category deleted successfully';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error deleting expense category: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    header('Location: ../expense_category.php');
    exit();
} else {
    $_SESSION['message'] = 'Invalid expense category ID';
    $_SESSION['message_type'] = 'error';
    header('Location: ../expense_category.php');
    exit();
}
?>