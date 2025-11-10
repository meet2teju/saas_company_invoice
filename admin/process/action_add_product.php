<?php
session_start();
include '../../config/config.php';

// Show PHP errors (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['product'])) {
    // Get logged-in user ID and organization ID from session
    $currentUserId = $_SESSION['crm_user_id'] ?? 1;
    $orgId = $_SESSION['org_id'] ?? 1; // Add this line to get org_id
    
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $item_type = ($_POST['item_type'] == "1") ? 1 : 0;

    $category_id     = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : "NULL";
    $selling_price   = !empty($_POST['selling_price']) ? (float)$_POST['selling_price'] : 0.00;
    $purchase_price  = !empty($_POST['purchase_price']) ? (float)$_POST['purchase_price'] : 0.00;
    $quantity        = !empty($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $unit_id         = !empty($_POST['unit_id']) ? (int)$_POST['unit_id'] : "NULL";

    // Important: discount_type is enum('%','fixed'), must be one of these or NULL
    $discount_type   = (!empty($_POST['discount_type']) && in_array($_POST['discount_type'], ['%', 'fixed'])) 
                        ? "'" . mysqli_real_escape_string($conn, $_POST['discount_type']) . "'" 
                        : "NULL";

    $tax_id          = !empty($_POST['tax_id']) ? (int)$_POST['tax_id'] : "NULL";
    $alert_quantity  = !empty($_POST['alert_quantity']) ? (int)$_POST['alert_quantity'] : 0;
    $description     = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

    // Check duplicate code WITH org_id filter (similar to client reference)
    $check = mysqli_query($conn, "SELECT id FROM product WHERE code = '$code' AND org_id = '$orgId' AND is_deleted = 0");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['message'] = 'Product Or Service code already exists.';
        $_SESSION['message_type'] = 'error';
        header('Location: ../products.php');
        exit();
    }

    // Handle image upload
    $image_name = '';
    if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] == 0) {
        $targetDir = "../../uploads/";
        $image_name = time() . '_' . basename($_FILES["product_img"]["name"]);
        $targetFile = $targetDir . $image_name;

        $validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (in_array($_FILES["product_img"]["type"], $validTypes) && $_FILES["product_img"]["size"] <= 5 * 1024 * 1024) {
            move_uploaded_file($_FILES["product_img"]["tmp_name"], $targetFile);
        }
    }

    // Use logged-in user ID for user_id column and org_id from session
    $user_id = $currentUserId;

    // **UPDATED QUERY: Added org_id, user_id, and other common fields from reference**
    $query = "INSERT INTO product 
        (name, item_type, code, category_id, selling_price, purchase_price, quantity, unit_id, 
         discount_type, tax_id, alert_quantity, description, product_img, 
         user_id, org_id, status, is_deleted, created_by, updated_by, created_at)
        VALUES 
        ('$name', '$item_type', '$code', $category_id, '$selling_price', '$purchase_price', '$quantity', $unit_id, 
         $discount_type, $tax_id, '$alert_quantity', '$description', '$image_name', 
         '$user_id', '$orgId', 1, 0, '$currentUserId', '$currentUserId', NOW())";

    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Product or Service added successfully';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error adding Product: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    header('Location: ../products.php');
    exit();
}
?>