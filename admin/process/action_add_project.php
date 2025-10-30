<?php
session_start();
include '../../config/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sanitize and assign inputs
$project_name        = mysqli_real_escape_string($conn, $_POST['project_name'] ?? '');
$project_code        = mysqli_real_escape_string($conn, $_POST['project_code'] ?? '');
$billing_method      = intval($_POST['billing_method'] ?? 0);
$currency_type       = intval($_POST['currency_type'] ?? 1);
$total_project_cost  = isset($_POST['total_project_cost']) && is_numeric($_POST['total_project_cost']) ? $_POST['total_project_cost'] : 'NULL';
$rate_per_hour       = isset($_POST['rate_per_hour']) && is_numeric($_POST['rate_per_hour']) ? $_POST['rate_per_hour'] : 'NULL';
$description         = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

// Convert client_id[] array to comma-separated string
$client_ids = '';
if (!empty($_POST['client_id']) && is_array($_POST['client_id'])) {
    $clean_ids = array_map('intval', $_POST['client_id']);
    $client_ids = implode(',', $clean_ids);
}

// Insert project
$insert_project = "
    INSERT INTO project (
        project_name, project_code, billing_method, currency_type,
        total_project_cost, rate_per_hour, client_id, description
    ) VALUES (
        '$project_name', '$project_code', $billing_method, $currency_type,
        $total_project_cost, $rate_per_hour, '$client_ids', '$description'
    )
";

if (mysqli_query($conn, $insert_project)) {
    $project_id = mysqli_insert_id($conn);

    // Insert users (project_users)
    if (!empty($_POST['user_id']) && is_array($_POST['user_id'])) {
        foreach ($_POST['user_id'] as $index => $user_id) {
            $user_id = intval($user_id);
            $email = mysqli_real_escape_string($conn, $_POST['email'][$index] ?? '');
            if ($user_id && $email) {
                mysqli_query($conn, "
                    INSERT INTO project_users (project_id, user_id, email)
                    VALUES ('$project_id', '$user_id', '$email')
                ");
            }
        }
    }

    // Insert tasks (project_task)
// Insert tasks (project_task)
if (!empty($_POST['task_name']) && is_array($_POST['task_name'])) {
    foreach ($_POST['task_name'] as $index => $task_name) {
        $task_desc  = $_POST['task_description'][$index] ?? '';
        $start_date = $_POST['start_date'][$index] ?? '';
        $end_date   = $_POST['end_date'][$index] ?? '';
        $hour  = $_POST['hour'][$index] ?? '';
        $status   = $_POST['status_id'][$index] ?? '';

        $task_name  = mysqli_real_escape_string($conn, $task_name);
        $task_desc  = mysqli_real_escape_string($conn, $task_desc);
         $hour       = mysqli_real_escape_string($conn, $hour);
        $status     = mysqli_real_escape_string($conn, $status);
        $start_date = !empty($start_date) ? "'" . mysqli_real_escape_string($conn, $start_date) . "'" : 'NULL';
        $end_date   = !empty($end_date) ? "'" . mysqli_real_escape_string($conn, $end_date) . "'" : 'NULL';

        if (!empty($task_name) && !empty($task_desc)) {
            $query = "
                INSERT INTO project_task (project_id, task_name, task_description, start_date, end_date, hour, status_id)
                VALUES ('$project_id', '$task_name', '$task_desc', $start_date, $end_date, '$hour', '$status')
            ";
            mysqli_query($conn, $query) or die("Task insert failed: " . mysqli_error($conn));
        }
    }
}

    $_SESSION['message'] = 'Project added successfully';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Error adding Project: ' . mysqli_error($conn);
    $_SESSION['message_type'] = 'error';
}

header("Location: ../projects.php");
exit;
