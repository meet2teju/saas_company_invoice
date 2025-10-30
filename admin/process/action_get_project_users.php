<?php
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'])) {
    $project_id = intval($_POST['project_id']);
    
    $query = "
        SELECT DISTINCT u.id, u.name, u.email, u.profile_img 
        FROM project_users pu 
        JOIN login u ON pu.user_id = u.id 
        WHERE pu.project_id = ? AND u.is_deleted = 0
        ORDER BY u.name ASC
    ";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $project_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Project ID is required'
    ]);
}
?>