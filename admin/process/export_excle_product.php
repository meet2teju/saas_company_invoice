<?php
include '../../config/config.php';

// Set headers for Excel file download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=products_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Sanitize GET parameters
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$unit = isset($_GET['unit']) ? mysqli_real_escape_string($conn, $_GET['unit']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$product = isset($_GET['product']) ? mysqli_real_escape_string($conn, $_GET['product']) : '';

// Build dynamic SQL
$query = "SELECT 
            p.code, p.name, c.name AS category, u.name AS unit,
            p.quantity, p.selling_price, p.purchase_price, p.status
          FROM product p
          LEFT JOIN category c ON p.category_id = c.id
          LEFT JOIN units u ON p.unit_id = u.id
          WHERE p.is_deleted = 0";

if (!empty($category)) {
    $query .= " AND p.category_id = '$category'";
}

if (!empty($unit)) {
    $query .= " AND p.unit_id = '$unit'";
}

if ($status !== '') {
    $query .= " AND p.status = '$status'";
}

if (!empty($product)) {
    $query .= " AND p.name LIKE '%$product%'";
}

// Execute and fetch
$result = mysqli_query($conn, $query);

// Start Excel content
echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>
      <head>
      <!--[if gte mso 9]>
      <xml>
        <x:ExcelWorkbook>
          <x:ExcelWorksheets>
            <x:ExcelWorksheet>
              <x:Name>Users Data</x:Name>
              <x:WorksheetOptions>
                <x:DisplayGridlines/>
              </x:WorksheetOptions>
            </x:ExcelWorksheet>
          </x:ExcelWorksheets>
        </x:ExcelWorkbook>
      </xml>
      <![endif]-->
  
      </head>
      <body>";
      echo "<table>";
echo "<tr>
        <th>Code</th>
        <th>Name</th>
        <th>Category</th>
        <th>Unit</th>
        <th>Quantity</th>
        <th>Selling Price</th>
        <th>Purchase Price</th>
        <th>Status</th>
      </tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>{$row['code']}</td>
            <td>{$row['name']}</td>
            <td>{$row['category']}</td>
            <td>{$row['unit']}</td>
            <td>{$row['quantity']}</td>
            <td>{$row['selling_price']}</td>
            <td>{$row['purchase_price']}</td>
            <td>" . ($row['status'] == 1 ? 'Active' : 'Inactive') . "</td>
          </tr>";
}
echo "</table>";
exit;
