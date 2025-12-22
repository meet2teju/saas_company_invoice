<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session first
session_start();

// Include config with error handling
$configPath = dirname(__DIR__, 2) . '/config/config.php'; // Go up two levels
if (file_exists($configPath)) {
    require_once $configPath;
} else {
    die("Error: Config file not found at: " . $configPath);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get session values with defaults
    $currentUserId = isset($_SESSION['crm_user_id']) ? $_SESSION['crm_user_id'] : 0;
    $currentOrgId = isset($_SESSION['org_id']) ? $_SESSION['org_id'] : 0;
    
    // Get form values with proper escaping
    $bank_name = isset($_POST['bank_name']) ? mysqli_real_escape_string($conn, trim($_POST['bank_name'])) : '';
    $account_holder = isset($_POST['account_holder']) ? mysqli_real_escape_string($conn, trim($_POST['account_holder'])) : '';
    $account_number = isset($_POST['account_number']) ? mysqli_real_escape_string($conn, trim($_POST['account_number'])) : '';
    $routing_number = isset($_POST['routing_number']) ? mysqli_real_escape_string($conn, trim($_POST['routing_number'])) : '';
    $ifsc_code = isset($_POST['ifsc_code']) ? mysqli_real_escape_string($conn, trim($_POST['ifsc_code'])) : '';
    $swift_code = isset($_POST['swift_code']) ? mysqli_real_escape_string($conn, trim($_POST['swift_code'])) : '';
    
    // Handle opening balance - convert empty to 0.00
    $opening_balance = '0.00'; // Default value
    if (isset($_POST['opening_balance']) && $_POST['opening_balance'] !== '') {
        $opening_balance_input = trim($_POST['opening_balance']);
        // Validate it's a valid number
        if (is_numeric($opening_balance_input)) {
            $opening_balance = number_format((float)$opening_balance_input, 2, '.', '');
        }
    }
    
    // Validate required fields
    $errors = [];
    if (empty($bank_name)) $errors[] = "Bank name is required";
    if (empty($account_holder)) $errors[] = "Account holder is required";
    if (empty($account_number)) $errors[] = "Account number is required";
    if ($currentOrgId <= 0) $errors[] = "Organization ID is not set";
    
    if (!empty($errors)) {
        $_SESSION['message'] = implode('<br>', $errors);
        $_SESSION['message_type'] = 'danger';
        header("Location: ../bank.php");
        exit;
    }
    
    // Check database connection
    if (!$conn) {
        $_SESSION['message'] = 'Database connection failed: ' . mysqli_connect_error();
        $_SESSION['message_type'] = 'danger';
        header("Location: ../bank.php");
        exit;
    }
    
    // CHECK FOR DUPLICATE ACCOUNT NUMBER WITHIN THE SAME ORGANIZATION
    $checkDuplicateSql = "SELECT id FROM bank WHERE account_number = '$account_number' AND org_id = $currentOrgId";
    $duplicateResult = mysqli_query($conn, $checkDuplicateSql);
    
    if ($duplicateResult && mysqli_num_rows($duplicateResult) > 0) {
        // Store form data to repopulate
        $_SESSION['old_bank_data'] = [
            'bank_name' => $bank_name,
            'account_holder' => $account_holder,
            'account_number' => $account_number,
            'routing_number' => $routing_number,
            'ifsc_code' => $ifsc_code,
            'swift_code' => $swift_code,
            'opening_balance' => $opening_balance_input ?? ''
        ];
        
        $_SESSION['message'] = 'Account number already exists in your organization.';
        $_SESSION['message_type'] = 'error';
        header("Location: ../bank.php?error=duplicate_account&open=add_bank_modal");
        exit;
    }
    
    // Prepare SQL query - use proper numeric value for opening_balance
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
        $opening_balance,  -- Note: No quotes for numeric value
        1,
        $currentOrgId,
        $currentUserId,
        NOW()
    )";
    
    // Execute query
    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = 'Bank added successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $error_msg = 'Database error: ' . mysqli_error($conn);
        error_log("ERROR: " . $error_msg);
        
        // Check if error is due to duplicate entry
        if (mysqli_errno($conn) == 1062) { // MySQL duplicate entry error code
            $_SESSION['old_bank_data'] = [
                'bank_name' => $bank_name,
                'account_holder' => $account_holder,
                'account_number' => $account_number,
                'routing_number' => $routing_number,
                'ifsc_code' => $ifsc_code,
                'swift_code' => $swift_code,
                'opening_balance' => $opening_balance_input ?? ''
            ];
            
            $_SESSION['message'] = 'Account number already exists in your organization.';
            $_SESSION['message_type'] = 'error';
            header("Location: ../bank.php?error=duplicate_account&open=add_bank_modal");
            exit;
        }
        
        $_SESSION['message'] = 'Error adding bank: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'danger';
    }
    
    // Redirect back
    header("Location: ../bank.php");
    exit;
} else {
    // Not a POST request
    $_SESSION['message'] = 'Invalid request method.';
    $_SESSION['message_type'] = 'danger';
    header("Location: ../bank.php");
    exit;
}
?>