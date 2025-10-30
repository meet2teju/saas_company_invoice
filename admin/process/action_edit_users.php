<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $user_id     = mysqli_real_escape_string($conn, $_POST['user_id']);
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $phone       = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $role_id     = mysqli_real_escape_string($conn, $_POST['role_id']);
    $status      = mysqli_real_escape_string($conn, $_POST['status']);

    $password    = !empty($_POST['password']) ? md5($_POST['password']) : '';
    $profile_img = '';
//  $checkEmail = mysqli_query($conn, "SELECT id FROM login WHERE email = '$email' AND id != '$user_id' LIMIT 1");
//     if (mysqli_num_rows($checkEmail) > 0) {
//         $_SESSION['message'] = "Email already exists. Please use another email.";
//         $_SESSION['message_type'] = "error";
//         header("Location: ../users.php");
//         exit;
//     }

    // Handle image upload
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0) {
        $img_name = time() . '_' . basename($_FILES['profile_img']['name']);
        $target_path = '../../uploads/' . $img_name;
        if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_path)) {
            $profile_img = $img_name;
        }
    }

    // Start building update query
    $query = "UPDATE login SET 
                name = '$name', 
                email = '$email', 
                phone_number = '$phone', 
                role_id = '$role_id', 
                status = '$status'";

    // Update password only if it's provided
    if (!empty($password)) {
        $query .= ", password = '$password'";
    }

    // Update profile image only if uploaded
    if (!empty($profile_img)) {
        $query .= ", profile_img = '$profile_img'";
    }

    $query .= " WHERE id = '$user_id'";

    if (mysqli_query($conn, $query)) {
        // Only update session if the edited user is the currently logged-in user
        if ($user_id == $_SESSION['crm_user_id']) {
            $_SESSION['crm_user_name']  = $name;
            $_SESSION['crm_user_email'] = $email;
            $_SESSION['crm_user_phone'] = $phone;
            
            // Get role name
            $roleQuery = mysqli_query($conn, "SELECT name FROM user_role WHERE id = '$role_id'");
            $roleRow = mysqli_fetch_assoc($roleQuery);
            $roleName = $roleRow['name'] ?? 'User';
            $_SESSION['crm_user_role'] = $roleName;

            if (!empty($profile_img)) {
                $_SESSION['crm_profile_img'] = $profile_img;
            }
        }

        $_SESSION['message'] = "User updated successfully!";
        header("Location: ../users.php");
        $_SESSION['message_type'] = 'success';
        exit;
    } else {
       $_SESSION['message'] = 'Error adding User: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }
} else {
    echo "Invalid request.";
}
?>