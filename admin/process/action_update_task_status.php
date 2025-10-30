<?php
session_start();
include '../../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['crm_user_id'])) {
    $_SESSION['message'] = "Please login to continue";
    $_SESSION['message_type'] = "error";
    header("Location: ../login.php");
    exit();
}

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (!isset($_POST['task_id']) || !isset($_POST['status_id'])) {
        $_SESSION['message'] = "Task ID and Status are required";
        $_SESSION['message_type'] = "error";
        header("Location: ../project-tasks.php");
        exit();
    }

    $task_id = intval($_POST['task_id']);
    $status_id = intval($_POST['status_id']);
    $user_id = $_SESSION['crm_user_id'];

    // Validate task exists and user has permission
    try {
        // Check if task exists
        $check_sql = "SELECT pt.*, p.project_name 
                      FROM project_task pt 
                      LEFT JOIN project p ON pt.project_id = p.id 
                      WHERE pt.id = ? AND pt.is_deleted = 0";
        
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "i", $task_id);
        mysqli_stmt_execute($check_stmt);
        $task_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($task_result) === 0) {
            $_SESSION['message'] = "Task not found or already deleted";
            $_SESSION['message_type'] = "error";
            header("Location: ../project-tasks.php");
            exit();
        }
        
        $task_data = mysqli_fetch_assoc($task_result);
        
        // Check if status exists and is valid
        $status_check_sql = "SELECT id, status_name FROM project_status WHERE id = ? AND is_deleted = 0";
        $status_check_stmt = mysqli_prepare($conn, $status_check_sql);
        mysqli_stmt_bind_param($status_check_stmt, "i", $status_id);
        mysqli_stmt_execute($status_check_stmt);
        $status_result = mysqli_stmt_get_result($status_check_stmt);
        
        if (mysqli_num_rows($status_result) === 0) {
            $_SESSION['message'] = "Invalid status selected";
            $_SESSION['message_type'] = "error";
            header("Location: ../task-details.php?id=" . $task_id);
            exit();
        }
        
        $status_data = mysqli_fetch_assoc($status_result);
        
        // Check user permission (admin or project member)
        $role_id = $_SESSION['role_id'];
        $role_query = mysqli_query($conn, "SELECT name FROM user_role WHERE id = $role_id LIMIT 1");
        $role_row = mysqli_fetch_assoc($role_query);
        $role_name = strtolower(trim($role_row['name'] ?? ''));
        
        if ($role_name !== 'admin') {
            // Check if user is assigned to the project
            $access_sql = "SELECT 1 FROM project_users WHERE project_id = ? AND user_id = ?";
            $access_stmt = mysqli_prepare($conn, $access_sql);
            mysqli_stmt_bind_param($access_stmt, "ii", $task_data['project_id'], $user_id);
            mysqli_stmt_execute($access_stmt);
            $access_result = mysqli_stmt_get_result($access_stmt);
            
            if (mysqli_num_rows($access_result) === 0) {
                $_SESSION['message'] = "You don't have permission to update this task";
                $_SESSION['message_type'] = "error";
                header("Location: ../project-tasks.php");
                exit();
            }
        }

        // Update task status
        $update_sql = "UPDATE project_task SET 
                       status_id = ?, 
                       updated_by = ?, 
                       updated_at = NOW() 
                       WHERE id = ?";
        
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "iii", $status_id, $user_id, $task_id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            $_SESSION['message'] = "Task status updated to '{$status_data['status_name']}' successfully";
            $_SESSION['message_type'] = "success";
            
            // Log the activity
            $activity_sql = "INSERT INTO activity_logs (user_id, activity_type, description, ip_address, user_agent) 
                             VALUES (?, 'task_status_updated', ?, ?, ?)";
            $activity_stmt = mysqli_prepare($conn, $activity_sql);
            $description = "Updated task '{$task_data['task_name']}' status to {$status_data['status_name']} (Project: {$task_data['project_name']})";
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            mysqli_stmt_bind_param($activity_stmt, "isss", $user_id, $description, $ip_address, $user_agent);
            mysqli_stmt_execute($activity_stmt);
            
        } else {
            throw new Exception("Failed to update task status: " . mysqli_error($conn));
        }
        
        mysqli_stmt_close($update_stmt);
        
    } catch (Exception $e) {
        $_SESSION['message'] = "Error updating task status: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }

    // Redirect back to task details page
    header("Location: ../task-details.php?id=" . $task_id);
    exit();
    
} else {
    // If not POST request, redirect to tasks list
    $_SESSION['message'] = "Invalid request method";
    $_SESSION['message_type'] = "error";
    header("Location: ../project-tasks.php");
    exit();
}
?>