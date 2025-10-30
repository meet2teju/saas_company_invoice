<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $projectId = intval($_POST['id']);

    // Delete related entries
    mysqli_query($conn, "DELETE FROM project_users WHERE project_id = $projectId");
    mysqli_query($conn, "DELETE FROM project_task WHERE project_id = $projectId");

    // Delete main project
    $deleteProject = mysqli_query($conn, "DELETE FROM project WHERE id = $projectId");

    if ($deleteProject) {
        $_SESSION['message'] = "Project and related data deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to delete project.";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: ../projects.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../projects.php");
    exit();
}
?>
