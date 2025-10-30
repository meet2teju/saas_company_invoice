<?php
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = (int)$_POST['project_id'];
    $task_ids = $_POST['task_id'] ?? [];
    $task_names = $_POST['task_name'] ?? [];
    $task_descriptions = $_POST['task_description'] ?? [];
    $start_dates = $_POST['start_date'] ?? [];
    $end_dates = $_POST['end_date'] ?? [];
    $hours = $_POST['hour'] ?? [];
    $status_ids = $_POST['status_id'] ?? [];

    // --- Fetch existing task IDs from DB ---
    $existingTasksRes = mysqli_query($conn, "SELECT id FROM project_task WHERE project_id = $project_id");
    $existingTaskIds = [];
    while ($row = mysqli_fetch_assoc($existingTasksRes)) {
        $existingTaskIds[] = $row['id'];
    }

    // --- Determine tasks to delete ---
    $taskIdsToKeep = array_filter($task_ids, fn($id) => !empty($id));
    $taskIdsToDelete = array_diff($existingTaskIds, $taskIdsToKeep);

    if (!empty($taskIdsToDelete)) {
        $idsToDeleteStr = implode(',', array_map('intval', $taskIdsToDelete));
        mysqli_query($conn, "DELETE FROM project_task WHERE id IN ($idsToDeleteStr)");
    }

    // --- Insert/update tasks ---
    foreach ($task_names as $index => $name) {
        $name = mysqli_real_escape_string($conn, $name);
        $desc = mysqli_real_escape_string($conn, $task_descriptions[$index] ?? '');
        $hour = mysqli_real_escape_string($conn, $hours[$index] ?? '');
        $status_id = mysqli_real_escape_string($conn, $status_ids[$index] ?? '');
        $start_date = !empty($start_dates[$index]) ? "'" . mysqli_real_escape_string($conn, $start_dates[$index]) . "'" : 'NULL';
        $end_date = !empty($end_dates[$index]) ? "'" . mysqli_real_escape_string($conn, $end_dates[$index]) . "'" : 'NULL';
        $task_id = $task_ids[$index] ?? null;

        if (!empty($task_id)) {
            // Update existing task
            $sql = "UPDATE project_task 
                    SET task_name='$name', task_description='$desc', start_date=$start_date, end_date=$end_date, hour=$hour, status_id=$status_id
                    WHERE id=$task_id";
        } else {
            // Insert new task
            $sql = "INSERT INTO project_task (project_id, task_name, task_description, start_date, end_date, hour, status_id) 
                    VALUES ($project_id, '$name', '$desc', $start_date, $end_date, '$hour', '$state_id')";
        }

        if (!mysqli_query($conn, $sql)) {
            echo "<pre>Task Error: " . mysqli_error($conn) . "</pre>";
            exit;
        }
    }

    header("Location: ../projects.php?id=$project_id");
    exit;
}
?>
