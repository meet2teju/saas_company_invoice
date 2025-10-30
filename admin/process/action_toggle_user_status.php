<?php
include '../../config/config.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = (int)$_GET['status'];

    $sql = "UPDATE login SET status = $status WHERE id = $id";
    mysqli_query($conn, $sql);
}

// go back to user list
header("Location: ../users.php");
exit();
