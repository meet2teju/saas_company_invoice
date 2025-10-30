<?php
session_start();
require '../../config/config.php';

// ---------------- Helper Functions ----------------
function dbString($conn, $value) {
    return mysqli_real_escape_string($conn, trim($value ?? ''));
}

function dbInt($value) {
    return (is_numeric($value) && $value !== '') ? (int)$value : 0;
}

function dbFloat($value) {
    return (is_numeric($value) && $value !== '') ? (float)$value : 0.0;
}

function dbDate($conn, $value) {
    if (!empty($value)) {
        $ts = strtotime($value);
        if ($ts) {
            return mysqli_real_escape_string($conn, date("Y-m-d", $ts));
        }
    }
    return null; // return null if invalid/empty
}

// ---------------- Main Logic ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $expense_id   = dbInt($_POST['id']);
    $category_id  = dbInt($_POST['ecategory_id'] ?? null);
    $title        = dbString($conn, $_POST['title'] ?? '');
    $client_id    = dbInt($_POST['client_id'] ?? null);
    // $invoice_id   = dbInt($_POST['invoice_id'] ?? null);
    $expense_date = dbDate($conn, $_POST['date'] ?? null);
    $amount       = dbFloat($_POST['amount'] ?? null);
    $description  = dbString($conn, $_POST['description'] ?? '');
    $currentUserId = $_SESSION['user_id'] ?? 0;

    // -------- 1. Update expense --------
 $invoice_id   = dbString($conn, $_POST['invoice_id'] ?? '');

$update = "
    UPDATE expenses SET
        ecategory_id = " . ($category_id ?: "NULL") . ",
        title        = '$title',
        client_id    = " . ($client_id ?: "NULL") . ",
        invoice_id   = " . ($invoice_id !== '' ? "'$invoice_id'" : "NULL") . ",
        date         = " . ($expense_date ? "'$expense_date'" : "NULL") . ",
        amount       = $amount,
        description  = '$description',
        updated_by   = $currentUserId,
        updated_at   = NOW()
    WHERE id = $expense_id
";

// print_R($update);
// exit();
    if (!mysqli_query($conn, $update)) {
        $_SESSION['message'] = "Error updating expense: " . mysqli_error($conn);
        $_SESSION['message_type'] = "danger";
        header("Location: ../edit-expense.php?id=$expense_id");
        exit();
    }

    // -------- 2. Handle new document uploads --------
    if (!empty($_FILES['document']['name'][0])) { // IMPORTANT: matches form name="document[]"
        foreach ($_FILES['document']['tmp_name'] as $key => $tmpName) {
            if (!empty($_FILES['document']['name'][$key])) {
                $fileName = time() . '_' . basename($_FILES['document']['name'][$key]);
                $targetPath = '../../uploads/' . $fileName;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    $insertDoc = "
                        INSERT INTO expense_document (expense_id, document, created_by, updated_by)
                        VALUES ('$expense_id', '$fileName', '$currentUserId', '$currentUserId')
                    ";
                    mysqli_query($conn, $insertDoc);
                }
            }
        }
    }

    // -------- 3. Success Message --------
    $_SESSION['message'] = "Expense updated successfully.";
    $_SESSION['message_type'] = "success";
    header("Location: ../expense.php");
    exit();

} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edit-expense.php");
    exit();
}
