<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $org_id = $_SESSION['org_id'] ?? 0;
    $created_by = $_SESSION['crm_user_id'] ?? 0;
    $created_at = date('Y-m-d H:i:s');
    $is_deleted = 0;
    $status = 1; // Default status is active (1)

    $sql = "INSERT INTO expense_category (name, org_id, is_deleted, created_by, created_at, status)
            VALUES ('$name', '$org_id', '$is_deleted', '$created_by', '$created_at', '$status')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = 'Expense category added successfully';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error adding expense category: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    header("Location: ../expense_category.php");
    exit;
}
?>