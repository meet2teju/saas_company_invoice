<?php
session_start();
include '../../config/config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    $query = "DELETE FROM category WHERE id = $id";
    $delete = mysqli_query($conn, $query);

    if ($delete) {
        $_SESSION['message'] = 'Category deleted successfully';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error deleting category: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    header('Location: ../category.php');
    exit();
} else {
    $_SESSION['message'] = 'Invalid category ID';
    $_SESSION['message_type'] = 'error';
    header('Location: ../category.php');
    exit();
}
?>
