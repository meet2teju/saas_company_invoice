<?php
session_start();
include '../../config/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function uploadFile($file, $uploadDir) {
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $fileName;
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get organization ID from session (current user's organization)
    $orgId = $_SESSION['org_id'] ?? 1;

    mysqli_begin_transaction($conn);

    try {
        $name       = trim($_POST['name'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $phone      = trim($_POST['phone_number'] ?? '');
        $password   = trim($_POST['password'] ?? '');
        $type_id    = $_POST['role_id'] ?? '';
        $status     = $_POST['status'] ?? '';
        $image_name = '';

        // Check duplicate email WITH org_id filter
        $checkEmail = mysqli_query($conn, "SELECT id FROM login WHERE email = '$email' AND org_id = '$orgId' AND is_deleted = 0");
        if (mysqli_num_rows($checkEmail) > 0) {
            $_SESSION['message'] = 'Email already exists. Please use another email.';
            $_SESSION['message_type'] = 'error';
            header("Location: ../users.php");
            exit();
        }

        // Handle file upload
        if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0) {
            $image_name = uploadFile($_FILES['profile_img'], '../../uploads/');
        }

        $hashedPassword = md5($password);

        // **UPDATED QUERY: Only store org_id (no user_id since this is the user table)**
        $query = "INSERT INTO login (
            name, email, phone_number, password, role_id, status, profile_img, 
            org_id, is_deleted, created_at, updated_at,
            reset_token, reset_token_expire
        ) VALUES (
            '$name', '$email', '$phone', '$hashedPassword', '$type_id', '$status', '$image_name',
            '$orgId', 0, NOW(), NOW(),
            NULL, NULL
        )";

        if (!mysqli_query($conn, $query)) {
            throw new Exception("User insert failed: " . mysqli_error($conn));
        }

        // Commit transaction
        mysqli_commit($conn);
        
        $_SESSION['message'] = 'User added successfully';
        $_SESSION['message_type'] = 'success';

    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        $_SESSION['message'] = 'Error adding User: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
    }

    header("Location: ../users.php");
    exit();
} else {
    echo "Invalid request.";
}
?>