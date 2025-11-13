<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $bank_name = $_POST['bank_name'];
    // $account_holder = $_POST['account_holder'];
    // $account_number = $_POST['account_number'];
    // $ifsc_code = $_POST['ifsc_code'];
    // $swift_code = $_POST['swift_code'];
    // $opening_balance = $_POST['opening_balance'];

     $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
    $account_holder = mysqli_real_escape_string($conn, $_POST['account_holder']);
    $account_number = mysqli_real_escape_string($conn, $_POST['account_number']);
    $ifsc_code = mysqli_real_escape_string($conn, $_POST['ifsc_code']);
    $swift_code = mysqli_real_escape_string($conn, $_POST['swift_code']);
    $opening_balance = mysqli_real_escape_string($conn, $_POST['opening_balance']);

    // Basic validation - check if required fields are not empty
    if (!empty($bank_name) && !empty($account_holder) && !empty($account_number)) {
        $sql = "INSERT INTO bank (bank_name, account_holder, account_number, ifsc_code, swift_code, opening_balance, status)
                VALUES ('$bank_name', '$account_holder', '$account_number', '$ifsc_code', '$swift_code', '$opening_balance', 1)";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = 'Bank added successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error: ' . mysqli_error($conn);
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        $_SESSION['message'] = 'Please fill in all required fields.';
        $_SESSION['message_type'] = 'danger';
    }

    header("Location: ../bank.php");
    exit;
}
?>
