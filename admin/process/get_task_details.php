<?php
include '../../config/config.php';

header('Content-Type: application/json');

if (isset($_POST['task_id'])) {
    $taskId = (int)$_POST['task_id'];
    
    $query = "SELECT pt.*, p.rate_per_hour, (pt.hour * p.rate_per_hour) as total_amount 
              FROM project_task pt 
              JOIN project p ON pt.project_id = p.id 
              WHERE pt.id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $taskId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'success' => true,
            'task_name' => $row['task_name'],
            'hours' => $row['hour'],
            'rate_per_hour' => $row['rate_per_hour'],
            'total_amount' => $row['total_amount']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Task not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No task ID provided']);
}
?>