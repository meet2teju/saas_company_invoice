<?php
session_start();
include '../../config/config.php';

$user_id = $_SESSION['crm_user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] === 'delete') {
    $getQuery = mysqli_query($conn, "SELECT profile_img FROM login WHERE id = '$user_id'");
    $data = mysqli_fetch_assoc($getQuery);

    if (!empty($data['profile_img'])) {
        $image_path = "../../uploads/" . $data['profile_img'];
        if (file_exists($image_path)) {
            unlink($image_path); 
        }
    }

    $update = mysqli_query($conn, "UPDATE login SET profile_img = '' WHERE id = '$user_id'");

    if ($update) {
        
        $_SESSION['crm_profile_img'] = '';

        echo "success";
    } else {
        echo "error";
    }
}
?>
