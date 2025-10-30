<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['user_id'];
    $name = $_POST['name'];

    if (!empty($id) && !empty($name)) {
        $sql = "UPDATE user_role SET name = '$name', updated_at = NOW() WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = 'Role updated successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error updating role: ' . mysqli_error($conn);
            $_SESSION['message_type'] = 'danger';
        }

    }
    header("Location: ../roles-permissions.php");
    exit;
}
?>
