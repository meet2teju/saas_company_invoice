<?php
include 'layouts/session.php';
include '../config/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Invalid Quotation ID.";
    $_SESSION['message_type'] = "danger";
    header("Location: quotations.php");
    exit();
}

$quotationId = intval($_GET['id']);

// Fetch quotation info
$sql = "SELECT q.*, c.first_name, c.last_name, c.email, c.customer_image 
        FROM quotation q
        LEFT JOIN client c ON q.client_id = c.id
        WHERE q.id = $quotationId";
$result = mysqli_query($conn, $sql);
$quotation = mysqli_fetch_assoc($result);

$items_result = mysqli_query($conn, "SELECT ii.*, p.name AS product_name,p.code, t.name AS tax_name, u.name AS unit_name, t.rate AS tax_rate
    FROM quotation_item ii
    LEFT JOIN product p ON p.id = ii.product_id
    LEFT JOIN units u ON u.id = ii.unit_id
    LEFT JOIN tax t ON t.id = ii.tax_id
    WHERE ii.quotation_id = $quotationId AND ii.is_deleted = 0");

if (!$quotation) {
    $_SESSION['message'] = "Quotation not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: quotations.php");
    exit();
}

// Fetch company info (Bill From)
$company = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT ci.*, 
           co.name AS country_name,
           s.name AS state_name,
           c.name AS city_name
    FROM company_info ci
    LEFT JOIN countries co ON co.id = ci.country_id
    LEFT JOIN states s ON s.id = ci.state_id
    LEFT JOIN cities c ON c.id = ci.city_id
    LIMIT 1
"));

// Fetch client address
$client_address = null;
if (!empty($quotation['client_id'])) {
    $client_address_query = "
        SELECT ca.*, 
               co.name AS country_name, 
               s.name AS state_name, 
               ci.name AS city_name
        FROM client_address ca
        LEFT JOIN countries co ON co.id = ca.billing_country
        LEFT JOIN states s ON s.id = ca.billing_state
        LEFT JOIN cities ci ON ci.id = ca.billing_city
        WHERE ca.client_id = {$quotation['client_id']}
        LIMIT 1
    ";
    $client_address_result = mysqli_query($conn, $client_address_query);
    $client_address = mysqli_fetch_assoc($client_address_result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    
    <!-- Add the required libraries for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>

<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>

    <div class="page-wrapper">
        <div class="content content-two">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                        <h6>Quotation Detail</h6>
                        <div class="d-flex align-items-center flex-wrap row-gap-3">
                            <a href="javascript:void(0);" onclick="downloadQuotationAsPDF(event)" class="btn btn-outline-white d-inline-flex align-items-center me-3">
                                <i class="isax isax-document-download me-1"></i>Download PDF
                            </a>
                            <a href="process/action_send_quotation_email.php?quotation_id=<?= $quotationId ?>" 
                                class="btn btn-outline-white d-inline-flex align-items-center me-3">
                                <i class="isax isax-message-notif me-1"></i>Send Email
                            </a>
                            <a href="" class="btn btn-outline-white d-inline-flex align-items-center me-3" onclick="window.print(); return false;">
                                <i class="isax isax-printer me-1"></i>Print
                            </a>
                            <a href="#" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="offcanvas" data-bs-target="#quotationDetailsCanvas">
                                <i class="isax isax-eye me-1"></i>View Details
                            </a>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card" id="downloadpdf">
                        <div class="card-body">
                            <div class="bg-light rounded position-relative mb-3">

                                <!-- start row -->
                                <div class="row gy-3 position-relative z-1">
                                    <div class="col-lg-4">
                                        <div>
                                            <h6 class="mb-2 fs-16 fw-semibold">Quotation Details</h6>
                                            <div>
                                                <p class="mb-1">Quotation Number : <span class="text-dark"><?= htmlspecialchars($quotation['quotation_id']) ?></span></p>
                                                <p class="mb-1">Issued On : <span class="text-dark"><?= htmlspecialchars($quotation['quotation_date']) ?></span></p>
                                                <p class="mb-1">Expiry Date : <span class="text-dark"><?= htmlspecialchars($quotation['expiry_date']) ?></span></p>
                                                <p class="mb-1">Reference Name: <span class="text-dark"><?= htmlspecialchars($quotation['reference_name']) ?></span></p>
                                                <?php 
                                                    $status = $quotation['status'] ?? 'Draft';
                                                    $badgeClass = match(strtolower($status)) {
                                                        'accepted' => 'bg-success',
                                                        'sent' => 'bg-info',
                                                        'convert' => 'bg-info',
                                                        'expired' => 'bg-warning text-dark',
                                                        'rejected' => 'bg-danger',
                                                        'cancel' => 'bg-danger',
                                                        'draft' => 'bg-secondary',
                                                        default => 'bg-secondary'
                                                    };
                                                ?>
                                                <p class="mb-1">Status : <span class="badge <?= $badgeClass ?> badge-sm"><?= ucfirst($status) ?></span></p>
                                            </div>
                                        </div>
                                    </div><!-- end col -->
                                    
                                    <div class="col-lg-4">
                                        <div>
                                            <h6 class="mb-2 fs-16 fw-semibold">Billing From</h6>
                                            <div class="bg-white rounded">
                                                <div class="d-flex align-items-center mb-1">
                                                    <div>
                                                        <h6 class="fs-14 fw-semibold"><?= htmlspecialchars($company['name']) ?></h6>
                                                    </div>
                                                </div>
                                                <p class="mb-1"><?= htmlspecialchars($company['address']) ?></p>
                                                <p class="mb-1">
                                                    <?= htmlspecialchars($company['city_name']) ?>, 
                                                    <?= htmlspecialchars($company['state_name']) ?>, 
                                                    <?= htmlspecialchars($company['country_name']) ?>, 
                                                    <?= htmlspecialchars($company['zipcode']) ?>
                                                </p>
                                                <p class="mb-1">Phone : <?= htmlspecialchars($company['mobile_number']) ?></p>
                                                <p class="mb-1">Email : <?= htmlspecialchars($company['email']) ?></p>
                                            </div>
                                        </div>
                                    </div><!-- end col -->

                                    <div class="col-lg-4">
                                        <div>
                                            <h6 class="mb-2 fs-16 fw-semibold">Billing To</h6>
                                            <div class="bg-white rounded">
                                                <div class="d-flex align-items-center mb-1">
                                                    <div>
                                                        <h6 class="fs-14 fw-semibold"><?= htmlspecialchars($quotation['first_name'] . ' ' . $quotation['last_name']) ?></h6>
                                                    </div>
                                                </div>
                                                <?php if ($client_address): ?>
                                                    <p class="mb-1"><?= htmlspecialchars($client_address['billing_address1']) ?></p>
                                                    <p class="mb-1"><?= htmlspecialchars($client_address['city_name']) ?>, <?= htmlspecialchars($client_address['state_name']) ?>, <?= htmlspecialchars($client_address['country_name']) ?>, <?= htmlspecialchars($client_address['billing_pincode']) ?></p>
                                                <?php endif; ?>
                                                <p class="mb-1">Phone : <?= htmlspecialchars($quotation['phone_number'] ?? 'N/A') ?></p>
                                                <p class="mb-1">Email : <?= htmlspecialchars($quotation['email']) ?></p>
                                            </div>
                                        </div>
                                    </div><!-- end col -->
                                </div>
                                <!-- end row -->
                            </div>

                            <div class="mb-3">
                                <h6 class="mb-3">Product / Service Items</h6>
                                <div class="table-responsive rounded border-bottom-0 border table-nowrap">
                                    <table class="table m-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Product/Service</th>
                                                <th>HSN code</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Selling Price</th>
                                                <th>Tax</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $taxSummary = [];
                                            $subtotal = 0;
                                            $i = 1;
                                            
                                            // Reset pointer to beginning for items result
                                            mysqli_data_seek($items_result, 0);
                                            
                                            while ($item = mysqli_fetch_assoc($items_result)) {
                                                $subtotal += $item['amount'];
                                                
                                                // calculate tax for this item
                                                if (!empty($item['tax_rate'])) {
                                                    $lineTax = ($item['amount'] * $item['tax_rate']) / 100;

                                                    // build label like GST (18%)
                                                    $taxKey = $item['tax_name'] . ' (' . $item['tax_rate'] . '%)';

                                                    // add to summary
                                                    if (!isset($taxSummary[$taxKey])) {
                                                        $taxSummary[$taxKey] = 0;
                                                    }
                                                    $taxSummary[$taxKey] += $lineTax;
                                                }
                                            ?>
                                                <tr>
                                                    <td><?= $i++ ?></td>
                                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                                    <td><?= $item['code'] ?></td>
                                                    <td><?= $item['quantity'] ?></td>
                                                    <td><?= htmlspecialchars($item['unit_name']) ?></td>
                                                    <td>$&nbsp;<?= number_format($item['selling_price'], 2) ?></td>
                                                    <td>
                                                        <?= $item['tax_name'] . (!empty($item['tax_rate']) ? ' (' . $item['tax_rate'] . '%)' : '') ?>
                                                    </td>
                                                    <td>$&nbsp;<?= number_format($item['amount'], 2) ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="border-bottom mb-3">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="d-flex align-items-center p-4 mb-3">
                                            <div>
                                                <h6 class="mb-2">Terms & Conditions</h6>
                                                <div>
                                                    <p class="mb-1"><?= htmlspecialchars($quotation['description'] ?? 'No terms specified.') ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- end col -->
                                    <div class="col-lg-6">
                                        <div class="mb-3 p-4">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="fs-14 fw-semibold">Sub Amount</h6>
                                                <h6 class="fs-14 fw-semibold">$ <?= number_format($subtotal, 2) ?></h6>
                                            </div>

                                            <?php 
                                            $totalTax = 0;
                                            if (!empty($taxSummary)): 
                                                foreach ($taxSummary as $taxLabel => $taxAmount): 
                                                    $totalTax += $taxAmount;
                                            ?>
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <h6 class="fs-14 fw-semibold"><?= $taxLabel ?></h6>
                                                    <h6 class="fs-14 fw-semibold">$ <?= number_format($taxAmount, 2) ?></h6>
                                                </div>
                                            <?php 
                                                endforeach; 
                                            endif; 
                                            ?>

                                           <?php if (!empty($quotation['shipping_charge']) && $quotation['shipping_charge'] > 0): ?>
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <h6 class="fs-14 fw-semibold">Shipping Charge</h6>
                                                    <h6 class="fs-14 fw-semibold">$ <?= number_format($quotation['shipping_charge'], 2) ?></h6>
                                                </div>
                                            <?php endif; ?>

                                            <div class="d-flex align-items-center justify-content-between border-top pt-3 mb-3">
                                                <h5 class="fw-bold">Total Amount</h5>
                                                <h5 class="fw-bold">$ <?= number_format($quotation['total_amount'], 2) ?></h5>
                                            </div>

                                            <div class="mt-4">
                                                <h6 class="fs-14 fw-semibold mb-1">Total In Words</h6>
                                                <p class="fst-italic"><?= numberToWords($quotation['total_amount']) ?> Dollars</p>
                                            </div>
                                        </div>
                                    </div><!-- end col -->
                                </div>
                            </div>

                            <!-- start row -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <h6 class="fs-14 fw-semibold mb-1">Notes</h6>
                                            <p><?= htmlspecialchars($quotation['quotation_note'] ?? 'No notes available.') ?></p>
                                        </div>
                                    </div>
                                </div><!-- end col -->
                            </div>
                            <!-- end row -->

                            <div class="mt-4">
                                <h6 class="fw-bold mb-3">Attached Documents</h6>
                                <?php
                                $docs = mysqli_query($conn, "SELECT * FROM quotation_document WHERE quotation_id = $quotationId");
                                if (mysqli_num_rows($docs) > 0) {
                                    while ($doc = mysqli_fetch_assoc($docs)) {
                                        $path = '../uploads' . $doc['document'];
                                        echo "<p><a href='$path' target='_blank' class='btn btn-outline-primary btn-sm me-2 mb-2'><i class='fa fa-file me-1'></i>" . basename($doc['document']) . "</a></p>";
                                    }
                                } else {
                                    echo "<p class='text-muted'>No documents attached</p>";
                                }
                                ?>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
            </div>
            <!-- end row -->
        </div>

        <?php include 'layouts/footer.php'; ?>
    </div>
</div>

<!-- Quotation Details Offcanvas -->
<div class="offcanvas offcanvas-offset offcanvas-end" tabindex="-1" id="quotationDetailsCanvas">                                      
    <div class="offcanvas-header d-block pb-0">
        <div class="border-bottom d-flex align-items-center justify-content-between pb-3">
            <h6 class="offcanvas-title">Quotation Details</h6>
            <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-x"></i></button>
        </div>
    </div>          
    <div class="offcanvas-body pt-3">  
        <form action="process/action_update_quotation_status.php" method="POST" id="quotationStatusForm">
            <input type="hidden" name="quotation_id" value="<?= $quotation['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <span id="statusError" class="text-danger fs-12 d-block mt-1"></span>
                <div class="dropdown">
                    <a href="javascript:void(0);" id="statusDropdownBtn" class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
                         <?= !empty($quotation['status']) ? ucfirst($quotation['status']) : 'Select' ?>
                    </a>
                    <div class="dropdown-menu shadow-lg w-100 dropdown-info">    
                        <ul class="mb-3">
                            <li>
                                <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                    <input class="form-check-input" type="radio" name="status" value="draft" <?= $quotation['status'] === 'draft' ? 'checked' : '' ?>>
                                    <i class="fa-solid fa-circle fs-6 text-secondary me-1"></i>Draft
                                </label>
                            </li>
                            <li>
                                <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                    <input class="form-check-input" type="radio" name="status" value="sent" <?= $quotation['status'] === 'sent' ? 'checked' : '' ?>>
                                    <i class="fa-solid fa-circle fs-6 text-info me-1"></i>Sent
                                </label>
                            </li>
                             <li>
                                <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                    <input class="form-check-input" type="radio" name="status" value="convert" <?= $quotation['status'] === 'convert' ? 'checked' : '' ?>>
                                    <i class="fa-solid fa-circle fs-6 text-info me-1"></i>Convert to Invoice
                                </label>
                            </li>
                            <li>
                                <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                    <input class="form-check-input" type="radio" name="status" value="accepted" <?= $quotation['status'] === 'accepted' ? 'checked' : '' ?>>
                                    <i class="fa-solid fa-circle fs-6 text-success me-1"></i>Accepted
                                </label>
                            </li>
                            <li>
                                <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                    <input class="form-check-input" type="radio" name="status" value="rejected" <?= $quotation['status'] === 'rejected' ? 'checked' : '' ?>>
                                    <i class="fa-solid fa-circle fs-6 text-danger me-1"></i>Rejected
                                </label>
                            </li>
                            <li>
                                <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                    <input class="form-check-input" type="radio" name="status" value="expired" <?= $quotation['status'] === 'expired' ? 'checked' : '' ?>>
                                    <i class="fa-solid fa-circle fs-6 text-warning me-1"></i>Expired
                                </label>
                            </li>
                            <li>
                                <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                    <input class="form-check-input" type="radio" name="status" value="cancel" <?= $quotation['status'] === 'cancel' ? 'checked' : '' ?>>
                                    <i class="fa-solid fa-circle fs-6 text-warning me-1"></i>Cancelled
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="offcanvas-footer">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="javascript:location.reload();" class="btn btn-outline-white w-100">Reset</a>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary w-100" id="filter-submit">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Convert to Invoice Confirmation Modal -->
<div class="modal fade" id="convertToInvoiceModal" tabindex="-1" aria-labelledby="convertToInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="convertToInvoiceModalLabel">Convert to Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to convert this quotation to an invoice?</p>
                <p class="text-muted">This action will:</p>
                <ul class="text-muted">
                    <li>Create a new invoice with the quotation details</li>
                    <li>Copy all items and documents</li>
                    <li>Remove the quotation from the quotations list</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="process/action_update_quotation_status.php" method="POST" id="convertToInvoiceForm">
                    <input type="hidden" name="quotation_id" value="<?= $quotation['id'] ?>">
                    <input type="hidden" name="status" value="convert">
                    <button type="submit" class="btn btn-primary">Convert to Invoice</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/vendor-scripts.php'; ?>

<script>
// Function to download quotation as PDF
function downloadQuotationAsPDF(event) {
    const element = document.getElementById('downloadpdf');
    const loadingBtn = event.currentTarget;
    const originalText = loadingBtn.innerHTML;
    loadingBtn.innerHTML = 'Converting...';
    loadingBtn.disabled = true;
    
    html2canvas(element, {
        scale: 2,
        useCORS: true,
        logging: true,
        backgroundColor: '#ffffff'
    }).then(function(canvas) {
        const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
        const imgData = canvas.toDataURL('image/png');
        const imgWidth = pdf.internal.pageSize.getWidth();
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        
        pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
        pdf.save('quotation-<?= $quotation['quotation_id'] ?>.pdf');
        
        loadingBtn.innerHTML = originalText;
        loadingBtn.disabled = false;
    }).catch(function(error) {
        console.error('Error generating PDF:', error);
        alert('Error generating PDF. Please try again.');
        loadingBtn.innerHTML = originalText;
        loadingBtn.disabled = false;
    });
    
    if (event) {
        event.preventDefault();
    }
    return false;
}

// Status dropdown functionality
function updateDropdownBtn() {
    let selected = document.querySelector("input[name='status']:checked");
    let btn = document.getElementById("statusDropdownBtn");

    if (selected) {
        let labelText = selected.closest("label").innerText.trim();
        btn.textContent = labelText;  
    }
}

// === When user changes status ===
document.querySelectorAll("input[name='status']").forEach(function(radio) {
    radio.addEventListener("change", function() {
        updateDropdownBtn();
        
        // If "Convert to Invoice" is selected, show confirmation modal
        if (this.value === 'convert') {
            // Uncheck the radio button temporarily
            this.checked = false;
            updateDropdownBtn();
            
            // Show confirmation modal
            var convertModal = new bootstrap.Modal(document.getElementById('convertToInvoiceModal'));
            convertModal.show();
        }
    });
});

// === On page load (already saved status) ===
window.addEventListener("DOMContentLoaded", updateDropdownBtn);

// === Validation on submit (for non-convert statuses) ===
document.getElementById("quotationStatusForm").addEventListener("submit", function(e) {
    let statusChecked = document.querySelector("input[name='status']:checked");
    let errorSpan = document.getElementById("statusError");

    // Don't validate if it's convert status (handled by modal)
    if (statusChecked && statusChecked.value === 'convert') {
        e.preventDefault();
        return;
    }

    if (!statusChecked) {
        e.preventDefault();
        errorSpan.textContent = "Please select a status.";
    } else {
        errorSpan.textContent = "";
    }
});

// Handle modal form submission
document.getElementById('convertToInvoiceForm').addEventListener('submit', function() {
    // Close the modal
    var modal = bootstrap.Modal.getInstance(document.getElementById('convertToInvoiceModal'));
    modal.hide();
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Converting...';
    submitBtn.disabled = true;
});
</script>

</body>
</html>