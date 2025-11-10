<?php
session_start();
include '../../config/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get current user info from session
$currentUserId = $_SESSION['crm_user_id'] ?? 0;
$currentOrgId = $_SESSION['org_id'] ?? 0;

// Get the correct org_id from database if session org_id is 0
if ($currentOrgId == 0 && $currentUserId > 0) {
    $fixQuery = "SELECT org_id, role_id FROM login WHERE id = $currentUserId";
    $fixResult = mysqli_query($conn, $fixQuery);
    if ($fixResult && mysqli_num_rows($fixResult) > 0) {
        $userData = mysqli_fetch_assoc($fixResult);
        $_SESSION['org_id'] = $userData['org_id'];
        $_SESSION['role_id'] = $userData['role_id'];
        $currentOrgId = $userData['org_id'];
    }
}

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

// Start transaction for data consistency
mysqli_begin_transaction($conn);

try {
    // Insert project with user_id, org_id, and other reference fields
    $insert_project = "
        INSERT INTO project (
            project_name, project_code, billing_method, currency_type,
            total_project_cost, rate_per_hour, client_id, description,
            org_id, user_id, created_by, updated_by, status, is_deleted
        ) VALUES (
            '$project_name', '$project_code', $billing_method, $currency_type,
            $total_project_cost, $rate_per_hour, '$client_ids', '$description',
            '$currentOrgId', '$currentUserId', '$currentUserId', '$currentUserId', 1, 0
        )
    ";

    if (!mysqli_query($conn, $insert_project)) {
        throw new Exception("Project insert failed: " . mysqli_error($conn));
    }

    $project_id = mysqli_insert_id($conn);

    // Insert users (project_users)
    if (!empty($_POST['user_id']) && is_array($_POST['user_id'])) {
        foreach ($_POST['user_id'] as $index => $user_id) {
            $user_id = intval($user_id);
            $email = mysqli_real_escape_string($conn, $_POST['email'][$index] ?? '');
            if ($user_id && $email) {
                $insert_user = "
                    INSERT INTO project_users (project_id, user_id, email, org_id, created_by, updated_by)
                    VALUES ('$project_id', '$user_id', '$email', '$currentOrgId', '$currentUserId', '$currentUserId')
                ";
                if (!mysqli_query($conn, $insert_user)) {
                    throw new Exception("Project user insert failed: " . mysqli_error($conn));
                }
            }
        }
    }

    // Insert tasks (project_task)
    if (!empty($_POST['task_name']) && is_array($_POST['task_name'])) {
        foreach ($_POST['task_name'] as $index => $task_name) {
            $task_desc  = $_POST['task_description'][$index] ?? '';
            $start_date = $_POST['start_date'][$index] ?? '';
            $end_date   = $_POST['end_date'][$index] ?? '';
            $hour       = $_POST['hour'][$index] ?? '';
            $status     = $_POST['status_id'][$index] ?? '';

            $task_name  = mysqli_real_escape_string($conn, $task_name);
            $task_desc  = mysqli_real_escape_string($conn, $task_desc);
            $hour       = mysqli_real_escape_string($conn, $hour);
            $status     = mysqli_real_escape_string($conn, $status);
            $start_date = !empty($start_date) ? "'" . mysqli_real_escape_string($conn, $start_date) . "'" : 'NULL';
            $end_date   = !empty($end_date) ? "'" . mysqli_real_escape_string($conn, $end_date) . "'" : 'NULL';

            if (!empty($task_name) && !empty($task_desc)) {
                $query = "
                    INSERT INTO project_task (
                        project_id, task_name, task_description, start_date, end_date, hour, status_id,
                        org_id, created_by, updated_by
                    ) VALUES (
                        '$project_id', '$task_name', '$task_desc', $start_date, $end_date, '$hour', '$status',
                        '$currentOrgId', '$currentUserId', '$currentUserId'
                    )
                ";
                if (!mysqli_query($conn, $query)) {
                    throw new Exception("Task insert failed: " . mysqli_error($conn));
                }
            }
        }
    }

    // Commit transaction
    mysqli_commit($conn);
    
    $_SESSION['message'] = 'Project added successfully';
    $_SESSION['message_type'] = 'success';
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    
    $_SESSION['message'] = 'Error adding Project: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

header("Location: ../projects.php");
exit;
?>