<?php
include '../../config/config.php';

// Excel headers
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=clients_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Start Excel content
echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' 
            xmlns:x='urn:schemas-microsoft-com:office:excel' 
            xmlns='http://www.w3.org/TR/REC-html40'>
      <head>
      <!--[if gte mso 9]>
      <xml>
        <x:ExcelWorkbook>
          <x:ExcelWorksheets>
            <x:ExcelWorksheet>
              <x:Name>Client Data</x:Name>
              <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
            </x:ExcelWorksheet>
          </x:ExcelWorksheets>
        </x:ExcelWorkbook>
      </xml>
      <![endif]-->
      </head><body>";

// Table Header
echo "<table border='1'>
<tr>
  <th>#</th>
  <th>Client Name</th>
  <th>Company</th>
  <th>Email</th>
  <th>Phone</th>
  <th>Display Name</th>
  <th>Status</th>
  <th>Billing Address</th>
  <th>Shipping Address</th>
  <th>Bank Name</th>
  <th>Branch</th>
  <th>Account No</th>
  <th>IFSC</th>
  <th>Contact Persons</th>
  <th>Documents</th>
</tr>";

// Fetch clients
$query = "SELECT * FROM client WHERE is_deleted = 0";
$result = mysqli_query($conn, $query);
$count = 1;

while ($client = mysqli_fetch_assoc($result)) {
    $clientId = $client['id'];
    $fullName = $client['salutation'] . ' ' . $client['first_name'] . ' ' . $client['last_name'];

    // Address
    $addr = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM client_address WHERE client_id = $clientId AND is_deleted = 0"));
    $billing = $addr ? $addr['billing_name'] . ', ' . $addr['billing_address1'] . ', ' . $addr['billing_city'] . ', ' . $addr['billing_state'] . ', ' . $addr['billing_country'] . ' - ' . $addr['billing_pincode'] : '';
    $shipping = $addr ? $addr['shipping_name'] . ', ' . $addr['shipping_address1'] . ', ' . $addr['shipping_city'] . ', ' . $addr['shipping_state'] . ', ' . $addr['shipping_country'] . ' - ' . $addr['shipping_pincode'] : '';

    // Bank
    $bank = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM client_bank WHERE client_id = $clientId AND is_deleted = 0"));

    // Contact Persons
    $contacts = '';
    $contactQ = mysqli_query($conn, "SELECT * FROM client_contact_persons WHERE client_id = $clientId AND is_deleted = 0");
    while ($cp = mysqli_fetch_assoc($contactQ)) {
        $contacts .= $cp['contact_salutation'] . ' ' . $cp['contact_first_name'] . ' ' . $cp['contact_last_name'] . ' (' . $cp['contact_email'] . '), ';
    }

    // Documents
    $docs = '';
    $docQ = mysqli_query($conn, "SELECT * FROM client_document WHERE client_id = $clientId");
    while ($doc = mysqli_fetch_assoc($docQ)) {
        $docs .= $doc['document'] . ', ';
    }

    echo "<tr>
        <td>$count</td>
        <td>" . htmlspecialchars($fullName) . "</td>
        <td>" . htmlspecialchars($client['company_name']) . "</td>
        <td>" . htmlspecialchars($client['email']) . "</td>
        <td>" . htmlspecialchars($client['phone_number']) . "</td>
        <td>" . htmlspecialchars($client['display_name']) . "</td>
        <td>" . ($client['status'] == 1 ? 'Active' : 'Inactive') . "</td>
        <td>" . htmlspecialchars($billing) . "</td>
        <td>" . htmlspecialchars($shipping) . "</td>
        <td>" . htmlspecialchars($bank['bank_name'] ?? '') . "</td>
        <td>" . htmlspecialchars($bank['bank_branch'] ?? '') . "</td>
        <td>" . htmlspecialchars($bank['account_number'] ?? '') . "</td>
        <td>" . htmlspecialchars($bank['IFSC_code'] ?? '') . "</td>
        <td>" . htmlspecialchars(rtrim($contacts, ', ')) . "</td>
        <td>" . htmlspecialchars(rtrim($docs, ', ')) . "</td>
    </tr>";
    $count++;
}

echo "</table></body></html>";
exit;
