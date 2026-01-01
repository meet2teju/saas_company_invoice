<?php
session_start();
include '../../config/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ---------------- Helper Functions ----------------
function dbString($conn, $value) {
    return mysqli_real_escape_string($conn, trim($value ?? ''));
}

function unformat($value) {
    if ($value === null || $value === '') {
        return 0;
    }
    
    // Remove all non-numeric characters except decimal point and minus sign
    $cleaned = preg_replace('/[^0-9.-]/', '', $value);
    
    // Handle empty result
    if ($cleaned === '' || $cleaned === null) {
        return 0;
    }
    
    $result = (float)$cleaned;
    return is_nan($result) ? 0 : $result;
}

function dbInt($value) {
    return (is_numeric($value) && $value !== '') ? (int)$value : 0;
}

function dbFloat($value) {
    return (is_numeric($value) && $value !== '') ? (float)$value : 0;
}

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

// ---------------- Main Logic ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $currentUserId = $_SESSION['crm_user_id'] ?? 1;
    $orgId = $_SESSION['org_id'] ?? 1;
    $invoice_id = dbInt($_POST['id'] ?? 0);

    mysqli_begin_transaction($conn);

    try {
        // === Sanitize Inputs ===
        $client_id       = dbInt($_POST['client_id']);
        $reference_name  = dbString($conn, $_POST['reference_name'] ?? '');
        $invoice_date    = dbString($conn, $_POST['invoice_date']);
        $due_date        = dbString($conn, $_POST['due_date']);
        
        // FIX: Handle order_number properly - convert empty string to NULL for integer column
        $order_number_raw = $_POST['order_number'] ?? '';
        $order_number_sql = ($order_number_raw !== '' && is_numeric($order_number_raw)) 
                            ? (int)$order_number_raw 
                            : 'NULL';
        
        $item_type       = dbInt($_POST['item_type'] ?? 1);
        $user_id         = dbInt($_POST['user_id'] ?? 0);
        $project_id      = dbInt($_POST['project_id'] ?? 0);
        $invoice_note    = dbString($conn, $_POST['invoice_note'] ?? '');
        $description     = dbString($conn, $_POST['description'] ?? '');
        $amount          = dbFloat($_POST['sub_amount'] ?? 0);
        $tax_amount      = dbFloat($_POST['tax_amount'] ?? 0);
        $shipping_charge = unformat($_POST['shipping_charge'] ?? 0);
        $total_amount    = dbFloat($_POST['total_amount'] ?? 0);
        $gst_type        = dbString($conn, $_POST['gst_type'] ?? 'non_gst');
        $tax_type        = dbString($conn, $_POST['tax_type'] ?? 'non_gst');
        
        // === NEW: Get location information ===
        $client_country_id = dbInt($_POST['client_country_id'] ?? 0);
        $client_state_id = dbInt($_POST['client_state_id'] ?? 0);
        $company_country_id = dbInt($_POST['company_country_id'] ?? 0);
        $company_state_id = dbInt($_POST['company_state_id'] ?? 0);
        
        // If location info is not provided, calculate it
        if ($client_country_id == 0 || $company_country_id == 0) {
            $locationInfo = getLocationInfoForGST($conn, $client_id, $orgId);
            $client_country_id = $locationInfo['client_country_id'];
            $client_state_id = $locationInfo['client_state_id'];
            $company_country_id = $locationInfo['company_country_id'];
            $company_state_id = $locationInfo['company_state_id'];
        }
        
        // === FIXED: Handle GST mode selection to match quotation logic ===
        $gst_mode_radio = $_POST['gst_type_radio'] ?? 'non_gst';
        
        if ($gst_mode_radio === 'gst') {
            // Manual GST mode selected
            if ($tax_type === 'non_gst') {
                // If current tax_type is non_gst but user manually selected GST
                // Check if GST is applicable based on location
                $locationInfo = getLocationInfoForGST($conn, $client_id, $orgId);
                if ($locationInfo['tax_type'] !== 'non_gst') {
                    // GST is applicable based on location
                    $tax_type = $locationInfo['tax_type']; // Use location-based tax type
                    $gst_type = 'gst'; // Set gst_type to 'gst' when GST is applicable
                } else {
                    // GST not applicable by location, but user manually selected GST
                    // Default to 'igst' for manual GST selection
                    $tax_type = 'igst';
                    $gst_type = 'gst'; // Set gst_type to 'gst'
                }
            } else {
                // tax_type is already a GST type (cgst_sgst or igst)
                // Ensure gst_type is 'gst' not 'non_gst'
                if ($gst_type === 'non_gst') {
                    $gst_type = 'gst';
                }
            }
        } else if ($gst_mode_radio === 'non_gst') {
            // Non-GST mode selected - Set both fields to 'non_gst'
            $tax_type = 'non_gst';
            $gst_type = 'non_gst';
        }

        // --- Handle bank_id properly ---
        $bank_id_sql = "NULL";
        if (!empty($_POST['bank_id']) && is_numeric($_POST['bank_id'])) {
            $bank_id_sql = (int) $_POST['bank_id'];
        }

        // === 1. Update invoice main record ===
        // FIXED: Use $order_number_sql instead of '$order_number'
        $update_invoice = "UPDATE invoice SET
            client_id = '$client_id',
            reference_name = '$reference_name',
            invoice_date = '$invoice_date',
            due_date = '$due_date',
            order_number = $order_number_sql,
            item_type = '$item_type',
            user_id = '$user_id',
            project_id = '$project_id',
            bank_id = $bank_id_sql,
            invoice_note = '$invoice_note',
            description = '$description',
            amount = '$amount',
            tax_amount = '$tax_amount',
            shipping_charge = '$shipping_charge',
            total_amount = '$total_amount',
            gst_type = '$gst_type',
            tax_type = '$tax_type',
            client_country_id = '$client_country_id',
            client_state_id = '$client_state_id',
            company_country_id = '$company_country_id',
            company_state_id = '$company_state_id',
            updated_by = '$currentUserId'
            WHERE id = '$invoice_id'";
        
        if (!mysqli_query($conn, $update_invoice)) {
            throw new Exception("Failed to update invoice: " . mysqli_error($conn));
        }

        // === 2. Update invoice tasks ===
        // First, mark ALL tasks for this invoice as deleted
        $mark_tasks_deleted = "UPDATE invoice_tasks SET is_deleted = 1, updated_by = '$currentUserId' WHERE invoice_id = '$invoice_id'";
        if (!mysqli_query($conn, $mark_tasks_deleted)) {
            throw new Exception("Failed to mark old tasks as deleted: " . mysqli_error($conn));
        }

        // Then, insert or restore only the selected tasks
        $task_ids = [];
        if (isset($_POST['task_id']) && is_array($_POST['task_id'])) {
            foreach ($_POST['task_id'] as $task_id) {
                $task_id = dbInt($task_id);
                if ($task_id > 0) {
                    $task_ids[] = $task_id;
                    
                    // Check if this task already exists for this invoice
                    $check_task = "SELECT id FROM invoice_tasks WHERE invoice_id = '$invoice_id' AND task_id = '$task_id' LIMIT 1";
                    $task_exists = mysqli_query($conn, $check_task);

                    if ($task_exists && mysqli_num_rows($task_exists) > 0) {
                        // Task exists, just undelete it
                        $update_task = "UPDATE invoice_tasks SET is_deleted = 0, updated_by = '$currentUserId' WHERE invoice_id = '$invoice_id' AND task_id = '$task_id'";
                        if (!mysqli_query($conn, $update_task)) {
                            throw new Exception("Failed to update task: " . mysqli_error($conn));
                        }
                    } else {
                        // Insert new task
                        $insert_task = "INSERT INTO invoice_tasks (invoice_id, task_id, org_id, created_by, updated_by) 
                                        VALUES ('$invoice_id', '$task_id', '$orgId', '$currentUserId', '$currentUserId')";
                        if (!mysqli_query($conn, $insert_task)) {
                            throw new Exception("Failed to insert task: " . mysqli_error($conn));
                        }
                    }
                }
            }
        }

        // === 3. MARK ALL OLD ITEMS AS DELETED FIRST ===
        $mark_deleted = "UPDATE invoice_item SET is_deleted = 1 WHERE invoice_id = '$invoice_id'";
        
        if (!mysqli_query($conn, $mark_deleted)) {
            throw new Exception("Failed to mark old items as deleted: " . mysqli_error($conn));
        }

        // === 4. Process NEW items from form ===
        $item_id_array = $_POST['item_id'] ?? [];
        $item_type_row_array = $_POST['item_type_row'] ?? [];
        $service_name_array = $_POST['service_name'] ?? [];
        $quantity_array = $_POST['quantity'] ?? [];
        $selling_price_array = $_POST['selling_price'] ?? [];
        $unit_id_array = $_POST['unit_id'] ?? [];
        $tax_id_array = $_POST['tax_id'] ?? [];
        $rate_array = $_POST['rate'] ?? [];
        $amount_array = $_POST['amount'] ?? [];
        // $code_array = $_POST['code'] ?? [];
        
        $item_count = 0;
        $max_rows = count($item_type_row_array);
        
        for ($index = 0; $index < $max_rows; $index++) {
            // Get row type
            $item_type_row = $item_type_row_array[$index] ?? '';
            
            // Get raw values
            $raw_item_id = $_POST['item_id'][$index] ?? '';
            $raw_service_name = $_POST['service_name'][$index] ?? '';
            $raw_quantity = $_POST['quantity'][$index] ?? '';
            $raw_selling_price = $_POST['selling_price'][$index] ?? '';
            $raw_unit_id = $_POST['unit_id'][$index] ?? '';
            $raw_tax_id = $_POST['tax_id'][$index] ?? '';
            $raw_rate = $_POST['rate'][$index] ?? '';
            $raw_amount = $_POST['amount'][$index] ?? '';
            // $raw_code = $_POST['code'][$index] ?? '';
            
            // Process values
            $item_id = dbInt($raw_item_id);
            $service_name = dbString($conn, $raw_service_name);
            $quantity = dbFloat($raw_quantity);
            $selling_price = unformat($raw_selling_price);
            $unit_id = dbInt($raw_unit_id);
            $tax_id = dbInt($raw_tax_id);
            $rate = unformat($raw_rate);
            $amount = unformat($raw_amount);
            // $code = dbString($conn, $raw_code);
            
            // Check if this is an empty/blank row
            $is_empty_row = false;
            
            if ($item_type_row === 'product') {
                if ($item_id <= 0 || $selling_price <= 0) {
                    $is_empty_row = true;
                }
            } else if ($item_type_row === 'service') {
                $has_service_id = ($item_id > 0);
                $has_service_name = (!empty($service_name) && trim($service_name) !== '');
                
                if ((!$has_service_id && !$has_service_name) || $selling_price <= 0) {
                    $is_empty_row = true;
                }
            } else {
                $is_empty_row = true;
            }
            
            if ($is_empty_row) {
                continue;
            }

            // Initialize product_id and service_id
            $product_id_sql = 'NULL';
            $service_id_sql = 'NULL';
            $service_name_sql = 'NULL';

            if ($item_type_row === 'product') {
                $product_id_sql = $item_id;
                if ($quantity <= 0) {
                    $quantity = 1;
                }
            } else if ($item_type_row === 'service') {
                if ($item_id > 0) {
                    $service_id_sql = $item_id;
                    $service_name_sql = "'" . $service_name . "'";
                } else if (!empty($service_name)) {
                    $service_name_sql = "'" . $service_name . "'";
                }
            }

            // Calculate amount if not provided
            if ($amount <= 0 && $selling_price > 0) {
                $calc_quantity = $quantity;
                if ($item_type_row === 'service' && $quantity <= 0) {
                    $calc_quantity = 1;
                }
                
                $line_subtotal = $selling_price * $calc_quantity;
                $line_tax = $line_subtotal * ($rate / 100);
                $amount = $line_subtotal + $line_tax;
            }
            
            // Final validation
            if ($selling_price <= 0 || $amount <= 0) {
                continue;
            }

            // Set tax_id - only if not in non_gst mode
            $tax_id_sql = 'NULL';
            if ($gst_type !== 'non_gst' && $tax_type !== 'non_gst' && $tax_id > 0) {
                $tax_id_sql = $tax_id;
            } else if ($gst_type === 'non_gst' || $tax_type === 'non_gst') {
                $rate = 0;
                $tax_id_sql = 'NULL';
            }

            // Calculate CGST/SGST/IGST breakdown
            $cgst_rate = 0;
            $sgst_rate = 0;
            $igst_rate = 0;
            
            if ($tax_type === 'cgst_sgst' && $rate > 0) {
                // Split GST equally for CGST and SGST
                $cgst_rate = $rate / 2;
                $sgst_rate = $rate / 2;
            } elseif ($tax_type === 'igst' && $rate > 0) {
                $igst_rate = $rate;
            }

            // INSERT new item
            $insert_item = "INSERT INTO invoice_item (
                invoice_id, 
                product_id, 
                service_id, 
                service_name, 
                quantity, 
                selling_price, 
                unit_id,
                tax_id, 
                rate,
                cgst_rate,
                sgst_rate,
                igst_rate,
                amount, 
                org_id, 
                created_by, 
                updated_by
            ) VALUES (
                '$invoice_id', 
                $product_id_sql, 
                $service_id_sql, 
                $service_name_sql, 
                '$quantity',
                '$selling_price', 
                '$unit_id',
                $tax_id_sql, 
                '$rate',
                '$cgst_rate',
                '$sgst_rate',
                '$igst_rate',
                '$amount', 
                '$orgId',
                '$currentUserId', 
                '$currentUserId'
            )";
            
            if (!mysqli_query($conn, $insert_item)) {
                throw new Exception("Failed to insert item at index $index: " . mysqli_error($conn));
            }
            
            $item_count++;
        }

        // === 5. Handle task items in invoice_item table ===
        // Also add task items to invoice_item table for consistency
        if (!empty($task_ids)) {
            foreach ($task_ids as $task_id) {
                // Get task details to create invoice item
                $task_details_query = "SELECT pt.task_name, pt.hour, p.rate_per_hour 
                                      FROM project_task pt 
                                      LEFT JOIN project p ON pt.project_id = p.id 
                                      WHERE pt.id = '$task_id'";
                $task_details_result = mysqli_query($conn, $task_details_query);
                
                if ($task_details_result && mysqli_num_rows($task_details_result) > 0) {
                    $task = mysqli_fetch_assoc($task_details_result);
                    $task_name = dbString($conn, $task['task_name']);
                    $hours = dbFloat($task['hour'] ?? 0);
                    $rate_per_hour = dbFloat($task['rate_per_hour'] ?? 0);
                    $task_amount = $hours * $rate_per_hour;
                    
                    // Insert task as invoice item
                    $insert_task_item = "INSERT INTO invoice_item (
                        invoice_id, service_name, quantity, 
                        selling_price, unit_id, tax_id, rate, amount, org_id, 
                        created_by, updated_by
                    ) VALUES (
                        '$invoice_id', '$task_name', '$hours',
                        '$rate_per_hour', '0', '0', '0', '$task_amount', '$orgId',
                        '$currentUserId', '$currentUserId'
                    )";
                    
                    if (!mysqli_query($conn, $insert_task_item)) {
                        // Silently continue if task item insertion fails
                    }
                }
            }
        }

        // === 6. Handle document uploads ===
        if (!empty($_FILES['document']['name'][0])) {
            $uploadDir = '../../uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            foreach ($_FILES['document']['tmp_name'] as $key => $tmpName) {
                if (!empty($_FILES['document']['name'][$key]) && $_FILES['document']['error'][$key] === 0) {
                    $document = [
                        'name' => $_FILES['document']['name'][$key],
                        'type' => $_FILES['document']['type'][$key],
                        'tmp_name' => $tmpName,
                        'error' => $_FILES['document']['error'][$key],
                        'size' => $_FILES['document']['size'][$key]
                    ];

                    $docFileName = uploadFile($document, $uploadDir);
                    if ($docFileName) {
                        $docQuery = "INSERT INTO invoice_document (
                            invoice_id, document, created_by, updated_by
                        ) VALUES (
                            '$invoice_id', '$docFileName', '$currentUserId', '$currentUserId'
                        )";
                        
                        if (!mysqli_query($conn, $docQuery)) {
                            throw new Exception("Document insert failed: " . mysqli_error($conn));
                        }
                    }
                }
            }
        }

        // === 7. Commit transaction ===
        mysqli_commit($conn);
        
        $_SESSION['message'] = "Invoice updated successfully with $item_count items!";
        $_SESSION['message_type'] = "success";
        
        header("Location: ../edit-invoice.php?id=" . $invoice_id);
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error_message = "Error: " . $e->getMessage();
        
        $_SESSION['error'] = $error_message;
        header("Location: ../edit-invoice.php?id=" . $invoice_id);
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request";
    header("Location: ../invoices.php");
    exit();
}
?>