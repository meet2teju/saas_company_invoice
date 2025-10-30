<?php
include '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
    
    if ($project_id > 0) {
        // Query to get users assigned to the selected project
        $query = "
            SELECT DISTINCT l.id, l.name, l.email 
            FROM login l
            INNER JOIN project_users pu ON l.id = pu.user_id
            WHERE pu.project_id = ? AND pu.is_deleted = 0 AND l.is_deleted = 0
            ORDER BY l.name ASC
        ";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $project_id);
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
            'message' => 'Invalid project ID'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>