<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id      = $_POST['client_id'];
    $first_name     = $_POST['first_name'];
    $email          = $_POST['email'];
    $phone_number   = $_POST['phone_number'];
    $company_name   = $_POST['company_name'];
    $website_url    = $_POST['website_url'];
    $old_image      = $_POST['old_image'];

    $image_name = $old_image;

    // Check if new image uploaded
    if (!empty($_FILES['image']['name'])) {
        $img = $_FILES['image'];
        $ext = pathinfo($img['name'], PATHINFO_EXTENSION);
        $image_name = time() . '.' . $ext;
        move_uploaded_file($img['tmp_name'], '../../uploads/' . $image_name);
    }

    // Update query
    $query = "UPDATE client SET 
        first_name = '$first_name',
        email = '$email',
        phone_number = '$phone_number',
        company_name = '$company_name',
        website_url = '$website_url',
        customer_image = '$image_name'
        WHERE id = $client_id";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Client updated successfully.";
    } else {
        $_SESSION['error'] = "Update failed.";
    }

    header("Location: ../customer-details.php?id=$client_id");
    exit;
}
?>
