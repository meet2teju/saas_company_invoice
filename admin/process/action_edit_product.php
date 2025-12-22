<?php
session_start();
include '../../config/config.php';

// Show PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get logged-in user ID and organization ID from session
    $currentUserId = $_SESSION['crm_user_id'] ?? 1;
    $orgId = $_SESSION['org_id'] ?? 1;
    
    // Get form data
    $product_id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $code = mysqli_real_escape_string($conn, $_POST['code'] ?? '');
    $item_type = isset($_POST['item_type']) && $_POST['item_type'] == "1" ? 1 : 0;

    $category_id     = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : NULL;
    $selling_price   = !empty($_POST['selling_price']) ? (float)$_POST['selling_price'] : 0.00;
    $purchase_price  = !empty($_POST['purchase_price']) ? (float)$_POST['purchase_price'] : 0.00;
    $quantity        = !empty($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $unit_id         = !empty($_POST['unit_id']) ? (int)$_POST['unit_id'] : NULL;

    // Handle discount_type
    $discount_type = NULL;
    if (!empty($_POST['discount_type']) && in_array($_POST['discount_type'], ['%', 'Fixed', 'fixed'])) {
        $discount_type = mysqli_real_escape_string($conn, $_POST['discount_type']);
    }

    $tax_id          = !empty($_POST['tax_id']) ? (int)$_POST['tax_id'] : NULL;
    $alert_quantity  = !empty($_POST['alert_quantity']) ? (int)$_POST['alert_quantity'] : 0;
    $description     = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    
    $current_image = mysqli_real_escape_string($conn, $_POST['current_image'] ?? '');
    $remove_main_image = isset($_POST['remove_main_image']) ? (int)$_POST['remove_main_image'] : 0;
    
    // REMOVED DUPLICATE HSN CODE CHECK HERE (Same as add product)
    
    // Handle main image upload/removal
    $image_name = $current_image;
    
    // If remove flag is set, delete the image
    if ($remove_main_image == 1) {
        if (!empty($current_image)) {
            $old_image_path = "../../uploads/" . $current_image;
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }
        $image_name = '';
    }
    
    // If a new image is uploaded
    if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] == 0) {
        // Delete old image if exists
        if (!empty($current_image)) {
            $old_image_path = "../../uploads/" . $current_image;
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }
        
        $targetDir = "../../uploads/";
        
        // Check if uploads directory exists
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $image_name = time() . '_' . basename($_FILES["product_img"]["name"]);
        $targetFile = $targetDir . $image_name;

        $validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (in_array($_FILES["product_img"]["type"], $validTypes) && $_FILES["product_img"]["size"] <= 5 * 1024 * 1024) {
            if (!move_uploaded_file($_FILES["product_img"]["tmp_name"], $targetFile)) {
                $image_name = $current_image; // Keep old image if upload fails
            }
        } else {
            $image_name = $current_image; // Keep old image if validation fails
        }
    }

    // Build the UPDATE query
    $query = "UPDATE product SET 
        name = '$name',
        item_type = '$item_type',
        code = '$code',
        category_id = " . (is_null($category_id) ? "NULL" : "'$category_id'") . ",
        selling_price = '$selling_price',
        purchase_price = '$purchase_price',
        quantity = '$quantity',
        unit_id = " . (is_null($unit_id) ? "NULL" : "'$unit_id'") . ",
        discount_type = " . (is_null($discount_type) ? "NULL" : "'$discount_type'") . ",
        tax_id = " . (is_null($tax_id) ? "NULL" : "'$tax_id'") . ",
        alert_quantity = '$alert_quantity',
        description = '$description',
        product_img = '$image_name',
        updated_by = '$currentUserId',
        updated_at = NOW()
        WHERE id = '$product_id' AND org_id = '$orgId'";

    // Execute the update query
    if (mysqli_query($conn, $query)) {
        
        // Handle gallery images deletion
        if (!empty($_POST['deleted_images'])) {
            $deleted_ids = explode(',', $_POST['deleted_images']);
            foreach ($deleted_ids as $img_id) {
                $img_id = (int)$img_id;
                if ($img_id > 0) {
                    // Get image filename before deleting
                    $get_img_query = "SELECT gallery_img FROM product_images WHERE id = $img_id";
                    $img_result = mysqli_query($conn, $get_img_query);
                    if ($img_result && mysqli_num_rows($img_result) > 0) {
                        $img_data = mysqli_fetch_assoc($img_result);
                        $img_path = "../../uploads/" . $img_data['gallery_img'];
                        if (file_exists($img_path)) {
                            unlink($img_path);
                        }
                    }
                    // Delete from database
                    mysqli_query($conn, "DELETE FROM product_images WHERE id = $img_id");
                }
            }
        }
        
        // Handle new gallery image uploads
        if (!empty($_FILES['gallery_img']['name'][0])) {
            $targetDir = "../../uploads/";
            
            foreach ($_FILES['gallery_img']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['gallery_img']['error'][$key] == 0) {
                    $file_name = time() . '_' . $key . '_' . basename($_FILES['gallery_img']['name'][$key]);
                    $targetFile = $targetDir . $file_name;
                    
                    $validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    if (in_array($_FILES['gallery_img']['type'][$key], $validTypes) && 
                        $_FILES['gallery_img']['size'][$key] <= 5 * 1024 * 1024) {
                        
                        if (move_uploaded_file($tmp_name, $targetFile)) {
                            $insert_gallery = "INSERT INTO product_images (product_id, gallery_img) 
                                            VALUES ('$product_id', '$file_name')";
                            mysqli_query($conn, $insert_gallery);
                        }
                    }
                }
            }
        }
        
        $_SESSION['message'] = 'Product updated successfully';
        $_SESSION['message_type'] = 'success';
        header("Location: ../edit-product.php?id=$product_id");
        exit();
    } else {
        $error = mysqli_error($conn);
        $_SESSION['message'] = 'Error updating product: ' . $error;
        $_SESSION['message_type'] = 'error';
         header("Location: ../edit-product.php?id=$product_id");
        exit();
    }
    
} else {
    // If not POST request, redirect
    header('Location: ../products.php');
    exit();
}
?>