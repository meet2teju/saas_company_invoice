<?php
session_start();
require '../../config/config.php'; // DB connection

// =================== File Upload Function ===================
function uploadFile($file, $uploadDir) {
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $fileName;
    }
    return null;
}

// =================== Main Insert ===================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // INT / DECIMAL → 0 if empty
    $category_id  = isset($_POST['ecategory_id']) && $_POST['ecategory_id'] !== '' ? (int)$_POST['ecategory_id'] : 0;
    $client_id    = isset($_POST['client_id']) && $_POST['client_id'] !== '' ? (int)$_POST['client_id'] : 0;
    $amount       = isset($_POST['amount']) && $_POST['amount'] !== '' ? (float)$_POST['amount'] : 0;

    $created_by   = isset($_SESSION['crm_user_id']) && $_SESSION['crm_user_id'] !== '' ? (int)$_SESSION['crm_user_id'] : 0;
    $org_id       = isset($_SESSION['org_id']) && $_SESSION['org_id'] !== '' ? (int)$_SESSION['org_id'] : 0;
    $currentUserId = $created_by;

    // VARCHAR / TEXT / DATE → '' if empty
    $title        = isset($_POST['title']) ? mysqli_real_escape_string($conn, $_POST['title']) : '';
    $invoice_id   = isset($_POST['invoice_id']) ? mysqli_real_escape_string($conn, $_POST['invoice_id']) : '';
    $expense_date = isset($_POST['date']) ? mysqli_real_escape_string($conn, $_POST['date']) : '';
    $description  = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : '';

    // Insert expense - ONLY ADDED user_id, status, is_deleted, updated_by fields
    $insert = "
        INSERT INTO expenses 
        (client_id, date, ecategory_id, title, amount, invoice_id, description, 
         user_id, org_id, status, is_deleted, created_by, updated_by) 
        VALUES 
        ($client_id, '$expense_date', $category_id, '$title', $amount, '$invoice_id', '$description', 
         $currentUserId, $org_id, 1, 0, $created_by, $created_by)
    ";

    if (mysqli_query($conn, $insert)) {
        $expense_id = mysqli_insert_id($conn);

        // Handle file uploads only if files exist
        if (!empty($_FILES['document']) && !empty($_FILES['document']['name'][0])) {
            foreach ($_FILES['document']['tmp_name'] as $key => $tmpName) {
                if (!empty($_FILES['document']['name'][$key])) {
                    $document = [
                        'name' => $_FILES['document']['name'][$key],
                        'type' => $_FILES['document']['type'][$key],
                        'tmp_name' => $tmpName,
                        'error' => $_FILES['document']['error'][$key],
                        'size' => $_FILES['document']['size'][$key]
                    ];

                    $docFileName = uploadFile($document, '../../uploads/');
                    if ($docFileName) {
                        $docQuery = "
                            INSERT INTO expense_document 
                            (expense_id, document, org_id, created_by, updated_by)
                            VALUES 
                            ($expense_id, '" . mysqli_real_escape_string($conn, $docFileName) . "', $org_id, $currentUserId, $currentUserId)
                        ";
                        if (!mysqli_query($conn, $docQuery)) {
                            die("Document insert failed: " . mysqli_error($conn));
                        }
                    }
                    
                }
            }
        }

        $_SESSION['message'] = "Expense added successfully.";
        $_SESSION['message_type'] = "success";
        header("Location: ../expense.php");
        exit();
    } else {
        die("Expense insert failed: " . mysqli_error($conn));
    }
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edit-expense.php");
    exit();
}
?>