<?php
include '../../config/config.php';
session_start();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// header('Content-Type: application/json');
// Upload function for images and documents
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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUserId = $_SESSION['user_id'] ?? 1;
    $orgId = $_SESSION['org_id'] ?? 1;

    mysqli_begin_transaction($conn);

    try {
        // Upload client image
        $imageName = '';
        if (!empty($_FILES['customer_image']['name'])) {
            $imageName = uploadFile($_FILES['customer_image'], '../../uploads/');
        }

        // Get client form data
        $salutation = $_POST['salutation'];
        $company_name = $_POST['company_name'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $display_name = $_POST['display_name'];
        $client_type = isset($_POST['client_type']) ? (int)$_POST['client_type'] : 0;
        $phone_number = $_POST['phone_number'];
        $business_number = $_POST['business_number'];
        $email = $_POST['email'];
        $enable_portal = isset($_POST['enable_portal']) ? 1 : 0;
        $remark = $_POST['remark'];
        $currency = $_POST['currency'];
        $pan = $_POST['pan_number'];
        $website_url = $_POST['website_url'];
        $department = $_POST['department'];
        $designation = $_POST['designation'];
        $twitter = $_POST['twitter'];
        $skype_name_number = $_POST['skype_name_number'];
        $facebook = $_POST['facebook'];

        // Insert client
        $query = "INSERT INTO client (
            customer_image, salutation, company_name, first_name, last_name, display_name, client_type,
            phone_number, business_number, email, enable_portal, remark, pan_number,
            website_url, department, designation, twitter, skype_name_number, facebook,
            currency, status, org_id, is_deleted, created_by, updated_by
        ) VALUES (
            '$imageName', '$salutation', '$company_name', '$first_name', '$last_name', '$display_name', '$client_type',
            '$phone_number', '$business_number', '$email', '$enable_portal', '$remark', '$pan',
            '$website_url', '$department', '$designation', '$twitter', '$skype_name_number', '$facebook',
            '$currency', 1, '$orgId', 0, '$currentUserId', '$currentUserId'
        )";

        if (!mysqli_query($conn, $query)) {
            throw new Exception("Client insert failed: " . mysqli_error($conn));
        }

        $clientId = mysqli_insert_id($conn); // Get inserted ID

        // Insert billing and shipping address into client_address
        $billing_name = $_POST['billing_name'];
        $billing_address1 = $_POST['billing_address1'];
        $billing_address2 = $_POST['billing_address2'];
        $billing_country = $_POST['billing_country'];
        $billing_state = $_POST['billing_state'];
        $billing_city = $_POST['billing_city'];
        $billing_pincode = $_POST['billing_pincode'];

        $shipping_name = $_POST['shipping_name'];
        $shipping_address1 = $_POST['shipping_address1'];
        $shipping_address2 = $_POST['shipping_address2'];
        $shipping_country = $_POST['shipping_country'];
        $shipping_state = $_POST['shipping_state'];
        $shipping_city = $_POST['shipping_city'];
        $shipping_pincode = $_POST['shipping_pincode'];

        $addressQuery = "INSERT INTO client_address (
            client_id, billing_name, billing_address1, billing_address2, billing_country, billing_state, billing_city, billing_pincode,
            shipping_name, shipping_address1, shipping_address2, shipping_country, shipping_state, shipping_city, shipping_pincode,
            status, org_id, is_deleted, created_by, updated_by
        ) VALUES (
            '$clientId', '$billing_name', '$billing_address1', '$billing_address2', '$billing_country', '$billing_state', '$billing_city', '$billing_pincode',
            '$shipping_name', '$shipping_address1', '$shipping_address2', '$shipping_country', '$shipping_state', '$shipping_city', '$shipping_pincode',
            1, '$orgId', 0, '$currentUserId', '$currentUserId'
        )";

        if (!mysqli_query($conn, $addressQuery)) {
            throw new Exception("Address insert failed: " . mysqli_error($conn));
        }

        // Insert banking details into client_bank
     // Get banking details (with null coalescing for safety)
                    $bank_name = $_POST['bank_name'] ?? '';
                    $bank_branch = $_POST['bank_branch'] ?? '';
                    $account_holder = $_POST['account_holder'] ?? '';
                    $account_number = $_POST['account_number'] ?? '';
                    $ifsc = $_POST['IFSC_code'] ?? '';

                    // Only insert if at least one banking field has a value
                    if (!empty($bank_name) || !empty($bank_branch) || !empty($account_holder) || 
                        !empty($account_number) || !empty($ifsc)) {
                        
                        $bankQuery = "INSERT INTO client_bank (
                            client_id, bank_name, bank_branch, account_holder, account_number, IFSC_code, status,
                            org_id, is_deleted, created_by, updated_by
                        ) VALUES (
                            '$clientId', '$bank_name', '$bank_branch', '$account_holder', '$account_number', '$ifsc', 1,
                            '$orgId', 0, '$currentUserId', '$currentUserId'
                        )";

                        if (!mysqli_query($conn, $bankQuery)) {
                            throw new Exception("Bank insert failed: " . mysqli_error($conn));
                        }
                    }
            // Else: Skip entirely - no record will be created in client_bank
        // Handle multiple document uploads
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
            // Insert multiple contact persons
           // Insert multiple contact persons - only if at least one field has data
if (!empty($_POST['contact_first_name'])) {
    foreach ($_POST['contact_first_name'] as $index => $firstName) {
        $salutation = $_POST['contact_salutation'][$index] ?? '';
        $lastName = $_POST['contact_last_name'][$index] ?? '';
        $email = $_POST['contact_email'][$index] ?? '';
        $workPhone = $_POST['contact_work_phone'][$index] ?? '';
        $mobile = $_POST['contact_mobile'][$index] ?? '';
        $skype = $_POST['contact_skype'][$index] ?? '';
        $designation = $_POST['contact_designation'][$index] ?? '';
        $department = $_POST['contact_department'][$index] ?? '';

        // Only insert if at least one main field has data
        if (!empty($firstName) || !empty($lastName) || !empty($email) || 
            !empty($workPhone) || !empty($mobile)) {
            
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
}

        // Commit everything
        mysqli_commit($conn);
        $_SESSION['success'] = "Client Added successfully!";
        header("Location: ../add-projects.php");
        exit();

    } 
    catch (Exception $e) {
        mysqli_rollback($conn);
        echo " Error: " . $e->getMessage();
    }
}
?>
