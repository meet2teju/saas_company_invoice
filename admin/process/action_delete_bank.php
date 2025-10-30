<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $sql = "DELETE FROM bank WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Bank deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting bank.";
        $_SESSION['message_type'] = "danger";
    }
}
header('Location: ../bank.php');
exit();
