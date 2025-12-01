<?php
session_start();
include '../../config/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = intval($_POST['id']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$updated_by = $_SESSION['crm_user_id'] ?? 0;
$updated_at = date('Y-m-d H:i:s');

$query = "UPDATE expense_category SET 
    name = '$name',
    updated_by = '$updated_by',
    updated_at = '$updated_at'
WHERE id = $id";

$res = mysqli_query($conn, $query);

if (!$res) {
    $_SESSION['message'] = "Update failed! Error: " . mysqli_error($conn);
    $_SESSION['message_type'] = "danger";
} else {
    $_SESSION['message'] = "Expense category updated successfully!";
    $_SESSION['message_type'] = "success";
}

header("Location: ../expense_category.php");
exit;
?>