<?php
// echo 'hiii';
// exit;
session_start();
include '../../config/config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $item_type = $_POST['item_type'] ?? '';
    // $created_by = $_SESSION['crm_user_id'] ?? 0;
    // $org_id = $_SESSION['org_id'] ?? 0;
    // $created_at = date('Y-m-d H:i:s');
    // $is_deleted = 0;

    // Image upload
    $image_name = '';
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_path = '../../uploads/' . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_path);
    }
   

    
    $sql = "INSERT INTO category (category_type,name, slug, image)
            VALUES ('$item_type', '$name', '$slug', '$image_name')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = 'Category added successfully';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error adding Category: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    header("Location: ../category.php");
    exit;
}
?>
