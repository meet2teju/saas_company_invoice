<?php include 'layouts/session.php'; ?>
<?php 
include '../config/config.php'; // DB connection

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-29 days'));
$end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Include full day for end_date
$start_date .= " 00:00:00";
$end_date   .= " 23:59:59";

// ------------------- Totals for Invoice Analytics -------------------
$query_invoiced = "SELECT SUM(total_amount) AS total_invoiced FROM invoice WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
$total_invoiced = mysqli_fetch_assoc(mysqli_query($conn, $query_invoiced))['total_invoiced'] ?? 0;

$query_received = "SELECT SUM(total_amount) AS total_received FROM invoice WHERE status='paid' AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
$total_received = mysqli_fetch_assoc(mysqli_query($conn, $query_received))['total_received'] ?? 0;

// $query_pending = "SELECT SUM(total_amount) AS total_pending FROM invoice WHERE status='unpaid' AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
$query_pending = "SELECT SUM(total_amount) AS total_pending 
                  FROM invoice 
                  WHERE status IN ('unpaid','draft') 
                  AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";

$total_pending = mysqli_fetch_assoc(mysqli_query($conn, $query_pending))['total_pending'] ?? 0;

// Calculate percentages for donut chart
$total_all = $total_invoiced > 0 ? $total_invoiced : 1; // avoid division by zero
$percent_invoiced = round(($total_invoiced / $total_all) * 100, 2);
$percent_received = round(($total_received / $total_all) * 100, 2);
$percent_pending  = round(($total_pending / $total_all) * 100, 2);

// ------------------- Monthly Paid & Unpaid -------------------
$paidAmounts = array_fill(1, 12, 0);
$unpaidAmounts = array_fill(1, 12, 0);

// FIXED: Use total_amount instead of amount for consistency
$query = "
    SELECT MONTH(DATE(created_at)) AS month, status, SUM(total_amount) AS total
    FROM invoice
    WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'
    GROUP BY MONTH(DATE(created_at)), status
";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['status'] === 'paid') {
        $paidAmounts[(int)$row['month']] = (float)$row['total'];
    } elseif ($row['status'] === 'unpaid') {
        $unpaidAmounts[(int)$row['month']] = (float)$row['total'];
    }
}

// ------------------- Totals & Recent Invoices -------------------
$total_clients = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_clients FROM client WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'"))['total_clients'] ?? 0;

$total_invoices = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_invoices FROM invoice WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'"))['total_invoices'] ?? 0;

$totalAmount = number_format(mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) AS total_amount FROM invoice WHERE status !='paid' AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'"))['total_amount'] ?? 0, 2);

$invoiceresult = mysqli_query($conn, "
    SELECT i.id, i.invoice_id, i.total_amount, i.due_date, DATE(i.created_at) AS created_date, i.status, c.first_name, c.customer_image 
    FROM invoice i
    LEFT JOIN client c ON i.client_id = c.id
    WHERE DATE(i.created_at) BETWEEN '$start_date' AND '$end_date'
    ORDER BY i.id DESC LIMIT 10
");
// $search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
// $invoiceresult = mysqli_query($conn, "
//     SELECT i.id, i.invoice_id, i.total_amount, i.due_date, DATE(i.created_at) AS created_date, i.status, c.first_name
//     FROM invoice i
//     LEFT JOIN client c ON i.client_id = c.id
//     WHERE (c.first_name LIKE '%$search%' OR i.invoice_id LIKE '%$search%')
// ");

// ------------------- Sales, Expenses, Earnings -------------------
$salesValue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) AS total_sales FROM invoice WHERE status='paid' AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'"))['total_sales'] ?? 0;
$expenseValue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) AS total_expense FROM expenses WHERE date BETWEEN '$start_date' AND '$end_date'"))['total_expense'] ?? 0;
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
    $row = mysqli_fetch_assoc($result);
    return (int)($row['total'] ?? 0);
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

$growth_invoices = growthPercent(getTotal($conn, 'invoice', $currentMonth, $currentYear), getTotal($conn, 'invoice', $lastMonth, $lastYear));
$growth_clients = growthPercent(getTotal($conn, 'client', $currentMonth, $currentYear), getTotal($conn, 'client', $lastMonth, $lastYear));
$growth_amount = growthPercent(getTotal($conn, 'invoice', $currentMonth, $currentYear), getTotal($conn, 'invoice', $lastMonth, $lastYear));

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
                                li>
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
                    <div class="col-sm-6 col-xl-3 d-flex">
                        <div class="card overflow-hidden z-1 flex-fill">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between border-bottom mb-2 pb-2">
                                    <div>
                                        <p class="mb-1">Estimates</p>
                                        <!-- <h6 class="fs-16 fw-semibold">2,000</h6> -->
                                         upcomming
                                    </div>
                                    <span class="avatar avatar-lg bg-danger text-white avatar-rounded">
										<i class="isax isax-information fs-16"></i>
									</span>
                                </div>
                                <p class="fs-13"><span class="text-danger d-inline-flex align-items-center"><i class="isax isax-received me-1"></i>0.00%</span> from last month</p>
                            </div> <!-- end card body -->
                            <div class="position-absolute end-0 bottom-0 z-n1">
                                <img src="assets/img/bg/card-bg-07.svg" alt="img">
                            </div>
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <!-- End Estimates -->

                </div>
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
                                    <h6 class="mb-1">Recent Estimates</h6>
                                    <a href="quotations.php" class="btn btn-sm btn-dark mb-1">View all</a>
                                </div>
                                <div class="table-responsive border table-nowrap">
                                  <table>
                                      
                                    </table>
                                Upcomming
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
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var paidAmounts = <?= json_encode(array_values($paidAmounts)) ?>;
        var unpaidAmounts = <?= json_encode(array_values($unpaidAmounts)) ?>;

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
        salesChart.render();
    </script>
</body>
</html>