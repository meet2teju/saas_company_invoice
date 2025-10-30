<?php
session_start();
include '../../config/config.php';

// Check if user has permission to delete tasks
// if (!isset($_SESSION['role_id']) || check_is_access_new("delete_task") != 1) {
//     $_SESSION['message'] = "You don't have permission to delete tasks.";
//     $_SESSION['message_type'] = 'danger';
//     header("Location: ../project-tasks.php");
//     exit();
// }

// Check if task IDs are provided
if (isset($_POST['task_ids']) && is_array($_POST['task_ids'])) {
    $task_ids = $_POST['task_ids'];
    
    // Sanitize and validate IDs
    $valid_ids = array();
    foreach ($task_ids as $id) {
        $id = intval($id);
        if ($id > 0) {
            $valid_ids[] = $id;
        }
    }
    
    if (!empty($valid_ids)) {
        $ids_string = implode(',', $valid_ids);
        
        // Soft delete the tasks (set is_deleted = 1)
        $sql = "UPDATE project_task SET is_deleted = 1 WHERE id IN ($ids_string)";
        
        if (mysqli_query($conn, $sql)) {
            $count = mysqli_affected_rows($conn);
            $_SESSION['message'] = "Successfully deleted $count task(s).";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error deleting tasks: " . mysqli_error($conn);
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        $_SESSION['message'] = "No valid tasks selected for deletion.";
        $_SESSION['message_type'] = 'warning';
    }
} else {
    $_SESSION['message'] = "No tasks selected for deletion.";
    $_SESSION['message_type'] = 'warning';
}

header("Location: ../project-tasks.php");
exit();
?>