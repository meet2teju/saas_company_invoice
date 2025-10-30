<?php
include '../../config/config.php'; // adjust path

header('Content-Type: application/json');

$response = ['exists' => false];

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

    $query = "SELECT id FROM login WHERE email = '$email'";
    if ($userId > 0) {
        $query .= " AND id != $userId";
    }
    $query .= " LIMIT 1";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $response['exists'] = true;
    }
}

echo json_encode($response);
exit;
