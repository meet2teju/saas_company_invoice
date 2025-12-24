<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id       = $_POST['id'] ?? '';
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    
    // Get country code and phone number separately
    $mobile_country_code = trim($_POST['mobile_country_code'] ?? '+91');
    $phone_number = trim($_POST['phone_number'] ?? ''); // Just the number without country code
    
    // Clean phone number - remove any non-digit characters
    $phone_number = preg_replace('/[^0-9]/', '', $phone_number);
    
    $dob      = trim($_POST['dob'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    
    // Convert to int only if values are not empty
    $country  = isset($_POST['country']) && $_POST['country'] !== '' ? (int)$_POST['country'] : 'NULL';
    $state    = isset($_POST['state']) && $_POST['state'] !== '' ? (int)$_POST['state'] : 'NULL';
    $city     = isset($_POST['city']) && $_POST['city'] !== '' ? (int)$_POST['city'] : 'NULL';
    
    // Handle zipcode properly
    $zipcode  = trim($_POST['zipcode'] ?? '');
    if ($zipcode === '') {
        $zipcode = 'NULL';
    } else {
        $zipcode = "'" . mysqli_real_escape_string($conn, $zipcode) . "'";
    }
    
    $updated_at = date('Y-m-d H:i:s');
    $updated_by = $_SESSION['crm_user_id'] ?? 0;

    if (!empty($dob)) {
        $dob = date('Y-m-d', strtotime($dob));
        $dob = "'$dob'";
    } else {
        $dob = 'NULL';
    }

    // Handle profile image
    $image = '';
    if (!empty($_FILES['profile_img']['name'])) {
        $image = time() . '_' . basename($_FILES['profile_img']['name']);
        $target_path = '../../uploads/' . $image;
        move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_path);
        $image = "'$image'";
    } else {
        $image = 'profile_img';
    }

    // Email uniqueness check
    $emailCheck = "SELECT id FROM login WHERE email = '".mysqli_real_escape_string($conn, $email)."' AND id != '".mysqli_real_escape_string($conn, $id)."' LIMIT 1";
    $emailResult = mysqli_query($conn, $emailCheck);

    if (mysqli_num_rows($emailResult) > 0) {
        $_SESSION['error'] = "Email already exists. Please use a different email.";
        $_SESSION['message_type'] = 'error';
        header("Location: ../account-settings.php");
        exit();
    }

    // Build update query - store phone number and country code separately
    $sql = "UPDATE login SET 
        name = '" . mysqli_real_escape_string($conn, $name) . "',
        email = '" . mysqli_real_escape_string($conn, $email) . "',
        phone_number = '" . mysqli_real_escape_string($conn, $phone_number) . "',
        mobile_country_code = '" . mysqli_real_escape_string($conn, $mobile_country_code) . "',
        address = '" . mysqli_real_escape_string($conn, $address) . "',
        country = $country,
        state = $state,
        city = $city,
        zipcode = $zipcode,
        updated_at = '$updated_at',
        updated_by = '$updated_by'";

    if ($dob !== 'NULL') {
        $sql .= ", dob = $dob";
    }

    if ($image !== 'profile_img') {
        $sql .= ", profile_img = $image";
    }

    $sql .= " WHERE id = '" . mysqli_real_escape_string($conn, $id) . "'";

    if (mysqli_query($conn, $sql)) {
        // Update session
        $_SESSION['crm_user_name']  = $name;
        $_SESSION['crm_user_email'] = $email;
        $_SESSION['crm_user_phone'] = $mobile_country_code . $phone_number; // Combine for display if needed
        $_SESSION['crm_mobile_country_code'] = $mobile_country_code; // Store separately if needed

        if ($image !== 'profile_img') {
            $_SESSION['crm_profile_img'] = str_replace("'", "", $image);
        }

        $_SESSION['success'] = "Profile updated successfully.";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['error'] = "Failed to update profile. Error: " . mysqli_error($conn);
    }

    header("Location: ../account-settings.php");
    exit();
}