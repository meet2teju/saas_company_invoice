<?php
include 'layouts/session.php';
include '../config/config.php'; // DB connection

$customer_id = $_SESSION['crm_user_id'] ?? 0;

// Set date filter
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-29 days'));
$end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Total Projects for logged-in customer within date range
$totalProjectQuery = "
    SELECT COUNT(*) as total 
    FROM project_users 
    WHERE user_id = '$customer_id' 
      AND is_deleted = 0 
      AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'
";

$totalProjectResult = mysqli_query($conn, $totalProjectQuery);
$totalProjectRow = mysqli_fetch_assoc($totalProjectResult);
$totalProjects = $totalProjectRow['total'] ?? 0;

// Get customer's outstanding amount (assuming status field indicates payment status)
$outstandingQuery = "
    SELECT SUM(total_amount) as outstanding
    FROM invoice 
    WHERE user_id = '$customer_id' 
    AND status IN ('unpaid', 'partially_paid')
    AND is_deleted = 0
";

$outstandingResult = mysqli_query($conn, $outstandingQuery);
$outstandingRow = mysqli_fetch_assoc($outstandingResult);
$outstandingAmount = $outstandingRow['outstanding'] ?? 0;

// Get customer's overdue amount
$overdueQuery = "
    SELECT SUM(total_amount) as overdue
    FROM invoice 
    WHERE user_id = '$customer_id' 
    AND status IN ('unpaid', 'partially_paid')
    AND due_date < CURDATE()
    AND is_deleted = 0
";

$overdueResult = mysqli_query($conn, $overdueQuery);
$overdueRow = mysqli_fetch_assoc($overdueResult);
$overdueAmount = $overdueRow['overdue'] ?? 0;

// Get customer's cancelled invoices amount
$cancelledQuery = "
    SELECT SUM(total_amount) as cancelled
    FROM invoice 
    WHERE user_id = '$customer_id' 
    AND status = 'cancelled'
    AND is_deleted = 0
    AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'
";

$cancelledResult = mysqli_query($conn, $cancelledQuery);
$cancelledRow = mysqli_fetch_assoc($cancelledResult);
$cancelledAmount = $cancelledRow['cancelled'] ?? 0;

// Get latest invoice for the customer
$latestInvoiceQuery = "
    SELECT i.*, c.first_name, c.last_name, c.email
    FROM invoice i
    LEFT JOIN client c ON i.user_id = c.id
    WHERE i.user_id = '$customer_id'
    AND i.is_deleted = 0
    ORDER BY i.created_at DESC
    LIMIT 1
";

$latestInvoiceResult = mysqli_query($conn, $latestInvoiceQuery);
$latestInvoice = mysqli_fetch_assoc($latestInvoiceResult);

// Get payment statistics for the customer
$paymentStatsQuery = "
    SELECT 
        COUNT(*) as total_invoices,
        SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
        SUM(CASE WHEN status = 'partially_paid' THEN 1 ELSE 0 END) as partial_count,
        SUM(CASE WHEN status = 'unpaid' THEN 1 ELSE 0 END) as unpaid_count,
        SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) as overdue_count,
        SUM(total_amount) as total_invoiced,
        SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END) as paid_amount,
        SUM(CASE WHEN status = 'partially_paid' THEN total_amount ELSE 0 END) as partial_amount,
        SUM(CASE WHEN status = 'unpaid' THEN total_amount ELSE 0 END) as unpaid_amount
    FROM invoice 
    WHERE user_id = '$customer_id'
    AND is_deleted = 0
";

$paymentStatsResult = mysqli_query($conn, $paymentStatsQuery);
$paymentStats = mysqli_fetch_assoc($paymentStatsResult);

// Get recent invoices for the customer (NO SERVER-SIDE SORTING - let DataTables handle it)
$recentInvoicesQuery = "
    SELECT i.*
    FROM invoice i
    WHERE i.user_id = '$customer_id'
    AND i.is_deleted = 0
    ORDER BY i.created_at DESC
    LIMIT 7
";

$recentInvoicesResult = mysqli_query($conn, $recentInvoicesQuery);
$recentInvoicesCount = mysqli_num_rows($recentInvoicesResult);

// Since there's no payments table, we'll use invoice status for recent activities
$recentActivitiesQuery = "
    SELECT 'invoice' as type, id, created_at, 
           CONCAT('Invoice ', invoice_id, ' created - Status: ', status) as description 
    FROM invoice 
    WHERE user_id = '$customer_id' 
    AND is_deleted = 0
    ORDER BY created_at DESC
    LIMIT 5
";

$recentActivitiesResult = mysqli_query($conn, $recentActivitiesQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
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

                <!-- Page Header -->
                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <h6>Dashboard</h6>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                        <div id="reportrange" class="reportrange-picker d-flex align-items-center">
                            <i class="isax isax-calendar text-gray-5 fs-14 me-1"></i>
                            <span class="reportrange-picker-field"><?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?></span>
                        </div>
                    </div>
                </div>
                <!-- End Page Header -->

                <!-- start row -->
                <div class="row">
                    <div class="col-sm-6 col-xl-3 d-flex">
                        <div class="card overflow-hidden z-1 flex-fill">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <p class="mb-1">Total Project</p>
                                        <h6 class="fs-16 fw-semibold"><?= number_format($totalProjects) ?></h6>
                                    </div>
                                    <span class="avatar avatar-lg bg-primary text-white avatar-rounded">
                                        <i class="isax isax-diagram"></i>
                                    </span>
                                </div>
                            </div><!-- end card body -->
                            <div class="position-absolute start-0 bottom-0 z-n1">
                                <img src="assets/img/bg/income-report-1.svg" alt="img">
                            </div>
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <!-- Outstanding -->
                    <div class="col-sm-6 col-xl-3 d-flex">
                        <div class="card overflow-hidden z-1 flex-fill">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <p class="mb-1">Outstanding</p>
                                        <h6 class="fs-16 fw-semibold">₹<?= number_format($outstandingAmount, 2) ?></h6>
                                    </div>
                                    <span class="avatar avatar-lg bg-success text-white avatar-rounded">
                                        <i class="isax isax-bag-2 fs-24"></i>
                                    </span>
                                </div>
                            </div><!-- end card body -->
                            <div class="position-absolute start-0 bottom-0 z-n1">
                                <img src="assets/img/bg/income-report-2.svg" alt="img">
                            </div>
                        </div><!-- end card -->
                    </div>
                    <!-- /Outstanding -->

                    <!-- Overdue -->
                    <div class="col-sm-6 col-xl-3 d-flex">
                        <div class="card overflow-hidden z-1 flex-fill">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <p class="mb-1">Overdue</p>
                                        <h6 class="fs-16 fw-semibold">₹<?= number_format($overdueAmount, 2) ?></h6>
                                    </div>
                                    <span class="avatar avatar-lg bg-warning text-white avatar-rounded">
                                        <i class="isax isax-wallet-3 fs-24"></i>
                                    </span>
                                </div>
                            </div><!-- end card body -->
                            <div class="position-absolute start-0 bottom-0 z-n1">
                                <img src="assets/img/bg/income-report-3.svg" alt="img">
                            </div>
                        </div><!-- end card -->
                    </div>
                    <!-- /Overdue -->

                    <!-- Cancelled -->
                    <div class="col-sm-6 col-xl-3 d-flex">
                        <div class="card overflow-hidden z-1 flex-fill">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <p class="mb-1">Cancelled</p>
                                        <h6 class="fs-16 fw-semibold">₹<?= number_format($cancelledAmount, 2) ?></h6>
                                    </div>
                                    <span class="avatar avatar-lg bg-danger text-white avatar-rounded">
                                        <i class="isax isax-wallet-money fs-24"></i>
                                    </span>
                                </div>
                            </div><!-- end card body -->
                            <div class="position-absolute start-0 bottom-0 z-n1">
                                <img src="assets/img/bg/income-report-4.svg" alt="img">
                            </div>
                        </div><!-- end card -->
                    </div>
                    <!-- /Cancelled -->

                </div>
                <!-- end row -->

                <div class="row">

                    <!-- Start Invoice Detail -->
                    <div class="col-xl-4 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="mb-1">Invoice Detail</h6>
                                </div>
                                <?php if ($latestInvoice): ?>
                                <div class="bg-dark-gradient  p-3 rounded mb-2">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="fs-16 fw-semibold  text-white">#<?= $latestInvoice['invoice_id'] ?></h6>
                                        <?php
                                        $dueDate = new DateTime($latestInvoice['due_date']);
                                        $today = new DateTime();
                                        $daysLeft = $today->diff($dueDate)->days;
                                        $badgeClass = $daysLeft <= 7 ? 'bg-danger' : 'bg-warning';
                                        $dueText = $daysLeft == 0 ? 'Due today' : ($daysLeft == 1 ? 'Due tomorrow' : "Due in $daysLeft days");
                                        ?>
                                        <span class="badge badge-sm <?= $badgeClass ?>"><?= $dueText ?></span>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div>
                                                <p class="fs-13 text-light mb-1">Issued On</p>
                                                <h6 class="fs-14 text-white fw-semibold text-truncate"><?= date('d M Y', strtotime($latestInvoice['created_at'])) ?></h6>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div>
                                                <p class="fs-13 text-light mb-1">Due Date</p>
                                                <h6 class="fs-14 text-white fw-semibold text-truncate"><?= date('d M Y', strtotime($latestInvoice['due_date'])) ?></h6>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div>
                                                <p class="fs-13 text-light mb-1">Status</p>
                                                <h6 class="fs-14 text-white fw-semibold text-truncate"><?= ucfirst($latestInvoice['status']) ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card border-0 bg-light mb-3 shadow-none">
                                    <div class="card-body">
                                        <div class="mb-3 pb-2 border-bottom">
                                            <p class="text-dark mb-1">Amount <span class="float-end">₹<?= number_format($latestInvoice['total_amount'], 2) ?></span></p>
                                            <?php if ($latestInvoice['tax_amount'] > 0): ?>
                                            <p class="text-dark mb-1">Tax <span class="float-end">₹<?= number_format($latestInvoice['tax_amount'], 2) ?></span></p>
                                            <?php endif; ?>
                                            <?php if ($latestInvoice['total_amount'] > 0): ?>
                                            <p class="text-dark mb-1">Discount <span class="text-danger float-end">- ₹<?= number_format($latestInvoice['total_amount'], 2) ?></span></p>
                                            <?php endif; ?>
                                        </div>
                                        <h6>Total (USD) <span class="float-end">₹<?= number_format($latestInvoice['total_amount'], 2) ?></span></h6>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col pt-1">
                                        <a href="invoice-details.php?id=<?= $latestInvoice['id'] ?>" class="btn btn-primary w-100 d-flex align-items-center justify-content-center"><i class="isax isax-money-send5 me-1"></i>Pay</a>
                                    </div>
                                    <div class="col pt-1">
                                        <a href="view-invoice.php?id=<?= $latestInvoice['id'] ?>" class="btn btn-dark w-100 d-flex align-items-center justify-content-center"><i class="isax isax-document-download5 me-1"></i>View</a>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-4">
                                    <p>No invoices found</p>
                                </div>
                                <?php endif; ?>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <!-- End Invoice Detail -->

                    <!-- Start Payment Statistics -->
                    <div class="col-xl-4 col-lg-6 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="mb-1">Payment Statistics</h6>
                                </div>
                                <div class="mb-2">
                                    <div id="radial-chart2" class="chart-set"></div>
                                </div>
                                <div class="d-flex align-items-center flex-wrap justify-content-center gap-2 mb-3">
                                    <p class="fs-13 text-dark d-flex align-items-center mb-0"><i class="fa-solid fa-square text-success fs-11 me-1"></i>Paid</p>
                                    <p class="fs-13 text-dark d-flex align-items-center mb-0"><i class="fa-solid fa-square text-primary fs-11 me-1"></i>Partially</p>
                                    <p class="fs-13 text-dark d-flex align-items-center mb-0"><i class="fa-solid fa-square text-warning fs-11 me-1"></i>Unpaid</p>
                                    <p class="fs-13 text-dark d-flex align-items-center mb-0"><i class="fa-solid fa-square text-pink fs-11 me-1"></i>Overdue</p>
                                </div>
                                <div class="border rounded p-2 mb-3">
                                    <div class="row g-2">
                                        <div class="col d-flex border-end">
                                            <div class="text-center flex-fill">
                                                <p class="fs-13 mb-1">Invoiced</p>
                                                <h6 class="fs-16 fw-semibold">₹<?= number_format($paymentStats['total_invoiced'] ?? 0, 2) ?></h6>
                                            </div>
                                        </div>
                                        <div class="col d-flex border-end">
                                            <div class="text-center flex-fill">
                                                <p class="fs-13 mb-1">Paid</p>
                                                <h6 class="fs-16 fw-semibold">₹<?= number_format($paymentStats['paid_amount'] ?? 0, 2) ?></h6>
                                            </div>
                                        </div>
                                        <div class="col d-flex border-end">
                                            <div class="text-center flex-fill">
                                                <p class="fs-13 mb-1">Partial</p>
                                                <h6 class="fs-16 fw-semibold">₹<?= number_format($paymentStats['partial_amount'] ?? 0, 2) ?></h6>
                                            </div>
                                        </div>
                                        <div class="col d-flex">
                                            <div class="text-center flex-fill">
                                                <p class="fs-13 mb-1">Unpaid</p>
                                                <h6 class="fs-16 fw-semibold">₹<?= number_format($paymentStats['unpaid_amount'] ?? 0, 2) ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-1 gap-3">
                                    <p class="mb-0">Updated from the last transaction on <?= date('l, d M Y') ?></p>
                                    <a href="javascript:void(0);" class="btn btn-md rounded-2 bg-light flex-shrink-0 fs-16 text-gray-9 border"><i class="isax isax-refresh fs-16"></i></a>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <!-- End Payment Statistics -->

                    <!-- Start Recent Activities -->
                    <div class="col-xl-4 col-lg-6 d-flex">
                        <div class="card flex-fill overflow-hidden">
                            <div class="card-body pb-0">
                                <div class="mb-0">
                                    <h6 class="mb-1 pb-3 mb-3 border-bottom">Recent Activities</h6>
                                    <div class="recent-activities">
                                        <?php if ($recentActivitiesResult && mysqli_num_rows($recentActivitiesResult) > 0): ?>
                                            <?php while ($activity = mysqli_fetch_assoc($recentActivitiesResult)): ?>
                                            <div class="d-flex align-items-center pb-3">
                                                <span class="border z-1 border-primary rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center bg-white p-1"><i class="fa fa-circle fs-8 text-primary"></i></span>
                                                <div class="recent-activities-flow">
                                                    <p class="mb-1"><?= $activity['description'] ?></p>
                                                    <p class="mb-0 d-inline-flex align-items-center fs-13"><i class="isax isax-calendar-25 me-1"></i><?= date('d M Y', strtotime($activity['created_at'])) ?></p>
                                                </div>
                                            </div>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <div class="text-center py-4">
                                                <p>No recent activities</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                            <a href="customer-activities.php" class="btn w-100 fs-14 py-2 shadow-lg fw-medium">View All</a>
                        </div><!-- end card -->
                    </div>
                    <!-- End Recent Activities -->

                </div>

                <div class="row">

                    <!-- Start Recent Invoices -->
                    <div class="col-xl-8 d-flex">
                        <div class="card flex-fill w-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                                    <h6 class="mb-1">Recent Invoices</h6>
                                    <a href="invoices.php" class="btn btn-sm btn-dark mb-1">View all Invoices</a>
                                </div>
                                <div class="table-responsive border recent-invoice-table table-nowrap">
                                    <table class="table table-nowrap m-0" id="invoicesTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Due Date</th>
                                                <th class="no-sort">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($recentInvoicesCount > 0): ?>
                                                <?php while ($invoice = mysqli_fetch_assoc($recentInvoicesResult)): ?>
                                                <tr>
                                                    <td>
                                                        <a href="invoice-details.php?id=<?= $invoice['id'] ?>" class="link-default"><?= $invoice['invoice_id'] ?></a>
                                                    </td>
                                                    <td class="text-dark">₹<?= number_format($invoice['total_amount'], 2) ?></td>
                                                    <td>
                                                        <?php
                                                        $statusClass = 'badge-soft-info';
                                                        $statusIcon = 'isax-timer';
                                                        if ($invoice['status'] == 'paid') {
                                                            $statusClass = 'badge-soft-success';
                                                            $statusIcon = 'isax-tick-circle';
                                                        } elseif ($invoice['status'] == 'partially_paid') {
                                                            $statusClass = 'badge-soft-warning';
                                                            $statusIcon = 'isax-slash';
                                                        } elseif ($invoice['status'] == 'cancelled') {
                                                            $statusClass = 'badge-soft-danger';
                                                            $statusIcon = 'isax-close-circle';
                                                        } elseif ($invoice['status'] == 'overdue') {
                                                            $statusClass = 'badge-soft-danger';
                                                            $statusIcon = 'isax-close-circle';
                                                        }
                                                        ?>
                                                        <span class="badge <?= $statusClass ?> badge-sm d-inline-flex align-items-center"><?= ucfirst($invoice['status']) ?><i class="isax <?= $statusIcon ?> ms-1"></i></span>
                                                    </td>
                                                    <td><?= date('d M Y', strtotime($invoice['due_date'])) ?></td>
                                                    <td>
                                                        <?php if ($invoice['status'] != 'paid' && $invoice['status'] != 'cancelled'): ?>
                                                        <a href="invoice-details.php?id=<?= $invoice['id'] ?>" class="btn btn-sm btn-primary d-inline-flex align-items-center"><i class="isax isax-money-send5 me-1"></i>Pay</a>
                                                        <?php else: ?>
                                                        <a href="view-invoice.php?id=<?= $invoice['id'] ?>" class="btn btn-sm btn-soft-info d-inline-flex align-items-center border-0 text-gray-3"><i class="isax isax-eye me-1"></i>View</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center py-4">No invoices found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <!-- End Recent Invoices -->

                    <!-- Start Recent Transactions -->
                    <div class="col-xl-4 d-flex">
                        <div class="card flex-fill w-100">
                            <div class="card-body">
                                <div class="mb-0">
                                    <h6 class="mb-3">Recent Invoices Status</h6>
                                    <?php if ($recentInvoicesCount > 0): ?>
                                        <?php 
                                        // Reset pointer and loop through invoices again
                                        mysqli_data_seek($recentInvoicesResult, 0);
                                        while ($invoice = mysqli_fetch_assoc($recentInvoicesResult)): 
                                        ?>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-lg rounded-pill border bg-light p-2 flex-shrink-0">
                                                    <i class="isax isax-receipt-item fs-24 text-primary"></i>
                                                </span>
                                                <div class="ms-2">
                                                    <h6 class="fs-14 fw-semibold mb-1"><?= $invoice['invoice_id'] ?></h6>
                                                    <p><?= date('d M Y', strtotime($invoice['created_at'])) ?></p>
                                                </div>
                                            </div>
                                            <span class="badge badge-soft-<?= $invoice['status'] == 'paid' ? 'success' : ($invoice['status'] == 'partially_paid' ? 'warning' : 'danger') ?> badge-lg d-inline-flex align-items-center">
                                                ₹<?= number_format($invoice['total_amount'], 2) ?>
                                            </span>
                                        </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <p>No recent invoices</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <!-- End Recent Transactions -->

                </div>
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

    <script>
    // Payment Statistics Chart
    var options = {
        series: [
            <?= $paymentStats['paid_count'] ?? 0 ?>,
            <?= $paymentStats['partial_count'] ?? 0 ?>,
            <?= $paymentStats['unpaid_count'] ?? 0 ?>,
            <?= $paymentStats['overdue_count'] ?? 0 ?>
        ],
        chart: {
            height: 250,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                dataLabels: {
                    name: {
                        fontSize: '22px',
                    },
                    value: {
                        fontSize: '16px',
                    },
                    total: {
                        show: true,
                        label: 'Total',
                        formatter: function (w) {
                            return <?= $paymentStats['total_invoices'] ?? 0 ?>;
                        }
                    }
                }
            }
        },
        labels: ['Paid', 'Partially Paid', 'Unpaid', 'Overdue'],
        colors: ['#27AE60', '#2F80ED', '#E2B93B', '#EB5757'],
    };

    var chart = new ApexCharts(document.querySelector("#radial-chart2"), options);
    chart.render();

    // Initialize DataTables only if we have data
    $(document).ready(function() {
        var table = $('#invoicesTable');
        var hasData = table.find('tbody tr').length > 0 && !table.find('tbody tr td[colspan]').length;
        
        if (hasData) {
            // Only initialize DataTables if we have actual data rows (not the "no data" row)
            if (!$.fn.DataTable.isDataTable('#invoicesTable')) {
                table.DataTable({
                    "paging": false,
                    "searching": false,
                    "info": false,
                    "ordering": true,
                    "autoWidth": false,
                    "order": [], // No initial sorting
                    "columnDefs": [{
                        "targets": 'no-sort',
                        "orderable": false,
                    }],
                    "language": {
                        "emptyTable": "No invoices found"
                    }
                });
            }
        } else {
            // If no data, add a simple message and ensure table styling is maintained
            console.log('No invoice data found, skipping DataTables initialization');
        }
    });
    </script>

</body>

</html>