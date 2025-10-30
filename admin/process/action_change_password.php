<?php
session_start();
include '../../config/config.php';

header('Content-Type: application/json');

$login_user_id = $_SESSION['crm_user_id'];
$response = ['success' => false, 'errors' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['password'] ?? '';
    $newPassword = $_POST['newpassword'] ?? '';
    $renewPassword = $_POST['renewpassword'] ?? '';

    // Fetch current password from database
    $query = "SELECT password FROM login WHERE id = '$login_user_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $storedPassword = $row['password'];

    // Validate current password
    if (md5($currentPassword) !== $storedPassword) {
        $response['errors']['password'] = "Current password is incorrect.";
    }

    // Validate new password
    if (strlen($newPassword) < 6) {
        $response['errors']['newpassword'] = "Password must be at least 6 characters.";
    }

    // Validate confirm password
    if ($newPassword !== $renewPassword) {
        $response['errors']['renewpassword'] = "Confirm password does not match new password.";
    }

    // If no errors, update password
    if (empty($response['errors'])) {
        $newPasswordHashed = md5($newPassword);
        $updateQuery = "UPDATE login SET password = '$newPasswordHashed', password_updated_at = NOW() WHERE id = '$login_user_id'";
        $updateResult = mysqli_query($conn, $updateQuery);

        if ($updateResult) {
            $response['success'] = true;
            $response['message'] = "Password updated successfully.";
        } else {
            $response['message'] = "Something went wrong. Please try again.";
        }
    } else {
        // $response['message'] = "Please fix the errors below.";
    }
}

echo json_encode($response);
exit;
?>