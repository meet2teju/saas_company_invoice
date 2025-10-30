<?php
session_start(); // ✅ Required to use $_SESSION
include '../../config/config.php';

if (isset($_GET['user_id'])) {
    $id = intval($_GET['user_id']); // ✅ Always sanitize inputs

    // Execute delete query
    $delete = mysqli_query($conn, "DELETE FROM login WHERE id = '$id'");

    if ($delete) {
        $_SESSION['message'] = 'User deleted successfully';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error deleting user: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    header("Location: ../users.php");
    exit;
} else {
    echo "Invalid request.";
}
