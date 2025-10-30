<?php
include '../../config/config.php';
session_start();

if (isset($_POST['category_ids']) && is_array($_POST['category_ids'])) {
    $ids = array_map('intval', $_POST['category_ids']);
    $idList = implode(',', $ids);

    $query = "DELETE FROM category WHERE id IN ($idList)";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $_SESSION['message'] = "Selected categories deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting categories.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "No categories selected.";
    $_SESSION['message_type'] = "warning";
}

header("Location: ../category.php"); // Adjust this path if your filename is different
exit;
