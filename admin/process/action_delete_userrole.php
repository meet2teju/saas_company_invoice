<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'])) {
        $id = intval($_POST['user_id']); // sanitize

        // correct column name is "id"
        $sql = "DELETE FROM user_role WHERE id = $id";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Role deleted successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting Role: " . mysqli_error($conn);
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Role ID missing.";
        $_SESSION['message_type'] = "danger";
    }
}

header('Location: ../roles-permissions.php');
exit();
?>
