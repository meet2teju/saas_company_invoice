<?php
include '../../config/config.php';
session_start();

// Upload function for files
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
    $clientId = $_POST['client_id']; // Get client ID from form

    mysqli_begin_transaction($conn);

    try {
        // === Get client data ===
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        // === Check if client email exists in other clients ===
        $checkClientEmail = "SELECT id FROM client 
                             WHERE email = '$email' 
                             AND id != '$clientId' 
                             AND is_deleted = 0";
        $resClientEmail = mysqli_query($conn, $checkClientEmail);
        if (mysqli_num_rows($resClientEmail) > 0) {
            $_SESSION['message'] = "Client email already exists in another client.";
            $_SESSION['message_type'] = 'danger';
            header("Location: ../customers.php");
            exit();
        }

        // === Check if contact emails exist for other clients ===
        if (!empty($_POST['contact_first_name'])) {
            foreach ($_POST['contact_email'] as $index => $contactEmail) {
                $contactEmail = mysqli_real_escape_string($conn, $contactEmail ?? '');
                if (!empty($contactEmail)) {
                    $checkContactEmail = "SELECT id FROM client_contact_persons 
                                          WHERE contact_email = '$contactEmail' 
                                          AND client_id != '$clientId' 
                                          AND is_deleted = 0";
                    $resContact = mysqli_query($conn, $checkContactEmail);
                    if (mysqli_num_rows($resContact) > 0) {
                        $_SESSION['message'] = "Contact email '$contactEmail' already exists for another client.";
                        $_SESSION['message_type'] = 'danger';
                        header("Location: ../customers.php");
                        exit();
                    }
                }
            }
        }

        // === Handle Image ===
        $imageName = $_POST['existing_image'] ?? ''; // Existing image from form
        if (!empty($_FILES['customer_image']['name'])) {
            $newImage = uploadFile($_FILES['customer_image'], '../../uploads/');
            if ($newImage) {
                // Delete old image
                if (!empty($imageName)) {
                    $oldImagePath = '../../uploads/' . $imageName;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $imageName = $newImage;
            }
        }

        // === Get remaining client data ===
        $salutation = mysqli_real_escape_string($conn, $_POST['salutation']);
        $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $display_name = mysqli_real_escape_string($conn, $_POST['display_name']);
        $client_type = (int)($_POST['client_type'] ?? 0);
        $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
        $business_number = mysqli_real_escape_string($conn, $_POST['business_number']);
        $enable_portal = isset($_POST['enable_portal']) ? 1 : 0;
        $remark = mysqli_real_escape_string($conn, $_POST['remark']);
        $currency = mysqli_real_escape_string($conn, $_POST['currency']);
        $pan = mysqli_real_escape_string($conn, $_POST['pan_number']);
        $website_url = mysqli_real_escape_string($conn, $_POST['website_url']);
        $department = mysqli_real_escape_string($conn, $_POST['department']);
        $designation = mysqli_real_escape_string($conn, $_POST['designation']);
        $twitter = mysqli_real_escape_string($conn, $_POST['twitter']);
        $skype_name_number = mysqli_real_escape_string($conn, $_POST['skype_name_number']);
        $facebook = mysqli_real_escape_string($conn, $_POST['facebook']);

        // === Update client ===
        $query = "UPDATE client SET 
            customer_image = '$imageName',
            salutation = '$salutation',
            company_name = '$company_name',
            first_name = '$first_name',
            last_name = '$last_name',
            display_name = '$display_name',
            client_type = '$client_type',
            phone_number = '$phone_number',
            business_number = '$business_number',
            email = '$email',
            enable_portal = '$enable_portal',
            remark = '$remark',
            pan_number = '$pan',
            website_url = '$website_url',
            department = '$department',
            designation = '$designation',
            twitter = '$twitter',
            skype_name_number = '$skype_name_number',
            facebook = '$facebook',
            currency = '$currency',
            updated_by = '$currentUserId',
            updated_at = NOW()
        WHERE id = '$clientId'";

        if (!mysqli_query($conn, $query)) {
            throw new Exception("Client update failed: " . mysqli_error($conn));
        }

        // === Update client_address ===
        $billing_name = mysqli_real_escape_string($conn, $_POST['billing_name']);
        $billing_address1 = mysqli_real_escape_string($conn, $_POST['billing_address1']);
        $billing_address2 = mysqli_real_escape_string($conn, $_POST['billing_address2']);
        $billing_country = isset($_POST['billing_country']) && $_POST['billing_country'] !== '' ? (int)$_POST['billing_country'] : 0;
        $billing_state = isset($_POST['billing_state']) && $_POST['billing_state'] !== '' ? (int)$_POST['billing_state'] : 0;
        $billing_city = isset($_POST['billing_city']) && $_POST['billing_city'] !== '' ? (int)$_POST['billing_city'] : 0;
        $billing_pincode = mysqli_real_escape_string($conn, $_POST['billing_pincode']);
        $shipping_name = mysqli_real_escape_string($conn, $_POST['shipping_name']);
        $shipping_address1 = mysqli_real_escape_string($conn, $_POST['shipping_address1']);
        $shipping_address2 = mysqli_real_escape_string($conn, $_POST['shipping_address2']);
        $shipping_country = isset($_POST['shipping_country']) && $_POST['shipping_country'] !== '' ? (int)$_POST['shipping_country'] : 0;
        $shipping_state = isset($_POST['shipping_state']) && $_POST['shipping_state'] !== '' ? (int)$_POST['shipping_state'] : 0;
        $shipping_city = isset($_POST['shipping_city']) && $_POST['shipping_city'] !== '' ? (int)$_POST['shipping_city'] : 0;
        $shipping_pincode = mysqli_real_escape_string($conn, $_POST['shipping_pincode']);

        $addressQuery = "UPDATE client_address SET 
            billing_name = '$billing_name',
            billing_address1 = '$billing_address1',
            billing_address2 = '$billing_address2',
            billing_country = '$billing_country',
            billing_state = '$billing_state',
            billing_city = '$billing_city',
            billing_pincode = '$billing_pincode',
            shipping_name = '$shipping_name',
            shipping_address1 = '$shipping_address1',
            shipping_address2 = '$shipping_address2',
            shipping_country = '$shipping_country',
            shipping_state = '$shipping_state',
            shipping_city = '$shipping_city',
            shipping_pincode = '$shipping_pincode',
            updated_by = '$currentUserId',
            updated_at = NOW()
        WHERE client_id = '$clientId'";

        if (!mysqli_query($conn, $addressQuery)) {
            throw new Exception("Address update failed: " . mysqli_error($conn));
        }

        // === Update client_bank ===
        $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
        $bank_branch = mysqli_real_escape_string($conn, $_POST['bank_branch']);
        $account_holder = mysqli_real_escape_string($conn, $_POST['account_holder']);
        $account_number = mysqli_real_escape_string($conn, $_POST['account_number']);
        $ifsc = mysqli_real_escape_string($conn, $_POST['IFSC_code']);

        $bankQuery = "UPDATE client_bank SET 
            bank_name = '$bank_name',
            bank_branch = '$bank_branch',
            account_holder = '$account_holder',
            account_number = '$account_number',
            IFSC_code = '$ifsc',
            updated_by = '$currentUserId',
            updated_at = NOW()
        WHERE client_id = '$clientId'";

        if (!mysqli_query($conn, $bankQuery)) {
            throw new Exception("Bank update failed: " . mysqli_error($conn));
        }

        // === Handle documents ===
        if (!empty($_FILES['documents']['name'][0])) {
            foreach ($_FILES['documents']['tmp_name'] as $key => $tmpName) {
                if (!empty($_FILES['documents']['name'][$key])) {
                    $document = [
                        'name' => $_FILES['documents']['name'][$key],
                        'type' => $_FILES['documents']['type'][$key],
                        'tmp_name' => $tmpName,
                        'error' => $_FILES['documents']['error'][$key],
                        'size' => $_FILES['documents']['size'][$key]
                    ];

                    $docFileName = uploadFile($document, '../../uploads/');
                    if ($docFileName) {
                        $docQuery = "INSERT INTO client_document (client_id, document, created_by, updated_by)
                                     VALUES ('$clientId', '$docFileName', '$currentUserId', '$currentUserId')";
                        if (!mysqli_query($conn, $docQuery)) {
                            throw new Exception("Document insert failed: " . mysqli_error($conn));
                        }
                    }
                }
            }
        }

        // === Update contact persons (delete + insert) ===
        mysqli_query($conn, "DELETE FROM client_contact_persons WHERE client_id = '$clientId'");

        if (!empty($_POST['contact_first_name'])) {
            foreach ($_POST['contact_first_name'] as $index => $firstName) {
                $salutation = mysqli_real_escape_string($conn, $_POST['contact_salutation'][$index] ?? '');
                $lastName = mysqli_real_escape_string($conn, $_POST['contact_last_name'][$index] ?? '');
                $email = mysqli_real_escape_string($conn, $_POST['contact_email'][$index] ?? '');
                $workPhone = mysqli_real_escape_string($conn, $_POST['contact_work_phone'][$index] ?? '');
                $mobile = mysqli_real_escape_string($conn, $_POST['contact_mobile'][$index] ?? '');
                $skype = mysqli_real_escape_string($conn, $_POST['contact_skype'][$index] ?? '');
                $designation = mysqli_real_escape_string($conn, $_POST['contact_designation'][$index] ?? '');
                $department = mysqli_real_escape_string($conn, $_POST['contact_department'][$index] ?? '');

            
                $contactInsertQuery = "INSERT INTO client_contact_persons (
                    client_id, contact_salutation, contact_first_name, contact_last_name, contact_email,
                    contact_work_phone, contact_mobile, contact_skype, contact_designation, contact_department,
                    status, org_id, is_deleted, created_by, updated_by
                ) VALUES (
                    '$clientId', '$salutation', '$firstName', '$lastName', '$email',
                    '$workPhone', '$mobile', '$skype', '$designation', '$department',
                    1, '$orgId', 0, '$currentUserId', '$currentUserId'
                )";

                if (!mysqli_query($conn, $contactInsertQuery)) {
                    throw new Exception("Contact insert failed: " . mysqli_error($conn));
                }
            }
        }

        mysqli_commit($conn);
        $_SESSION['message'] = 'Client updated successfully';
        $_SESSION['message_type'] = 'success';
        header("Location: ../customers.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['message'] = 'Update failed: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
        header("Location: ../customers.php");
        exit();
    }
}
?>
