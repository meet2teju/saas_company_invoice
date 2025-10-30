<?php
session_start();
include '../../config/config.php';

// Check if user has permission to delete banks
if (!isset($_SESSION['role_id'])) {
    $_SESSION['message'] = "You don't have permission to delete banks.";
    $_SESSION['message_type'] = 'danger';
    header("Location: ../bank-details.php");
    exit();
}

// Check if bank IDs are provided
if (isset($_POST['bank_ids']) && is_array($_POST['bank_ids'])) {
    $bank_ids = $_POST['bank_ids'];
    
    // Sanitize and validate IDs
    $valid_ids = array();
    foreach ($bank_ids as $id) {
        $id = intval($id);
        if ($id > 0) {
            $valid_ids[] = $id;
        }
    }
    
    if (!empty($valid_ids)) {
        $ids_string = implode(',', $valid_ids);
        
        // Delete the banks
        $sql = "DELETE FROM bank WHERE id IN ($ids_string)";
        
        if (mysqli_query($conn, $sql)) {
            $count = mysqli_affected_rows($conn);
            $_SESSION['message'] = "Successfully deleted $count bank(s).";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error deleting banks: " . mysqli_error($conn);
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        $_SESSION['message'] = "No valid banks selected for deletion.";
        $_SESSION['message_type'] = 'warning';
    }
} else {
    $_SESSION['message'] = "No banks selected for deletion.";
    $_SESSION['message_type'] = 'warning';
}

header("Location: ../bank.php");
exit();
?>