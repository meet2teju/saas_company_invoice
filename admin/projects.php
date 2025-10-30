<?php
include 'layouts/session.php';
include '../config/config.php';

$login_id = $_SESSION['crm_user_id']; // login.id
$role_id  = $_SESSION['role_id'];     // login.role_id

// Get role name from user_role
$role_query = mysqli_query($conn, "SELECT name FROM user_role WHERE id = $role_id LIMIT 1");
$role_row   = mysqli_fetch_assoc($role_query);
$role_name  = strtolower(trim($role_row['name'] ?? ''));

// --- FILTER LOGIC ---
$filters = [];
$selected_customers = $_POST['customer'] ?? [];
$selected_projects  = $_POST['project'] ?? [];
$date_range         = $_POST['date_range'] ?? '';

// Customer Filter
if (!empty($selected_customers)) {
    $ids = array_map('intval', $selected_customers);
    $clientConditions = array_map(fn($id) => "FIND_IN_SET($id, p.client_id)", $ids);
    $filters[] = '(' . implode(' OR ', $clientConditions) . ')';
}

// Project Filter
if (!empty($selected_projects)) {
    $pids = array_map('intval', $selected_projects);
    $filters[] = "p.id IN (" . implode(',', $pids) . ")";
}

// Date Range Filter
if (!empty($date_range)) {
    $dates = explode(" - ", $date_range);
    if (count($dates) === 2) {
        $start = date('Y-m-d', strtotime($dates[0]));
        $end   = date('Y-m-d', strtotime($dates[1]));
        $filters[] = "DATE(p.created_at) BETWEEN '$start' AND '$end'";
    }
}

// Base WHERE
$where = "WHERE p.is_deleted = 0";

// Add other filters
if (!empty($filters)) {
    $where .= " AND " . implode(" AND ", $filters);
}

// User-based filtering - FIXED
if ($role_name !== 'admin') { 
    // For non-admin users, show only projects they're assigned to
    $where .= " AND EXISTS (
        SELECT 1 FROM project_users pu
        WHERE pu.project_id = p.id AND pu.user_id = $login_id
    )";
}

// Final Query - FIXED: Use DISTINCT to avoid duplicate projects
$sql = "
SELECT DISTINCT p.*
FROM project p
$where
ORDER BY p.created_at DESC
";

$result = mysqli_query($conn, $sql);

// Fetch clients and projects for filter
$customers = mysqli_query($conn, "SELECT id, first_name, customer_image FROM client WHERE is_deleted = 0");
$projectList = mysqli_query($conn, "SELECT id, project_name FROM project WHERE is_deleted = 0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include 'layouts/title-meta.php'; ?> 
	<?php include 'layouts/head-css.php'; ?>
</head>

<body>
<div class="main-wrapper">
	<?php include 'layouts/menu.php'; ?>

    <div class="page-wrapper">
        <div class="content content-two">

            <!-- Page Header -->
             <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>

            <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3">
                <div><h6>Projects</h6></div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                    
                    <div class="table-search d-flex align-items-center mb-0">
                        <div class="search-input">
                            <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                        </div>
                    </div>
                    <a class="btn btn-outline-white fw-normal d-inline-flex align-items-center" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#customcanvas">
                        <i class="isax isax-filter me-1"></i>Filter
                    </a>
                    <!-- Export Dropdown -->
                    <div class="dropdown d-inline-block me-2">
                        <a href="#" class="btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="isax isax-export-1 me-1"></i> Export
                        </a>
                        <ul class="dropdown-menu p-3" style="min-width: 250px;">
                            <li>
                                <a href="#" class="dropdown-item fw-semibold toggle-submenu" data-target="#exportClient">Export Client</a>
                                <ul class="collapse list-unstyled ps-3 mt-1" id="exportClient">
                                    <li><a class="dropdown-item" href="./process/action_export_clinetpdf.php">Download as PDF</a></li>
                                    <li><a class="dropdown-item" href="./process/action_export_clinetexcle.php">Download as Excel</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="dropdown-item fw-semibold toggle-submenu" data-target="#exportContact">Export Contact Person</a>
                                <ul class="collapse list-unstyled ps-3 mt-1" id="exportContact">
                                    <li><a class="dropdown-item" href="#">Download as PDF</a></li>
                                    <li><a class="dropdown-item" href="#">Download as Excel</a></li>
                                </ul>
                            </li>
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
                            <li><a class="dropdown-item" href="import_contact_excel.php">Import Contacts</a></li>
                        </ul>
                    </div>
                <?php if (check_is_access_new("add_project") == 1) { ?> 
                    <div>
                        <a href="add-projects.php" class="btn btn-primary d-flex align-items-center">
                            <i class="isax isax-add-circle5 me-1"></i>New Project
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Search & Actions -->
            <!-- <div class="mb-3">
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
                            <?php if (!empty($selected_customers) || !empty($selected_projects) || !empty($date_range)): ?>
                                <a href="projects.php" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                                </a>
                            <?php endif; ?>

                       <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                        <i class="fa-regular fa-trash-can me-1"></i>Delete
                        </a>

                    </div>
                </div>
            </div> -->
<!-- Search & Actions -->
<div class="mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <!-- <div class="table-search d-flex align-items-center mb-0">
                <div class="search-input">
                    <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                </div>
            </div>
            <a class="btn btn-outline-white fw-normal d-inline-flex align-items-center" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#customcanvas">
                <i class="isax isax-filter me-1"></i>Filter
            </a> -->
            
            <!-- Display Active Filters -->
            <?php 
            $active_filters = [];
            
            // Customer filters
            if (!empty($selected_customers)) {
                $customer_names = [];
                $ids = implode(",", array_map('intval', $selected_customers));
                $res = mysqli_query($conn, "SELECT first_name FROM client WHERE id IN ($ids)");
                while ($row = mysqli_fetch_assoc($res)) {
                    $customer_names[] = htmlspecialchars($row['first_name']);
                }
                if (!empty($customer_names)) {
                    $active_filters[] = "Client: " . (count($customer_names) > 2 ? 
                        implode(", ", array_slice($customer_names, 0, 2)) . " +" . (count($customer_names) - 2) : 
                        implode(", ", $customer_names));
                }
            }
            
            // Project filters
            if (!empty($selected_projects)) {
                $project_names = [];
                $ids = implode(",", array_map('intval', $selected_projects));
                $res = mysqli_query($conn, "SELECT project_name FROM project WHERE id IN ($ids)");
                while ($row = mysqli_fetch_assoc($res)) {
                    $project_names[] = htmlspecialchars($row['project_name']);
                }
                if (!empty($project_names)) {
                    $active_filters[] = "Project: " . (count($project_names) > 2 ? 
                        implode(", ", array_slice($project_names, 0, 2)) . " +" . (count($project_names) - 2) : 
                        implode(", ", $project_names));
                }
            }
            
            // Date range filter
            if (!empty($date_range)) {
                $active_filters[] = "Date: " . htmlspecialchars($date_range);
            }
            ?>
            
            <!-- Display active filters and clear button -->
            <?php if (!empty($active_filters)): ?>
                <div class="d-flex align-items-center gap-2">
                    <!-- Active Filters Display -->
                    <div class="active-filters bg-light px-3 py-2 rounded d-flex align-items-center gap-2">
                        <small class="text-muted fw-bold">Active Filters:</small>
                        <?php foreach ($active_filters as $filter): ?>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                <?= $filter ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Clear Filter Button -->
                    <a href="projects.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                    </a>
                </div>
            <?php endif; ?>

            <!-- Multiple Delete Button -->
            <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                <i class="fa-regular fa-trash-can me-1"></i>Delete
            </a>
        </div>
    </div>
</div>
           

            <!-- Table -->
<div class="table-responsive">
    <table class="table table-nowrap datatable">
        <thead class="thead-light">
            <tr>
                <th class="no-sort"><input class="form-check-input" type="checkbox" id="select-all"></th>
                <th>Clients</th>
                <th>Project Name</th>
                <th>Project Code</th>
                <th>Created On</th>
                <th class="no-sort">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while ($row = mysqli_fetch_assoc($result)):
                $projectId = $row['id'];
                
                // Fetch clients for this project
                $clients = [];
                $clientIds = explode(',', $row['client_id']);
                $cleanIds = array_filter(array_map('intval', $clientIds));
                
                if (!empty($cleanIds)) {
                    $clientIdList = implode(',', $cleanIds);
                    $clientQuery = mysqli_query($conn, 
                        "SELECT first_name, customer_image 
                         FROM client 
                         WHERE id IN ($clientIdList) AND is_deleted = 0");
                    while ($cRow = mysqli_fetch_assoc($clientQuery)) {
                        $clients[] = $cRow;
                    }
                }
            ?>
            <tr>
                <td><input type="checkbox" class="form-check-input user-checkbox" value="<?= $row['id'] ?>"></td>
                
                <!-- Client image + name -->
                <td>
                    <div class="d-flex flex-column">
                        <?php foreach ($clients as $c): 
                            $img = !empty($c['customer_image']) ? '../uploads/' . htmlspecialchars($c['customer_image']) : '';
                            $initials = strtoupper(substr($c['first_name'], 0, 2));
                        ?>
                            <div class="d-flex align-items-center mb-2">
                                <?php if ($img): ?>
                                    <img src="<?= $img ?>" 
                                         onerror="this.src='assets/img/users/user-16.jpg';"
                                         class="rounded-circle me-2" 
                                         style="width:32px; height:32px; object-fit:cover;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                         style="width:32px; height:32px; font-size:12px;">
                                        <?= $initials ?>
                                    </div>
                                <?php endif; ?>
                                <span><?= htmlspecialchars($c['first_name']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </td>

                <td><?= htmlspecialchars($row['project_name']) ?></td>
                <td><?= htmlspecialchars($row['project_code']) ?></td>
                <td><?= date('d-m-Y', strtotime($row['created_at'])) ?></td>
                
                 <td class="action-item">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" class="custom-elipse">
                        <i class="isax isax-more"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (check_is_access_new("view_project") == 1) { ?>
                            <li>
                                <a href="project-details.php?id=<?= $projectId ?>" class="dropdown-item">
                                    <i class="isax isax-eye"></i>&nbsp;&nbsp;&nbsp;View
                                </a>
                            </li>
                        <?php } ?>
                        <?php if (check_is_access_new("edit_project") == 1) { ?>
                            <li>
                                <a href="edit-project.php?id=<?= $projectId ?>" class="dropdown-item">
                                    <i class="isax isax-edit me-2"></i>Edit
                                </a>
                            </li>
                        <?php } ?>
                        <?php if (check_is_access_new("delete_project") == 1) { ?>
                            <li>
                                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#delete_modal<?= $row['id'] ?>">
                                    <i class="isax isax-trash me-2"></i>Delete
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

        </div>

        <?php include 'layouts/footer.php'; ?>
    </div>
</div>

<!-- Individual Delete Modals -->
<?php
// Reset the result pointer to loop through projects again for modals
mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_assoc($result)):
?>
<div class="modal fade" id="delete_modal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-m">
        <div class="modal-content">
            <form method="POST" action="process/action_delete_project.php">
<input type="hidden" name="id" value="<?= $row['id'] ?>">
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <img src="assets/img/icons/delete.svg" alt="img">
                    </div>
                    <h6 class="mb-1">Delete Project</h6>
                    <p class="mb-3">Are you sure you want to delete "<?= htmlspecialchars($row['project_name']) ?>"?</p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-white me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endwhile; ?>

        <!-- Start Filter -->
        <div class="offcanvas offcanvas-offset offcanvas-end" tabindex="-1" id="customcanvas">
    <div class="offcanvas-header d-block pb-0">
        <div class="border-bottom d-flex align-items-center justify-content-between pb-3">
            <h6 class="offcanvas-title">Filter</h6>
            <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-x"></i></button>
        </div>
    </div>
    <div class="offcanvas-body pt-3">
        <form action="projects.php" method="POST">
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
                    if (!empty($selectedClientNames)) {
                        if (count($selectedClientNames) > 3) {
                            $clientText = implode(", ", array_slice($selectedClientNames, 0, 3)) . " +" . (count($selectedClientNames) - 3);
                        } else {
                            $clientText = implode(", ", $selectedClientNames);
                        }
                    } else {
                        $clientText = "Select";
                    }
                ?>
                <div class="dropdown">
                    <a class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border customer-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <?= $clientText ?>
                    </a>
                    <div class="dropdown-menu shadow-lg w-100 dropdown-info p-3">
                        <div class="mb-3">
                            <div class="input-icon-start position-relative">
                                <span class="input-icon-addon fs-12">
                                    <i class="isax isax-search-normal"></i>
                                </span>
                                <input type="text" class="form-control form-control-sm search-customer" placeholder="Search Customers">
                            </div>
                        </div>
                        <ul class="mb-3 list-unstyled customer-list">
                            <li class="d-flex align-items-center justify-content-between mb-2">
                                <label class="d-inline-flex align-items-center text-gray-9">
                                    <input class="form-check-input select-all m-0 me-2" type="checkbox" 
                                        <?= count($selected_customers) > 0 ? 'checked' : '' ?>> Select All
                                </label>
                                <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline reset-customer">Reset</a>
                            </li>
                            <?php
                            mysqli_data_seek($customers, 0);
                            while ($row = mysqli_fetch_assoc($customers)) {
                                $isChecked = in_array($row['id'], $selected_customers) ? 'checked' : '';
                                $clientImg = !empty($row['customer_image']) ? '../uploads/' . htmlspecialchars($row['customer_image']) : 'assets/img/users/user-16.jpg';
                            ?>
                            <li>
                                <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                    <input class="form-check-input m-0 me-2 customer-checkbox" type="checkbox" name="customer[]" value="<?= $row['id'] ?>" <?= $isChecked ?>>
                                    <span class="avatar avatar-sm rounded-circle me-2">
                                        <img src="<?= $clientImg ?>" class="flex-shrink-0 rounded-circle" width="24" height="24" alt="<?= htmlspecialchars($row['first_name']) ?>">
                                    </span>
                                    <?= htmlspecialchars($row['first_name']) ?>
                                </label>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Project Name -->
            <div class="mb-3">
                <label class="form-label">Project Name</label>
                <?php
                $selectedProjectNames = [];
                if (!empty($selected_projects)) {
                    $ids = implode(",", array_map('intval', $selected_projects));
                    $res = mysqli_query($conn, "SELECT project_name FROM project WHERE id IN ($ids)");
                    while ($row = mysqli_fetch_assoc($res)) {
                        $selectedProjectNames[] = htmlspecialchars($row['project_name']);
                    }
                }
                if (!empty($selectedProjectNames)) {
                    if (count($selectedProjectNames) > 3) {
                        $projectText = implode(", ", array_slice($selectedProjectNames, 0, 3)) . " +" . (count($selectedProjectNames) - 3);
                    } else {
                        $projectText = implode(", ", $selectedProjectNames);
                    }
                } else {
                    $projectText = "Select";
                }
                ?>
                <div class="dropdown">
                    <a class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border project-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <?= $projectText ?>
                    </a>
                    <div class="dropdown-menu shadow-lg w-100 dropdown-info p-3">
                        <div class="mb-3">
                            <div class="input-icon-start position-relative">
                                <span class="input-icon-addon fs-12">
                                    <i class="isax isax-search-normal"></i>
                                </span>
                                <input type="text" class="form-control form-control-sm search-project" placeholder="Search Projects">
                            </div>
                        </div>
                        <ul class="mb-3 list-unstyled project-list">
                            <li class="d-flex align-items-center justify-content-between mb-2">
                                <label class="d-inline-flex align-items-center text-gray-9">
                                    <input class="form-check-input select-all m-0 me-2" type="checkbox" 
                                        <?= count($selected_projects) > 0 ? 'checked' : '' ?>> Select All
                                </label>
                                <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline reset-project">Reset</a>
                            </li>
                            <?php
                            mysqli_data_seek($projectList, 0);
                            while ($row = mysqli_fetch_assoc($projectList)) {
                                $isChecked = in_array($row['id'], $selected_projects) ? 'checked' : '';
                            ?>
                            <li>
                                <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                    <input class="form-check-input m-0 me-2 project-checkbox" type="checkbox" name="project[]" value="<?= $row['id'] ?>" <?= $isChecked ?>>
                                    <?= htmlspecialchars($row['project_name']) ?>
                                </label>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Date Range -->
            <div class="mb-3">
                <label for="dateRangePicker" class="form-label">Date Range</label>
                <div class="input-group position-relative">
                    <input type="text" class="form-control date-range bookingrange rounded-end" name="date_range" id="dateRangePicker" value="<?= $date_range ?>">
                    <span class="input-icon-addon fs-16 text-gray-9">
                        <i class="isax isax-calendar-2"></i>
                    </span>
                </div>
            </div>

            <div class="offcanvas-footer">
                        <div class="row g-2">
                            <div class="col-6"><a href="projects.php" class="btn btn-outline-white w-100">Reset</a></div>
                            <div class="col-6"><button type="submit" class="btn btn-primary w-100" id="filter-submit">Apply</button></div>
                        </div>
                    </div>
        </form>
    </div>
</div>
        <!-- End Filter -->
        <!-- Multi Delete Modal -->
        <div class="modal fade" id="multideleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form method="POST" id="multiDeleteForm" action="process/action_multi_delete_project.php">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Projects</h6>
                            <p class="mb-3">Are you sure you want to delete the selected projects?</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-white me-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

<?php include 'layouts/vendor-scripts.php'; ?>

<script>
   const multiDeleteModal = new bootstrap.Modal(document.getElementById('multideleteModal'));
const deleteBtn = document.querySelector('.delete-multiple');
const checkboxes = document.querySelectorAll('.user-checkbox');
const selectAll = document.getElementById('select-all');

// Function to toggle delete button
function toggleDeleteBtn() {
    const checked = document.querySelectorAll('.user-checkbox:checked').length;
    if (checked > 0) {
        deleteBtn.classList.remove('d-none'); // Show button
    } else {
        deleteBtn.classList.add('d-none'); // Hide button
    }
}

// Delete multiple click
deleteBtn.addEventListener('click', function (e) {
    e.preventDefault();

    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    const form = document.getElementById('multiDeleteForm');
    form.querySelectorAll('input[name="project_ids[]"]').forEach(el => el.remove());

    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'project_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    multiDeleteModal.show(); // Always open modal
});

// Select all checkbox
selectAll.addEventListener('change', function () {
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    toggleDeleteBtn();
});

// Individual checkboxes
checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', toggleDeleteBtn);
});

</script>
<script>
$(document).ready(function() {
    // Initialize date range picker with preserved values
    if ($('.bookingrange').length > 0) {
        var start = moment().subtract(6, 'days');
        var end = moment();

        // If we have a date range from previous filter, parse it
        if ('<?= $date_range ?>') {
            var dates = '<?= $date_range ?>'.split(" - ");
            if (dates.length === 2) {
                start = moment(dates[0], 'MM/DD/YYYY');
                end = moment(dates[1], 'MM/DD/YYYY');
            }
        }

        function booking_range(start, end) {
            $('.bookingrange').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
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
        });
    }

    // Update dropdown labels based on selected values
  function updateDropdownLabels() {
    // Customers
    let customerLabels = [];
    $('.customer-checkbox:checked').each(function() {
        customerLabels.push($(this).closest('label').text().trim());
    });
    if (customerLabels.length > 3) {
        $('.customer-toggle').text(customerLabels.slice(0, 3).join(', ') + ' +' + (customerLabels.length - 3));
    } else {
        $('.customer-toggle').text(customerLabels.length > 0 ? customerLabels.join(', ') : 'Select');
    }

    // Projects
    let projectLabels = [];
    $('.project-checkbox:checked').each(function() {
        projectLabels.push($(this).closest('label').text().trim());
    });
    if (projectLabels.length > 3) {
        $('.project-toggle').text(projectLabels.slice(0, 3).join(', ') + ' +' + (projectLabels.length - 3));
    } else {
        $('.project-toggle').text(projectLabels.length > 0 ? projectLabels.join(', ') : 'Select');
    }
}

    // Initialize dropdown labels on page load
    updateDropdownLabels();

    // Update dropdown labels when checkboxes change
    $('.customer-checkbox, .project-checkbox').change(function() {
        updateDropdownLabels();
        
        // Update "Select All" checkbox for each section
        if ($(this).hasClass('customer-checkbox')) {
            const allChecked = $('.customer-checkbox:not(:checked)').length === 0;
            $('.customer-list .select-all').prop('checked', allChecked);
        }
        
        if ($(this).hasClass('project-checkbox')) {
            const allChecked = $('.project-checkbox:not(:checked)').length === 0;
            $('.project-list .select-all').prop('checked', allChecked);
        }
    });

    // Select All functionality for customers
    $('.customer-list .select-all').change(function() {
        $('.customer-checkbox').prop('checked', this.checked);
        updateDropdownLabels();
    });

    // Select All functionality for projects
    $('.project-list .select-all').change(function() {
        $('.project-checkbox').prop('checked', this.checked);
        updateDropdownLabels();
    });

    // Reset functionality for customers
    $('.reset-customer').click(function() {
        $('.customer-checkbox, .customer-list .select-all').prop('checked', false);
        updateDropdownLabels();
    });

    // Reset functionality for projects
    $('.reset-project').click(function() {
        $('.project-checkbox, .project-list .select-all').prop('checked', false);
        updateDropdownLabels();
    });

    // Search functionality for customers
    $(".search-customer").on("keyup", function() {
        const value = $(this).val().toLowerCase();
        $(".customer-list li").each(function() {
            // Skip the first li (Select All and Reset)
            if ($(this).find('.select-all').length > 0) return;
            
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(value) > -1);
        });
    });

    // Search functionality for projects
    $(".search-project").on("keyup", function() {
        const value = $(this).val().toLowerCase();
        $(".project-list li").each(function() {
            // Skip the first li (Select All and Reset)
            if ($(this).find('.select-all').length > 0) return;
            
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(value) > -1);
        });
    });
});
</script>
</body>
</html>