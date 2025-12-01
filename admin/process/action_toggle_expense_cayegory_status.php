<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $status = intval($_POST['status']); // 1 = Active, 0 = Inactive
    
    $query = "UPDATE expense_category SET status = '$status' WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($conn);
    }
}
?>