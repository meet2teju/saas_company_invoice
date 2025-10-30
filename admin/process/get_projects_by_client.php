<?php
include '../../config/config.php';

if (isset($_POST['client_id'])) {
    $clientId = (int)$_POST['client_id'];
    
    $query = "SELECT * FROM project WHERE client_id = ? AND is_deleted = 0";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $clientId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    echo '<option value="">Select Project</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['project_name']) . '</option>';
    }
}
?>