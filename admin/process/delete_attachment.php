<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attachment_id = intval($_POST['attachment_id']);
    $task_id = intval($_POST['task_id']);
    
    // Get the filename before deleting
    $get_file_query = "SELECT image FROM project_task_doc WHERE id = $attachment_id AND task_id = $task_id";
    $file_result = mysqli_query($conn, $get_file_query);
    $file_data = mysqli_fetch_assoc($file_result);
    
    if ($file_data) {
        $filename = $file_data['image'];
        $file_path = '../../uploads/task_images/' . $filename;
        
        // Delete from database
        $delete_query = "DELETE FROM project_task_doc WHERE id = $attachment_id";
        if (mysqli_query($conn, $delete_query)) {
            // Delete physical file
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'File not found']);
    }
    exit;
}
?>