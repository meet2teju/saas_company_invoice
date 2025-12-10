<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

// Get next AUTO_INCREMENT value
$query = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES 
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotation'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($row && isset($row['AUTO_INCREMENT'])) {
    $nextId = $row['AUTO_INCREMENT'];
    $newQuotationID = 'EST-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
} else {
    // Fallback in case of error
    $newQuotationID = 'EST-0001';
}

// Fetch tax rates from database
$taxRates = [];
$taxQuery = "SELECT id, name, rate FROM tax WHERE status = 1 AND org_id = '" . $_SESSION['org_id'] . "'";
$taxResult = mysqli_query($conn, $taxQuery);
while ($taxRow = mysqli_fetch_assoc($taxResult)) {
    $taxRates[] = $taxRow;
}

// Fetch products and services from product table based on item_type
$products = [];
$services = [];
$itemQuery = "SELECT p.id, p.name, p.selling_price, p.code, p.item_type, 
                     t.id AS tax_id, t.rate AS tax_rate, t.name AS tax_name
              FROM product p
              LEFT JOIN tax t ON p.tax_id = t.id
              WHERE p.is_deleted = 0 AND p.status = 1 AND p.org_id = '" . $_SESSION['org_id'] . "'";
$itemResult = mysqli_query($conn, $itemQuery);
while ($item = mysqli_fetch_assoc($itemResult)) {
    if ($item['item_type'] == 1) {
        $products[] = $item;
    } else {
        $services[] = $item;
    }
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
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-md-12 mx-auto">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6>Add Quotations</h6>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="gst-toggle-group">
                                        <span class="gst-toggle-label">GST Type:</span>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gst_type" id="gst-enabled" value="gst" checked>
                                            <label class="form-check-label" for="gst-enabled">GST</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gst_type" id="gst-disabled" value="non_gst">
                                            <label class="form-check-label" for="gst-disabled">Non-GST</label>
                                        </div>
                                    </div>
                                    <!-- <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center"><i class="isax isax-eye me-1"></i>Preview</a> -->
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form action="process/action_add_quotation.php" method="POST" enctype="multipart/form-data" id="form">
                                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['crm_user_id'] ?? ''; ?>">
                                        <input type="hidden" name="gst_type" id="gst_type_field" value="gst">

                                        <div class="border-bottom mb-3 pb-1">
                                            <div class="row gx-3">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Client Name <span class="text-danger">*</span></label>
                                                        <select class="form-select select2" name="client_id" id="client_id">
                                                            <option value="">Select Client</option>
                                                            <?php
                                                            $result = mysqli_query($conn, "SELECT * FROM client WHERE org_id = '" . $_SESSION['org_id'] . "'");
                                                            while ($row = mysqli_fetch_assoc($result)) {
                                                                $displayName = trim($row['salutation'] . ' ' . $row['first_name'] . ' ' . $row['last_name']);
                                                                if (!empty($row['company_name'])) {
                                                                    $displayName .= ' - ' . $row['company_name'];
                                                                }
                                                                echo '<option value="' . $row['id'] . '">' . htmlspecialchars($displayName) . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                        <span class="text-danger error-text" id="clientname_error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Quotation ID</label>
                                                        <input type="text" class="form-control" name="quotation_id" value="<?= $newQuotationID ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Reference Name</label>
                                                        <input type="text" class="form-control" value="" name="reference_name" id="reference_name">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Quotation Date<span class="text-danger">*</span></label>
                                                        <div class="input-group position-relative">
                                                            <input type="text" class="form-control datepicker" id="quotation_date" placeholder="dd/mm/yyyy" name="quotation_date">
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
                                                        <div class="input-group position-relative">
                                                            <input type="text" class="form-control datepicker" id="quotation_due_date" placeholder="dd/mm/yyyy" name="expiry_date">
                                                            <span class="input-icon-addon fs-16 text-gray-9">
                                                                <i class="isax isax-calendar-2"></i>
                                                            </span>
                                                        </div>
                                                        <span class="text-danger error-text" id="quotation_due_date_error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Salesperson</label>
                                                        <select class="form-select select2" name="salesperson_id" id="salesperson_id">
                                                            <option value="">Select Salesperson</option>
                                                            <?php
                                                            $query = "SELECT login.id, login.name FROM login
                                                                      JOIN user_role ON login.role_id = user_role.id
                                                                      WHERE login.is_deleted = 0 AND login.org_id = '" . $_SESSION['org_id'] . "'
                                                                      ORDER BY login.name ASC";
                                                            $result = mysqli_query($conn, $query);
                                                            while ($row = mysqli_fetch_assoc($result)) {
                                                                echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                        <span class="text-danger error-text" id="salesperson_error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Project Name</label>
                                                        <select class="form-select select2" name="project_id" id="project_id">
                                                            <option value="">Select Project</option>
                                                            <?php
                                                            $result = mysqli_query($conn, "SELECT * FROM project WHERE org_id = '" . $_SESSION['org_id'] . "'");
                                                            while ($row = mysqli_fetch_assoc($result)) {
                                                                echo '<option value="' . $row['id'] . '">' . $row['project_name'] . '</option>';
                                                            }
                                                            ?>
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
                                            <div class="row">
                                                <div class="col-xl-4 col-md-6">
                                                    <h6 class="mb-3">Items & Details</h6>
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
                                            </div>

                                            <div class="table-responsive rounded table-nowrap border-bottom-0 border mb-3">
                                                <table class="table mb-0 add-table">
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
                                                                    <textarea class="form-control" name="client_note"></textarea>
                                                                </div>
                                                                <div class="tab-pane fade" id="terms" role="tabpanel">
                                                                    <label class="form-label">Terms & Conditions</label>
                                                                    <textarea class="form-control" name="description"></textarea>
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
                                                </div>
                                                <div class="col-lg-5">
                                                    <input type="hidden" name="sub_amount" id="subtotal-amount-field" value="0">
                                                    <input type="hidden" name="tax_amount" id="tax-amount-field" value="0">
                                                    <input type="hidden" name="total_amount" id="total-amount-field" value="0">

                                                    <div class="mb-3">
                                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                                            <h6 class="fs-14 fw-semibold">Amount</h6>
                                                            <h6 class="fs-14 fw-semibold" id="subtotal-amount"></h6>
                                                        </div>
                                                        <div class="tax-details"></div>
                                                        <div id="shipping-charge-group" class="d-flex align-items-center justify-content-between mb-3" style="display: none;">
                                                            <h6 class="fs-14 fw-semibold mb-0">Shipping Charge</h6>
                                                            <input type="text" class="form-control" id="shipping-charge" name="shipping_charge" value="0.00">
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                                            <h6>Total</h6>
                                                            <h6 id="total-amount"></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <button type="button" class="btn btn-outline-white" onclick="window.location.href='quotations.php'">Cancel</button>
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
        // COMPLETE SCRIPT - Fixed tax dropdown for products

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

            // GST/Non-GST toggle functionality
            $('input[name="gst_type"]').on('change', function() {
                const gstType = $(this).val();
                $('#gst_type_field').val(gstType);
                
                if (gstType === 'non_gst') {
                    $('.add-table').addClass('non-gst-mode');
                    $('.tax-details').hide();
                    $('.tax-rate').data('value', 0).val('0%');
                    $('.service-tax-select').val('');
                    $('.product-tax-select').val('');
                    $('.tax-amount-line').text('$ 0.00');
                    $('.tax-rate-line').text('0%');
                } else {
                    $('.add-table').removeClass('non-gst-mode');
                    $('.tax-details').show();
                    $('.product-select').each(function() {
                        const $row = $(this).closest('tr');
                        const option = $(this).find('option:selected');
                        if (option.val()) {
                            const tax = parseFloat(option.data('tax')) || 0;
                            $row.find('.tax-rate').data('value', tax).val(formatPercent(tax));
                        }
                    });
                    $('.service-select').each(function() {
                        const $row = $(this).closest('tr');
                        const option = $(this).find('option:selected');
                        if (option.val()) {
                            const tax = parseFloat(option.data('tax')) || 0;
                            $row.find('.tax-rate').data('value', tax).val(formatPercent(tax));
                        }
                    });
                }
                
                $('.add-tbody tr').each(function() {
                    calculateRow($(this));
                });
                
                calculateSummary();
            });

            function formatCurrency(value) {
                const n = parseFloat(value);
                if (isNaN(n)) return '';
                return `$ ${n.toFixed(2)}`;
            }

            function formatPercent(value) {
                const n = parseFloat(value);
                if (isNaN(n)) return '';
                return `${n.toFixed(2)}%`;
            }

            function unformat(value) {
                const n = parseFloat(String(value).replace(/[^0-9.-]/g, ''));
                return isNaN(n) ? 0 : n;
            }

            // Fetch client billing & shipping info
            $('#client_id').on('change', function() {
                const clientId = $(this).val();
                if (clientId) {
                    $.ajax({
                        url: 'process/fetch_client_full_info.php',
                        type: 'POST',
                        data: { client_id: clientId },
                        dataType: 'json',
                        success: response => {
                            $('#client_info_block').html(response.billing_html);
                            $('#shipping_info_block').html(response.shipping_html);
                        }
                    });
                } else {
                    $('#client_info_block, #shipping_info_block').empty();
                }
            });

            // Form validation
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

                if (!$('#quotation_due_date').val()) {
                    $('#quotation_due_date_error').text('Quotation Due Date is required.');
                    isValid = false;
                }

                if (!$('.add-tbody tr').length) {
                    $('#product_error').text('Please add at least one product or service');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    $('html, body').animate({ scrollTop: $('.error-text:visible').first().offset().top - 100 }, 500);
                }
            });

            // =============================================
            // Load products and services functions
            // =============================================
            function loadProducts(target) {
                let productOptions = '<option value="">Select Product</option>';
                <?php foreach ($products as $product): ?>
                productOptions += `<option value="<?= $product['id'] ?>" 
                                    data-price="<?= $product['selling_price'] ?>" 
                                    data-hsn="<?= $product['code'] ?>"
                                    data-tax="<?= $product['tax_rate'] ?>"
                                    data-tax-id="<?= $product['tax_id'] ?>"
                                    data-tax-name="<?= $product['tax_name'] ?>">
                                    <?= $product['name'] ?>
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
                                    data-tax-name="<?= $service['tax_name'] ?>">
                                    <?= $service['name'] ?>
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
                const isNonGST = $('input[name="gst_type"]:checked').val() === 'non_gst';
                
                let taxOptions = '<option value="">Select Tax</option>';
                <?php foreach ($taxRates as $tax): ?>
                taxOptions += `<option value="<?= $tax['id'] ?>" data-rate="<?= $tax['rate'] ?>"><?= $tax['name'] ?> (<?= $tax['rate'] ?>%)</option>`;
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
                            </td>
                            <td>
                                <input type="number" class="form-control quantity" name="quantity[]" value="1" min="1">
                            </td>
                            <td>
                                <input type="text" class="form-control hsn-code" name="code[]" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control selling-price" name="selling_price[]" value="0.00" data-value="0">
                            </td>
                            <td class="tax-column">
                                <!-- Tax dropdown for products -->
                                <select class="form-select product-tax-select" name="tax_id[]">
                                    ${taxOptions}
                                </select>
                                <input type="hidden" class="tax-rate" name="rate[]" data-value="${isNonGST ? '0' : '0'}">
                                <input type="hidden" class="tax-name" name="tax_name[]" value="">
                                <div class="tax-display-container mt-2">
                                    <div class="tax-amount-line"></div>
                                    <div class="tax-rate-line"></div>
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control amount" name="amount[]" data-value="0" readonly>
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
                            </td>
                            <td>
                                <input type="number" class="form-control quantity service-quantity" name="quantity[]" value="" placeholder="Optional">
                            </td>
                            <td>
                                <input type="text" class="form-control hsn-code" name="code[]" readonly>
                            </td>
                            <td>
                                <!-- FIXED: Changed to regular text input without currency formatting interference -->
                                <input type="text" class="form-control service-price-input" name="selling_price[]" value="0.00" data-value="0" placeholder="0.00">
                            </td>
                            <td class="tax-column">
                                <select class="form-select service-tax-select" name="tax_id[]">
                                    ${taxOptions}
                                </select>
                                <input type="hidden" class="tax-rate" name="rate[]" data-value="${isNonGST ? '0' : '0'}">
                                <input type="hidden" class="tax-name" name="tax_name[]" value="">
                                <div class="tax-display-container mt-2">
                                    <div class="tax-amount-line"></div>
                                    <div class="tax-rate-line"></div>
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control amount" name="amount[]" data-value="0" readonly>
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
                    $('.add-tbody tr:last .tax-amount-line').text('$ 0.00');
                    $('.add-tbody tr:last .tax-rate-line').text('0%');
                }
                
                updateProductDropdowns();
                updateServiceDropdowns();
            }

            // Format behaviors - FIXED: Simplified for service price inputs
            function attachCurrencyBehavior(selector, onChangeCallback) {
                $(document).on('focus', selector, function(){
                    const raw = $(this).data('value');
                    $(this).val(raw !== undefined ? raw : unformat($(this).val()));
                });
                $(document).on('blur', selector, function(){
                    const num = unformat($(this).val());
                    $(this).data('value', num).val(formatCurrency(num));
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
            
            // FIXED: Use direct input handling for service price (no currency formatting interference)
            $(document).on('input', '.service-price-input', function() {
                const $row = $(this).closest('tr');
                const price = unformat($(this).val());
                $(this).data('value', price);
                calculateRow($row);
            });
            
            $(document).on('blur', '.service-price-input', function() {
                const $row = $(this).closest('tr');
                const price = unformat($(this).val());
                $(this).data('value', price).val(formatCurrency(price));
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
                        $ship.val(formatCurrency(initVal));
                    } else {
                        $ship.val(initVal.toFixed(2));
                    }
                }
            })();

            // Item events
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
                    $row.find('.tax-id').val(taxId);
                    $row.find('.tax-name').val(taxName);
                    
                    const isNonGST = $('input[name="gst_type"]:checked').val() === 'non_gst';
                    const effectiveTax = isNonGST ? 0 : tax;
                    
                    $row.find('.selling-price').data('value', price).val(formatCurrency(price));
                    $row.find('.tax-rate').data('value', effectiveTax).val(formatPercent(effectiveTax));

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

            // Service select change handler
            $(document).on('change', '.service-select', function() {
                const $row = $(this).closest('tr');
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
                    
                    const isNonGST = $('input[name="gst_type"]:checked').val() === 'non_gst';
                    const effectiveTax = isNonGST ? 0 : tax;
                    
                    // FIXED: Set service price without formatting interference
                    $row.find('.service-price-input').data('value', price).val(price.toFixed(2));
                    $row.find('.tax-rate').data('value', effectiveTax).val(formatPercent(effectiveTax));
                    
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
                    $row.find('.amount').val('').removeData('value');
                    $row.find('.tax-amount-line').text('');
                    $row.find('.tax-rate-line').text('');
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

                const isNonGST = $('input[name="gst_type"]:checked').val() === 'non_gst';
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

                const isNonGST = $('input[name="gst_type"]:checked').val() === 'non_gst';
                const effectiveTax = isNonGST ? 0 : taxRate;
                
                $row.find('.tax-rate').data('value', effectiveTax).val(formatPercent(effectiveTax));
                $row.find('.tax-id').val(taxId);
                $row.find('.tax-name').val(taxName);
                calculateRow($row);
            });

            $(document).on('input', '.service-name-input', function() {
                const $row = $(this).closest('tr');
                const $serviceSelect = $row.find('.service-select');
                
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

                const taxAmountFormatted = formatCurrency(lineTaxAmount);
                const taxRateFormatted = `${taxRate}%`;
                
                $row.find('.tax-amount-line').text(taxAmountFormatted);
                $row.find('.tax-rate-line').text(taxRateFormatted);
                
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
                        q = 1; // Use 1 for calculation but keep display empty
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
                        const isServiceRow = $(this).hasClass('service-row');
                        if (isServiceRow && (q === 0 || qtyInput.val() === '')) {
                            q = 1; // Use 1 for calculation but keep display empty
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
                                    <h6 class="fs-14 fw-semibold">${formatCurrency(lineTaxAmount)}</h6>
                                </div>`;
                        }
                    });
                }

                $('.tax-details').html(taxHtml);

                const totalAll = grandTotal + shippingCharge;

                $('#subtotal-amount').text(formatCurrency(sub));
                $('#total-amount').text(formatCurrency(totalAll));

                $('#subtotal-amount-field').val(sub.toFixed(2));
                $('#tax-amount-field').val(Object.values(taxGroups).reduce((a,b)=>a+b,0).toFixed(2));
                $('#total-amount-field').val(totalAll.toFixed(2));
            }
            
            function resetRow($row) {
                const isService = $row.hasClass('service-row');
                $row.find('.quantity').val(isService ? '' : '1').removeClass('service-quantity');
                $row.find('.hsn-code, .selling-price, .tax-rate, .amount, .service-name-input, .service-price-input').val('').removeData('value');
                $row.find('.tax-id').val('');
                $row.find('.tax-amount-line').text('');
                $row.find('.tax-rate-line').text('');
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
            
            console.log('Initialization complete - service price editing works with optional quantity');
        });
    </script>
</body>
</html>