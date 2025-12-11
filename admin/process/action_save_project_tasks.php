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
        // Skip if task name is empty
        if (empty(trim($name))) {
            continue;
        }

        $name = mysqli_real_escape_string($conn, trim($name));
        $desc = mysqli_real_escape_string($conn, trim($task_descriptions[$index] ?? ''));
        
        // Handle hour field - convert empty to NULL or valid decimal
        $hour = trim($hours[$index] ?? '');
        if ($hour === '' || empty($hour)) {
            $hour_sql = "NULL";
        } else {
            $hour = number_format((float)$hour, 2, '.', '');
            $hour_sql = "'$hour'";
        }
        
        // Handle status_id field - convert empty to NULL
        $status_id = trim($status_ids[$index] ?? '');
        if ($status_id === '' || empty($status_id)) {
            $status_id_sql = "NULL";
        } else {
            $status_id = mysqli_real_escape_string($conn, $status_id);
            $status_id_sql = "'$status_id'";
        }
        
        // Handle date fields - convert empty to NULL
        $start_date = trim($start_dates[$index] ?? '');
        $start_date_sql = (!empty($start_date)) ? "'" . mysqli_real_escape_string($conn, $start_date) . "'" : "NULL";
        
        $end_date = trim($end_dates[$index] ?? '');
        $end_date_sql = (!empty($end_date)) ? "'" . mysqli_real_escape_string($conn, $end_date) . "'" : "NULL";
        
        $task_id = $task_ids[$index] ?? null;

        if (!empty($task_id) && $task_id > 0) {
            // Update existing task
            $sql = "UPDATE project_task 
                    SET task_name = '$name', 
                        task_description = '$desc', 
                        start_date = $start_date_sql, 
                        end_date = $end_date_sql, 
                        hour = $hour_sql, 
                        status_id = $status_id_sql,
                        updated_at = NOW()
                    WHERE id = $task_id";
        } else {
            // Insert new task - FIXED: changed '$state_id' to $status_id_sql
            $sql = "INSERT INTO project_task (project_id, task_name, task_description, start_date, end_date, hour, status_id, created_at) 
                    VALUES ($project_id, '$name', '$desc', $start_date_sql, $end_date_sql, $hour_sql, $status_id_sql, NOW())";
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