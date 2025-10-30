<?php
include 'layouts/session.php';
include '../config/config.php';

// Simple permission check
$has_import_permission = true; // Set to false if you want to restrict access

if (!$has_import_permission) {
    $_SESSION['message'] = "You don't have permission to import clients";
    $_SESSION['message_type'] = 'danger';
    header('Location: customers.php');
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Process the uploaded file
    $file = $_FILES['file'];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = 'File upload failed with error code: ' . $file['error'];
        $message_type = 'danger';
    } else {
        // Check file extension
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['csv'];
        
        if (!in_array($file_ext, $allowed_ext)) {
            $message = 'Invalid file type. Please upload CSV files only.';
            $message_type = 'danger';
        } else {
            // Process the file
            $message = processCSVFile($file, $conn);
            $message_type = strpos($message, 'successfully') !== false ? 'success' : 'danger';
        }
    }
}

function processCSVFile($file, $conn) {
    $handle = fopen($file['tmp_name'], 'r');
    if (!$handle) {
        return "Failed to open file.";
    }
    
    // Get header row to validate columns
    $header = fgetcsv($handle);
    if (!$header || count($header) < 2) {
        fclose($handle);
        return "Invalid CSV format. File must contain at least 2 columns.";
    }
    
    $imported_count = 0;
    $skipped_count = 0;
    $errors = [];
    $line = 1;
    
    while (($row = fgetcsv($handle)) !== FALSE) {
        $line++;
        
        // Skip empty rows
        if (empty(array_filter($row))) {
            continue;
        }
        
        // Validate required fields
        if (empty($row[0]) || empty($row[1])) {
            $errors[] = "Line $line: Missing required fields (First Name and Email are required)";
            $skipped_count++;
            continue;
        }
        
        // Validate email format
        $email = trim($row[1]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Line $line: Invalid email format '$email'";
            $skipped_count++;
            continue;
        }
        
        // Check if client already exists by email
        $email = mysqli_real_escape_string($conn, $email);
        $check_query = "SELECT id FROM client WHERE email = '$email' AND is_deleted = 0";
        $result = mysqli_query($conn, $check_query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $errors[] = "Line $line: Client with email '$email' already exists";
            $skipped_count++;
            continue;
        }
        
        // Prepare client data
        $first_name = mysqli_real_escape_string($conn, trim($row[0] ?? ''));
        $company_name = mysqli_real_escape_string($conn, trim($row[2] ?? ''));
        $phone_number = mysqli_real_escape_string($conn, trim($row[3] ?? ''));
        $current_amount = is_numeric($row[4] ?? 0) ? floatval($row[4]) : 0;
        
        // Insert client
        $insert_query = "INSERT INTO client (first_name, email, company_name, phone_number, created_at) 
                             VALUES ('$first_name', '$email', '$company_name', '$phone_number',NOW())";
        
        if (mysqli_query($conn, $insert_query)) {
            $imported_count++;
            
            // If you have an address table, you can insert basic address data here
            if (!empty($row[5])) { // Assuming country is in column 5
                $client_id = mysqli_insert_id($conn);
                $country = mysqli_real_escape_string($conn, trim($row[5]));
                
                // Get country ID if it exists in countries table
                $country_query = "SELECT id FROM countries WHERE name LIKE '%$country%' LIMIT 1";
                $country_result = mysqli_query($conn, $country_query);
                
                if ($country_result && mysqli_num_rows($country_result) > 0) {
                    $country_data = mysqli_fetch_assoc($country_result);
                    $country_id = $country_data['id'];
                    
                    $address_query = "INSERT INTO client_address (client_id, billing_country) 
                                     VALUES ($client_id, $country_id)";
                    mysqli_query($conn, $address_query);
                }
            }
        } else {
            $errors[] = "Line $line: Failed to import - " . mysqli_error($conn);
            $skipped_count++;
        }
    }
    
    fclose($handle);
    
    // Prepare result message
    $result_message = "Import completed. $imported_count clients imported successfully.";
    
    if ($skipped_count > 0) {
        $result_message .= " $skipped_count clients skipped.";
    }
    
    if (!empty($errors)) {
        $_SESSION['import_errors'] = $errors;
    }
    
    return $result_message;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layouts/title-meta.php'; ?> 
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .import-instructions {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin-bottom: 20px;
        }
        .import-instructions h6 {
            margin-top: 0;
            color: #0d6efd;
        }
        .sample-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .sample-table th, .sample-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        .sample-table th {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>

    <!-- Start Main Wrapper -->
    <div class="main-wrapper">

        <?php include 'layouts/menu.php'; ?>

        <!-- Page Content -->
        <div class="page-wrapper">
            <div class="content content-two">

                <!-- Page Header -->
                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <h6>Import Clients</h6>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                        <a href="customers.php" class="btn btn-outline-white d-inline-flex align-items-center">
                            <i class="isax isax-arrow-left me-1"></i> Back to Clients
                        </a>
                    </div>
                </div>
                <!-- End Page Header -->

                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['import_errors']) && !empty($_SESSION['import_errors'])): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading">Import Errors:</h6>
                    <ul class="mb-0" style="max-height: 200px; overflow-y: auto;">
                        <?php foreach ($_SESSION['import_errors'] as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['import_errors']); ?>
                <?php endif; ?>

                <!-- Import Form -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Import Clients from CSV File</h6>
                        
                        <div class="import-instructions">
                            <h6>File Requirements:</h6>
                            <ul>
                                <li>Supported format: CSV (Comma Separated Values)</li>
                                <li>Required columns: <strong>First Name, Email</strong></li>
                                <li>Optional columns: Company Name, Phone Number, Balance, Country</li>
                                <li>First row should contain column headers</li>
                                <li>Maximum file size: 2MB</li>
                            </ul>
                            
                            <!-- <h6 class="mt-3">Sample CSV Format:</h6>
                            <table class="sample-table">
                                <thead>
                                    <tr>
                                        <th>First Name</th>
                                        <th>Email</th>
                                        <th>Company Name</th>
                                        <th>Phone Number</th>
                                        <th>Balance</th>
                                        <th>Country</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>John Doe</td>
                                        <td>john@example.com</td>
                                        <td>ABC Company</td>
                                        <td>123-456-7890</td>
                                        <td>1000.00</td>
                                        <td>United States</td>
                                    </tr>
                                    <tr>
                                        <td>Jane Smith</td>
                                        <td>jane@example.com</td>
                                        <td>XYZ Corp</td>
                                        <td>098-765-4321</td>
                                        <td>2500.50</td>
                                        <td>Canada</td>
                                    </tr>
                                </tbody>
                            </table> -->
                            
                            <a href="./process/generate_sample_client_file.php" class="btn btn-outline-primary mt-2">
                                <i class="isax isax-document-download me-1"></i> Download Sample CSV File
                            </a>
                        </div>
                        
                        <form action="import_client_excel.php" method="POST" enctype="multipart/form-data" onsubmit="return validateFile()">
                            <div class="mb-3">
                                <label for="file" class="form-label">Select CSV File</label>
                                <input type="file" class="form-control" id="file" name="file" accept=".csv" required>
                                <div class="form-text">Only CSV files are accepted. Maximum file size: 2MB</div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="isax isax-import me-1"></i> Import Clients
                                </button>
                                <a href="customers.php" class="btn btn-outline-white">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- End Import Form -->

            </div>

            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>

    <?php include 'layouts/vendor-scripts.php'; ?>
    
    <script>
    function validateFile() {
        const fileInput = document.getElementById('file');
        const file = fileInput.files[0];
        
        if (!file) {
            alert('Please select a file to upload.');
            return false;
        }
        
        // Check file extension
        const fileName = file.name;
        const fileExt = fileName.split('.').pop().toLowerCase();
        
        if (fileExt !== 'csv') {
            alert('Please select a CSV file. Only .csv files are allowed.');
            return false;
        }
        
        // Check file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size exceeds the maximum limit of 2MB.');
            return false;
        }
        
        return true;
    }
    </script>
</body>
</html>