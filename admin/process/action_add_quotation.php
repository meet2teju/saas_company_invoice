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
        $salesperson_id  = (int)($_POST['salesperson_id'] ?? 0); // Changed from user_id to salesperson_id
        $project_id      = (int)($_POST['project_id'] ?? 0);
        $client_note     = mysqli_real_escape_string($conn, $_POST['client_note'] ?? '');
        $description     = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
        $amount          = unformat($_POST['sub_amount'] ?? 0);
        $tax_amount      = unformat($_POST['tax_amount'] ?? 0);
        $shipping_charge = unformat($_POST['shipping_charge'] ?? 0);
        $total_amount    = unformat($_POST['total_amount'] ?? 0);
        
        // === GST Type field ===
        $gst_type        = mysqli_real_escape_string($conn, $_POST['gst_type'] ?? 'gst');

        // === Insert quotation master ===
        // Store: current logged-in user in user_id, salesperson in salesperson_id
        $query = "INSERT INTO quotation (
            client_id, quotation_id, reference_name, quotation_date, expiry_date, item_type, 
            salesperson_id, project_id, client_note, description, amount, tax_amount, 
            shipping_charge, total_amount, gst_type, org_id, user_id, is_deleted, created_by, updated_by
        ) VALUES (
            '$client_id', '$quotation_id', '$reference_name', '$quotation_date', '$expiry_date', '$item_type', 
            '$salesperson_id', '$project_id', '$client_note', '$description', '$amount', '$tax_amount', 
            '$shipping_charge', '$total_amount', '$gst_type', '$orgId', '$currentUserId', 0, '$currentUserId', '$currentUserId'
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
                        $docQuery = "INSERT INTO quotation_document (quotation_id, document, created_by, updated_by, is_deleted, org_id)
                                     VALUES ('$quotationId', '$docFileName', '$currentUserId', '$currentUserId', 0, '$orgId')";
                        if (!mysqli_query($conn, $docQuery)) throw new Exception("Document insert failed: " . mysqli_error($conn));
                    }
                }
            }
        }

        // === Insert quotation items - FIXED: Store product_id and service_id separately ===
        if (isset($_POST['item_id']) && is_array($_POST['item_id'])) {
            foreach ($_POST['item_id'] as $index => $item_id) {
                // Skip completely empty items
                if (empty($item_id) && empty($_POST['service_name'][$index]) && empty($_POST['selling_price'][$index])) {
                    continue;
                }

                $item_id       = $_POST['item_id'][$index] ?? '';
                $service_name  = mysqli_real_escape_string($conn, $_POST['service_name'][$index] ?? '');
                $quantity      = (float)($_POST['quantity'][$index] ?? 0);
                $selling_price = unformat($_POST['selling_price'][$index] ?? 0);
                $tax_id        = $_POST['tax_id'][$index] ?? '';
                $rate          = unformat($_POST['rate'][$index] ?? 0);
                $item_amount   = unformat($_POST['amount'][$index] ?? 0);
                // $code          = mysqli_real_escape_string($conn, $_POST['code'][$index] ?? '');
                $item_type_row = $_POST['item_type_row'][$index] ?? 'product'; // Get the row type

                // Initialize product_id and service_id
                $product_id_sql = 'NULL';
                $service_id_sql = 'NULL';
                $service_name_sql = 'NULL';

                // Determine whether it's a product or service and set appropriate values
                if ($item_type_row === 'product' && !empty($item_id)) {
                    // This is a product - store in product_id
                    $product_id_sql = (int)$item_id;
                } else if ($item_type_row === 'service') {
                    if (!empty($item_id)) {
                        // This is a service selected from dropdown - store in service_id
                        $service_id_sql = (int)$item_id;
                        // Also store the service name for reference
                        $service_name_sql = "'" . mysqli_real_escape_string($conn, $_POST['service_name'][$index] ?? '') . "'";
                    } else if (!empty($service_name)) {
                        // This is a custom service (no dropdown selection) - store in service_name
                        $service_name_sql = "'$service_name'";
                    }
                }

                $tax_id_sql = (empty($tax_id) ? 'NULL' : (int)$tax_id);

                $itemInsertQuery = "INSERT INTO quotation_item (
                    quotation_id, quantity, product_id, service_id, service_name, selling_price,
                    tax_id, rate, amount, org_id, is_deleted, created_by, updated_by
                ) VALUES (
                    '$quotationId', '$quantity', $product_id_sql, $service_id_sql, $service_name_sql, '$selling_price',
                    $tax_id_sql, '$rate', '$item_amount','$orgId', 0, '$currentUserId', '$currentUserId'
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