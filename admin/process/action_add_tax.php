<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user and organization IDs from session (with fallbacks)
    $currentUserId = $_SESSION['crm_user_id'] ?? 1;
    $orgId = $_SESSION['org_id'] ?? 1;

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $rate = floatval($_POST['rate']);
    
    // Validate inputs
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
    
    // Insert into database with user_id, org_id, and other organizational fields
    $query = "INSERT INTO tax (
        name, 
        rate, 
        status, 
        org_id, 
        user_id, 
        is_deleted, 
        created_by, 
        updated_by, 
        created_at
    ) VALUES (
        '$name', 
        $rate, 
        1, 
        '$orgId', 
        '$currentUserId', 
        0, 
        '$currentUserId', 
        '$currentUserId', 
        NOW()
    )";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Tax rate added successfully';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error adding tax rate: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }
    
    header('Location: ../tax-rates.php');
    exit();
}
?>