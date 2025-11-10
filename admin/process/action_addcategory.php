<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../../config/config.php';

function uploadFile($file, $uploadDir) {
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $fileName;
    }
    return null;
}

// Check if form is submitted (POST method and has required fields)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $currentUserId = $_SESSION['crm_user_id'] ?? 1;
    $orgId = $_SESSION['org_id'] ?? 1;

    mysqli_begin_transaction($conn);

    try {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $slug = mysqli_real_escape_string($conn, $_POST['slug'] ?? '');
        $item_type = isset($_POST['item_type']) ? (int)$_POST['item_type'] : 1;

        // Check if name is provided
        if (empty($name)) {
            throw new Exception("Category name is required");
        }

        // Check duplicate name WITH org_id filter
        $checkQuery = "SELECT id FROM category WHERE name = '$name' AND org_id = '$orgId' AND is_deleted = 0";
        $result = mysqli_query($conn, $checkQuery);
        if (mysqli_num_rows($result) > 0) {
            $_SESSION['message'] = 'Category name already exists. Please use another name.';
            $_SESSION['message_type'] = 'error';
            header("Location: ../category.php");
            exit();
        }

        // Upload category image
        $imageName = '';
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0) {
            $imageName = uploadFile($_FILES['image'], '../../uploads/');
        }

        // If slug is empty, generate from name
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }

        // Insert category with user_id and org_id
        $query = "INSERT INTO category (
            name, slug, image, category_type, status, 
            org_id, user_id, is_deleted, created_by, updated_by, created_at
        ) VALUES (
            '$name', '$slug', '$imageName', '$item_type', 1,
            '$orgId', '$currentUserId', 0, '$currentUserId', '$currentUserId', NOW()
        )";

        if (!mysqli_query($conn, $query)) {
            throw new Exception("Category insert failed: " . mysqli_error($conn));
        }

        // Commit everything
        mysqli_commit($conn);
        $_SESSION['message'] = "Category added successfully!";
        $_SESSION['message_type'] = 'success';
        header("Location: ../category.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['message'] = "Error adding category: " . $e->getMessage();
        $_SESSION['message_type'] = 'error';
        header("Location: ../category.php");
        exit();
    }
} else {
    // If someone accesses this file directly
    $_SESSION['message'] = 'Invalid request';
    $_SESSION['message_type'] = 'error';
    header("Location: ../category.php");
    exit;
}
?>