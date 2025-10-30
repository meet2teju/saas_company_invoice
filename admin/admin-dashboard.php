<?php include 'layouts/session.php'; ?>

<?php 
include '../config/config.php'; // DB connection


$time_filter = isset($_GET['time_filter']) ? $_GET['time_filter'] : 'yearly';

// Set date range based on filter
$current_date = date('Y-m-d');
if ($time_filter === 'monthly') {
    $start_date = date('Y-m-01'); // First day of current month
    $end_date = $current_date;
} elseif ($time_filter === 'weekly') {
    $start_date = date('Y-m-d', strtotime('monday this week')); // Start of current week
    $end_date = $current_date;
} else { // yearly (default)
    $start_date = date('Y-01-01'); // First day of current year
    $end_date = $current_date;
}

// Only override with custom date range if both are provided
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

// Include full day for end_date
$start_date_full = $start_date . " 00:00:00";
$end_date_full = $end_date . " 23:59:59";

// ------------------- Totals for Invoice Analytics -------------------
$query_invoiced = "SELECT SUM(total_amount) AS total_invoiced FROM invoice WHERE created_at BETWEEN '$start_date_full' AND '$end_date_full' AND is_deleted = 0";
$result_invoiced = mysqli_query($conn, $query_invoiced);
$total_invoiced = ($result_invoiced ? (mysqli_fetch_assoc($result_invoiced)['total_invoiced'] ?? 0) : 0);

$query_received = "SELECT SUM(total_amount) AS total_received FROM invoice WHERE status='paid' AND created_at BETWEEN '$start_date_full' AND '$end_date_full' AND is_deleted = 0";
$result_received = mysqli_query($conn, $query_received);
$total_received = ($result_received ? (mysqli_fetch_assoc($result_received)['total_received'] ?? 0) : 0);

$query_pending = "SELECT SUM(total_amount) AS total_pending 
                  FROM invoice 
                  WHERE status IN ('unpaid','draft') 
                  AND created_at BETWEEN '$start_date_full' AND '$end_date_full' 
                  AND is_deleted = 0";
$result_pending = mysqli_query($conn, $query_pending);
$total_pending = ($result_pending ? (mysqli_fetch_assoc($result_pending)['total_pending'] ?? 0) : 0);

// Calculate percentages for donut chart
$total_all = $total_invoiced > 0 ? $total_invoiced : 1; // avoid division by zero
$percent_invoiced = round(($total_invoiced / $total_all) * 100, 2);
$percent_received = round(($total_received / $total_all) * 100, 2);
$percent_pending  = round(($total_pending / $total_all) * 100, 2);

// ------------------- Monthly Paid & Unpaid -------------------
$paidAmounts = array_fill(1, 12, 0);
$unpaidAmounts = array_fill(1, 12, 0);

// Determine year based on filter
if ($time_filter === 'monthly') {
    $chartYear = date('Y');
    $query = "
        SELECT MONTH(DATE(invoice_date)) AS month, status, SUM(total_amount) AS total
        FROM invoice
        WHERE YEAR(DATE(invoice_date)) = '$chartYear' 
        AND MONTH(DATE(invoice_date)) = MONTH(CURDATE())
        AND is_deleted = 0
        GROUP BY MONTH(DATE(invoice_date)), status
    ";
} elseif ($time_filter === 'weekly') {
    // For weekly view, show days of the week
    $monday = date('Y-m-d', strtotime('monday this week'));
    $sunday = date('Y-m-d', strtotime('sunday this week'));
    
    $query = "
        SELECT DAYOFWEEK(DATE(invoice_date)) AS day, status, SUM(total_amount) AS total
        FROM invoice
        WHERE DATE(invoice_date) BETWEEN '$monday' AND '$sunday'
        AND is_deleted = 0
        GROUP BY DAYOFWEEK(DATE(invoice_date)), status
    ";
} else { // yearly (default)
    $chartYear = date('Y');
    $query = "
        SELECT MONTH(DATE(invoice_date)) AS month, status, SUM(total_amount) AS total
        FROM invoice
        WHERE YEAR(DATE(invoice_date)) = '$chartYear'
        AND is_deleted = 0
        GROUP BY MONTH(DATE(invoice_date)), status
    ";
}

$result = mysqli_query($conn, $query);

// Reset arrays for weekly view
if ($time_filter === 'weekly') {
    $paidAmounts = array_fill(1, 7, 0);
    $unpaidAmounts = array_fill(1, 7, 0);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $day = (int)$row['day'];
            if ($row['status'] === 'paid') {
                $paidAmounts[$day] = (float)$row['total'];
            } elseif ($row['status'] === 'unpaid') {
                $unpaidAmounts[$day] = (float)$row['total'];
            }
        }
    }
} else {
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $month = (int)$row['month'];
            if ($row['status'] === 'paid') {
                $paidAmounts[$month] = (float)$row['total'];
            } elseif ($row['status'] === 'unpaid') {
                $unpaidAmounts[$month] = (float)$row['total'];
            }
        }
    }
}

// ------------------- Totals & Recent Invoices -------------------
$total_clients_query = "SELECT COUNT(*) AS total_clients FROM client WHERE created_at BETWEEN '$start_date_full' AND '$end_date_full' AND is_deleted = 0";
$total_clients_result = mysqli_query($conn, $total_clients_query);
$total_clients = ($total_clients_result ? (mysqli_fetch_assoc($total_clients_result)['total_clients'] ?? 0) : 0);

$total_invoices_query = "SELECT COUNT(*) AS total_invoices FROM invoice WHERE created_at BETWEEN '$start_date_full' AND '$end_date_full' AND is_deleted = 0";
$total_invoices_result = mysqli_query($conn, $total_invoices_query);
$total_invoices = ($total_invoices_result ? (mysqli_fetch_assoc($total_invoices_result)['total_invoices'] ?? 0) : 0);

$total_due_query = "SELECT SUM(total_amount) AS total_amount FROM invoice WHERE status IN ('unpaid','draft') AND created_at BETWEEN '$start_date_full' AND '$end_date_full' AND is_deleted = 0";
$total_due_result = mysqli_query($conn, $total_due_query);
$total_due_amount = ($total_due_result ? (mysqli_fetch_assoc($total_due_result)['total_amount'] ?? 0) : 0);
$totalAmount = number_format($total_due_amount, 2);

$invoiceresult = mysqli_query($conn, "
    SELECT i.id, i.invoice_id, i.total_amount, i.due_date, DATE(i.created_at) AS created_date, i.status, c.first_name, c.customer_image 
    FROM invoice i
    LEFT JOIN client c ON i.client_id = c.id
    WHERE i.created_at BETWEEN '$start_date_full' AND '$end_date_full'
    AND i.is_deleted = 0
    ORDER BY i.id DESC LIMIT 10
");

// ------------------- Sales, Expenses, Earnings -------------------
$sales_query = "SELECT SUM(total_amount) AS total_sales FROM invoice WHERE status='paid' AND created_at BETWEEN '$start_date_full' AND '$end_date_full' AND is_deleted = 0";
$sales_result = mysqli_query($conn, $sales_query);
$salesValue = ($sales_result ? (mysqli_fetch_assoc($sales_result)['total_sales'] ?? 0) : 0);

$expense_query = "SELECT SUM(amount) AS total_expense FROM expenses WHERE date BETWEEN '$start_date_full' AND '$end_date_full' AND is_deleted = 0";
$expense_result = mysqli_query($conn, $expense_query);
$expenseValue = ($expense_result ? (mysqli_fetch_assoc($expense_result)['total_expense'] ?? 0) : 0);

$earnings = $salesValue - $expenseValue;

$totalSales = number_format($salesValue, 2);
$totalExpense = number_format($expenseValue, 2);
$totalEarnings = number_format($earnings, 2);

// ------------------- Helper Functions -------------------
function getTotal($conn, $table, $month = null, $year = null) {
    $query = "SELECT COUNT(*) AS total FROM $table WHERE is_deleted = 0";
    if ($month !== null && $year !== null) {
        $query .= " AND MONTH(DATE(created_at)) = $month AND YEAR(DATE(created_at)) = $year";
    }
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (int)($row['total'] ?? 0);
    }
    return 0;
}

function growthPercent($current, $last) {
    if ($last == 0) return $current > 0 ? 100 : 0;
    return round((($current - $last) / $last) * 100, 2);
}

// ------------------- Monthly Growth -------------------
$currentMonth = date('m');
$currentYear = date('Y');
$lastMonth = date('m', strtotime('-1 month'));
$lastYear = date('Y', strtotime('-1 month'));

$current_invoices = getTotal($conn, 'invoice', $currentMonth, $currentYear);
$last_invoices = getTotal($conn, 'invoice', $lastMonth, $lastYear);
$growth_invoices = growthPercent($current_invoices, $last_invoices);

$current_clients = getTotal($conn, 'client', $currentMonth, $currentYear);
$last_clients = getTotal($conn, 'client', $lastMonth, $lastYear);
$growth_clients = growthPercent($current_clients, $last_clients);

// For amount growth, we need to calculate total amounts instead of counts
function getTotalAmount($conn, $month = null, $year = null) {
    $query = "SELECT SUM(total_amount) AS total_amount FROM invoice WHERE is_deleted = 0";
    if ($month !== null && $year !== null) {
        $query .= " AND MONTH(DATE(created_at)) = $month AND YEAR(DATE(created_at)) = $year";
    }
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (float)($row['total_amount'] ?? 0);
    }
    return 0;
}

$current_amount = getTotalAmount($conn, $currentMonth, $currentYear);
$last_amount = getTotalAmount($conn, $lastMonth, $lastYear);
$growth_amount = growthPercent($current_amount, $last_amount);

// ------------------- Total Projects Count -------------------
$total_projects_query = "SELECT COUNT(*) AS total_projects FROM project WHERE created_at BETWEEN '$start_date_full' AND '$end_date_full' AND is_deleted = 0";
$total_projects_result = mysqli_query($conn, $total_projects_query);
$total_projects = ($total_projects_result ? (mysqli_fetch_assoc($total_projects_result)['total_projects'] ?? 0) : 0);

// ------------------- Monthly Growth for Projects -------------------
function getTotalProjects($conn, $month = null, $year = null) {
    $query = "SELECT COUNT(*) AS total FROM project WHERE is_deleted = 0";
    if ($month !== null && $year !== null) {
        $query .= " AND MONTH(DATE(created_at)) = $month AND YEAR(DATE(created_at)) = $year";
    }
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (int)($row['total'] ?? 0);
    }
    return 0;
}

$current_projects = getTotalProjects($conn, $currentMonth, $currentYear);
$last_projects = getTotalProjects($conn, $lastMonth, $lastYear);
$growth_projects = growthPercent($current_projects, $last_projects);

// quotation ....

// Main query
$sql = "SELECT q.id, q.quotation_id, q.quotation_date, q.status, c.first_name, c.customer_image 
        FROM quotation q
        LEFT JOIN client c ON q.client_id = c.id
        ORDER BY q.id DESC LIMIT 10";

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- changes -->
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

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
            <div class="content">

                <!-- Start Breadcrumb -->
                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <h6>Dashboard</h6>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                        <div class="dropdown">
                            <a class="btn btn-primary d-flex align-items-center justify-content-center dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);" role="button">
								Create New
							</a>
                            <ul class="dropdown-menu dropdown-menu-start">
                                <li>
                                <a href="add-invoice.php" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-receipt-item5"></i>&nbsp;Invoice
                                </a>
                            </li>
                            <li>
                                <a href="add-expense.php" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-money-send5"></i>&nbsp;Expense
                                </a>
                            </li>
                        
                       
                            <li>
                                <a href="add-quotation.php" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-strongbox5"></i>&nbsp;Quotation
                                </a>
                            </li>
                             <li>
                                <a href="add-product.php" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-box5"></i>&nbsp;Product
                                </a>
                            </li>

                             <li>
                                <a href="add-customer.php" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-profile-2user5"></i>&nbsp;Client
                                </a>
                            </li>
                            <li>
                                <a href="add-projects.php" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-diagram"></i>&nbsp;Project
                                </a>
                            </li>
                        
                            <li>
                                <a href="users.php?open=add_user" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-profile-2user5"></i>&nbsp;Users
                                </a>
                            </li>

                            <li>
                                <a href="tax-rates.php?open=add_tax_rates" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-receipt-text"></i>&nbsp;Tax-Rates

                                </a>
                            </li>
                             <li>
                                <a href="bank.php?open=add_bank_modal" class="dropdown-item d-flex align-items-center">
                                   <i class="isax isax-building"></i>&nbsp;Bank
                                </a>
                            </li>
                               
                            </ul>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
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
                        <div id="reportrange" class="reportrange-picker d-flex align-items-center">
							<i class="isax isax-calendar text-gray-5 fs-14 me-1"></i>
                                                        <span class="reportrange-picker-field"><?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?></span>

						</div>
                    </div>
                </div>
                <!-- End Breadcrumb -->

				<!-- start row -->
                <div class="row">

                    <!-- Start Amount -->
                    <div class="col-sm-6 col-xl-3 d-flex">
                        <div class="card overflow-hidden z-1 flex-fill">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between border-bottom mb-2 pb-2">
                                    <div>
                                        <p class="mb-1">Amount Due</p>
                                         <h6 class="fs-16 fw-semibold">$&nbsp;<?php echo $totalAmount; ?></h6>
                                    </div>
                                    <span class="avatar avatar-lg bg-primary text-white avatar-rounded">
										<i class="isax isax-receipt-item fs-16"></i>
									</span>
                                </div>
                        <p class="fs-13">
                                        <span class="<?= $growth_amount >= 0 ? 'text-success' : 'text-danger' ?>">
                                            <i class="isax <?= $growth_amount >= 0 ? 'isax-send' : 'isax-received' ?> me-1"></i>
                                            <?= abs($growth_amount) ?>%
                                        </span> from last month
                                    </p>                            </div> <!-- end card body -->
                            <div class="position-absolute end-0 bottom-0 z-n1">
                                <img src="assets/img/bg/card-bg-04.svg" alt="img">
                            </div>
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <!-- End Amount -->

                 <!-- Invoices Card -->
                            <div class="col-sm-6 col-xl-3 d-flex">
                                <div class="card overflow-hidden z-1 flex-fill">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between border-bottom mb-2 pb-2">
                                            <div>
                                                <p class="mb-1">Invoices</p>
                                                <h6 class="fs-16 fw-semibold"><?php echo number_format($total_invoices); ?></h6>
                                            </div>
                                            <span class="avatar avatar-lg bg-warning text-white avatar-rounded">
                                                <i class="isax isax-timer fs-16"></i>
                                            </span>
                                        </div>
                                    <p class="fs-13">
                                            <span class="<?= $growth_invoices >= 0 ? 'text-success' : 'text-danger' ?>">
                                                <i class="isax <?= $growth_invoices >= 0 ? 'isax-send' : 'isax-received' ?> me-1"></i>
                                                <?= abs($growth_invoices) ?>%
                                            </span> from last month
                                        </p>
                                    </div>
                                    <div class="position-absolute end-0 bottom-0 z-n1">
                                        <img src="assets/img/bg/card-bg-06.svg" alt="img">
                                    </div>
                                </div>
                            </div>

                            <!-- Clients Card -->
                            <div class="col-sm-6 col-xl-3 d-flex">
                                <div class="card overflow-hidden z-1 flex-fill">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between border-bottom mb-2 pb-2">
                                            <div>
                                                <p class="mb-1">Clients</p>
                                                <h6 class="fs-16 fw-semibold"><?php echo number_format($total_clients); ?></h6>
                                            </div>
                                            <span class="avatar avatar-lg bg-success text-white avatar-rounded">
                                                <i class="isax isax-tick-circle fs-16"></i>
                                            </span>
                                        </div>
                                        <p class="fs-13">
                                            <span class="<?= $growth_clients >= 0 ? 'text-success' : 'text-danger' ?>">
                                                <i class="isax <?= $growth_clients >= 0 ? 'isax-send' : 'isax-received' ?> me-1"></i>
                                                <?= abs($growth_clients) ?>%
                                            </span> from last month
                                        </p>
                                    </div>
                                    <div class="position-absolute end-0 bottom-0 z-n1">
                                        <img src="assets/img/bg/card-bg-05.svg" alt="img">
                                    </div>
                                </div>
                            </div>


                    <!-- Start Estimates -->
                    <!-- <div class="col-sm-6 col-xl-3 d-flex">
                        <div class="card overflow-hidden z-1 flex-fill">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between border-bottom mb-2 pb-2">
                                    <div>
                                       
                                        <p class="mb-1">Users</p>

                                      
                                         upcomming
                                    </div>
                                    <span class="avatar avatar-lg bg-danger text-white avatar-rounded">
										<i class="isax isax-information fs-16"></i>
									</span>
                                </div>
                                <p class="fs-13"><span class="text-danger d-inline-flex align-items-center"><i class="isax isax-received me-1"></i>0.00%</span> from last month</p>
                            </div> 
                            <div class="position-absolute end-0 bottom-0 z-n1">
                                <img src="assets/img/bg/card-bg-07.svg" alt="img">
                            </div>
                        </div>
                    </div> -->
                    <!-- Projects Card -->
                    <div class="col-sm-6 col-xl-3 d-flex">
                        <div class="card overflow-hidden z-1 flex-fill">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between border-bottom mb-2 pb-2">
                                    <div>
                                        <p class="mb-1">Projects</p>
                                        <h6 class="fs-16 fw-semibold"><?php echo number_format($total_projects); ?></h6>
                                    </div>
                                    <span class="avatar avatar-lg bg-info text-white avatar-rounded">
                                        <i class="isax isax-diagram fs-16"></i>
                                    </span>
                                </div>
                                <p class="fs-13">
                                    <span class="<?= $growth_projects >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <i class="isax <?= $growth_projects >= 0 ? 'isax-send' : 'isax-received' ?> me-1"></i>
                                        <?= abs($growth_projects) ?>%
                                    </span> from last month
                                </p>
                            </div>
                            <div class="position-absolute end-0 bottom-0 z-n1">
                                <img src="assets/img/bg/card-bg-07.svg" alt="img">
                            </div>
                        </div>
                    </div>
                    <!-- end col -->

                
				<!-- end row -->

				<!-- start row -->
                <div class="row">

                    <!-- Start Sales Analytics -->
                    <div class="col-xl-8 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body pb-0">
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <h6 class="mb-1">Sales Analytics</h6>
                                   <div class="select-sm mb-1">
                                           <div class="dropdown">
                            <button class="btn btn-sm dropdown-toggle" type="button" id="timeframeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php 
                                if ($time_filter === 'monthly') echo 'This month';
                                elseif ($time_filter === 'weekly') echo 'This week';
                                else echo 'This year';
                                ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="timeframeDropdown">
                                <li><a class="dropdown-item <?= $time_filter === 'yearly' ? 'active' : '' ?>" href="?time_filter=yearly">This year</a></li>
                                <li><a class="dropdown-item <?= $time_filter === 'monthly' ? 'active' : '' ?>" href="?time_filter=monthly">This month</a></li>
                                <li><a class="dropdown-item <?= $time_filter === 'weekly' ? 'active' : '' ?>" href="?time_filter=weekly">This week</a></li>
                            </ul>
                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <div class="d-flex align-items-center flex-wrap gap-3">
                                            <div>
                                                <p class="fs-13 mb-1">Total Sales</p>
                                                    <h6 class="fs-16 fw-semibold text-primary">$&nbsp;<?php echo $totalSales; ?></h6>
                                            </div>
                                            <div>
                                                <p class="fs-13 mb-1">Expenses</p>
                                                <h6 class="fs-16 fw-semibold text-danger">$&nbsp;<?php echo $totalExpense; ?></h6>
                                            </div>
                                            <div>
                                                <p class="fs-13 mb-1">Earnings</p>
                                                <h6 class="fs-16 fw-semibold">$&nbsp;<?php echo $totalEarnings; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <p class="fs-13 text-dark d-flex align-items-center mb-0"><i class="fa-solid fa-circle text-info fs-12 me-1"></i>Received </p>
                                        <p class="fs-13 text-dark d-flex align-items-center mb-0"><i class="fa-solid fa-circle text-warning fs-12 me-1"></i>Pending</p>
                                    </div>
                                </div>
                                <div id="sales_analytics"></div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <!-- End Sales Analytics -->

                    <!-- Start Invoice Analytics -->
                    <div class="col-xl-4 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <h6 class="mb-1">Invoice Analytics</h6>
                                    
                                </div>
                                <div id="invoice_analytics"></div>
                                <div class="d-flex align-items-center justify-content-around gap-3 mb-3">
                                    <p class="fs-13 text-dark d-flex align-items-center mb-0"><i class="fa-solid fa-square text-primary fs-12 me-1"></i>Invoiced </p>
                                    <p class="fs-13 text-dark d-flex align-items-center mb-0"><i class="fa-solid fa-square text-success fs-12 me-1"></i>Received</p>
                                    <p class="fs-13 text-dark d-flex align-items-center mb-0"><i class="fa-solid fa-square text-warning fs-12 me-1"></i>Pending</p>
                                </div>
                                <div class="border rounded p-2">
                                    <div class="row g-2">
                                        <div class="col d-flex border-end ">
                                            <div class="text-center flex-fill">
                                                <p class="fs-13 mb-1">Invoiced</p>
                                                <h6 class="fs-16 fw-semibold">  $&nbsp;<?= number_format($total_invoiced, 2) ?></h6>
                                            </div>
                                        </div>
                                        <div class="col d-flex border-end ">
                                            <div class="text-center flex-fill">
                                                <p class="fs-13 mb-1">Received</p>
                                                <h6 class="fs-16 fw-semibold"> $&nbsp;<?= number_format($total_received, 2) ?></h6>
                                            </div>
                                        </div>
                                        <div class="col d-flex">
                                            <div class="text-center flex-fill">
                                                <p class="fs-13 mb-1">Pending</p>
                                                <h6 class="fs-16 fw-semibold">$&nbsp;<?= number_format($total_pending, 2) ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <!-- End Invoice Analytics -->

                </div>
				<!-- end row -->

				<!-- start row -->
                <div class="row">

                    <!-- Start Recent Invoices -->
                   <div class="col-lg-6">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                <h6 class="mb-1">Recent Invoices</h6>
                <a href="invoices.php" class="btn btn-sm btn-dark mb-1">View all</a>
            </div>
            <div class="table-responsive">
                            <table class="table table-nowrap datatable">
                                <thead class="thead-light">
                        <tr>
                            <th>Client</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($invoiceresult)): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0">
                                                 <a href="invoice-details.php?id=<?= $row['id'] ?>">
                                                <?= htmlspecialchars($row['first_name']) ?>
                                            </a>
                                            </h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-dark">$&nbsp;<?php echo number_format($row['total_amount'], 2); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['due_date'])); ?></td>
                                <td>
                                    <?php
                                    $status = strtolower($row['status']);
                                    $badge_class = 'badge-soft-info';
                                    $icon = 'isax-timer';
                                    if($status == 'paid'){
                                        $badge_class = 'badge-soft-success';
                                        $icon = 'isax-tick-circle';
                                    } elseif($status == 'partially paid'){
                                        $badge_class = 'badge-soft-warning';
                                        $icon = 'isax-slash';
                                    } elseif($status == 'overdue'){
                                        $badge_class = 'badge-soft-danger';
                                        $icon = 'isax-close-circle';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?> badge-sm d-inline-flex align-items-center">
                                        <?php echo ucfirst($row['status']); ?><i class="isax <?php echo $icon; ?> ms-1"></i>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div><!-- end card body -->
    </div><!-- end card -->
</div><!-- end col -->
                    <!-- End Recent Invoices -->

                    <!-- Start Recent Estimates -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                                    <h6 class="mb-1">Recent Quotation</h6>
                                    <a href="quotations.php" class="btn btn-sm btn-dark mb-1">View all</a>
                                </div>
                                <div class="table-responsive">
                           <table class="table table-nowrap datatable">
						<thead class="thead-light">
							<tr>
								<!-- <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input user-checkbox" type="checkbox" id="select-all">
                                    </div>
                                </th> -->
                                <th>Client</th>
								<th>Quotation ID</th>
								
								<th>Quotation Date</th>
								<th>Status</th>
								<!-- <th class="no-sort"></th> -->
							</tr>
						</thead>
						<tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                                $quotationId = $row['id'];
                                $clientImg = !empty($row['customer_image']) ? '../uploads/' . htmlspecialchars($row['customer_image']) : 'assets/img/users/user-16.jpg';
                            ?>
							<tr>
								<!-- <td>
                                  <div class="form-check form-check-md">
                                        <input class="form-check-input user-checkbox" type="checkbox" value="<?= $quotationId ?>">
                                    </div>
                                </td> -->
                                <td>
                                    <div class="d-flex align-items-center">
										<!-- <a href="view-quotation.php?id=<?= $quotationId ?>" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="<?= $clientImg ?>" onerror="this.src='assets/img/users/user-16.jpg';">
										</a> -->
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="view-quotation.php?id=<?= $quotationId ?>"><?= htmlspecialchars($row['first_name']) ?></a></h6>
										</div>
									</div>
                                </td>
								<td>
									<a href="view-quotation.php?id=<?= $quotationId ?>" class="link-default"><?= htmlspecialchars($row['quotation_id']) ?></a>
								</td>
								
								<td><?= date('d M Y', strtotime($row['quotation_date'])) ?></td>
								<td>
									<span class="badge badge-soft-success d-inline-flex align-items-center"><?= htmlspecialchars($row['status']) ?><i class="isax isax-tick-circle ms-1"></i></span>
								</td>
								<!-- <td class="action-item">
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

                                        <?php if (check_is_access_new("edit_quotation") == 1) { ?>
                                        <li>
                                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#delete_modal<?= $quotationId ?>"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>
                                         <?php } ?>

                                    </ul>
                                </td> -->
							</tr>
                   
							   <?php } ?>
						</tbody>
					</table>
            </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <!-- End Recent Estimates -->

                </div>
				<!-- end row -->
            </div>
            <!-- End Content -->

            <?php include 'layouts/footer.php'; ?>

        </div>
        
		<!-- ========================
			End Page Content
		========================= -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
      // ------------------- Invoice Donut -------------------
        var invoiceChart = new ApexCharts(document.querySelector("#invoice_analytics"), {
            series: [<?= $percent_invoiced ?>, <?= $percent_received ?>, <?= $percent_pending ?>],
            chart: { type:'donut', height:240 },
            labels:['Invoiced','Received','Pending'],
            colors:['#2F80ED','#27AE60','#E2B93B'],
            plotOptions:{ pie:{ donut:{ size:'70%' } } }
        });
        invoiceChart.render();

        // ------------------- Sales Bar -------------------
        <?php if ($time_filter === 'weekly'): ?>
        // Weekly view - show days of the week
        var categories = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        var paidAmounts = <?= json_encode(array_values($paidAmounts)) ?>;
        var unpaidAmounts = <?= json_encode(array_values($unpaidAmounts)) ?>;
        
        // Reorder to start with Monday (index 2) and end with Sunday (index 1)
        var reorderedPaid = [
            paidAmounts[1], // Sunday
            paidAmounts[2], // Monday
            paidAmounts[3], // Tuesday
            paidAmounts[4], // Wednesday
            paidAmounts[5], // Thursday
            paidAmounts[6], // Friday
            paidAmounts[0]  // Saturday (MySQL returns 7 for Saturday, but our array is 0-indexed)
        ];
        
        var reorderedUnpaid = [
            unpaidAmounts[1], // Sunday
            unpaidAmounts[2], // Monday
            unpaidAmounts[3], // Tuesday
            unpaidAmounts[4], // Wednesday
            unpaidAmounts[5], // Thursday
            unpaidAmounts[6], // Friday
            unpaidAmounts[0]  // Saturday
        ];
        
        var maxValue = Math.max(...reorderedPaid.concat(reorderedUnpaid));
        var stepSize = 200;
        var maxY = Math.ceil(maxValue / stepSize) * stepSize;

        var salesChart = new ApexCharts(document.querySelector("#sales_analytics"), {
            chart:{type:'bar',height:300},
            series:[
                {name:'Paid',data:reorderedPaid},
                {name:'Unpaid',data:reorderedUnpaid}
            ],
            colors:['#2F80ED','#E2B93B'],
            xaxis:{categories:categories},
            yaxis:{min:0,max:maxY,labels:{formatter:val=>'$ '+Number(val).toLocaleString()}}
        });
        <?php else: ?>
        // Monthly or Yearly view - show months
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var paidAmounts = <?= json_encode(array_values($paidAmounts)) ?>;
        var unpaidAmounts = <?= json_encode(array_values($unpaidAmounts)) ?>;

        // For monthly view, show only current month
        <?php if ($time_filter === 'monthly'): ?>
        var currentMonth = new Date().getMonth();
        var paidAmounts = [paidAmounts[currentMonth]];
        var unpaidAmounts = [unpaidAmounts[currentMonth]];
        var months = [months[currentMonth]];
        <?php endif; ?>

        var maxValue = Math.max(...paidAmounts.concat(unpaidAmounts));
        var stepSize = 200;
        var maxY = Math.ceil(maxValue / stepSize) * stepSize;

        var salesChart = new ApexCharts(document.querySelector("#sales_analytics"), {
            chart:{type:'bar',height:300},
            series:[
                {name:'Paid',data:paidAmounts},
                {name:'Unpaid',data:unpaidAmounts}
            ],
            colors:['#2F80ED','#E2B93B'],
            xaxis:{categories:months},
            yaxis:{min:0,max:maxY,labels:{formatter:val=>'$ '+Number(val).toLocaleString()}}
        });
        <?php endif; ?>
        salesChart.render();
    </script>
</body>
</html>