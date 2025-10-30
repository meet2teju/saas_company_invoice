<?php
session_start();
include '../../config/config.php';

// Set header to return JSON
header('Content-Type: application/json');

// Debug session
error_log("Session User ID: " . ($_SESSION['crm_user_id'] ?? 'Not set'));
error_log("Session Role ID: " . ($_SESSION['role_id'] ?? 'Not set'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = isset($_SESSION['crm_user_id']) ? intval($_SESSION['crm_user_id']) : 0;
    $user_role_id = isset($_SESSION['role_id']) ? intval($_SESSION['role_id']) : 0;
    
    // Initialize response array
    $response = ['success' => false, 'message' => 'Unknown action'];
    
    try {
        if ($action === 'add_comment') {
            // Add new comment
            $task_id = intval($_POST['task_id']);
            $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);
            
            // Validate required fields
            if (empty($task_id) || (empty($comment_text) && empty($_FILES['comment_files']))) {
                throw new Exception("Task ID and either comment text or files are required");
            }
            
            // Insert comment
            $comment_query = "INSERT INTO project_task_comments (task_id, user_id, comment_text) 
                            VALUES ('$task_id', '$user_id', '$comment_text')";
            
            if (mysqli_query($conn, $comment_query)) {
                $comment_id = mysqli_insert_id($conn);
                
                // Handle file uploads
                if (isset($_FILES['comment_files']) && !empty($_FILES['comment_files']['name'][0])) {
                    $uploadDir = '../../uploads/comment_files/';
                    
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
                    
                    foreach ($_FILES['comment_files']['name'] as $key => $name) {
                        $tmp_name = $_FILES['comment_files']['tmp_name'][$key];
                        $error = $_FILES['comment_files']['error'][$key];
                        $size = $_FILES['comment_files']['size'][$key];
                        $type = $_FILES['comment_files']['type'][$key];
                        
                        if ($error === UPLOAD_ERR_OK && 
                            in_array($type, $allowedTypes) && 
                            $size <= $maxFileSize) {
                            
                            // Generate unique filename
                            $fileExtension = pathinfo($name, PATHINFO_EXTENSION);
                            $uniqueFilename = uniqid() . '_' . time() . '.' . $fileExtension;
                            $uploadPath = $uploadDir . $uniqueFilename;
                            
                            if (move_uploaded_file($tmp_name, $uploadPath)) {
                                // Insert file record
                                $file_query = "INSERT INTO project_task_comment_files 
                                             (comment_id, file_name, file_path, file_type, file_size) 
                                             VALUES ('$comment_id', '$name', '$uniqueFilename', '$type', '$size')";
                                mysqli_query($conn, $file_query);
                            }
                        }
                    }
                }
                
                $response = ['success' => true, 'message' => 'Comment added successfully', 'comment_id' => $comment_id];
            } else {
                throw new Exception("Failed to add comment: " . mysqli_error($conn));
            }
            
        } elseif ($action === 'update_comment') {
            // Update existing comment
            $comment_id = intval($_POST['comment_id']);
            $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);
            
            // First, check if the comment exists
            $check_query = "SELECT user_id FROM project_task_comments WHERE id = '$comment_id' AND is_deleted = 0";
            $check_result = mysqli_query($conn, $check_query);
            
            if (mysqli_num_rows($check_result) === 0) {
                throw new Exception("Comment not found");
            }
            
            $comment_data = mysqli_fetch_assoc($check_result);
            
            // Check permission: Admin can edit any comment, users can only edit their own
            if ($user_role_id != 1 && $comment_data['user_id'] != $user_id) {
                throw new Exception("You don't have permission to edit this comment");
            }
            
            // Build update query based on permissions
            if ($user_role_id == 1) {
                // Admin can update any comment
                $update_query = "UPDATE project_task_comments 
                               SET comment_text = '$comment_text', updated_at = NOW() 
                               WHERE id = '$comment_id'";
            } else {
                // User can only update their own comments
                $update_query = "UPDATE project_task_comments 
                               SET comment_text = '$comment_text', updated_at = NOW() 
                               WHERE id = '$comment_id' AND user_id = '$user_id'";
            }
            
            if (mysqli_query($conn, $update_query)) {
                // Handle new file uploads for the updated comment
                if (isset($_FILES['comment_files']) && !empty($_FILES['comment_files']['name'][0])) {
                    $uploadDir = '../../uploads/comment_files/';
                    
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
                    
                    $maxFileSize = 10 * 1024 * 1024;
                    
                    foreach ($_FILES['comment_files']['name'] as $key => $name) {
                        $tmp_name = $_FILES['comment_files']['tmp_name'][$key];
                        $error = $_FILES['comment_files']['error'][$key];
                        $size = $_FILES['comment_files']['size'][$key];
                        $type = $_FILES['comment_files']['type'][$key];
                        
                        if ($error === UPLOAD_ERR_OK && 
                            in_array($type, $allowedTypes) && 
                            $size <= $maxFileSize) {
                            
                            $fileExtension = pathinfo($name, PATHINFO_EXTENSION);
                            $uniqueFilename = uniqid() . '_' . time() . '.' . $fileExtension;
                            $uploadPath = $uploadDir . $uniqueFilename;
                            
                            if (move_uploaded_file($tmp_name, $uploadPath)) {
                                $file_query = "INSERT INTO project_task_comment_files 
                                             (comment_id, file_name, file_path, file_type, file_size) 
                                             VALUES ('$comment_id', '$name', '$uniqueFilename', '$type', '$size')";
                                mysqli_query($conn, $file_query);
                            }
                        }
                    }
                }
                
                $response = ['success' => true, 'message' => 'Comment updated successfully'];
            } else {
                throw new Exception("Failed to update comment: " . mysqli_error($conn));
            }
            
        } elseif ($action === 'delete_comment') {
            // Delete comment (soft delete)
            // ALLOW: Admin to delete any comment, Users to delete only their own comments
            $comment_id = intval($_POST['comment_id']);
            
            // First, check if the comment exists
            $check_query = "SELECT user_id FROM project_task_comments WHERE id = '$comment_id' AND is_deleted = 0";
            $check_result = mysqli_query($conn, $check_query);
            
            if (mysqli_num_rows($check_result) === 0) {
                throw new Exception("Comment not found");
            }
            
            $comment_data = mysqli_fetch_assoc($check_result);
            
            // Check permission: Admin can delete any comment, users can only delete their own
            if ($user_role_id != 1 && $comment_data['user_id'] != $user_id) {
                throw new Exception("You don't have permission to delete this comment");
            }
            
            // Build delete query based on permissions
            if ($user_role_id == 1) {
                // Admin can delete any comment
                $delete_query = "UPDATE project_task_comments 
                               SET is_deleted = 1 
                               WHERE id = '$comment_id'";
            } else {
                // User can only delete their own comments
                $delete_query = "UPDATE project_task_comments 
                               SET is_deleted = 1 
                               WHERE id = '$comment_id' AND user_id = '$user_id'";
            }
            
            if (mysqli_query($conn, $delete_query)) {
                $response = ['success' => true, 'message' => 'Comment deleted successfully'];
            } else {
                throw new Exception("Failed to delete comment: " . mysqli_error($conn));
            }
        } else {
            $response = ['success' => false, 'message' => 'Invalid action'];
        }
        
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => $e->getMessage()];
    }
    
    // Return JSON response
    echo json_encode($response);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}
?>