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
    if (!empty($_POST['discount_type']) && in_array($_POST['discount_type'], ['%', 'fixed'])) {
        $discount_type = mysqli_real_escape_string($conn, $_POST['discount_type']);
    }

    $tax_id          = !empty($_POST['tax_id']) ? (int)$_POST['tax_id'] : NULL;
    $alert_quantity  = !empty($_POST['alert_quantity']) ? (int)$_POST['alert_quantity'] : 0;
    $description     = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

    // Check duplicate code WITH org_id filter
    $checkQuery = "SELECT id FROM product WHERE code = '$code' AND org_id = '$orgId' AND is_deleted = 0";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (!$checkResult) {
        $_SESSION['message'] = 'Error checking duplicate: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
        header('Location: ../add_product.php?error=query');
        exit();
    }

    if (mysqli_num_rows($checkResult) > 0) {
        // Store the form data in session to repopulate form
        $_SESSION['old_product_data'] = $_POST;
        $_SESSION['message'] = 'HSN code already exists.';
        $_SESSION['message_type'] = 'error';
        header('Location: ../add_product.php?error=duplicate');
        exit();
    }

    // Handle image upload
    $image_name = '';
    if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] == 0) {
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
                $image_name = ''; // Reset if upload fails
            }
        }
    }

    // Build the INSERT query with proper NULL handling
    $query = "INSERT INTO product 
        (name, item_type, code, category_id, selling_price, purchase_price, quantity, unit_id, 
         discount_type, tax_id, alert_quantity, description, product_img, 
         user_id, org_id, status, is_deleted, created_by, updated_by, created_at)
        VALUES 
        ('$name', '$item_type', '$code', ";
    
    // Handle NULL values properly
    $query .= (is_null($category_id) ? "NULL" : "'$category_id'") . ", ";
    $query .= "'$selling_price', '$purchase_price', '$quantity', ";
    $query .= (is_null($unit_id) ? "NULL" : "'$unit_id'") . ", ";
    $query .= (is_null($discount_type) ? "NULL" : "'$discount_type'") . ", ";
    $query .= (is_null($tax_id) ? "NULL" : "'$tax_id'") . ", ";
    $query .= "'$alert_quantity', '$description', '$image_name', 
     '$currentUserId', '$orgId', 1, 0, '$currentUserId', '$currentUserId', NOW())";

    // Execute the query
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Product or Service added successfully';
        $_SESSION['message_type'] = 'success';
        header('Location: ../products.php');
        exit();
    } else {
        $error = mysqli_error($conn);
        if (mysqli_errno($conn) == 1062) { // 1062 is MySQL error code for duplicate entry
            $_SESSION['old_product_data'] = $_POST;
            $_SESSION['message'] = 'HSN code already exists in your organization.';
            $_SESSION['message_type'] = 'error';
            header('Location: ../add_product.php?error=duplicate');
            exit();
        } else {
            $_SESSION['old_product_data'] = $_POST;
            $_SESSION['message'] = 'Error adding product: ' . $error;
            $_SESSION['message_type'] = 'error';
            header('Location: ../add_product.php?error=database');
            exit();
        }
    }
    
} else {
    // If not POST request, redirect
    header('Location: ../add_product.php');
    exit();
}
?>