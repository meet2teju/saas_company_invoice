<?php
session_start();
include '../../config/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name       = trim($_POST['name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone_number'] ?? '');
    $password   = trim($_POST['password'] ?? '');
    $type_id    = $_POST['role_id'] ?? '';
    $status     = $_POST['status'] ?? '';
    $image_name = '';

    // Validate
    //  $checkEmail = mysqli_query($conn, "SELECT id FROM login WHERE email = '$email' LIMIT 1");
    // if (mysqli_num_rows($checkEmail) > 0) {
    //     $_SESSION['message'] = 'Email already exists. Please use another email.';
    //     $_SESSION['message_type'] = 'error';
    //     header("Location: ../users.php");
    //     exit();
    // }
    // Handle file upload
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0) {
        $targetDir = "../../uploads/";
        $image_name = time() . '_' . basename($_FILES["profile_img"]["name"]);
        $targetFile = $targetDir . $image_name;
        move_uploaded_file($_FILES["profile_img"]["tmp_name"], $targetFile);
    }

    $hashedPassword = md5($password);

            $query = "INSERT INTO login (
                name, email, phone_number, password, role_id, status, profile_img, reset_token, reset_token_expire
            ) VALUES (
                '$name', '$email', '$phone', '$hashedPassword', '$type_id', '$status', '$image_name', NULL, NULL
            )";


    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'User added successfully';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error adding User: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    header("Location: ../users.php");
    exit();
} else {
    echo "Invalid request.";
}
