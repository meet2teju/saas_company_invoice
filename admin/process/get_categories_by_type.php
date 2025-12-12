<?php
include '../../config/config.php';

// Get POST data
$category_type = isset($_POST['category_type']) ? intval($_POST['category_type']) : 1;
$user_id = $_POST['user_id'] ?? 0;
$org_id = $_POST['org_id'] ?? 0;
$role_id = $_POST['role_id'] ?? 0;

// If org_id is 0, try to get from database
if ($org_id == 0 && $user_id > 0) {
    $userQuery = "SELECT org_id, role_id FROM login WHERE id = $user_id";
    $userResult = mysqli_query($conn, $userQuery);
    if ($userResult && mysqli_num_rows($userResult) > 0) {
        $userData = mysqli_fetch_assoc($userResult);
        $org_id = $userData['org_id'];
        $role_id = $userData['role_id'];
    }
}

// Build query
$query = "SELECT * FROM category WHERE category_type = $category_type AND is_deleted = 0 AND status = 1";

// Add organization filter
if ($org_id > 0) {
    $query .= " AND org_id = $org_id";
}

// For non-admin users: Can see their own categories AND categories created by admin users
if ($role_id != 1 && $org_id > 0) {
    $query .= " AND (
        created_by = $user_id 
        OR 
        created_by IN (
            SELECT id FROM login 
            WHERE org_id = $org_id AND role_id = 1
        )
    )";
}

$query .= " ORDER BY name ASC";

$result = mysqli_query($conn, $query);

$options = '<option value="">Select Category</option>';
while ($row = mysqli_fetch_assoc($result)) {
    $options .= '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
}
echo $options;
?>