<?php
include '../../config/config.php';

if (isset($_POST['project_id'])) {
    $projectId = (int)$_POST['project_id'];
    
    // Modified query to handle status directly without task_status table
    $query = "SELECT pt.* 
              FROM project_task pt 
              WHERE pt.project_id = ? AND pt.is_deleted = 0 AND pt.status_id = 3";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $projectId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    echo '<option value="">Select Task</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        // Since status_id = 3 means completed, we can display it as "Completed"
        echo '<option value="' . $row['id'] . '">' . 
             htmlspecialchars($row['task_name']) . ' (Completed - ' . $row['hour'] . ' hours)' . 
             '</option>';
    }
    
    // If no tasks found, show appropriate message
    if (mysqli_num_rows($result) === 0) {
        echo '<option value="">No completed tasks found</option>';
    }
}
?>