<?php
include 'layouts/session.php';
include '../config/config.php';

$login_id = $_SESSION['crm_user_id'];
$role_id  = $_SESSION['role_id'];

// Get role name from user_role
$role_query = mysqli_query($conn, "SELECT name FROM user_role WHERE id = $role_id LIMIT 1");
$role_row   = mysqli_fetch_assoc($role_query);
$role_name  = strtolower(trim($role_row['name'] ?? ''));

// --- FILTER LOGIC ---
$filters = [];
$selected_projects = $_POST['project'] ?? [];
$selected_statuses = $_POST['status'] ?? [];
$date_range        = $_POST['date_range'] ?? '';

// Project Filter
if (!empty($selected_projects)) {
    $pids = array_map('intval', $selected_projects);
    $filters[] = "pt.project_id IN (" . implode(',', $pids) . ")";
}

// Status Filter - using status_id directly
if (!empty($selected_statuses)) {
    $sids = array_map('intval', $selected_statuses);
    $filters[] = "pt.status_id IN (" . implode(',', $sids) . ")";
}

// Date Range Filter
if (!empty($date_range)) {
    $dates = explode(" - ", $date_range);
    if (count($dates) === 2) {
        $start = date('Y-m-d', strtotime($dates[0]));
        $end   = date('Y-m-d', strtotime($dates[1]));
        $filters[] = "(DATE(pt.start_date) BETWEEN '$start' AND '$end' OR DATE(pt.end_date) BETWEEN '$start' AND '$end')";
    }
}

// Base WHERE
$where = "WHERE pt.is_deleted = 0";

// Add other filters
if (!empty($filters)) {
    $where .= " AND " . implode(" AND ", $filters);
}

// ROLE-BASED ACCESS CONTROL - Same as expenses.php
// Check if user has admin role (role_id = 1) 
if ($role_id != 1) {
    // For non-admin users (role_id != 1), show only tasks from projects they're assigned to
    $where .= " AND EXISTS (
        SELECT 1 FROM project_users pu
        WHERE pu.project_id = pt.project_id AND pu.user_id = $login_id
    )";
}

// Final Query - removed task_status join
$sql = "
SELECT pt.*, 
       p.project_name,
       p.project_code,
       c.first_name as client_name,
       c.customer_image as client_image
FROM project_task pt
LEFT JOIN project p ON pt.project_id = p.id
LEFT JOIN client c ON p.client_id = c.id
$where
ORDER BY pt.created_at DESC
";

$result = mysqli_query($conn, $sql);

// Fetch projects for filter
$projectList = mysqli_query($conn, "SELECT id, project_name FROM project WHERE is_deleted = 0");

// Define status options manually since task_status table doesn't exist
$statusOptions = [
    1 => ['name' => 'Pending', 'color' => '#ffc107'],
    2 => ['name' => 'In Progress', 'color' => '#17a2b8'],
    3 => ['name' => 'Completed', 'color' => '#28a745'],
    4 => ['name' => 'On Hold', 'color' => '#6c757d'],
    5 => ['name' => 'Cancelled', 'color' => '#dc3545']
];
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
                <div><h6>Tasks</h6></div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">

                    <!-- Export Dropdown -->
                    <!-- <div class="dropdown d-inline-block me-2">
                        <a href="#" class="btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="isax isax-export-1 me-1"></i> Export
                        </a>
                        <ul class="dropdown-menu p-3" style="min-width: 250px;">
                            <li>
                                <a href="#" class="dropdown-item fw-semibold toggle-submenu" data-target="#exportTasks">Export Tasks</a>
                                <ul class="collapse list-unstyled ps-3 mt-1" id="exportTasks">
                                    <li><a class="dropdown-item" href="./process/action_export_taskpdf.php">Download as PDF</a></li>
                                    <li><a class="dropdown-item" href="./process/action_export_taskexcel.php">Download as Excel</a></li>
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
                    </div> -->

                    <!-- Import Dropdown -->
                    <!-- <div class="dropdown d-inline-block">
                        <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="isax isax-import me-1"></i> Import
                        </a>
                        <ul class="dropdown-menu p-2">
                            <li><a class="dropdown-item" href="import_tasks_excel.php">Import Tasks</a></li>
                        </ul>
                    </div> -->
                    
                    
                    

                    
                    <div class="table-search d-flex align-items-center mb-0">
                        <div class="search-input">
                            <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                        </div>
                    </div>
                    <a class="btn btn-outline-white fw-normal d-inline-flex align-items-center" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#customcanvas">
                        <i class="isax isax-filter me-1"></i>Filter
                    </a>
                    <div>
                        <a href="add-task.php" class="btn btn-primary d-flex align-items-center">
                            <i class="isax isax-add-circle5 me-1"></i>New Task
                        </a>
                    </div>
                   
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
                        <?php if (!empty($selected_projects) || !empty($selected_statuses) || !empty($date_range)): ?>
                            <a href="project-tasks.php" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                            </a>
                        <?php endif; ?>

                        <!-- Multiple Delete Button - Added here --
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
            
            // Project filters
            if (!empty($selected_projects)) {
                $project_names = [];
                $ids = implode(",", array_map('intval', $selected_projects));
                $res = mysqli_query($conn, "SELECT project_name FROM project WHERE id IN ($ids)");
                while ($row = mysqli_fetch_assoc($res)) {
                    $project_names[] = htmlspecialchars($row['project_name']);
                }
                if (!empty($project_names)) {
                    $active_filters[] = "Project: " . implode(", ", $project_names);
                }
            }
            
            // Status filters
            if (!empty($selected_statuses)) {
                $status_names = [];
                foreach ($selected_statuses as $statusId) {
                    if (isset($statusOptions[$statusId])) {
                        $status_names[] = $statusOptions[$statusId]['name'];
                    }
                }
                if (!empty($status_names)) {
                    $active_filters[] = "Status: " . implode(", ", $status_names);
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
                    <a href="project-tasks.php" class="btn btn-outline-secondary">
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
                            <!-- Checkbox column for multiple selection -->
                            <th class="no-sort"><input class="form-check-input" type="checkbox" id="select-all"></th>
                            <th>Project</th>
                            <th>Task Name</th>
                            <th>Client</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Hours</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): 
                            $taskId = $row['id'];
                            
                            // Format dates
                            $start_date = !empty($row['start_date']) ? date('d-m-Y', strtotime($row['start_date'])) : '-';
                            $end_date = !empty($row['end_date']) ? date('d-m-Y', strtotime($row['end_date'])) : '-';
                            
                            // Client image handling
                            $clientImg = !empty($row['client_image']) ? '../uploads/' . htmlspecialchars($row['client_image']) : '';
                            $clientInitials = !empty($row['client_name']) ? strtoupper(substr($row['client_name'], 0, 2)) : 'NA';
                            
                            // Status handling
                            $statusId = $row['status_id'] ?? 1;
                            $statusName = $statusOptions[$statusId]['name'] ?? 'Pending';
                            $statusColor = $statusOptions[$statusId]['color'] ?? '#6c757d';
                        ?>
                        <tr>
                            <!-- Checkbox for each task -->
                            <td><input type="checkbox" class="form-check-input task-checkbox" value="<?= $taskId ?>"></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span><?= htmlspecialchars($row['project_name']) ?></span>
                                    <small class="text-muted"><?= htmlspecialchars($row['project_code']) ?></small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong><?= htmlspecialchars($row['task_name']) ?></strong>
                                    <?php if (!empty($row['task_description'])): ?>
                                    <!-- <small class="text-muted"><?= htmlspecialchars(substr($row['task_description'], 0, 50)) ?>...</small>
                                    <?php endif; ?> -->
                                </div>
                            </td>
                            
                            <td>
                                <?php if (!empty($row['client_name'])): ?>
                                <div class="d-flex align-items-center">
                                    <?php if ($clientImg): ?>
                                        <img src="<?= $clientImg ?>" 
                                             onerror="this.src='assets/img/users/user-16.jpg';"
                                             class="rounded-circle me-2" 
                                             style="width:32px; height:32px; object-fit:cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                             style="width:32px; height:32px; font-size:12px;">
                                            <?= $clientInitials ?>
                                        </div>
                                    <?php endif; ?>
                                    <span><?= htmlspecialchars($row['client_name']) ?></span>
                                </div>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            
                            <td><?= $start_date ?></td>
                            <td><?= $end_date ?></td>
                            <td><?= !empty($row['hour']) ? htmlspecialchars($row['hour']) . 'h' : '-' ?></td>
                            
                            <td>
                                <span class="badge" style="background-color: <?= $statusColor ?>; color: white;">
                                    <?= htmlspecialchars($statusName) ?>
                                </span>
                            </td>
                            
                            <td class="action-item">
                                <a href="javascript:void(0);" data-bs-toggle="dropdown" class="custom-elipse">
                                    <i class="isax isax-more"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="task-details.php?id=<?= $taskId ?>" class="dropdown-item">
                                            <i class="isax isax-eye"></i>&nbsp;&nbsp;&nbsp;View
                                        </a>
                                    </li>
                                    <li>
                                        <a href="edit-task.php?id=<?= $taskId ?>" class="dropdown-item">
                                            <i class="isax isax-edit me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#delete_modal<?= $taskId ?>">
                                            <i class="isax isax-trash me-2"></i>Delete
                                        </a>
                                    </li>
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
mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_assoc($result)):
?>
<div class="modal fade" id="delete_modal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-m">
        <div class="modal-content">
            <form method="POST" action="process/action_delete_task.php">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <img src="assets/img/icons/delete.svg" alt="img">
                    </div>
                    <h6 class="mb-1">Delete Task</h6>
                    <p class="mb-3">Are you sure you want to delete "<?= htmlspecialchars($row['task_name']) ?>"?</p>
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

<!-- Filter Canvas -->
<div class="offcanvas offcanvas-offset offcanvas-end" tabindex="-1" id="customcanvas">
    <div class="offcanvas-header d-block pb-0">
        <div class="border-bottom d-flex align-items-center justify-content-between pb-3">
            <h6 class="offcanvas-title">Filter Tasks</h6>
            <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-x"></i></button>
        </div>
    </div>
    <div class="offcanvas-body pt-3">
        <form action="project-tasks.php" method="POST">
            <!-- Projects -->
            <div class="mb-3">
                <label class="form-label">Projects</label>
                <?php
                $selectedProjectNames = [];
                if (!empty($selected_projects)) {
                    $ids = implode(",", array_map('intval', $selected_projects));
                    $res = mysqli_query($conn, "SELECT project_name FROM project WHERE id IN ($ids)");
                    while ($row = mysqli_fetch_assoc($res)) {
                        $selectedProjectNames[] = htmlspecialchars($row['project_name']);
                    }
                }
                $projectText = !empty($selectedProjectNames) ? 
                    (count($selectedProjectNames) > 3 ? 
                        implode(", ", array_slice($selectedProjectNames, 0, 3)) . " +" . (count($selectedProjectNames) - 3) : 
                        implode(", ", $selectedProjectNames)) : 
                    "Select";
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

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label">Status</label>
                <?php
                $selectedStatusNames = [];
                if (!empty($selected_statuses)) {
                    foreach ($selected_statuses as $statusId) {
                        if (isset($statusOptions[$statusId])) {
                            $selectedStatusNames[] = $statusOptions[$statusId]['name'];
                        }
                    }
                }
                $statusText = !empty($selectedStatusNames) ? 
                    (count($selectedStatusNames) > 3 ? 
                        implode(", ", array_slice($selectedStatusNames, 0, 3)) . " +" . (count($selectedStatusNames) - 3) : 
                        implode(", ", $selectedStatusNames)) : 
                    "Select";
                ?>
                <div class="dropdown">
                    <a class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border status-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <?= $statusText ?>
                    </a>
                    <div class="dropdown-menu shadow-lg w-100 dropdown-info p-3">
                        <div class="mb-3">
                            <div class="input-icon-start position-relative">
                                <span class="input-icon-addon fs-12">
                                    <i class="isax isax-search-normal"></i>
                                </span>
                                <input type="text" class="form-control form-control-sm search-status" placeholder="Search Status">
                            </div>
                        </div>
                        <ul class="mb-3 list-unstyled status-list">
                            <li class="d-flex align-items-center justify-content-between mb-2">
                                <label class="d-inline-flex align-items-center text-gray-9">
                                    <input class="form-check-input select-all m-0 me-2" type="checkbox" 
                                        <?= count($selected_statuses) > 0 ? 'checked' : '' ?>> Select All
                                </label>
                                <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline reset-status">Reset</a>
                            </li>
                            <?php foreach ($statusOptions as $statusId => $status): ?>
                            <li>
                                <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                    <input class="form-check-input m-0 me-2 status-checkbox" type="checkbox" name="status[]" value="<?= $statusId ?>" 
                                        <?= in_array($statusId, $selected_statuses) ? 'checked' : '' ?>>
                                    <span class="badge me-2" style="background-color: <?= $status['color'] ?>; width: 12px; height: 12px;"></span>
                                    <?= htmlspecialchars($status['name']) ?>
                                </label>
                            </li>
                            <?php endforeach; ?>
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
                    <div class="col-6"><a href="project-tasks.php" class="btn btn-outline-white w-100">Reset</a></div>
                    <div class="col-6"><button type="submit" class="btn btn-primary w-100" id="filter-submit">Apply</button></div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Multi Delete Modal - Added this modal -->
<div class="modal fade" id="multideleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-m">
        <div class="modal-content">
            <form method="POST" id="multiDeleteForm" action="process/action_multi_delete_task.php">
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <img src="assets/img/icons/delete.svg" alt="img">
                    </div>
                    <h6 class="mb-1">Delete Tasks</h6>
                    <p class="mb-3">Are you sure you want to delete the selected tasks?</p>
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
// Multiple Delete Functionality
const multiDeleteModal = new bootstrap.Modal(document.getElementById('multideleteModal'));
const deleteBtn = document.querySelector('.delete-multiple');
const checkboxes = document.querySelectorAll('.task-checkbox');
const selectAll = document.getElementById('select-all');

// Function to toggle delete button
function toggleDeleteBtn() {
    const checked = document.querySelectorAll('.task-checkbox:checked').length;
    if (checked > 0) {
        deleteBtn.classList.remove('d-none'); // Show button
    } else {
        deleteBtn.classList.add('d-none'); // Hide button
    }
}

// Delete multiple click
deleteBtn.addEventListener('click', function (e) {
    e.preventDefault();

    const checkedBoxes = document.querySelectorAll('.task-checkbox:checked');
    const form = document.getElementById('multiDeleteForm');
    
    // Remove any existing hidden inputs
    form.querySelectorAll('input[name="task_ids[]"]').forEach(el => el.remove());

    // Add hidden inputs for each selected task
    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'task_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    multiDeleteModal.show(); // Show confirmation modal
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

// Filter functionality
$(document).ready(function() {
    // Initialize date range picker
    if ($('.bookingrange').length > 0) {
        var start = moment().subtract(6, 'days');
        var end = moment();

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

    // Update dropdown labels
    function updateDropdownLabels() {
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

        // Status
        let statusLabels = [];
        $('.status-checkbox:checked').each(function() {
            statusLabels.push($(this).closest('label').text().trim());
        });
        if (statusLabels.length > 3) {
            $('.status-toggle').text(statusLabels.slice(0, 3).join(', ') + ' +' + (statusLabels.length - 3));
        } else {
            $('.status-toggle').text(statusLabels.length > 0 ? statusLabels.join(', ') : 'Select');
        }
    }

    // Initialize dropdown labels
    updateDropdownLabels();

    // Update dropdown labels when checkboxes change
    $('.project-checkbox, .status-checkbox').change(function() {
        updateDropdownLabels();
        
        // Update "Select All" checkbox for each section
        if ($(this).hasClass('project-checkbox')) {
            const allChecked = $('.project-checkbox:not(:checked)').length === 0;
            $('.project-list .select-all').prop('checked', allChecked);
        }
        
        if ($(this).hasClass('status-checkbox')) {
            const allChecked = $('.status-checkbox:not(:checked)').length === 0;
            $('.status-list .select-all').prop('checked', allChecked);
        }
    });

    // Select All functionality
    $('.project-list .select-all').change(function() {
        $('.project-checkbox').prop('checked', this.checked);
        updateDropdownLabels();
    });

    $('.status-list .select-all').change(function() {
        $('.status-checkbox').prop('checked', this.checked);
        updateDropdownLabels();
    });

    // Reset functionality
    $('.reset-project').click(function() {
        $('.project-checkbox, .project-list .select-all').prop('checked', false);
        updateDropdownLabels();
    });

    $('.reset-status').click(function() {
        $('.status-checkbox, .status-list .select-all').prop('checked', false);
        updateDropdownLabels();
    });

    // Search functionality
    $(".search-project").on("keyup", function() {
        const value = $(this).val().toLowerCase();
        $(".project-list li").each(function() {
            if ($(this).find('.select-all').length > 0) return;
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(value) > -1);
        });
    });

    $(".search-status").on("keyup", function() {
        const value = $(this).val().toLowerCase();
        $(".status-list li").each(function() {
            if ($(this).find('.select-all').length > 0) return;
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(value) > -1);
        });
    });
});
</script>
</body>
</html>