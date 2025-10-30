<?php
include 'layouts/session.php';
include '../config/config.php';

$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch project
$project = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM project WHERE id = $project_id"));
$statuses = mysqli_query($conn, "SELECT id, status_name FROM project_status WHERE is_deleted = 0 ORDER BY id ASC");

// Fetch clients
$client_names = [];
$client_ids = explode(',', $project['client_id']);
if (!empty($client_ids)) {
    $client_query = mysqli_query($conn, "SELECT first_name FROM client WHERE id IN (" . implode(',', $client_ids) . ")");
    while ($client = mysqli_fetch_assoc($client_query)) {
        $client_names[] = $client['first_name'];
    }
}

// Fetch users
$users = [];
$user_query = mysqli_query($conn, "
    SELECT u.name, u.email 
    FROM project_users pu 
    JOIN login u ON pu.user_id = u.id 
    WHERE pu.project_id = $project_id
");
while ($row = mysqli_fetch_assoc($user_query)) {
    $users[] = $row;
}

// Fetch tasks
$tasks = [];
$task_query = mysqli_query($conn, "SELECT * FROM project_task WHERE project_id = $project_id");
while ($row = mysqli_fetch_assoc($task_query)) {
    $tasks[] = $row;
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
        <div class="content content-two">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Project Details</h5>
                <a href="edit-project.php?id=<?= $project_id ?>" class="btn btn-sm btn-primary">Edit Project</a>
            </div>

            <!-- Project Info Card -->
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Project Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-2"><strong>Project Name:</strong> <?= htmlspecialchars($project['project_name']) ?></div>
                        <div class="col-md-6 mb-2"><strong>Project Code:</strong> <?= htmlspecialchars($project['project_code']) ?></div>
                        <div class="col-md-6 mb-2"><strong>Clients:</strong> <?= htmlspecialchars(implode(', ', $client_names)) ?></div>
                        <div class="col-md-6 mb-2"><strong>Billing Method:</strong> 
                            <?php
                                $billing_types = [1 => 'Fixed Price', 2 => 'Project Hourly', 3 => 'Task Hourly', 4 => 'Staff Hourly'];
                                echo $billing_types[$project['billing_method']] ?? 'N/A';
                            ?>
                        </div>
                        <?php if ($project['billing_method'] == 1): ?>
                            <div class="col-md-6 mb-2"><strong>Total Project Cost:</strong> <?= ($project['currency_type'] == 1 ? 'INR' : '$') . ' ' . $project['total_project_cost'] ?></div>
                        <?php elseif ($project['billing_method'] == 2): ?>
                            <div class="col-md-6 mb-2"><strong>Rate Per Hour:</strong> <?= ($project['currency_type'] == 1 ? 'INR' : '$') . ' ' . $project['rate_per_hour'] ?></div>
                        <?php endif; ?>
                        <div class="col-12 mt-3"><strong>Description:</strong> <br><?= nl2br(htmlspecialchars($project['description'])) ?></div>
                    </div>
                </div>
            </div>

            <!-- Assigned Users Card -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="mb-3">Assigned Users</h6>
                    <?php if (!empty($users)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead><tr><th>Name</th><th>Email</th></tr></thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($user['name']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No users assigned.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Project Tasks Card -->
              <div class="card mt-4">
                <div class="card-body">
                    <h5 class="mb-3">Project Tasks</h5>
                    <form method="post" action="process/action_save_project_tasks.php">
                        <input type="hidden" name="project_id" value="<?= $project_id ?>">

                        <div class="table-responsive">
                            <table class="table table-hover" id="tasksTable">
                                <thead>
                                    <tr>
                                        <th>S.no</th>
                                        <th>Task name</th>
                                        <th>Description</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Hour</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tasksTableBody">
                                    <?php if (!empty($tasks)): ?>
                                        <?php foreach ($tasks as $i => $task): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td>
                                                    <input type="hidden" name="task_id[]" value="<?= $task['id'] ?>">
                                                    <input type="text" name="task_name[]" class="form-control" value="<?= htmlspecialchars($task['task_name']) ?>" placeholder="Task Name">
                                                </td>
                                                <td>
                                                    <textarea name="task_description[]" class="form-control" rows="2" ><?= htmlspecialchars($task['task_description']) ?></textarea>
                                                </td>
                                                <td>
                                                    <input type="text" name="start_date[]" class="form-control task-start-date datepicker" value="<?= htmlspecialchars($task['start_date']) ?>" placeholder="Start Date" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="end_date[]" class="form-control task-end-date datepicker" value="<?= htmlspecialchars($task['end_date']) ?>" placeholder="End Date" readonly>
                                                </td>
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
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteTaskRow(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td>1</td>
                                            <td><input type="text" name="task_name[]" class="form-control" placeholder="Task Name"></td>
                                            <td><input type="text" name="task_description[]" class="form-control" placeholder="Description"></td>
                                            <td><input type="text" name="start_date[]" class="form-control task-start-date datepicker" placeholder="Start Date" readonly></td>
                                            <td><input type="text" name="end_date[]" class="form-control task-end-date datepicker" placeholder="End Date" readonly></td>
                                            <td><input type="number" name="hour[]" class="form-control" placeholder="Hour" step="0.01" min="0"></td>
                                            <td>
                                                <select name="status_id[]" class="form-control select2">
                                                    <option value="">Select Status</option>
                                                    <?php foreach ($statuses as $status): ?>
                                                        <option value="<?= $status['id'] ?>"><?= htmlspecialchars($status['status_name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteTaskRow(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-start mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTaskRow()">
                                <i class="fas fa-plus me-1"></i> Add Project Task
                            </button>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">Save Tasks</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <?php include 'layouts/footer.php'; ?>
</div>

<?php include 'layouts/vendor-scripts.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// Initialize datepicker
$(document).ready(function() {
    // Initialize Flatpickr for all datepicker fields
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

function getTodayDate() {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
}

function initDatePickers() {
    $('.task-start-date, .task-end-date').flatpickr({
       dateFormat: "Y-m-d",
                allowInput: true,
                defaultDate: new Date(),
                clickOpens: true
    });
}

// Call on page load
initDatePickers();

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
        <td><input type="text" name="start_date[]" class="form-control task-start-date datepicker" placeholder="Start Date" readonly></td>
        <td><input type="text" name="end_date[]" class="form-control task-end-date datepicker" placeholder="End Date" readonly></td>
        <td><input type="number" name="hour[]" class="form-control" placeholder="Hour" step="0.01" min="0"></td>
        <td>
            <select name="status_id[]" class="form-control select2">
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

    // Initialize Flatpickr for the new date fields
    $(newRow).find('.datepicker').flatpickr({
       dateFormat: "Y-m-d",
                allowInput: true,
                defaultDate: new Date(),
                clickOpens: true
    });

    // Initialize Select2
    $(newRow).find('.select2').select2({ width: '100%' });
}

function deleteTaskRow(btn) {
    const row = btn.closest("tr");
    row.remove();
    updateTaskSerialNumbers();
}

function updateTaskSerialNumbers() {
    const rows = document.querySelectorAll("#tasksTableBody tr");
    rows.forEach((row, index) => {
        row.cells[0].innerText = index + 1;
    });
}
</script>
</body>
</html>