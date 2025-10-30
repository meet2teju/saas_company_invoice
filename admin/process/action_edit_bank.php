<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $bank_name = $_POST['bank_name'];
    $account_holder = $_POST['account_holder'];
    $account_number = $_POST['account_number'];
    $ifsc_code = $_POST['ifsc_code'];
    $swift_code = $_POST['swift_code'];
    $opening_balance = $_POST['opening_balance'];

    //     $check_sql = "SELECT * FROM bank 
    //               WHERE id != $id 
    //               AND (account_number='$account_number' OR ifsc_code='$ifsc_code' OR swift_code='$swift_code')";
    // $check_result = mysqli_query($conn, $check_sql);

    // if (mysqli_num_rows($check_result) > 0) {
    //     $_SESSION['message'] = "Account Number, IFSC code or SWIFT code already exists!";
    //     $_SESSION['message_type'] = "danger";
    //     header('Location: ../bank.php');
    //     exit();
    // }

    $sql = "UPDATE bank SET 
                bank_name = '$bank_name',
                account_holder = '$account_holder',
                account_number = '$account_number',
                ifsc_code = '$ifsc_code',
                swift_code = '$swift_code',
                opening_balance = '$opening_balance'
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Bank updated successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating bank.";
        $_SESSION['message_type'] = "danger";
    }
}
header('Location: ../bank.php');
exit();
