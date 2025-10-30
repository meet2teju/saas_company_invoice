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
                                <h6>Add Quotations</h6>
                                <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center"><i class="isax isax-eye me-1"></i>Preview</a>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form action="process/action_add_quotation.php" method="POST" enctype="multipart/form-data" id="form">
                                      <input type="hidden" name="user_id" value="<?php echo $_SESSION['crm_user_id'] ?? ''; ?>">

                                        <div class="border-bottom mb-3 pb-1">
                                          <div class="row gx-3">
                                              <div class="col-lg-4 col-md-6">
                                                  <div class="mb-3">
                                                    <label class="form-label">Client Name <span class="text-danger">*</span></label>
                                                    <select class="form-select select2" name="client_id" id="client_id">
                                                        <option value="">Select Client</option>
                                                        <?php                                                         
                                                        $result = mysqli_query($conn, "SELECT * FROM client");
                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            echo '<option value="' . $row['id'] . '">' . $row['first_name'] . '</option>';
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
                                                    <label class="form-label">Salesperson </label>
                                                    <select class="form-select select2" name="user_id" id="user_id">
                                                        <option value="">Select Salesperson</option>
                                                        <?php
                                                        $query = "SELECT login.id, login.name FROM login
                                                                  JOIN user_role ON login.role_id = user_role.id
                                                                  WHERE  login.is_deleted = 0
                                                                  ORDER BY login.name ASC";
                                                        $result = mysqli_query($conn, $query);
                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                                                        }
                                                        ?>
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
                                                      $result = mysqli_query($conn, "SELECT * FROM project");
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
                                                            <th>Unit</th>
                                                            <th>Selling Price</th>
                                                            <th>Tax (%)</th>
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
                                                                    <a class="nav-link d-inline-flex align-items-center border fs-12 fw-semibold rounded-2" data-bs-toggle="tab" data-bs-target="#documents" href="javascript:void(0);"><i class="isax isax-bank me-1"></i>Upload Documnets</a>
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
    $(document).ready(function () {
   

    // === Allow only text (no digits) ===
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

});

</script>
    
 <script>
$(document).ready(function() {

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

    // if (!$('#user_id').val()) {
    //   $('#username_error').text('Salesperson is required.');
    //   isValid = false;
    // }
    if (!$('.add-tbody tr').length) {
      $('#product_error').text('Please add at least one product or service');
      isValid = false;
    }
      //  if ($('#project_id').val() === '') {
      //       $('#projectname_error').text('Project is required.');
      //       isValid = false;
      //   }
    if (!isValid) {
      e.preventDefault();
      $('html, body').animate({ scrollTop: $('.error-text:visible').first().offset().top - 100 }, 500);
    }
  });

  // Show selected file counts
  $('#document-upload').on('change', function() {
    const files = this.files;
    const label = files.length === 1 ? files[0].name : (files.length > 1 ? `${files.length} files selected` : '');
    $('#file-count-label').text(label);
  });
 function loadItems(type, target) {
    $.post('process/get_productcategories_by_type.php', { item_type: type }, data => {
      if (target) target.html(data);
      updateItemDropdowns();
    });
  }

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
  // ⚠️ If your input is type="number", it will still COUNT but won't show "$".
  // To display "$", make it type="text".
  attachCurrencyBehavior('#shipping-charge', function(){
    calculateSummary();
  });

  // Initialize shipping field formatting on load
  (function initShipping(){
    const $ship = $('#shipping-charge');
    if ($ship.length) {
      const initVal = unformat($ship.val());
      $ship.data('value', initVal);
      // Only show $ if input type allows text
      if ($ship.attr('type') !== 'number') {
        $ship.val(formatCurrency(initVal));
      } else {
        $ship.val(initVal.toFixed(2)); // keep numeric visible
      }
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
      const taxName = option.data('tax-name') || ''; // Get tax name


      $row.find('.unit-name').val(unit);
      $row.find('.unit-id').val(unitId);
      $row.find('.tax-id').val(taxId);
      $row.find('.tax-name').val(taxName); // Store tax name
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

    $row.find('.amount').data('value', lineTotal).val(formatCurrency(lineTotal));
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
    const taxName = $(this).find('.tax-name').val() || ''; // Get tax name

    const lineSubtotal = p * q;
    const lineTax = (lineSubtotal * t / 100);
    const lineTotal = lineSubtotal + lineTax;

    sub += lineSubtotal;
    grandTotal += lineTotal;

    if (t > 0) {
      const taxKey = `${taxName} (${t}%)`; // Create key with name and rate
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
let taxHtml = "";

$('.add-tbody tr').each(function(index) {
  const p = $(this).find('.selling-price').data('value') || 0;
  const q = unformat($(this).find('.quantity').val());
  const t = $(this).find('.tax-rate').data('value') || 0;
  const taxName = $(this).find('.tax-name').val() || '';

  const lineSubtotal = p * q;
  const lineTax = (lineSubtotal * t / 100);

  if (t > 0 && lineTax > 0) {
    const taxLabel = `${taxName} (${t}%)`;
    taxHtml += `
      <div class="d-flex align-items-center justify-content-between mb-2">
        <h6 class="fs-14 fw-semibold">${taxLabel}</h6>
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
    $row.find('.unit-name, .selling-price, .tax-rate, .amount').val('').removeData('value');
    $row.find('.unit-id, .tax-id').val('');
    calculateSummary();
  }

  // Initial pass
  updateItemDropdowns();
  calculateSummary();

});
</script>
    <script>
    $(document).ready(function () {
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
    });
    </script>
</body>


</html>