<?php
include '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $expenseId = intval($_POST['id']);

    mysqli_query($conn, "DELETE FROM expense_document WHERE expense_id = $expenseId");



    $deleteexpense = mysqli_query($conn, "DELETE FROM expenses WHERE id = $expenseId");

    if ($deleteexpense) {
        $_SESSION['message'] = "expense and related data deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to delete expense.";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: ../expense.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../expense.php");
    exit();
}

?>
