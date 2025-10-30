<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    
    $query = "DELETE FROM tax WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Tax rate deleted successfully';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error deleting tax rate: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }
    
    header('Location: ../tax-rates.php');
    exit();
}
?>