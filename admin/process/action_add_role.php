<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $org_id = $_SESSION['org_id'] ?? 0;
    $user_id = $_SESSION['user_id'] ?? 0;

    if (!empty($name)) {
        $now = date('Y-m-d H:i:s');

        $sql = "INSERT INTO user_role 
                (`name`, `status`, `org_id`, `is_deleted`, `created_by`, `updated_by`, `created_at`, `updated_at`)
                VALUES 
                ('$name', 1, '$org_id', 0, '$user_id', '$user_id', '$now', '$now')";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = 'Role Added successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error adding role: ' . mysqli_error($conn);
            $_SESSION['message_type'] = 'danger';
        }

        header("Location: ../roles-permissions.php");
        exit;
    }
}
?>
