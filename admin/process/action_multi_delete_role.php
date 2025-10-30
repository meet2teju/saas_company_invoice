<?php
session_start();
include '../../config/config.php';

// Check if user has permission to delete roles
if (!isset($_SESSION['role_id']) || check_is_access_new("delete_role") != 1) {
    $_SESSION['message'] = "You don't have permission to delete roles.";
    $_SESSION['message_type'] = 'danger';
    header("Location: ../roles.php");
    exit();
}

// Check if role IDs are provided
if (isset($_POST['role_ids']) && is_array($_POST['role_ids'])) {
    $role_ids = $_POST['role_ids'];
    
    // Sanitize and validate IDs
    $valid_ids = array();
    foreach ($role_ids as $id) {
        $id = intval($id);
        if ($id > 0) {
            $valid_ids[] = $id;
        }
    }
    
    if (!empty($valid_ids)) {
        $ids_string = implode(',', $valid_ids);
        
        // Soft delete the roles (set is_deleted = 1)
        $sql = "UPDATE user_role SET is_deleted = 1 WHERE id IN ($ids_string)";
        
        if (mysqli_query($conn, $sql)) {
            $count = mysqli_affected_rows($conn);
            $_SESSION['message'] = "Successfully deleted $count role(s).";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error deleting roles: " . mysqli_error($conn);
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        $_SESSION['message'] = "No valid roles selected for deletion.";
        $_SESSION['message_type'] = 'warning';
    }
} else {
    $_SESSION['message'] = "No roles selected for deletion.";
    $_SESSION['message_type'] = 'warning';
}

header("Location: ../roles-permissions.php");
exit();
?>