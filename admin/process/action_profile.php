<?php
// ===== Enable error reporting for development =====
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();
include '../../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id       = $_POST['id'] ?? '';
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone_number'] ?? '');
    $dob      = trim($_POST['dob'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    
    // Convert to int only if values are not empty
    $country  = isset($_POST['country']) && $_POST['country'] !== '' ? (int)$_POST['country'] : 'NULL';
    $state    = isset($_POST['state']) && $_POST['state'] !== '' ? (int)$_POST['state'] : 'NULL';
    $city     = isset($_POST['city']) && $_POST['city'] !== '' ? (int)$_POST['city'] : 'NULL';
    
    $zipcode  = trim($_POST['zipcode'] ?? '');
    $updated_at = date('Y-m-d H:i:s');
    $updated_by = $_SESSION['crm_user_id'] ?? 0;

    if (!empty($dob)) {
        $dob = date('Y-m-d', strtotime($dob));
    } else {
        $dob = 'NULL';
    }

    // === Handle profile image ===
    $image = '';
    if (!empty($_FILES['profile_img']['name'])) {
        $image = time() . '_' . basename($_FILES['profile_img']['name']);
        $target_path = '../../uploads/' . $image;
        move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_path);
    }

    // === Build update query ===
    $sql = "UPDATE login SET 
        name = '" . mysqli_real_escape_string($conn, $name) . "',
        email = '" . mysqli_real_escape_string($conn, $email) . "',
        phone_number = '" . mysqli_real_escape_string($conn, $phone) . "',
        address = '" . mysqli_real_escape_string($conn, $address) . "',
        country = $country,
        state = $state,
        city = $city,
        zipcode = '" . mysqli_real_escape_string($conn, $zipcode) . "',
        updated_at = '$updated_at',
        updated_by = '$updated_by'";
 $emailCheck = "SELECT id FROM login WHERE email = '".mysqli_real_escape_string($conn, $email)."' AND id != '".mysqli_real_escape_string($conn, $id)."' LIMIT 1";
    $emailResult = mysqli_query($conn, $emailCheck);

    if (mysqli_num_rows($emailResult) > 0) {
        $_SESSION['error'] = "Email already exists. Please use a different email.";
        $_SESSION['message_type'] = 'error';
        header("Location: ../account-settings.php");
        exit();
    }

    if ($dob !== 'NULL') {
        $sql .= ", dob = '$dob'";
    } else {
        $sql .= ", dob = NULL";
    }

    if ($image != '') {
        $sql .= ", profile_img = '$image'";
    }

    $sql .= " WHERE id = '" . mysqli_real_escape_string($conn, $id) . "'";

    if (mysqli_query($conn, $sql)) {
        // === Update session for topbar ===
        $_SESSION['crm_user_name']  = $name;
        $_SESSION['crm_user_email'] = $email;
        $_SESSION['crm_user_phone'] = $phone;

        if ($image != '') {
            $_SESSION['crm_profile_img'] = $image;
        }

        $_SESSION['success'] = "Profile updated successfully.";
          $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['error'] = "Failed to update profile. Error: " . mysqli_error($conn);
    }

    header("Location: ../account-settings.php");
    exit();
}
?>
