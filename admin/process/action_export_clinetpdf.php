<?php
require_once '../../tcpdf/tcpdf.php';
include '../../config/config.php';

// Init PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('CRM System');
$pdf->SetTitle('Full Client Export');
$pdf->SetHeaderData('', 0, 'Full Client Export', '', [0,64,255], [0,64,128]);
$pdf->setHeaderFont(['helvetica', '', 12]);
$pdf->setFooterFont(['helvetica', '', 10]);
$pdf->SetMargins(10, 20, 10);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();

$html = '<h2 style="text-align:center;">Client Full Report</h2>';

$query = "SELECT * FROM client WHERE is_deleted = 0";
$result = mysqli_query($conn, $query);
$counter = 1;

while ($client = mysqli_fetch_assoc($result)) {
    $clientId = $client['id'];
    $fullName = $client['salutation'] . ' ' . $client['first_name'] . ' ' . $client['last_name'];

    $html .= '<hr><h3>Client #' . $counter++ . ': ' . htmlspecialchars($fullName) . '</h3>';
    $html .= '<strong>Company:</strong> ' . htmlspecialchars($client['company_name']) . '<br>';
    $html .= '<strong>Display Name:</strong> ' . htmlspecialchars($client['display_name']) . '<br>';
    $html .= '<strong>Email:</strong> ' . htmlspecialchars($client['email']) . '<br>';
    $html .= '<strong>Phone:</strong> ' . htmlspecialchars($client['phone_number']) . '<br>';
    $html .= '<strong>Business Number:</strong> ' . htmlspecialchars($client['business_number']) . '<br>';
    $html .= '<strong>Website:</strong> ' . htmlspecialchars($client['website_url']) . '<br>';
    $html .= '<strong>PAN:</strong> ' . htmlspecialchars($client['pan_number']) . '<br>';
    $html .= '<strong>Status:</strong> ' . ($client['status'] ? 'Active' : 'Inactive') . '<br>';

    // 1. Address Info
    $addrQ = mysqli_query($conn, "SELECT * FROM client_address WHERE client_id = $clientId AND is_deleted = 0");
    if ($addr = mysqli_fetch_assoc($addrQ)) {
        $html .= '<h4>Address</h4>';
        $html .= '<strong>Billing:</strong> ' . htmlspecialchars($addr['billing_name']) . ', ' . 
                 htmlspecialchars($addr['billing_address1']) . ', ' . htmlspecialchars($addr['billing_city']) . ', ' .
                 htmlspecialchars($addr['billing_state']) . ', ' . htmlspecialchars($addr['billing_country']) . ' - ' .
                 htmlspecialchars($addr['billing_pincode']) . '<br>';
        $html .= '<strong>Shipping:</strong> ' . htmlspecialchars($addr['shipping_name']) . ', ' .
                 htmlspecialchars($addr['shipping_address1']) . ', ' . htmlspecialchars($addr['shipping_city']) . ', ' .
                 htmlspecialchars($addr['shipping_state']) . ', ' . htmlspecialchars($addr['shipping_country']) . ' - ' .
                 htmlspecialchars($addr['shipping_pincode']) . '<br>';
    }

    // 2. Bank Info
    $bankQ = mysqli_query($conn, "SELECT * FROM client_bank WHERE client_id = $clientId AND is_deleted = 0");
    if ($bank = mysqli_fetch_assoc($bankQ)) {
        $html .= '<h4>Bank Details</h4>';
        $html .= '<strong>Bank:</strong> ' . htmlspecialchars($bank['bank_name']) . '<br>';
        $html .= '<strong>Branch:</strong> ' . htmlspecialchars($bank['bank_branch']) . '<br>';
        $html .= '<strong>Account Holder:</strong> ' . htmlspecialchars($bank['account_holder']) . '<br>';
        $html .= '<strong>Account Number:</strong> ' . htmlspecialchars($bank['account_number']) . '<br>';
        $html .= '<strong>IFSC:</strong> ' . htmlspecialchars($bank['IFSC_code']) . '<br>';
    }

    // 3. Contact Persons
    $contactQ = mysqli_query($conn, "SELECT * FROM client_contact_persons WHERE client_id = $clientId AND is_deleted = 0");
    if (mysqli_num_rows($contactQ) > 0) {
        $html .= '<h4>Contact Persons</h4><ul>';
        while ($cp = mysqli_fetch_assoc($contactQ)) {
            $html .= '<li>' . htmlspecialchars($cp['contact_salutation'] . ' ' . $cp['contact_first_name'] . ' ' . $cp['contact_last_name']) .
                     ' - ' . htmlspecialchars($cp['contact_email']) . ', ' .
                     htmlspecialchars($cp['contact_mobile']) . '</li>';
        }
        $html .= '</ul>';
    }

    // 4. Documents
    $docQ = mysqli_query($conn, "SELECT * FROM client_document WHERE client_id = $clientId");
    if (mysqli_num_rows($docQ) > 0) {
        $html .= '<h4>Documents</h4><ul>';
        while ($doc = mysqli_fetch_assoc($docQ)) {
            $html .= '<li>' . htmlspecialchars($doc['document']) . '</li>';
        }
        $html .= '</ul>';
    }
}

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('client_full_export.pdf', 'D');
exit;
?>
