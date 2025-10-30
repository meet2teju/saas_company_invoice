<?php
session_start(); // ✅ Needed to use $_SESSION
include '../../config/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id']; 

    $delete = mysqli_query($conn, "DELETE FROM units WHERE id = '$id'");

    if ($delete) {
        $_SESSION['message'] = 'Unit deleted successfully';
        $_SESSION['message_type'] = 'success';

       
    } else {
        $_SESSION['message'] = 'Failed to delete unit: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';

    }
} 

    header("Location: ../units.php");
    exit();
?>