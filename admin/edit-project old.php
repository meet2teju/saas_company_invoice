<?php
include 'layouts/session.php';
include '../config/config.php';

// $project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$project = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM project WHERE id = $project_id"));
$statuses = mysqli_query($conn, "SELECT id, status_name FROM project_status WHERE is_deleted = 0 ORDER BY id ASC");

$users = [];
// $user_result = mysqli_query($conn, "SELECT id, name, email FROM login WHERE role_id = (SELECT id FROM user_role WHERE name = 'user') AND is_deleted = 0 ORDER BY name ASC");
$user_result = mysqli_query($conn, "
    SELECT l.id, l.name, l.email
    FROM login l
    INNER JOIN user_role ur ON l.role_id = ur.id
    WHERE ur.is_deleted = 0 
      AND l.is_deleted = 0
    ORDER BY l.name ASC
");

while ($row = mysqli_fetch_assoc($user_result)) {
    $users[] = $row;
}

$project_users = [];
$project_user_result = mysqli_query($conn, "SELECT user_id FROM project_users WHERE project_id = $project_id");
while ($row = mysqli_fetch_assoc($project_user_result)) {
    $project_users[] = $row['user_id'];
}

$project_tasks = [];
$task_result = mysqli_query($conn, "SELECT * FROM project_task WHERE project_id = $project_id");
while ($row = mysqli_fetch_assoc($task_result)) {
    $project_tasks[] = $row;
}

$clients = [];
$client_result = mysqli_query($conn, "SELECT id, first_name FROM client WHERE is_deleted = 0 ORDER BY first_name ASC");
while ($row = mysqli_fetch_assoc($client_result)) {
    $clients[] = $row;
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

        <!-- ========================
            Start Page Content
        ========================= -->
        <div class="page-wrapper">
            <div class="content content-two">
                <!-- Page Header -->
              <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6><a href="projects.php"><i class="isax isax-arrow-left me-2"></i> Projects</a></h6>
                            <a href="#" class="btn btn-outline-white d-inline-flex align-items-center"><i class="isax isax-eye me-1"></i>Preview</a>
                </div>
                <!-- End Page Header -->

                <!-- Project Form -->
                <form action="process/action_edit_project.php" method="POST" id="form">
                    <input type="hidden" name="project_id" value="<?= $project_id ?>">
                    <div class="card">
                        <div class="card-body">
                            <!-- Project Details Section -->
                            <div class="mb-4">
                                <h5 class="mb-3">Project Details</h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Project Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="project_name" id="project_name" value="<?= htmlspecialchars($project['project_name'] ?? '') ?>">
                                        <span class="text-danger error-text" id="name_error"></span>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Project Code</label>
                                        <input type="text" class="form-control" name="project_code" value="<?php echo $project['project_code'] ?? ''; ?>">
                                    </div>
                                    
                                   <div class="col-md-6 mb-3">
                                    <label class="form-label">Client Name <span class="text-danger">*</span></label>
                                        <select class="form-select select2" name="client_id[]" multiple="multiple" id="client_id">
                                            <?php foreach ($clients as $client): ?>
                                            <option value="<?= $client['id'] ?>" <?= in_array($client['id'], explode(',', $project['client_id'])) ? 'selected' : '' ?>><?=$client['first_name']?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <span class="text-danger error-text" id="clientname_error"></span>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Billing Method <span class="text-danger">*</span></label>
                                        <select class="form-select" name="billing_method" id="billingMethod" onchange="showBillingFields()">
                                            <option value="">Select billing method</option>
                                            <option value="1" <?= ($project['billing_method'] ?? '') == 1 ? 'selected' : '' ?>>Fixed Price</option>
                                            <option value="2" <?= ($project['billing_method'] ?? '') == 2 ? 'selected' : '' ?>>Project Hourly</option>
                                            <option value="3" <?= ($project['billing_method'] ?? '') == 3 ? 'selected' : '' ?>>Task Hourly</option>
                                            <option value="4" <?= ($project['billing_method'] ?? '') == 4 ? 'selected' : '' ?>>Staff Hourly</option>
                                        </select>
                                    <span class="text-danger error-text" id="method_error"></span>
                                    </div>

                                    <!-- Dynamic Fields Container -->
                                    <div id="billingFieldsContainer">
                                        <!-- Fields will be inserted here based on selection -->
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3" ><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Users Section -->
                         <div class="mb-4">
                                <h5 class="mb-3">Users</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="contactTable">
                                        <thead>
                                            <tr>
                                                <th>S.no</th>
                                                <th>User</th>
                                                <th>Email</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                         <tbody id="projectuserTableBody">
                                        <?php foreach ($project_users as $index => $uid): ?>
                                            <?php $user = array_filter($users, fn($u) => $u['id'] == $uid); $user = reset($user); ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td>
                                                    <select name="user_id[]" class="form-select user-select" onchange="updateEmail(this)" required>
                                                        <option value="">Select User</option>
                                                        <?php foreach ($users as $opt): ?>
                                                            <option value="<?= $opt['id'] ?>" data-email="<?= $opt['email'] ?>" <?= $opt['id'] == $uid ? 'selected' : '' ?>><?= htmlspecialchars($opt['name']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td><input type="email" name="email[]" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly></td>
                                                <td><button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(this)"><i class="fas fa-trash"></i></button></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    </table>
                                </div>

                                <div class="text-start mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addContactRow()">
                                        <i class="fas fa-plus me-1"></i> Add More
                                    </button>
                                </div>
                            </div>

                            <hr class="my-4">
                            
                            <!-- Project Tasks Section -->
                            <div class="mb-4">
                                <h5 class="mb-3">Project Tasks</h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tasksTable">
                                        <thead>
                                            <tr>
                                                <th>S.no</th>
                                                <th>Task name</th>
                                                <th>Description</th>
                                                <th>Start date</th>
                                                <th>End date</th>
                                                <th>Hour</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tasksTableBody">
                                        <?php foreach ($project_tasks as $index => $task): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><input type="text" name="task_name[]" class="form-control" value="<?= htmlspecialchars($task['task_name']) ?>" required></td>
                                                <td>
                                                    <textarea name="task_description[]" class="form-control" rows="2" ><?= htmlspecialchars($task['task_description']) ?></textarea>
                                                </td>
                                                 <td><input type="text" name="start_date[]"  class="form-control task-start-date" readonly  value="<?= htmlspecialchars($task['start_date']) ?>"></td>
                                                  <td><input type="text" name="end_date[]" class="form-control task-end-date" readonly value="<?= htmlspecialchars($task['end_date']) ?>"></td>
                                                <td>
                                            <input type="number" name="hour[]" class="form-control" 
                                                value="<?= htmlspecialchars($task['hour']) ?>" step="0.01" min="0">
                                        </td>
                                        
                                        <!-- Status -->
                                        <td>
                                            <select name="status_id[]" class="form-control select2">
                                                <option value="">Select Status</option>
                                                <?php foreach ($statuses as $status): ?>
                                                    <option value="<?= $status['id'] ?>" <?= $status['id'] == $task['status_id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($status['status_name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><button type="button" class="btn btn-sm btn-danger" onclick="deleteTaskRow(this)"><i class="fas fa-trash"></i></button></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    </table>
                                </div>
                                
                                 <div class="text-start mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addContactBtn" onclick="addTaskRow()">
                                        <i class="fas fa-plus me-1"></i> Add Project Task
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='projects.php'">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
                     <?php include 'layouts/footer.php'; ?>
            </div>
        </div>
        <!-- End Content -->

    </div>
    <!-- ========================
        End Page Content
    ========================= -->

    <?php include 'layouts/vendor-scripts.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
        <script>
    $(document).ready(function () {
    

    // === Allow only text (no digits) ===
    $('#project_name').on('input', function () {
        this.value = this.value.replace(/[0-9]/g, '');
    });

  
});

</script>
    <script>

const users = <?= json_encode($users) ?>;

function addContactRow() {
    const tableBody = document.getElementById("projectuserTableBody");
    const rowCount = tableBody.rows.length;
    const newRow = document.createElement("tr");

    let options = '<option value="">Select User</option>';
    users.forEach(user => {
        options += `<option value="${user.id}" data-email="${user.email}">${user.name}</option>`;
    });

    newRow.innerHTML = `
        <td>${rowCount + 1}</td>
        <td>
            <select name="user_id[]" class="form-select user-select" onchange="updateEmail(this); filterUserOptions();">
                ${options}
            </select>
        </td>
        <td><input type="email" name="email[]" class="form-control" placeholder="Email" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(this)">
            <i class="fas fa-trash"></i></button></td>
    `;

    tableBody.appendChild(newRow);
    updateSerialNumbers();
    filterUserOptions(); // apply filtering immediately
}

function deleteRow(btn) {
    const row = btn.closest("tr");
    row.remove();
    updateSerialNumbers();
    filterUserOptions(); // re-filter after delete
}

function updateSerialNumbers() {
    const rows = document.querySelectorAll("#projectuserTableBody tr");
    rows.forEach((row, index) => {
        row.cells[0].innerText = index + 1;
    });
}

function updateEmail(selectElem) {
    const emailInput = selectElem.closest("tr").querySelector('input[name="email[]"]');
    const selectedOption = selectElem.options[selectElem.selectedIndex];
    const email = selectedOption.getAttribute("data-email") || "";
    emailInput.value = email;
}

function filterUserOptions() {
    // collect selected user_ids
    const selectedIds = Array.from(document.querySelectorAll('select[name="user_id[]"]'))
        .map(sel => sel.value)
        .filter(v => v !== "");

    // loop through all dropdowns
    document.querySelectorAll('select[name="user_id[]"]').forEach(select => {
        const currentValue = select.value;
        Array.from(select.options).forEach(option => {
            if (option.value === "" || option.value === currentValue) {
                option.hidden = false; // always show placeholder & current selection
            } else {
                option.hidden = selectedIds.includes(option.value);
            }
        });
    });
}

    function updateEmail(selectElem) {
        const emailInput = selectElem.closest("tr").querySelector('input[name="email[]"]');
        const selectedOption = selectElem.options[selectElem.selectedIndex];
        const email = selectedOption.getAttribute("data-email") || "";
        emailInput.value = email;
    }
function getTodayDate() {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
}

// Initialize datepicker for all rows
function initDatePickers() {
    $('.task-start-date, .task-end-date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        startDate: getTodayDate() // Disable past dates
    });
}
// Call on page load
initDatePickers();
//     // Function to add a new task row
// function addTaskRow() {
//     const tableBody = document.getElementById("tasksTableBody");
//     const rowCount = tableBody.rows.length;
//     const newRow = document.createElement("tr");

//     newRow.innerHTML = `
//         <td>${rowCount + 1}</td>
//         <td><input type="text" name="task_name[]" class="form-control" placeholder="Task Name"></td>
//         <td><input type="text" name="task_description[]" class="form-control" placeholder="Description"></td>
//         <td><input type="text" name="start_date[]" class="form-control task-start-date" placeholder="Start Date"></td>
//         <td><input type="text" name="end_date[]" class="form-control task-end-date" placeholder="End Date"></td>
//         <td><button type="button" class="btn btn-sm btn-danger" onclick="deleteTaskRow(this)">
//             <i class="fas fa-trash"></i></button>
//         </td>
//     `;

//     tableBody.appendChild(newRow);
//     updateTaskSerialNumbers();

//     // ✅ Initialize datepicker for the new inputs
//     $(newRow).find('.task-start-date, .task-end-date').datepicker({
//         format: 'yyyy-mm-dd',
//         autoclose: true,
//         todayHighlight: true,
//         startDate: getTodayDate() // disable past dates
//     });
// }

    // Pre-generate status options HTML from PHP
const statusOptions = `<?php
    $optionsHtml = '<option value="">Select Status</option>';
    foreach ($statuses as $status) {
        $optionsHtml .= '<option value="'.$status['id'].'">'.htmlspecialchars($status['status_name']).'</option>';
    }
    echo $optionsHtml;
?>`;

function addTaskRow() {
    const tableBody = document.getElementById("tasksTableBody");
    const rowCount = tableBody.rows.length;
    const newRow = document.createElement("tr");

    newRow.innerHTML = `
        <td>${rowCount + 1}</td>
        <td><input type="text" name="task_name[]" class="form-control" placeholder="Task Name" required></td>
        <td>
            <textarea name="task_description[]" class="form-control" placeholder="Description" rows="2"></textarea>
        </td>
        <td><input type="text" name="start_date[]" class="form-control task-start-date" placeholder="Start Date" readonly></td>
        <td><input type="text" name="end_date[]" class="form-control task-end-date" placeholder="End Date" readonly></td>
        <td><input type="number" name="hour[]" class="form-control" placeholder="Hour" step="0.01" min="0"></td>
        <td>
            <select name="status[]" class="form-control select2">
                ${statusOptions}
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="deleteTaskRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;

    tableBody.appendChild(newRow);
    updateTaskSerialNumbers();

    // Initialize datepicker
    $(newRow).find('.task-start-date, .task-end-date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        startDate: getTodayDate()
    });

    // Initialize Select2
    $(newRow).find('.select2').select2({ width: '100%' });
}

// Function to delete a task row
    function deleteTaskRow(btn) {
        const row = btn.closest("tr");
        const tableBody = document.getElementById("tasksTableBody");
        tableBody.removeChild(row);
        updateTaskSerialNumbers();
    }

    // Function to update serial numbers for task rows
    function updateTaskSerialNumbers() {
        const rows = document.querySelectorAll("#tasksTableBody tr");
        rows.forEach((row, index) => {
            row.cells[0].innerText = index + 1;
        });
    }

    function showBillingFields() {
        const billingMethod = document.getElementById('billingMethod').value;
        const container = document.getElementById('billingFieldsContainer');
        container.innerHTML = ''; // Clear previous fields

        if (billingMethod === '1') {
            container.innerHTML = `
            <div class="col-md-6 mb-3">
                <label class="form-label">Total Project Cost <span class="text-danger">*</span></label>
                <div class="input-group">
                    <select class="form-select" name="currency_type" style="max-width: 100px;">
                        <option value="1" ${<?= ($project['currency_type'] ?? '') == '1' ? 'true' : 'false' ?> ? 'selected' : ''}>INR</option>
                        <option value="0" ${<?= ($project['currency_type'] ?? '') == '0' ? 'true' : 'false' ?> ? 'selected' : ''}>$</option>
                    </select>
                    <input type="number" class="form-control" name="total_project_cost" value="<?php echo $project['total_project_cost'] ?? ''; ?>" step="0.01" min="0">
                </div>
                <span class="text-danger error-text" id="fixed_error"></span>
            </div>
            `;

        } 
        else if (billingMethod === '2') {
           container.innerHTML = `
            <div class="col-md-6 mb-3">
                <label class="form-label">Rate Per Hour <span class="text-danger">*</span></label>
                <div class="input-group">
                    <select class="form-select" name="currency_type" style="max-width: 100px;">
                        <option value="1" ${<?= ($project['currency_type'] ?? '') == '1' ? 'true' : 'false' ?> ? 'selected' : ''}>INR</option>
                        <option value="0" ${<?= ($project['currency_type'] ?? '') == '0' ? 'true' : 'false' ?> ? 'selected' : ''}>$</option>
                    </select>
                    <input type="number" class="form-control" name="rate_per_hour" value="<?php echo $project['rate_per_hour'] ?? ''; ?>" step="0.01" min="0">
                </div>
                <span class="text-danger error-text" id="hour_error"></span>
            </div>
            `;
        }
        else if (billingMethod === '3') {
            container.innerHTML = `
                <div class="col-12 mb-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Billing is calculated based on hourly rate of project tasks.
                    </div>
                </div>
            `;
        }
        else if (billingMethod === '4') {
            container.innerHTML = `
                <div class="col-12 mb-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Billing is calculated based on hourly rate of staff.
                    </div>
                </div>
            `;
        }
    }

    // Initialize fields on page load if a method is already selected
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('billingMethod').value) {
            showBillingFields();
        }
        
        // Initialize select2
        $('.select2').select2();
    });

    // Form validation
    $(document).ready(function () {
        $('#form').on('submit', function (e) {
            let valid = true;
            $('.error-text').text('');

            // Project name validation
            if (!$('#project_name').val().trim()) {
                $('#name_error').text('Project name is required.');
                valid = false;
            }

            // Client validation
            if ($('#client_id').val() === null || $('#client_id').val().length === 0) {
                $('#clientname_error').text('Please select at least one client.');
                valid = false;
            }

            // Billing method validation
            if (!$('#billingMethod').val()) {
                $('#method_error').text('Please select a billing method.');
                valid = false;
            }

            // Additional validation based on billing method
            const billingMethod = $('#billingMethod').val();
            if (billingMethod === '1') {
                if (!$('input[name="total_project_cost"]').val()) {
                    $('#fixed_error').text('Total project cost is required.');
                    valid = false;
                }
            } else if (billingMethod === '2') {
                if (!$('input[name="rate_per_hour"]').val()) {
                    $('#hour_error').text('Rate per hour is required.');
                    valid = false;
                }
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    });
    </script>
    
</body>
</html>