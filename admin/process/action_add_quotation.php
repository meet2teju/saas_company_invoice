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

// Function to get location info for GST calculation
function getLocationInfoForGST($conn, $clientId, $orgId) {
    $result = [
        'client_country_id' => 0,
        'client_state_id' => 0,
        'company_country_id' => 0,
        'company_state_id' => 0,
        'tax_type' => 'non_gst'
    ];
    
    try {
        // Get company location
        $companyQuery = "SELECT country_id, state_id FROM company_info 
                        WHERE org_id = ? AND is_deleted = 0 LIMIT 1";
        $stmt = $conn->prepare($companyQuery);
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $companyResult = $stmt->get_result();
        
        if ($companyData = $companyResult->fetch_assoc()) {
            $result['company_country_id'] = $companyData['country_id'] ?? 0;
            $result['company_state_id'] = $companyData['state_id'] ?? 0;
        }
        
        // Get client location from billing address
        $clientQuery = "SELECT billing_country, billing_state FROM client_address 
                       WHERE client_id = ? AND is_deleted = 0 
                       ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($clientQuery);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $clientResult = $stmt->get_result();
        
        if ($clientData = $clientResult->fetch_assoc()) {
            $result['client_country_id'] = $clientData['billing_country'] ?? 0;
            $result['client_state_id'] = $clientData['billing_state'] ?? 0;
        }
        
        // Get India country ID
        $indiaQuery = "SELECT id FROM countries WHERE name = 'India' LIMIT 1";
        $indiaResult = $conn->query($indiaQuery);
        $indiaData = $indiaResult->fetch_assoc();
        $indiaCountryId = $indiaData['id'] ?? 0;
        
        // Determine tax type based on locations
        if ($result['company_country_id'] == $indiaCountryId && 
            $result['client_country_id'] == $indiaCountryId) {
            // Both in India
            if ($result['company_state_id'] > 0 && $result['client_state_id'] > 0) {
                if ($result['company_state_id'] == $result['client_state_id']) {
                    $result['tax_type'] = 'cgst_sgst';
                } else {
                    $result['tax_type'] = 'igst';
                }
            } else {
                $result['tax_type'] = 'igst'; // Default for incomplete info
            }
        } elseif ($result['company_country_id'] == $indiaCountryId && 
                  $result['client_country_id'] != $indiaCountryId && 
                  $result['client_country_id'] > 0) {
            // Company in India, client outside India
            $result['tax_type'] = 'non_gst';
        }
        
    } catch (Exception $e) {
        error_log("Error getting location info: " . $e->getMessage());
    }
    
    return $result;
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
        $salesperson_id  = (int)($_POST['salesperson_id'] ?? 0);
        $project_id      = (int)($_POST['project_id'] ?? 0);
        $client_note     = mysqli_real_escape_string($conn, $_POST['client_note'] ?? '');
        $description     = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
        $amount          = unformat($_POST['sub_amount'] ?? 0);
        $tax_amount      = unformat($_POST['tax_amount'] ?? 0);
        $shipping_charge = unformat($_POST['shipping_charge'] ?? 0);
        $total_amount    = unformat($_POST['total_amount'] ?? 0);
        
        // === GST Type field ===
        $gst_type        = mysqli_real_escape_string($conn, $_POST['gst_type'] ?? 'non_gst');
        
        // === Get location information for GST ===
        $locationInfo = getLocationInfoForGST($conn, $client_id, $orgId);
        
        // Handle GST mode selection (Auto radio button is removed, only GST/Non-GST exist)
        $gst_mode_radio = $_POST['gst_type_radio'] ?? 'non_gst';
        if ($gst_mode_radio === 'gst') {
            // Manual GST mode selected - check if GST is applicable
            if ($locationInfo['tax_type'] === 'non_gst') {
                // If automatic determination says non-gst but user manually selected GST
                $locationInfo['tax_type'] = 'igst'; // Default to IGST for manual GST selection
            }
        } else {
            // Non-GST mode selected
            $locationInfo['tax_type'] = 'non_gst';
        }

        // === Insert quotation master with location info ===
        $query = "INSERT INTO quotation (
            client_id, quotation_id, reference_name, quotation_date, expiry_date, item_type, 
            salesperson_id, project_id, client_note, description, amount, tax_amount, 
            shipping_charge, total_amount, gst_type, tax_type,
            client_country_id, client_state_id, company_country_id, company_state_id,
            org_id, user_id, is_deleted, created_by, updated_by
        ) VALUES (
            '$client_id', '$quotation_id', '$reference_name', '$quotation_date', '$expiry_date', '$item_type', 
            '$salesperson_id', '$project_id', '$client_note', '$description', '$amount', '$tax_amount', 
            '$shipping_charge', '$total_amount', '$gst_type', '{$locationInfo['tax_type']}',
            '{$locationInfo['client_country_id']}', '{$locationInfo['client_state_id']}', 
            '{$locationInfo['company_country_id']}', '{$locationInfo['company_state_id']}',
            '$orgId', '$currentUserId', 0, '$currentUserId', '$currentUserId'
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

        // === Insert quotation items - Store CGST/SGST split if applicable ===
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
                $item_type_row = $_POST['item_type_row'][$index] ?? 'product';
                
                // Store tax breakdown for CGST/SGST
                $cgst_rate = 0;
                $sgst_rate = 0;
                $igst_rate = 0;
                
                if ($locationInfo['tax_type'] === 'cgst_sgst' && $rate > 0) {
                    // Split GST equally for CGST and SGST
                    $cgst_rate = $rate / 2;
                    $sgst_rate = $rate / 2;
                } elseif ($locationInfo['tax_type'] === 'igst' && $rate > 0) {
                    $igst_rate = $rate;
                }

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
                    tax_id, rate, cgst_rate, sgst_rate, igst_rate, amount, 
                    org_id, is_deleted, created_by, updated_by
                ) VALUES (
                    '$quotationId', '$quantity', $product_id_sql, $service_id_sql, $service_name_sql, '$selling_price',
                    $tax_id_sql, '$rate', '$cgst_rate', '$sgst_rate', '$igst_rate', '$item_amount',
                    '$orgId', 0, '$currentUserId', '$currentUserId'
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
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
        header("Location: ../add-quotation.php");
        exit();
    }
}
?>