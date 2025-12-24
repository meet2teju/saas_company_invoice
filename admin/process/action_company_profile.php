<?php
// Start output buffering
ob_start();

session_start();

// Turn on error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../config/config.php';

// Include currency helper
if (file_exists('../../config/currency_helper.php')) {
    require_once '../../config/currency_helper.php';
}

// Clear any output before processing
if (ob_get_length() > 0) {
    ob_clean();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id          = $_POST['id'] ?? '';
    $org_id      = $_POST['org_id'] ?? ($_SESSION['org_id'] ?? 1);
    $name        = trim($_POST['name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    
    // Get mobile country code and number separately
    $mobile_country_code = trim($_POST['mobile_country_code'] ?? '+91');
    $mobile_number = trim($_POST['mobile_number'] ?? '');
    
    // Clean mobile number - remove any non-digit characters
    $mobile_number = preg_replace('/[^0-9]/', '', $mobile_number);
    
    $address     = trim($_POST['address'] ?? '');
    $pan_number  = trim($_POST['pan_number'] ?? '');
    $gst_number  = trim($_POST['gst_number'] ?? '');
    $zipcode     = trim($_POST['zipcode'] ?? '');

    // Handle currency - if empty, set to NULL
    $currency_symbol_id = !empty($_POST['currency_symbol_id']) ? (int)$_POST['currency_symbol_id'] : "NULL";
    
    // Handle other optional fields - FIXED: Handle empty strings properly
    $country = (!empty($_POST['country_id']) && $_POST['country_id'] !== '') ? (int)$_POST['country_id'] : "NULL";
    $state   = (!empty($_POST['state_id']) && $_POST['state_id'] !== '') ? (int)$_POST['state_id'] : "NULL";
    $city    = (!empty($_POST['city_id']) && $_POST['city_id'] !== '') ? (int)$_POST['city_id'] : "NULL";
    
    // FIX: Handle zipcode properly - if empty, set to NULL, otherwise escape as string
    if ($zipcode === '') {
        $zipcode_sql = "NULL";
    } else {
        $zipcode_sql = "'" . mysqli_real_escape_string($conn, $zipcode) . "'";
    }

    $updated_at = date('Y-m-d H:i:s');
    $updated_by = $_SESSION['crm_user_id'] ?? 0;

    // === File upload helper ===
    function uploadFile($field, $folder = "../../uploads/") {
        if (!empty($_FILES[$field]['name'])) {
            $fileName = time() . '_' . basename($_FILES[$field]['name']);
            $targetPath = $folder . $fileName;

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            // Get file info safely
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($finfo, $_FILES[$field]['tmp_name']);
            finfo_close($finfo);

            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['error'] = "Only JPG, PNG, GIF, or WEBP files are allowed.";
                return false;
            }
            
            if ($_FILES[$field]['size'] > 5 * 1024 * 1024) {
                $_SESSION['error'] = "File size must be less than 5MB.";
                return false;
            }
            
            if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetPath)) {
                return $fileName;
            }
        }
        return null;
    }

    // Upload files
    $upload_errors = [];
    $company_logo = uploadFile('company_logo');
    if ($company_logo === false) $upload_errors[] = "company_logo";
    
    $mini_logo = uploadFile('mini_logo');
    if ($mini_logo === false) $upload_errors[] = "mini_logo";
    
    $invoice_logo = uploadFile('invoice_logo');
    if ($invoice_logo === false) $upload_errors[] = "invoice_logo";

    // If there were upload errors, redirect back
    if (!empty($upload_errors)) {
        $_SESSION['error'] = "File upload failed for: " . implode(', ', $upload_errors);
        header("Location: ../company-settings.php");
        exit();
    }

    // Check if company exists for this organization
    $check_query = "SELECT id FROM company_info WHERE org_id = '$org_id' LIMIT 1";
    $check_result = mysqli_query($conn, $check_query);
    
    if (!$check_result) {
        $_SESSION['error'] = "Database error: " . mysqli_error($conn);
        header("Location: ../company-settings.php");
        exit();
    }
    
    if (mysqli_num_rows($check_result) > 0) {
        // UPDATE existing company
        $existing = mysqli_fetch_assoc($check_result);
        $id = $existing['id'];
        
        // Start building update query
        $sql = "UPDATE company_info SET 
            name = '" . mysqli_real_escape_string($conn, $name) . "',
            email = '" . mysqli_real_escape_string($conn, $email) . "',
            mobile_country_code = '" . mysqli_real_escape_string($conn, $mobile_country_code) . "',
            mobile_number = '" . mysqli_real_escape_string($conn, $mobile_number) . "',
            address = '" . mysqli_real_escape_string($conn, $address) . "',
            pan_number = '" . mysqli_real_escape_string($conn, $pan_number) . "',
            gst_number = '" . mysqli_real_escape_string($conn, $gst_number) . "',
            currency_symbol_id = $currency_symbol_id,
            country_id = $country,
            state_id = $state,
            city_id = $city,
            zipcode = $zipcode_sql,
            updated_at = '$updated_at',
            updated_by = '$updated_by'";

        // Add file updates if new files were uploaded
        if ($company_logo) $sql .= ", company_logo = '" . mysqli_real_escape_string($conn, $company_logo) . "'";
        if ($mini_logo)    $sql .= ", mini_logo = '" . mysqli_real_escape_string($conn, $mini_logo) . "'";
        if ($invoice_logo) $sql .= ", invoice_logo = '" . mysqli_real_escape_string($conn, $invoice_logo) . "'";

        $sql .= " WHERE id = '$id' AND org_id = '$org_id'";
    } else {
        // INSERT new company profile
        $sql = "INSERT INTO company_info 
            (name, email, mobile_country_code, mobile_number, address, pan_number, gst_number,
             currency_symbol_id, country_id, state_id, city_id, zipcode,
             company_logo, mini_logo, invoice_logo,
             org_id, created_at, created_by) 
        VALUES (
            '" . mysqli_real_escape_string($conn, $name) . "',
            '" . mysqli_real_escape_string($conn, $email) . "',
            '" . mysqli_real_escape_string($conn, $mobile_country_code) . "',
            '" . mysqli_real_escape_string($conn, $mobile_number) . "',
            '" . mysqli_real_escape_string($conn, $address) . "',
            '" . mysqli_real_escape_string($conn, $pan_number) . "',
            '" . mysqli_real_escape_string($conn, $gst_number) . "',
            $currency_symbol_id,
            $country,
            $state,
            $city,
            $zipcode_sql,
            '" . ($company_logo ? mysqli_real_escape_string($conn, $company_logo) : '') . "',
            '" . ($mini_logo ? mysqli_real_escape_string($conn, $mini_logo) : '') . "',
            '" . ($invoice_logo ? mysqli_real_escape_string($conn, $invoice_logo) : '') . "',
            '$org_id',
            '$updated_at',
            '$updated_by'
        )";
    }

    // Execute SQL
    if (mysqli_query($conn, $sql)) {
        // Clear the currency cache from session
        if (isset($_SESSION['company_currency'])) {
            unset($_SESSION['company_currency']);
        }
        
        // Also clear any cache timestamps
        if (isset($_SESSION['currency_last_updated'])) {
            unset($_SESSION['currency_last_updated']);
        }
        
        // Set success message
        $_SESSION['success'] = empty($id) ? "Company profile created successfully." : "Company profile updated successfully.";
        
    } else {
        $_SESSION['error'] = "Database error: " . mysqli_error($conn);
    }

    // Close database connection
    mysqli_close($conn);
    
    // Clear output buffer before redirect
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Close session
    session_write_close();
    
    // Redirect
    header("Location: ../company-settings.php");
    exit();
}

// Clean up if we get here
while (ob_get_level()) {
    ob_end_clean();
}
?>