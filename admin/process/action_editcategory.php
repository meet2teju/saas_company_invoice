<?php
session_start();
include '../../config/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = intval($_POST['id']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$slug = mysqli_real_escape_string($conn, $_POST['slug']);
$current_image = $_POST['current_image'] ?? '';
$category_type = intval($_POST['category_type'] ?? 0);
$status = intval($_POST['status'] ?? 1);
$updated_by = intval($_SESSION['user_id'] ?? 0); // or from POST if you send it
$org_id = intval($_SESSION['org_id'] ?? 0); // or from POST if you send it

// Default to current image
$image_name = $current_image;

// Handle new image upload
if (!empty($_FILES['image']['name'])) {
    $img = $_FILES['image'];
    $ext = pathinfo($img['name'], PATHINFO_EXTENSION);
    $image_name = time() . '.' . $ext;
    move_uploaded_file($img['tmp_name'], '../../uploads/' . $image_name);
}

$query = "UPDATE category SET 
    image = '$image_name',
    category_type = '$category_type',
    name = '$name',
    slug = '$slug',
    status = '$status',
    updated_by = '$updated_by',
    org_id = '$org_id'
WHERE id = $id";

$res = mysqli_query($conn, $query);

if (!$res) {
    $_SESSION['message'] = "Update failed! Error: " . mysqli_error($conn);
    $_SESSION['message_type'] = "danger";
} else {
    $_SESSION['message'] = "Category updated successfully!";
    $_SESSION['message_type'] = "success";
}

header("Location: ../category.php");
exit;
?>
