<?php
include '../../config/config.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['user_ids']) && is_array($_POST['user_ids'])) {
        $user_ids = array_map('intval', $_POST['user_ids']);
        $ids_string = implode(',', $user_ids);

        $sql = "DELETE FROM login WHERE id IN ($ids_string)";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Selected Users deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting Users.";
        $_SESSION['message_type'] = "danger";
    }
    } else {
        echo "No users selected.";
    }
} else {
    echo "Invalid request.";
}
header("Location: ../users.php"); // Adjust this path if your filename is different
exit;
?>
