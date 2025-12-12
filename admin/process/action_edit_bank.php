<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUserId = $_SESSION['crm_user_id'] ?? 0;
    $currentOrgId = $_SESSION['org_id'] ?? 0;
    $id = (int)$_POST['id'];
    $org_id = isset($_POST['org_id']) ? (int)$_POST['org_id'] : $currentOrgId;
    
    // Validate user can edit this bank
    if ($currentOrgId > 0) {
        // Check if bank belongs to user's organization
        $checkQuery = "SELECT id FROM bank WHERE id = $id AND org_id = $currentOrgId";
        $checkResult = mysqli_query($conn, $checkQuery);
        
        if (mysqli_num_rows($checkResult) == 0) {
            $_SESSION['message'] = 'You do not have permission to edit this bank.';
            $_SESSION['message_type'] = 'danger';
            header("Location: ../bank.php");
            exit;
        }
    }
    
    // Escape inputs
    $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
    $account_holder = mysqli_real_escape_string($conn, $_POST['account_holder']);
    $account_number = mysqli_real_escape_string($conn, $_POST['account_number']);
    $routing_number = mysqli_real_escape_string($conn, $_POST['routing_number']);
    $ifsc_code = mysqli_real_escape_string($conn, $_POST['ifsc_code']);
    $swift_code = mysqli_real_escape_string($conn, $_POST['swift_code']);
    $opening_balance = mysqli_real_escape_string($conn, $_POST['opening_balance']);
    
    // Update query
    $sql = "UPDATE bank SET 
        bank_name = '$bank_name',
        account_holder = '$account_holder',
        account_number = '$account_number',
        routing_number = '$routing_number',
        ifsc_code = '$ifsc_code',
        swift_code = '$swift_code',
        opening_balance = '$opening_balance',
        updated_at = NOW()
        WHERE id = $id AND org_id = $org_id";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = 'Bank updated successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'danger';
    }
    
    header("Location: ../bank.php");
    exit;
}
?>