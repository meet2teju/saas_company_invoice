<?php
include '../../config/config.php';

// Set headers for Excel file download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=roles_export_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Start HTML table
echo "<html xmlns:o='urn:schemas-microsoft-com:office:office'
            xmlns:x='urn:schemas-microsoft-com:office:excel'
            xmlns='http://www.w3.org/TR/REC-html40'>
        <head>
        <!--[if gte mso 9]>
        <xml>
            <x:ExcelWorkbook>
                <x:ExcelWorksheets>
                    <x:ExcelWorksheet>
                        <x:Name>Roles Data</x:Name>
                        <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
                    </x:ExcelWorksheet>
                </x:ExcelWorksheets>
            </x:ExcelWorkbook>
        </xml>
        <![endif]-->
        </head>
        <body>";

echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Role Name</th>
            <th>Created At</th>
        </tr>";

$query = "SELECT id, name, created_at FROM user_role WHERE is_deleted = 0 ORDER BY id DESC";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . (!empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '') . "</td>
          </tr>";
}

echo "</table></body></html>";
exit;
?>
