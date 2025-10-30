<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

$invoice_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($invoice_id <= 0) {
    die('Invalid Invoice ID!');
}

// Fetch invoice
$invoice_result = mysqli_query($conn, "
    SELECT i.*, l.name AS salesperson_name
    FROM invoice i
    LEFT JOIN login l ON i.user_id = l.id
    WHERE i.id = $invoice_id AND i.is_deleted = 0
");

$invoice = mysqli_fetch_assoc($invoice_result);

if (!$invoice) {
    die('Invoice not found!');
}

$invoiceId = $invoice['id'];
$client_id = $invoice['client_id'];
$bank_id = $invoice['bank_id'];

// Fetch client only if client_id is valid
$client = null;
if (!empty($client_id)) {
    $client = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM client WHERE id = $client_id"));
}

// Fetch bank only if bank_id is valid
$bank = null;
if (!empty($bank_id)) {
    $bank = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bank WHERE id = $bank_id"));
}

// Fetch items
$items_result = mysqli_query($conn, "SELECT ii.*, p.name AS product_name,p.code, t.name AS tax_name, u.name AS unit_name
    FROM invoice_item ii
    LEFT JOIN product p ON p.id = ii.product_id
    LEFT JOIN units u ON u.id = ii.unit_id
    LEFT JOIN tax t ON t.id = ii.tax_id
	
    WHERE ii.invoice_id = $invoice_id AND ii.is_deleted = 0");

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
// Fetch company info (Bill From)
// Fetch company info (Bill From) with city/state/country names
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

    <!-- Start Main Wrapper -->
    <div class="main-wrapper">

		<?php include 'layouts/menu.php'; ?>

		<!-- ========================
			Start Page Content
		========================= -->

		<div class="page-wrapper">

			<!-- Start Content -->
			<div class="content">
			  <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>
				<!-- start row -->
				<div class="row">
					<div class="col-md-10 mx-auto">
						<div>
							<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
								<h6><a href="invoices.php"><i class="isax isax-arrow-left me-2"></i>Invoice</a></h6>
								<div class="d-flex align-items-center flex-wrap row-gap-3">
									<a href="javascript:void(0);" onclick="downloadAsPDF(event)" class="btn btn-outline-white d-inline-flex align-items-center me-3">Download PDF</a>
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
							<div class="card" id="downloadpdf">
								<div class="card-body">
									<div class="bg-light p-4 rounded position-relative mb-3">
										<div class="position-absolute top-0 end-0 z-0">
											
										</div>
										<div class="d-flex align-items-center justify-content-between border-bottom flex-wrap mb-3 pb-2 position-relative z-1">
											<div class="mb-3">
												<h4 class="mb-1">Invoice</h4>
												<div class="d-flex align-items-center flex-wrap row-gap-3">
													<div class="me-4">
														<!-- <h6 class="fs-14 fw-semibold mb-1">Dreams Technologies Pvt Ltd.,</h6> -->
														<!-- <p class="mb-1"><?= htmlspecialchars($login_user['city_name']) ?>, <?= htmlspecialchars($login_user['state_name']) ?>, <?= htmlspecialchars($login_user['country_name']) ?>, <?= htmlspecialchars($login_user['zipcode']) ?></p> -->

													</div>
													
												</div>
											</div>
											
										</div>

										<!-- start row -->
										<div class="row gy-3 position-relative z-1">
											<div class="col-lg-4">
												<div>
													<h6 class="mb-2 fs-16 fw-semibold">Invoice Details</h6>
													<div>
														<p class="mb-1">Invoice Number : <span class="text-dark"><?= htmlspecialchars($invoice['invoice_id']) ?></span></p>
														<p class="mb-1">Issued On : <span class="text-dark"><?= htmlspecialchars($invoice['invoice_date']) ?></span></span></p>
														<p class="mb-1">Due Date :  <span class="text-dark"><?= htmlspecialchars($invoice['due_date']) ?></span></span></p>
														<p class="mb-1">Reference Name:  <span class="text-dark"><?= htmlspecialchars($invoice['reference_name']) ?></span></span></p>
														<p class="mb-1">Sales Person :  <span class="text-dark"> <?= htmlspecialchars($invoice['salesperson_name'] ?? 'N/A') ?></span></span></p>
														<p class="mb-1">Order Number :  <span class="text-dark"><?= htmlspecialchars($invoice['order_number']) ?></span></span></p>
														<!-- <p class="mb-1">Recurring Invoice  :  <span class="text-dark">Monthly</span></p> -->
														<?php 
															$status = $invoice['status'] ?? 'Pending';

															$badgeClass = match(strtolower($status)) {
																'paid'    => 'bg-success',
																'unpaid'  => 'bg-warning text-dark',
																'cancelled'  => 'bg-danger',
																default   => 'bg-secondary'
															};
															?>

															<span id="invoice-status" class="badge <?= $badgeClass ?> badge-sm">
																<?= ucfirst($status) ?>
															</span>

													</div>
												</div>
											</div><!-- end col -->
											<div class="col-lg-4">
    <div>
        <h6 class="mb-2 fs-16 fw-semibold">Billing From</h6>
        <div class="bg-white rounded p-3">
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
</div>


											<div class="col-lg-4">
												<div>
													<h6 class="mb-2 fs-16 fw-semibold">Billing To</h6>
													<div class="bg-white rounded p-3">
														<div class="d-flex align-items-center mb-1">
															
															<div>
																<h6 class="fs-14 fw-semibold"><?= htmlspecialchars($client['first_name']) ?></h6>
															</div>
														</div>
														<p class="mb-1"><?= htmlspecialchars($client_address['billing_address1']) ?></p>
														<p class="mb-1"><?= htmlspecialchars($client_address['city_name']) ?>, <?= htmlspecialchars($client_address['state_name']) ?>, <?= htmlspecialchars($client_address['country_name']) ?>, <?= htmlspecialchars($client_address['billing_pincode']) ?></p>
														<p class="mb-1">Phone : <?= htmlspecialchars($client['phone_number']) ?></p>
														<p class="mb-1">Email : <?= htmlspecialchars($client['email']) ?></p>
														
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
													
														<th>Tax (%)</th>
														<th>Amount</th>
														
													</tr>
												</thead>
												<tbody>
													 <?php $i = 1; while($item = mysqli_fetch_assoc($items_result)) { ?>
                                                <tr>
                                                    <td><?= $i++ ?></td>
                                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
													<td><?= $item['code'] ?></td>
                                                    <td><?= $item['quantity'] ?></td>
                                                    <td><?= htmlspecialchars($item['unit_name']) ?></td>
                                                    <td><?= $item['selling_price'] ?></td>
                                                    <td><?= $item['tax_name'] ?></td>
                                                    <td><?= $item['amount'] ?></td>
                                                </tr>
                                            <?php } ?>
												</tbody>
											</table>
										</div>
									</div>
									<div class="border-bottom mb-3">

										<!-- start row -->
										<div class="row">
											<div class="col-lg-6">
												<div class="d-flex align-items-center p-4 mb-3">

													<div>
														<h6 class="mb-2">Bank Details</h6>
														<div>
															<p class="mb-1">Bank Name :  <span class="text-dark"><?= htmlspecialchars($bank['bank_name']) ?></span></p>
															<p class="mb-1">Account Number :  <span class="text-dark"> <?= htmlspecialchars($bank['account_number']) ?></span></p>
															<p class="mb-1">IFSC Code :  <span class="text-dark"><?= htmlspecialchars($bank['ifsc_code']) ?></span></p>
															<p class="mb-0">Payment Reference :  <span class="text-dark">INV-20250220-001</span></p>
														</div>
													</div>
												</div>
											</div><!-- end col -->
											<div class="col-lg-6">
												<div class="mb-3 p-4">
													<div class="d-flex align-items-center justify-content-between mb-3">
														<h6 class="fs-14 fw-semibold">Sub Amount</h6>
														<h6 class="fs-14 fw-semibold"><?= $invoice['amount'] ?></h6>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<h6 class="fs-14 fw-semibold">Tax Amount</h6>
														<h6 class="fs-14 fw-semibold"><?= $invoice['tax_amount'] ?></h6>
													</div>
													<div class="d-flex align-items-center justify-content-between mb-3">
														<h6 class="fs-14 fw-semibold">Shipping Charge</h6>
														<h6 class="fs-14 fw-semibold"><?= $invoice['shipping_charge'] ?></h6>
													</div>
													
													<div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
														<h6>Total</h6>
														<h6><?= $invoice['total_amount'] ?></h6>
													</div>
													<div>
														<h6 class="fs-14 fw-semibold mb-1">Total In Words</h6>
														<p><?= numberToWords($invoice['total_amount']) ?> </p>

													</div>
												</div>
											</div><!-- end col -->
										</div>
										<!-- end row -->

									</div>

									<!-- start row -->
									<div class="row">
										<div class="col-lg-7">
											<div class="mb-3">
												<div class="mb-3">
													<h6 class="fs-14 fw-semibold mb-1">Terms & Conditions</h6>
													<p><?= htmlspecialchars($invoice['description']) ?>.</p>
												</div>
												<div>
													<h6 class="fs-14 fw-semibold mb-1">Notes</h6>
													<p><?= htmlspecialchars($invoice['invoice_note']) ?></p>
												</div>
											</div>
										<div><!-- end col -->
										
									</div>
									<!-- end row -->

									<div class="">
										<!-- <div>
											<h6 class="fs-14 fw-semibold mb-1">Dreams Technologies Pvt Ltd.,</h6>
											<p>15 Hodges Mews, High Wycombe HP12 3JL, United Kingdom</p>
										</div> -->
										
									</div>
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

		<!-- Start Filter -->
		<div class="offcanvas offcanvas-offset offcanvas-end" tabindex="-1" id="customcanvas">                                      
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
					<!-- <div>
						<h6 class="fs-16 fw-semibold mb-2">Payment Details</h6>
						<div class="border-bottom mb-3 pb-0">
							<div class="row">
								<div class="col-6">
									<div class="mb-3">
										<h6 class="fs-14 fw-semibold mb-1">PayPal</h6>
										<p>examplepaypal.com</p>
									</div>
								</div>
								<div class="col-6">
									<div class="mb-3">
										<h6 class="fs-14 fw-semibold mb-1">Account </h6>
										<p>examplepaypal.com</p>
									</div>
								</div>
								<div class="col-6">
									<div class="mb-3">
										<h6 class="fs-14 fw-semibold mb-1">Payment Term</h6>
										<p class="d-flex align-items-center">Days <span class="badge bg-danger ms-2">Due in 8 days</span></p>
									</div>
								</div>
							</div>
						</div>
					</div>		 -->
					<!-- <div>
						<h6 class="fs-16 mb-2">Invoice History</h6>
						<ul class="activity-feed bg-light rounded">
							<li class="feed-item timeline-item">
								<p class="mb-1">Status Changed to <span class="text-dark fw-semibold">Partially Paid</span></p>
								<div class="invoice-date"><span><i class="isax isax-calendar me-1"></i>17 Jan 2025</span></div>
							</li>
							<li class="feed-item timeline-item">
								<p class="mb-1"><span class="text-dark fw-semibold">$300 </span> Partial Amount Paid on <span class="text-dark fw-semibold">Paypal</span></p>
								<div class="invoice-date"><span><i class="isax isax-calendar me-1"></i>16 Jan 2025</span></div>
							</li>
							<li class="feed-item timeline-item">
								<p class="mb-1"><span class="text-dark fw-semibold">John Smith </span> Created <span class="text-dark fw-semibold">Invoice</span><a href="#" class="text-primary">#INV1254</a></p>
								<div class="invoice-date"><span><i class="isax isax-calendar me-1"></i>16 Jan 2025</span></div>
							</li>
						</ul>
					</div> -->
					<div class="offcanvas-footer">
						<div class="row g-2">
							<div class="col-6">
								<a href="invoice-details.php"  class="btn btn-outline-white w-100">Reset</a>
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
	// Fixed Function to download invoice as PDF
	function downloadAsPDF(event) {
		// Show alert for testing
		alert('PDF download starting...');
		
		// Get the element to convert to PDF
		const element = document.getElementById('downloadpdf');
		
		// Get the button that was clicked to show loading state
		const loadingBtn = event.currentTarget;
		const originalText = loadingBtn.innerHTML;
		loadingBtn.innerHTML = 'Converting...';
		loadingBtn.disabled = true;
		
		// Use html2canvas to capture the content
		html2canvas(element, {
			scale: 2,
			useCORS: true,
			logging: true,
			backgroundColor: '#ffffff'
		}).then(function(canvas) {
			// Create PDF
			const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
			const imgData = canvas.toDataURL('image/png');
			const imgWidth = pdf.internal.pageSize.getWidth();
			const imgHeight = (canvas.height * imgWidth) / canvas.width;
			
			// Add image to PDF
			pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
			
			// Download the PDF
			pdf.save('invoice-<?= $invoice['invoice_id'] ?>.pdf');
			
			// Reset button
			loadingBtn.innerHTML = originalText;
			loadingBtn.disabled = false;
		}).catch(function(error) {
			console.error('Error generating PDF:', error);
			alert('Error generating PDF. Please try again.');
			loadingBtn.innerHTML = originalText;
			loadingBtn.disabled = false;
		});
		
		// Prevent default link behavior
		if (event) {
			event.preventDefault();
		}
		return false;
	}
	</script>
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
    radio.addEventListener("change", updateDropdownBtn);
});

// === On page load (already saved status) ===
window.addEventListener("DOMContentLoaded", updateDropdownBtn);

// === Validation on submit ===
document.getElementById("invoiceStatusForm").addEventListener("submit", function(e) {
    let statusChecked = document.querySelector("input[name='status']:checked");
    let errorSpan = document.getElementById("statusError");

    if (!statusChecked) {
        e.preventDefault();
        errorSpan.textContent = "Please select a status.";
    } else {
        errorSpan.textContent = "";
    }
});
</script>

</body>

</html>