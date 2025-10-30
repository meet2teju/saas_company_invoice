<?php
require_once '../../tcpdf/tcpdf.php';
include '../../config/config.php';

// Fetch filters from GET
$product = $_GET['product'] ?? '';
$category = $_GET['category'] ?? '';
$unit = $_GET['unit'] ?? '';
$status = $_GET['status'] ?? '';

// Build dynamic SQL
$query = "SELECT 
            p.code, p.name, p.quantity, p.selling_price, p.purchase_price, 
            c.name AS category_name, u.name AS unit_name, p.status
          FROM product p
          LEFT JOIN category c ON p.category_id = c.id
          LEFT JOIN units u ON p.unit_id = u.id
          WHERE p.is_deleted = 0";

if (!empty($product)) {
    $query .= " AND p.name LIKE '%" . mysqli_real_escape_string($conn, $product) . "%'";
}
if (!empty($category)) {
    $query .= " AND p.category_id = '" . mysqli_real_escape_string($conn, $category) . "'";
}
if (!empty($unit)) {
    $query .= " AND p.unit_id = '" . mysqli_real_escape_string($conn, $unit) . "'";
}
if ($status !== '') {
    $query .= " AND p.status = '" . mysqli_real_escape_string($conn, $status) . "'";
}

$result = mysqli_query($conn, $query);

// Start PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your CRM');
$pdf->SetTitle('Product Export');
$pdf->SetHeaderData('', 0, 'Product Export', '', [0,64,255], [0,64,128]);
$pdf->setHeaderFont(['helvetica', '', 12]);
$pdf->setFooterFont(['helvetica', '', 10]);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(10, 20, 10);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();

// Build table
$html = '
<h3 style="text-align:center;">Product List</h3>
<table border="1" cellpadding="4">
    <thead>
        <tr>
            <th><strong>Code</strong></th>
            <th><strong>Name</strong></th>
            <th><strong>Category</strong></th>
            <th><strong>Unit</strong></th>
            <th><strong>Quantity</strong></th>
            <th><strong>Selling Price</strong></th>
            <th><strong>Purchase Price</strong></th>
            <th><strong>Status</strong></th>
        </tr>
    </thead>
    <tbody>';

while ($row = mysqli_fetch_assoc($result)) {
    $html .= '<tr>
        <td>' . htmlspecialchars($row['code']) . '</td>
        <td>' . htmlspecialchars($row['name']) . '</td>
        <td>' . htmlspecialchars($row['category_name']) . '</td>
        <td>' . htmlspecialchars($row['unit_name']) . '</td>
        <td>' . htmlspecialchars($row['quantity']) . '</td>
        <td>' . htmlspecialchars($row['selling_price']) . '</td>
        <td>' . htmlspecialchars($row['purchase_price']) . '</td>
        <td>' . ($row['status'] == 1 ? 'Active' : 'Inactive') . '</td>
    </tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('product_export.pdf', 'D');
exit;
?>
    