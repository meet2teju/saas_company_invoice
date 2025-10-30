<?php
require '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_id = isset($_POST['invoice_id']) ? (int)$_POST['invoice_id'] : 0;
$status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : '';
    if ($invoice_id > 0 && $status !== '') {
        $update = mysqli_query($conn, "UPDATE invoice SET status = '$status' WHERE id = $invoice_id");

        if ($update) {
             $_SESSION['message'] = "Status updated successfully!";
            $_SESSION['message_type'] = "success";
            header("Location: ../invoice-details.php?id=$invoice_id");
        exit();
        } else {
            echo 'error: ' . mysqli_error($conn); // Show exact SQL error
        }
    } else {
        echo 'invalid data';
    }
}
?>
