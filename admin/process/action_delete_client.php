<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $clientId = intval($_POST['id']);

    mysqli_query($conn, "DELETE FROM client_document WHERE client_id = $clientId");
    mysqli_query($conn, "DELETE FROM client_contact_persons WHERE client_id = $clientId");
    mysqli_query($conn, "DELETE FROM client_bank WHERE client_id = $clientId");
    mysqli_query($conn, "DELETE FROM client_address WHERE client_id = $clientId");

    $deleteClient = mysqli_query($conn, "DELETE FROM client WHERE id = $clientId");

    if ($deleteClient) {
        $_SESSION['message'] = "Client and related data deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to delete client.";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: ../customers.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../customers.php");
    exit();
}

?>
