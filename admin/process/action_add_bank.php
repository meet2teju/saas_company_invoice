<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get current user info from session
    $currentUserId = $_SESSION['crm_user_id'] ?? 0;
    $currentOrgId = $_SESSION['org_id'] ?? 0;
    
    // Use the org_id from the form (or fallback to session)
    $org_id = isset($_POST['org_id']) ? (int)$_POST['org_id'] : $currentOrgId;
    $created_by = isset($_POST['created_by']) ? (int)$_POST['created_by'] : $currentUserId;
    
    // Validate that org_id is set
    if ($org_id <= 0) {
        $_SESSION['message'] = 'Organization ID is missing. Please log in again.';
        $_SESSION['message_type'] = 'danger';
        header("Location: ../bank.php");
        exit;
    }
    
    // Escape all inputs
    $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
    $account_holder = mysqli_real_escape_string($conn, $_POST['account_holder']);
    $account_number = mysqli_real_escape_string($conn, $_POST['account_number']);
    $routing_number = mysqli_real_escape_string($conn, $_POST['routing_number']);
    $ifsc_code = mysqli_real_escape_string($conn, $_POST['ifsc_code']);
    $swift_code = mysqli_real_escape_string($conn, $_POST['swift_code']);
    $opening_balance = mysqli_real_escape_string($conn, $_POST['opening_balance']);

    // Basic validation
    if (!empty($bank_name) && !empty($account_holder) && !empty($account_number)) {
        // Insert with org_id and created_by
        $sql = "INSERT INTO bank (
            bank_name, 
            account_holder, 
            account_number,
            routing_number, 
            ifsc_code, 
            swift_code, 
            opening_balance, 
            status,
            org_id,
            created_by,
            created_at
        ) VALUES (
            '$bank_name', 
            '$account_holder', 
            '$account_number',
            '$routing_number', 
            '$ifsc_code', 
            '$swift_code', 
            '$opening_balance', 
            1,
            '$org_id',
            '$created_by',
            NOW()
        )";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = 'Bank added successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error: ' . mysqli_error($conn);
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        $_SESSION['message'] = 'Please fill in all required fields.';
        $_SESSION['message_type'] = 'danger';
    }

    header("Location: ../bank.php");
    exit;
}
?>