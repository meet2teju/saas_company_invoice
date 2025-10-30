<?php
session_start();
include '../../config/config.php';

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

if (isset($_POST['submit'])) {
    $currentUserId = $_SESSION['user_id'] ?? 1;
    $orgId = $_SESSION['org_id'] ?? 1;

    mysqli_begin_transaction($conn);

    try {
        // Sanitize inputs
        $client_id     = (int)($_POST['client_id'] ?? 0);
        $invoice_id    = mysqli_real_escape_string($conn, $_POST['invoice_id'] ?? '');
        $reference_name= mysqli_real_escape_string($conn, $_POST['reference_name'] ?? '');
        $invoice_date  = mysqli_real_escape_string($conn, $_POST['invoice_date'] ?? '');
        $expiry_date   = mysqli_real_escape_string($conn, $_POST['due_date'] ?? '');
        $order_number  = (int)($_POST['order_number'] ?? 0);
        $item_type     = (int)($_POST['item_type'] ?? 0);
        $user_id       = (int)($_POST['user_id'] ?? 0);

        $bank_id_raw   = $_POST['bank_id'] ?? '';
        $bank_id_sql   = ($bank_id_raw !== '' && is_numeric($bank_id_raw)) ? (int)$bank_id_raw : 'NULL';

        $invoice_note  = mysqli_real_escape_string($conn, $_POST['invoice_note'] ?? '');
        $description   = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
        $amount        = (float)($_POST['sub_amount'] ?? 0);
        $tax_amount    = (float)($_POST['tax_amount'] ?? 0);
        $shipping_charge= (float)($_POST['shipping_charge'] ?? 0);
        $total_amount  = (float)($_POST['total_amount'] ?? 0);

        // Insert invoice
        $query = "INSERT INTO invoice (
            client_id, invoice_id, reference_name, invoice_date, due_date,
            order_number, item_type, user_id, bank_id,
            invoice_note, description, amount, tax_amount, shipping_charge, total_amount,
            org_id, is_deleted, created_by, updated_by
        ) VALUES (
            '$client_id', '$invoice_id', '$reference_name', '$invoice_date', '$expiry_date',
            '$order_number', '$item_type', '$user_id', $bank_id_sql,
            '$invoice_note', '$description', '$amount', '$tax_amount', '$shipping_charge', '$total_amount',
            '$orgId', 0, '$currentUserId', '$currentUserId'
        )";

        if (!mysqli_query($conn, $query)) {
            throw new Exception("Invoice insert failed: " . mysqli_error($conn));
        }

        $invoiceId = mysqli_insert_id($conn);

        // Multiple document uploads
        if (!empty($_FILES['document']['name'][0])) {
            foreach ($_FILES['document']['tmp_name'] as $key => $tmpName) {
                if (!empty($_FILES['document']['name'][$key])) {
                    $document = [
                        'name'     => $_FILES['document']['name'][$key],
                        'type'     => $_FILES['document']['type'][$key],
                        'tmp_name' => $tmpName,
                        'error'    => $_FILES['document']['error'][$key],
                        'size'     => $_FILES['document']['size'][$key]
                    ];

                    $docFileName = uploadFile($document, '../../uploads/');
                    if ($docFileName) {
                        $docQuery = "INSERT INTO invoice_document (invoice_id, document, created_by, updated_by)
                                     VALUES ('$invoiceId', '$docFileName', '$currentUserId', '$currentUserId')";
                        if (!mysqli_query($conn, $docQuery)) {
                            throw new Exception("Document insert failed: " . mysqli_error($conn));
                        }
                    }
                }
            }
        }

        // Insert invoice items
        foreach ($_POST['product_id'] as $index => $product_id) {
            $product_id   = $_POST['product_id'][$index] ?? '';
            $quantity     = (float)($_POST['quantity'][$index] ?? 0);
            $unit_id      = $_POST['unit_id'][$index] ?? '';
            $selling_price= (float)($_POST['selling_price'][$index] ?? 0);
            $tax_id       = $_POST['tax_id'][$index] ?? '';
            $item_amount  = (float)($_POST['amount'][$index] ?? 0);

            $product_id_sql = ($product_id === '' ? 'NULL' : (int)$product_id);
            $unit_id_sql    = ($unit_id === '' ? 0 : (int)$unit_id); // default 0 instead of NULL to avoid errors
            $tax_id_sql     = ($tax_id === '' ? 'NULL' : (int)$tax_id);

            if (!empty($product_id)) {
                $itemInsertQuery = "INSERT INTO invoice_item (
                    invoice_id, quantity, product_id, unit_id, selling_price,
                    tax_id, amount, org_id, is_deleted, created_by, updated_by
                ) VALUES (
                    '$invoiceId', '$quantity', $product_id_sql, $unit_id_sql, '$selling_price',
                    $tax_id_sql, '$item_amount', '$orgId', 0, '$currentUserId', '$currentUserId'
                )";

                if (!mysqli_query($conn, $itemInsertQuery)) {
                    throw new Exception("Item insert failed: " . mysqli_error($conn));
                }
            }
        }

        // Commit everything
        mysqli_commit($conn);
        $_SESSION['message'] = "Invoice added successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: ../invoices.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
}
?>
