<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $task_id = intval($_POST['task_id']);
    $project_id = intval($_POST['project_id']);
    $task_name = mysqli_real_escape_string($conn, $_POST['task_name']);
    $task_description = mysqli_real_escape_string($conn, $_POST['task_description']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $hour = !empty($_POST['hour']) ? floatval($_POST['hour']) : NULL;
    $status_id = intval($_POST['status_id']);
    $user_id = intval($_POST['user_id']);
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Update task
        $update_query = "UPDATE project_task SET 
                        project_id = '$project_id', 
                        task_name = '$task_name', 
                        task_description = '$task_description', 
                        start_date = '$start_date', 
                        end_date = '$end_date', 
                        hour = " . ($hour !== NULL ? "'$hour'" : "NULL") . ", 
                        status_id = '$status_id', 
                        updated_by = '$user_id', 
                        updated_at = NOW() 
                        WHERE id = $task_id";
        
        if (mysqli_query($conn, $update_query)) {
            
            // Handle user assignments
            if (isset($_POST['assigned_users']) && !empty($_POST['assigned_users'])) {
                // Decode the JSON string from the hidden input
                $assigned_users = json_decode($_POST['assigned_users'], true);
                
                if (is_array($assigned_users) && !empty($assigned_users)) {
                    // First, delete all existing assignments for this task from project_users
                    $delete_assignments = "DELETE FROM project_users WHERE project_id = $task_id";
                    mysqli_query($conn, $delete_assignments);
                    
                    // Then insert new assignments into project_users table
                    foreach ($assigned_users as $assigned_user_id) {
                        $user_id_val = intval($assigned_user_id);
                        if ($user_id_val > 0) {
                            $assign_query = "INSERT INTO project_users (project_id, user_id) 
                                           VALUES ('$task_id', '$user_id_val')";
                            mysqli_query($conn, $assign_query);
                        }
                    }
                } else {
                    // If no users assigned, delete all assignments
                    $delete_assignments = "DELETE FROM project_users WHERE project_id = $task_id";
                    mysqli_query($conn, $delete_assignments);
                }
            } else {
                // If no users assigned, delete all assignments
                $delete_assignments = "DELETE FROM project_users WHERE project_id = $task_id";
                mysqli_query($conn, $delete_assignments);
            }
            
            // Handle new file uploads
            if (isset($_FILES['task_attachments']) && !empty($_FILES['task_attachments']['name'][0])) {
                $uploadDir = '../../uploads/task_images/';
                
                // Create upload directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $allowedTypes = [
                    'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'text/plain'
                ];
                $maxFileSize = 10 * 1024 * 1024; // 10MB
                
                foreach ($_FILES['task_attachments']['name'] as $key => $name) {
                    $tmp_name = $_FILES['task_attachments']['tmp_name'][$key];
                    $error = $_FILES['task_attachments']['error'][$key];
                    $size = $_FILES['task_attachments']['size'][$key];
                    $type = $_FILES['task_attachments']['type'][$key];
                    
                    // Skip if there's an error
                    if ($error !== UPLOAD_ERR_OK) {
                        error_log("File upload error for $name: $error");
                        continue;
                    }
                    
                    // Check file type
                    if (!in_array($type, $allowedTypes)) {
                        error_log("File type not allowed for $name: $type");
                        continue;
                    }
                    
                    // Check file size
                    if ($size > $maxFileSize) {
                        error_log("File too large for $name: $size");
                        continue;
                    }
                    
                    // Generate unique filename
                    $fileExtension = pathinfo($name, PATHINFO_EXTENSION);
                    $uniqueFilename = uniqid() . '_' . time() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $uniqueFilename;
                    
                    // Move uploaded file
                    if (move_uploaded_file($tmp_name, $uploadPath)) {
                        // Insert into project_task_doc table
                        $image_query = "INSERT INTO project_task_doc (task_id, image, created_at) 
                                      VALUES ('$task_id', '$uniqueFilename', NOW())";
                        
                        if (!mysqli_query($conn, $image_query)) {
                            error_log("Failed to insert image record: " . mysqli_error($conn));
                            // Continue with other files even if one fails
                        }
                    } else {
                        error_log("Failed to move uploaded file: $name");
                    }
                }
            }
            
            mysqli_commit($conn);
            $_SESSION['message'] = 'Task updated successfully';
            $_SESSION['message_type'] = 'success';
        } else {
            throw new Exception("Failed to update task: " . mysqli_error($conn));
        }
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['message'] = 'Error updating task: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
    }
    
    header('Location: ../project-tasks.php');
    exit();
}
?>