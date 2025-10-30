<?php
session_start();
include '../../config/config.php'; // adjust path

if (isset($_POST['field']) && isset($_POST['value'])) {
    $field = $_POST['field'];
    $value = mysqli_real_escape_string($conn, $_POST['value']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0; // for edit form

    if (!in_array($field, ['account_number','ifsc_code','swift_code'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid field']);
        exit;
    }

    $sql = "SELECT * FROM bank WHERE $field = '$value'";
    if ($id > 0) {
        $sql .= " AND id != $id"; // exclude current record in edit
    }

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode([
            'status' => 'exists', 
            'message' => ucfirst(str_replace('_', ' ', $field)) . ' already exists!'
        ]);
    } else {
        echo json_encode(['status' => 'ok']);
    }
}
?>
