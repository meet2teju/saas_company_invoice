<?php
include '../../config/config.php';

function timeAgo($datetime) {
    if (empty($datetime)) {
        return 'Never';
    }
    
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) return "Online";
    if ($diff < 3600) return floor($diff / 60) . " mins ago";
    if ($diff < 86400) return floor($diff / 3600) . " hours ago";
    return date('d M Y', $timestamp);
}

// Get filter parameters from request
$status_filter = isset($_GET['status']) ? (array)$_GET['status'] : [];
$date_range = isset($_GET['date_range']) ? $_GET['date_range'] : '';

// Build the base query
$query = "SELECT login.*, user_role.name as role_name FROM login 
          LEFT JOIN user_role ON login.role_id = user_role.id 
          WHERE 1=1";

// Add status filter if provided
if (!empty($status_filter)) {
    $status_filter = array_map(function($status) use ($conn) {
        return mysqli_real_escape_string($conn, $status);
    }, $status_filter);
    
    $status_list = implode("','", $status_filter);
    $query .= " AND login.status IN ('$status_list')";
}

// Add date range filter if provided
if (!empty($date_range)) {
    $dates = explode(' - ', $date_range);
    $start_date = date('Y-m-d', strtotime(trim($dates[0])));
    $end_date = date('Y-m-d', strtotime(trim($dates[1])));
    $query .= " AND DATE(login.created_at) BETWEEN '$start_date' AND '$end_date'";
}

$query .= " ORDER BY login.id DESC";

// Set headers for Excel file download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=users_export_".date('Y-m-d').".xls");
header("Pragma: no-cache");
header("Expires: 0");

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
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Role</th>
        <th>Last Activity</th>
        <th>Created At</th>
        <th>Status</th>
      </tr>";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $status = ($row['status'] == '1') ? 'Active' : 'Inactive';
    $last_activity = timeAgo($row['last_activity']);
    $created_at = !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '';
    
    echo "<tr>
            <td>".$row['id']."</td>
            <td>".htmlspecialchars($row['name'])."</td>
            <td>".$row['email']."</td>
            <td>".$row['phone_number']."</td>
            <td>".$row['role_name']."</td>
            <td>".$last_activity."</td>
            <td>".$created_at."</td>
            <td>".$status."</td>
          </tr>";
}

echo "</table>";
exit;
?>