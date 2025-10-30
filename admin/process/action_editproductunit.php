<?php
include '../../config/config.php';
session_start(); // Needed for $_SESSION

// Get form data
$id = $_POST['id'];
$name =$_POST['name'];
$short_name = $_POST['short_name'];

// Update database
$sql = "UPDATE units SET name='$name', short_name='$short_name' WHERE id=$id";
$result = mysqli_query($conn, $sql);

if ($result) {
    $_SESSION['message'] = 'Unit updated successfully';
    $_SESSION['message_type'] = 'success';

    // Redirect on success
    header("Location: ../units.php");
    exit();
} else {
    echo "Error updating unit: " . mysqli_error($conn);
}
?>
