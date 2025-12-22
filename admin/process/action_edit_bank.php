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
    $bank_name = mysqli_real_escape_string($conn, trim($_POST['bank_name']));
    $account_holder = mysqli_real_escape_string($conn, trim($_POST['account_holder']));
    $account_number = mysqli_real_escape_string($conn, trim($_POST['account_number']));
    $routing_number = mysqli_real_escape_string($conn, trim($_POST['routing_number']));
    $ifsc_code = mysqli_real_escape_string($conn, trim($_POST['ifsc_code']));
    $swift_code = mysqli_real_escape_string($conn, trim($_POST['swift_code']));
    $opening_balance = mysqli_real_escape_string($conn, trim($_POST['opening_balance']));
    
    // Validate required fields
    $errors = [];
    if (empty($bank_name)) $errors[] = "Bank name is required";
    if (empty($account_holder)) $errors[] = "Account holder is required";
    if (empty($account_number)) $errors[] = "Account number is required";
    
    if (!empty($errors)) {
        $_SESSION['message'] = implode('<br>', $errors);
        $_SESSION['message_type'] = 'danger';
        header("Location: ../bank.php");
        exit;
    }
    
    // CHECK FOR DUPLICATE ACCOUNT NUMBER (EXCLUDING CURRENT RECORD)
    $checkDuplicateSql = "SELECT id FROM bank WHERE account_number = '$account_number' 
                         AND org_id = $currentOrgId 
                         AND id != $id";
    $duplicateResult = mysqli_query($conn, $checkDuplicateSql);
    
    if ($duplicateResult && mysqli_num_rows($duplicateResult) > 0) {
        // Store form data in session to repopulate edit form
        $_SESSION['edit_bank_data'] = [
            'id' => $id,
            'bank_name' => $bank_name,
            'account_holder' => $account_holder,
            'account_number' => $account_number,
            'routing_number' => $routing_number,
            'ifsc_code' => $ifsc_code,
            'swift_code' => $swift_code,
            'opening_balance' => $opening_balance
        ];
        
        $_SESSION['message'] = 'Account number already exists in your organization.';
        $_SESSION['message_type'] = 'error';
        header("Location: ../bank.php?error=duplicate_account&edit_id=" . $id);
        exit;
    }
    
    // Also check for duplicate IFSC code if provided
    if (!empty($ifsc_code)) {
        $checkIfscSql = "SELECT id FROM bank WHERE ifsc_code = '$ifsc_code' 
                        AND org_id = $currentOrgId 
                        AND id != $id 
                        AND ifsc_code != ''";
        $ifscResult = mysqli_query($conn, $checkIfscSql);
        
        if ($ifscResult && mysqli_num_rows($ifscResult) > 0) {
            $_SESSION['edit_bank_data'] = [
                'id' => $id,
                'bank_name' => $bank_name,
                'account_holder' => $account_holder,
                'account_number' => $account_number,
                'routing_number' => $routing_number,
                'ifsc_code' => $ifsc_code,
                'swift_code' => $swift_code,
                'opening_balance' => $opening_balance
            ];
            
            $_SESSION['message'] = 'IFSC code already exists in your organization.';
            $_SESSION['message_type'] = 'error';
            header("Location: ../bank.php?error=duplicate_ifsc&edit_id=" . $id);
            exit;
        }
    }
    
    // Also check for duplicate SWIFT code if provided
    if (!empty($swift_code)) {
        $checkSwiftSql = "SELECT id FROM bank WHERE swift_code = '$swift_code' 
                         AND org_id = $currentOrgId 
                         AND id != $id 
                         AND swift_code != ''";
        $swiftResult = mysqli_query($conn, $checkSwiftSql);
        
        if ($swiftResult && mysqli_num_rows($swiftResult) > 0) {
            $_SESSION['edit_bank_data'] = [
                'id' => $id,
                'bank_name' => $bank_name,
                'account_holder' => $account_holder,
                'account_number' => $account_number,
                'routing_number' => $routing_number,
                'ifsc_code' => $ifsc_code,
                'swift_code' => $swift_code,
                'opening_balance' => $opening_balance
            ];
            
            $_SESSION['message'] = 'SWIFT code already exists in your organization.';
            $_SESSION['message_type'] = 'error';
            header("Location: ../bank.php?error=duplicate_swift&edit_id=" . $id);
            exit;
        }
    }
    
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
        // Clear any edit session data if update is successful
        if (isset($_SESSION['edit_bank_data'])) {
            unset($_SESSION['edit_bank_data']);
        }
        
        $_SESSION['message'] = 'Bank updated successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        // Check if error is due to duplicate entry (database level constraint)
        if (mysqli_errno($conn) == 1062) { // MySQL duplicate entry error code
            $_SESSION['edit_bank_data'] = [
                'id' => $id,
                'bank_name' => $bank_name,
                'account_holder' => $account_holder,
                'account_number' => $account_number,
                'routing_number' => $routing_number,
                'ifsc_code' => $ifsc_code,
                'swift_code' => $swift_code,
                'opening_balance' => $opening_balance
            ];
            
            $_SESSION['message'] = 'Account number already exists in your organization.';
            $_SESSION['message_type'] = 'error';
            header("Location: ../bank.php?error=duplicate_account&edit_id=" . $id);
            exit;
        }
        
        $_SESSION['message'] = 'Error: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'danger';
    }
    
    header("Location: ../bank.php");
    exit;
}