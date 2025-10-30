<?php include 'layouts/session.php'; ?>
<?php

include '../config/config.php'; // Adjust the path if needed

// Get user role ID and user ID from session
$currentUserId = $_SESSION['crm_user_id'] ?? 0;
$userRoleId = $_SESSION['role_id'] ?? 0;

// Initialize filter variables
$selected_customers = [];
$selected_statuses = [];
$selected_amounts = [];
$date_range = '';
$start_date = '';
$end_date = '';

// Build the filter SQL - MODIFIED THIS PART FOR ROLE-BASED FILTERING
$filterSql = "WHERE i.is_deleted = 0";

// Add user-specific filtering
if ($userRoleId != 1) {
    // For non-admin users (role_id != 1), show only their own invoices
    $filterSql .= " AND i.user_id = $currentUserId";
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Date filter
    if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
        $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
        $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
        $filterSql .= " AND DATE(i.invoice_date) BETWEEN '$start_date' AND '$end_date'";
        $date_range = $start_date . ' - ' . $end_date;
    }

    // Client filter
    if (!empty($_POST['customer']) && is_array($_POST['customer'])) {
        $selected_customers = $_POST['customer'];
        $customer_ids = array_map('intval', $selected_customers);
        $customer_ids = implode(',', $customer_ids);
        $filterSql .= " AND i.client_id IN ($customer_ids)";
    }

    // Status filter
    if (!empty($_POST['status']) && is_array($_POST['status'])) {
        $selected_statuses = $_POST['status'];
        $statuses = array_map(function ($status) use ($conn) {
            return "'" . mysqli_real_escape_string($conn, $status) . "'";
        }, $selected_statuses);
        $statuses = implode(',', $statuses);
        $filterSql .= " AND i.status IN ($statuses)";
    }

    // Amount filter
    if (!empty($_POST['amount']) && is_array($_POST['amount'])) {
        $selected_amounts = $_POST['amount'];
        $amount_conditions = [];

        foreach ($selected_amounts as $range) {
            $range = mysqli_real_escape_string($conn, $range);

            if (strpos($range, '+') !== false) {
                $min = (int) filter_var($range, FILTER_SANITIZE_NUMBER_INT);
                $amount_conditions[] = "i.total_amount >= $min";
            } elseif (strpos($range, '-') !== false) {
                list($min, $max) = explode('-', $range);
                $min = (int) trim($min);
                $max = (int) trim($max);
                $amount_conditions[] = "i.total_amount BETWEEN $min AND $max";
            }
        }

        if (!empty($amount_conditions)) {
            $filterSql .= " AND (" . implode(" OR ", $amount_conditions) . ")";
        }
    }
}

// Get invoice data with filters applied - MODIFIED QUERY TO INCLUDE ROLE-BASED FILTERING
$sql = "SELECT i.id, i.invoice_id, i.total_amount,i.invoice_date, i.due_date, i.invoice_date, i.status, c.first_name, c.customer_image 
        FROM invoice i
        LEFT JOIN client c ON i.client_id = c.id
        $filterSql
        ORDER BY i.id DESC";

$result = mysqli_query($conn, $sql);

// MODIFIED TOTAL INVOICE QUERIES WITH ROLE-BASED FILTERING
$totalinvoiceWhere = "WHERE is_deleted = 0";
if ($userRoleId != 1) {
    $totalinvoiceWhere .= " AND user_id = $currentUserId";
}

$totalinvoice = "SELECT SUM(total_amount) AS total_invoices FROM invoice $totalinvoiceWhere";
$totalinvoiceresult = mysqli_query($conn, $totalinvoice);

// Fetch the result
$total_invoices = 0;
if ($totalinvoicerow = mysqli_fetch_assoc($totalinvoiceresult)) {
    $total_invoices = $totalinvoicerow['total_invoices'];
}

// Query to get the total sum of all paid invoices - MODIFIED WITH ROLE-BASED FILTERING
$sql_paid_where = "WHERE status = 'Paid' AND is_deleted = 0";
if ($userRoleId != 1) {
    $sql_paid_where .= " AND user_id = $currentUserId";
}

$sql_paid = "SELECT SUM(total_amount) AS total_paid_invoices FROM invoice $sql_paid_where";
$result_paid = mysqli_query($conn, $sql_paid);
$total_paid_invoices = 0;
if ($row = mysqli_fetch_assoc($result_paid)) {
    $total_paid_invoices = $row['total_paid_invoices'];
}

// Query to get the total sum of all pending invoices - MODIFIED WITH ROLE-BASED FILTERING
$sql_pending_where = "WHERE status = 'unpaid' AND is_deleted = 0";
if ($userRoleId != 1) {
    $sql_pending_where .= " AND user_id = $currentUserId";
}

$sql_pending = "SELECT SUM(total_amount) AS total_pending_invoices FROM invoice $sql_pending_where";
$result_pending = mysqli_query($conn, $sql_pending);
$total_pending_invoices = 0;
if ($row = mysqli_fetch_assoc($result_pending)) {
    $total_pending_invoices = $row['total_pending_invoices'];
}

// Query to get the total sum of all overdue invoices - MODIFIED WITH ROLE-BASED FILTERING
$sql_overdue_where = "WHERE status = 'Overdue' AND is_deleted = 0";
if ($userRoleId != 1) {
    $sql_overdue_where .= " AND user_id = $currentUserId";
}

$sql_overdue = "SELECT SUM(total_amount) AS total_overdue_invoices FROM invoice $sql_overdue_where";
$result_overdue = mysqli_query($conn, $sql_overdue);
$total_overdue_invoices = 0;
if ($row = mysqli_fetch_assoc($result_overdue)) {
    $total_overdue_invoices = $row['total_overdue_invoices'];
}

function getInvoiceTotal($conn, $status = null, $month = null, $year = null)
{
    global $userRoleId, $currentUserId;
    
    $query = "SELECT SUM(total_amount) AS total FROM invoice WHERE is_deleted = 0";
    
    // Add role-based filtering
    if ($userRoleId != 1) {
        $query .= " AND user_id = $currentUserId";
    }

    if ($status !== null) {
        $query .= " AND status = '$status'";
    }

    if ($month !== null && $year !== null) {
        $query .= " AND MONTH(invoice_date) = $month AND YEAR(invoice_date) = $year";
    }

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

// Helper: Calculate % growth from last month
function growthPercent($current, $last)
{
    if ($last == 0)
        return $current > 0 ? 100 : 0;
    return round((($current - $last) / $last) * 100, 2);
}

$currentMonth = date('m');
$currentYear = date('Y');
$lastMonth = date('m', strtotime('-1 month'));
$lastYear = date('Y', strtotime('-1 month'));

// Get totals for this month
$total_invoices = getInvoiceTotal($conn, null, $currentMonth, $currentYear);
$total_paid_invoices = getInvoiceTotal($conn, 'Paid', $currentMonth, $currentYear);
$total_pending_invoices = getInvoiceTotal($conn, 'unpaid', $currentMonth, $currentYear);
$total_overdue_invoices = getInvoiceTotal($conn, 'Overdue', $currentMonth, $currentYear);

// Get totals for last month
$last_total_invoices = getInvoiceTotal($conn, null, $lastMonth, $lastYear);
$last_paid_invoices = getInvoiceTotal($conn, 'Paid', $lastMonth, $lastYear);
$last_pending_invoices = getInvoiceTotal($conn, 'unpaid', $lastMonth, $lastYear);
$last_overdue_invoices = getInvoiceTotal($conn, 'Overdue', $lastMonth, $lastYear);

// Get % growth
$growth_total = growthPercent($total_invoices, $last_total_invoices);
$growth_paid = growthPercent($total_paid_invoices, $last_paid_invoices);
$growth_pending = growthPercent($total_pending_invoices, $last_pending_invoices);
$growth_overdue = growthPercent($total_overdue_invoices, $last_overdue_invoices);
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
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>

            <!-- Start Content -->
            <div class="content content-two">

                <!-- Start Breadcrumb -->
                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <h6>Invoices</h6>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">

                        <div class="dropdown">
                            <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center"
                                data-bs-toggle="dropdown">
                                <i class="isax isax-export-1 me-1"></i>Export
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);">Download as PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);">Download as Excel</a>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <a href="add-invoice.php" class="btn btn-primary d-flex align-items-center">
                                <i class="isax isax-add-circle5 me-1"></i>New Invoice
                            </a>
                        </div>
                    </div>
                </div>
                <!-- End Breadcrumb -->
                <div class="row">
                    <!-- Total Invoices -->
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                    <div>
                                        <p class="mb-1">Total Invoices</p>
                                        <h6 class="fs-16 fw-semibold">$<?= number_format($total_invoices, 2) ?></h6>
                                    </div>
                                    <div>
                                        <span class="avatar bg-primary rounded-circle">
                                            <i class="isax isax-receipt-item"></i>
                                        </span>
                                    </div>
                                </div>
                                <p class="fs-13 mb-0">
                                    <span class="<?= $growth_total >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <i
                                            class="isax <?= $growth_total >= 0 ? 'isax-send text-success' : 'isax-received text-danger' ?> me-1"></i>
                                        <?= abs($growth_total) ?>%
                                    </span> from last month
                                </p>
                                <span class="position-absolute end-0 bottom-0">
                                    <img src="assets/img/bg/card-overlay-01.svg" alt="User Img">
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Paid Invoices -->
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                    <div>
                                        <p class="mb-1">Paid Invoices</p>
                                        <h6 class="fs-16 fw-semibold">$<?= number_format($total_paid_invoices, 2) ?>
                                        </h6>
                                    </div>
                                    <div>
                                        <span class="avatar bg-success rounded-circle">
                                            <i class="isax isax-tick-circle"></i>
                                        </span>
                                    </div>
                                </div>
                                <p class="fs-13 mb-0">
                                    <span class="<?= $growth_paid >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <i
                                            class="isax <?= $growth_paid >= 0 ? 'isax-send text-success' : 'isax-received text-danger' ?> me-1"></i>
                                        <?= abs($growth_paid) ?>%
                                    </span> from last month
                                </p>
                                <span class="position-absolute end-0 bottom-0">
                                    <img src="assets/img/bg/card-overlay-02.svg" alt="User Img">
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Invoices -->
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                    <div>
                                        <p class="mb-1">Pending Invoices</p>
                                        <h6 class="fs-16 fw-semibold">$<?= number_format($total_pending_invoices, 2) ?>
                                        </h6>
                                    </div>
                                    <div>
                                        <span class="avatar bg-warning rounded-circle">
                                            <i class="isax isax-timer"></i>
                                        </span>
                                    </div>
                                </div>
                                <p class="fs-13 mb-0">
                                    <span class="<?= $growth_pending >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <i
                                            class="isax <?= $growth_pending >= 0 ? 'isax-send text-success' : 'isax-received text-danger' ?> me-1"></i>
                                        <?= abs($growth_pending) ?>%
                                    </span> from last month
                                </p>
                                <span class="position-absolute end-0 bottom-0">
                                    <img src="assets/img/bg/card-overlay-03.svg" alt="User Img">
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Overdue Invoices -->
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                    <div>
                                        <p class="mb-1">Overdue Invoices</p>
                                        <h6 class="fs-16 fw-semibold">$<?= number_format($total_overdue_invoices, 2) ?>
                                        </h6>
                                    </div>
                                    <div>
                                        <span class="avatar bg-danger rounded-circle">
                                            <i class="isax isax-information"></i>
                                        </span>
                                    </div>
                                </div>
                                <p class="fs-13 mb-0">
                                    <span class="<?= $growth_overdue >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <i
                                            class="isax <?= $growth_overdue >= 0 ? 'isax-send text-success' : 'isax-received text-danger' ?> me-1"></i>
                                        <?= abs($growth_overdue) ?>%
                                    </span> from last month
                                </p>
                                <span class="position-absolute end-0 bottom-0">
                                    <img src="assets/img/bg/card-overlay-04.svg" alt="User Img">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>


                <ul class="nav nav-tabs nav-bordered mb-3">
                    <li class="nav-item"><a class="nav-link active" href="javascript:void(0);" data-bs-toggle="tab"
                            data-bs-target="#tab1">All</a></li>
                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab"
                            data-bs-target="#tab2">Paid</a></li>

                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab"
                            data-bs-target="#tab5">Cancelled</a></li>

                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab"
                            data-bs-target="#tab7">Unpaid</a></li>

                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab"
                            data-bs-target="#tab9">Draft</a></li>
                </ul>

                <!-- Table Search Start -->
              <!-- Search & Actions -->
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
            
            // Status filters
            if (!empty($selected_statuses)) {
                $status_names = array_map('ucfirst', $selected_statuses);
                $active_filters[] = "Status: " . (count($status_names) > 2 ? 
                    implode(", ", array_slice($status_names, 0, 2)) . " +" . (count($status_names) - 2) : 
                    implode(", ", $status_names));
            }
            
            // Amount filters
            if (!empty($selected_amounts)) {
                $amount_labels = [];
                $amount_mapping = [
                    '0-1000' => '0 - 1,000',
                    '1000-10000' => '1,000 - 10,000',
                    '10000-20000' => '10,000 - 20,000',
                    '20000-30000' => '20,000 - 30,000',
                    '30000-40000' => '30,000 - 40,000',
                    '40000-60000' => '40,000 - 60,000',
                    '70000-80000' => '70,000 - 80,000',
                    '80000-90000' => '80,000 - 90,000',
                    '90000-100000' => '90,000 - 100,000',
                    '100000+' => '100,000+'
                ];
                foreach ($selected_amounts as $amount) {
                    if (isset($amount_mapping[$amount])) {
                        $amount_labels[] = $amount_mapping[$amount];
                    }
                }
                if (!empty($amount_labels)) {
                    $active_filters[] = "Amount: " . (count($amount_labels) > 2 ? 
                        implode(", ", array_slice($amount_labels, 0, 2)) . " +" . (count($amount_labels) - 2) : 
                        implode(", ", $amount_labels));
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
                    <a href="invoices.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                    </a>
                </div>
            <?php endif; ?>

            <!-- Multiple Delete Button -->
            <a href="#" class="btn btn-outline-danger delete-multiple" style="display:none;">
                <i class="fa-regular fa-trash-can me-1"></i>Delete
            </a>
        </div>
    </div>
</div>
                <!-- /Table Search End -->

                <!-- Table List Start -->
                <div class="tab-content">
                    <div class="tab-pane active show" id="tab1">
                        <div class="table-responsive">
                            <table class="table table-nowrap datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input type="checkbox" class="form-check-input parent-checkbox">
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Client</th>
                                        <th>Invoice Date</th>
                                        <th>Amount</th>

                                        <th>Status</th>
                                        <th>Payment Mode</th>
                                        <th>Due Date</th>
                                        <th class="no-sort">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $invoiceId = $row['id'];
                                        $clientImg = !empty($row['customer_image']) ? '../uploads/' . htmlspecialchars($row['customer_image']) : 'assets/img/users/user-16.jpg';
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input user-checkbox" type="checkbox"
                                                        value="<?= $invoiceId ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                    class="link-default"><?= htmlspecialchars($row['invoice_id']) ?></a>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                        class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                                        <img src="<?= $clientImg ?>"
                                                            onerror="this.src='assets/img/users/user-16.jpg';">
                                                    </a>
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-0"><a
                                                                href="invoice-details.php?id=<?= $invoiceId ?>"><?= htmlspecialchars($row['first_name']) ?></a></a>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= date('d M Y', strtotime($row['invoice_date'])) ?></td>
                                            <td class="text-dark">$&nbsp;<?= htmlspecialchars($row['total_amount']) ?></td>


                                            <td>
                                                <?php
                                                $status = strtolower($row['status']);
                                                $statusClass = '';
                                                $statusIcon = '';

                                                switch ($status) {
                                                    case 'paid':
                                                        $statusClass = 'badge-soft-success';
                                                        $statusIcon = 'isax-tick-circle';
                                                        break;

                                                    case 'unpaid':
                                                        $statusClass = 'badge-soft-warning';
                                                        $statusIcon = 'isax-clock';
                                                        break;

                                                    case 'cancelled':
                                                        $statusClass = 'badge-soft-danger';
                                                        $statusIcon = 'isax-close-circle';
                                                        break;

                                                    case 'partially paid':
                                                        $statusClass = 'badge-soft-purple';
                                                        $statusIcon = 'isax-money-send';
                                                        break;

                                                    case 'uncollectable':
                                                        $statusClass = 'badge-soft-orange';
                                                        $statusIcon = 'isax-alert-circle';
                                                        break;

                                                    default:
                                                        $statusClass = 'badge-soft-secondary';
                                                        $statusIcon = 'isax-more';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?> d-inline-flex align-items-center">
                                                    <?= ucfirst($status) ?>
                                                    <i class="isax <?= $statusIcon ?> ms-1"></i>
                                                </span>
                                            </td>

                                            <td class="text-dark">Cash</td>
                                            <td><?= date('d M Y', strtotime($row['due_date'])) ?></td>
                                            <td class="action-item">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                    <i class="isax isax-more"></i>
                                                </a>
                                                <ul class="dropdown-menu">
                                                       <?php if (check_is_access_new("view_invoice") == 1) { ?>
                                                    <li>
                                                        <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                class="isax isax-eye"></i>&nbsp;&nbsp;View</a>

                                                    </li>
                                                     <?php } ?>
                                                      <?php if (check_is_access_new("edit_invoice") == 1) { ?>
                                                    <li>
                                                        <a href="edit-invoice.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                class="isax isax-edit me-2"></i>Edit</a>
                                                    </li>
                                                     <?php } ?>
                                                      <?php if (check_is_access_new("delete_invoice") == 1) { ?>
                                                     <li>
                                                    <a href="javascript:void(0);"
                                                        class="dropdown-item d-flex align-items-center open-delete-modal"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-invoice-id="<?= $invoiceId ?>">
                                                        <i class="isax isax-trash me-2"></i>Delete
                                                    </a>
                                                    </li>
                                                     <?php } ?>

                                                    <!-- <li>
                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Resend</a>
                                                </li> -->
                                                    <li>
                                                        <a href="process/action_download_listing_invoice_pdf.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center" target="_blank">
                                                            <i class="isax isax-document-download me-2"></i>Download Invoice
                                                            as PDF
                                                        </a>
                                                    </li>

                                                    <!-- <li>
                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-convert me-2"></i>Convert to Sales Return</a>
                                                </li> -->
                                                    <li>
                                                        <a href="process/action_duplicate_invoice.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center">
                                                            <i class="isax isax-copy me-2"></i>Clone as Invoice
                                                        </a>
                                                    </li>

                                                </ul>
                                            </td>
                                        </tr>

                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab2">
                        <div class="table-responsive">
                            <table class="table table-nowrap datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input type="checkbox" class="form-check-input parent-checkbox">
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Client</th>
                                        <th>Invoice Date</th>
                                        <th>Amount</th>

                                        <th>Status</th>
                                        <th>Payment Mode</th>
                                        <th>Due Date</th>
                                        <th class="no-sort">Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // MODIFIED QUERY FOR PAID TAB WITH ROLE-BASED FILTERING
                                    $paidFilterSql = "WHERE i.status = 'Paid' AND i.is_deleted = 0";
                                    if ($userRoleId != 1) {
                                        $paidFilterSql .= " AND i.user_id = $currentUserId";
                                    }
                                    
                                    $sql = "SELECT i.*, c.first_name, c.last_name, c.customer_image 
                                    FROM invoice i 
                                    LEFT JOIN client c ON i.client_id = c.id 
                                    $paidFilterSql
                                    ORDER BY i.id DESC";

                                    $result = mysqli_query($conn, $sql);

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $invoiceId = $row['id'];
                                        $clientImg = !empty($row['customer_image']) ? '../uploads/' . htmlspecialchars($row['customer_image']) : 'assets/img/users/user-16.jpg';
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input user-checkbox" type="checkbox"
                                                        value="<?= $invoiceId ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                    class="link-default"><?= htmlspecialchars($row['invoice_id']) ?></a>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                        class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                                        <img src="<?= $clientImg ?>"
                                                            onerror="this.src='assets/img/users/user-16.jpg';">
                                                    </a>
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-0"><a
                                                                href="invoice-details.php?id=<?= $invoiceId ?>"><?= htmlspecialchars($row['first_name']) ?></a></a>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= date('d M Y', strtotime($row['invoice_date'])) ?></td>
                                            <td class="text-dark">$&nbsp;<?= htmlspecialchars($row['total_amount']) ?></td>


                                            <td>
                                                <?php
                                                $status = strtolower($row['status']);
                                                $statusClass = '';
                                                $statusIcon = '';

                                                switch ($status) {
                                                    case 'paid':
                                                        $statusClass = 'badge-soft-success';
                                                        $statusIcon = 'isax-tick-circle';
                                                        break;

                                                    case 'unpaid':
                                                        $statusClass = 'badge-soft-warning';
                                                        $statusIcon = 'isax-clock';
                                                        break;

                                                    case 'cancelled':
                                                        $statusClass = 'badge-soft-danger';
                                                        $statusIcon = 'isax-close-circle';
                                                        break;

                                                    case 'partially paid':
                                                        $statusClass = 'badge-soft-purple';
                                                        $statusIcon = 'isax-money-send';
                                                        break;

                                                    case 'uncollectable':
                                                        $statusClass = 'badge-soft-orange';
                                                        $statusIcon = 'isax-alert-circle';
                                                        break;

                                                    default:
                                                        $statusClass = 'badge-soft-secondary';
                                                        $statusIcon = 'isax-more';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?> d-inline-flex align-items-center">
                                                    <?= ucfirst($status) ?>
                                                    <i class="isax <?= $statusIcon ?> ms-1"></i>
                                                </span>
                                            </td>

                                            <td class="text-dark">Cash</td>
                                            <td><?= date('d M Y', strtotime($row['due_date'])) ?></td>
                                            <td class="action-item">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                    <i class="isax isax-more"></i>
                                                </a>
                                                <ul class="dropdown-menu">
                                                     <?php if (check_is_access_new("view_invoice") == 1) { ?>
                                                    <li>
                                                        <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                class="isax isax-eye"></i>&nbsp;&nbsp;View</a>

                                                    </li>
                                                     <?php } ?>
                                                     <?php if (check_is_access_new("edit_invoice") == 1) { ?>
                                                    <li>
                                                        <a href="edit-invoice.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                class="isax isax-edit me-2"></i>Edit</a>
                                                    </li>
                                                     <?php } ?>
                                                     <?php if (check_is_access_new("delete_invoice") == 1) { ?>
                                                    <a href="javascript:void(0);"
                                                        class="dropdown-item d-flex align-items-center open-delete-modal"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-invoice-id="<?= $invoiceId ?>">
                                                        <i class="isax isax-trash me-2"></i>Delete
                                                    </a>
                                                     <?php } ?>

                                                    <!-- <li>
                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Resend</a>
                                                </li> -->
                                                    <li>
                                                        <a href="process/action_download_listing_invoice_pdf.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center" target="_blank">
                                                            <i class="isax isax-document-download me-2"></i>Download Invoice
                                                            as PDF
                                                        </a>
                                                    </li>

                                                    <!-- <li>
                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-convert me-2"></i>Convert to Sales Return</a>
                                                </li> -->
                                                    <li>
                                                        <a href="process/action_duplicate_invoice.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center">
                                                            <i class="isax isax-copy me-2"></i>Clone as Invoice
                                                        </a>
                                                    </li>

                                                </ul>
                                            </td>
                                        </tr>

                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab5">
                        <div class="table-responsive">
                            <table class="table table-nowrap datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input type="checkbox" class="form-check-input parent-checkbox">
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Client</th>
                                        <th>Invoice Date</th>
                                        <th>Amount</th>

                                        <th>Status</th>
                                        <th>Payment Mode</th>
                                        <th>Due Date</th>
                                        <th class="no-sort">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // MODIFIED QUERY FOR CANCELLED TAB WITH ROLE-BASED FILTERING
                                    $cancelledFilterSql = "WHERE i.status = 'cancelled' AND i.is_deleted = 0";
                                    if ($userRoleId != 1) {
                                        $cancelledFilterSql .= " AND i.user_id = $currentUserId";
                                    }
                                    
                                    $sql = "SELECT i.*, c.first_name, c.last_name, c.customer_image 
                                    FROM invoice i 
                                    LEFT JOIN client c ON i.client_id = c.id 
                                    $cancelledFilterSql
                                    ORDER BY i.id DESC";

                                    $result = mysqli_query($conn, $sql);

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $invoiceId = $row['id'];
                                        $clientImg = !empty($row['customer_image']) ? '../uploads/' . htmlspecialchars($row['customer_image']) : 'assets/img/users/user-16.jpg';
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input user-checkbox" type="checkbox"
                                                        value="<?= $invoiceId ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                    class="link-default"><?= htmlspecialchars($row['invoice_id']) ?></a>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                        class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                                        <img src="<?= $clientImg ?>"
                                                            onerror="this.src='assets/img/users/user-16.jpg';">
                                                    </a>
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-0"><a
                                                                href="invoice-details.php?id=<?= $invoiceId ?>"><?= htmlspecialchars($row['first_name']) ?></a></a>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= date('d M Y', strtotime($row['invoice_date'])) ?></td>
                                            <td class="text-dark">$&nbsp;<?= htmlspecialchars($row['total_amount']) ?></td>


                                            <td>
                                                <?php
                                                $status = strtolower($row['status']);
                                                $statusClass = '';
                                                $statusIcon = '';

                                                switch ($status) {
                                                    case 'paid':
                                                        $statusClass = 'badge-soft-success';
                                                        $statusIcon = 'isax-tick-circle';
                                                        break;

                                                    case 'unpaid':
                                                        $statusClass = 'badge-soft-warning';
                                                        $statusIcon = 'isax-clock';
                                                        break;

                                                    case 'cancelled':
                                                        $statusClass = 'badge-soft-danger';
                                                        $statusIcon = 'isax-close-circle';
                                                        break;

                                                    case 'partially paid':
                                                        $statusClass = 'badge-soft-purple';
                                                        $statusIcon = 'isax-money-send';
                                                        break;

                                                    case 'uncollectable':
                                                        $statusClass = 'badge-soft-orange';
                                                        $statusIcon = 'isax-alert-circle';
                                                        break;

                                                    default:
                                                        $statusClass = 'badge-soft-secondary';
                                                        $statusIcon = 'isax-more';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?> d-inline-flex align-items-center">
                                                    <?= ucfirst($status) ?>
                                                    <i class="isax <?= $statusIcon ?> ms-1"></i>
                                                </span>
                                            </td>

                                            <td class="text-dark">Cash</td>
                                            <td><?= date('d M Y', strtotime($row['due_date'])) ?></td>
                                            <td class="action-item">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                    <i class="isax isax-more"></i>
                                                </a>
                                                <ul class="dropdown-menu">
                                                       <?php if (check_is_access_new("view_invoice") == 1) { ?>
                                                    <li>
                                                        <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                class="isax isax-eye"></i>&nbsp;&nbsp;View</a>

                                                    </li>
                                                      <?php } ?>
                                                       <?php if (check_is_access_new("edit_invoice") == 1) { ?>
                                                    <li>
                                                        <a href="edit-invoice.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                class="isax isax-edit me-2"></i>Edit</a>
                                                    </li>
                                                     <?php } ?>
                                                       <?php if (check_is_access_new("delete_invoice") == 1) { ?>
                                                    <a href="javascript:void(0);"
                                                        class="dropdown-item d-flex align-items-center open-delete-modal"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-invoice-id="<?= $invoiceId ?>">
                                                        <i class="isax isax-trash me-2"></i>Delete
                                                    </a>
                                                       <?php } ?>
                                                    <!-- <li>
                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Resend</a>
                                                </li> -->
                                                    <li>
                                                        <a href="process/action_download_listing_invoice_pdf.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center" target="_blank">
                                                            <i class="isax isax-document-download me-2"></i>Download Invoice
                                                            as PDF
                                                        </a>
                                                    </li>

                                                    <!-- <li>
                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-convert me-2"></i>Convert to Sales Return</a>
                                                </li> -->
                                                    <li>
                                                        <a href="process/action_duplicate_invoice.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center">
                                                            <i class="isax isax-copy me-2"></i>Clone as Invoice
                                                        </a>
                                                    </li>

                                                </ul>
                                            </td>
                                        </tr>

                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab7">
                        <div class="table-responsive">
                            <table class="table table-nowrap datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input type="checkbox" class="form-check-input parent-checkbox">
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Client</th>
                                        <th>Invoice Date</th>
                                        <th>Amount</th>

                                        <th>Status</th>
                                        <th>Payment Mode</th>
                                        <th>Due Date</th>
                                        <th class="no-sort">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // MODIFIED QUERY FOR UNPAID TAB WITH ROLE-BASED FILTERING
                                    $unpaidFilterSql = "WHERE i.status = 'unpaid' AND i.is_deleted = 0";
                                    if ($userRoleId != 1) {
                                        $unpaidFilterSql .= " AND i.user_id = $currentUserId";
                                    }
                                    
                                    $sql = "SELECT i.*, c.first_name, c.last_name, c.customer_image 
                                    FROM invoice i 
                                    LEFT JOIN client c ON i.client_id = c.id 
                                    $unpaidFilterSql
                                    ORDER BY i.id DESC";

                                    $result = mysqli_query($conn, $sql);

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $invoiceId = $row['id'];
                                        $clientImg = !empty($row['customer_image']) ? '../uploads/' . htmlspecialchars($row['customer_image']) : 'assets/img/users/user-16.jpg';
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input user-checkbox" type="checkbox"
                                                        value="<?= $invoiceId ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                    class="link-default"><?= htmlspecialchars($row['invoice_id']) ?></a>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                        class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                                        <img src="<?= $clientImg ?>"
                                                            onerror="this.src='assets/img/users/user-16.jpg';">
                                                    </a>
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-0"><a
                                                                href="invoice-details.php?id=<?= $invoiceId ?>"><?= htmlspecialchars($row['first_name']) ?></a></a>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= date('d M Y', strtotime($row['invoice_date'])) ?></td>
                                            <td class="text-dark">$&nbsp;<?= htmlspecialchars($row['total_amount']) ?></td>


                                            <td>
                                                <?php
                                                $status = strtolower($row['status']);
                                                $statusClass = '';
                                                $statusIcon = '';

                                                switch ($status) {
                                                    case 'paid':
                                                        $statusClass = 'badge-soft-success';
                                                        $statusIcon = 'isax-tick-circle';
                                                        break;

                                                    case 'unpaid':
                                                        $statusClass = 'badge-soft-warning';
                                                        $statusIcon = 'isax-clock';
                                                        break;

                                                    case 'cancelled':
                                                        $statusClass = 'badge-soft-danger';
                                                        $statusIcon = 'isax-close-circle';
                                                        break;

                                                    case 'partially paid':
                                                        $statusClass = 'badge-soft-purple';
                                                        $statusIcon = 'isax-money-send';
                                                        break;

                                                    case 'uncollectable':
                                                        $statusClass = 'badge-soft-orange';
                                                        $statusIcon = 'isax-alert-circle';
                                                        break;

                                                    default:
                                                        $statusClass = 'badge-soft-secondary';
                                                        $statusIcon = 'isax-more';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?> d-inline-flex align-items-center">
                                                    <?= ucfirst($status) ?>
                                                    <i class="isax <?= $statusIcon ?> ms-1"></i>
                                                </span>
                                            </td>

                                            <td class="text-dark">Cash</td>
                                            <td><?= date('d M Y', strtotime($row['due_date'])) ?></td>
                                            <td class="action-item">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                    <i class="isax isax-more"></i>
                                                </a>
                                                <ul class="dropdown-menu">
                                                 <?php if (check_is_access_new("view_invoice") == 1) { ?>

                                                    <li>
                                                        <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                class="isax isax-eye"></i>&nbsp;&nbsp;View</a>

                                                    </li>
                                                    <?php } ?>
                                                    <?php if (check_is_access_new("edit_invoice") == 1) { ?>
                                                    <li>
                                                        <a href="edit-invoice.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                class="isax isax-edit me-2"></i>Edit</a>
                                                    </li>
                                                    <?php } ?>
                                                 <?php if (check_is_access_new("delete_invoice") == 1) { ?>
                                                    <a href="javascript:void(0);"
                                                        class="dropdown-item d-flex align-items-center open-delete-modal"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-invoice-id="<?= $invoiceId ?>">
                                                        <i class="isax isax-trash me-2"></i>Delete
                                                    </a>
                                                    <?php } ?>
                                                    <!-- <li>
                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Resend</a>
                                                </li> -->
                                                    <li>
                                                        <a href="process/action_download_listing_invoice_pdf.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center" target="_blank">
                                                            <i class="isax isax-document-download me-2"></i>Download Invoice
                                                            as PDF
                                                        </a>
                                                    </li>

                                                    <!-- <li>
                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-convert me-2"></i>Convert to Sales Return</a>
                                                </li> -->
                                                    <li>
                                                        <a href="process/action_duplicate_invoice.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center">
                                                            <i class="isax isax-copy me-2"></i>Clone as Invoice
                                                        </a>
                                                    </li>

                                                </ul>
                                            </td>
                                        </tr>


                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab9">
                        <div class="table-responsive">
                            <table class="table table-nowrap datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input type="checkbox" class="form-check-input parent-checkbox">
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Client</th>
                                        <th>Invoice Date</th>
                                        <th>Amount</th>

                                        <th>Status</th>
                                        <th>Payment Mode</th>
                                        <th>Due Date</th>
                                        <th class="no-sort">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // MODIFIED QUERY FOR DRAFT TAB WITH ROLE-BASED FILTERING
                                    $draftFilterSql = "WHERE i.status = 'draft' AND i.is_deleted = 0";
                                    if ($userRoleId != 1) {
                                        $draftFilterSql .= " AND i.user_id = $currentUserId";
                                    }
                                    
                                    $sql = "SELECT i.*, c.first_name, c.last_name, c.customer_image 
                                    FROM invoice i 
                                    LEFT JOIN client c ON i.client_id = c.id 
                                    $draftFilterSql
                                    ORDER BY i.id DESC";

                                    $result = mysqli_query($conn, $sql);

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $invoiceId = $row['id'];
                                        $clientImg = !empty($row['customer_image']) ? '../uploads/' . htmlspecialchars($row['customer_image']) : 'assets/img/users/user-16.jpg';
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input user-checkbox" type="checkbox"
                                                        value="<?= $invoiceId ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                    class="link-default"><?= htmlspecialchars($row['invoice_id']) ?></a>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                        class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                                        <img src="<?= $clientImg ?>"
                                                            onerror="this.src='assets/img/users/user-16.jpg';">
                                                    </a>
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-0"><a
                                                                href="invoice-details.php?id=<?= $invoiceId ?>"><?= htmlspecialchars($row['first_name']) ?></a></a>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= date('d M Y', strtotime($row['invoice_date'])) ?></td>
                                            <td class="text-dark">$&nbsp;<?= htmlspecialchars($row['total_amount']) ?></td>

                                            <td>
                                                <?php
                                                $status = strtolower($row['status']);
                                                $statusClass = '';
                                                $statusIcon = '';

                                                switch ($status) {
                                                    case 'paid':
                                                        $statusClass = 'badge-soft-success';
                                                        $statusIcon = 'isax-tick-circle';
                                                        break;

                                                    case 'unpaid':
                                                        $statusClass = 'badge-soft-warning';
                                                        $statusIcon = 'isax-clock';
                                                        break;

                                                    case 'cancelled':
                                                        $statusClass = 'badge-soft-danger';
                                                        $statusIcon = 'isax-close-circle';
                                                        break;

                                                    case 'partially paid':
                                                        $statusClass = 'badge-soft-purple';
                                                        $statusIcon = 'isax-money-send';
                                                        break;

                                                    case 'uncollectable':
                                                        $statusClass = 'badge-soft-orange';
                                                        $statusIcon = 'isax-alert-circle';
                                                        break;

                                                    default:
                                                        $statusClass = 'badge-soft-secondary';
                                                        $statusIcon = 'isax-more';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?> d-inline-flex align-items-center">
                                                    <?= ucfirst($status) ?>
                                                    <i class="isax <?= $statusIcon ?> ms-1"></i>
                                                </span>
                                            </td>

                                            <td class="text-dark">Cash</td>
                                            <td><?= date('d M Y', strtotime($row['due_date'])) ?></td>
                                            <td class="action-item">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                    <i class="isax isax-more"></i>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <?php if (check_is_access_new("view_invoice") == 1) { ?>
                                                    <li>
                                                        <a href="invoice-details.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                class="isax isax-eye"></i>&nbsp;&nbsp;View</a>

                                                    </li>
                                                      <?php } ?>

                                                     <?php if (check_is_access_new("edit_invoice") == 1) { ?>
                                                    <li>
                                                        <a href="edit-invoice.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                class="isax isax-edit me-2"></i>Edit</a>
                                                    </li>
                                                     <?php } ?>
                                                       <?php if (check_is_access_new("delete_invoice") == 1) { ?>
                                                      <li>
                                                    <a href="javascript:void(0);"
                                                        class="dropdown-item d-flex align-items-center open-delete-modal"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-invoice-id="<?= $invoiceId ?>">
                                                        <i class="isax isax-trash me-2"></i>Delete
                                                    </a>
                                                </li>
                                                 <?php } ?>
                                                    <!-- <li>
                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Resend</a>
                                                </li> -->
                                                    <li>
                                                        <a href="process/action_download_listing_invoice_pdf.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center" target="_blank">
                                                            <i class="isax isax-document-download me-2"></i>Download Invoice
                                                            as PDF
                                                        </a>
                                                    </li>

                                                    <!-- <li>
                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-convert me-2"></i>Convert to Sales Return</a>
                                                </li> -->
                                                    <li>
                                                        <a href="process/action_duplicate_invoice.php?id=<?= $invoiceId ?>"
                                                            class="dropdown-item d-flex align-items-center">
                                                            <i class="isax isax-copy me-2"></i>Clone as Invoice
                                                        </a>
                                                    </li>

                                                </ul>
                                            </td>
                                        </tr>

                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /Table List End -->

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
                    <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="offcanvas"
                        aria-label="Close"><i class="fa-solid fa-x"></i></button>
                </div>
            </div>
            <div class="offcanvas-body pt-3">
                <form action="invoices.php" method="post">
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
                            <a href="javascript:void(0);"
                                class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border customer-toggle"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
                                <?= $clientText ?>
                            </a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info">
                                <div class="mb-3">
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon fs-12"><i
                                                class="isax isax-search-normal"></i></span>
                                        <input type="text" class="form-control form-control-sm search-customer"
                                            placeholder="Search">
                                    </div>
                                </div>
                                <ul class="mb-3 customer-list">
                                    <li class="d-flex align-items-center justify-content-between mb-3">
                                        <label class="d-inline-flex align-items-center text-gray-9">
                                            <input class="form-check-input select-all m-0 me-2" type="checkbox">
                                            Select All
                                        </label>
                                        <a href="javascript:void(0);"
                                            class="link-danger fw-medium text-decoration-underline reset-customer">Reset</a>
                                    </li>
                                    <?php
                                    $clients = mysqli_query($conn, "SELECT * FROM client WHERE is_deleted = 0");
                                    while ($client = mysqli_fetch_assoc($clients)) {
                                        $checked = in_array($client['id'], $selected_customers) ? 'checked' : '';
                                        echo '<li>
                                    <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                        <input class="form-check-input m-0 me-2" type="checkbox" name="customer[]" value="' . $client['id'] . '" ' . $checked . '>
                                        <span class="avatar avatar-sm rounded-circle me-2">
                                            <img src="' . (!empty($client['customer_image']) ? '../uploads/' . $client['customer_image'] : 'assets/img/users/user-16.jpg') . '" class="flex-shrink-0 rounded-circle" width="24" height="24" alt="' . htmlspecialchars($client['first_name']) . '">
                                        </span>
                                        ' . htmlspecialchars($client['first_name']) . '
                                    </label>
                                </li>';
                                    }
                                    ?>
                                </ul>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="javascript:void(0);"
                                            class="btn btn-outline-white w-100 close-filter">Cancel</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="javascript:void(0);"
                                            class="btn btn-primary w-100 user-apply">Select</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="input-group position-relative">
                            <input type="text" class="form-control date-range bookingrange rounded-end"
                                name="date_range" value="<?= $date_range ?>">
                            <input type="hidden" name="start_date" id="start_date" value="<?= $start_date ?>">
                            <input type="hidden" name="end_date" id="end_date" value="<?= $end_date ?>">
                            <span class="input-icon-addon fs-16 text-gray-9">
                                <i class="isax isax-calendar-2"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <?php
                        $amounts = [
                            '0-1000' => '0 - 1000',
                            '1000-10000' => '1000- 10000',
                            '10000-20000' => '10000-20000',
                            '20000-30000' => '20000-30000',
                            '30000-40000' => '30000-40000',
                            '40000-60000' => '40000-60000',
                            '70000-80000' => '70000-80000',
                            '80000-90000' => '80000-90000',
                            '90000-100000' => '90000-100000',
                            '100000+' => '100000+'
                        ];
                        $selectedAmountLabels = [];
                        foreach ($selected_amounts as $val) {
                            if (isset($amounts[$val])) {
                                $selectedAmountLabels[] = $amounts[$val];
                            }
                        }
                        $amountText = !empty($selectedAmountLabels) ? implode(", ", $selectedAmountLabels) : "Select";
                        ?>
                        <div class="dropdown">
                            <a href="javascript:void(0);"
                                class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border amount-toggle"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
                                <?= $amountText ?>
                            </a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info">
                                <ul class="mb-3 amount-list">
                                    <?php
                                    foreach ($amounts as $value => $label) {
                                        $checked = in_array($value, $selected_amounts) ? 'checked' : '';
                                        echo '<li>
                                    <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                        <input class="form-check-input m-0 me-2" type="checkbox" name="amount[]" value="' . $value . '" ' . $checked . '>
                                        ' . $label . '
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
                        $statusList = [
                            'draft' => 'text-secondary',
                            // 'sent' => 'text-info',
                            'unpaid' => 'text-danger',
                            'paid' => 'text-success',
                            // 'partailly paid' => 'text-warning',
                            // 'overdue' => 'text-danger',
                            'cancelled' => 'text-dark',
                            // 'refund' => 'text-primary',
                            // 'upcoming' => 'text-info',
                            // 'uncollectable' => 'text-muted'
                        ];
                        $selectedStatusLabels = [];
                        foreach ($selected_statuses as $val) {
                            $selectedStatusLabels[] = ucwords($val);
                        }
                        $statusText = !empty($selectedStatusLabels) ? implode(", ", $selectedStatusLabels) : "Select";
                        ?>
                        <div class="dropdown">
                            <a href="javascript:void(0);"
                                class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border status-toggle"
                                data-bs-toggle="dropdown">
                                <?= $statusText ?>
                            </a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info">
                                <ul class="mb-3 status-list ">
                                    <?php
                                    foreach ($statusList as $label => $color) {
                                        $checked = in_array($label, $selected_statuses) ? 'checked' : '';
                                        echo '<li>
                                    <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                        <input class="form-check-input m-0 me-2" type="checkbox" name="status[]" value="' . $label . '" ' . $checked . '>
                                        <i class="fa-solid fa-circle fs-6 ' . $color . ' me-1"></i>' . ucwords($label) . '
                                    </label>
                                </li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Payment Mode</label>
                        <div class="dropdown">
                            <a href="javascript:void(0);"
                                class="dropdown-toggle btn btn-lg bg-light  d-flex align-items-center justify-content-start fs-13 fw-normal border"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
                                Select
                            </a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info">
                                <ul class="mb-3">
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox"> Cash
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox"> Check
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox"> Bank Transfer
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox"> Paypal
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox"> Stripe
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="offcanvas-footer">
                        <div class="row g-2">
                            <div class="col-6"><a href="invoices.php" class="btn btn-outline-white w-100">Reset</a>
                            </div>
                            <div class="col-6"><button type="submit" class="btn btn-primary w-100"
                                    id="filter-submit">Apply</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Filter -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form method="POST" action="process/action_delete_invoice.php">
                        <input type="hidden" name="invoice_id" id="deleteInvoiceId">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Invoice</h6>
                            <p class="mb-3">Are you sure you want to delete this invoice?</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-outline-white me-3"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="multideleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form method="POST" id="multiDeleteForm" action="process/action_multi_delete_invoice.php">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete invoices</h6>
                            <p class="mb-3">Are you sure you want to delete the selected invoices?</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-outline-white me-3"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Wrapper -->

    <?php include 'layouts/vendor-scripts.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const deleteBtn = document.querySelector('.delete-multiple');

            // Function to toggle button visibility
            function toggleDeleteButton() {
                const anyChecked = document.querySelectorAll('.user-checkbox:checked').length > 0;
                deleteBtn.style.display = anyChecked ? 'inline-block' : 'none';
            }

            // Listen for changes on all checkboxes
            document.querySelectorAll('.user-checkbox, #parentCheckbox').forEach(checkbox => {
                checkbox.addEventListener('change', toggleDeleteButton);
            });

            // Parent checkbox (select all) handling per table
            // Watch all user + parent checkboxes
            document.addEventListener('change', function (e) {
                if (e.target.classList.contains('user-checkbox') || e.target.classList.contains('parent-checkbox')) {
                    toggleDeleteButton();
                }
            });

            // Parent checkbox select all
            document.querySelectorAll('.parent-checkbox').forEach(parent => {
                parent.addEventListener('change', function () {
                    const table = parent.closest('table');
                    table.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = parent.checked);
                    toggleDeleteButton();
                });
            });


            // Existing multi-delete logic
            const multiDeleteModal = new bootstrap.Modal(document.getElementById('multideleteModal'));

            deleteBtn.addEventListener('click', function (e) {
                e.preventDefault();
                const checkboxes = document.querySelectorAll('.user-checkbox:checked');

                if (checkboxes.length === 0) {
                    alert("Please select at least one invoice to delete.");
                    return;
                }

                const form = document.getElementById('multiDeleteForm');
                form.querySelectorAll('input[name="invoice_ids[]"]').forEach(el => el.remove());

                checkboxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'invoice_ids[]';
                    input.value = checkbox.value;
                    form.appendChild(input);
                });

                multiDeleteModal.show();
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            const parentCheckbox = $('#parentCheckbox');
            const childCheckboxes = $('.user-checkbox');

            // Parent controls all children
            parentCheckbox.on('change', function () {
                childCheckboxes.prop('checked', this.checked);
            });

            // Children control parent
            childCheckboxes.on('change', function () {
                const total = childCheckboxes.length;
                const checked = childCheckboxes.filter(':checked').length;
                parentCheckbox.prop('checked', checked > 0);
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.open-delete-modal');
            const deleteInput = document.getElementById('deleteInvoiceId');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const invoiceId = this.getAttribute('data-invoice-id');
                    deleteInput.value = invoiceId;
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {

            // --- Track previous selections ---
            function savePreviousSelection(container) {
                $(container).find("input[type='checkbox']").each(function () {
                    $(this).data("prev", $(this).prop("checked"));
                });
            }

            // --- Update dropdown label ---
            function formatDropdown(labels, selector, limit = 3) {
                if (labels.length > limit) {
                    $(selector).text(labels.slice(0, limit).join(", ") + " +" + (labels.length - limit));
                } else {
                    $(selector).text(labels.length > 0 ? labels.join(", ") : "Select");
                }
            }

            function updateDropdownLabels() {
                // Clients
                let customerLabels = [];
                $('.customer-list input[type="checkbox"]:checked').each(function () {
                    customerLabels.push($(this).closest('label').text().trim());
                });
                formatDropdown(customerLabels, '.customer-toggle');

                // Amount
                let amountLabels = [];
                $('.amount-list input[type="checkbox"]:checked').each(function () {
                    amountLabels.push($(this).closest('label').text().trim());
                });
                formatDropdown(amountLabels, '.amount-toggle');

                // Status
                let statusLabels = [];
                $('.status-list input[type="checkbox"]:checked').each(function () {
                    statusLabels.push($(this).closest('label').text().trim());
                });
                formatDropdown(statusLabels, '.status-toggle');
            }

            // Trigger update when checkboxes change
            $(document).on('change', '.customer-list input, .amount-list input, .status-list input', updateDropdownLabels);

            // --- Initialize a dropdown container ---
            function initDropdown(type) {
                const container = `.${type}-list`;

                // Save previous selection when dropdown opens
                $(container).closest(".dropdown").on("show.bs.dropdown", function () {
                    savePreviousSelection(container);
                });

                // Search
                $(`.search-${type}`).on("keyup", function () {
                    const value = $(this).val().toLowerCase();
                    $(container).find("li").each(function () {
                        if ($(this).find(".select-all").length > 0) return;
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                    });
                });

                // Select All
                $(`${container} .select-all`).on("change", function () {
                    const checked = $(this).is(":checked");
                    $(container).find("input[type='checkbox']").not(this).prop("checked", checked);
                    updateDropdownLabel(type);
                });

                // Reset button
                $(`.reset-${type}`).on("click", function () {
                    $(container).find("input[type='checkbox']").prop("checked", false);
                    $(container).find(".select-all").prop("checked", false);
                    updateDropdownLabel(type);
                });

                // Individual checkbox change
                $(`${container} input[type='checkbox']`).not(".select-all").on("change", function () {
                    updateDropdownLabel(type);
                    // Update select-all if all checked
                    const allChecked = $(container).find("input[type='checkbox']").not(".select-all").length ===
                        $(container).find("input[type='checkbox']:checked").not(".select-all").length;
                    $(container).find(".select-all").prop("checked", allChecked);
                });
            }

            // --- Cancel button restores previous selection ---
            $(".dropdown").on("click", ".close-filter", function () {
                const $dropdown = $(this).closest(".dropdown-menu");
                $dropdown.find("input[type='checkbox']").each(function () {
                    $(this).prop("checked", $(this).data("prev"));
                });
                // Update toggle text
                const toggleClass = $dropdown.closest(".dropdown").find(".dropdown-toggle").attr("class").split(" ").filter(c => c.includes("-toggle"))[0];
                updateDropdownLabel(toggleClass.split("-")[0]);
                $dropdown.removeClass("show");
            });

            // --- Apply button closes dropdown & updates label ---
            $(".dropdown").on("click", ".user-apply", function () {
                const $dropdown = $(this).closest(".dropdown-menu");
                const toggleClass = $dropdown.closest(".dropdown").find(".dropdown-toggle").attr("class").split(" ").filter(c => c.includes("-toggle"))[0];
                updateDropdownLabel(toggleClass.split("-")[0]);
                $dropdown.removeClass("show");
            });

            // --- Initialize all dropdowns ---
            initDropdown("customer");
            initDropdown("amount");
            initDropdown("status");
            // Add here if you want Payment Mode dropdown similar to others

        });
    </script>

    <script>
        $(document).ready(function () {
            // Initialize date range picker
            $('.bookingrange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            });

            $('.bookingrange').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
                $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
            });

            $('.bookingrange').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                $('#start_date').val('');
                $('#end_date').val('');
            });

            // Reset filter button
            $('#reset-filter').click(function () {
                $('#filter-form').find('input[type="checkbox"]').prop('checked', false);
                $('.bookingrange').val('');
                $('#start_date').val('');
                $('#end_date').val('');
                $('#filter-form').submit();
            });

            // Select all customers
            $('.select-all').change(function () {
                $('.customer-list input[type="checkbox"]').prop('checked', this.checked);
            });

            // Customer search
            $('.search-customer').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('.customer-list li').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Update dropdown toggle text based on selected values
            function updateDropdownLabel(container, toggleSelector) {
                let selected = [];
                $(container).find('input[type="checkbox"]:checked').each(function () {
                    selected.push($(this).closest('label').text().trim());
                });

                if (selected.length > 0) {
                    $(toggleSelector).text(selected.join(', '));
                } else {
                    $(toggleSelector).text('Select');
                }
            }

            // Watch changes in Clients
            $('.customer-list input[type="checkbox"]').on('change', function () {
                updateDropdownLabel('.customer-list', '.customer-toggle');
            });

            // Watch changes in Amount
            $('.amount-list input[type="checkbox"]').on('change', function () {
                updateDropdownLabel('.amount-list', '.amount-toggle');
            });

            // Watch changes in Status
            $('.status-list input[type="checkbox"]').on('change', function () {
                updateDropdownLabel('.status-list', '.status-toggle');
            });

        });
    </script>
    <script>
        $(document).ready(function () {
            // --- Your existing dropdown logic ---

            // Datepicker (keep your version)
            if ($('.bookingrange').length > 0) {
                var start = moment().subtract(6, 'days');
                var end = moment();

                function booking_range(start, end) {
                    $('.bookingrange').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
                    $('#start_date').val(start.format('YYYY-MM-DD'));
                    $('#end_date').val(end.format('YYYY-MM-DD'));
                }

                $('.bookingrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    locale: { format: 'MM/DD/YYYY', cancelLabel: 'Clear' },
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Year': [moment().startOf('year'), moment().endOf('year')],
                        'Next Year': [moment().add(1, 'year').startOf('year'), moment().add(1, 'year').endOf('year')]
                    }
                }, booking_range);

                booking_range(start, end);

                $('.bookingrange').on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                    $('#start_date').val('');
                    $('#end_date').val('');
                });
            }
        });
    </script>
</body>

</html>