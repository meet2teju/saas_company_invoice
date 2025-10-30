<?php
session_start();
include '../../config/config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ---------------------------
    // ✅ Collect form data safely
    // ---------------------------
    $id             = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name           = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
    $code           = isset($_POST['code']) ? mysqli_real_escape_string($conn, $_POST['code']) : '';
    $item_type      = isset($_POST['item_type']) && $_POST['item_type'] == "1" ? 1 : 0;
    $category_id    = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : "NULL";
    $selling_price  = !empty($_POST['selling_price']) ? (float)$_POST['selling_price'] : 0.00;
    $purchase_price = !empty($_POST['purchase_price']) ? (float)$_POST['purchase_price'] : 0.00;
    $quantity       = !empty($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $unit_id        = !empty($_POST['unit_id']) ? (int)$_POST['unit_id'] : "NULL";
    $discount_type  = (!empty($_POST['discount_type']) && in_array($_POST['discount_type'], ['%', 'fixed'])) 
                      ? "'" . mysqli_real_escape_string($conn, $_POST['discount_type']) . "'"
                      : "NULL";
    $alert_quantity = !empty($_POST['alert_quantity']) ? (int)$_POST['alert_quantity'] : 0;
    $tax_id         = !empty($_POST['tax_id']) ? (int)$_POST['tax_id'] : "NULL";
    $description    = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : '';
    $updated_at     = date('Y-m-d H:i:s');

    $product_img = isset($_POST['current_image']) ? $_POST['current_image'] : '';
    $remove_main_image = isset($_POST['remove_main_image']) ? (int)$_POST['remove_main_image'] : 0;

    // ---------------------------
    // ✅ Remove main image if requested
    // ---------------------------
    if ($remove_main_image === 1 && !empty($product_img)) {
        $old_path = '../../uploads/' . $product_img;
        if (file_exists($old_path)) unlink($old_path);
        $product_img = '';
    }

    // ---------------------------
    // ✅ Handle new main image upload
    // ---------------------------
    if (isset($_FILES['product_img']['name']) && $_FILES['product_img']['name'] != '') {
        $img_name = time() . '_' . basename($_FILES['product_img']['name']);
        $img_path = '../../uploads/' . $img_name;
        if (move_uploaded_file($_FILES['product_img']['tmp_name'], $img_path)) {
            // Delete previous image if exists
            if (!empty($product_img) && file_exists('../../uploads/' . $product_img)) {
                unlink('../../uploads/' . $product_img);
            }
            $product_img = $img_name;
        }
    }

    // ---------------------------
    // ✅ Update product in database
    // ---------------------------
    $update = "UPDATE product SET 
        name = '$name',
        code = '$code',
        item_type = $item_type,
        category_id = $category_id,
        selling_price = $selling_price,
        purchase_price = $purchase_price,
        quantity = $quantity,
        unit_id = $unit_id,
        discount_type = $discount_type,
        alert_quantity = $alert_quantity,
        tax_id = $tax_id,
        product_img = '$product_img',
        description = '$description',
        updated_at = '$updated_at'
        WHERE id = $id";

    if (mysqli_query($conn, $update)) {
        $productId = $id;

        // ---------------------------
        // ✅ Upload new gallery images
        // ---------------------------
        if (!empty($_FILES['gallery_img']['name'][0])) {
            foreach ($_FILES['gallery_img']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['gallery_img']['error'][$key] === 0) {
                    $file_name = time() . '_' . basename($_FILES['gallery_img']['name'][$key]);
                    $target_path = '../../uploads/' . $file_name;
                    if (move_uploaded_file($tmp_name, $target_path)) {
                        $insert = "INSERT INTO product_images (product_id, gallery_img, created_at) 
                                   VALUES ($productId, '$file_name', NOW())";
                        mysqli_query($conn, $insert);
                    }
                }
            }
        }

        // ---------------------------
        // ✅ Delete removed gallery images
        // ---------------------------
        if (!empty($_POST['deleted_images'])) {
            $deleted_ids = explode(',', $_POST['deleted_images']);
            foreach ($deleted_ids as $img_id) {
                $img_id = (int)$img_id;
                $res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT gallery_img FROM product_images WHERE id = $img_id"));
                if ($res && file_exists('../../uploads/' . $res['gallery_img'])) {
                    unlink('../../uploads/' . $res['gallery_img']);
                }
                mysqli_query($conn, "DELETE FROM product_images WHERE id = $img_id");
            }
        }

        $_SESSION['message'] = 'Product updated successfully';
        $_SESSION['message_type'] = 'success';
        header("Location: ../products.php");
        exit();
    } else {
   
        echo "Error updating product: " . mysqli_error($conn);
        exit();
    }
} else {
    echo "Invalid request method!";
    exit();
}
?>
