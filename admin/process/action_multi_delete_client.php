<?php
include '../../config/config.php';
session_start(); // Required for session messaging

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['client_ids']) && is_array($_POST['client_ids'])) {
        $user_ids = array_map('intval', $_POST['client_ids']);
        $ids_string = implode(',', $user_ids);

        $sql = "DELETE FROM client WHERE id IN ($ids_string)";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Selected clients deleted successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting clients: " . mysqli_error($conn);
            $_SESSION['message_type'] = "danger";
        }
    } 

    header("Location: ../customers.php");
    exit;
}
?>
