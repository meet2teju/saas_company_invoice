<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_ids'])) {
    $projectIds = $_POST['project_ids'];

    foreach ($projectIds as $projectId) {
        $projectId = intval($projectId);

        // Delete related records
        mysqli_query($conn, "DELETE FROM project_users WHERE project_id = $projectId");
        mysqli_query($conn, "DELETE FROM project_task WHERE project_id = $projectId");

        // Delete the project
        mysqli_query($conn, "DELETE FROM project WHERE id = $projectId");
    }

    $_SESSION['message'] = "Selected projects and their related data deleted successfully!";
    $_SESSION['message_type'] = "success";
    header("Location: ../projects.php");
    exit();
} else {
    $_SESSION['message'] = "No projects selected for deletion.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../projects.php");
    exit();
}
?>
