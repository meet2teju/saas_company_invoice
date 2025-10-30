<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

// Validate and sanitize the ID
$invoice_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($invoice_id <= 0) {
    $_SESSION['error'] = "Invalid invoice ID.";
    header("Location: invoices.php");
    exit();
}

// Fetch invoice data
$query = "SELECT * FROM invoice WHERE id = $invoice_id";
$result = mysqli_query($conn, $query);

// Check if invoice exists
if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Invoice not found.";
    header("Location: invoices.php");
    exit();
}

$row = mysqli_fetch_assoc($result);

// Fetch related data
$clients = mysqli_query($conn, "SELECT id, first_name FROM client WHERE is_deleted = 0");
$users = mysqli_query($conn,  "SELECT login.id, login.name FROM login
    JOIN user_role ON login.role_id = user_role.id
    WHERE   login.is_deleted = 0
    ORDER BY login.name ASC");
$documents = mysqli_query($conn, "SELECT id, document FROM invoice_document WHERE invoice_id = $invoice_id AND is_deleted = 0");

// Radio precheck
$is_product = ($row['item_type'] == 1) ? 'checked' : '';
$is_service = ($row['item_type'] == 0) ? 'checked' : '';

// Get project and task data for pre-selection
$project_id = $row['project_id'] ?? 0;
$task_ids = [];
if ($project_id > 0) {
    // First check if invoice_tasks table exists, if not create it
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'invoice_tasks'");
    if (mysqli_num_rows($check_table) == 0) {
        $create_table = "CREATE TABLE invoice_tasks (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            invoice_id INT(11) NOT NULL,
            task_id INT(11) NOT NULL,
            org_id BIGINT(20) NULL,
            is_deleted TINYINT(1) DEFAULT 0,
            created_by BIGINT(20) NULL,
            updated_by BIGINT(20) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        mysqli_query($conn, $create_table);
    }
    
    $task_query = "SELECT task_id FROM invoice_tasks WHERE invoice_id = $invoice_id AND is_deleted = 0";
    $task_result = mysqli_query($conn, $task_query);
    if ($task_result) {
        while ($task = mysqli_fetch_assoc($task_result)) {
            $task_ids[] = $task['task_id'];
        }
    }
    
    // FIX: Update radio button selection based on tasks
    if (!empty($task_ids)) {
        $is_service = 'checked';
        $is_product = '';
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
                                <h6>Edit Invoice</h6>
                                <a href="invoice-details.php?id=<?= $invoice_id ?>" class="btn btn-outline-white d-inline-flex align-items-center">
                                    <i class="isax isax-eye me-1"></i>Preview
                                </a>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form action="process/action_edit_invoice.php" method="POST" enctype="multipart/form-data" id="form">
                                           <input type="hidden" name="id" value="<?= $invoice_id ?>">
                                        <div class="border-bottom mb-3 pb-1">
                                          <div class="row gx-3">
                                            <div class="col-lg-4 col-md-6">
                                              <div class="mb-3">
                                                  <label class="form-label">Client Name<span class="text-danger">*</span></label>
                                                    <select class="form-select select2" name="client_id" id="client_id" >
                                                  <option value="">Select Client</option>
                                                  <?php while ($client = mysqli_fetch_assoc($clients)) {
                                                  $selected = ($client['id'] == $row['client_id']) ? 'selected' : '';
                                                  echo "<option value='{$client['id']}' $selected>{$client['first_name']}</option>";
                                              } ?> 
                                              </select>
                                              <span class="text-danger error-text" id="clientname_error"></span>
                                              </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                              <div class="mb-3">
                                                <label class="form-label">Project<span class="text-danger">*</span></label>
                                                <select class="form-select select2" name="project_id" id="project_id" <?= $project_id ? '' : 'disabled' ?>>
                                                  <option value="">Select Project</option>
                                                  <?php
                                                  if ($row['client_id']) {
                                                      $client_projects = mysqli_query($conn, "SELECT * FROM project WHERE client_id = {$row['client_id']} AND is_deleted = 0");
                                                      while ($project = mysqli_fetch_assoc($client_projects)) {
                                                          $selected = ($project['id'] == $project_id) ? 'selected' : '';
                                                          echo "<option value='{$project['id']}' $selected>{$project['project_name']}</option>";
                                                      }
                                                  }
                                                  ?>
                                                </select>
                                                <span class="text-danger error-text" id="project_error"></span>
                                              </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                              <div class="mb-3">
                                                <label class="form-label">Tasks<span class="text-danger">*</span></label>
                                                <select class="form-select select2" name="task_id[]" id="task_id" multiple="multiple" <?= $project_id ? '' : 'disabled' ?>>
                                                  <option value="">Select Tasks</option>
                                                  <?php
                                                  if ($project_id) {
                                                      // FIX: Removed status filter to show all tasks
                                                      $project_tasks = mysqli_query($conn, "SELECT * FROM project_task WHERE project_id = $project_id AND is_deleted = 0");
                                                      while ($task = mysqli_fetch_assoc($project_tasks)) {
                                                          $selected = in_array($task['id'], $task_ids) ? 'selected' : '';
                                                          echo "<option value='{$task['id']}' $selected>{$task['task_name']} ({$task['hour']} hours)</option>";
                                                      }
                                                  }
                                                  ?>
                                                </select>
                                                <span class="text-danger error-text" id="task_error"></span>
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
                                                <label class="form-label">Order Number</label>
                                                <input type="number" class="form-control" name="order_number" value="<?= htmlspecialchars($row['order_number']) ?>">
                                              </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                              <div class="mb-3">
                                                  <label class="form-label">Invoice Number</label>
                                                  <input type="text" class="form-control" name="invoice_id" value="<?= htmlspecialchars($row['invoice_id']) ?>" readonly >
                                              </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                              <div class="mb-3">
                                                  <label class="form-label">Salesperson<span class="text-danger">*</span></label>
                                                    <select class="form-select select2" name="user_id" id="user_id">
                                                  <option value="">Select Salesperson</option>
                                                  <?php 
                                                  // Reset users pointer and loop through
                                                  mysqli_data_seek($users, 0);
                                                  while ($user = mysqli_fetch_assoc($users)) {
                                                      $selected = ($user['id'] == $row['user_id']) ? 'selected' : '';
                                                      echo "<option value='{$user['id']}' $selected>{$user['name']}</option>";
                                                  } ?>
                                                </select>
                                                <span class="text-danger error-text" id="username_error"></span>

                                              </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <label class="form-label">Invoice Date<span class="text-danger">*</span></label>
                                                <div class="input-group position-relative mb-3">
                                                    <input type="text" class="form-control datepicker"id="invoice_date" name="invoice_date" value="<?= htmlspecialchars($row['invoice_date']) ?>">
                                                    <span class="input-icon-addon fs-16 text-gray-9">
                                                      <i class="isax isax-calendar-2"></i>
                                                    </span>
                                                </div>
                                                <span class="text-danger error-text" id="invoice_date_error"></span>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                              <label class="form-label">Invoice Due Date<span class="text-danger">*</span></label>
                                              <div class="input-group position-relative mb-3">
                                                  <input type="text" class="form-control datepicker" id="due_date" name="due_date" value="<?= htmlspecialchars($row['due_date']) ?>">
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
        <thead class="table-dark" id="table-heading">
            <!-- Dynamic headings will be inserted here by JavaScript -->
            <tr>
                <th>Product/Service</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Selling Price</th>
                <th>Tax</th>
                <th>Amount</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="add-tbody" id="product">
            <span class="text-danger error-text" id="product_error"></span>

            <?php
            $invoice_id = $_GET['id'];
            $item_query = "SELECT 
                                qi.*, 
                                IFNULL(p.name, '') AS product_name, 
                                IFNULL(u.name, '') AS unit_name,
                                IFNULL(t.rate, 0) AS tax_rate,
                                IFNULL(t.name, '') AS tax_name,
                                IFNULL(t.id, 0) AS tax_id,
                                IFNULL(qi.selling_price, 0) AS item_selling_price,
                                IFNULL(p.unit_id, 0) AS unit_id,
                                IFNULL(p.item_type, 0) AS item_type
                            FROM invoice_item qi
                            LEFT JOIN product p ON qi.product_id = p.id
                            LEFT JOIN units u ON p.unit_id = u.id
                            LEFT JOIN tax t ON p.tax_id = t.id
                            WHERE qi.invoice_id = $invoice_id AND qi.is_deleted = 0";

            $item_result = mysqli_query($conn, $item_query);

            while ($item = mysqli_fetch_assoc($item_result)) {
                // Use the selling price from invoice_item table
                $qty      = (float)($item['quantity'] ?? 0);
                $price    = (float)($item['item_selling_price'] ?? 0);
                $taxRate  = (float)($item['tax_rate'] ?? 0);
                $taxName  = htmlspecialchars($item['tax_name'] ?? '');
                $unitName = htmlspecialchars($item['unit_name'] ?? '');
                $unitId   = (int)($item['unit_id'] ?? 0);
                $taxId    = (int)($item['tax_id'] ?? 0);

                $lineSubtotal = $qty * $price;
                $lineTax      = $lineSubtotal * $taxRate / 100;
                $amount       = $lineSubtotal + $lineTax;
                ?>
                <tr>
                    <td>
                        <select class="form-select item-select" name="product_id[]">
                            <option value="">Select Product/Service</option>
                            <?php
                            $product_query = "SELECT 
                                                p.id, 
                                                p.name, 
                                                IFNULL(p.selling_price, 0) AS selling_price, 
                                                IFNULL(p.unit_id, 0) AS unit_id, 
                                                IFNULL(u.name, '') AS unit_name, 
                                                IFNULL(t.id, 0) AS tax_id, 
                                                IFNULL(t.rate, 0) AS tax_rate, 
                                                IFNULL(t.name, '') AS tax_name
                                              FROM product p
                                              LEFT JOIN units u ON p.unit_id = u.id
                                              LEFT JOIN tax t ON p.tax_id = t.id
                                              WHERE p.item_type = {$item['item_type']} AND p.is_deleted = 0";
                            $product_result = mysqli_query($conn, $product_query);

                            while ($product = mysqli_fetch_assoc($product_result)) {
                                $selected = ($product['id'] == $item['product_id']) ? 'selected' : '';
                                echo '<option value="' . $product['id'] . '" 
                                        data-unit="' . htmlspecialchars($product['unit_name']) . '"
                                        data-unit-id="' . $product['unit_id'] . '"
                                        data-price="' . $product['selling_price'] . '"
                                        data-tax="' . $product['tax_rate'] . '"
                                        data-tax-name="' . htmlspecialchars($product['tax_name']) . '"
                                        data-tax-id="' . $product['tax_id'] . '" ' . $selected . '>' . 
                                        htmlspecialchars($product['name']) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control quantity" name="quantity[]" 
                               value="<?= $qty ?>" min="1">
                    </td>
                    <td>
                        <input type="text" class="form-control unit-name" 
                               value="<?= $unitName ?>" readonly>
                        <input type="hidden" class="unit-id" name="unit_id[]" 
                               value="<?= $unitId ?>">
                    </td>
                    <td>
                        <input type="text" class="form-control selling-price" 
                               name="selling_price[]" 
                               value="<?= $price > 0 ? '$ ' . number_format($price, 2) : '$ 0.00' ?>" 
                               data-value="<?= $price ?>" readonly>
                    </td>
                    <td>
                        <input type="text" class="form-control tax-display" 
                               value="<?= $taxRate > 0 ? number_format($taxRate, 2) . '%' : '0.00 %' ?>" 
                               data-value="<?= $taxRate ?>" readonly>
                        <input type="hidden" class="tax-id" name="tax_id[]" value="<?= $taxId ?>">
                        <input type="hidden" class="tax-name" value="<?= $taxName ?>">
                    </td>
                    <td>
                        <input type="text" class="form-control amount-display" 
                               value="<?= '$ ' . number_format($amount, 2) ?>" 
                               data-value="<?= $amount ?>" readonly>
                        <input type="hidden" class="amount-storage" name="amount[]" value="<?= $amount ?>">
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="text-danger remove-table">
                            <i class="isax isax-close-circle"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

                                            </div>
                                            <div>
                                                <a href="javascript:void(0);" class="d-inline-flex align-items-center add-invoice-data"><i class="isax isax-add-circle5 text-primary me-1"></i>Add New</a>
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
                                                                    <a class="nav-link d-inline-flex align-items-center border fs-12 fw-semibold rounded-2" data-bs-toggle="tab" data-bs-target="#bank" href="javascript:void(0);"><i class="isax isax-bank me-1"></i>Bank Details</a>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <a class="nav-link d-inline-flex align-items-center border fs-12 fw-semibold rounded-2" data-bs-toggle="tab" data-bs-target="#documents" href="javascript:void(0);"><i class="isax isax-bank me-1"></i>Upload Documents</a>
                                                                </li>
                                                            </ul>
                                                            <div class="tab-content">
                                                                <div class="tab-pane active show" id="notes" role="tabpanel">
                                                                    <label class="form-label">Additional Notes</label>
                                                                    <textarea class="form-control" name="invoice_note"><?= htmlspecialchars($row['invoice_note']) ?></textarea>
                                                                </div>
                                                                <div class="tab-pane fade" id="terms" role="tabpanel">
                                                                    <label class="form-label">Terms & Conditions</label>
                                                                    <textarea class="form-control" name="description"><?= htmlspecialchars($row['description']) ?></textarea>
                                                                </div>
                                                                <div class="tab-pane fade" id="bank" role="tabpanel">
                                                                    <label class="form-label">Account<span class="text-danger">*</span></label>
                                                                   <select class="select2" name="bank_id" id="bank_id">
                                                                        <option value="">Select Account</option>
                                                                        <?php
                                                                        $invoiceBankId = $row['bank_id'] ?? 0;
                                                                            $bankResult = mysqli_query($conn, "SELECT * FROM bank WHERE status = 1");
                                                                                while ($bank = mysqli_fetch_assoc($bankResult)) {
                                                                                    $selected = ($bank['id'] == $invoiceBankId) ? 'selected' : '';
                                                                                    echo '<option value="' . $bank['id'] . '" ' . $selected . '>'
                                                                                        . htmlspecialchars($bank['account_holder']) . ' - '
                                                                                        . htmlspecialchars($bank['account_number']) . ' ('
                                                                                        . htmlspecialchars($bank['bank_name']) . ')</option>';
                                                                                }
                                                                        ?>
                                                                    </select>
                                                                                <span class="text-danger error-text" id="invoice_account_error"></span> 
                                                                </div>
                                                                <div class="tab-pane fade" id="documents" role="tabpanel">
                                                                    <div class="file-upload drag-file w-100 h-auto py-3 d-flex align-items-center justify-content-center flex-column">
                                                                        <span class="upload-img d-block"><i class="isax isax-image text-primary me-1"></i>Upload Documents</span>
                                                                        <input type="file" class="form-control" name="document[]" id="document-upload" multiple>
                                                                        <span id="file-count-label" class="mt-2 text-muted"></span>
                                                                    </div>
                                                                    <span id="document_error" class="text-danger error-text"></span>
                                                                    
                                                                    <?php 
                                                                    // Reset and check if we have documents
                                                                    if ($documents && mysqli_num_rows($documents) > 0): ?>
                                                                        <div class="mt-3 w-100">
                                                                            <label class="form-label">Uploaded Documents:</label>
                                                                            <ul class="list-group">
                                                                                <?php 
                                                                                // Reset pointer and loop through documents
                                                                                mysqli_data_seek($documents, 0);
                                                                                while ($doc = mysqli_fetch_assoc($documents)): 
                                                                                ?>
                                                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                        <a href="../uploads/<?= htmlspecialchars($doc['document']) ?>" target="_blank" class="text-truncate" style="max-width: 70%;">
                                                                                            <i class="isax isax-document me-2"></i>
                                                                                            <?= htmlspecialchars($doc['document']) ?>
                                                                                        </a>
                                                                                        <a href="process/delete_document.php?id=<?= $doc['id'] ?>&invoice_id=<?= $invoice_id ?>" 
                                                                                           class="text-danger delete-document" 
                                                                                           title="Delete document">
                                                                                            <i class="isax isax-trash"></i>
                                                                                        </a>
                                                                                    </li>
                                                                                <?php endwhile; ?>
                                                                            </ul>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div class="mt-3">
                                                                            <p class="text-muted">No documents uploaded yet.</p>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!-- end col -->
                                                <div class="col-lg-5">
                                                <input type="hidden" name="sub_amount" id="subtotal-amount-field" value="<?= $row['amount'] ?>">
                                                <input type="hidden" name="tax_amount" id="tax-amount-field" value="<?= $row['tax_amount'] ?>">
                                                <input type="hidden" name="total_amount" id="total-amount-field" value="<?= $row['total_amount'] ?>">

                                                    <div class="mb-3">
                                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                                            <h6 class="fs-14 fw-semibold">Amount</h6>
                                                            <h6 class="fs-14 fw-semibold" id="subtotal-amount">$ <?= number_format($row['amount'] ?? 0, 2) ?></h6>
                                                        </div>
                                                       <div class="tax-details mb-3">
                                                            <!-- JS will populate tax per rate here -->
                                                        </div>
                                                        <div id="shipping-charge-group" class="d-flex align-items-center justify-content-between mb-3" style="display: none;">
                                                            <h6 class="fs-14 fw-semibold mb-0">Shipping Charge</h6>
                                                            <input type="text" class="form-control" id="shipping-charge" name="shipping_charge" value="<?= '$' . number_format($row['shipping_charge'] ?? 0, 2) ?>">
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                                            <h6>Total</h6>
                                                            <h6 id="total-amount">$ <?= number_format($row['total_amount'] ?? 0, 2) ?></h6>
                                                        </div>
                                                    </div>
                                                </div><!-- end col -->
                                            </div>
											                <!-- end row -->

                                        </div>

                                        <div class="d-flex align-items-center justify-content-between">
                                        <button type="button" class="btn btn-outline-white" onclick="window.location.href='invoices.php'">Cancel</button>
                                            <button type="submit" name="submit" value="1" class="btn btn-primary">Save</button>
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
  // Initialize datepicker
  $('.datepicker').flatpickr({
    dateFormat: "Y-m-d",
    allowInput: true,
    defaultDate: new Date(),
    clickOpens: true
  });

  /* =========================
     Table Heading Management
  ========================== */
  function updateTableHeading(itemType) {
    const $tableHead = $('#table-heading');
    
    if (itemType === '0') { // Service
      $tableHead.html(`
        <tr>
          <th>Service</th>
          <th>Hours</th>
          <th>Unit</th>
          <th>Hourly Price</th>
          <th>Tax</th>
          <th>Amount</th>
          <th></th>
        </tr>
      `);
    } else { // Product
      $tableHead.html(`
        <tr>
          <th>Product/Service</th>
          <th>Quantity</th>
          <th>Unit</th>
          <th>Selling Price</th>
          <th>Tax</th>
          <th>Amount</th>
          <th></th>
        </tr>
      `);
    }
  }

  // Initialize table heading based on current radio selection
  const initialItemType = $('input[name="item_type"]:checked').val();
  updateTableHeading(initialItemType);

  // If tasks are pre-selected, ensure service radio is checked
  const selectedTasks = $('#task_id').val();
  if (selectedTasks && selectedTasks.length > 0) {
      $('#Radio-service').prop('checked', true).trigger('change');
  }

  /* =========================
     Initialize existing rows data
  ========================== */
  function initializeExistingRowsData() {
    $('.add-tbody tr').each(function(index) {
      const $row = $(this);
      
      // Selling Price
      const $sellingPrice = $row.find('.selling-price');
      let sellingPriceVal = $sellingPrice.attr('data-value');
      if (!sellingPriceVal || sellingPriceVal === '0') {
        sellingPriceVal = unformat($sellingPrice.val());
      }
      $sellingPrice.data('value', parseFloat(sellingPriceVal) || 0);
      
      // Tax Display
      const $taxDisplay = $row.find('.tax-display');
      let taxVal = $taxDisplay.attr('data-value');
      if (!taxVal || taxVal === '0') {
        taxVal = unformat($taxDisplay.val());
      }
      $taxDisplay.data('value', parseFloat(taxVal) || 0);
      
      // Amount Display
      const $amountDisplay = $row.find('.amount-display');
      let amountVal = $amountDisplay.attr('data-value');
      if (!amountVal || amountVal === '0') {
        amountVal = unformat($amountDisplay.val());
      }
      $amountDisplay.data('value', parseFloat(amountVal) || 0);
      
      // Quantity
      const $quantity = $row.find('.quantity');
      $quantity.data('value', parseFloat($quantity.val()) || 1);
    });
  }

  /* =========================
     Project and Task Selection
  ========================== */
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
        data: { client_id: clientId },
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
        data: { project_id: projectId },
        success: function(data) {
          $('#task_id').html(data);
          // Re-initialize select2 for multi-select
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
      // Clear existing task rows first (but keep product rows if any)
      $('.add-tbody tr').each(function() {
        const $row = $(this);
        const productId = $row.find('select[name="product_id[]"]').val();
        if (productId && productId.toString().startsWith('task_')) {
          $row.remove();
        }
      });
      
      // Set item type to Service when tasks are selected
      $('#Radio-service').prop('checked', true).trigger('change');
      
      // Load details for each selected task
      let processedTasks = 0;
      selectedTasks.forEach(function(taskId) {
        if (taskId) {
          $.ajax({
            url: 'process/get_task_details.php',
            type: 'POST',
            data: { task_id: taskId },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                // Add task as an invoice item
                const rowId = 'row_task_' + taskId;
                const newRow = `
                <tr id="${rowId}">
                    <td>
                        <input type="text" class="form-control" value="${response.task_name}" readonly>
                        <input type="hidden" name="product_id[]" value="task_${taskId}">
                        <input type="hidden" name="item_type[]" value="0">
                    </td>
                    <td>
                        <input type="number" class="form-control quantity" name="quantity[]" value="${response.hours}" min="1" readonly>
                    </td>
                    <td>
                        <input type="text" class="form-control unit-name" value="Hours" readonly>
                        <input type="hidden" class="unit-id" name="unit_id[]" value="0">
                    </td>
                    <td>
                        <input type="text" class="form-control selling-price" name="selling_price[]" value="${response.rate_per_hour}" data-value="${response.rate_per_hour}" readonly>
                    </td>
                    <td>
                        <input type="text" class="form-control tax-display" value="0.00%" data-value="0" readonly>
                        <input type="hidden" class="tax-id" name="tax_id[]" value="0">
                        <input type="hidden" class="tax-name" value="">
                    </td>
                    <td>
                        <input type="text" class="form-control amount-display" value="${response.total_amount}" data-value="${response.total_amount}" readonly>
                        <input type="hidden" class="amount-storage" name="amount[]" value="${response.total_amount}">
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="remove-table"><i class="isax isax-trash text-danger"></i></a>
                    </td>
                </tr>`;
                
                $('.add-tbody').append(newRow);
                
                processedTasks++;
                // Update summary after all tasks are processed
                if (processedTasks === selectedTasks.length) {
                  calculateSummary();
                }
              }
            },
            error: function() {
              console.log('Error loading task details for task ID: ' + taskId);
              processedTasks++;
              if (processedTasks === selectedTasks.length) {
                calculateSummary();
              }
            }
          });
        }
      });
    } else {
      // If no tasks selected, remove all task rows
      $('.add-tbody tr').each(function() {
        const $row = $(this);
        const productId = $row.find('select[name="product_id[]"]').val();
        if (productId && productId.toString().startsWith('task_')) {
          $row.remove();
        }
      });
      calculateSummary();
    }
  });

  /* =========================
     Helpers: format / unformat
  ========================== */
  function formatCurrency(value) {
    const n = parseFloat(value);
    if (isNaN(n)) return '$ 0.00';
    return `$ ${n.toFixed(2)}`;
  }

  function formatPercent(value) {
    const n = parseFloat(value);
    if (isNaN(n)) return '0.00%';
    return `${n.toFixed(2)}%`;
  }

  function unformat(value) {
    if (typeof value !== 'string') value = String(value);
    const n = parseFloat(value.replace(/[^\d.-]/g, ''));
    return isNaN(n) ? 0 : n;
  }

  /* =========================
     Radio button change handler
  ========================== */
  $(document).on('change', 'input[name="item_type"]', function() {
    const selectedType = $(this).val();
    
    // Update table heading based on selected type
    updateTableHeading(selectedType);
    
    updateAllItemDropdowns(selectedType);
  });

  function updateAllItemDropdowns(itemType) {
    $('.item-select').each(function() {
      const currentVal = $(this).val();
      loadItems(itemType, $(this));
      
      if (currentVal) {
        setTimeout(() => {
          if ($(this).find('option[value="' + currentVal + '"]').length > 0) {
            $(this).val(currentVal).trigger('change');
          } else {
            resetRow($(this).closest('tr'));
          }
        }, 100);
      }
    });
  }

  /* =========================
     Items dropdown utilities
  ========================== */
  function loadItems(type, target) {
    $.post('process/get_productcategories_by_type.php', { item_type: type }, function(data) {
      if (target) {
        target.html(data);
        updateItemDropdowns();
      }
    }).fail(function() {
      if (target) {
        target.html('<option value="">Error loading items</option>');
      }
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
     Add new row functionality
  ========================== */
  function addNewRow() {
    const itemType = $('input[name="item_type"]:checked').val();
    const rowId = 'row_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    
    const newRow = `
    <tr id="${rowId}">
        <td>
            <select class="form-select item-select" name="product_id[]" required>
                <option value="">Loading...</option>
            </select>
            <input type="hidden" class="unit-id" name="unit_id[]">
            <input type="hidden" class="tax-id" name="tax_id[]">
            <input type="hidden" class="tax-name" name="tax_name[]">
        </td>
        <td>
            <input type="number" class="form-control quantity" name="quantity[]" value="1" min="1" required>
        </td>
        <td>
            <input type="text" class="form-control unit-name" name="unit_name[]" readonly>
        </td>
        <td>
            <input type="text" class="form-control selling-price" name="selling_price[]" readonly>
        </td>
        <td>
            <input type="text" class="form-control tax-display" readonly>
            <input type="hidden" class="tax-id" name="tax_id[]">
            <input type="hidden" class="tax-name" name="tax_name[]">
        </td>
        <td>
            <input type="text" class="form-control amount-display" readonly>
            <input type="hidden" class="amount-storage" name="amount[]">
        </td>
        <td>
            <a href="javascript:void(0);" class="remove-table"><i class="isax isax-trash text-danger"></i></a>
        </td>
    </tr>`;
    
    $('.add-tbody').append(newRow);
    
    const $select = $('#' + rowId + ' .item-select');
    loadItems(itemType, $select);
  }

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
      $row.find('.tax-display').data('value', tax).val(formatPercent(tax));

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
     Add new row click handler
  ========================== */
  $(document).on('click', '.add-invoice-data', function() {
    addNewRow();
  });

  /* =========================
     Calculations
  ========================== */
  function calculateRow($row) {
    const qty  = parseFloat($row.find('.quantity').val()) || 0;
    const price = $row.find('.selling-price').data('value') || 0;
    const tax   = $row.find('.tax-display').data('value') || 0;

    const lineSubtotal = qty * price;
    const lineTax = lineSubtotal * (tax / 100);
    const lineTotal = lineSubtotal + lineTax;

    $row.find('.amount-display').data('value', lineTotal).val(formatCurrency(lineTotal));
    $row.find('.amount-storage').val(lineTotal.toFixed(2));
    calculateSummary();
  }

  function calculateSummary() {
    let sub = 0, grandTotal = 0, totalTax = 0;
    let taxGroups = {};

    $('.add-tbody tr').each(function(index) {
      const price = $(this).find('.selling-price').data('value') || 0;
      const qty = parseFloat($(this).find('.quantity').val()) || 0;
      const taxRate = $(this).find('.tax-display').data('value') || 0;
      const taxName = $(this).find('.tax-name').val() || 'Tax';

      const lineSubtotal = price * qty;
      const lineTax = (lineSubtotal * taxRate / 100);
      const lineTotal = lineSubtotal + lineTax;

      sub += lineSubtotal;
      grandTotal += lineTotal;
      totalTax += lineTax;

      if (taxRate > 0 && lineTax > 0) {
        const taxKey = `${taxName} (${taxRate}%)`;
        if (!taxGroups[taxKey]) taxGroups[taxKey] = 0;
        taxGroups[taxKey] += lineTax;
      }
    });

    let taxHtml = "";
    $.each(taxGroups, function(taxLabel, amount) {
      taxHtml += `
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="fs-14 fw-semibold">${taxLabel}</h6>
          <h6 class="fs-14 fw-semibold">${formatCurrency(amount)}</h6>
        </div>`;
    });

    $('.tax-details').html(taxHtml);
    $('#subtotal-amount').text(formatCurrency(sub));
    $('#total-amount').text(formatCurrency(grandTotal));

    $('#subtotal-amount-field').val(sub.toFixed(2));
    $('#tax-amount-field').val(totalTax.toFixed(2));
    $('#total-amount-field').val(grandTotal.toFixed(2));
  }

  function resetRow($row) {
    $row.find('.quantity').val(1);
    $row.find('.unit-name, .selling-price, .tax-display, .amount-display, .tax-name').val('').removeData('value');
    $row.find('.unit-id, .tax-id').val('');
    $row.find('.amount-storage').val('0');
    calculateSummary();
  }

  /* =========================
     FORM VALIDATION
  ========================== */
  $('#form').on('submit', function(e) {
    let isValid = true;
    $('.error-text').text('');
    let firstErrorTab = null;

    if (!$('#client_id').val()) {
      $('#clientname_error').text('Client is required.');
      isValid = false;
    }
    if (!$('#project_id').val()) {
      $('#project_error').text('Project is required.');
      isValid = false;
    }
    if (!$('#task_id').val() || $('#task_id').val().length === 0) {
      $('#task_error').text('At least one task is required.');
      isValid = false;
    }
    if (!$('#invoice_date').val()) {
      $('#invoice_date_error').text('Invoice Date is required.');
      isValid = false;
    }
    if (!$('#user_id').val()) {
      $('#username_error').text('Salesperson is required.');
      isValid = false;
    }
    if (!$('#due_date').val()) {
      $('#invoice_due_error').text('Due Date is required.');
      isValid = false;
    }
    if (!$('#bank_id').val()) {
      $('#invoice_account_error').text('Account is required.');
      isValid = false;
      firstErrorTab = firstErrorTab || '#bank';
    }
    if (!$('.add-tbody tr').length) {
      $('#product_error').text('Please add at least one product or service');
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
      if (firstErrorTab) {
        $('a[data-bs-toggle="tab"][data-bs-target="' + firstErrorTab + '"]').tab('show');
      }
      $('html, body').animate({ scrollTop: $('.error-text:visible').first().offset().top - 100 }, 500);
      return;
    }
  });

  /* =========================
     Client info fetch
  ========================== */
  function fetchClientInfo(clientId) {
    if (clientId) {
      $.ajax({
        url: 'process/fetch_client_full_info.php',
        type: 'POST',
        data: { client_id: clientId },
        dataType: 'json',
        success: function(response) {
          $('#client_info_block').html(response.billing_html);
          $('#shipping_info_block').html(response.shipping_html);
        },
        error: function(xhr, status, error) {
          console.error(error);
        }
      });
    } else {
      $('#client_info_block, #shipping_info_block').empty();
    }
  }

  $('#client_id').on('select2:select', function(e) {
    fetchClientInfo($(this).val());
  });

  // Fetch client info on page load if client is selected
  const preselectedClient = $('#client_id').val();
  if (preselectedClient) {
    fetchClientInfo(preselectedClient);
  }

  /* =========================
     Upload label
  ========================== */
  $('#document-upload').on('change', function() {
    const files = this.files;
    const label = files.length === 1 ? files[0].name : (files.length > 1 ? `${files.length} files selected` : '');
    $('#file-count-label').text(label);
  });

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
        val = parts[0] + '.' + parts[1];
    }
    this.value = val;
  });

  /* =========================
     Initialize on page load
  ========================== */
  // Initialize calculations for existing rows
  initializeExistingRowsData();
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