<?php
include '../../config/config.php';
session_start(); // Required for session messaging

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['expense_ids']) && is_array($_POST['expense_ids'])) {
        $expense_ids = array_map('intval', $_POST['expense_ids']);
        $ids_string = implode(',', $expense_ids);

        // Delete from related tables first
        $deleteDocuments = mysqli_query($conn, "DELETE FROM expense_document WHERE expense_id IN ($ids_string)");
        $deleteMain      = mysqli_query($conn, "DELETE FROM expenses WHERE id IN ($ids_string)");

        if ($deleteMain) {
            $_SESSION['message'] = "Selected expenses and their related data deleted successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting expenses: " . mysqli_error($conn);
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "No expenses selected.";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: ../expense.php");
    exit;
}
?>
