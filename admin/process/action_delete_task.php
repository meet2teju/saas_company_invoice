

<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    
    $query = "DELETE FROM project_task WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Task deleted successfully';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error deleting task: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }
    
    header('Location: ../project-tasks.php');
    exit();
}
?>