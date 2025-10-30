
<?php
include '../../config/config.php';

if (isset($_POST['item_type'])) {
    $item_type = (int)$_POST['item_type'];

    $query = "SELECT 
                p.id, 
                p.name, 
                p.selling_price, 
                p.unit_id, 
                u.name AS unit_name, 
                p.tax_id, 
                t.rate,
                t.name AS tax_name  -- Add tax name to the query
              FROM product p
              LEFT JOIN units u ON u.id = p.unit_id
              LEFT JOIN tax t ON t.id = p.tax_id
              WHERE p.item_type = $item_type 
                AND p.status = 1 
                AND p.is_deleted = 0 
              ORDER BY p.name ASC";

    $result = mysqli_query($conn, $query);

    $options = '<option value="">Select ' . ($item_type == 1 ? 'Product' : 'Service') . '</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        $options .= sprintf(
            '<option value="%s" data-price="%s" data-unit="%s" data-unit-id="%s" data-tax="%s" data-tax-id="%s" data-tax-name="%s">%s</option>',
            $row['id'],
            $row['selling_price'],
            htmlspecialchars($row['unit_name']),
            $row['unit_id'],
            $row['rate'] ?? 0,
            $row['tax_id'],
            htmlspecialchars($row['tax_name'] ?? ''),  // Add tax name to data attribute
            htmlspecialchars($row['name'])
        );
    }
    echo $options;
}
?>
