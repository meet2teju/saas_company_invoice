<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

// Initialize filter variables
$selected_customers = [];
$selected_quotation_ids = [];
$selected_statuses = [];
$date_range = '';
$start_date = '';
$end_date = '';

// Build the filter SQL
$filterSql = "WHERE q.is_deleted = 0";

// Process form submission (using GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Date range filter
    if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
        $start_date = mysqli_real_escape_string($conn, $_GET['start_date']);
        $end_date = mysqli_real_escape_string($conn, $_GET['end_date']);
        $filterSql .= " AND DATE(q.created_at) BETWEEN '$start_date' AND '$end_date'";
        $date_range = $start_date . ' - ' . $end_date;
    }
    
    // Client filter
    if (!empty($_GET['customer']) && is_array($_GET['customer'])) {
        $selected_customers = $_GET['customer'];
        $customer_ids = array_map('intval', $selected_customers);
        $customer_ids = implode(',', $customer_ids);
        $filterSql .= " AND q.client_id IN ($customer_ids)";
    }
    
    // Quotation ID filter
    if (!empty($_GET['quotation_id']) && is_array($_GET['quotation_id'])) {
        $selected_quotation_ids = $_GET['quotation_id'];
        $quotation_ids = array_map(function($id) use ($conn) {
            return "'" . mysqli_real_escape_string($conn, $id) . "'";
        }, $selected_quotation_ids);
        $quotation_ids = implode(',', $quotation_ids);
        $filterSql .= " AND q.quotation_id IN ($quotation_ids)";
    }
    
    // Status filter
    if (!empty($_GET['status']) && is_array($_GET['status'])) {
        $selected_statuses = $_GET['status'];
        $statuses = array_map(function($status) use ($conn) {
            return "'" . mysqli_real_escape_string($conn, $status) . "'";
        }, $selected_statuses);
        $statuses = implode(',', $statuses);
        $filterSql .= " AND q.status IN ($statuses)";
    }
}

// Main query
$sql = "SELECT q.id, q.quotation_id, q.quotation_date, q.status, c.first_name, c.customer_image 
        FROM quotation q
        LEFT JOIN client c ON q.client_id = c.id
        $filterSql
        ORDER BY q.id DESC";

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php include 'layouts/title-meta.php'; ?> 
	<?php include 'layouts/head-css.php'; ?>
</head>

<body>
    <!-- Start Main Wrapper -->
    <div class="main-wrapper">
		<?php include 'layouts/menu.php'; ?>

		<!-- Page Content -->
		<div class="page-wrapper">	
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>

			<!-- Start Content -->
			<div class="content content-two">
    
				<!-- Page Header -->
				<div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
					<div>
						<h6 class="mb-0">Quotations</h6>
					</div>
					<div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
						<div class="dropdown">
							<a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center"  data-bs-toggle="dropdown">
								<i class="isax isax-export-1 me-1"></i>Export
							</a>
							<ul class="dropdown-menu">
								<li>
									<a class="dropdown-item" href="#">Download as PDF</a>
								</li>
								<li>
									<a class="dropdown-item" href="#">Download as Excel</a>
								</li>
							</ul>
						</div>

                        <div>
                             <?php if (check_is_access_new("add_quotation") == 1) { ?>
							<a href="add-quotation.php" class="btn btn-primary d-flex align-items-center">
								<i class="isax isax-add-circle5 me-1"></i>New Quotations
							</a>
                            <?php } ?>
						</div>
					</div>
				</div>
				<!-- End Page Header -->
				
				<!-- Table Search -->
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap flex-lg-nowrap flex-md-nowrap">
                            <div class="table-search d-flex align-items-center mb-0">
                                <div class="search-input">
                                    <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                                </div>
                            </div>
							<!-- <div id="reportrange" class="reportrange-picker d-flex align-items-center">
                                <i class="isax isax-calendar text-gray-5 fs-14 me-1"></i>
                                <span class="reportrange-picker-field"><?= !empty($date_range) ? $date_range : date('d M Y') . ' - ' . date('d M Y') ?></span>
                            </div> -->

                            <a class="btn btn-outline-white fw-normal d-inline-flex align-items-center" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#customcanvas">
                                <i class="isax isax-filter me-1"></i>Filter
                            </a>
                           <?php if (
                                !empty($selected_customers) || 
                                !empty($date_range) || 
                                !empty($selected_amounts) || 
                                !empty($selected_statuses)
                            ): ?>
                                <a href="quotations.php" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                                </a>
                            <?php endif; ?>


                         <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                            <i class="fa-regular fa-trash-can me-1"></i>Delete
                        </a>
                        </div>
                      
                    </div>

                   

                </div>
                <!-- /Table Search -->
				
				<!-- Table List -->
				<div class="table-responsive">
					<table class="table table-nowrap datatable">
						<thead class="thead-light">
							<tr>
								<th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input user-checkbox" type="checkbox" id="select-all">
                                    </div>
                                </th>
								<th>Quotation ID</th>
								<th>Client</th>
								<th>Quotation Date</th>
								<th class="no-sort">Status</th>
								<th class="no-sort"></th>
							</tr>
						</thead>
						<tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                                $quotationId = $row['id'];
                                $clientImg = !empty($row['customer_image']) ? '../uploads/' . htmlspecialchars($row['customer_image']) : 'assets/img/users/user-16.jpg';
                            ?>
							<tr>
								<td>
                                  <div class="form-check form-check-md">
                                        <input class="form-check-input user-checkbox" type="checkbox" value="<?= $quotationId ?>">
                                    </div>
                                </td>
								<td>
									<a href="view-quotation.php?id=<?= $quotationId ?>" class="link-default"><?= htmlspecialchars($row['quotation_id']) ?></a>
								</td>
								<td>
                                    <div class="d-flex align-items-center">
										<a href="view-quotation.php?id=<?= $quotationId ?>" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="<?= $clientImg ?>" onerror="this.src='assets/img/users/user-16.jpg';">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="view-quotation.php?id=<?= $quotationId ?>"><?= htmlspecialchars($row['first_name']) ?></a></h6>
										</div>
									</div>
                                </td>
								<td><?= date('d M Y', strtotime($row['quotation_date'])) ?></td>
								<td>
									<span class="badge badge-soft-success d-inline-flex align-items-center"><?= htmlspecialchars($row['status']) ?><i class="isax isax-tick-circle ms-1"></i></span>
								</td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown" class="custom-elipse">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php if (check_is_access_new("view_quotation") == 1) { ?>
                                        <li>
                                            <a href="view-quotation.php?id=<?= $quotationId ?>" class="dropdown-item d-flex align-items-center">
                                                <i class="isax isax-eye me-2"></i>View
                                            </a>
                                        </li>
                                        <?php } ?>

                                        <?php if (check_is_access_new("edit_quotation") == 1) { ?>
                                        <li>
                                            <a href="edit-quotation.php?id=<?= $quotationId ?>" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                            <?php } ?>

                                        <?php if (check_is_access_new("delete_quotation") == 1) { ?>
                                        <li>
                                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#delete_modal<?= $quotationId ?>"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>
                                         <?php } ?>

                                    </ul>
                                </td>
							</tr>
                     <div class="modal fade" id="delete_modal<?= $quotationId ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-m">
                            <div class="modal-content">
                                <form method="POST" action="process/action_delete_quotation.php">
                                    <input type="hidden" name="id" value="<?= $quotationId ?>">
                                    <div class="modal-body text-center">
                                        <div class="mb-3">
                                            <img src="assets/img/icons/delete.svg" alt="img">
                                        </div>
                                        <h6 class="mb-1">Delete Quotation</h6>
                                        <p class="mb-3">Are you sure, you want to delete this quotation?</p>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Yes, Delete</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
							   <?php } ?>
						</tbody>
					</table>
				</div>
				<!-- /Table List -->

			</div>
			<!-- End Content -->
			
			<?php include 'layouts/footer.php'; ?>

		</div>
		
		<!-- End Page Content -->

		<!-- Start Filter -->
        <div class="offcanvas offcanvas-offset offcanvas-end" tabindex="-1" id="customcanvas">                                      
            <div class="offcanvas-header d-block pb-0">
                <div class="border-bottom d-flex align-items-center justify-content-between pb-3">
                    <h6 class="offcanvas-title">Filter</h6>
                    <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                        <i class="fa-solid fa-x"></i>
                    </button>
                </div>
            </div>          
            <div class="offcanvas-body pt-3">  
                <form method="GET" action="quotations.php" id="filter-form">
                    <!-- Clients -->
                    <div class="mb-3">
                        <label class="form-label">Clients</label>
                        <?php
                        $selectedClientNames = [];
                        if (!empty($selected_customers)) {
                            $ids = implode(",", array_map('intval', $selected_customers));
                            $res = mysqli_query($conn, "SELECT first_name FROM client WHERE id IN ($ids)");
                            while ($row = mysqli_fetch_assoc($res)) {
                                $selectedClientNames[] = htmlspecialchars($row['first_name']);
                            }
                        }
                        $clientText = !empty($selectedClientNames) ? implode(", ", $selectedClientNames) : "Select";
                        ?>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border customer-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
                                <?= $clientText ?>
                            </a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info">                            
                                <div class="mb-3">
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon fs-12"><i class="isax isax-search-normal"></i></span>
                                        <input type="text" class="form-control form-control-sm search-customer" placeholder="Search">
                                    </div>
                                </div>
                                <ul class="mb-3 customer-list">
                                    <li class="d-flex align-items-center justify-content-between mb-3">
                                        <label class="d-inline-flex align-items-center text-gray-9">
                                            <input class="form-check-input select-all m-0 me-2" type="checkbox" 
                                                <?= count($selected_customers) > 0 ? 'checked' : '' ?>>
                                            Select All
                                        </label>
                                        <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline reset-customer">Reset</a>
                                    </li>
                                    <?php 
                                    $clients = mysqli_query($conn, "SELECT * FROM client WHERE is_deleted = 0");
                                    while ($client = mysqli_fetch_assoc($clients)) {
                                        $checked = in_array($client['id'], $selected_customers) ? 'checked' : '';
                                        echo '<li>
                                            <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                                <input class="form-check-input m-0 me-2 customer-checkbox" type="checkbox" name="customer[]" value="'.$client['id'].'" '.$checked.'>
                                                <span class="avatar avatar-sm rounded-circle me-2">
                                                    <img src="'.(!empty($client['customer_image']) ? '../uploads/' . $client['customer_image'] : 'assets/img/users/user-16.jpg').'" class="flex-shrink-0 rounded-circle" width="24" height="24" alt="'.htmlspecialchars($client['first_name']).'">
                                                </span>
                                                '.htmlspecialchars($client['first_name']).'
                                            </label>
                                        </li>';
                                    }
                                    ?>
                                </ul>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="javascript:void(0);" class="btn btn-outline-white w-100 close-filter">Cancel</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="javascript:void(0);" class="btn btn-primary w-100 user-apply">Select</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="input-group position-relative">
                            <input type="text" class="form-control date-range bookingrange rounded-end" name="date_range" value="<?= $date_range ?>">
                            <input type="hidden" name="start_date" id="start_date" value="<?= $start_date ?>">
                            <input type="hidden" name="end_date" id="end_date" value="<?= $end_date ?>">
                            <span class="input-icon-addon fs-16 text-gray-9">
                                <i class="isax isax-calendar-2"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Quotation Id -->
                    <div class="mb-3">
                        <label class="form-label">Quotation Id</label>
                        <?php
                        $quotationIdText = !empty($selected_quotation_ids) ? implode(", ", $selected_quotation_ids) : "Select";
                        ?>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border quotation_id-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
                                <?= $quotationIdText ?>
                            </a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info">                         
                                <div class="mb-3">
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon fs-12"><i class="isax isax-search-normal"></i></span>
                                        <input type="text" class="form-control form-control-sm search-quotation_id" placeholder="Search">
                                    </div>
                                </div>
                                <ul class="mb-3 quotation_id-list">
                                    <li class="d-flex align-items-center justify-content-between mb-3">
                                        <label class="d-inline-flex align-items-center text-gray-9">
                                            <input class="form-check-input select-all m-0 me-2" type="checkbox" 
                                                <?= count($selected_quotation_ids) > 0 ? 'checked' : '' ?>>
                                            Select All
                                        </label>
                                        <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline reset-quotation_id">Reset</a>
                                    </li>
                                    <?php 
                                    $quotations = mysqli_query($conn, "SELECT quotation_id FROM quotation WHERE is_deleted = 0");
                                    while ($q = mysqli_fetch_assoc($quotations)) {
                                        $checked = in_array($q['quotation_id'], $selected_quotation_ids) ? 'checked' : '';
                                        echo '<li>
                                            <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                                <input class="form-check-input m-0 me-2 quotation_id-checkbox" type="checkbox" name="quotation_id[]" value="'.htmlspecialchars($q['quotation_id']).'" '.$checked.'>
                                                '.htmlspecialchars($q['quotation_id']).'
                                            </label>
                                        </li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <?php
                        $statusText = !empty($selected_statuses) ? implode(", ", array_map('ucfirst', $selected_statuses)) : "Select";
                        ?>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border status-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
                                <?= $statusText ?>
                            </a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info">    
                                <ul class="mb-3 status-list">
                                    <?php 
                                    $statuses = [
                                        'draft' => 'text-secondary',
                                        'sent' => 'text-info',
                                        'approval' => 'text-warning',
                                        'accepted' => 'text-success',
                                        'declined' => 'text-danger',
                                        'expired' => 'text-dark'
                                    ];
                                    foreach ($statuses as $label => $color) {
                                        $checked = in_array($label, $selected_statuses) ? 'checked' : '';
                                        echo '<li>
                                            <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                                <input class="form-check-input m-0 me-2 status-checkbox" type="checkbox" name="status[]" value="'.$label.'" '.$checked.'>
                                                <i class="fa-solid fa-circle fs-6 '.$color.' me-1"></i>'.ucfirst($label).'
                                            </label>
                                        </li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                     <div class="offcanvas-footer">
                        <div class="row g-2">
                            <div class="col-6"><a href="quotations.php" class="btn btn-outline-white w-100">Reset</a></div>
                            <div class="col-6"><button type="submit" class="btn btn-primary w-100" id="filter-submit">Apply</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Filter -->

		<!-- Start Delete Modal  -->
		 <div class="modal fade" id="multideleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form method="POST" id="multiDeleteForm" action="process/action_multi_delete_quotation.php">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Quotations</h6>
                            <p class="mb-3">Are you sure you want to delete the selected quotations?</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
		<!-- End Delete Modal  -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>
   
   <script>
$(document).ready(function() {

    // --- Save previous selection for Cancel ---
    function savePreviousSelection(container) {
        $(container).find("input[type='checkbox']").each(function() {
            $(this).data("prev", $(this).prop("checked"));
        });
    }

    // --- Update dropdown toggle label ---
    function updateDropdownLabel(type) {
        const checked = $(`.${type}-list input[type='checkbox']:checked`).not(".select-all");
        const names = [];
        checked.each(function() {
            const label = $(this).closest("label").text().trim();
            if (label) names.push(label);
        });

        const toggle = $(`.${type}-toggle`);
        if (names.length === 0) {
            toggle.text("Select");
        } else if (names.length <= 2) {
            toggle.text(names.join(", "));
        } else {
            toggle.text(`${names[0]}, ${names[1]} +${names.length - 2}`);
        }
    }

    // --- Initialize a dropdown (search, select all, reset, cancel, apply) ---
    function initDropdown(type) {
        const container = `.${type}-list`;
        const toggleBtn = `.${type}-toggle`;

        // Save previous selection when dropdown opens
        $(container).closest(".dropdown").on("show.bs.dropdown", function() {
            savePreviousSelection(container);
        });

        // Search functionality
        $(`.search-${type}`).on("keyup", function() {
            const value = $(this).val().toLowerCase();
            $(container).find("li").each(function() {
                if ($(this).find('.select-all').length > 0) return;
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(value) > -1);
            });
        });

        // Select All
        $(`${container} .select-all`).on("change", function() {
            const checked = $(this).is(":checked");
            $(container).find("input[type='checkbox']").not(this).prop("checked", checked);
            updateDropdownLabel(type);
        });

        // Individual checkbox change
        $(`${container} input[type='checkbox']`).not(".select-all").on("change", function() {
            updateDropdownLabel(type);
            const allChecked = $(container).find("input[type='checkbox']").not(".select-all").length ===
                               $(container).find("input[type='checkbox']:checked").not(".select-all").length;
            $(container).find(".select-all").prop("checked", allChecked);
        });

        // Reset button
        $(`.reset-${type}`).on("click", function() {
            $(container).find("input[type='checkbox']").prop("checked", false);
            $(container).find(".select-all").prop("checked", false);
            updateDropdownLabel(type);
        });

        // Cancel button restores previous selection
        $(container).closest(".dropdown-menu").on("click", ".close-filter", function() {
            $(container).find("input[type='checkbox']").each(function() {
                $(this).prop("checked", $(this).data("prev"));
            });
            updateDropdownLabel(type);
            $(this).closest(".dropdown-menu").removeClass("show");
        });

        // Apply button closes dropdown and updates label
        $(container).closest(".dropdown-menu").on("click", ".user-apply", function() {
            updateDropdownLabel(type);
            $(this).closest(".dropdown-menu").removeClass("show");
        });
    }

    // --- Initialize all dropdowns ---
    initDropdown("customer");
    initDropdown("quotation_id");
    initDropdown("status");

    // --- Initialize date range picker ---
    if ($('.bookingrange').length > 0) {
        var start = '<?= $start_date ?>' ? moment('<?= $start_date ?>') : moment().subtract(6, 'days');
        var end = '<?= $end_date ?>' ? moment('<?= $end_date ?>') : moment();

        function booking_range(start, end) {
            $('.bookingrange').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
            $('#start_date').val(start.format('YYYY-MM-DD'));
            $('#end_date').val(end.format('YYYY-MM-DD'));
        }

        $('.bookingrange').daterangepicker({
            startDate: start,
            endDate: end,
            locale: { 
                format: 'MM/DD/YYYY', 
                cancelLabel: 'Clear',
                applyLabel: 'Apply'
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, booking_range);

        booking_range(start, end);

        $('.bookingrange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#start_date').val('');
            $('#end_date').val('');
        });
    }

    // --- Initialize dropdown labels on page load ---
    updateDropdownLabel("customer");
    updateDropdownLabel("quotation_id");
    updateDropdownLabel("status");

});
</script>
<script>
	   // Multi-delete functionality
const multiDeleteModal = new bootstrap.Modal(document.getElementById('multideleteModal'));
const deleteBtn = document.querySelector('.delete-multiple');

// Toggle delete button visibility
function toggleDeleteBtn() {
    const anyChecked = document.querySelectorAll('.user-checkbox:checked').length > 0;
    deleteBtn.classList.toggle('d-none', !anyChecked);
}

// Delete button click
deleteBtn.addEventListener('click', function(e) {
    e.preventDefault();
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    const form = document.getElementById('multiDeleteForm');

    // Clear old hidden inputs
    form.querySelectorAll('input[name="quotation_ids[]"]').forEach(el => el.remove());

    // Add selected ids
    checkboxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'quotation_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    // Update modal text
    const modalTitle = document.querySelector('#multideleteModal h6');
    const modalMessage = document.querySelector('#multideleteModal p');

    if (checkboxes.length === 1) {
        modalTitle.textContent = 'Delete quotation';
        modalMessage.textContent = 'Are you sure you want to delete the selected quotation?';
    } else {
        modalTitle.textContent = 'Delete quotations';
        modalMessage.textContent = `Are you sure you want to delete the ${checkboxes.length} selected quotations?`;
    }

    multiDeleteModal.show();
});

// Select All functionality
document.getElementById('select-all').addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    toggleDeleteBtn();
});

// Individual checkbox change
document.querySelectorAll('.user-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', toggleDeleteBtn);
});

// Run once on page load (in case some boxes are pre-checked)
toggleDeleteBtn();

</script>
</body>
</html>