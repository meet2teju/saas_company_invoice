<?php
include '../../config/config.php';

if (isset($_POST['tax_ids'])) {
    $taxIds = $_POST['tax_ids'];
    $taxNames = [];
    
    if (!empty($taxIds)) {
        // Convert to comma-separated string for SQL query
        $idsString = implode(',', array_map('intval', $taxIds));
        
        $query = "SELECT id, name FROM tax WHERE id IN ($idsString)";
        $result = mysqli_query($conn, $query);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $taxNames[$row['id']] = $row['name'];
        }
    }
    
    echo json_encode($taxNames);
    exit;
}

echo json_encode([]);
?>