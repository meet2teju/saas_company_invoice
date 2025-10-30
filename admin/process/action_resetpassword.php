<?php
session_start();
include '../../config/config.php';

if (isset($_POST['reset'])) {
    $email        = $_POST['email'] ?? '';
    $reset_token  = $_POST['reset_token'] ?? '';
    $password     = $_POST['password'] ?? '';
    $cpassword    = $_POST['cpassword'] ?? '';
    $errors       = [];

    // Check for token & user match
    $query = "SELECT * FROM login WHERE email = '$email' AND reset_token = '$reset_token'";
    $result = mysqli_query($conn, $query);

    // if (!$result || mysqli_num_rows($result) !== 1) {
    //     $errors['global'] = "Invalid or expired reset link.";
    // }

    // Password validation
    if (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    }

    if ($password !== $cpassword) {
        $errors['cpassword'] = "Passwords do not match.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../reset-password.php?email=$email&reset_token=$reset_token");
        exit;
    }

    $hashedPassword = md5($password); 
    $updateQuery = "UPDATE login SET password = '$hashedPassword', reset_token = NULL, reset_token_expire = NULL WHERE email = '$email'";

    if (mysqli_query($conn, $updateQuery)) {
        header("Location: ../login.php?reset=success");
        exit;
    } else {
        $_SESSION['errors']['global'] = "Something went wrong. Try again.";
        header("Location: ../reset-password.php?email=$email&reset_token=$reset_token");
        exit;
    }
} else {
    $_SESSION['errors']['global'] = "Invalid request.";
    header("Location: ../reset-password.php");
    exit;
}
