<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include config first to get database connection
include '../config/config.php';

// Include currency helper (with function_exists check to prevent redeclaration)
require_once '../config/currency_helper.php';

// Initialize company currency in session if not set
if (!isset($_SESSION['company_currency']) && isset($conn)) {
    getCompanyCurrency($conn);
}

header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$currentPage = basename($_SERVER['PHP_SELF']);
$publicPages = ['register.php','login.php', 'forgot-password.php', 'reset-password.php'];

if (!in_array($currentPage, $publicPages)) {
    if (!isset($_SESSION['crm_is_login']) || $_SESSION['crm_is_login'] !== 1) {
        header("Location: login.php");
        exit;
    }

    $login_user_id = $_SESSION['crm_user_id'];
    $current_time = date("Y-m-d H:i:s");

    mysqli_query($conn, "UPDATE login SET last_activity = '$current_time' WHERE id = '$login_user_id'");
}
?>