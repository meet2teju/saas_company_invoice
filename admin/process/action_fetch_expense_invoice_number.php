<?php
include '../../config/config.php';

$response = ['success' => false];

if (isset($_POST['client_id']) && is_numeric($_POST['client_id'])) {
    $client_id = (int)$_POST['client_id'];

    // Get the latest invoice number for the selected client
    $query = "SELECT invoice_id FROM invoice WHERE client_id = $client_id ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $response['success'] = true;
        $response['invoice_id'] = $row['invoice_id'];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
