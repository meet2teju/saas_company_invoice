<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bank_name = $_POST['bank_name'];
    $account_holder = $_POST['account_holder'];
    $account_number = $_POST['account_number'];
    $ifsc_code = $_POST['ifsc_code'];
    $swift_code = $_POST['swift_code'];
    $opening_balance = $_POST['opening_balance'];

    // $check_sql = "SELECT * FROM bank WHERE account_number='$account_number' OR ifsc_code='$ifsc_code' OR swift_code='$swift_code'";
    // $check_result = mysqli_query($conn, $check_sql);

    // if (mysqli_num_rows($check_result) > 0) {
    //     $_SESSION['message'] = 'Account Number, IFSC code or SWIFT code already exists!';
    //     $_SESSION['message_type'] = 'danger';
    //     header("Location: ../bank.php");
    //     exit;
    // }
    // Basic validation
   
        $sql = "INSERT INTO bank (bank_name, account_holder, account_number, ifsc_code, swift_code, opening_balance, status)
                VALUES ('$bank_name', '$account_holder', '$account_number', '$ifsc_code', '$swift_code', '$opening_balance', 1)";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = 'Bank added successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error: ' . mysqli_error($conn);
            $_SESSION['message_type'] = 'danger';
        }
    

    header("Location: ../bank.php");
    exit;
}
?>
