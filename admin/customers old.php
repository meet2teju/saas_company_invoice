<?php
include 'layouts/session.php';
include '../config/config.php';

// ✅ Set selected filter variables here
$selectedClients   = $_POST['customer'] ?? [];
$selectedCountries = $_POST['country'] ?? [];
$dateRange         = $_POST['date_range'] ?? '';
$selectedBalances  = $_POST['balance'] ?? [];

// Initialize
$filters = [];
$join = "LEFT JOIN client_address ca ON c.id = ca.client_id";

// Filter: by selected customer(s)
if (!empty($_POST['customer']) && is_array($_POST['customer'])) {
    $ids = array_map('intval', $_POST['customer']);
    $filters[] = "c.id IN (" . implode(',', $ids) . ")";
}

// Filter: by selected country (from billing_country in client_address)
if (!empty($_POST['country']) && is_array($_POST['country'])) {
    $country_ids = array_map('intval', $_POST['country']);
    $filters[] = "ca.billing_country IN (" . implode(',', $country_ids) . ")";
}

// Filter: by balance thresholds
if (!empty($_POST['balance']) && is_array($_POST['balance'])) {
    $balance_filters = array_map('floatval', $_POST['balance']);
    $balance_conditions = [];
    foreach ($balance_filters as $value) {
        $balance_conditions[] = "c.current_amount >= {$value}";
    }
    $filters[] = '(' . implode(' OR ', $balance_conditions) . ')';
}

// Filter: by created_at date range
if (!empty($_POST['date_range'])) {
    $range = explode(' - ', $_POST['date_range']);
    if (count($range) === 2) {
        $start = date('Y-m-d', strtotime($range[0]));
        $end = date('Y-m-d', strtotime($range[1]));
        $filters[] = "DATE(c.created_at) BETWEEN '$start' AND '$end'";
    }
}

// Combine all filters into WHERE clause
$where = "WHERE c.is_deleted = 0";
if (!empty($filters)) {
    $where .= " AND " . implode(" AND ", $filters);
}

// Final SQL Query
// $sql = "SELECT 
//     c.*, 
//     COALESCE(SUM(i.total_amount), 0) AS total_invoice
// FROM client c
// LEFT JOIN invoice i ON i.client_id = c.id
// GROUP BY c.id
// ORDER BY c.created_at DESC";
$sql = "SELECT 
    c.*, 
    COALESCE(SUM(i.total_amount), 0) AS total_invoice
FROM client c
$join
LEFT JOIN invoice i ON i.client_id = c.id
$where
GROUP BY c.id
ORDER BY c.created_at DESC";

$result = mysqli_query($conn, $sql);

// Data for filter dropdowns
$countries = mysqli_query($conn, "SELECT * FROM countries ORDER BY name");
$customers = mysqli_query($conn, "SELECT * FROM client WHERE is_deleted = 0");
$balance_query = mysqli_query($conn, "SELECT DISTINCT current_amount FROM client_bank ORDER BY current_amount ASC");
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

        <!-- ========================
			Start Page Content
		========================= -->

        <div class="page-wrapper">

			<!-- Start Content -->
            <div class="content content-two">

                <!-- Page Header -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo ($_SESSION['message_type'] == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>

                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <h6>Clients</h6>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                       <!-- Export Dropdown -->
               <div class="dropdown d-inline-block me-2">
                <a href="#" class="btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                    <i class="isax isax-export-1 me-1"></i> Export
                </a>
                <ul class="dropdown-menu p-3" style="min-width: 250px;">
                    <!-- Export Client -->
                    <li>
                    <a href="#" class="dropdown-item fw-semibold toggle-submenu" data-target="#exportClient">Export Client</a>
                    <ul class="collapse list-unstyled ps-3 mt-1" id="exportClient">
                        <li><a class="dropdown-item" href="./process/action_export_clinetpdf.php">Download as PDF</a></li>
                        <li><a class="dropdown-item" href="./process/action_export_clinetexcle.php">Download as Excel</a></li>
                    </ul>
                    </li>

                    <!-- Export Contact Person -->
                    <li>
                    <a href="#" class="dropdown-item fw-semibold toggle-submenu" data-target="#exportContact">Export Contact Person</a>
                    <ul class="collapse list-unstyled ps-3 mt-1" id="exportContact">
                        <li><a class="dropdown-item" href="#">Download as PDF</a></li>
                        <li><a class="dropdown-item" href="#">Download as Excel</a></li>
                    </ul>
                    </li>

                    <!-- Export Current View -->
                    <li>
                    <a href="#" class="dropdown-item fw-semibold toggle-submenu" data-target="#exportCurrent">Current View</a>
                    <ul class="collapse list-unstyled ps-3 mt-1" id="exportCurrent">
                        <li><a class="dropdown-item" href="#">Download as PDF</a></li>
                        <li><a class="dropdown-item" href="#">Download as Excel</a></li>
                    </ul>
                    </li>
                </ul>
                    </div>

                <!-- Import Dropdown -->
                <div class="dropdown d-inline-block">
                    <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="isax isax-import me-1"></i> Import
                    </a>
                    <ul class="dropdown-menu p-2">
                        <li><a class="dropdown-item" href="import_client_excel.php">Import Clients</a></li>
                        <!-- <li><a class="dropdown-item" href="import_contact_excel.php">Import Contacts</a></li> -->
                    </ul>
                </div>
                <?php if (check_is_access_new("add_client") == 1) { ?> 
                        <div>
                            <a href="add-customer.php" class="btn btn-primary d-flex align-items-center">
                                <i class="isax isax-add-circle5 me-1"></i>New Client
                            </a>
                        </div>
                         <?php } ?>
                    </div>
                </div>
                <!-- End Page Header -->

                <!-- Table Search Start -->
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="table-search d-flex align-items-center mb-0">
                                <div class="search-input">
                                    <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                                </div>
                            </div>
                            <a class="btn btn-outline-white fw-normal d-inline-flex align-items-center" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#customcanvas">
                                <i class="isax isax-filter me-1"></i>Filter
                            </a>
                          <?php if (
                                    !empty($selectedClients) || 
                                    !empty($selectedCountries) || 
                                    !empty($dateRange) || 
                                    !empty($selectedBalances)
                                ): ?>
                                    <a href="customers.php" class="btn btn-outline-secondary">
                                        <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                                    </a>
                                <?php endif; ?>


                        <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                            <i class="fa-regular fa-trash-can me-1"></i>Delete
                        </a>


                        </div>
                        
                    </div>

                   
                </div>
                <!-- Table Search End -->

                <!-- Table List -->
                <div class="table-responsive">
                    <table class="table table-nowrap datatable">
                        <thead class="thead-light">
                            <tr>
                                <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                    </div>
                                </th>
                                <th>Clients</th>
                                <th>Company Name</th>
                                <th>Email</th>
                                <th>Work Phone</th>
                                <!-- <th>Country</th> -->
                                <!-- <th>Balance</th> -->
                                <!-- <th class="no-sort">Total Invoice</th> -->
                                <th>Created On</th>
                                <!-- <th class="no-sort">Status</th> -->
                                <th class="no-sort"></th>
                                <th class="no-sort"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                                $clientId = $row['id'];
                                $clientImg = !empty($row['customer_image']) ? '../uploads/' . htmlspecialchars($row['customer_image']) : 'assets/img/users/user-16.jpg';
                            ?>
                            <tr>
                                <td><div class="form-check form-check-md"><input type="checkbox" class="form-check-input user-checkbox" name="client_ids[]" value="<?= $clientId ?>"></div></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="<?= $clientImg ?>" onerror="this.src='assets/img/users/user-16.jpg';">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><?= htmlspecialchars($row['first_name']) ?></h6>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['company_name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                                <!-- <td>$&nbsp;<?= htmlspecialchars($row['current_amount'] ?? '0.00') ?></td>
                                <td>$&nbsp;<?= htmlspecialchars($row['total_invoice']) ?></td> -->

                                <td><?= !empty($row['created_at']) ? date('d-m-Y', strtotime($row['created_at'])) : '' ?></td>
                                <td>
                                    <a href="add-invoice.php?client_id=<?= $clientId ?>" class="btn btn-sm btn-outline-white d-inline-flex align-items-center me-1">
                                            <i class="isax isax-add-circle me-1"></i> Invoice
                                        </a>

                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown"><i class="isax isax-more"></i></a>
                                    <ul class="dropdown-menu">
                                        <?php if (check_is_access_new("view_client") == 1) { ?> 
                                        <li><a href="customer-details.php?id=<?= $clientId ?>" class="dropdown-item"><i class="isax isax-eye me-2"></i>View</a></li>
                                         <?php } ?>
                                        <?php if (check_is_access_new("edit_client") == 1) { ?> 
                                        <li><a href="edit-customer.php?id=<?= $clientId ?>" class="dropdown-item"><i class="isax isax-edit me-2"></i>Edit</a></li>
                                         <?php } ?>
                                        <?php if (check_is_access_new("delete_client") == 1) { ?> 
                                        <li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#delete_modal<?= $clientId ?>"><i class="isax isax-trash me-2"></i>Delete</a></li>
                                         <?php } ?>
                                    </ul>
                                </td>
                            </tr>
                              <!-- Delete Modal Start -->
        <div class="modal fade" id="delete_modal<?= $clientId ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form method="POST" action="process/action_delete_client.php">
                        <input type="hidden" name="id" value="<?= $clientId ?>">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Client</h6>
                            <p class="mb-3">Are you sure, you want to delete Client?</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Delete Modal End -->
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <!-- /Table List -->

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
                    <h6 class="offcanvas-title">Filter</h6>
                    <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                </div>
            </div>
            <div class="offcanvas-body pt-3">
                <form action="customers.php" method="POST">
                 <div class="mb-3">
                    <label class="form-label">Clients</label>
                    <div class="dropdown">
                        <a
                            class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border customer-toggle"
                            data-bs-toggle="dropdown"
                            data-bs-auto-close="outside"
                            aria-expanded="false">
                            <?php
                            $selectedCustomerNames = [];
                            if (!empty($selectedClients)) {
                                $customerIds = implode(",", $selectedClients);
                                $customerQuery = mysqli_query($conn, "SELECT first_name FROM client WHERE id IN ($customerIds)");
                                while ($customer = mysqli_fetch_assoc($customerQuery)) {
                                    $selectedCustomerNames[] = $customer['first_name'];
                                }
                            }
                            echo !empty($selectedCustomerNames) ? implode(", ", $selectedCustomerNames) : 'Select';
                            ?>
                </a>

                        <div class="dropdown-menu shadow-lg w-100 dropdown-info p-3">
                            <!-- Search Box -->
                            <div class="mb-3">
                                <div class="input-icon-start position-relative">
                                    <span class="input-icon-addon fs-12">
                                        <i class="isax isax-search-normal"></i>
                                    </span>
                                    <input type="text" class="form-control form-control-sm search-customer" placeholder="Search Customers">
                                </div>
                            </div>

                            <!-- Customer Checkbox List -->
                            <ul class="mb-3 list-unstyled customer-list">
                                <li class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="d-inline-flex align-items-center text-gray-9">
                                        <input class="form-check-input select-all m-0 me-2" type="checkbox" <?php echo (count($selectedClients) == mysqli_num_rows($customers)) ? 'checked' : ''; ?>> Select All
                                    </label>
                                    <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline reset-customer">Reset</a>
                                </li>

                                <?php 
                                // Reset pointer for customers query
                                mysqli_data_seek($customers, 0);
                                while ($row = mysqli_fetch_assoc($customers)) {
                                    $clientImg = !empty($row['customer_image']) ? '../uploads/' . htmlspecialchars($row['customer_image']) : 'assets/img/users/user-16.jpg';
                                    $isChecked = in_array($row['id'], $selectedClients) ? 'checked' : '';
                                ?>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox" name="customer[]" value="<?= $row['id'] ?>" <?= $isChecked ?>>
                                            <span class="avatar avatar-sm rounded-circle me-2">
                                                <img src="<?= $clientImg ?>" class="flex-shrink-0 rounded-circle" width="24" height="24" alt="<?= htmlspecialchars($row['first_name']) ?>">
                                            </span>
                                            <?= htmlspecialchars($row['first_name']) ?>
                                        </label>
                                    </li>
                                <?php } ?>
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

                    <div class="mb-3">
                        <label class="form-label">Country</label>
                        <div class="dropdown">
                           <a href="javascript:void(0);" class="dropdown-toggle country-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
                            <?php
                            $selectedCountryNames = [];
                            if (!empty($selectedCountries)) {
                                $countryIds = implode(",", $selectedCountries);
                                $countryQuery = mysqli_query($conn, "SELECT name FROM countries WHERE id IN ($countryIds)");
                                while ($country = mysqli_fetch_assoc($countryQuery)) {
                                    $selectedCountryNames[] = $country['name'];
                                }
                            }
                            echo !empty($selectedCountryNames) ? implode(", ", $selectedCountryNames) : 'Select';
                            ?>
                            </a>

                            
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info">
                                <div class="mb-3">
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon fs-12">
											<i class="isax isax-search-normal"></i>
										</span>
                                        <input type="text" class="form-control form-control-sm search-country" placeholder="Search Countries">
                                    </div>
                                </div>
                                <ul class="mb-3 country-list">
                                    <li class="d-flex align-items-center justify-content-between mb-3">
                                        <label class="d-inline-flex align-items-center text-gray-9">
                                            <input class="form-check-input select-all m-0 me-2" type="checkbox" <?php echo (count($selectedCountries) == mysqli_num_rows($countries)) ? 'checked' : ''; ?>> Select All
                                        </label>
                                        <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline reset-country">Reset</a>
                                    </li>
                                    <?php 
                                    // Reset pointer for countries query
                                    mysqli_data_seek($countries, 0);
                                    while ($row = mysqli_fetch_assoc($countries)) {
                                        $isChecked = in_array($row['id'], $selectedCountries) ? 'checked' : '';
                                    ?>
                                        <li>
                                            <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                                <input class="form-check-input m-0 me-2" type="checkbox" name="country[]" value="<?= $row['id'] ?>" <?= $isChecked ?>>
                                                <span class="me-2">
                                                    <!-- <img src="assets/img/icons/<?= htmlspecialchars($row['flag']) ?>" class="flex-shrink-0" alt="<?= htmlspecialchars($row['name']) ?>"> -->
                                                </span>
                                                <?= htmlspecialchars($row['name']) ?>
                                            </label>
                                        </li>
                                    <?php } ?>
                                </ul>

                                
                            </div>
                        </div>
                    </div>
                  <div class="mb-3">
                    <label for="dateRangePicker" class="form-label">Date Range</label>
                    <div class="input-group position-relative">
                        <input type="text" 
                            class="form-control date-range bookingrange rounded-end" 
                            name="date_range"
                            id="dateRangePicker"
                            value="<?= htmlspecialchars($dateRange) ?>">
                        <span class="input-icon-addon fs-16 text-gray-9">
                            <i class="isax isax-calendar-2"></i>
                        </span>
                    </div>
                </div>

                    <div class="mb-3">
                        <label class="form-label">Balance</label>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light  d-flex align-items-center justify-content-start fs-13 fw-normal border" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
                                <?php
                                $balanceTexts = [];
                                if (!empty($selectedBalances)) {
                                    foreach ($selectedBalances as $balance) {
                                        $balanceTexts[] = '$' . number_format($balance, 2);
                                    }
                                }
                                echo !empty($balanceTexts) ? implode(", ", $balanceTexts) : 'Select';
                                ?>
							</a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info">
                                <div class="filter-range">
                                  <?php 
                                  // Reset pointer for balance query
                                  mysqli_data_seek($balance_query, 0);
                                  while ($row = mysqli_fetch_assoc($balance_query)) {
                                      $isChecked = in_array($row['current_amount'], $selectedBalances) ? 'checked' : '';
                                  ?>
                                <li>
                                    <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                        <input class="form-check-input m-0 me-2" type="checkbox" name="balance[]" value="<?= $row['current_amount'] ?>" <?= $isChecked ?>>
                                        $<?= number_format($row['current_amount'], 2) ?>
                                    </label>
                                </li>
                            <?php } ?>
                                </div>

                            </div>
                        </div>
                    </div>
                 
                     <div class="offcanvas-footer">
                        <div class="row g-2">
                            <div class="col-6"><a href="customers.php" class="btn btn-outline-white w-100">Reset</a></div>
                            <div class="col-6"><button type="submit" class="btn btn-primary w-100" id="filter-submit">Apply</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Filter -->


     
         <!-- /multiDelete Modal Start -->
        <div class="modal fade" id="multideleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form method="POST" id="multiDeleteForm" action="process/action_multi_delete_client.php">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Clients</h6>
                            <p class="mb-3">Are you sure you want to delete the selected Clients?</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-white me-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <!-- /Delete Modal End -->
    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>

<script>
	        // Multi-delete functionality
       const multiDeleteModal = new bootstrap.Modal(document.getElementById('multideleteModal'));
const deleteBtn = document.querySelector('.delete-multiple');
const selectAll = document.getElementById('select-all');
const checkboxes = document.querySelectorAll('.user-checkbox');

// Function to toggle delete button visibility
function toggleDeleteBtn() {
    const anyChecked = document.querySelectorAll('.user-checkbox:checked').length > 0;
    deleteBtn.classList.toggle('d-none', !anyChecked);
}

// Show modal on delete button click
deleteBtn.addEventListener('click', function(e) {
    e.preventDefault();
    const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
    const form = document.getElementById('multiDeleteForm');

    // Remove previous hidden inputs
    form.querySelectorAll('input[name="client_ids[]"]').forEach(el => el.remove());

    // Add new hidden inputs
    selectedCheckboxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'client_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    // Update modal text based on selection count
    const modalTitle = document.querySelector('#multideleteModal h6');
    const modalMessage = document.querySelector('#multideleteModal p');

    if (selectedCheckboxes.length === 1) {
        modalTitle.textContent = 'Delete Client';
        modalMessage.textContent = 'Are you sure you want to delete the selected client?';
    } else {
        modalTitle.textContent = 'Delete Clients';
        modalMessage.textContent = `Are you sure you want to delete the ${selectedCheckboxes.length} selected clients?`;
    }

    multiDeleteModal.show();
});

// Select All functionality
selectAll.addEventListener('change', function() {
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    toggleDeleteBtn();
});

// Individual checkbox change
checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', toggleDeleteBtn);
});

// Initialize button visibility on page load
toggleDeleteBtn();

</script>
<script>
$(document).ready(function () {

    // --- Save previous selection for Cancel ---
    function savePreviousSelection(container) {
        $(container).find("input[type='checkbox']").each(function() {
            $(this).data("prev", $(this).prop("checked"));
        });
    }

function updateDropdownLabel(type, limit = 3) {
    // Get all checked checkboxes (excluding "Select All")
    const checked = $(`.${type}-list input[type='checkbox']:checked`).not(".select-all");

    // Collect their label texts
    const names = [];
    checked.each(function () {
        const label = $(this).closest("label").text().trim();
        if (label) names.push(label);
    });

    // Find the toggle button text element
    const toggle = $(`.${type}-toggle`);

    // Set display text
    if (names.length === 0) {
        toggle.text("Select");
    } else if (names.length > limit) {
        let visible = names.slice(0, limit).join(", ");
        let extra = names.length - limit;
        toggle.text(`${visible} +${extra}`);
    } else {
        toggle.text(names.join(", "));
    }
}

// Bind change event for all dropdown types
$(document).on("change", ".customer-list input[type=checkbox]", function () {
    updateDropdownLabel("customer");
});

$(document).on("change", ".country-list input[type=checkbox]", function () {
    updateDropdownLabel("country");
});

$(document).on("change", ".balance-list input[type=checkbox]", function () {
    updateDropdownLabel("balance");
});

    // --- Initialize dropdown (search, select all, reset, cancel, apply) ---
    function initDropdown(type) {
        const container = `.${type}-list`;

        // Save previous selection when dropdown opens
        $(container).closest(".dropdown").on("show.bs.dropdown", function() {
            savePreviousSelection(container);
        });

        // Search functionality
        $(`.search-${type}`).on("keyup", function() {
            const value = $(this).val().toLowerCase();
            $(container).find("li").each(function() {
                if ($(this).find('.select-all').length > 0) return; // skip Select All / Reset
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
            const allChecked = $(container).find("input[type='checkbox']").not(".select-all").length ===
                               $(container).find("input[type='checkbox']:checked").not(".select-all").length;
            $(container).find(".select-all").prop("checked", allChecked);
            updateDropdownLabel(type);
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
            const allChecked = $(container).find("input[type='checkbox']").not(".select-all").length ===
                               $(container).find("input[type='checkbox']:checked").not(".select-all").length;
            $(container).find(".select-all").prop("checked", allChecked);
            updateDropdownLabel(type);
            $(this).closest(".dropdown-menu").removeClass("show");
        });

        // Apply / Select button
        $(container).closest(".dropdown-menu").on("click", ".user-apply", function() {
            const allChecked = $(container).find("input[type='checkbox']").not(".select-all").length ===
                               $(container).find("input[type='checkbox']:checked").not(".select-all").length;
            $(container).find(".select-all").prop("checked", allChecked);
            updateDropdownLabel(type);
            $(this).closest(".dropdown-menu").removeClass("show");
        });
    }

    // --- Initialize all dropdowns ---
    initDropdown("customer");
    initDropdown("country");
    initDropdown("balance");

    // --- Initialize labels on page load ---
    updateDropdownLabel("customer");
    updateDropdownLabel("country");
    updateDropdownLabel("balance");

    // --- Date Range Picker ---
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
                applyLabel: 'Apply',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
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

});
</script>


<script>
    document.querySelectorAll('.dropdown .no-close').forEach(function (item) {
        item.addEventListener('click', function (e) {
            e.stopPropagation(); // prevent dropdown from closing
        });
    });

    // Optional: Collapse others when opening one
    document.querySelectorAll('.dropdown-toggle[data-bs-toggle="collapse"]').forEach(link => {
        link.addEventListener('click', function () {
            const target = this.getAttribute('data-bs-target');
            document.querySelectorAll('.dropdown-menu .collapse').forEach(el => {
                if (el.id !== target.replace('#', '')) {
                    bootstrap.Collapse.getOrCreateInstance(el).hide();
                }
            });
        });
    });
</script>
<script>
  document.querySelectorAll('.toggle-submenu').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      const targetId = this.getAttribute('data-target');
      const targetCollapse = document.querySelector(targetId);
      const bsTarget = bootstrap.Collapse.getOrCreateInstance(targetCollapse);

      // Collapse all other submenus
      document.querySelectorAll('.dropdown-menu .collapse').forEach(item => {
        if (item !== targetCollapse) {
          bootstrap.Collapse.getOrCreateInstance(item).hide();
        }
      });

      // Toggle the clicked one
      if (targetCollapse.classList.contains('show')) {
        bsTarget.hide();
      } else {
        bsTarget.show();
      }
    });
  });

  // Prevent dropdown from closing on inner click
  document.querySelectorAll('.dropdown-menu').forEach(menu => {
    menu.addEventListener('click', e => e.stopPropagation());
  });
</script>


</body>

</html>