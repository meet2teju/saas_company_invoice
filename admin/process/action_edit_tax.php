<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $rate = floatval($_POST['rate']);
    
    // // Validate inputs
    // if (empty($name)) {
    //     $_SESSION['message'] = 'Tax name is required';
    //     $_SESSION['message_type'] = 'error';
    //     header('Location: ../tax-rates.php');
    //     exit();
    // }
    
    // if ($rate < 0 || $rate > 100) {
    //     $_SESSION['message'] = 'Tax rate must be between 0 and 100';
    //     $_SESSION['message_type'] = 'error';
    //     header('Location: ../tax-rates.php');
    //     exit();
    // }
    
    // Update database
    $query = "UPDATE tax SET name = '$name', rate = $rate WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Tax rate updated successfully';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error updating tax rate: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }
    
    header('Location: ../tax-rates.php');
    exit();
}
?>