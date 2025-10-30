<?php
session_start();
include '../../config/config.php';

// Enable debugging temporarily
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $project_id = (int)$_POST['project_id'];
    $project_name = mysqli_real_escape_string($conn, $_POST['project_name']);
    $project_code = is_numeric($_POST['project_code']) ? $_POST['project_code'] : null;
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $client_ids = implode(',', array_map('intval', $_POST['client_id']));
    $billing_method = (int)$_POST['billing_method'];
    $currency_type = mysqli_real_escape_string($conn, $_POST['currency_type'] ?? '');
    $total_project_cost = isset($_POST['total_project_cost']) ? (float)$_POST['total_project_cost'] : null;
    $rate_per_hour = isset($_POST['rate_per_hour']) ? (float)$_POST['rate_per_hour'] : null;

    // Update project table
    $sql = "UPDATE project SET 
                project_name = '$project_name',
                project_code = " . ($project_code !== null ? "'$project_code'" : "NULL") . ",
                client_id = '$client_ids',
                billing_method = $billing_method,
                currency_type = " . ($currency_type !== '' ? "'$currency_type'" : "NULL") . ",
                total_project_cost = " . ($total_project_cost !== null ? $total_project_cost : "NULL") . ",
                rate_per_hour = " . ($rate_per_hour !== null ? $rate_per_hour : "NULL") . ",
                description = '$description',
                updated_at = NOW()
            WHERE id = $project_id";

    if (!mysqli_query($conn, $sql)) {
        echo "<pre>Project Update Error: " . mysqli_error($conn) . "</pre>";
        exit;
    }

    // Update project_users table
   // Update project_users table
mysqli_query($conn, "DELETE FROM project_users WHERE project_id = $project_id");
if (!empty($_POST['user_id'])) {
    foreach ($_POST['user_id'] as $uid) {
        $uid = (int)$uid;

        // Fetch email from login table
        $email = '';
        $res = mysqli_query($conn, "SELECT email FROM login WHERE id = $uid");
        if ($res && mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            $email = mysqli_real_escape_string($conn, $row['email']);
        }

        if ($uid && $email) {
            mysqli_query($conn, "
                INSERT INTO project_users (project_id, user_id, email)
                VALUES ($project_id, $uid, '$email')
            ");
        }
    }
}


    // Update project_task table
    mysqli_query($conn, "DELETE FROM project_task WHERE project_id = $project_id");
    if (!empty($_POST['task_name'])) {
        foreach ($_POST['task_name'] as $index => $task_name) {
            $task_name = mysqli_real_escape_string($conn, $task_name);
            $task_desc = mysqli_real_escape_string($conn, $_POST['task_description'][$index] ?? '');
              $start_date = mysqli_real_escape_string($conn, $_POST['start_date'][$index] ?? '');
        $end_date   = mysqli_real_escape_string($conn, $_POST['end_date'][$index] ?? '');

                $start_date = !empty($start_date) ? "'$start_date'" : "NULL";
        $end_date   = !empty($end_date)   ? "'$end_date'"   : "NULL";
 $hour = mysqli_real_escape_string($conn, $_POST['hour'][$index] ?? '');
  $status_id = mysqli_real_escape_string($conn, $_POST['status_id'][$index] ?? '');
           $task_sql = "
            INSERT INTO project_task (project_id, task_name, task_description, start_date, end_date, hour, status_id) 
            VALUES ($project_id, '$task_name', '$task_desc', $start_date, $end_date, '$hour', '$status_id')
        ";
            if (!mysqli_query($conn, $task_sql)) {
                echo "<pre>Task Insert Error: " . mysqli_error($conn) . "</pre>";
                exit;
            }
        }
    }


    // On success
    $_SESSION['message'] = 'Project updated successfully';
    $_SESSION['message_type'] = 'success';
    header("Location: ../projects.php");
    exit();
}
?>
