<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'] ?? 'success';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>
<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

// Get company currency
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

// Validate and sanitize the ID
$quotation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($quotation_id <= 0) {
    $_SESSION['error'] = "Invalid quotation ID.";
    header("Location: quotations.php");
    exit();
}

// Fetch quotation data with organization filtering
$query = "SELECT * FROM quotation WHERE id = $quotation_id";
if ($currentOrgId > 0) {
    $query .= " AND org_id = $currentOrgId";
}
$result = mysqli_query($conn, $query);

// Check if quotation exists
if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Quotation not found.";
    header("Location: quotations.php");
    exit();
}

$row = mysqli_fetch_assoc($result);

// Fetch dropdown data with organization filtering and role-based access
$clients_query = "SELECT id, salutation, first_name, last_name, company_name FROM client WHERE is_deleted = 0";
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
$clients = mysqli_query($conn, $clients_query);

$users_query = "SELECT login.id, login.name FROM login
        JOIN user_role ON login.role_id = user_role.id
        WHERE login.is_deleted = 0";
if ($currentOrgId > 0) {
    $users_query .= " AND login.org_id = $currentOrgId";
}
$users_query .= " ORDER BY login.name ASC";
$users = mysqli_query($conn, $users_query);

$projects_query = "SELECT id, project_name FROM project WHERE is_deleted = 0";
if ($currentOrgId > 0) {
    $projects_query .= " AND org_id = $currentOrgId";
}
// Add role-based filtering for non-admin users
if ($userRoleId != 1) {
    $projects_query .= " AND (user_id = $currentUserId OR EXISTS (
        SELECT 1 FROM login u 
        WHERE u.id = project.user_id 
        AND u.role_id = 1 
        AND u.org_id = $currentOrgId
    ))";
}
$projects_query .= " ORDER BY project_name ASC";
$projects = mysqli_query($conn, $projects_query);

$documents = mysqli_query($conn, "SELECT id, document FROM quotation_document WHERE quotation_id = $quotation_id AND org_id = '" . $_SESSION['org_id'] . "'");

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

// Radio precheck
$is_product = ($row['item_type'] == 1) ? 'checked' : '';
$is_service = ($row['item_type'] == 0) ? 'checked' : '';

// Check if we're in Non-GST mode for initial display
$isNonGST = ($row['gst_type'] ?? 'gst') === 'non_gst';
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
        .product-tax-select, .service-tax-select {
            margin-bottom: 8px;
        }
        .hidden-tax-id {
            display: none;
        }
        /* Currency styles */
        .currency-badge {
            background: #0dcaf0;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .currency-prefix {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="page-wrapper">
            <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>
            <div class="content">
                <div class="row">
                    <div class="col-md-12 mx-auto">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6>Edit Quotations</h6>
                                <div class="d-flex align-items-center gap-3">
                                    <!-- Display current company currency -->
                                    <!-- <div class="currency-badge">
                                        Company Currency: <?php echo $companyCurrency['currency_symbol'] . ' (' . $companyCurrency['currency_name'] . ')'; ?>
                                    </div> -->
                                    <!-- <div class="gst-toggle-group">
                                        <span class="gst-toggle-label">GST Type:</span>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gst_type" id="gst-enabled" value="gst" <?= ($row['gst_type'] ?? 'gst') === 'gst' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="gst-enabled">GST</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gst_type" id="gst-disabled" value="non_gst" <?= ($row['gst_type'] ?? 'gst') === 'non_gst' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="gst-disabled">Non-GST</label>
                                        </div>
                                    </div> -->
                                    <!-- <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center"><i class="isax isax-eye me-1"></i>Preview</a> -->
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form action="process/action_edit_quotation.php" method="POST" enctype="multipart/form-data" id="form">
                                        <input type="hidden" name="id" value="<?= $quotation_id ?>">
                                        <input type="hidden" name="gst_type" id="gst_type_field" value="<?= $row['gst_type'] ?? 'gst' ?>">
                                        
                                        <!-- Add currency hidden fields -->
                                        <input type="hidden" name="currency_id" value="<?php echo $companyCurrency['id']; ?>">
                                        <input type="hidden" name="currency_symbol" value="<?php echo $companyCurrency['currency_symbol']; ?>">
                                        <input type="hidden" name="currency_name" value="<?php echo $companyCurrency['currency_name']; ?>">

                                        <div class="border-bottom mb-3 pb-1">
                                            <div class="row gx-3">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="mb-3">
                                                      <label class="form-label">Client Name <span class="text-danger">*</span></label>
                                                    <select class="form-select select2" name="client_id" id="client_id">
                                                        <option value="">Select Client</option>
                                                        <?php 
                                                        while ($client = mysqli_fetch_assoc($clients)) {
                                                            $displayName = trim($client['salutation'] . ' ' . $client['first_name'] . ' ' . $client['last_name']);
                                                            if (!empty($client['company_name'])) {
                                                                $displayName .= ' - ' . $client['company_name'];
                                                            }
                                                            $selected = ($client['id'] == $row['client_id']) ? 'selected' : '';
                                                            echo "<option value='{$client['id']}' $selected>" . htmlspecialchars($displayName) . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                      <span class="text-danger error-text" id="clientname_error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Quotation ID</label>
                                                        <input type="text" class="form-control" name="quotation_id" value="<?= htmlspecialchars($row['quotation_id']) ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Reference Name</label>
                                                        <input type="text" class="form-control" name="reference_name" id="reference_name" value="<?= htmlspecialchars($row['reference_name']) ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                  <div class="mb-3">
                                                    <label class="form-label">Quotation Date<span class="text-danger">*</span></label>
                                                    <div class="input-group position-relative mb-3">
                                                        <input type="text" class="form-control datepicker" id="quotation_date" placeholder="dd/mm/yyyy" name="quotation_date" value="<?= htmlspecialchars($row['quotation_date']) ?>">
                                                        <span class="input-icon-addon fs-16 text-gray-9">
                                                            <i class="isax isax-calendar-2"></i>
                                                        </span>
                                                    </div>
                                                    <span class="text-danger error-text" id="quotation_date_error"></span>
                                                  </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                  <div class="mb-3">
                                                    <label class="form-label">Expire Date<span class="text-danger">*</span></label>
                                                    <div class="input-group position-relative mb-3">
                                                        <input type="text" class="form-control datepicker" placeholder="dd/mm/yyyy" name="expiry_date" value="<?= htmlspecialchars($row['expiry_date']) ?>">
                                                        <span class="input-icon-addon fs-16 text-gray-9">
                                                            <i class="isax isax-calendar-2"></i>
                                                        </span>
                                                    </div>
                                                    <span class="text-danger error-text" id="expiry_date_error"></span>
                                                  </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                  <div class="mb-3">
                                                    <label class="form-label">Salesperson </label>
                                                    <select class="form-select select2" name="user_id" id="user_id">
                                                        <option value="">Select Salesperson</option>
                                                        <?php 
                                                        mysqli_data_seek($users, 0);
                                                        while ($user = mysqli_fetch_assoc($users)) {
                                                            $selected = ($user['id'] == $row['user_id']) ? 'selected' : '';
                                                            echo "<option value='{$user['id']}' $selected>" . htmlspecialchars($user['name']) . "</option>";
                                                        } ?>
                                                    </select>
                                                    <span class="text-danger error-text" id="username_error"></span>
                                                  </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                  <div class="mb-3">
                                                    <label class="form-label">Project Name </label>
                                                    <select class="form-select select2" name="project_id" id="project_id">
                                                        <option value="">Select Project</option>
                                                        <?php 
                                                        mysqli_data_seek($projects, 0);
                                                        while ($project = mysqli_fetch_assoc($projects)) {
                                                            $selected = ($project['id'] == $row['project_id']) ? 'selected' : '';
                                                            echo "<option value='{$project['id']}' $selected>" . htmlspecialchars($project['project_name']) . "</option>";
                                                        } ?> 
                                                    </select>
                                                    <span class="text-danger error-text" id="projectname_error"></span>
                                                  </div>
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
                                                                <div id="client_info_block">
                                                                    <p class="text-muted mb-0">Client information will appear here after selection</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="card shadow-none">
                                                        <div class="card-body">
                                                            <h6 class="mb-3">Bill From</h6>
                                                            <div class="bg-light border rounded p-3 d-flex align-items-start">
                                                                <div id="shipping_info_block">
                                                                    <p class="text-muted mb-0">Shipping information will appear here after selection</p>
                                                                </div>
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
                                                    <label class="form-label">Item Type<span class="text-danger">*</span></label>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="form-check me-3">
                                                            <input class="form-check-input" type="radio" name="item_type" id="Radio-product" value="1" <?= $is_product ?>>
                                                            <label class="form-check-label" for="Radio-product">Product</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="item_type" id="Radio-service" value="0" <?= $is_service ?>>
                                                            <label class="form-check-label" for="Radio-service">Service</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <label class="form-label">Quotation Type:</label>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="form-check me-3">
                                                            <input class="form-check-input" type="radio" name="gst_type" id="gst-enabled" value="gst" <?= ($row['gst_type'] ?? 'gst') === 'gst' ? 'checked' : '' ?>>
                                                            <label class="form-check-label" for="gst-enabled">GST</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="gst_type" id="gst-disabled" value="non_gst" <?= ($row['gst_type'] ?? 'gst') === 'non_gst' ? 'checked' : '' ?>>
                                                            <label class="form-check-label" for="gst-disabled">Non-GST</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="table-responsive rounded table-nowrap border-bottom-0 border mb-3">
                                                <table class="table mb-0 add-table <?= $isNonGST ? 'non-gst-mode' : '' ?>">
                                                    <thead class="table-dark">
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

    <?php
    $quotation_id = $_GET['id'];
    $item_query = "SELECT 
                    qi.*, 
                    p.name AS product_name, 
                    p.code AS product_hsn_code,
                    p.selling_price AS product_price,
                    p.item_type AS product_item_type,
                    s.name AS service_name_from_product,
                    s.code AS service_hsn_code,
                    s.selling_price AS service_price,
                    t.rate AS tax_rate,
                    t.name AS tax_name,
                    t.id AS tax_id
                FROM quotation_item qi
                LEFT JOIN product p ON qi.product_id = p.id
                LEFT JOIN product s ON qi.service_id = s.id
                LEFT JOIN tax t ON qi.tax_id = t.id
                WHERE qi.quotation_id = $quotation_id AND qi.is_deleted = 0 AND qi.org_id = '" . $_SESSION['org_id'] . "'";

    $item_result = mysqli_query($conn, $item_query);
    while ($item = mysqli_fetch_assoc($item_result)) {
        $qty = (float)($item['quantity'] ?? 0);
        $price = (float)($item['selling_price'] ?? 0);
        $taxRate = (float)($item['tax_rate'] ?? 0);
        $taxName = $item['tax_name'] ?? '';
        $taxId = $item['tax_id'] ?? '';
        
        // DETERMINE IF IT'S A PRODUCT OR SERVICE
        $isProduct = (!empty($item['product_id']) && $item['product_id'] != 0);
        $isService = (!empty($item['service_id']) && $item['service_id'] != 0) || !empty($item['service_name']);

        // Get display name
        if ($isProduct) {
            $displayName = $item['product_name'] ?? '';
            $itemId = $item['product_id'];
            $hsnCode = $item['product_hsn_code'] ?? '';
            $rowType = 'product';
        } else {
            $displayName = $item['service_name'] ?? $item['service_name_from_product'] ?? '';
            $itemId = $item['service_id'] ?? '';
            $hsnCode = $item['service_hsn_code'] ?? '';
            $rowType = 'service';
        }
        
        // Calculate amounts
        $lineSubtotal = $qty * $price;
        $lineTax = $lineSubtotal * $taxRate / 100;
        $amount = $lineSubtotal + $lineTax;
        
        // Adjust for Non-GST mode
        $isNonGST = ($row['gst_type'] ?? 'gst') === 'non_gst';
        $displayTaxRate = $isNonGST ? 0 : $taxRate;
        $displayLineTax = $isNonGST ? 0 : $lineTax;
        $displayAmount = $isNonGST ? $lineSubtotal : $amount;
        
        // Determine row class and values
        $rowClass = $isProduct ? 'product-row' : 'service-row';
        $quantityClass = $isProduct ? '' : 'service-quantity';
        $quantityValue = $isProduct ? $qty : ($qty > 0 ? $qty : '');
        $quantityPlaceholder = $isProduct ? '' : 'Optional';
        
        // Get currency symbol
        $currencySymbol = $companyCurrency['currency_symbol'];
    ?>
        <tr class="<?= $rowClass ?>">
            <!-- Hidden field for quotation item ID -->
            <td style="display:none;">
                <input type="hidden" name="quotation_item_id[]" value="<?= $item['id'] ?>">
            </td>
            
            <td>
                <?php if ($isProduct): ?>
                    <!-- PRODUCT FIELDS -->
                    <div class="product-fields">
                        <select class="form-select product-select" name="item_id[]">
                            <option value="">Select Product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product['id'] ?>" 
                                    data-price="<?= $product['selling_price'] ?>" 
                                    data-hsn="<?= $product['code'] ?>"
                                    data-tax="<?= $product['tax_rate'] ?>"
                                    data-tax-id="<?= $product['tax_id'] ?>"
                                    data-tax-name="<?= htmlspecialchars($product['tax_name'] ?? '', ENT_QUOTES) ?>"
                                    <?= ($product['id'] == $itemId) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($product['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" class="tax-name" name="tax_name[]" value="<?= htmlspecialchars($taxName) ?>">
                        <!-- FIXED: Always set item_type_row as product for product rows -->
                        <input type="hidden" name="item_type_row[]" value="product">
                        <!-- EMPTY service_name for product rows -->
                        <input type="hidden" name="service_name[]" value="">
                    </div>
                <?php else: ?>
                    <!-- SERVICE FIELDS -->
                    <div class="service-fields">
                        <select class="form-select service-select" name="item_id[]">
                            <option value="">Select Service</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= $service['id'] ?>" 
                                    data-price="<?= $service['selling_price'] ?>" 
                                    data-hsn="<?= $service['code'] ?>"
                                    data-tax="<?= $service['tax_rate'] ?>"
                                    data-tax-id="<?= $service['tax_id'] ?>"
                                    data-tax-name="<?= htmlspecialchars($service['tax_name'] ?? '', ENT_QUOTES) ?>"
                                    <?= ($service['id'] == $itemId) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" class="form-control service-name-input service-custom-input" name="service_name[]" placeholder="Or enter custom service name" value="<?= !empty($item['service_name']) ? htmlspecialchars($item['service_name']) : '' ?>">
                        <input type="hidden" class="tax-name" name="tax_name[]" value="<?= htmlspecialchars($taxName) ?>">
                        <!-- FIXED: Always set item_type_row as service for service rows -->
                        <input type="hidden" name="item_type_row[]" value="service">
                    </div>
                <?php endif; ?>
            </td>
            
            <td>
                <input type="number" class="form-control quantity <?= $quantityClass ?>" name="quantity[]" value="<?= $quantityValue ?>" <?= $isProduct ? 'min="1"' : '' ?> placeholder="<?= $quantityPlaceholder ?>">
            </td>
            
            <td>
                <input type="text" class="form-control hsn-code" name="code[]" value="<?= htmlspecialchars($hsnCode) ?>" readonly>
            </td>
            
            <td>
                <?php if ($isProduct): ?>
                    <!-- Store plain number for form submission -->
                    <input type="text" class="form-control selling-price" name="selling_price[]" 
                        value="<?= $currencySymbol . ' ' . number_format($price, 2) ?>" 
                        data-value="<?= $price ?>">
                <?php else: ?>
                    <!-- Store plain number for form submission -->
                    <input type="text" class="form-control service-price-input" name="selling_price[]" 
                        value="<?= $currencySymbol . ' ' . number_format($price, 2) ?>" 
                        data-value="<?= $price ?>" placeholder="0.00">
                <?php endif; ?>
            </td>
            
            <td class="tax-column">
                <?php if ($isProduct): ?>
                    <!-- Tax dropdown for products -->
                    <select class="form-select product-tax-select" name="tax_id[]" <?= $isNonGST ? 'disabled' : '' ?>>
                        <option value="">Select Tax</option>
                        <?php foreach ($taxRates as $tax): ?>
                        <option value="<?= $tax['id'] ?>" 
                            data-rate="<?= $tax['rate'] ?>"
                            <?= (!$isNonGST && $tax['id'] == $taxId) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tax['name']) ?> (<?= $tax['rate'] ?>%)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" class="tax-rate" name="rate[]" data-value="<?= $displayTaxRate ?>" value="<?= number_format($displayTaxRate, 2) . '%' ?>">
                    <div class="tax-display-container mt-2">
                        <div class="tax-amount-line"><?= $currencySymbol . ' ' . number_format($displayLineTax, 2) ?></div>
                        <div class="tax-rate-line"><?= number_format($displayTaxRate, 2) . '%' ?></div>
                    </div>
                <?php else: ?>
                    <select class="form-select service-tax-select" name="tax_id[]" <?= $isNonGST ? 'disabled' : '' ?>>
                        <option value="">Select Tax</option>
                        <?php foreach ($taxRates as $tax): ?>
                        <option value="<?= $tax['id'] ?>" 
                            data-rate="<?= $tax['rate'] ?>"
                            <?= (!$isNonGST && $tax['id'] == $taxId) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tax['name']) ?> (<?= $tax['rate'] ?>%)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" class="tax-rate" name="rate[]" data-value="<?= $displayTaxRate ?>" value="<?= number_format($displayTaxRate, 2) . '%' ?>">
                    <div class="tax-display-container mt-2">
                        <div class="tax-amount-line"><?= $currencySymbol . ' ' . number_format($displayLineTax, 2) ?></div>
                        <div class="tax-rate-line"><?= number_format($displayTaxRate, 2) . '%' ?></div>
                    </div>
                <?php endif; ?>
            </td>
            
            <td>
                <!-- Store plain number for form submission -->
                <input type="text" class="form-control amount" name="amount[]" 
                    value="<?= $currencySymbol . ' ' . number_format($displayAmount, 2) ?>" 
                    data-value="<?= $displayAmount ?>" readonly>
            </td>
            
            <td>
                <a href="javascript:void(0);" class="remove-table"><i class="isax isax-trash"></i></a>
            </td>
        </tr>
    <?php } ?>
</tbody>
                                                </table>
                                            </div>
                                            <div>
                                                <a href="javascript:void(0);" class="d-inline-flex align-items-center add-invoice-data"><i class="isax isax-add-circle5 text-primary me-1"></i>Add New</a>
                                            </div>
                                        </div>

                                        <div class="border-bottom mb-3">
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
                                                                    <a class="nav-link d-inline-flex align-items-center border fs-12 fw-semibold rounded-2" data-bs-toggle="tab" data-bs-target="#documents" href="javascript:void(0);"><i class="isax isax-bank me-1"></i>Upload Documents</a>
                                                                </li>
                                                            </ul>
                                                            <div class="tab-content">
                                                                <div class="tab-pane active show" id="notes" role="tabpanel">
                                                                    <label class="form-label">Client Notes</label>
                                                                    <textarea class="form-control" name="client_note"><?= htmlspecialchars($row['client_note']) ?></textarea>
                                                                </div>
                                                                <div class="tab-pane fade" id="terms" role="tabpanel">
                                                                    <label class="form-label">Terms & Conditions</label>
                                                                    <textarea class="form-control" name="description"><?= htmlspecialchars($row['description']) ?></textarea>
                                                                </div>
                                                                <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                                                                    <div class="file-upload drag-file w-100 h-auto py-3 d-flex align-items-center justify-content-center flex-column">
                                                                        <span class="upload-img d-block"><i class="isax isax-image text-primary me-1"></i>Upload Documents</span>
                                                                        <input type="file" class="form-control" name="document[]" id="document-upload" multiple>
                                                                        <span id="file-count-label" class="mt-2 text-muted"></span>
                                                                    </div>
                                                                    <span id="document_error" class="text-danger error-text"></span>
                                                                      <?php if (mysqli_num_rows($documents) > 0): ?>
                                                                   <div class="mt-3 w-100">
                                                                    <label class="form-label">Uploaded Documents:</label>
                                                                    <ul class="list-group">
                                                                        <?php mysqli_data_seek($documents, 0); ?>
                                                                        <?php while ($doc = mysqli_fetch_assoc($documents)): ?>
                                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                <a href="../uploads/<?= htmlspecialchars($doc['document']) ?>" target="_blank">
                                                                                    <?= htmlspecialchars($doc['document']) ?>
                                                                                </a>
                                                                            </li>
                                                                        <?php endwhile; ?>
                                                                    </ul>
                                                                </div>
                                                        <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <div class="col-lg-5">
                                                <?php
                                                // Calculate display amounts for Non-GST mode
                                                $displaySubAmount = $isNonGST ? ($row['amount'] - $row['tax_amount']) : $row['amount'];
                                                $displayTotalAmount = $isNonGST ? ($row['total_amount'] - $row['tax_amount']) : $row['total_amount'];
                                                $currencySymbol = $companyCurrency['currency_symbol'];
                                                ?>
                                                <input type="hidden" name="sub_amount" id="subtotal-amount-field" value="<?= $displaySubAmount ?>">
                                                <input type="hidden" name="tax_amount" id="tax-amount-field" value="<?= $isNonGST ? 0 : $row['tax_amount'] ?>">
                                                <input type="hidden" name="total_amount" id="total-amount-field" value="<?= $displayTotalAmount ?>">

                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <h6 class="fs-14 fw-semibold">Amount</h6>
                                                        <h6 class="fs-14 fw-semibold"><span id="currency-symbol"><?php echo $currencySymbol; ?></span> <span id="subtotal-amount"><?= number_format($displaySubAmount, 2) ?></span></h6>
                                                    </div>
                                                     <div class="tax-details" style="<?= $isNonGST ? 'display: none !important;' : '' ?>">
                                                            <!-- JS will populate tax per rate here -->
                                                        </div>
                                                    <div id="shipping-charge-group" class="d-flex align-items-center justify-content-between mb-3">
                                                        <h6 class="fs-14 fw-semibold mb-0">Shipping Charge</h6>
                                                        <div class="input-group" style="width: 150px;">
                                                            <span class="input-group-text currency-prefix"><?php echo $currencySymbol; ?></span>
                                                            <input type="text" class="form-control" id="shipping-charge" name="shipping_charge" value="<?= number_format($row['shipping_charge'], 2) ?>">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                                        <h6>Total</h6>
                                                        <h6><span id="currency-symbol-total"><?php echo $currencySymbol; ?></span> <span id="total-amount"><?= number_format($displayTotalAmount, 2) ?></span></h6>
                                                    </div>
                                                </div>
                                            </div>

                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                             <a href="quotations.php" class="btn btn-outline-white">Cancel</a>
                                            <button type="submit" name="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>

    <?php include 'layouts/vendor-scripts.php'; ?>
<!-- Additional JS for datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
// Remove ALL existing click handlers from add button
$(document).off('click', '.add-invoice-data');
$('.add-invoice-data').off('click');

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

    // ================ CURRENCY FUNCTIONS ================
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

    // Fetch client billing & shipping info on page load
    function fetchClientInfo(clientId) {
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
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching client info:', error);
                    $('#client_info_block').html('<p class="text-muted">Client information not available</p>');
                    $('#shipping_info_block').html('<p class="text-muted">Shipping information not available</p>');
                }
            });
        } else {
            $('#client_info_block').html('<p class="text-muted">Please select a client</p>');
            $('#shipping_info_block').html('<p class="text-muted">Please select a client</p>');
        }
    }

    // Fetch client info when page loads
    const initialClientId = $('#client_id').val();
    fetchClientInfo(initialClientId);

    // Fetch client info when client selection changes
    $('#client_id').on('change', function() {
        const clientId = $(this).val();
        fetchClientInfo(clientId);
    });

    // ================ FIXED: GST/Non-GST Toggle Functionality ================
    // Store tax data before switching to Non-GST mode
    let savedTaxData = [];
    
    $('input[name="gst_type"]').on('change', function() {
        const gstType = $(this).val();
        $('#gst_type_field').val(gstType);
        
        if (gstType === 'non_gst') {
            // Save current tax data BEFORE switching to Non-GST
            savedTaxData = [];
            $('.add-tbody tr').each(function(index) {
                const $row = $(this);
                const isProduct = $row.hasClass('product-row');
                const isService = $row.hasClass('service-row');
                
                savedTaxData[index] = {
                    taxId: isProduct ? $row.find('.product-tax-select').val() : $row.find('.service-tax-select').val(),
                    taxRate: $row.find('.tax-rate').data('value') || 0,
                    taxName: $row.find('.tax-name').val() || ''
                };
            });
            
            // Non-GST mode - hide tax column and tax details
            $('.add-table').addClass('non-gst-mode');
            $('.tax-details').hide();
            
            // Set all tax rates to 0 and disable dropdowns
            $('.tax-rate').data('value', 0).val('0%');
            $('.service-tax-select, .product-tax-select').prop('disabled', true);
            
            // Update tax display containers to show 0
            const currencySymbol = getCurrencySymbol();
            $('.tax-amount-line').text(currencySymbol + ' 0.00');
            $('.tax-rate-line').text('0%');
            
        } else {
            // GST mode - show tax column and tax details
            $('.add-table').removeClass('non-gst-mode');
            $('.tax-details').show();
            
            // Enable both tax dropdowns
            $('.service-tax-select, .product-tax-select').prop('disabled', false);
            
            // Restore saved tax data
            $('.add-tbody tr').each(function(index) {
                const $row = $(this);
                const isProduct = $row.hasClass('product-row');
                const taxData = savedTaxData[index] || {};
                
                if (taxData.taxId && taxData.taxId !== '') {
                    if (isProduct) {
                        $row.find('.product-tax-select').val(taxData.taxId);
                    } else {
                        $row.find('.service-tax-select').val(taxData.taxId);
                    }
                    
                    // Restore tax rate and name
                    $row.find('.tax-rate').data('value', taxData.taxRate || 0).val(formatPercent(taxData.taxRate || 0));
                    $row.find('.tax-name').val(taxData.taxName || '');
                    
                    // Update tax display
                    const price = isProduct ? 
                        ($row.find('.selling-price').data('value') || 0) : 
                        ($row.find('.service-price-input').data('value') || 0);
                    const qtyInput = $row.find('.quantity');
                    let qty = unformat(qtyInput.val());
                    
                    if (!isProduct && (qty === 0 || qtyInput.val() === '')) {
                        qty = 1;
                    }
                    
                    const lineSubtotal = price * qty;
                    const lineTaxAmount = lineSubtotal * (taxData.taxRate || 0) / 100;
                    
                    $row.find('.tax-amount-line').text(formatCurrency(lineTaxAmount));
                    $row.find('.tax-rate-line').text(formatPercent(taxData.taxRate || 0));
                }
            });
        }
        
        // Recalculate all rows
        $('.add-tbody tr').each(function() {
            calculateRow($(this));
        });
        
        calculateSummary();
    });

    // Form validation - IMPORTANT: Ensure plain numbers are submitted
    $('#form').on('submit', function(e) {
        let isValid = true;
        $('.error-text').text('');

        if (!$('#client_id').val()) {
            $('#clientname_error').text('Client is required.');
            isValid = false;
        }
        if (!$('#quotation_date').val()) {
            $('#quotation_date_error').text('Quotation Date is required.');
            isValid = false;
        }

        if (!$('input[name="expiry_date"]').val()) {
            $('#expiry_date_error').text('Expire Date is required.');
            isValid = false;
        }

        if (!$('.add-tbody tr').length) {
            $('#product_error').text('Please add at least one product or service');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({ scrollTop: $('.error-text:visible').first().offset().top - 100 }, 500);
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

    // ================ FIXED: DATA STORAGE FOR BOTH MODES ================
    let savedProductData = [];
    let savedServiceData = [];
    let currentMode = $('input[name="item_type"]:checked').val(); // 1 for product, 0 for service

    // Function to save current data before switching item types
    function saveCurrentData() {
        const data = [];
        $('.add-tbody tr').each(function(index) {
            const $row = $(this);
            const isProduct = $row.hasClass('product-row');
            const isService = $row.hasClass('service-row');
            
            if (isProduct) {
                data[index] = {
                    type: 'product',
                    productId: $row.find('.product-select').val(),
                    productName: $row.find('.product-select').find('option:selected').text(),
                    quantity: $row.find('.quantity').val(),
                    hsn: $row.find('.hsn-code').val(),
                    price: $row.find('.selling-price').data('value') || 0,
                    taxRate: $row.find('.tax-rate').data('value') || 0,
                    taxName: $row.find('.tax-name').val(),
                    amount: $row.find('.amount').data('value') || 0,
                    taxId: $row.find('.product-tax-select').val(),
                    quotationItemId: $row.find('input[name="quotation_item_id[]"]').val()
                };
            } else if (isService) {
                data[index] = {
                    type: 'service',
                    serviceId: $row.find('.service-select').val(),
                    serviceName: $row.find('.service-name-input').val(),
                    quantity: $row.find('.quantity').val(),
                    hsn: $row.find('.hsn-code').val(),
                    price: $row.find('.service-price-input').data('value') || 0,
                    taxRate: $row.find('.tax-rate').data('value') || 0,
                    taxName: $row.find('.tax-name').val(),
                    amount: $row.find('.amount').data('value') || 0,
                    taxId: $row.find('.service-tax-select').val(),
                    quotationItemId: $row.find('input[name="quotation_item_id[]"]').val()
                };
            }
        });
        
        // Save to appropriate array
        if (currentMode == 1) {
            savedProductData = data;
        } else {
            savedServiceData = data;
        }
        
        console.log('Saved data for mode:', currentMode == 1 ? 'product' : 'service', data);
    }

    // Function to load saved data for a mode
    function loadSavedData(isProductMode) {
        const dataArray = isProductMode ? savedProductData : savedServiceData;
        const rowCount = $('.add-tbody tr').length;
        
        // Clear table
        $('.add-tbody').empty();
        
        // Recreate rows with saved data
        for (let i = 0; i < rowCount; i++) {
            const rowData = dataArray[i] || {};
            const isProduct = isProductMode;
            
            if (isProduct) {
                // Create product row
                addNewProductRow(rowData);
            } else {
                // Create service row
                addNewServiceRow(rowData);
            }
        }
        
        // Update dropdowns
        updateProductDropdowns();
        updateServiceDropdowns();
        
        // Recalculate
        setTimeout(() => {
            $('.add-tbody tr').each(function() {
                calculateRow($(this));
            });
            calculateSummary();
        }, 100);
    }

    // Function to add new product row with data
    function addNewProductRow(data = {}) {
        const isNonGST = $('input[name="gst_type"]:checked').val() === 'non_gst';
        const currencySymbol = getCurrencySymbol();
        let taxOptions = '<option value="">Select Tax</option>';
        <?php foreach ($taxRates as $tax): ?>
        taxOptions += `<option value="<?= $tax['id'] ?>" data-rate="<?= $tax['rate'] ?>"><?= htmlspecialchars($tax['name']) ?> (<?= $tax['rate'] ?>%)</option>`;
        <?php endforeach; ?>

        const newRow = $(`
            <tr class="product-row">
                <td style="display:none;">
                    <input type="hidden" name="quotation_item_id[]" value="${data.quotationItemId || ''}">
                </td>
                <td>
                    <div class="product-fields">
                        <select class="form-select product-select" name="item_id[]">
                            <option value="">Select Product</option>
                        </select>
                        <input type="hidden" class="tax-name" name="tax_name[]" value="${data.taxName || ''}">
                        <input type="hidden" name="item_type_row[]" value="product">
                        <input type="hidden" name="service_name[]" value="">
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control quantity" name="quantity[]" value="${data.quantity || '1'}" min="1">
                </td>
                <td>
                    <input type="text" class="form-control hsn-code" name="code[]" value="${data.hsn || ''}" readonly>
                </td>
                <td>
                    <!-- Store plain number for form submission -->
                    <input type="text" class="form-control selling-price" name="selling_price[]" 
                           data-value="${data.price || 0}" value="${data.price ? (currencySymbol + ' ' + parseFloat(data.price).toFixed(2)) : '0.00'}">
                </td>
                <td class="tax-column">
                    <select class="form-select product-tax-select" name="tax_id[]" ${isNonGST ? 'disabled' : ''}>
                        ${taxOptions}
                    </select>
                    <input type="hidden" class="tax-rate" name="rate[]" 
                           data-value="${isNonGST ? '0' : (data.taxRate || 0)}" 
                           value="${isNonGST ? '0%' : formatPercent(data.taxRate || 0)}">
                    <div class="tax-display-container mt-2">
                        <div class="tax-amount-line">${formatCurrency((data.price || 0) * (data.quantity || 1) * (isNonGST ? 0 : (data.taxRate || 0)) / 100)}</div>
                        <div class="tax-rate-line">${isNonGST ? '0%' : formatPercent(data.taxRate || 0)}</div>
                    </div>
                </td>
                <td>
                    <!-- Store plain number for form submission -->
                    <input type="text" class="form-control amount" name="amount[]" 
                           data-value="${data.amount || 0}" value="${data.amount ? (currencySymbol + ' ' + parseFloat(data.amount).toFixed(2)) : '0.00'}" readonly>
                </td>
                <td>
                    <a href="javascript:void(0);" class="remove-table"><i class="isax isax-trash"></i></a>
                </td>
            </tr>
        `);
        
        $('.add-tbody').append(newRow);
        
        // Load products and select if data exists
        const $productSelect = newRow.find('.product-select');
        loadProducts($productSelect);
        
        if (data.productId) {
            setTimeout(() => {
                $productSelect.val(data.productId);
                if (data.taxId && !isNonGST) {
                    newRow.find('.product-tax-select').val(data.taxId);
                }
            }, 50);
        }
    }

    // Function to add new service row with data
    function addNewServiceRow(data = {}) {
        const isNonGST = $('input[name="gst_type"]:checked').val() === 'non_gst';
        const currencySymbol = getCurrencySymbol();
        let taxOptions = '<option value="">Select Tax</option>';
        <?php foreach ($taxRates as $tax): ?>
        taxOptions += `<option value="<?= $tax['id'] ?>" data-rate="<?= $tax['rate'] ?>"><?= htmlspecialchars($tax['name']) ?> (<?= $tax['rate'] ?>%)</option>`;
        <?php endforeach; ?>

        const newRow = $(`
            <tr class="service-row">
                <td style="display:none;">
                    <input type="hidden" name="quotation_item_id[]" value="${data.quotationItemId || ''}">
                </td>
                <td>
                    <div class="service-fields">
                        <select class="form-select service-select" name="item_id[]">
                            <option value="">Select Service</option>
                        </select>
                        <input type="text" class="form-control service-name-input service-custom-input" 
                               name="service_name[]" placeholder="Or enter custom service name" value="${data.serviceName || ''}">
                        <input type="hidden" class="tax-name" name="tax_name[]" value="${data.taxName || ''}">
                        <input type="hidden" name="item_type_row[]" value="service">
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control quantity service-quantity" name="quantity[]" 
                           value="${data.quantity || ''}" placeholder="Optional">
                </td>
                <td>
                    <input type="text" class="form-control hsn-code" name="code[]" value="${data.hsn || ''}" readonly>
                </td>
                <td>
                    <!-- Store plain number for form submission -->
                    <input type="text" class="form-control service-price-input" name="selling_price[]" 
                           data-value="${data.price || 0}" value="${data.price ? (currencySymbol + ' ' + parseFloat(data.price).toFixed(2)) : '0.00'}" placeholder="0.00">
                </td>
                <td class="tax-column">
                    <select class="form-select service-tax-select" name="tax_id[]" ${isNonGST ? 'disabled' : ''}>
                        ${taxOptions}
                    </select>
                    <input type="hidden" class="tax-rate" name="rate[]" 
                           data-value="${isNonGST ? '0' : (data.taxRate || 0)}" 
                           value="${isNonGST ? '0%' : formatPercent(data.taxRate || 0)}">
                    <div class="tax-display-container mt-2">
                        <div class="tax-amount-line">${formatCurrency((data.price || 0) * (data.quantity || 1) * (isNonGST ? 0 : (data.taxRate || 0)) / 100)}</div>
                        <div class="tax-rate-line">${isNonGST ? '0%' : formatPercent(data.taxRate || 0)}</div>
                    </div>
                </td>
                <td>
                    <!-- Store plain number for form submission -->
                    <input type="text" class="form-control amount" name="amount[]" 
                           data-value="${data.amount || 0}" value="${data.amount ? (currencySymbol + ' ' + parseFloat(data.amount).toFixed(2)) : '0.00'}" readonly>
                </td>
                <td>
                    <a href="javascript:void(0);" class="remove-table"><i class="isax isax-trash"></i></a>
                </td>
            </tr>
        `);
        
        $('.add-tbody').append(newRow);
        
        // Load services and select if data exists
        const $serviceSelect = newRow.find('.service-select');
        loadServices($serviceSelect);
        
        if (data.serviceId) {
            setTimeout(() => {
                $serviceSelect.val(data.serviceId);
                if (data.taxId && !isNonGST) {
                    newRow.find('.service-tax-select').val(data.taxId);
                }
            }, 50);
        }
    }

    // ================ FIXED: Item Type Change Handler ================
    $('input[name="item_type"]').on('change', function() {
        const newMode = $(this).val();
        console.log('Switching from mode', currentMode, 'to', newMode);
        
        // Save current data before switching
        saveCurrentData();
        
        // Update current mode
        currentMode = newMode;
        
        // Load saved data for the new mode
        loadSavedData(newMode == 1);
        
        console.log('Mode switched successfully');
    });

    // ================ "Add New" Button Handler ================
    $('body').on('click', '.add-invoice-data', function(e) {
        e.preventDefault();
        
        const itemType = $('input[name="item_type"]:checked').val();
        
        if (itemType == 1) {
            addNewProductRow();
        } else {
            addNewServiceRow();
        }
        
        updateProductDropdowns();
        updateServiceDropdowns();
    });

    // ================ EVENT HANDLERS ================
    
    // Product select change
    $(document).on('change', '.product-select', function() {
        const $row = $(this).closest('tr');
        const option = $(this).find('option:selected');

        if (option.val()) {
            const price = parseFloat(option.data('price')) || 0;
            const hsnCode = option.data('hsn') || '';
            const tax = parseFloat(option.data('tax')) || 0;
            const taxId = option.data('tax-id') || '';
            const taxName = option.data('tax-name') || '';

            $row.find('.hsn-code').val(hsnCode);
            $row.find('.tax-name').val(taxName);
            
            const isNonGST = $('input[name="gst_type"]:checked').val() === 'non_gst';
            const effectiveTax = isNonGST ? 0 : tax;
            
            // Store plain number in input
            $row.find('.selling-price').data('value', price).val(formatCurrency(price));
            $row.find('.tax-rate').data('value', effectiveTax).val(formatPercent(effectiveTax));

            if (taxId && !isNonGST) {
                $row.find('.product-tax-select').val(taxId);
            }

            calculateRow($row);
        } else {
            $row.find('.hsn-code').val('');
            $row.find('.selling-price').data('value', 0).val(formatCurrency(0));
            $row.find('.tax-rate').data('value', 0).val('0%');
            $row.find('.tax-name').val('');
            $row.find('.product-tax-select').val('');
            calculateRow($row);
        }

        updateProductDropdowns();
    });

    // Service select change
    $(document).on('change', '.service-select', function() {
        const $row = $(this).closest('tr');
        const option = $(this).find('option:selected');

        if (option.val()) {
            const price = parseFloat(option.data('price')) || 0;
            const hsnCode = option.data('hsn') || '';
            const tax = parseFloat(option.data('tax')) || 0;
            const taxId = option.data('tax-id') || '';
            const taxName = option.data('tax-name') || '';

            const isNonGST = $('input[name="gst_type"]:checked').val() === 'non_gst';
            const effectiveTax = isNonGST ? 0 : tax;
            
            $row.find('.hsn-code').val(hsnCode);
            $row.find('.tax-name').val(taxName);
            // Store plain number
            $row.find('.service-price-input').data('value', price).val(formatCurrency(price));
            $row.find('.tax-rate').data('value', effectiveTax).val(formatPercent(effectiveTax));
            
            if (taxId && !isNonGST) {
                $row.find('.service-tax-select').val(taxId);
            }

            calculateRow($row);
        } else {
            $row.find('.hsn-code').val('');
            $row.find('.tax-name').val('');
            $row.find('.tax-rate').data('value', 0).val('0%');
            $row.find('.service-tax-select').val('');
            calculateRow($row);
        }

        updateServiceDropdowns();
    });

    // Product tax select change
    $(document).on('change', '.product-tax-select', function() {
        const $row = $(this).closest('tr');
        const selectedOption = $(this).find('option:selected');
        const taxRate = parseFloat(selectedOption.data('rate')) || 0;
        const taxId = selectedOption.val();
        const taxName = selectedOption.text().split(' (')[0];

        const isNonGST = $('input[name="gst_type"]:checked').val() === 'non_gst';
        const effectiveTax = isNonGST ? 0 : taxRate;
        
        $row.find('.tax-rate').data('value', effectiveTax).val(formatPercent(effectiveTax));
        $row.find('.tax-id').val(taxId);
        $row.find('.tax-name').val(taxName);
        calculateRow($row);
    });

    // Service tax select change
    $(document).on('change', '.service-tax-select', function() {
        const $row = $(this).closest('tr');
        const selectedOption = $(this).find('option:selected');
        const taxRate = parseFloat(selectedOption.data('rate')) || 0;
        const taxId = selectedOption.val();
        const taxName = selectedOption.text().split(' (')[0];

        const isNonGST = $('input[name="gst_type"]:checked').val() === 'non_gst';
        const effectiveTax = isNonGST ? 0 : taxRate;
        
        $row.find('.tax-rate').data('value', effectiveTax).val(formatPercent(effectiveTax));
        $row.find('.tax-id').val(taxId);
        $row.find('.tax-name').val(taxName);
        calculateRow($row);
    });

    // Service name input
    $(document).on('input', '.service-name-input', function() {
        const $row = $(this).closest('tr');
        calculateRow($row);
    });

    // Price inputs - store plain numbers
    $(document).on('input', '.selling-price, .service-price-input', function() {
        const $row = $(this).closest('tr');
        const price = unformat($(this).val());
        $(this).data('value', price);
        calculateRow($row);
    });

    // Quantity input
    $(document).on('input', '.quantity', function() {
        calculateRow($(this).closest('tr'));
    });

    // Shipping charge input
    $(document).on('input', '#shipping-charge', function() {
        const price = unformat($(this).val());
        $(this).data('value', price);
        calculateSummary();
    });

    // Remove row
    $(document).on('click', '.remove-table', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        calculateSummary();
        updateProductDropdowns();
        updateServiceDropdowns();
    });

    // ================ CALCULATION FUNCTIONS ================
    function calculateRow($row) {
        const qtyInput = $row.find('.quantity');
        let qty = unformat(qtyInput.val());
        
        const isService = $row.hasClass('service-row');
        
        // For services, if quantity is empty or 0, use 1 for calculation
        if (isService && (qty === 0 || qtyInput.val() === '')) {
            qty = 1;
        }
        
        let price = 0;
        if (isService) {
            price = $row.find('.service-price-input').data('value') || 0;
        } else {
            price = $row.find('.selling-price').data('value') || 0;
        }
        
        const taxRate = $row.find('.tax-rate').data('value') || 0;

        const lineSubtotal = qty * price;
        const lineTaxAmount = lineSubtotal * (taxRate / 100);
        const lineTotal = lineSubtotal + lineTaxAmount;

        const taxAmountFormatted = formatCurrency(lineTaxAmount);
        const taxRateFormatted = `${taxRate}%`;
        
        $row.find('.tax-amount-line').text(taxAmountFormatted);
        $row.find('.tax-rate-line').text(taxRateFormatted);
        
        // Store plain number in input value
        $row.find('.amount').data('value', lineTotal).val(formatCurrency(lineTotal));
        
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

        $('.add-tbody tr').each(function() {
            let p = 0;
            const isService = $(this).hasClass('service-row');
            
            if (isService) {
                p = $(this).find('.service-price-input').data('value') || 0;
            } else {
                p = $(this).find('.selling-price').data('value') || 0;
            }
            
            const qtyInput = $(this).find('.quantity');
            let q = unformat(qtyInput.val());
            
            if (isService && (q === 0 || qtyInput.val() === '')) {
                q = 1;
            }
            
            const t = $(this).find('.tax-rate').data('value') || 0;
            const taxName = $(this).find('.tax-name').val() || 'Tax';

            const lineSubtotal = p * q;
            const lineTaxAmount = (lineSubtotal * t / 100);
            const lineTotal = lineSubtotal + lineTaxAmount;

            sub += lineSubtotal;
            grandTotal += lineTotal;

            if (t > 0) {
                const taxKey = `${taxName} (${t}%)`;
                if (!taxGroups[taxKey]) taxGroups[taxKey] = 0;
                taxGroups[taxKey] += lineTaxAmount;
            }
        });

        const shippingCharge = getShippingCharge();
        let taxHtml = "";

        const isNonGST = $('input[name="gst_type"]:checked').val() === 'non_gst';
        if (!isNonGST) {
            $('.add-tbody tr').each(function(index) {
                let p = 0;
                const isService = $(this).hasClass('service-row');
                
                if (isService) {
                    p = $(this).find('.service-price-input').data('value') || 0;
                } else {
                    p = $(this).find('.selling-price').data('value') || 0;
                }
                
                const qtyInput = $(this).find('.quantity');
                let q = unformat(qtyInput.val());
                
                if (isService && (q === 0 || qtyInput.val() === '')) {
                    q = 1;
                }
                
                const t = $(this).find('.tax-rate').data('value') || 0;
                const taxName = $(this).find('.tax-name').val() || 'Tax';

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

        $('.tax-details').html(taxHtml);

        const totalAll = grandTotal + shippingCharge;

        $('#subtotal-amount').text(sub.toFixed(2));
        $('#total-amount').text(totalAll.toFixed(2));

        $('#subtotal-amount-field').val(sub.toFixed(2));
        $('#tax-amount-field').val(Object.values(taxGroups).reduce((a,b)=>a+b,0).toFixed(2));
        $('#total-amount-field').val(totalAll.toFixed(2));
    }

    // ================ INITIALIZATION ================
    
    // Save initial data based on current mode
    setTimeout(() => {
        saveCurrentData();
        console.log('Initial data saved for mode:', currentMode == 1 ? 'product' : 'service');
    }, 500);

    // Initialize calculations
    $('.add-tbody tr').each(function() {
        calculateRow($(this));
    });
    calculateSummary();

    console.log('Initialization complete - Currency functionality added and data storage fixed');
});

</script>



</body>
</html>