<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

// Validate and sanitize the ID
$quotation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($quotation_id <= 0) {
    $_SESSION['error'] = "Invalid quotation ID.";
    header("Location: quotations.php");
    exit();
}

// Fetch quotation data
$query = "SELECT * FROM quotation WHERE id = $quotation_id";
$result = mysqli_query($conn, $query);

// Check if quotation exists
if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Quotation not found.";
    header("Location: quotations.php");
    exit();
}

$row = mysqli_fetch_assoc($result);

// Fetch dropdown data
$clients = mysqli_query($conn, "SELECT id, first_name,company_name FROM client WHERE is_deleted = 0");
$users = mysqli_query($conn,  "SELECT login.id, login.name FROM login
        JOIN user_role ON login.role_id = user_role.id
        WHERE login.is_deleted = 0
        ORDER BY login.name ASC");
$projects = mysqli_query($conn, "SELECT id, project_name FROM project WHERE is_deleted = 0");
$documents = mysqli_query($conn, "SELECT id, document FROM quotation_document WHERE quotation_id = $quotation_id");

// Radio precheck
$is_product = ($row['item_type'] == 1) ? 'checked' : '';
$is_service = ($row['item_type'] == 0) ? 'checked' : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>


     <!-- Additional CSS for datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
                                <h6>Edit Quotations</h6>
                                <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center"><i class="isax isax-eye me-1"></i>Preview</a>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form action="process/action_edit_quotation.php" method="POST" enctype="multipart/form-data" id="form">
                                    <input type="hidden" name="id" value="<?= $quotation_id ?>">   
                                    <div class="border-bottom mb-3 pb-1">
                                        <div class="row gx-3">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                  <label class="form-label">Client Name <span class="text-danger">*</span></label>
                                                  <select class="form-select select2" name="client_id" id="client_id" >
                                                      <option value="">Select Client</option>
                                                   <?php while ($client = mysqli_fetch_assoc($clients)) {
    $displayName = $client['first_name'];
    if (!empty($client['company_name'])) {
        $displayName .= ' - ' . $client['company_name'];
    }
    $selected = ($client['id'] == $row['client_id']) ? 'selected' : '';
    echo "<option value='{$client['id']}' $selected>{$displayName}</option>";
} ?>
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
                                                    <?php while ($user = mysqli_fetch_assoc($users)) {
                                                    $selected = ($user['id'] == $row['user_id']) ? 'selected' : '';
                                                    echo "<option value='{$user['id']}' $selected>{$user['name']}</option>";
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
                                                    <?php while ($project = mysqli_fetch_assoc($projects)) {
                                                    $selected = ($project['id'] == $row['project_id']) ? 'selected' : '';
                                                    echo "<option value='{$project['id']}' $selected>{$project['project_name']}</option>";
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
                                                                <input class="form-check-input" type="radio" name="item_type" id="Radio-product" value="1" <?= $is_product ?>>
                                                                <label class="form-check-label" for="Radio-product">Product</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="item_type" id="Radio-service" value="0" <?= $is_service ?>>
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
                                                            <th>Unit</th>
                                                            <th>Selling Price</th>
                                                            <th>Tax (%)</th>
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
                                                                            u.name AS unit_name,
                                                                            t.rate AS tax_rate,
                                                                            t.name AS tax_name,
                                                                            t.id AS tax_id,
                                                                            p.selling_price,
                                                                            p.unit_id
                                                                        FROM quotation_item qi
                                                                        LEFT JOIN product p ON qi.product_id = p.id
                                                                        LEFT JOIN units u ON p.unit_id = u.id
                                                                        LEFT JOIN tax t ON p.tax_id = t.id
                                                                        WHERE qi.quotation_id = $quotation_id AND qi.is_deleted = 0";

                                                            $item_result = mysqli_query($conn, $item_query);
                                                            while ($item = mysqli_fetch_assoc($item_result)) {
                                                                $qty = (float)($item['quantity'] ?? 0);
                                                                $price = (float)($item['selling_price'] ?? 0);
                                                                $taxRate = (float)($item['tax_rate'] ?? 0);
                                                                $taxName = $item['tax_name'] ?? '';

                                                                $lineSubtotal = $qty * $price;
                                                                $lineTax = $lineSubtotal * $taxRate / 100;
                                                                $amount = $lineSubtotal + $lineTax;
                                                            ?>
                                                                <tr>
                                                                    <td>
                                                                        <select class="form-select item-select" name="product_id[]">
                                                                            <option value="">Select Product</option>
                                                                            <?php
                                                                            $product_query = "SELECT 
                                                                                                p.id, p.name, p.selling_price, p.unit_id, 
                                                                                                u.name AS unit_name, 
                                                                                                t.id AS tax_id, t.rate AS tax_rate, t.name AS tax_name
                                                                                            FROM product p
                                                                                            LEFT JOIN units u ON p.unit_id = u.id
                                                                                            LEFT JOIN tax t ON p.tax_id = t.id
                                                                                            WHERE p.is_deleted = 0";
                                                                            $product_result = mysqli_query($conn, $product_query);
                                                                            while ($product = mysqli_fetch_assoc($product_result)) {
                                                                                $selected = ($product['id'] == $item['product_id']) ? 'selected' : '';
                                                                                echo '<option value="' . $product['id'] . '" 
                                                                                    data-unit="' . htmlspecialchars($product['unit_name']) . '"
                                                                                    data-unit-id="' . $product['unit_id'] . '"
                                                                                    data-price="' . $product['selling_price'] . '"
                                                                                    data-tax="' . $product['tax_rate'] . '"
                                                                                    data-tax-id="' . $product['tax_id'] . '"
                                                                                    data-tax-name="' . htmlspecialchars($product['tax_name']) . '" ' . $selected . '>' . 
                                                                                    htmlspecialchars($product['name']) . '</option>';
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </td>
                                                                    <td><input type="number" class="form-control quantity" name="quantity[]" value="<?= $qty ?>" min="1"></td>
                                                                    <td>
                                                                        <input type="text" class="form-control unit-name" value="<?= htmlspecialchars($item['unit_name'] ?? '') ?>" readonly>
                                                                        <input type="hidden" class="unit-id" name="unit_id[]" value="<?= $item['unit_id'] ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control selling-price" name="selling_price[]" 
                                                                            value="<?= '$ ' . number_format($price, 2) ?>" 
                                                                            data-value="<?= $price ?>" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control tax-rate" 
                                                                            value="<?= number_format($taxRate, 2) . '%' ?>" 
                                                                            data-value="<?= $taxRate ?>" readonly>
                                                                        <input type="hidden" class="tax-id" name="tax_id[]" value="<?= $item['tax_id'] ?>">
                                                                        <input type="hidden" class="tax-name" value="<?= htmlspecialchars($taxName) ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control amount-display" value="<?= '$ ' . number_format($amount, 2) ?>" readonly>
                                                                        <input type="hidden" class="amount-storage" name="amount[]" value="<?= $amount ?>">
                                                                    </td>
                                                                    <td><a href="javascript:void(0);" class="text-danger remove-table"><i class="isax isax-close-circle"></i></a></td>
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
                                                <input type="hidden" name="sub_amount" id="subtotal-amount-field" value="<?= $row['amount'] ?>">
                                                <input type="hidden" name="tax_amount" id="tax-amount-field" value="<?= $row['tax_amount'] ?>">
                                                <input type="hidden" name="total_amount" id="total-amount-field" value="<?= $row['total_amount'] ?>">

                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <h6 class="fs-14 fw-semibold">Amount</h6>
                                                        <h6 class="fs-14 fw-semibold" id="subtotal-amount"><?= '$ ' . number_format($row['amount'], 2) ?></h6>
                                                    </div>
                                                     <div class="tax-details">
                                                            <!-- JS will populate tax per rate here -->
                                                        </div>
                                                    <div id="shipping-charge-group" class="d-flex align-items-center justify-content-between mb-3">
                                                        <h6 class="fs-14 fw-semibold mb-0">Shipping Charge</h6>
                                                        <input type="text" class="form-control" id="shipping-charge" name="shipping_charge" value="<?= '$ ' . number_format($row['shipping_charge'], 2) ?>">
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                                        <h6>Total</h6>
                                                        <h6 id="total-amount"><?= '$ ' . number_format($row['total_amount'], 2) ?></h6>
                                                    </div>
                                                </div>
                                            </div>

                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                             <a href="quotations.php" class="btn btn-outline-whit">Cancel</a>
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
        // Initialize datepicker
        $(document).ready(function() {
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
          });
            </script>
<script>
$(document).ready(function() {

  /* =========================
     Helpers: format / unformat
  ========================== */
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

  /* =========================
     Allow only text (no digits)
  ========================== */
  $('#reference_name').on('input', function () {
    this.value = this.value.replace(/[0-9]/g, '');
  });

  $('#shipping-charge').on('input', function () {
    let val = this.value.replace(/[^0-9.]/g, ''); 
    let parts = val.split('.');
    if (parts.length > 2) {
        val = parts[0] + '.' + parts[1]; // keep only first decimal point
    }
    this.value = val;
  });

  /* =========================
     Fetch client billing & shipping info
  ========================== */
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

  /* =========================
     FORM VALIDATION
  ========================== */
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
    // if (!$('#user_id').val()) {
    //   $('#username_error').text('Salesperson is required.');
    //   isValid = false;
    // }
    if (!$('input[name="expiry_date"]').val()) {
      $('#expiry_date_error').text('Expire Date is required.');
      isValid = false;
    }
    if (!$('.add-tbody tr').length) {
      $('#product_error').text('Please add at least one product or service');
      isValid = false;
    }
    // if ($('#project_id').val() === '') {
    //   $('#projectname_error').text('Project is required.');
    //   isValid = false;
    // }

    // File type validation
    const allowedExtensions = ['pdf', 'xls', 'xlsx', 'csv'];
    const files = $('#document-upload')[0].files;

    for (let i = 0; i < files.length; i++) {
      const ext = files[i].name.split('.').pop().toLowerCase();
      if (!allowedExtensions.includes(ext)) {
        $('#document_error').text('Only PDF, Excel, or CSV files are allowed.');
        isValid = false;
        break;
      }
    }

    if (!isValid) {
      e.preventDefault();
      $('html, body').animate({ scrollTop: $('.error-text:visible').first().offset().top - 100 }, 500);
    }
  });

  /* =========================
     Upload label
  ========================== */
  $('#document-upload').on('change', function() {
    const files = this.files;
    const label = files.length === 1 ? files[0].name : (files.length > 1 ? `${files.length} files selected` : '');
    $('#file-count-label').text(label);
  });

  /* =========================
     Items dropdown utilities
  ========================== */
  function updateItemDropdowns() {
    let selectedItems = [];
    $('.item-select').each(function() {
      let val = $(this).val();
      if (val) selectedItems.push(val);
    });

    $('.item-select').each(function() {
      let currentVal = $(this).val();
      $(this).find('option').each(function() {
        if ($(this).val() && selectedItems.includes($(this).val()) && $(this).val() !== currentVal) {
          $(this).hide();
        } else {
          $(this).show();
        }
      });
    });
  }

  /* =========================
     Format behaviors for currency/percent inputs
  ========================== */
  function attachCurrencyBehavior(selector, onChangeCallback) {
    // Focus -> show raw number
    $(document).on('focus', selector, function(){
      const raw = $(this).data('value');
      $(this).val(raw !== undefined ? raw : unformat($(this).val()));
    });
    // Blur -> store & show $xx.xx
    $(document).on('blur', selector, function(){
      const num = unformat($(this).val());
      $(this).data('value', num).val(formatCurrency(num));
      if (onChangeCallback) onChangeCallback($(this));
    });
    // Input -> recalc live
    $(document).on('input', selector, function(){
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

  // Apply to line-item fields
  attachCurrencyBehavior('.selling-price', function($el){
    calculateRow($el.closest('tr'));
  });
  attachPercentBehavior('.tax-rate', function($el){
    calculateRow($el.closest('tr'));
  });

  // Apply to shipping charge
  attachCurrencyBehavior('#shipping-charge', function(){
    calculateSummary();
  });

  // Initialize shipping field formatting on load
  (function initShipping(){
    const $ship = $('#shipping-charge');
    if ($ship.length) {
      const initVal = unformat($ship.val());
      $ship.data('value', initVal);
      $ship.val(formatCurrency(initVal));
    }
  })();

  /* =========================
     Item events
  ========================== */
  $(document).on('change', '.item-select', function() {
    const $row = $(this).closest('tr');
    const option = $(this).find('option:selected');

    if (option.val()) {
      const price = parseFloat(option.data('price')) || 0;
      const unit = option.data('unit') || '';
      const unitId = option.data('unit-id') || '';
      const tax = parseFloat(option.data('tax')) || 0;
      const taxId = option.data('tax-id') || '';
      const taxName = option.data('tax-name') || '';

      $row.find('.unit-name').val(unit);
      $row.find('.unit-id').val(unitId);
      $row.find('.tax-id').val(taxId);
      $row.find('.tax-name').val(taxName);

      $row.find('.selling-price').data('value', price).val(formatCurrency(price));
      $row.find('.tax-rate').data('value', tax).val(formatPercent(tax));

      calculateRow($row);
    } else {
      resetRow($row);
    }

    updateItemDropdowns();
  });

  $(document).on('input', '.quantity', function() {
    calculateRow($(this).closest('tr'));
  });

  $(document).on('click', '.remove-table', function(e) {
    e.preventDefault();
    $(this).closest('tr').remove();
    calculateSummary();
    updateItemDropdowns();
  });

  /* =========================
     Calculations
  ========================== */
  function calculateRow($row) {
    const qty  = unformat($row.find('.quantity').val());
    const price = $row.find('.selling-price').data('value') || 0;
    const tax   = $row.find('.tax-rate').data('value') || 0;

    const lineSubtotal = qty * price;
    const lineTax = lineSubtotal * (tax / 100);
    const lineTotal = lineSubtotal + lineTax;

    $row.find('.amount-storage').val(lineTotal.toFixed(2));
    $row.find('.amount-display').val(formatCurrency(lineTotal));
    calculateSummary();
  }

  function getShippingCharge() {
    const $ship = $('#shipping-charge');
    if (!$ship.length) return 0;
    // Prefer stored numeric if available
    const stored = $ship.data('value');
    if (stored !== undefined) return parseFloat(stored) || 0;
    // Fallback: parse current field
    return unformat($ship.val());
  }

  function calculateSummary() {
    let sub = 0, taxGroups = {}, grandTotal = 0;

    $('.add-tbody tr').each(function() {
      const p = $(this).find('.selling-price').data('value') || 0;
      const q = unformat($(this).find('.quantity').val());
      const t = $(this).find('.tax-rate').data('value') || 0;
      const taxName = $(this).find('.tax-name').val() || 'Tax';

      const lineSubtotal = p * q;
      const lineTax = (lineSubtotal * t / 100);
      const lineTotal = lineSubtotal + lineTax;

      sub += lineSubtotal;
      grandTotal += lineTotal;

      if (t > 0) {
        // Use tax name + rate as key
        const taxKey = `${taxName} (${t}%)`;
        if (!taxGroups[taxKey]) taxGroups[taxKey] = 0;
        taxGroups[taxKey] += lineTax;
      }
    });

    // Shipping
    const shippingCharge = getShippingCharge();
    // let taxHtml = "";

    // $.each(taxGroups, function(taxLabel, amount) {
    //   taxHtml += `
    //     <div class="d-flex align-items-center justify-content-between mb-2">
    //       <h6 class="fs-14 fw-semibold">${taxLabel}</h6>
    //       <h6 class="fs-14 fw-semibold">${formatCurrency(amount)}</h6>
    //     </div>`;
    // });

    // $('.tax-details').html(taxHtml);
let taxHtml = "";

$('.add-tbody tr').each(function() {
  const p = $(this).find('.selling-price').data('value') || 0;
  const q = unformat($(this).find('.quantity').val());
  const t = $(this).find('.tax-rate').data('value') || 0;
  const taxName = $(this).find('.tax-name').val() || 'Tax';

  const lineSubtotal = p * q;
  const lineTax = (lineSubtotal * t / 100);

  if (t > 0) {
    taxHtml += `
      <div class="d-flex align-items-center justify-content-between mb-2">
        <h6 class="fs-14 fw-semibold">${taxName} (${t}%)</h6>
        <h6 class="fs-14 fw-semibold">${formatCurrency(lineTax)}</h6>
      </div>`;
  }
});
$('.tax-details').html(taxHtml);

    const totalAll = grandTotal + shippingCharge;

    $('#subtotal-amount').text(formatCurrency(sub));
    $('#total-amount').text(formatCurrency(totalAll));

    // Hidden numeric fields for backend
    $('#subtotal-amount-field').val(sub.toFixed(2));
    $('#tax-amount-field').val(Object.values(taxGroups).reduce((a,b)=>a+b,0).toFixed(2));
    $('#total-amount-field').val(totalAll.toFixed(2));
  }

  function resetRow($row) {
    $row.find('.quantity').val(1);
    $row.find('.unit-name, .selling-price, .tax-rate, .amount-display').val('').removeData('value');
    $row.find('.unit-id, .tax-id, .amount-storage, .tax-name').val('');
    calculateSummary();
  }

  // Initial pass
  updateItemDropdowns();
  
  // Initialize all row calculations on page load
  $('.add-tbody tr').each(function() {
    calculateRow($(this));
  });
  
  // Calculate summary and tax details on page load
  calculateSummary();

  // === Trigger change for existing selected client in edit mode ===
  if ($('#client_id').val()) {
    $('#client_id').trigger('change');
  }
});
</script>

</body>
</html>