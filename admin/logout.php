
<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

if (isset($_SESSION['crm_user_id'])) {
    $user_id = $_SESSION['crm_user_id'];

    // ✅ Mark user as offline
    mysqli_query($conn, "UPDATE login SET last_activity = NULL WHERE id = '$user_id'");
}

session_unset();
session_destroy();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

header("Location: login.php");
exit();
?>