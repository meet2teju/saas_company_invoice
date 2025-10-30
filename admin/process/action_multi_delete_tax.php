<?php
session_start();
include '../../config/config.php';

// Check if user has permission to delete tax rates
if (!isset($_SESSION['role_id']) || check_is_access_new("delete_tax") != 1) {
    $_SESSION['message'] = "You don't have permission to delete tax rates.";
    $_SESSION['message_type'] = 'danger';
    header("Location: ../tax-rates.php");
    exit();
}

// Check if tax IDs are provided
if (isset($_POST['tax_ids']) && is_array($_POST['tax_ids'])) {
    $tax_ids = $_POST['tax_ids'];
    
    // Sanitize and validate IDs
    $valid_ids = array();
    foreach ($tax_ids as $id) {
        $id = intval($id);
        if ($id > 0) {
            $valid_ids[] = $id;
        }
    }
    
    if (!empty($valid_ids)) {
        $ids_string = implode(',', $valid_ids);
        
        // Delete the tax rates
        $sql = "DELETE FROM tax WHERE id IN ($ids_string)";
        
        if (mysqli_query($conn, $sql)) {
            $count = mysqli_affected_rows($conn);
            $_SESSION['message'] = "Successfully deleted $count tax rate(s).";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error deleting tax rates: " . mysqli_error($conn);
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        $_SESSION['message'] = "No valid tax rates selected for deletion.";
        $_SESSION['message_type'] = 'warning';
    }
} else {
    $_SESSION['message'] = "No tax rates selected for deletion.";
    $_SESSION['message_type'] = 'warning';
}

header("Location: ../tax-rates.php");
exit();
?>