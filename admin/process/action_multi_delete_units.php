<?php
include '../../config/config.php';
session_start();

if (isset($_POST['units_ids']) && is_array($_POST['units_ids'])) {
    $ids = array_map('intval', $_POST['units_ids']);
    $idList = implode(',', $ids);

    $query = "DELETE FROM units WHERE id IN ($idList)";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $_SESSION['message'] = "Selected units deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting units.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "No units selected.";
    $_SESSION['message_type'] = "warning";
}

header("Location: ../units.php"); // Adjust this path if your filename is different
exit;
