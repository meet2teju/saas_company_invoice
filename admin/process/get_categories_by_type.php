<?php
include '../../config/config.php';

$category_type = isset($_POST['category_type']) ? intval($_POST['category_type']) : 1;

$result = mysqli_query($conn, "SELECT * FROM category WHERE category_type = $category_type AND is_deleted = 0 AND status=1");

$options = '<option value="">Select Category</option>';
while ($row = mysqli_fetch_assoc($result)) {
    $options .= '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
}
echo $options;
