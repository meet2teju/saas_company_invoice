<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

// Get company currency (already available via session.php)
$companyCurrency = getCompanyCurrency($conn);

// Get current user info
$currentUserId = $_SESSION['crm_user_id'] ?? 0;
$currentOrgId = $_SESSION['org_id'] ?? 0;
$userRoleId = $_SESSION['role_id'] ?? 0;

// Get the correct org_id from database if session org_id is 0
if ($currentOrgId == 0 && $currentUserId > 0) {
    $fixQuery = "SELECT org_id, role_id FROM login WHERE id = $currentUserId";
    $fixResult = mysqli_query($conn, $fixQuery);
    if ($fixResult && mysqli_num_rows($fixResult) > 0) {
        $userData = mysqli_fetch_assoc($fixResult);
        $_SESSION['org_id'] = $userData['org_id'];
        $_SESSION['role_id'] = $userData['role_id'];
        $currentOrgId = $userData['org_id'];
        $userRoleId = $userData['role_id'];
    }
}

// Get next AUTO_INCREMENT value
$query = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES 
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoice'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($row && isset($row['AUTO_INCREMENT'])) {
    $nextId = $row['AUTO_INCREMENT'];
    $newinvoiceID = 'INV-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
} else {
    // Fallback in case of error
    $newinvoiceID = 'INV-0001';
}

// Fetch tax rates from database with organization filtering
$taxRates = [];
$taxQuery = "SELECT id, name, rate FROM tax WHERE status = 1";
if ($currentOrgId > 0) {
    $taxQuery .= " AND org_id = $currentOrgId";
}
$taxResult = mysqli_query($conn, $taxQuery);
while ($taxRow = mysqli_fetch_assoc($taxResult)) {
    $taxRates[] = $taxRow;
}

// Fetch products and services from product table based on item_type with organization filtering
$products = [];
$services = [];
$itemQuery = "SELECT p.id, p.name, p.selling_price, p.code, p.item_type, 
                     t.id AS tax_id, t.rate AS tax_rate, t.name AS tax_name
              FROM product p
              LEFT JOIN tax t ON p.tax_id = t.id
              WHERE p.is_deleted = 0 AND p.status = 1";
if ($currentOrgId > 0) {
    $itemQuery .= " AND p.org_id = $currentOrgId";
}
// Add role-based filtering for non-admin users
if ($userRoleId != 1) {
    $itemQuery .= " AND (p.user_id = $currentUserId OR EXISTS (
        SELECT 1 FROM login u 
        WHERE u.id = p.user_id 
        AND u.role_id = 1 
        AND u.org_id = $currentOrgId
    ))";
}
$itemQuery .= " ORDER BY p.name ASC";

$itemResult = mysqli_query($conn, $itemQuery);
while ($item = mysqli_fetch_assoc($itemResult)) {
    if ($item['item_type'] == 1) {
        $products[] = $item;
    } else {
        $services[] = $item;
    }
}

// Get India country ID from database
$indiaCountryId = 0;
$countryQuery = "SELECT id FROM countries WHERE name = 'India' LIMIT 1";
$countryResult = mysqli_query($conn, $countryQuery);
if ($countryResult && mysqli_num_rows($countryResult) > 0) {
    $countryRow = mysqli_fetch_assoc($countryResult);
    $indiaCountryId = $countryRow['id'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include 'layouts/title-meta.php'; ?> 
	<?php include 'layouts/head-css.php'; ?>
   <!-- Additional CSS for datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .tax-display-container {
            min-height: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            background-color: #f8f9fa;
        }
        .tax-amount-line {
            font-weight: 600;
            font-size: 14px;
            line-height: 1.2;
        }
        .tax-rate-line {
            font-size: 12px;
            color: #6c757d;
            line-height: 1.2;
        }
        .table td {
            vertical-align: middle;
        }
        .service-fields {
            display: none;
        }
        .service-row .service-fields {
            display: block;
        }
        .service-row .product-fields {
            display: none;
        }
        .product-row .product-fields {
            display: block;
        }
        .product-row .service-fields {
            display: none;
        }
        .service-quantity {
            background-color: #f8f9fa;
        }
        .service-custom-input {
            margin-top: 5px;
        }
        .gst-toggle-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .gst-toggle-label {
            font-weight: 600;
            color: #495057;
        }
        .non-gst-mode .tax-column {
            display: none;
        }
        .non-gst-mode .tax-details {
            display: none !important;
        }
        /* Added for product tax dropdown */
        .product-tax-select {
            margin-bottom: 8px;
        }
        /* Currency info badge */
        .currency-badge {
            background: #0dcaf0;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        /* Added for validation error styling */
        .product-service-error {
            display: none;
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
        .is-invalid-dropdown {
            border-color: #dc3545 !important;
        }
        /* GST info badge */
        #gst-info-badge {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            margin-left: 10px;
        }
        .tax-breakdown {
            font-size: 12px;
            color: #6c757d;
        }
        .cgst-sgst-split {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        .cgst-sgst-split .tax-half {
            width: 48%;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- Start Main Wrapper -->
    <div class="main-wrapper">

		<?php include 'layouts/menu.php'; ?>

        <!-- ========================
			Start Page Content
		========================= -->

        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content">

                <!-- Start row  -->
                <div class="row">
                    <div class="col-md-12 mx-auto">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6>Add Invoice</h6>
                                <div class="d-flex align-items-center gap-3">
                                    <!-- Display current company currency -->
                                    <!-- <div class="currency-badge">
                                        Company Currency: <?php echo $companyCurrency['currency_symbol'] . ' (' . $companyCurrency['currency_name'] . ')'; ?>
                                    </div> -->
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form action="process/action_add_invoice.php" method="POST" enctype="multipart/form-data" id="form">
                                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['crm_user_id'] ?? ''; ?>">
                                        <input type="hidden" name="gst_type" id="gst_type_field" value="non_gst">
                                        <input type="hidden" name="tax_type" id="tax_type_field" value="non_gst">
                                        <input type="hidden" name="india_country_id" value="<?php echo $indiaCountryId; ?>">
                                        
                                        <!-- Add currency hidden fields -->
                                        <input type="hidden" name="currency_id" value="<?php echo $companyCurrency['id']; ?>">
                                        <input type="hidden" name="currency_symbol" value="<?php echo $companyCurrency['currency_symbol']; ?>">
                                        <input type="hidden" name="currency_name" value="<?php echo $companyCurrency['currency_name']; ?>">

                                        <div class="border-bottom mb-3 pb-1">
                                          <div class="row gx-3">
                                            <div class="col-lg-4 col-md-6">
                                              <div class="mb-3">
                                                <label class="form-label">Client Name<span class="text-danger">*</span></label>
                                               <select class="form-select select2" name="client_id" id="client_id">
    <option value="">Select Client</option>
    <?php
    // Get selected client ID from URL
    $selectedClient = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;

    // MODIFIED: Apply access control to clients dropdown
    $clients_query = "SELECT * FROM client WHERE is_deleted = 0";
    if ($currentOrgId > 0) {
        $clients_query .= " AND org_id = $currentOrgId";
    }
    // Add role-based filtering for non-admin users
    if ($userRoleId != 1) {
        $clients_query .= " AND (user_id = $currentUserId OR EXISTS (
            SELECT 1 FROM login u 
            WHERE u.id = client.user_id 
            AND u.role_id = 1 
            AND u.org_id = $currentOrgId
        ))";
    }
    $clients_query .= " ORDER BY first_name ASC";
    
    $result = mysqli_query($conn, $clients_query);
    while ($row = mysqli_fetch_assoc($result)) {
        $isSelected = ($row['id'] == $selectedClient) ? 'selected' : '';
        
        // Build the display name with salutation, first name, last name, and company
        $displayName = trim($row['salutation'] . ' ' . $row['first_name'] . ' ' . $row['last_name']);
        if (!empty($row['company_name'])) {
            $displayName .= ' - ' . $row['company_name'];
        }
        
        echo '<option value="' . $row['id'] . '" ' . $isSelected . '>' . htmlspecialchars($displayName) . '</option>';
    }
    ?>
</select>
                                                <span class="text-danger error-text" id="clientname_error"></span>
                                              </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                              <div class="mb-3">
                                                <label class="form-label">Project</label>
                                                <select class="form-select select2" name="project_id" id="project_id" disabled>
                                                  <option value="">Select Project</option>
                                                </select>
                                                <!-- <span class="text-danger error-text" id="project_error"></span> -->
                                              </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                              <div class="mb-3">
                                                <label class="form-label">Tasks</label>
                                                <select class="form-select select2" name="task_id[]" id="task_id" multiple="multiple" disabled>
                                                  <option value="">Select Tasks</option>
                                                </select>
                                                <!-- <span class="text-danger error-text" id="task_error"></span> -->
                                              </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                              <div class="mb-3">
                                                  <label class="form-label">Reference Name</label>
                                                  <input type="text" class="form-control" name="reference_name" id="reference_name">
                                              </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                              <div class="mb-3">
                                                <label class="form-label">Order Number</label>
                                                <input type="number" class="form-control" name="order_number">
                                              </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                              <div class="mb-3">
                                                  <label class="form-label">Invoice Number</label>
                                                  <input type="text" class="form-control" name="invoice_id" value="<?= $newinvoiceID ?>" >
                                              </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                              <div class="mb-3">
                                                  <label class="form-label">Salesperson</label>
                                                    <select class="form-select select2" name="user_id" id="user_id">
                                                  <option value="">Select Salesperson</option>
                                                  <?php
                                                  // MODIFIED: Apply organization filtering to salesperson dropdown
                                                  $query = "SELECT login.id, login.name FROM login
                                                            JOIN user_role ON login.role_id = user_role.id
                                                            WHERE login.is_deleted = 0";
                                                  if ($currentOrgId > 0) {
                                                      $query .= " AND login.org_id = $currentOrgId";
                                                  }
                                                  $query .= " ORDER BY login.name ASC";
                                                  
                                                  $result = mysqli_query($conn, $query);
                                                  while ($row = mysqli_fetch_assoc($result)) {
                                                      echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                                                  }
                                                  ?>
                                              </select>
                                              <!-- <span class="text-danger error-text" id="username_error"></span> -->
                                              </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <label class="form-label">Invoice Date<span class="text-danger">*</span></label>
                                                <div class="input-group position-relative mb-3">
                                                    <input type="text" class="form-control datepicker"id="invoice_date" name="invoice_date">
                                                    <span class="input-icon-addon fs-16 text-gray-9">
                                                      <i class="isax isax-calendar-2"></i>
                                                    </span>
                                                </div>
                                                <span class="text-danger error-text" id="invoice_date_error"></span>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                    <label class="form-label">Invoice Due Date<span class="text-danger">*</span></label>
                                                    <div class="input-group position-relative mb-3">
                                                        <input type="text" class="form-control datepicker" id="due_date" name="due_date">
                                                        <span class="input-icon-addon fs-16 text-gray-9">
                                                          <i class="isax isax-calendar-2"></i>
                                                        </span>
                                                    </div>
                                                  <span class="text-danger error-text" id="invoice_due_error"></span> 
                                                </div>
                                            </div>
                                          </div>
                                        <div class="border-bottom mb-3">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="card shadow-none">
                                                        <div class="card-body">
                                                            <h6 class="mb-3">Bill To</h6>
                                                            <div class="bg-light border rounded p-3 d-flex align-items-start">
                                                                <div id="client_info_block"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="card shadow-none">
                                                        <div class="card-body">
                                                            <h6 class="mb-3">Bill From</h6>
                                                            <div class="bg-light border rounded p-3 d-flex align-items-start">
                                                                <div id="shipping_info_block"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                   <div class="border-bottom mb-3 pb-3">
                                    <h6 class="mb-3">Items & Details</h6>
                                            <div class="row justify-content-between">
                                                <div class="col-auto">
                                                    <div>
                                                        <label class="form-label">Item Type<span class="text-danger">*</span></label>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="form-check me-3">
                                                                <input class="form-check-input" type="radio" name="item_type" id="Radio-product" value="1" checked>
                                                                <label class="form-check-label" for="Radio-product">Product</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="item_type" id="Radio-service" value="0">
                                                                <label class="form-check-label" for="Radio-service">Service</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- GST Type section updated -->
                                                <div class="col-auto">
                                                    <div>
                                                        <label class="form-label">Tax Mode:</label>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="form-check me-3">
                                                                <input class="form-check-input" type="radio" name="gst_type_radio" id="gst-manual" value="gst" checked>
                                                                <label class="form-check-label" for="gst-manual">GST</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="gst_type_radio" id="gst-none" value="non_gst">
                                                                <label class="form-check-label" for="gst-none">Non-GST</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="table-responsive rounded table-nowrap border-bottom-0 border mb-3">
                                                <table class="table mb-0 add-table">
                                                    <thead class="table-dark" id="table-heading">
                                                        <tr>
                                                            <th>Product/Service</th>
                                                            <th>Quantity</th>
                                                            <th>HSN Code</th>
                                                            <th>Selling Price</th>
                                                            <th class="tax-column">Tax</th>
                                                            <th>Amount</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="add-tbody" id="product">
                                                        <span class="text-danger error-text" id="product_error"></span>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div>
                                                <a href="javascript:void(0);" class="d-inline-flex align-items-center add-invoice-data"><i class="isax isax-add-circle5 text-primary me-1"></i>Add New</a>
                                            </div>
                                        </div>
                                        <div class="border-bottom mb-3">
                                            <!-- start row -->
                                            <div class="row">
                                                <div class="col-lg-7">
                                                    <div class="mb-3">
                                                        <h6 class="mb-3">Extra Information</h6>
                                                        <div>
                                                            <ul class="nav nav-tabs nav-solid-primary tab-style-1 border-0 p-0 d-flex flex-wrap gap-3 mb-3" role="tablist">
                                                                <li class="nav-item" role="presentation">
                                                                    <a class="nav-link active d-inline-flex align-items-center border fs-12 fw-semibold rounded-2" data-bs-toggle="tab" data-bs-target="#notes" aria-current="page" href="javascript:void(0);"><i class="isax isax-document-text me-1"></i>Add Notes</a>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <a class="nav-link d-inline-flex align-items-center border fs-12 fw-semibold rounded-2" data-bs-toggle="tab" data-bs-target="#terms" href="javascript:void(0);"><i class="isax isax-document me-1"></i>Add Terms & Conditions</a>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <a class="nav-link d-inline-flex align-items-center border fs-12 fw-semibold rounded-2" data-bs-toggle="tab" data-bs-target="#bank" href="javascript:void(0);" id="bank-tab-link"><i class="isax isax-bank me-1"></i>Bank Details</a>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <a class="nav-link d-inline-flex align-items-center border fs-12 fw-semibold rounded-2" data-bs-toggle="tab" data-bs-target="#documents" href="javascript:void(0);"><i class="isax isax-bank me-1"></i>Upload Documnets</a>
                                                                </li>
                                                            </ul>
                                                            <div class="tab-content">
                                                                <div class="tab-pane active show" id="notes" role="tabpanel">
                                                                    <label class="form-label">Additional Notes</label>
                                                                    <textarea class="form-control" name="invoice_note"></textarea>
                                                                </div>
                                                                <div class="tab-pane fade" id="terms" role="tabpanel">
                                                                    <label class="form-label">Terms & Conditions</label>
                                                                    <textarea class="form-control" name="description"></textarea>
                                                                </div>
                                                                <div class="tab-pane fade" id="bank" role="tabpanel">
                                                                    <label class="form-label">Account<span class="text-danger">*</span></label>
                                                                    <select class="select2" name="bank_id" id="bank_id">
                                                                         <option value="">Select Account</option>
                                                                        <?php                                                         
                                                                        // MODIFIED: Apply organization filtering to bank accounts
                                                                        $bank_query = "SELECT * FROM bank WHERE status = 1";
                                                                        if ($currentOrgId > 0) {
                                                                            $bank_query .= " AND org_id = $currentOrgId";
                                                                        }
                                                                        $bank_query .= " ORDER BY account_holder ASC";
                                                                        
                                                                        $result = mysqli_query($conn, $bank_query);
                                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                                        echo '<option value="' . $row['id'] . '">' . $row['account_holder'] . ' - ' . $row['account_number'] . ' (' . $row['bank_name'] . ')</option>';
                                                                        }
                                                                        ?>  
                                                                    </select>
                                                                    <span class="text-danger error-text" id="invoice_account_error"></span> 
                                                                </div>
                                                                <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                                                                    <div class="file-upload drag-file w-100 h-auto py-3 d-flex align-items-center justify-content-center flex-column">
                                                                        <span class="upload-img d-block"><i class="isax isax-image text-primary me-1"></i>Upload Documents</span>
                                                                        <input type="file" class="form-control" name="document[]" id="document-upload" multiple>
                                                                        <span id="file-count-label" class="mt-2 text-muted"></span>
                                                                    </div>
                                                                    <span id="document_error" class="text-danger error-text"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!-- end col -->
                                                <div class="col-lg-5">
                                                    <input type="hidden" name="sub_amount" id="subtotal-amount-field" value="0">
                                                    <input type="hidden" name="tax_amount" id="tax-amount-field" value="0">
                                                    <input type="hidden" name="total_amount" id="total-amount-field" value="0">

                                                    <div class="mb-3">
                                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                                            <h6 class="fs-14 fw-semibold">Subtotal</h6>
                                                            <h6 class="fs-14 fw-semibold"><span id="currency-symbol"><?php echo $companyCurrency['currency_symbol']; ?></span> <span id="subtotal-amount">0.00</span></h6>
                                                        </div>
                                                        <div class="tax-details">
                                                           
                                                        </div>
                                                        <div id="shipping-charge-group" class="d-flex align-items-center justify-content-between mb-3">
                                                            <h6 class="fs-14 fw-semibold mb-0">Shipping Charge</h6>
                                                            <div class="input-group" style="width: 150px;">
                                                                <span class="input-group-text currency-prefix"><?php echo $companyCurrency['currency_symbol']; ?></span>
                                                                <input type="text" class="form-control" id="shipping-charge" name="shipping_charge" value="0.00">
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                                            <h6>Total</h6>
                                                            <h6><span id="currency-symbol-total"><?php echo $companyCurrency['currency_symbol']; ?></span> <span id="total-amount">0.00</span></h6>
                                                        </div>
                                                    </div>
                                                </div><!-- end col -->
                                            </div>
											<!-- end row -->

                                        </div>

                                        <div class="d-flex align-items-center justify-content-between">
                                        <button type="button" class="btn btn-outline-white" onclick="window.location.href='invoices.php'">Cancel</button>
                                            <button type="submit" name="submit" class="btn btn-primary">Save</button>
                                        </div>
										
                                    </form>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

            </div>
            <!-- End Content -->

            <?php include 'layouts/footer.php'; ?>

        </div>

        <!-- ========================
			End Page Content
		========================= -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>
   <!-- Additional JS for datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
   <script>
$(document).ready(function() {
    console.log('Document ready - initializing...');

    // Initialize datepicker
    $('.datepicker').flatpickr({
        dateFormat: "Y-m-d",
        allowInput: true,
        defaultDate: new Date(),
        clickOpens: true
    });
    
    // Initialize select2
    $('.select2').select2({
        theme: 'bootstrap-5'
    });

    // === Allow only text (no digits) ===
    $('#reference_name').on('input', function () {
        this.value = this.value.replace(/[0-9]/g, '');
    });

    $('#shipping-charge').on('input', function () {
        let val = this.value.replace(/[^0-9.]/g, ''); 
        let parts = val.split('.');
        if (parts.length > 2) {
            val = parts[0] + '.' + parts[1];
        }
        this.value = val;
    });

    // Document upload functionality
    $('#document-upload').on('change', function () {
        let files = $(this)[0].files;
        if (files.length === 0) {
            $('#file-count-label').text('');
        } else if (files.length === 1) {
            $('#file-count-label').text(files[0].name);
        } else {
            $('#file-count-label').text(`${files.length} files selected`);
        }
    });

    // =============================================
    // GST Type Determination based on Location
    // =============================================
    function determineGSTType(clientId) {
        if (!clientId) {
            // Reset to default (non_gst)
            setGSTMode('non_gst');
            return;
        }
        
        $.ajax({
            url: 'process/check_gst_type.php',
            type: 'POST',
            data: { 
                client_id: clientId,
                org_id: <?= $currentOrgId ?>
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const taxType = response.gst_type || 'non_gst';
                    
                    // Update GST radio button based on tax applicability
                    if (taxType === 'non_gst') {
                        $('#gst-none').prop('checked', true);
                        setGSTMode('non_gst');
                    } else {
                        // If any GST is applicable (cgst_sgst or igst)
                        $('#gst-manual').prop('checked', true);
                        setGSTMode(taxType);
                    }
                } else {
                    // Default to non-gst on error
                    $('#gst-none').prop('checked', true);
                    setGSTMode('non_gst');
                }
            },
            error: function(xhr, status, error) {
                console.error('GST determination error:', error);
                // Default to non-gst on error
                $('#gst-none').prop('checked', true);
                setGSTMode('non_gst');
            }
        });
    }

    function setGSTMode(taxType) {
        console.log('Setting GST mode to:', taxType);
        
        // Update both fields correctly
        if (taxType === 'non_gst') {
            // For non-gst: both fields should be 'non_gst'
            $('#gst_type_field').val('non_gst');
            $('#tax_type_field').val('non_gst');
        } else {
            // For GST modes: gst_type='gst', tax_type=specific type
            $('#gst_type_field').val('gst');
            $('#tax_type_field').val(taxType); // 'cgst_sgst' or 'igst'
        }
        
        // Hide all tax columns first
        $('.tax-column').hide();
        $('.tax-details').hide();
        
        // Reset all tax values to 0
        $('.tax-rate').data('value', 0).val('0%');
        $('.service-tax-select').val('');
        $('.product-tax-select').val('');
        $('.tax-amount-line').text(getCurrencySymbol() + ' 0.00');
        $('.tax-rate-line').text('0%');
        
        if (taxType === 'non_gst') {
            $('.add-table').addClass('non-gst-mode');
            // Hide tax columns
            $('.tax-column').hide();
            $('.tax-details').hide();
        } else {
            $('.add-table').removeClass('non-gst-mode');
            
            // Show tax columns for GST modes
            $('.tax-column').show();
            $('.tax-details').show();
            
            // Recalculate with appropriate tax rates
            $('.product-select').each(function() {
                const $row = $(this).closest('tr');
                const option = $(this).find('option:selected');
                if (option.val()) {
                    const baseTax = parseFloat(option.data('tax')) || 0;
                    let effectiveTax = baseTax;
                    
                    // Split tax for CGST+SGST (each half of total GST)
                    if (taxType === 'cgst_sgst' && baseTax > 0) {
                        effectiveTax = baseTax; // Total GST (CGST+SGST combined)
                    }
                    
                    $row.find('.tax-rate').data('value', effectiveTax).val(formatPercent(effectiveTax));
                }
            });
            
            $('.service-select').each(function() {
                const $row = $(this).closest('tr');
                const option = $(this).find('option:selected');
                if (option.val()) {
                    const baseTax = parseFloat(option.data('tax')) || 0;
                    let effectiveTax = baseTax;
                    
                    if (taxType === 'cgst_sgst' && baseTax > 0) {
                        effectiveTax = baseTax; // Total GST (CGST+SGST combined)
                    }
                    
                    $row.find('.tax-rate').data('value', effectiveTax).val(formatPercent(effectiveTax));
                }
            });
        }
        
        // Recalculate all rows
        $('.add-tbody tr').each(function() {
            calculateRow($(this));
        });
        
        calculateSummary();
    }

    // Update the client change handler to include GST determination
    $('#client_id').on('change', function() {
        const clientId = $(this).val();
        
        // Existing code for fetching client info
        if (clientId) {
            $.ajax({
                url: 'process/fetch_client_full_info.php',
                type: 'POST',
                data: { 
                    client_id: clientId,
                    current_user_id: <?= $currentUserId ?>,
                    current_org_id: <?= $currentOrgId ?>,
                    user_role_id: <?= $userRoleId ?>
                },
                dataType: 'json',
                success: response => {
                    $('#client_info_block').html(response.billing_html);
                    $('#shipping_info_block').html(response.shipping_html);
                    
                    // Determine GST type based on location
                    determineGSTType(clientId);
                },
                error: function() {
                    $('#client_info_block, #shipping_info_block').empty();
                    determineGSTType(null);
                }
            });
        } else {
            $('#client_info_block, #shipping_info_block').empty();
            // Reset to non_gst when no client selected
            setGSTMode('non_gst');
            $('#gst-none').prop('checked', true);
        }
    });

    // Manual GST toggle handling
    $('input[name="gst_type_radio"]').on('change', function() {
        const radioValue = $(this).val();
        const clientId = $('#client_id').val();
        
        if (radioValue === 'gst') {
            // Manual GST mode selected
            if (clientId) {
                // Check if GST is applicable for this client
                $.ajax({
                    url: 'process/check_gst_type.php',
                    type: 'POST',
                    data: { 
                        client_id: clientId,
                        org_id: <?= $currentOrgId ?>
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.gst_type !== 'non_gst') {
                            // GST is applicable, use the determined type
                            const taxType = response.gst_type;
                            setGSTMode(taxType);
                        } else {
                            // GST not applicable, but user manually selected GST
                            // Default to igst for manual GST mode
                            setGSTMode('igst');
                        }
                    },
                    error: function() {
                        // Default to igst on error for manual GST mode
                        setGSTMode('igst');
                    }
                });
            } else {
                // No client selected, default to igst for manual GST
                setGSTMode('igst');
            }
        } else if (radioValue === 'non_gst') {
            // Manual override to non-GST
            setGSTMode('non_gst');
        }
    });

    // =============================================
    // Product/Service Dropdown Validation
    // =============================================
    function validateProductServiceDropdowns() {
        let isValid = true;
        let firstErrorRow = null;
        
        // Clear all previous error messages and styling
        $('.product-service-error').hide();
        $('.product-select, .service-select').removeClass('is-invalid-dropdown');
        $('.service-name-input').removeClass('is-invalid');
        
        // Check each row
        $('.add-tbody tr').each(function(index) {
            const $row = $(this);
            const isProductRow = $row.hasClass('product-row');
            const isServiceRow = $row.hasClass('service-row');
            
            let hasError = false;
            let errorMessage = '';
            
            if (isProductRow) {
                const $productSelect = $row.find('.product-select');
                const productValue = $productSelect.val();
                
                if (!productValue) {
                    hasError = true;
                    errorMessage = 'Please select a product';
                    $productSelect.addClass('is-invalid-dropdown');
                    
                    if (firstErrorRow === null) {
                        firstErrorRow = $row;
                    }
                }
            } else if (isServiceRow) {
                const $serviceSelect = $row.find('.service-select');
                const $serviceNameInput = $row.find('.service-name-input');
                const serviceValue = $serviceSelect.val();
                const serviceNameValue = $serviceNameInput.val();
                
                // Check if both are empty
                if (!serviceValue && !serviceNameValue) {
                    hasError = true;
                    errorMessage = 'Please select a service or enter a custom service name';
                    $serviceSelect.addClass('is-invalid-dropdown');
                    $serviceNameInput.addClass('is-invalid');
                    
                    if (firstErrorRow === null) {
                        firstErrorRow = $row;
                    }
                }
            }
            
            // Show error message if there's an error
            if (hasError) {
                $row.find('.product-service-error').text(errorMessage).show();
                isValid = false;
            }
        });
        
        return {
            isValid: isValid,
            firstErrorRow: firstErrorRow
        };
    }

    // Get currency symbol from PHP
    function getCurrencySymbol() {
        return '<?php echo $companyCurrency['currency_symbol']; ?>';
    }

    // Format currency for display
    function formatCurrency(value) {
        const n = parseFloat(value);
        if (isNaN(n)) return '';
        const symbol = getCurrencySymbol();
        return `${symbol} ${n.toFixed(2)}`;
    }

    function formatPercent(value) {
        const n = parseFloat(value);
        if (isNaN(n)) return '';
        return `${n.toFixed(2)}%`;
    }

    function unformat(value) {
        if (value === undefined || value === null) return 0;
        const n = parseFloat(String(value).replace(/[^0-9.-]/g, ''));
        return isNaN(n) ? 0 : n;
    }

    // =============================================
    // Project and Task Selection (EXISTING LOGIC - PRESERVED)
    // =============================================
    // When client changes
    $('#client_id').on('change', function() {
        const clientId = $(this).val();
        
        if (clientId) {
            // Enable and load projects
            $('#project_id').prop('disabled', false).html('<option value="">Loading projects...</option>');
            $('#task_id').prop('disabled', true).html('<option value="">Select Tasks</option>');
            
            $.ajax({
                url: 'process/get_projects_by_client.php',
                type: 'POST',
                data: { 
                    client_id: clientId,
                    current_user_id: <?= $currentUserId ?>,
                    current_org_id: <?= $currentOrgId ?>,
                    user_role_id: <?= $userRoleId ?>
                },
                success: function(data) {
                    $('#project_id').html(data);
                },
                error: function() {
                    $('#project_id').html('<option value="">Error loading projects</option>');
                }
            });
        } else {
            $('#project_id, #task_id').prop('disabled', true);
            $('#project_id').html('<option value="">Select Project</option>');
            $('#task_id').html('<option value="">Select Tasks</option>');
        }
    });

    // When project changes
    $('#project_id').on('change', function() {
        const projectId = $(this).val();
        
        if (projectId) {
            // Enable and load tasks
            $('#task_id').prop('disabled', false).html('<option value="">Loading tasks...</option>');
            
            $.ajax({
                url: 'process/get_tasks_by_project.php',
                type: 'POST',
                data: { 
                    project_id: projectId,
                    current_user_id: <?= $currentUserId ?>,
                    current_org_id: <?= $currentOrgId ?>,
                    user_role_id: <?= $userRoleId ?>
                },
                success: function(data) {
                    $('#task_id').html(data);
                    // Initialize select2 for multi-select
                    $('#task_id').select2({
                        placeholder: "Select Tasks",
                        allowClear: true
                    });
                },
                error: function() {
                    $('#task_id').html('<option value="">Error loading tasks</option>');
                }
            });
        } else {
            $('#task_id').prop('disabled', true).html('<option value="">Select Tasks</option>');
        }
    });

    // When tasks are selected
    $('#task_id').on('change', function() {
        const selectedTasks = $(this).val();
        
        if (selectedTasks && selectedTasks.length > 0) {
            // Clear existing rows first
            $('.add-tbody').empty();
            
            // Set item type to Service when tasks are selected
            $('#Radio-service').prop('checked', true).trigger('change');
            
            // Load details for each selected task
            selectedTasks.forEach(function(taskId) {
                if (taskId) {
                    $.ajax({
                        url: 'process/get_task_details.php',
                        type: 'POST',
                        data: { 
                            task_id: taskId,
                            current_user_id: <?= $currentUserId ?>,
                            current_org_id: <?= $currentOrgId ?>,
                            user_role_id: <?= $userRoleId ?>
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Add task as an invoice item using your existing row structure
                                const rowId = 'row_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                                const currentTaxType = $('#tax_type_field').val();
                                const isNonGST = currentTaxType === 'non_gst';
                                const currencySymbol = getCurrencySymbol();
                                const newRow = `
                                <tr id="${rowId}" class="service-row">
                                    <td>
                                        <div class="service-fields">
                                            <input type="text" class="form-control service-name-input" value="${response.task_name}" readonly>
                                            <input type="hidden" name="item_id[]" value="${taskId}">
                                            <input type="hidden" class="tax-id" name="tax_id[]" value="0">
                                            <input type="hidden" class="tax-name" name="tax_name[]" value="">
                                            <!-- Hidden field to track item type for this row -->
                                            <input type="hidden" name="item_type_row[]" value="service">
                                            <div class="product-service-error"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control quantity service-quantity" name="quantity[]" value="${response.hours}" min="1" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control hsn-code" name="code[]" value="" readonly>
                                    </td>
                                    <td>
                                        <div class="service-fields">
                                            <!-- Store plain number for form submission -->
                                            <input type="text" class="form-control service-price-input" name="selling_price[]" value="${response.rate_per_hour}" data-value="${response.rate_per_hour}" readonly>
                                        </div>
                                    </td>
                                    <td class="tax-column" style="${isNonGST ? 'display: none;' : ''}">
                                        <div class="service-fields">
                                            <input type="hidden" class="tax-rate" name="rate[]" value="0" data-value="0" readonly>
                                            <div class="tax-display-container">
                                                <div class="tax-amount-line">${currencySymbol} 0.00</div>
                                                <div class="tax-rate-line">0%</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <!-- Store plain number for form submission -->
                                        <input type="text" class="form-control amount" name="amount[]" value="${response.total_amount}" data-value="${response.total_amount}" readonly>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="remove-table"><i class="isax isax-trash text-danger"></i></a>
                                    </td>
                                </tr>`;
                                
                                $('.add-tbody').append(newRow);
                                
                                // Update summary
                                calculateSummary();
                            }
                        },
                        error: function() {
                            console.log('Error loading task details for task ID: ' + taskId);
                        }
                    });
                }
            });
        } else {
            // If no tasks selected, clear the items table
            $('.add-tbody').empty();
            calculateSummary();
        }
    });

    // Form validation with bank account tab auto-open
    $('#form').on('submit', function(e) {
        let isValid = true;
        $('.error-text').text('');

        if (!$('#client_id').val()) {
            $('#clientname_error').text('Client is required.');
            isValid = false;
        }
        if (!$('#invoice_date').val()) {
            $('#invoice_date_error').text('Invoice Date is required.');
            isValid = false;
        }
        if (!$('#due_date').val()) {
            $('#invoice_due_error').text('Due Date is required.');
            isValid = false;
        }
        if (!$('.add-tbody tr').length) {
            $('#product_error').text('Please add at least one product or service');
            isValid = false;
        }

        // ADDED: Product/Service dropdown validation
        const dropdownValidation = validateProductServiceDropdowns();
        if (!dropdownValidation.isValid) {
            isValid = false;
            // Scroll to the first row with error
            if (dropdownValidation.firstErrorRow) {
                $('html, body').animate({
                    scrollTop: dropdownValidation.firstErrorRow.offset().top - 150
                }, 500);
            }
        }

        // ADDED: Bank account validation
        if (!$('#bank_id').val()) {
            $('#invoice_account_error').text('Account is required.');
            
            // Auto-open the Bank Details tab
            $('#bank-tab-link').tab('show');
            
            // Add a red border to highlight the required field
            $('#bank_id').addClass('is-invalid');
            
            isValid = false;
        } else {
            $('#invoice_account_error').text('');
            $('#bank_id').removeClass('is-invalid');
        }

        if (!isValid) {
            e.preventDefault();
            
            // Scroll to the first error
            let firstError = $('.error-text:visible').first();
            if (firstError.length) {
                $('html, body').animate({ 
                    scrollTop: firstError.offset().top - 100 
                }, 500);
            }
            
            // If bank error exists, scroll to bank tab
            if ($('#invoice_account_error').text()) {
                $('html, body').animate({ 
                    scrollTop: $('#bank-tab-link').offset().top - 150 
                }, 500);
            }
        } else {
            // Before submitting, ensure all numeric fields have plain numbers (no formatting)
            $('.selling-price, .service-price-input, .amount').each(function() {
                const $input = $(this);
                const currentValue = $input.val();
                const plainNumber = unformat(currentValue);
                $input.val(plainNumber.toFixed(2));
            });
            
            // Also format shipping charge
            const shippingCharge = $('#shipping-charge');
            if (shippingCharge.length) {
                const plainShipping = unformat(shippingCharge.val());
                shippingCharge.val(plainShipping.toFixed(2));
            }
        }
    });

    // =============================================
    // Load products and services functions - FIXED
    // =============================================
    function loadProducts(target) {
        let productOptions = '<option value="">Select Product</option>';
        <?php foreach ($products as $product): ?>
        productOptions += `<option value="<?= $product['id'] ?>" 
                          data-price="<?= $product['selling_price'] ?>" 
                          data-hsn="<?= $product['code'] ?>"
                          data-tax="<?= $product['tax_rate'] ?>"
                          data-tax-id="<?= $product['tax_id'] ?>"
                          data-tax-name="<?= htmlspecialchars($product['tax_name'] ?? '', ENT_QUOTES) ?>">
                          <?= htmlspecialchars($product['name']) ?>
                          </option>`;
        <?php endforeach; ?>
        
        if (target) {
            target.html(productOptions);
        }
        updateProductDropdowns();
    }

    function loadServices(target) {
        let serviceOptions = '<option value="">Select Service</option>';
        <?php foreach ($services as $service): ?>
        serviceOptions += `<option value="<?= $service['id'] ?>" 
                          data-price="<?= $service['selling_price'] ?>" 
                          data-hsn="<?= $service['code'] ?>"
                          data-tax="<?= $service['tax_rate'] ?>"
                          data-tax-id="<?= $service['tax_id'] ?>"
                          data-tax-name="<?= htmlspecialchars($service['tax_name'] ?? '', ENT_QUOTES) ?>">
                          <?= htmlspecialchars($service['name']) ?>
                          </option>`;
        <?php endforeach; ?>
        
        if (target) {
            target.html(serviceOptions);
        }
        updateServiceDropdowns();
    }

    function updateProductDropdowns() {
        let selectedProducts = [];
        $('.product-select').each(function() {
            let val = $(this).val();
            if (val) selectedProducts.push(val);
        });

        $('.product-select').each(function() {
            let currentVal = $(this).val();
            $(this).find('option').each(function() {
                if ($(this).val() && selectedProducts.includes($(this).val()) && $(this).val() !== currentVal) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
    }

    function updateServiceDropdowns() {
        let selectedServices = [];
        $('.service-select').each(function() {
            let val = $(this).val();
            if (val) selectedServices.push(val);
        });

        $('.service-select').each(function() {
            let currentVal = $(this).val();
            $(this).find('option').each(function() {
                if ($(this).val() && selectedServices.includes($(this).val()) && $(this).val() !== currentVal) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
    }

    // =============================================
    // FIXED: Item type change handler - COMPLETELY RESETS ALL ROWS
    // =============================================
    $('input[name="item_type"]').on('change', function() {
        const itemType = $(this).val();
        console.log('Item type changed to:', itemType);
        
        // Remove ALL existing rows
        $('.add-tbody tr').remove();
        
        // Add one fresh row with the new item type
        addNewRow(itemType);
        
        calculateSummary();
    });

    // Helper function to add a new row with the specified item type
    function addNewRow(itemType) {
        const rowClass = itemType == 1 ? 'product-row' : 'service-row';
        const currentTaxType = $('#tax_type_field').val();
        const isNonGST = currentTaxType === 'non_gst';
        const currencySymbol = getCurrencySymbol();
        
        let taxOptions = '<option value="">Select Tax</option>';
        <?php foreach ($taxRates as $tax): ?>
        taxOptions += `<option value="<?= $tax['id'] ?>" data-rate="<?= $tax['rate'] ?>"><?= htmlspecialchars($tax['name']) ?> (<?= $tax['rate'] ?>%)</option>`;
        <?php endforeach; ?>

        let newRow = '';
        
        if (itemType == 1) {
            // Product row
            newRow = `
                <tr class="${rowClass}">
                    <td>
                        <select class="form-select product-select" name="item_id[]">
                            <option value="">Select Product</option>
                        </select>
                        <input type="hidden" class="tax-id" name="tax_id[]">
                        <input type="hidden" class="tax-name" name="tax_name[]">
                        <!-- Hidden field to track item type for this row -->
                        <input type="hidden" name="item_type_row[]" value="product">
                        <!-- ADDED: Error message container -->
                        <div class="product-service-error"></div>
                    </td>
                    <td>
                        <input type="number" class="form-control quantity" name="quantity[]" value="1" min="1">
                    </td>
                    <td>
                        <input type="text" class="form-control hsn-code" name="code[]" readonly>
                    </td>
                    <td>
                        <!-- Store plain number for form submission -->
                        <input type="text" class="form-control selling-price" name="selling_price[]" value="0.00" data-value="0">
                    </td>
                    <td class="tax-column" style="${isNonGST ? 'display: none;' : ''}">
                        <!-- Tax dropdown for products -->
                        <select class="form-select product-tax-select" name="tax_id[]">
                            ${taxOptions}
                        </select>
                        <input type="hidden" class="tax-rate" name="rate[]" data-value="${isNonGST ? '0' : '0'}">
                        <input type="hidden" class="tax-name" name="tax_name[]" value="">
                        <div class="tax-display-container mt-2">
                            <div class="tax-amount-line">${currencySymbol} 0.00</div>
                            <div class="tax-rate-line">0%</div>
                        </div>
                    </td>
                    <td>
                        <!-- Store plain number for form submission -->
                        <input type="text" class="form-control amount" name="amount[]" value="0.00" data-value="0" readonly>
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="remove-table"><i class="isax isax-trash"></i></a>
                    </td>
                </tr>
            `;
        } else {
            // Service row - FIXED: Quantity remains optional (empty by default)
            newRow = `
                <tr class="${rowClass}">
                    <td>
                        <select class="form-select service-select" name="item_id[]">
                            <option value="">Select Service</option>
                        </select>
                        <input type="text" class="form-control service-name-input service-custom-input" name="service_name[]" placeholder="Or enter custom service name">
                        <input type="hidden" class="tax-id" name="tax_id[]">
                        <input type="hidden" class="tax-name" name="tax_name[]">
                        <!-- Hidden field to track item type for this row -->
                        <input type="hidden" name="item_type_row[]" value="service">
                        <!-- ADDED: Error message container -->
                        <div class="product-service-error"></div>
                    </td>
                    <td>
                        <input type="number" class="form-control quantity service-quantity" name="quantity[]" value="" placeholder="Optional">
                    </td>
                    <td>
                        <input type="text" class="form-control hsn-code" name="code[]" readonly>
                    </td>
                    <td>
                        <!-- Store plain number for form submission -->
                        <input type="text" class="form-control service-price-input" name="selling_price[]" value="0.00" data-value="0" placeholder="0.00">
                    </td>
                    <td class="tax-column" style="${isNonGST ? 'display: none;' : ''}">
                        <select class="form-select service-tax-select" name="tax_id[]">
                            ${taxOptions}
                        </select>
                        <input type="hidden" class="tax-rate" name="rate[]" data-value="${isNonGST ? '0' : '0'}">
                        <input type="hidden" class="tax-name" name="tax_name[]" value="">
                        <div class="tax-display-container mt-2">
                            <div class="tax-amount-line">${currencySymbol} 0.00</div>
                            <div class="tax-rate-line">0%</div>
                        </div>
                    </td>
                    <td>
                        <!-- Store plain number for form submission -->
                        <input type="text" class="form-control amount" name="amount[]" value="0.00" data-value="0" readonly>
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="remove-table"><i class="isax isax-trash"></i></a>
                    </td>
                </tr>
            `;
        }
        
        $('.add-tbody').append(newRow);
        
        if (itemType == 1) {
            const $productSelect = $('.add-tbody tr:last .product-select');
            loadProducts($productSelect);
        } else {
            const $serviceSelect = $('.add-tbody tr:last .service-select');
            loadServices($serviceSelect);
        }
        
        updateProductDropdowns();
        updateServiceDropdowns();
    }

    // Format behaviors - FIXED: Keep plain numbers in inputs
    function attachCurrencyBehavior(selector, onChangeCallback) {
        $(document).on('focus', selector, function(){
            const raw = $(this).data('value');
            $(this).val(raw !== undefined ? raw : unformat($(this).val()));
        });
        $(document).on('blur', selector, function(){
            const num = unformat($(this).val());
            $(this).data('value', num).val(num.toFixed(2));
            if (onChangeCallback) onChangeCallback($(this));
        });
        $(document).on('input', selector, function(){
            const num = unformat($(this).val());
            $(this).data('value', num);
            if (onChangeCallback) onChangeCallback($(this));
        });
    }

    function attachPercentBehavior(selector, onChangeCallback) {
        $(document).on('focus', selector, function(){
            const raw = $(this).data('value');
            $(this).val(raw !== undefined ? raw : unformat($(this).val()));
        });
        $(document).on('blur', selector, function(){
            const num = unformat($(this).val());
            $(this).data('value', num).val(formatPercent(num));
            if (onChangeCallback) onChangeCallback($(this));
        });
        $(document).on('input', selector, function(){
            if (onChangeCallback) onChangeCallback($(this));
        });
    }

    // FIXED: Attach event handlers properly
    attachCurrencyBehavior('.selling-price', function($el){
        calculateRow($el.closest('tr'));
    });
    
    // Service price input handling
    $(document).on('input', '.service-price-input', function() {
        const $row = $(this).closest('tr');
        const price = unformat($(this).val());
        $(this).data('value', price);
        calculateRow($row);
    });
    
    $(document).on('blur', '.service-price-input', function() {
        const $row = $(this).closest('tr');
        const price = unformat($(this).val());
        $(this).data('value', price).val(price.toFixed(2));
        calculateRow($row);
    });
    
    attachPercentBehavior('.tax-rate', function($el){
        calculateRow($el.closest('tr'));
    });
    
    attachCurrencyBehavior('#shipping-charge', function(){
        calculateSummary();
    });

    // Initialize shipping field
    (function initShipping(){
        const $ship = $('#shipping-charge');
        if ($ship.length) {
            const initVal = unformat($ship.val());
            $ship.data('value', initVal);
            if ($ship.attr('type') !== 'number') {
                $ship.val(initVal.toFixed(2));
            } else {
                $ship.val(initVal.toFixed(2));
            }
        }
    })();

    // Item events - UPDATED: Added validation error clearing
    $(document).on('change', '.product-select', function() {
        const $row = $(this).closest('tr');
        
        // Clear validation error
        $row.find('.product-service-error').hide();
        $(this).removeClass('is-invalid-dropdown');
        
        const option = $(this).find('option:selected');

        if (option.val()) {
            const price = parseFloat(option.data('price')) || 0;
            const hsnCode = option.data('hsn') || '';
            const tax = parseFloat(option.data('tax')) || 0;
            const taxId = option.data('tax-id') || '';
            const taxName = option.data('tax-name') || '';

            $row.find('.hsn-code').val(hsnCode);
            $row.find('.tax-id').val(taxId);
            $row.find('.tax-name').val(taxName);
            
            const currentTaxType = $('#tax_type_field').val();
            const isNonGST = currentTaxType === 'non_gst';
            const effectiveTax = isNonGST ? 0 : tax;
            
            // Store plain number in input
            $row.find('.selling-price').data('value', price).val(price.toFixed(2));
            $row.find('.tax-rate').data('value', effectiveTax).val(formatPercent(effectiveTax));

            // Update tax display
            const currencySymbol = getCurrencySymbol();
            $row.find('.tax-amount-line').text(currencySymbol + ' 0.00');
            $row.find('.tax-rate-line').text(effectiveTax + '%');

            // Set the tax dropdown for products
            if (taxId && !isNonGST) {
                $row.find('.product-tax-select').val(taxId).trigger('change');
            }

            calculateRow($row);
        } else {
            resetRow($row);
        }

        updateProductDropdowns();
    });

    // Service select change handler - UPDATED: Added validation error clearing
    $(document).on('change', '.service-select', function() {
        const $row = $(this).closest('tr');
        
        // Clear validation errors
        $row.find('.product-service-error').hide();
        $(this).removeClass('is-invalid-dropdown');
        $row.find('.service-name-input').removeClass('is-invalid');
        
        const option = $(this).find('option:selected');

        if (option.val()) {
            const price = parseFloat(option.data('price')) || 0;
            const hsnCode = option.data('hsn') || '';
            const tax = parseFloat(option.data('tax')) || 0;
            const taxId = option.data('tax-id') || '';
            const taxName = option.data('tax-name') || '';

            $row.find('.hsn-code').val(hsnCode);
            $row.find('.tax-id').val(taxId);
            $row.find('.tax-name').val(taxName);
            
            const currentTaxType = $('#tax_type_field').val();
            const isNonGST = currentTaxType === 'non_gst';
            const effectiveTax = isNonGST ? 0 : tax;
            
            // Store plain number
            $row.find('.service-price-input').data('value', price).val(price.toFixed(2));
            $row.find('.tax-rate').data('value', effectiveTax).val(formatPercent(effectiveTax));
            
            // Update tax display
            const currencySymbol = getCurrencySymbol();
            $row.find('.tax-amount-line').text(currencySymbol + ' 0.00');
            $row.find('.tax-rate-line').text(effectiveTax + '%');
            
            if (taxId && !isNonGST) {
                $row.find('.service-tax-select').val(taxId).trigger('change');
            }

            calculateRow($row);
        } else {
            $row.find('.hsn-code').val('');
            $row.find('.tax-id').val('');
            $row.find('.tax-name').val('');
            $row.find('.service-price-input').val('0.00').data('value', 0);
            $row.find('.tax-rate').val('').removeData('value');
            $row.find('.amount').val('0.00').removeData('value');
            const currencySymbol = getCurrencySymbol();
            $row.find('.tax-amount-line').text(currencySymbol + ' 0.00');
            $row.find('.tax-rate-line').text('0%');
            calculateSummary();
        }

        updateServiceDropdowns();
    });

    // FIXED: Added product tax select change handler
    $(document).on('change', '.product-tax-select', function() {
        const $row = $(this).closest('tr');
        const selectedOption = $(this).find('option:selected');
        const taxRate = parseFloat(selectedOption.data('rate')) || 0;
        const taxId = selectedOption.val();
        const taxName = selectedOption.text().split(' (')[0];

        const currentTaxType = $('#tax_type_field').val();
        const isNonGST = currentTaxType === 'non_gst';
        const effectiveTax = isNonGST ? 0 : taxRate;
        
        $row.find('.tax-rate').data('value', effectiveTax).val(formatPercent(effectiveTax));
        $row.find('.tax-id').val(taxId);
        $row.find('.tax-name').val(taxName);
        calculateRow($row);
    });

    $(document).on('change', '.service-tax-select', function() {
        const $row = $(this).closest('tr');
        const selectedOption = $(this).find('option:selected');
        const taxRate = parseFloat(selectedOption.data('rate')) || 0;
        const taxId = selectedOption.val();
        const taxName = selectedOption.text().split(' (')[0];

        const currentTaxType = $('#tax_type_field').val();
        const isNonGST = currentTaxType === 'non_gst';
        const effectiveTax = isNonGST ? 0 : taxRate;
        
        $row.find('.tax-rate').data('value', effectiveTax).val(formatPercent(effectiveTax));
        $row.find('.tax-id').val(taxId);
        $row.find('.tax-name').val(taxName);
        calculateRow($row);
    });

    // Service name input - UPDATED: Added validation error clearing
    $(document).on('input', '.service-name-input', function() {
        const $row = $(this).closest('tr');
        const $serviceSelect = $row.find('.service-select');
        
        // Clear validation error when user types
        $row.find('.product-service-error').hide();
        $(this).removeClass('is-invalid');
        $serviceSelect.removeClass('is-invalid-dropdown');
        
        if ($serviceSelect.val() === '') {
            $row.find('.hsn-code').val('');
        }
        
        calculateRow($row);
    });

    // FIXED: Added direct input event for selling-price to handle real-time changes
    $(document).on('input', '.selling-price', function() {
        const $row = $(this).closest('tr');
        const price = unformat($(this).val());
        $row.find('.selling-price').data('value', price);
        calculateRow($row);
    });

    $(document).on('input', '.quantity', function() {
        calculateRow($(this).closest('tr'));
    });

    $(document).on('click', '.remove-table', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        calculateSummary();
        updateProductDropdowns();
        updateServiceDropdowns();
    });

    // Calculations - FIXED: Enhanced to properly handle optional service quantity
    function calculateRow($row) {
        const qtyInput = $row.find('.quantity');
        let qty = unformat(qtyInput.val());
        
        const isService = $row.hasClass('service-row');
        
        // FIXED: Service quantity remains optional - if empty or 0, use 1 for calculation
        if (isService && (qty === 0 || qtyInput.val() === '')) {
            qty = 1; // Use 1 for calculation but keep display empty
        }
        
        let price = 0;
        if (isService) {
            price = $row.find('.service-price-input').data('value') || 0;
            // If data value is not set, try to get from the input value
            if (price === 0) {
                price = unformat($row.find('.service-price-input').val()) || 0;
                $row.find('.service-price-input').data('value', price);
            }
        } else {
            price = $row.find('.selling-price').data('value') || 0;
            // If data value is not set, try to get from the input value
            if (price === 0) {
                price = unformat($row.find('.selling-price').val()) || 0;
                $row.find('.selling-price').data('value', price);
            }
        }
        
        const taxRate = $row.find('.tax-rate').data('value') || 0;

        const lineSubtotal = qty * price;
        const lineTaxAmount = lineSubtotal * (taxRate / 100);
        const lineTotal = lineSubtotal + lineTaxAmount;

        const currencySymbol = getCurrencySymbol();
        const taxAmountFormatted = currencySymbol + ' ' + lineTaxAmount.toFixed(2);
        const taxRateFormatted = `${taxRate}%`;
        
        $row.find('.tax-amount-line').text(taxAmountFormatted);
        $row.find('.tax-rate-line').text(taxRateFormatted);
        
        // Store plain number in input value
        $row.find('.amount').data('value', lineTotal).val(lineTotal.toFixed(2));
        
        calculateSummary();
    }

    function getShippingCharge() {
        const $ship = $('#shipping-charge');
        if (!$ship.length) return 0;
        const stored = $ship.data('value');
        if (stored !== undefined) return parseFloat(stored) || 0;
        return unformat($ship.val());
    }

    function calculateSummary() {
        let sub = 0, taxGroups = {}, grandTotal = 0;
        const currencySymbol = getCurrencySymbol();
        const currentTaxType = $('#tax_type_field').val();

        $('.add-tbody tr').each(function() {
            let p = 0;
            const isService = $(this).hasClass('service-row');
            
            if (isService) {
                p = $(this).find('.service-price-input').data('value') || 0;
                if (p === 0) {
                    p = unformat($(this).find('.service-price-input').val()) || 0;
                }
            } else {
                p = $(this).find('.selling-price').data('value') || 0;
                if (p === 0) {
                    p = unformat($(this).find('.selling-price').val()) || 0;
                }
            }
            
            const qtyInput = $(this).find('.quantity');
            let q = unformat(qtyInput.val());
            
            // FIXED: Service quantity remains optional - if empty or 0, use 1 for calculation
            if (isService && (q === 0 || qtyInput.val() === '')) {
                q = 1;
            }
            
            const t = $(this).find('.tax-rate').data('value') || 0;
            const taxName = $(this).find('.tax-name').val() || (currentTaxType === 'igst' ? 'IGST' : 'GST');

            const lineSubtotal = p * q;
            const lineTaxAmount = (lineSubtotal * t / 100);
            const lineTotal = lineSubtotal + lineTaxAmount;

            sub += lineSubtotal;
            grandTotal += lineTotal;

            if (t > 0) {
                // Handle CGST+SGST split display
                if (currentTaxType === 'cgst_sgst') {
                    const cgstAmount = lineTaxAmount / 2;
                    const sgstAmount = lineTaxAmount / 2;
                    
                    const cgstKey = `CGST (${(t/2).toFixed(2)}%)`;
                    const sgstKey = `SGST (${(t/2).toFixed(2)}%)`;
                    
                    if (!taxGroups[cgstKey]) taxGroups[cgstKey] = 0;
                    if (!taxGroups[sgstKey]) taxGroups[sgstKey] = 0;
                    
                    taxGroups[cgstKey] += cgstAmount;
                    taxGroups[sgstKey] += sgstAmount;
                } else {
                    const taxKey = `${taxName} (${t}%)`;
                    if (!taxGroups[taxKey]) taxGroups[taxKey] = 0;
                    taxGroups[taxKey] += lineTaxAmount;
                }
            }
        });

        const shippingCharge = getShippingCharge();
        let taxHtml = "";

        const isNonGST = currentTaxType === 'non_gst';
        if (!isNonGST) {
            // Display tax breakdown based on GST type
            if (currentTaxType === 'cgst_sgst') {
                // Show CGST and SGST separately
                Object.keys(taxGroups).forEach(taxName => {
                    const taxAmount = taxGroups[taxName];
                    taxHtml += `
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fs-14 fw-semibold">${taxName}</h6>
                            <h6 class="fs-14 fw-semibold">${currencySymbol} ${taxAmount.toFixed(2)}</h6>
                        </div>`;
                });
            } else if (currentTaxType === 'igst') {
                // Show IGST for inter-state transactions
                // Group all IGST taxes together
                let totalIGST = 0;
                const igstRates = {};
                
                $('.add-tbody tr').each(function() {
                    const t = $(this).find('.tax-rate').data('value') || 0;
                    if (t > 0) {
                        let p = 0;
                        const isService = $(this).hasClass('service-row');
                        
                        if (isService) {
                            p = $(this).find('.service-price-input').data('value') || 0;
                            if (p === 0) p = unformat($(this).find('.service-price-input').val()) || 0;
                        } else {
                            p = $(this).find('.selling-price').data('value') || 0;
                            if (p === 0) p = unformat($(this).find('.selling-price').val()) || 0;
                        }
                        
                        const qtyInput = $(this).find('.quantity');
                        let q = unformat(qtyInput.val());
                        
                        if ($(this).hasClass('service-row') && (q === 0 || qtyInput.val() === '')) {
                            q = 1;
                        }
                        
                        const lineSubtotal = p * q;
                        const lineTaxAmount = (lineSubtotal * t / 100);
                        
                        if (!igstRates[t]) igstRates[t] = 0;
                        igstRates[t] += lineTaxAmount;
                        totalIGST += lineTaxAmount;
                    }
                });
                
                // Display each IGST rate separately
                Object.keys(igstRates).forEach(rate => {
                    if (igstRates[rate] > 0) {
                        taxHtml += `
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fs-14 fw-semibold">IGST (${parseFloat(rate)}%)</h6>
                                <h6 class="fs-14 fw-semibold">${currencySymbol} ${igstRates[rate].toFixed(2)}</h6>
                            </div>`;
                    }
                });
            } else {
                // For other GST types or manual GST
                $('.add-tbody tr').each(function(index) {
                    let p = 0;
                    const isService = $(this).hasClass('service-row');
                    
                    if (isService) {
                        p = $(this).find('.service-price-input').data('value') || 0;
                        if (p === 0) {
                            p = unformat($(this).find('.service-price-input').val()) || 0;
                        }
                    } else {
                        p = $(this).find('.selling-price').data('value') || 0;
                        if (p === 0) {
                            p = unformat($(this).find('.selling-price').val()) || 0;
                        }
                    }
                    
                    const qtyInput = $(this).find('.quantity');
                    let q = unformat(qtyInput.val());
                    
                    const isServiceRow = $(this).hasClass('service-row');
                    if (isServiceRow && (q === 0 || qtyInput.val() === '')) {
                        q = 1;
                    }
                    
                    const t = $(this).find('.tax-rate').data('value') || 0;
                    const taxName = $(this).find('.tax-name').val() || 'GST';

                    const lineSubtotal = p * q;
                    const lineTaxAmount = (lineSubtotal * t / 100);

                    if (t > 0 && lineTaxAmount > 0) {
                        const taxLabel = `${taxName} (${t}%)`;
                        taxHtml += `
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fs-14 fw-semibold">${taxLabel}</h6>
                                <h6 class="fs-14 fw-semibold">${currencySymbol} ${lineTaxAmount.toFixed(2)}</h6>
                            </div>`;
                    }
                });
            }
        }

        $('.tax-details').html(taxHtml);

        const totalAll = grandTotal + shippingCharge;

        $('#subtotal-amount').text(sub.toFixed(2));
        $('#total-amount').text(totalAll.toFixed(2));

        $('#subtotal-amount-field').val(sub.toFixed(2));
        $('#tax-amount-field').val(Object.values(taxGroups).reduce((a,b)=>a+b,0).toFixed(2));
        $('#total-amount-field').val(totalAll.toFixed(2));
    }
    
    function resetRow($row) {
        const isService = $row.hasClass('service-row');
        $row.find('.quantity').val(isService ? '' : '1').removeClass('service-quantity');
        $row.find('.hsn-code, .selling-price, .tax-rate, .amount, .service-name-input, .service-price-input').val('').removeData('value');
        $row.find('.tax-id').val('');
        const currencySymbol = getCurrencySymbol();
        $row.find('.tax-amount-line').text(currencySymbol + ' 0.00');
        $row.find('.tax-rate-line').text('0%');
        calculateSummary();
    }

    // =============================================
    // Add New button - creates one row based on current selection
    // =============================================
    
    // Remove ALL possible handlers
    $(document).off('click', '.add-invoice-data');
    $('body').off('click', '.add-invoice-data');
    $('.add-invoice-data').off('click');
    
    // Use a flag to prevent multiple executions
    let isAddingRow = false;
    
    $('body').on('click', '.add-invoice-data', function(e) {
        e.preventDefault();
        
        // Prevent multiple simultaneous clicks
        if (isAddingRow) {
            console.log('Already adding a row, please wait...');
            return false;
        }
        
        isAddingRow = true;
        
        console.log('Add New clicked - adding ONE row only');
        
        const itemType = $('input[name="item_type"]:checked').val();
        addNewRow(itemType);
        
        // Reset flag after a short delay
        setTimeout(() => {
            isAddingRow = false;
        }, 100);
    });

    // Initial setup - add one empty row on page load
    const initialItemType = $('input[name="item_type"]:checked').val();
    addNewRow(initialItemType);
    
    updateProductDropdowns();
    updateServiceDropdowns();
    calculateSummary();
    
    console.log('Initialization complete - GST functionality added to invoice');
});
</script>
</body>
</html>