<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $project_id = intval($_POST['project_id']);
    $task_name = mysqli_real_escape_string($conn, $_POST['task_name']);
    $task_description = mysqli_real_escape_string($conn, $_POST['task_description']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $hour = !empty($_POST['hour']) ? floatval($_POST['hour']) : NULL;
    $status_id = intval($_POST['status_id']);
    $user_id = intval($_POST['user_id']);
    
    // Debug: Check what's being sent
    error_log("assigned_users raw: " . print_r($_POST['assigned_users'], true));
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert task
        $task_query = "INSERT INTO project_task (
                        project_id, 
                        task_name, 
                        task_description, 
                        start_date, 
                        end_date, 
                        hour, 
                        status_id, 
                        created_by, 
                        updated_by
                    ) VALUES (
                        '$project_id', 
                        '$task_name', 
                        '$task_description', 
                        '$start_date', 
                        '$end_date', 
                        " . ($hour !== NULL ? "'$hour'" : "NULL") . ", 
                        '$status_id', 
                        '$user_id', 
                        '$user_id'
                    )";
        
        if (mysqli_query($conn, $task_query)) {
            $task_id = mysqli_insert_id($conn);
            
            // Handle user assignments
            if (isset($_POST['assigned_users']) && !empty($_POST['assigned_users'])) {
                $assigned_users = [];
                
                // Check if it's a JSON string (from edit form) or array (from add form)
                if (is_string($_POST['assigned_users'])) {
                    // It's a JSON string - decode it
                    $assigned_users = json_decode($_POST['assigned_users'], true);
                } else if (is_array($_POST['assigned_users'])) {
                    // It's already an array - use it directly
                    $assigned_users = $_POST['assigned_users'];
                }
                
                error_log("Processed assigned_users: " . print_r($assigned_users, true));
                
                if (is_array($assigned_users) && !empty($assigned_users)) {
                    foreach ($assigned_users as $assigned_user_id) {
                        $user_id_val = intval($assigned_user_id);
                        if ($user_id_val > 0) {
                            $assign_query = "INSERT INTO project_users (project_id, user_id) 
                                           VALUES ('$task_id', '$user_id_val')";
                            mysqli_query($conn, $assign_query);
                        }
                    }
                }
            }
            
            // Handle file uploads - ONLY IMAGES
            if (isset($_FILES['task_attachments']) && !empty($_FILES['task_attachments']['name'][0])) {
                $uploadDir = '../../uploads/task_images/';
                
                // Create upload directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
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
                    
                    // Check if it's an image
                    if (!in_array($type, $allowedImageTypes)) {
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
            $_SESSION['message'] = 'Task added successfully with assigned users and images';
            $_SESSION['message_type'] = 'success';
        } else {
            throw new Exception("Failed to add task: " . mysqli_error($conn));
        }
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['message'] = 'Error adding task: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
    }
    
    header('Location: ../project-tasks.php');
    exit();
}
?>