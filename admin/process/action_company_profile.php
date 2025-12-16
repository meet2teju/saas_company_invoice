<?php
session_start();
include '../../config/config.php';
require_once '../../config/currency_helper.php';

// DEBUG: Log what's being posted
error_log("DEBUG: Currency symbol ID posted: " . ($_POST['currency_symbol_id'] ?? 'NULL'));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id          = $_POST['id'] ?? '';
    $name        = trim($_POST['name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $phone       = trim($_POST['mobile_number'] ?? '');
    $address     = trim($_POST['address'] ?? '');
    $pan_number  = trim($_POST['pan_number'] ?? '');
    $gst_number  = trim($_POST['gst_number'] ?? '');
    $zipcode     = trim($_POST['zipcode'] ?? '');

    $currency_symbol_id = !empty($_POST['currency_symbol_id']) ? (int)$_POST['currency_symbol_id'] : "NULL";
    $country            = !empty($_POST['country_id']) ? (int)$_POST['country_id'] : "NULL";
    $state              = !empty($_POST['state_id']) ? (int)$_POST['state_id'] : "NULL";
    $city               = !empty($_POST['city_id']) ? (int)$_POST['city_id'] : "NULL";

    // DEBUG: Log the currency ID
    error_log("DEBUG: Processing currency ID: $currency_symbol_id");

    $org_id     = $_SESSION['org_id'] ?? 1;
    $updated_at = date('Y-m-d H:i:s');
    $updated_by = $_SESSION['crm_user_id'] ?? 0;

    // === File upload helper ===
    function uploadFile($field, $folder = "../../uploads/") {
        if (!empty($_FILES[$field]['name'])) {
            $fileName = time() . '_' . basename($_FILES[$field]['name']);
            $targetPath = $folder . $fileName;

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($_FILES[$field]['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['error'] = "Only JPG, PNG, GIF, or WEBP files are allowed.";
                header("Location: ../company-settings.php");
                exit();
            }
            if ($_FILES[$field]['size'] > 5 * 1024 * 1024) {
                $_SESSION['error'] = "File size must be less than 5MB.";
                header("Location: ../company-settings.php");
                exit();
            }
            if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetPath)) {
                return $fileName;
            }
        }
        return null;
    }

    // Upload files
    $company_logo = uploadFile('company_logo');
    $mini_logo    = uploadFile('mini_logo');
    $invoice_logo = uploadFile('invoice_logo');

    if (empty($id)) {
        // === INSERT ===
        $sql = "INSERT INTO company_info 
            (name, email, mobile_number, address, pan_number, gst_number,
             currency_symbol_id, country_id, state_id, city_id, zipcode,
             company_logo, mini_logo, invoice_logo,
             org_id, created_at, created_by) 
        VALUES (
            '" . mysqli_real_escape_string($conn, $name) . "',
            '" . mysqli_real_escape_string($conn, $email) . "',
            '" . mysqli_real_escape_string($conn, $phone) . "',
            '" . mysqli_real_escape_string($conn, $address) . "',
            '" . mysqli_real_escape_string($conn, $pan_number) . "',
            '" . mysqli_real_escape_string($conn, $gst_number) . "',
            $currency_symbol_id,
            $country,
            $state,
            $city,
            '" . mysqli_real_escape_string($conn, $zipcode) . "',
            '" . ($company_logo ?: '') . "',
            '" . ($mini_logo ?: '') . "',
            '" . ($invoice_logo ?: '') . "',
            '$org_id',
            '$updated_at',
            '$updated_by'
        )";
    } else {
        // === UPDATE ===
        $sql = "UPDATE company_info SET 
            name = '" . mysqli_real_escape_string($conn, $name) . "',
            email = '" . mysqli_real_escape_string($conn, $email) . "',
            mobile_number = '" . mysqli_real_escape_string($conn, $phone) . "',
            address = '" . mysqli_real_escape_string($conn, $address) . "',
            pan_number = '" . mysqli_real_escape_string($conn, $pan_number) . "',
            gst_number = '" . mysqli_real_escape_string($conn, $gst_number) . "',
            currency_symbol_id = $currency_symbol_id,
            country_id = $country,
            state_id = $state,
            city_id = $city,
            zipcode = '" . mysqli_real_escape_string($conn, $zipcode) . "',
            updated_at = '$updated_at',
            updated_by = '$updated_by'";

        if ($company_logo) $sql .= ", company_logo = '" . mysqli_real_escape_string($conn, $company_logo) . "'";
        if ($mini_logo)    $sql .= ", mini_logo = '" . mysqli_real_escape_string($conn, $mini_logo) . "'";
        if ($invoice_logo) $sql .= ", invoice_logo = '" . mysqli_real_escape_string($conn, $invoice_logo) . "'";

        $sql .= " WHERE id = '" . mysqli_real_escape_string($conn, $id) . "'";
    }

    // DEBUG: Log the SQL
    error_log("DEBUG: SQL Query: $sql");

    // Run SQL
    if (mysqli_query($conn, $sql)) {
        // DEBUG: Log before clearing cache
        error_log("DEBUG: Before clearing cache - Session currency: " . 
                 (isset($_SESSION['company_currency']['id']) ? $_SESSION['company_currency']['id'] : 'NOT SET'));
        
        // ✅ SIMPLIFIED CURRENCY CACHE UPDATE
        // Method 1: Direct session update - SIMPLEST APPROACH
        if ($currency_symbol_id !== "NULL") {
            // Fetch the new currency from database
            $currency_query = "SELECT id, currency_name, currency_symbol, isocode FROM currency WHERE id = $currency_symbol_id LIMIT 1";
            $currency_result = mysqli_query($conn, $currency_query);
            
            if ($currency_result && mysqli_num_rows($currency_result) > 0) {
                $new_currency = mysqli_fetch_assoc($currency_result);
                
                // DEBUG: Log the new currency
                error_log("DEBUG: New currency fetched: ID=" . $new_currency['id'] . ", Name=" . $new_currency['currency_name']);
                
                // Update session directly
                $_SESSION['company_currency'] = [
                    'id' => $new_currency['id'],
                    'currency_name' => $new_currency['currency_name'],
                    'currency_symbol' => $new_currency['currency_symbol'],
                    'currency_code' => $new_currency['isocode'] ?? 'INR'
                ];
                
                // DEBUG: Log after update
                error_log("DEBUG: After update - Session currency ID: " . $_SESSION['company_currency']['id']);
            }
        }
        
        // Also clear any cache timestamps
        unset($_SESSION['currency_last_updated']);
        
        $_SESSION['success'] = empty($id) ? "Company created successfully." : "Company updated successfully.";
        
        // DEBUG: Final session check
        error_log("DEBUG: Final session currency ID: " . 
                 (isset($_SESSION['company_currency']['id']) ? $_SESSION['company_currency']['id'] : 'NOT SET'));
    } else {
        $_SESSION['error'] = "DB Error: " . mysqli_error($conn);
        error_log("ERROR: Database error: " . mysqli_error($conn));
    }

    header("Location: ../company-settings.php");
    exit();
}
?>