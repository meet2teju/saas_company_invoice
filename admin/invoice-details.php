<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

// Get current organization ID
$org_id = $_SESSION['org_id'] ?? 1;

$invoice_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($invoice_id <= 0) {
    die('Invalid Invoice ID!');
}

// Get company currency with organization condition
$companyCurrency = getCompanyCurrency($conn, $org_id);

// Get all currencies for dropdown
$currency_query = "SELECT * FROM currency ORDER BY currency_name";
$currency_result = mysqli_query($conn, $currency_query);

// Fetch invoice with organization condition
$invoice_result = mysqli_query($conn, "
    SELECT i.*, l.name AS salesperson_name
    FROM invoice i
    LEFT JOIN login l ON i.user_id = l.id
    WHERE i.id = $invoice_id AND i.is_deleted = 0 AND i.org_id = '$org_id'
");

$invoice = mysqli_fetch_assoc($invoice_result);

if (!$invoice) {
    die('Invoice not found!');
}

$invoiceId = $invoice['id'];
$client_id = $invoice['client_id'];
$bank_id = $invoice['bank_id'];
$item_type = $invoice['item_type']; // Get item type from invoice

// ALWAYS use company currency for display - removed client currency override
$displayCurrency = $companyCurrency;

// Fetch client only if client_id is valid
$client = null;
if (!empty($client_id)) {
    $client = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM client WHERE id = $client_id"));
}

// Fetch bank only if bank_id is valid
$bank = null;
if (!empty($bank_id)) {
    $bank = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bank WHERE id = $bank_id AND org_id = '$org_id'"));
}

// Fetch items with updated structure for products and services
$items_result = mysqli_query($conn, "
    SELECT ii.*, 
           p.name AS product_name,
           p.code AS product_code,
           s.name AS service_product_name,
           s.code AS service_code,
           COALESCE(p.code, s.code) AS code,
           t.name AS tax_name, 
           u.name AS unit_name
    FROM invoice_item ii
    LEFT JOIN product p ON p.id = ii.product_id
    LEFT JOIN product s ON s.id = ii.service_id
    LEFT JOIN units u ON u.id = ii.unit_id
    LEFT JOIN tax t ON t.id = ii.tax_id
    WHERE ii.invoice_id = $invoice_id AND ii.is_deleted = 0
");

// Check if any item has quantity value (not null and greater than 0)
$showQuantityColumn = false;
mysqli_data_seek($items_result, 0); // Reset pointer
while ($item = mysqli_fetch_assoc($items_result)) {
    if (!is_null($item['quantity']) && $item['quantity'] > 0) {
        $showQuantityColumn = true;
        break;
    }
}
// Reset pointer again for later use
mysqli_data_seek($items_result, 0);

// Fetch client address only if client_id is valid
$client_address = null;
if (!empty($client_id)) {
    $client_address_query = "
        SELECT ca.*, 
               co.name AS country_name, 
               s.name AS state_name, 
               ci.name AS city_name
        FROM client_address ca
        LEFT JOIN countries co ON co.id = ca.billing_country
        LEFT JOIN states s ON s.id = ca.billing_state
        LEFT JOIN cities ci ON ci.id = ca.billing_city
        WHERE ca.client_id = $client_id
        LIMIT 1
    ";
    $client_address = mysqli_fetch_assoc(mysqli_query($conn, $client_address_query));
}

// Fetch company info (Bill From) with city/state/country names and invoice logo - ADDED ORG_ID CONDITION
$company = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT ci.*, 
           co.name AS country_name,
           s.name AS state_name,
           c.name AS city_name
    FROM company_info ci
    LEFT JOIN countries co ON co.id = ci.country_id
    LEFT JOIN states s ON s.id = ci.state_id
    LEFT JOIN cities c ON c.id = ci.city_id
    WHERE ci.org_id = '$org_id'
    LIMIT 1
"));

// Check if notes are available
$showNotes = !empty($invoice['invoice_note']);

// Check if terms & conditions are available
$showTerms = !empty($invoice['description']);

// Check if bank details are available
$showBankDetails = $bank && (!empty($bank['bank_name']) || !empty($bank['account_number']) || !empty($bank['ifsc_code']));

// Function to format amount with currency symbol - ALWAYS USE COMPANY CURRENCY
function formatAmount($amount, $currencySymbol = null) {
    global $displayCurrency;
    // Always use display currency symbol (which is company currency)
    $symbol = $displayCurrency['currency_symbol'] ?? '$';
    return $symbol . number_format($amount, 2);
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
	
<style>
    /* Print-specific styles - Only for PDF */
    @media print {
        @page {
            size: A4;
            margin: 15mm;
        }
        
        body * {
            visibility: hidden;
        }
        #pdf-content, #pdf-content * {
            visibility: visible;
        }
        #pdf-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: white;
            margin: 0;
            padding: 0;
        }
        .no-print, .btn, .alert, .offcanvas, .modal {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
            margin-bottom: 0 !important;
        }
        .table-dark {
            background-color: #2c3e50 !important;
            color: white !important;
        }
        /* Hide empty elements in PDF */
        .pdf-hide-empty:empty {
            display: none !important;
        }
        /* Hide invoice details section in print */
        .invoice-details-section {
            display: none !important;
        }
        /* Ensure proper page breaks */
        .card-body {
            padding: 20px !important;
        }
        /* Improve readability for A4 */
        table {
            font-size: 11px;
            width: 100%;
        }
        h6, .fs-14, .fs-16 {
            font-size: 12px !important;
        }
    }

    /* PDF-only header - EXACTLY LIKE QUOTATION FILE */
    .pdf-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #333;
        padding-bottom: 10px;
    }
    .pdf-logo {
        max-width: 150px;
        max-height: 80px;
    }
    
    @media screen {
        .pdf-header {
            display: none;
        }
    }

    /* Rest of your existing CSS remains exactly the same */
    .gst-badge {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 600;
    }
    .gst-badge.gst {
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
    .gst-badge.non-gst {
        background-color: #fff3cd;
        color: #664d03;
        border: 1px solid #ffecb5;
    }

    .billing-section {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
    }
    .billing-from, .billing-to {
        flex: 1;
        min-width: 300px;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    .billing-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #2c3e50;
        border-bottom: 2px solid #007bff;
        padding-bottom: 5px;
    }

    .company-logo-section {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    .company-logo-img {
        max-width: 120px;
        max-height: 80px;
        margin-right: 20px;
    }
    .company-info-text {
        flex: 1;
    }
    .company-name {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 5px;
    }
    .company-tagline {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 0;
    }
    
    .pdf-only {
        display: none;
    }
    
    @media print {
        .pdf-only {
            display: block;
        }
        .no-pdf {
            display: none;
        }
    }
    
    /* Status dropdown styles */
    .status-dropdown {
        position: relative;
        display: inline-block;
        margin-right: 10px;
    }
    .status-btn {
        background: #fff;
        border: 1px solid #dee2e6;
        color: #495057;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s;
    }
    .status-btn:hover {
        background: #f8f9fa;
        border-color: #adb5bd;
    }
    .status-dropdown-content {
        display: none;
        position: absolute;
        background-color: #fff;
        min-width: 160px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        border-radius: 4px;
        z-index: 1000;
        border: 1px solid #dee2e6;
        padding: 8px 0;
    }
    .status-dropdown-content.show {
        display: block;
    }
    .status-option {
        padding: 8px 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #495057;
        transition: background-color 0.2s;
    }
    .status-option:hover {
        background-color: #f8f9fa;
    }
    .status-option i {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    .status-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }
    
    /* Currency dropdown styles (same as add customer) */
    .currency-select-container {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .currency-badge {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 14px;
        color: #495057;
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
			<div class="content content-two">
			  <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['message_type'] ?> no-print">
                        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>
				<!-- start row -->
				<div class="row">
					<div class="col-md-12 mx-auto">
						<div>
							<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3 no-print">
								<h6>Invoice Detail</h6>
								<div class="d-flex align-items-center flex-wrap row-gap-3">
								
                                    <!-- Status Dropdown -->
                                    <div class="status-dropdown">
                                        <button class="status-btn" onclick="toggleStatusDropdown()">
                                            <span class="status-indicator" id="statusIndicator" style="background-color: <?php 
                                                $statusColor = match(strtolower($invoice['status'] ?? 'unpaid')) {
                                                    'paid' => '#28a745',
                                                    'unpaid' => '#ffc107',
                                                    'cancelled' => '#dc3545',
                                                    'uncollectable' => '#dc3545',
                                                    default => '#6c757d'
                                                };
                                                echo $statusColor;
                                            ?>"></span>
                                            <span id="statusText"><?= ucfirst($invoice['status'] ?? 'Unpaid') ?></span>
                                            <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                                        </button>
                                        <div class="status-dropdown-content" id="statusDropdown">
                                            <div class="status-option" data-status="paid" onclick="updateStatus('paid')">
                                                <i class="fas fa-circle" style="color: #28a745;"></i> Paid
                                            </div>
                                            <div class="status-option" data-status="unpaid" onclick="updateStatus('unpaid')">
                                                <i class="fas fa-circle" style="color: #ffc107;"></i> Unpaid
                                            </div>
                                            <div class="status-option" data-status="cancelled" onclick="updateStatus('cancelled')">
                                                <i class="fas fa-circle" style="color: #dc3545;"></i> Cancelled
                                            </div>
                                            <div class="status-option" data-status="uncollectable" onclick="updateStatus('uncollectable')">
                                                <i class="fas fa-circle" style="color: #dc3545;"></i> Uncollectable
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Download PDF button -->
                                    <a href="generate-invoice-pdf.php?id=<?= $invoiceId ?>" class="btn btn-outline-white d-inline-flex align-items-center me-3">
                                        <i class="isax isax-document-download me-1"></i>Download PDF
                                    </a>
                                    
									<a href="process/action_send_invoice_email.php?invoice_id=<?= $invoiceId ?>" 
										class="btn btn-outline-white d-inline-flex align-items-center me-3">
											<i class="isax isax-message-notif me-1"></i>Send Email
										</a>

									<a href="" class="btn btn-outline-white d-inline-flex align-items-center me-3" onclick="window.print(); return false;">
										<i class="isax isax-printer me-1"></i>Print
									</a>

									<a href="#" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="offcanvas" data-bs-target="#customcanvas">
										<i class="isax isax-eye me-1"></i>View Details
							
									</a>
								</div>
							</div>
							
							<!-- PDF Content Section - This is what gets converted to PDF -->
							<div id="pdf-content">
								<!-- PDF Header with Logo - Only visible in PDF -->
								<div class="pdf-header">
									<div>
										<?php if (!empty($company['invoice_logo'])): ?>
											<img src="../uploads/<?= htmlspecialchars($company['invoice_logo']) ?>" class="pdf-logo" alt="Company Logo">
										<?php else: ?>
											<h4><?= htmlspecialchars($company['name'] ?? 'Company Name') ?></h4>
										<?php endif; ?>
									</div>
									<div class="text-end">
										<h5>INVOICE</h5>
										<p class="mb-0">Invoice No: <?= htmlspecialchars($invoice['invoice_id']) ?></p>
										<p class="mb-0">Date: <?= htmlspecialchars($invoice['invoice_date']) ?></p>
										<p class="mb-0">Currency: <?= htmlspecialchars($displayCurrency['currency_name']) ?></p>
									</div>
								</div>
								
								<div class="card">
									<div class="card-body">
										<!-- Company Logo Section - Visible on screen but not in PDF -->
										<div class="company-logo-section no-pdf">
                                            <div class="col-md-6">
                                                <?php if (!empty($company['invoice_logo'])): ?>
                                                    <img src="../uploads/<?= htmlspecialchars($company['invoice_logo']) ?>" class="company-logo-img mb-3" alt="Company Logo">
                                                <?php endif; ?>
                                                <?php if (!empty($invoice['reference_name'])): ?>
																<p class="mb-1">Reference Name:  <span class="text-dark"><?= htmlspecialchars($invoice['reference_name']) ?></span></span></p>
															<?php endif; ?>
															<?php if (!empty($invoice['salesperson_name'])): ?>
																<p class="mb-1">Sales Person :  <span class="text-dark"> <?= htmlspecialchars($invoice['salesperson_name']) ?></span></span></p>
															<?php endif; ?>
															<?php if (!empty($invoice['order_number'])): ?>
																<p class="mb-1">Order Number :  <span class="text-dark"><?= htmlspecialchars($invoice['order_number']) ?></span></span></p>
															<?php endif; ?>
															<p class="mb-1">GST Type : 
																<span class="gst-badge <?= ($invoice['gst_type'] ?? 'gst') === 'non_gst' ? 'non-gst' : 'gst' ?>">
																	<?= ($invoice['gst_type'] ?? 'gst') === 'non_gst' ? 'Non-GST' : 'GST' ?>
																</span>
															</p>
															<?php 
															$status = $invoice['status'] ?? 'Pending';
															$badgeClass = match(strtolower($status)) {
																'paid' => 'bg-success',
																'unpaid' => 'bg-warning text-dark',
																'cancelled' => 'bg-danger',
																'uncollectable' => 'bg-danger',
																default => 'bg-secondary'
															};
															?>
															<p class="mb-1">Status : <span class="badge <?= $badgeClass ?> badge-sm status-display"><?= ucfirst($status) ?></span></p>
															<p class="mb-1">Currency : <span class="text-dark"><?= htmlspecialchars($displayCurrency['currency_name']) ?> (<?= htmlspecialchars($displayCurrency['currency_symbol']) ?>)</span></p>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <p class="mb-1">Invoice Number : <span class="text-dark"><?= htmlspecialchars($invoice['invoice_id']) ?></span></p>
															<p class="mb-1">Issued On : <span class="text-dark"><?= htmlspecialchars($invoice['invoice_date']) ?></span></span></p>
															<p class="mb-1">Due Date :  <span class="text-dark"><?= htmlspecialchars($invoice['due_date']) ?></span></span></p>
                                            </div>
											
										</div>

										<!-- Invoice Details Section - Hidden in Print/PDF -->


										<!-- Bill From and Bill To Side by Side -->
										<div class="billing-section mb-3">
											<!-- Bill From -->
											<div class="billing-from">
												<div class="billing-title">Billing From</div>
												<div class="d-flex align-items-center mb-1">
													<div>
														<h6 class="fs-14 fw-semibold"><?= htmlspecialchars($company['name'] ??'') ?></h6>
													</div>
												</div>
												<?php if (!empty($company['address'])): ?>
													<p class="mb-1"><?= htmlspecialchars($company['address'] ??'') ?></p>
												<?php endif; ?>
												<?php if (!empty($company['city_name']) || !empty($company['state_name']) || !empty($company['country_name']) || !empty($company['zipcode'])): ?>
													<p class="mb-1">
														<?= htmlspecialchars($company['city_name'] ??'') ?>, 
														<?= htmlspecialchars($company['state_name'] ??'') ?>, 
														<?= htmlspecialchars($company['country_name'] ??'') ?>, 
														<?= htmlspecialchars($company['zipcode'] ??'') ?>
													</p>
												<?php endif; ?>
												<?php if (!empty($company['mobile_number'])): ?>
													<p class="mb-1">Phone : <?= htmlspecialchars($company['mobile_number'] ??'') ?></p>
												<?php endif; ?>
												<?php if (!empty($company['email'])): ?>
													<p class="mb-1">Email : <?= htmlspecialchars($company['email'] ??'') ?></p>
												<?php endif; ?>
											</div>

											<!-- Bill To - Updated section -->
											<div class="billing-to">
												<div class="billing-title">Billing To</div>
												<?php if (!empty($client['company_name'])): ?>
													<h6 class="fs-14 fw-semibold mb-1"><?= htmlspecialchars($client['company_name']) ?></h6>
												<?php endif; ?>
												
												<?php 
												// Build client name from first_name and last_name
												$client_name = '';
												if (!empty($client['first_name']) || !empty($client['last_name'])) {
													$client_name = trim(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? ''));
												}
												if (!empty($client_name)): ?>
													<p class="mb-1 fs-13"><span class="text-dark">Client:</span> <?= htmlspecialchars($client_name) ?></p>
												<?php endif; ?>
												
												<?php if (!empty($client_address['billing_address1'])): ?>
													<p class="mb-1"><?= htmlspecialchars($client_address['billing_address1'] ??'') ?></p>
												<?php endif; ?>
												<?php if (!empty($client_address['city_name']) || !empty($client_address['state_name']) || !empty($client_address['country_name']) || !empty($client_address['billing_pincode'])): ?>
													<p class="mb-1"><?= htmlspecialchars($client_address['city_name'] ??'') ?>, <?= htmlspecialchars($client_address['state_name'] ??'') ?>, <?= htmlspecialchars($client_address['country_name'] ??'') ?>, <?= htmlspecialchars($client_address['billing_pincode'] ??'') ?></p>
												<?php endif; ?>
												<?php if (!empty($client['phone_number'])): ?>
													<p class="mb-1">Phone : <?= htmlspecialchars($client['phone_number'] ??'') ?></p>
												<?php endif; ?>
												<?php if (!empty($client['email'])): ?>
													<p class="mb-1">Email : <?= htmlspecialchars($client['email'] ??'') ?></p>
												<?php endif; ?>
											</div>
										</div>

										<div class="mb-3">
											<h6 class="mb-3">Product / Service Items</h6>
											<div class="table-responsive rounded border-bottom-0 border table-nowrap">
												<table class="table m-0">
													<thead class="table-dark" id="table-heading">
														<tr>
															<th>#</th>
															<th>Product/Service</th>
															<th>HSN code</th>
															<?php if ($showQuantityColumn): ?>
																<th><?= $item_type == 1 ? 'Quantity' : 'Hours' ?></th>
															<?php endif; ?>
															
															<th><?= $item_type == 1 ? 'Selling Price' : 'Hourly Price' ?></th>
															<th>Tax</th>
															<th>Amount</th>
														</tr>
													</thead>
													
													<tbody>
														<?php 
														$i = 1; 
														mysqli_data_seek($items_result, 0);
														while($item = mysqli_fetch_assoc($items_result)) { 
															// Combine product name (from product table) and service name (from invoice_item)
															if (!empty($item['service_id'])) {
																// If it's a service, combine both names with hyphen
																$productName = !empty($item['service_product_name']) ? $item['service_product_name'] : '';
																$serviceName = !empty($item['service_name']) ? $item['service_name'] : '';
																$itemName = $productName .' '. '-' . ' '. $serviceName;
															} else {
																// If it's a product, use only product name
																$itemName = !empty($item['product_name']) ? $item['product_name'] : 'Product';
															}
														?>
														<tr>
															<td><?= $i++ ?></td>
															<td><?= htmlspecialchars($itemName) ?></td>
															<td><?= htmlspecialchars($item['code'] ?? 'N/A') ?></td>
															<?php if ($showQuantityColumn): ?>
																<td><?= $item['quantity'] ?></td>
															<?php endif; ?>
													
															<td><?= formatAmount($item['selling_price']) ?></td>
															<td>
																<?php if (($invoice['gst_type'] ?? 'gst') === 'non_gst'): ?>
																	Non-GST
																<?php else: ?>
																	<?= $item['tax_name'] ?>
																<?php endif; ?>
															</td>
															<td><?= formatAmount($item['amount']) ?></td>
														</tr>
														<?php } ?>
													</tbody>
												</table>
											</div>
										</div>
										<div class="border-bottom mb-3">

											<!-- start row -->
											<div class="row justify-content-between">
												<div class="col-lg-6">
													<?php if ($showBankDetails): ?>
														<div class="d-flex align-items-center p-4 mb-3">
															<div>
																<h6 class="mb-2">Bank Details</h6>
																<div class="pdf-hide-empty">
																	<?php if (!empty($bank['bank_name'])): ?>
																		<p class="mb-1">Bank Name :  <span class="text-dark"><?= htmlspecialchars($bank['bank_name']) ?></span></p>
																	<?php endif; ?>
																	<?php if (!empty($bank['account_number'])): ?>
																		<p class="mb-1">Account Number :  <span class="text-dark"> <?= htmlspecialchars($bank['account_number']) ?></span></p>
																	<?php endif; ?>
																	<?php if (!empty($bank['ifsc_code'])): ?>
																		<p class="mb-1">IFSC Code :  <span class="text-dark"><?= htmlspecialchars($bank['ifsc_code']) ?></span></p>
																	<?php endif; ?>
																</div>
															</div>
														</div>
													<?php endif; ?>
													
													<?php if ($showTerms): ?>
														<div class="p-4">
															<h6 class="mb-2">Terms & Conditions</h6>
															<div>
																<p class="mb-1"><?= htmlspecialchars($invoice['description']) ?>.</p>
															</div>
														</div>
													<?php endif; ?>
												</div><!-- end col -->
												<div class="col-lg-4">
													<div class="mb-3 p-4">
														<div class="d-flex align-items-center justify-content-between mb-3">
															<h6 class="fs-14 fw-semibold">Sub Amount</h6>
															<h6 class="fs-14 fw-semibold"><?= formatAmount($invoice['amount']) ?></h6>
														</div>
														
														<?php if (($invoice['gst_type'] ?? 'gst') === 'non_gst'): ?>
															<div class="d-flex align-items-center justify-content-between mb-3">
																<h6 class="fs-14 fw-semibold">Tax (Non-GST)</h6>
																<h6 class="fs-14 fw-semibold"><?= formatAmount(0) ?></h6>
															</div>
														<?php else: ?>
															<div class="d-flex align-items-center justify-content-between mb-3">
																<h6 class="fs-14 fw-semibold">Tax Amount</h6>
																<h6 class="fs-14 fw-semibold"><?= formatAmount($invoice['tax_amount']) ?></h6>
															</div>
														<?php endif; ?>
														
														<?php if (!empty($invoice['shipping_charge']) && $invoice['shipping_charge'] > 0): ?>
															<div class="d-flex align-items-center justify-content-between mb-3">
																<h6 class="fs-14 fw-semibold">Shipping Charge</h6>
																<h6 class="fs-14 fw-semibold"><?= formatAmount($invoice['shipping_charge']) ?></h6>
															</div>
														<?php endif; ?>
														
														<div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
															<h6>Total</h6>
															<h6><?= formatAmount($invoice['total_amount']) ?></h6>
														</div>
														<div class="d-flex justify-content-between align-items-center">
															<h6 class="fs-14 fw-semibold mb-1 m-0">Total In Words</h6>
															<p class="m-0"><?= numberToWords($invoice['total_amount']) ?> <?= htmlspecialchars($displayCurrency['currency_name']) ?></p>
														</div>

													</div>
												</div><!-- end col -->
											</div>
											<!-- end row -->

										</div>

										<!-- start row -->
										<?php if ($showNotes): ?>
										<div class="row">
											<div class="col-lg-12">
												<div class="mb-3">
													<div>
														<h6 class="fs-14 fw-semibold mb-1">Notes</h6>
														<p><?= htmlspecialchars($invoice['invoice_note']) ?></p>
													</div>
												</div>
											</div><!-- end col -->
										</div>
										<?php endif; ?>
										<!-- end row -->

										<div class="">
											<!-- <div>
												<h6 class="fs-14 fw-semibold mb-1">Dreams Technologies Pvt Ltd.,</h6>
												<p>15 Hodges Mews, High Wycombe HP12 3JL, United Kingdom</p>
											</div> -->
											
										</div>
									</div><!-- end card body -->
								</div><!-- end card -->
							</div><!-- end pdf-content -->
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

		<!-- Start Filter -->
		<div class="offcanvas offcanvas-offset offcanvas-end no-print" tabindex="-1" id="customcanvas">                                      
			<div class="offcanvas-header d-block pb-0">
				<div class="border-bottom d-flex align-items-center justify-content-between pb-3">
					<h6 class="offcanvas-title">Details</h6>
					<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-x"></i></button>
				</div>
			</div>			
			<div class="offcanvas-body pt-3">  
				<form action="process/action_update_invoice_status.php" method="POST" id="invoiceStatusForm">
					<input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>">
					<div class="mb-3">
						<label class="form-label">Status <span class="text-danger">*</span></label>
						<span id="statusError" class="text-danger fs-12 d-block mt-1"></span>
						<div class="dropdown">
							<a href="javascript:void(0);" id="statusDropdownBtn" class="dropdown-toggle btn btn-lg bg-light  d-flex align-items-center justify-content-start fs-13 fw-normal border" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
								 <?= !empty($invoice['status']) ? ucfirst($invoice['status']) : 'Select' ?>
							</a>
							<div class="dropdown-menu shadow-lg w-100 dropdown-info">	
								<ul class="mb-3">
								<li>
								<label class="dropdown-item px-2 d-flex align-items-center text-dark">
									<input class="form-check-input" type="radio" name="status" value="paid" <?= $invoice['status'] === 'paid' ? 'checked' : '' ?>>
									<i class="fa-solid fa-circle fs-6 text-success me-1"></i>Paid
								</label>
								</li>
								<li>
								<label class="dropdown-item px-2 d-flex align-items-center text-dark">
									<input class="form-check-input" type="radio" name="status" value="unpaid" <?= $invoice['status'] === 'unpaid' ? 'checked' : '' ?>>
									<i class="fa-solid fa-circle fs-6 text-warning me-1"></i>Unpaid
								</label>
								</li>
								<li>
								<label class="dropdown-item px-2 d-flex align-items-center text-dark">
									<input class="form-check-input" type="radio" name="status" value="cancelled" <?= $invoice['status'] === 'cancelled' ? 'checked' : '' ?>>
									<i class="fa-solid fa-circle fs-6 text-danger me-1"></i>Cancelled
								</label>
								</li>
								<!-- <li>
								<label class="dropdown-item px-2 d-flex align-items-center text-dark">
									<input class="form-check-input" type="radio" name="status" value="partially paid" <?= $invoice['status'] === 'partially paid' ? 'checked' : '' ?>>
									<i class="fa-solid fa-circle fs-6 text-purple me-1"></i>Partially Paid
								</label>
								</li> -->
								<li>
								<label class="dropdown-item px-2 d-flex align-items-center text-dark">
									<input class="form-check-input" type="radio" name="status" value="uncollectable" <?= $invoice['status'] === 'uncollectable' ? 'checked' : '' ?>>
									<i class="fa-solid fa-circle fs-6 text-orange me-1"></i>Uncollectable
								</label>
								</li>

								</ul>
							</div>
						</div>
					</div>
					
					<!-- Currency Selection Section - Removed since we only use company currency -->
					
					<div class="offcanvas-footer">
						<div class="row g-2">
							<div class="col-6">
								<a href="javascript:location.reload();" class="btn btn-outline-white w-100">Reset</a>
							</div>
							<div class="col-6">
								<button data-bs-dismiss="offcanvas" class="btn btn-primary w-100" id="filter-submit">Submit</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- End Filter -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>

	
<script>
function sendInvoiceEmail(invoiceId) {
    fetch('process/action_send_invoice_email.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'invoice_id=' + encodeURIComponent(invoiceId)
    }).then(response => {
        if (response.ok) {
            console.log("Email sent successfully.");
        } else {
            console.error("Failed to send email.");
        }
    });
}
</script>
<script>
// Status dropdown functionality for the new dropdown
function toggleStatusDropdown() {
    const dropdown = document.getElementById('statusDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('statusDropdown');
    const button = document.querySelector('.status-btn');
    
    if (!button.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

// Update status function for invoice
function updateStatus(status) {
    // Close dropdown
    document.getElementById('statusDropdown').classList.remove('show');
    
    // Send AJAX request to update status
    updateInvoiceStatusViaAjax(status);
}

// AJAX function to update invoice status
function updateInvoiceStatusViaAjax(status) {
    const formData = new FormData();
    formData.append('invoice_id', '<?= $invoiceId ?>');
    formData.append('status', status);
    
    fetch('process/action_update_invoice_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Show success message
        showToast('Invoice status updated successfully!', 'success');
        
        // Update all status displays on the page
        updateAllStatusDisplays(status);
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to update status. Please try again.', 'danger');
    });
}

// Function to update all status displays on the page
function updateAllStatusDisplays(status) {
    // Update the dropdown button text and indicator
    const statusText = document.getElementById('statusText');
    const statusIndicator = document.getElementById('statusIndicator');
    
    let displayText = status.charAt(0).toUpperCase() + status.slice(1);
    
    // Update text
    statusText.textContent = displayText;
    
    // Update indicator color
    const statusColors = {
        'paid': '#28a745',
        'unpaid': '#ffc107',
        'cancelled': '#dc3545',
        'uncollectable': '#dc3545'
    };
    
    statusIndicator.style.backgroundColor = statusColors[status] || '#6c757d';
    
    // Update the status badge in the invoice details section
    const statusBadge = document.querySelector('.status-display');
    if (statusBadge) {
        const badgeClasses = {
            'paid': 'bg-success',
            'unpaid': 'bg-warning text-dark',
            'cancelled': 'bg-danger',
            'uncollectable': 'bg-danger'
        };
        
        // Update badge class
        statusBadge.className = 'badge ' + (badgeClasses[status] || 'bg-secondary') + ' badge-sm status-display';
        
        // Update badge text
        let badgeText = status.charAt(0).toUpperCase() + status.slice(1);
        statusBadge.textContent = badgeText;
    }
    
    // Update the radio button in the offcanvas if it exists
    const offcanvasStatusRadio = document.querySelector(`input[name="status"][value="${status}"]`);
    if (offcanvasStatusRadio) {
        offcanvasStatusRadio.checked = true;
        updateDropdownBtn(); // Update the dropdown button text in offcanvas
    }
}

// Toast notification function for invoice
function showToast(message, type) {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0`;
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', function () {
        toast.remove();
    });
}

// Original status dropdown functionality for offcanvas (keep existing)
function updateDropdownBtn() {
    let selected = document.querySelector("input[name='status']:checked");
    let btn = document.getElementById("statusDropdownBtn");

    if (selected) {
        let labelText = selected.closest("label").innerText.trim();
        btn.textContent = labelText;  
    }
}

// === When user changes status in offcanvas ===
document.querySelectorAll("input[name='status']").forEach(function(radio) {
    radio.addEventListener("change", updateDropdownBtn);
});

// === On page load (already saved status) ===
window.addEventListener("DOMContentLoaded", updateDropdownBtn);

// === Validation on submit for offcanvas form ===
document.getElementById("invoiceStatusForm").addEventListener("submit", function(e) {
    let statusChecked = document.querySelector("input[name='status']:checked");
    let errorSpan = document.getElementById("statusError");
    let isValid = true;

    if (!statusChecked) {
        e.preventDefault();
        errorSpan.textContent = "Please select a status.";
        isValid = false;
    } else {
        errorSpan.textContent = "";
    }
    
    return isValid;
});
</script>

</body>

</html>