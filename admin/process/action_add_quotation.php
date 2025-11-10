<?php
include '../../config/config.php';
session_start();

// Function to remove currency symbols and commas
function unformat($value) {
    return (float)str_replace(['$', ',',' '], '', $value);
}

// File upload function
function uploadFile($file, $uploadDir) {
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    return move_uploaded_file($file['tmp_name'], $targetPath) ? $fileName : null;
}

if (isset($_POST['submit'])) {

    $currentUserId = $_SESSION['crm_user_id'] ?? 1;
    $orgId         = $_SESSION['org_id'] ?? 1;

    mysqli_begin_transaction($conn);

    try {
        // === Quotation master fields ===
        $client_id       = (int)($_POST['client_id'] ?? 0);
        $quotation_id    = mysqli_real_escape_string($conn, $_POST['quotation_id'] ?? '');
        $reference_name  = mysqli_real_escape_string($conn, $_POST['reference_name'] ?? '');
        $quotation_date  = mysqli_real_escape_string($conn, $_POST['quotation_date'] ?? '');
        $expiry_date     = mysqli_real_escape_string($conn, $_POST['expiry_date'] ?? '');
        $item_type       = (int)($_POST['item_type'] ?? 0);
        $salesperson_id  = (int)($_POST['user_id'] ?? 0); // Renamed to salesperson_id for clarity
        $project_id      = (int)($_POST['project_id'] ?? 0);
        $client_note     = mysqli_real_escape_string($conn, $_POST['client_note'] ?? '');
        $description     = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
        $amount          = unformat($_POST['sub_amount'] ?? 0);
        $tax_amount      = unformat($_POST['tax_amount'] ?? 0);
        $shipping_charge = unformat($_POST['shipping_charge'] ?? 0);
        $total_amount    = unformat($_POST['total_amount'] ?? 0);

        // === Insert quotation master ===
        // **FIXED: Store current logged-in user ID in user_id field, and salesperson ID in salesperson_id field**
        $query = "INSERT INTO quotation (
            client_id, quotation_id, reference_name, quotation_date, expiry_date, item_type, 
            salesperson_id, project_id, client_note, description, amount, tax_amount, 
            shipping_charge, total_amount, org_id, user_id, is_deleted, created_by, updated_by
        ) VALUES (
            '$client_id', '$quotation_id', '$reference_name', '$quotation_date', '$expiry_date', '$item_type', 
            '$salesperson_id', '$project_id', '$client_note', '$description', '$amount', '$tax_amount', 
            '$shipping_charge', '$total_amount', '$orgId', '$currentUserId', 0, '$currentUserId', '$currentUserId'
        )";

        if (!mysqli_query($conn, $query)) throw new Exception("Quotation insert failed: " . mysqli_error($conn));

        $quotationId = mysqli_insert_id($conn);

        // === Handle document uploads ===
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
                        $docQuery = "INSERT INTO quotation_document (quotation_id, document, created_by, updated_by, is_deleted)
                                     VALUES ('$quotationId', '$docFileName', '$currentUserId', '$currentUserId', 0)";
                        if (!mysqli_query($conn, $docQuery)) throw new Exception("Document insert failed: " . mysqli_error($conn));
                    }
                }
            }
        }

        // === Insert quotation items ===
        foreach ($_POST['product_id'] as $index => $product_id) {
            $product_id    = $_POST['product_id'][$index] ?? '';
            $quantity      = (float)($_POST['quantity'][$index] ?? 0);
            $unit_id       = $_POST['unit_id'][$index] ?? '';
            $selling_price = unformat($_POST['selling_price'][$index] ?? 0);
            $tax_id        = $_POST['tax_id'][$index] ?? '';
            $item_amount   = unformat($_POST['amount'][$index] ?? 0);

            $product_id_sql = ($product_id === '' ? 'NULL' : (int)$product_id);
            $unit_id_sql    = ($unit_id === '' ? 0 : (int)$unit_id);
            $tax_id_sql     = ($tax_id === '' ? 'NULL' : (int)$tax_id);

            if (!empty($product_id)) {
                $itemInsertQuery = "INSERT INTO quotation_item (
                    quotation_id, quantity, product_id, unit_id, selling_price,
                    tax_id, amount, org_id, is_deleted, created_by, updated_by
                ) VALUES (
                    '$quotationId', '$quantity', $product_id_sql, '$unit_id_sql', '$selling_price',
                    $tax_id_sql, '$item_amount', '$orgId', 0, '$currentUserId', '$currentUserId'
                )";

                if (!mysqli_query($conn, $itemInsertQuery)) throw new Exception("Item insert failed: " . mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        $_SESSION['message'] = "Quotation added successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: ../quotations.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
}
?>