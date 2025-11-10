<?php
include '../../config/config.php';
session_start();

if (isset($_POST['add_unit'])) {
    $unit_name = trim($_POST['name']);
    $short_name = trim($_POST['short_name']);
    $created_by = $_SESSION['crm_user_id'] ?? 0;
    $updated_by = $created_by;
    $org_id = $_SESSION['org_id'] ?? 0;
    $user_id = $_SESSION['crm_user_id'] ?? 0; // Add this line
    $is_deleted = 0;

    // **UPDATED: Added user_id to the INSERT query**
    $sql = "INSERT INTO units (name, short_name, created_at, updated_at, created_by, updated_by, is_deleted, org_id, user_id)
            VALUES ('$unit_name', '$short_name', NOW(), NOW(), $created_by, $updated_by, $is_deleted, $org_id, $user_id)";
    
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['message'] = 'Unit added successfully';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error adding Unit: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    header("Location: ../units.php");
    exit;
}
?>