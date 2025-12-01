<?php
include '../../config/config.php';
session_start();

if (isset($_POST['expense_category_ids']) && is_array($_POST['expense_category_ids'])) {
    $ids = array_map('intval', $_POST['expense_category_ids']);
    $idList = implode(',', $ids);

    // Soft delete by setting is_deleted = 1
    $query = "UPDATE expense_category SET is_deleted = 1 WHERE id IN ($idList)";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $_SESSION['message'] = "Selected expense categories deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting expense categories.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "No expense categories selected.";
    $_SESSION['message_type'] = "warning";
}

header("Location: ../expense_category.php");
exit;
?>