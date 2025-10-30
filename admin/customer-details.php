<?php
include 'layouts/session.php';
include '../config/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Client ID.");
}

$clientId = intval($_GET['id']);
// $query = "SELECT * FROM client WHERE id = $clientId AND is_deleted = 0";
$query = "SELECT c.*, ca.billing_address1 , ca.billing_address2
          FROM client c
          LEFT JOIN client_address ca ON c.id = ca.client_id
          WHERE c.id = $clientId AND c.is_deleted = 0";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Client not found.");
}

// $client = mysqli_fetch_assoc($result);

$client = mysqli_fetch_assoc($result);

// Fetch invoices for this client
$invoiceQuery = "SELECT * FROM invoice WHERE client_id = $clientId AND is_deleted = 0 ORDER BY invoice_date DESC";
$invoiceResult = mysqli_query($conn, $invoiceQuery);

// Calculate invoice statistics
$statsQuery = "SELECT 
                COUNT(*) as total_invoices,
                SUM(total_amount) as total_amount,
                SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN status = 'unpaid' THEN total_amount ELSE 0 END) as outstanding_amount,
                SUM(CASE WHEN status = 'overdue' THEN total_amount ELSE 0 END) as overdue_amount,
                SUM(CASE WHEN status = 'draft' THEN total_amount ELSE 0 END) as draft_amount,
                SUM(CASE WHEN status = 'cancelled' THEN total_amount ELSE 0 END) as cancelled_amount,
                COUNT(CASE WHEN status = 'paid' THEN 1 END) as paid_count,
                COUNT(CASE WHEN status = 'unpaid' THEN 1 END) as unpaid_count,
                COUNT(CASE WHEN status = 'overdue' THEN 1 END) as overdue_count,
                COUNT(CASE WHEN status = 'draft' THEN 1 END) as draft_count,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_count
              FROM invoice 
              WHERE client_id = $clientId AND is_deleted = 0";
$statsResult = mysqli_query($conn, $statsQuery);
$stats = mysqli_fetch_assoc($statsResult);

$activitiesQuery = "SELECT 
                    id,
                    invoice_id,
                    status,
                    total_amount,
                    invoice_date,
                    due_date,
                    updated_at,
                    created_at
                  FROM invoice 
                  WHERE client_id = $clientId 
                  AND is_deleted = 0 
                  ORDER BY GREATEST(created_at, updated_at) DESC 
                  LIMIT 4";
$activitiesResult = mysqli_query($conn, $activitiesQuery);
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
                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <h6>Clients Detail</h6>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-primary d-flex align-items-center fs-14 fw-semibold" data-bs-toggle="dropdown">
                               Add New
                            </a>
                            <ul class="dropdown-menu  dropdown-menu-end">
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item"> <i class="isax isax-document-text fs-14 me-2"></i>Invoice </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item"> <i class="isax isax-money-send fs-14 me-2"></i> Expense</a>
                                </li>
                                <!-- <li>
                                    <a href="javascript:void(0);" class="dropdown-item"> <i class="isax isax-money-add fs-14 me-2"></i> Credit Notes</a>
                                </li> -->
                                <!-- <li>
                                    <a href="javascript:void(0);" class="dropdown-item"> <i class="isax isax-money-recive fs-14 me-2"></i> Debit Notes</a>
                                </li> -->
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item"> <i class="isax isax-document fs-14 me-2"></i> Purchase Order</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item"> <i class="isax isax-document-download fs-14 me-2"></i> Quotation</a>
                                </li>
                                <!-- <li>
                                    <a href="javascript:void(0);" class="dropdown-item"> <i class="isax isax-document-forward fs-14 me-2"></i> Delivery Challan</a>
                                </li> -->
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- End Page Header -->

                <!-- start row -->
                <div class="row">
                    <div class="col-xl-8">

                        <!-- Start User -->
                        <div class="card bg-light customer-details-info position-relative overflow-hidden">
                            <div class="card-body position-relative z-1">
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                        <?php
                                        $imagePath = !empty($client['customer_image']) ? "../uploads/" . $client['customer_image'] : "assets/img/users/default-user.jpg";
                                        ?>
                                        <div class="avatar avatar-xxl rounded-circle flex-shrink-0">
                                            <img src="<?= htmlspecialchars($imagePath) ?>" alt="Client Image" class="img-fluid rounded-circle border border-white border-2">
                                        </div>

                                        <div class="">
                                            <p class="text-primary fs-14 fw-medium mb-1">Cl-<?= htmlspecialchars($client['id']) ?></p>
                                            <h6 class="mb-2"> <?= htmlspecialchars($client['first_name']) ?><img src="assets/img/icons/confirme.svg" alt="confirme" class="ms-1">  </h6>
                                    <p class="fs-14 fw-regular">
                                        <i class="isax isax-location fs-14 me-1 text-gray-9"></i>
                                        <?= htmlspecialchars($client['billing_address1']) ?>
                                        <?= !empty($client['billing_address2']) ? '<br>' . htmlspecialchars($client['billing_address2']) : '' ?>
                                    </p>
                                        </div>
                                    </div>
                                            <a href="javascript:void(0);" class="btn btn-outline-white border border-1 border-grey border-sm bg-white"
                                            data-bs-toggle="modal" data-bs-target="#editClientModal">
                                            <i class="isax isax-edit-2 fs-13 fw-semibold text-dark me-1"></i> Edit Profile
                                            </a>
                                </div>

                                <div class="card border-0 shadow shadow-none mb-0 bg-white">
                                    <div class="card-body border-0 shadow shadow-none">
                                        <ul class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-0 m-0 list-unstyled">
                                            <li>
                                                <h6 class="mb-1 fs-14 fw-semibold"> <i class="isax isax-sms fs-14 me-2"></i>Email Address</h6>
                                                <p><?= htmlspecialchars($client['email']) ?></p>
                                            </li>
                                            <li>
                                                <h6 class="mb-1 fs-14 fw-semibold"> <i class="isax isax-call fs-14 me-2"></i>Phone</h6>
                                                <p><?= htmlspecialchars($client['phone_number']) ?></p>
                                            </li>
                                            <li>
                                                <h6 class="mb-1 fs-14 fw-semibold"> <i class="isax isax-building fs-14 me-2"></i>Company </h6>
                                                <p><?= htmlspecialchars($client['company_name']) ?></p>
                                            </li>
                                            <li>
                                                <h6 class="mb-1 fs-14 fw-semibold"> <i class="isax isax-global fs-14 me-2"></i>Website</h6>
                                                <p class="d-flex align-items-center"> <?= htmlspecialchars($client['website_url']) ?>  <i class="isax isax-link fs-14 ms-1 text-primary"></i></p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                            <img src="assets/img/icons/elements-01.svg" alt="elements-01" class="img-fluid customer-details-bg">
                        </div><!-- end card -->
                        <!-- End User -->

                        <!-- Start Statistics -->
                          <div class="card">
                            <div class="card-body">
                                <h6 class="pb-3 mb-3 border-1 border-bottom border-gray"> Invoice Statistics </h6>
                                <ul class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-0 m-0 list-unstyled">
                                    <li>
                                        <p class="mb-2"> <i class="fa-solid fa-circle fs-10 text-primary me-2"></i> Total Invoice </p>
                                        <h6 class="fs-16 fw-600"> $<?= number_format($stats['total_amount'] ?? 0, 2) ?></h6>
                                        <small class="text-muted">(<?= $stats['total_invoices'] ?? 0 ?> invoices)</small>
                                    </li>
                                    <li>
                                        <p class="mb-2"> <i class="fa-solid fa-circle fs-10 text-info me-2"></i> Outstanding </p>
                                        <h6 class="fs-16 fw-600"> $<?= number_format($stats['outstanding_amount'] ?? 0, 2) ?></h6>
                                        <small class="text-muted">(<?= $stats['unpaid_count'] ?? 0 ?> unpaid)</small>
                                    </li>
                                    <li>
                                        <p class="mb-2"> <i class="fa-solid fa-circle fs-10 text-danger me-2"></i> Overdue </p>
                                        <h6 class="fs-16 fw-600"> $<?= number_format($stats['overdue_amount'] ?? 0, 2) ?></h6>
                                        <small class="text-muted">(<?= $stats['overdue_count'] ?? 0 ?> overdue)</small>
                                    </li>
                                    <li>
                                        <p class="mb-2"> <i class="fa-solid fa-circle fs-10 text-purple me-2"></i> Draft </p>
                                        <h6 class="fs-16 fw-600"> $<?= number_format($stats['draft_amount'] ?? 0, 2) ?></h6>
                                        <small class="text-muted">(<?= $stats['draft_count'] ?? 0 ?> drafts)</small>
                                    </li>
                                    <li>
                                        <p class="mb-2"> <i class="fa-solid fa-circle fs-10 text-error me-2"></i> Cancelled </p>
                                        <h6 class="fs-16 fw-600"> $<?= number_format($stats['cancelled_amount'] ?? 0, 2) ?></h6>
                                        <small class="text-muted">(<?= $stats['cancelled_count'] ?? 0 ?> cancelled)</small>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- End Statistics -->

                        <!-- Start Tablelist -->
                          <div class="card table-info">
                            <div class="card-body">
                                <h6 class="pb-3 mb-3 border-1 border-bottom border-gray"> Invoice </h6>
                                <div class="table-responsive table-nowrap">
                                    <table class="table border  m-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="no-sort">ID</th>
                                                <th>Created On</th>
                                                <th>Amount</th>
                                                <th>Paid</th>
                                                <th class="no-sort">Status</th>
                                                <th>Due Date</th>
                                                <th class="no-sort"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(mysqli_num_rows($invoiceResult) > 0): ?>
                                                <?php while($invoice = mysqli_fetch_assoc($invoiceResult)): ?>
                                                    <tr>
                                                        <td>
                                                            <a href="invoice-details.php?id=<?= $invoice['id'] ?>" class="link-default"><?= htmlspecialchars($invoice['invoice_id']) ?></a>
                                                        </td>
                                                        <td><?= date('d M Y', strtotime($invoice['invoice_date'])) ?></td>
                                                        <td class="text-dark">$<?= number_format($invoice['total_amount'], 2) ?></td>
                                                        <td class="">
                                                            <?php 
                                                            $paidAmount = 0;
                                                            if ($invoice['status'] == 'paid') {
                                                                $paidAmount = $invoice['total_amount'];
                                                            } elseif ($invoice['status'] == 'partially_paid') {
                                                                // You might want to calculate actual paid amount from payments table
                                                                $paidAmount = $invoice['total_amount'] * 0.5; // Example: 50% paid
                                                            }
                                                            ?>
                                                            $<?= number_format($paidAmount, 2) ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $statusBadge = '';
                                                            switch($invoice['status']) {
                                                                case 'paid':
                                                                    $statusBadge = 'badge-soft-success';
                                                                    $statusIcon = 'isax-tick-circle';
                                                                    $statusText = 'Paid';
                                                                    break;
                                                                case 'unpaid':
                                                                    $statusBadge = 'badge-soft-warning';
                                                                    $statusIcon = 'isax-slash';
                                                                    $statusText = 'Unpaid';
                                                                    break;
                                                                case 'partially_paid':
                                                                    $statusBadge = 'badge-soft-primary';
                                                                    $statusIcon = 'isax-timer';
                                                                    $statusText = 'Partially Paid';
                                                                    break;
                                                                case 'cancelled':
                                                                    $statusBadge = 'badge-soft-danger';
                                                                    $statusIcon = 'isax-close-circle';
                                                                    $statusText = 'Cancelled';
                                                                    break;
                                                                case 'overdue':
                                                                    $statusBadge = 'badge-soft-danger';
                                                                    $statusIcon = 'isax-danger';
                                                                    $statusText = 'Overdue';
                                                                    break;
                                                                case 'draft':
                                                                    $statusBadge = 'badge-soft-purple';
                                                                    $statusIcon = 'isax-document';
                                                                    $statusText = 'Draft';
                                                                    break;
                                                                default:
                                                                    $statusBadge = 'badge-soft-secondary';
                                                                    $statusIcon = 'isax-info-circle';
                                                                    $statusText = ucfirst($invoice['status']);
                                                            }
                                                            ?>
                                                            <span class="badge <?= $statusBadge ?> badge-sm d-inline-flex align-items-center">
                                                                <?= $statusText ?><i class="isax <?= $statusIcon ?> ms-1"></i>
                                                            </span>
                                                        </td>
                                                        <td><?= date('d M Y', strtotime($invoice['due_date'])) ?></td>
                                                        <td class="action-item">
                                                            <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                                <i class="fa-solid fa-ellipsis"></i>
                                                            </a>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a href="invoice-details.php?id=<?= $invoice['id'] ?>" class="dropdown-item d-flex align-items-center"><i class="isax isax-eye me-2"></i>View</a>
                                                                </li>
                                                                <li>
                                                                    <a href="edit-invoice.php?id=<?= $invoice['id'] ?>" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-archive-2 me-2"></i>Archive</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal" data-invoice-id="<?= $invoice['id'] ?>"><i class="isax isax-trash me-2"></i>Delete</a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center py-4">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <i class="isax isax-document-text fs-48 text-muted mb-2"></i>
                                                            <p class="text-muted mb-0">No invoices found for this client</p>
                                                            <a href="create-invoice.php?client_id=<?= $clientId ?>" class="btn btn-primary btn-sm mt-2">Create First Invoice</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- End Tablelist -->

                    </div><!-- end col -->
                  <div class="col-xl-4">
                        <!-- Start Notes -->
                        <div class="card">
                            <div class="card-body">
                                <h6 class="pb-3 mb-3 border-1 border-bottom border-gray"> Notes </h6>
                                <p class="text-truncate line-clamb-3">  <?= htmlspecialchars($client['remark']) ?> </p>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                        <!-- End Notes -->
                        <!-- End Notes -->

                        <!-- Start Payment -->
                      <!-- Start Payment -->
<div class="card">
    <div class="card-body">
        <h6 class="pb-3 mb-3 border-1 border-bottom border-gray"> Payments History </h6>
        <?php
        // Fetch paid invoices for this client
        $paidInvoicesQuery = "SELECT * FROM invoice WHERE client_id = $clientId AND status = 'paid' AND is_deleted = 0 ORDER BY invoice_date DESC LIMIT 5";
        $paidInvoicesResult = mysqli_query($conn, $paidInvoicesQuery);
        
        if($paidInvoicesResult && mysqli_num_rows($paidInvoicesResult) > 0): 
            $counter = 0;
            while($paidInvoice = mysqli_fetch_assoc($paidInvoicesResult)):
                $counter++;
                // Alternate between transaction icons for visual variety
                $transactionIcon = ($counter % 2 == 0) ? 'transaction-02.svg' : 'transaction-01.svg';
        ?>
        <!-- Payment Item -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center">
                <a href="javascript:void(0);" class="avatar avatar-md flex-shrink-0 me-2">
                    <img src="assets/img/icons/<?= $transactionIcon ?>" class="rounded-circle" alt="img">
                </a>
                <div>
                    <h6 class="fs-14 fw-semibold mb-1"><a href="javascript:void(0);"><?= htmlspecialchars($client['first_name']) ?></a></h6>
                    <p class="fs-13"><a href="invoice-details.php?id=<?= $paidInvoice['id'] ?>" class="link-default">#<?= htmlspecialchars($paidInvoice['invoice_id']) ?></a></p>
                </div>
            </div>
            <div>
                <p class="mb-0 fs-13"> Amount </p>
                <p class="mb-0 fs-14 fw-semibold text-gray-9"> $<?= number_format($paidInvoice['total_amount'], 2) ?> </p>
            </div>
            <div class="text-end">
                <span class="badge badge-sm badge-soft-success"> Paid <i class="isax isax-tick-circle fs-10 fw-semibold ms-1"></i></span>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
            <!-- Static fallback payments if no paid invoices found -->
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center">
                    <a href="javascript:void(0);" class="avatar avatar-md flex-shrink-0 me-2">
                        <img src="assets/img/icons/transaction-01.svg" class="rounded-circle" alt="img">
                    </a>
                    <div>
                        <h6 class="fs-14 fw-semibold mb-1"><a href="javascript:void(0);"><?= htmlspecialchars($client['first_name']) ?></a></h6>
                        <p class="fs-13"><a href="invoice-details.php" class="link-default">#INV00001</a></p>
                    </div>
                </div>
                <div>
                    <p class="mb-0 fs-13"> Amount </p>
                    <p class="mb-0 fs-14 fw-semibold text-gray-9"> $0.00 </p>
                </div>
                <div class="text-end">
                    <span class="badge badge-sm badge-soft-success"> Paid <i class="isax isax-tick-circle fs-10 fw-semibold ms-1"></i></span>
                </div>
            </div>
            <div class="text-center py-3">
                <i class="isax isax-money-tick fs-32 text-muted mb-2"></i>
                <p class="text-muted mb-0">No paid invoices found</p>
                <small class="text-muted">Payments will appear here when invoices are marked as paid</small>
            </div>
        <?php endif; ?>
    </div><!-- end card body -->
</div><!-- end card -->
<!-- End Payment -->
                        <!-- End Payment -->

                        <!-- Start Recent Activities -->
                       <!-- Start Recent Activities -->
<div class="card flex-fill overflow-hidden">
    <div class="card-body pb-0">
        <div class="mb-0">
            <h6 class="mb-1 pb-3 mb-3 border-bottom">Recent Activities</h6>
            <div class="recent-activities recent-activities-two">
                <?php
                // Fetch recent invoice activities for this client
                $activitiesQuery = "SELECT 
                                    id,
                                    invoice_id,
                                    status,
                                    total_amount,
                                    invoice_date,
                                    due_date,
                                    updated_at,
                                    created_at
                                  FROM invoice 
                                  WHERE client_id = $clientId 
                                  AND is_deleted = 0 
                                  ORDER BY GREATEST(created_at, updated_at) DESC 
                                  LIMIT 4";
                $activitiesResult = mysqli_query($conn, $activitiesQuery);
                
                if($activitiesResult && mysqli_num_rows($activitiesResult) > 0): 
                    while($activity = mysqli_fetch_assoc($activitiesResult)):
                        $activityDate = !empty($activity['updated_at']) && $activity['updated_at'] != $activity['created_at'] 
                                      ? $activity['updated_at'] 
                                      : $activity['created_at'];
                        
                        $activityDescription = '';
                        $amountDisplay = '$' . number_format($activity['total_amount'], 2);
                        
                        switch($activity['status']) {
                            case 'paid':
                                $activityDescription = "Invoice <span class='text-gray-9 fw-semibold'>#{$activity['invoice_id']}</span> was fully paid";
                                break;
                            case 'partially_paid':
                                $activityDescription = "Invoice <span class='text-gray-9 fw-semibold'>#{$activity['invoice_id']}</span> was partially paid";
                                break;
                            case 'unpaid':
                                $activityDescription = "Invoice <span class='text-gray-9 fw-semibold'>#{$activity['invoice_id']}</span> was created - Amount: <span class='text-gray-9 fw-semibold'>{$amountDisplay}</span>";
                                break;
                            case 'overdue':
                                $activityDescription = "Invoice <span class='text-gray-9 fw-semibold'>#{$activity['invoice_id']}</span> is overdue - Amount: <span class='text-gray-9 fw-semibold'>{$amountDisplay}</span>";
                                break;
                            case 'draft':
                                $activityDescription = "Draft invoice <span class='text-gray-9 fw-semibold'>#{$activity['invoice_id']}</span> was created";
                                break;
                            case 'cancelled':
                                $activityDescription = "Invoice <span class='text-gray-9 fw-semibold'>#{$activity['invoice_id']}</span> was cancelled";
                                break;
                            default:
                                $activityDescription = "Invoice <span class='text-gray-9 fw-semibold'>#{$activity['invoice_id']}</span> status updated to <span class='text-gray-9 fw-semibold'>" . ucfirst($activity['status']) . "</span>";
                        }
                ?>
                <div class="d-flex align-items-center pb-3">
                    <span class="border z-1 border-primary rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center bg-white p-1">
                        <i class="fa fa-circle fs-8 text-primary"></i>
                    </span>
                    <div class="recent-activities-flow">
                        <p class="mb-1"><?= $activityDescription ?></p>
                        <p class="mb-0 d-inline-flex align-items-center fs-13">
                            <i class="isax isax-calendar-25 me-1"></i>
                            <?= date('d M Y', strtotime($activityDate)) ?>
                        </p>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php else: ?>
                    <!-- Fallback activities if no invoices found -->
                    <div class="d-flex align-items-center pb-3">
                        <span class="border z-1 border-primary rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center bg-white p-1">
                            <i class="fa fa-circle fs-8 text-primary"></i>
                        </span>
                        <div class="recent-activities-flow">
                            <p class="mb-1">Client <span class="text-gray-9 fw-semibold"><?= htmlspecialchars($client['first_name']) ?></span> was added to the system</p>
                            <p class="mb-0 d-inline-flex align-items-center fs-13">
                                <i class="isax isax-calendar-25 me-1"></i>
                                <?= date('d M Y', strtotime($client['created_at'] ?? 'now')) ?>
                            </p>
                        </div>
                    </div>
                    <div class="text-center py-3">
                        <i class="isax isax-activity fs-32 text-muted mb-2"></i>
                        <p class="text-muted mb-0">No recent invoice activities</p>
                        <small class="text-muted">Activities will appear here when invoices are created or updated</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- end card body -->
    <a href="invoice.php?client_id=<?= $clientId ?>" class="btn w-100 fs-14 py-2 shadow-lg fw-medium">View All Invoices</a>
</div><!-- end card -->
<!-- End Recent Activities -->
                        <!-- End Recent Activities -->
                    </div>
                </div>
                <!-- end row -->

                <!-- Start Footer-->
                <div class="footer d-sm-flex align-items-center justify-content-between bg-white py-2 px-4 border-top">
                    <p class="text-dark mb-0">&copy; 2025 <a href="javascript:void(0);" class="link-primary">kanakku</a>, All Rights Reserved</p>
                    <p class="text-dark">Version : 1.3.8</p>
                </div>
                <!-- End Footer-->
				 
            </div>
			<!-- End Content -->

        </div>

        <!-- ========================
			End Page Content
		========================= -->
        <!-- Edit Client Modal -->
       <!-- Edit Client Modal -->
        <div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="process/action_edit_clientprofile.php" method="POST" enctype="multipart/form-data" id="editClientForm">
            <input type="hidden" name="client_id" value="<?= $client['id'] ?>">
            <input type="hidden" name="old_image" value="<?= $client['customer_image'] ?>">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="editClientModalLabel">Edit Client Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body row">
                <!-- Image Preview -->
                <div class="mb-3 col-md-12 d-flex align-items-center">
                    <div id="add_image_preview" class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0">
                    <?php if (!empty($client['customer_image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($client['customer_image']) ?>" class="avatar avatar-xl rounded-circle" alt="Customer Image">
                    <?php else: ?>
                        <i class="isax isax-image text-primary fs-24"></i>
                    <?php endif; ?>
                    </div>
                    <div class="flex-grow-1">
                    <label for="clientImage" class="form-label">Upload Image<span class="text-danger ms-1">*</span></label>
                    <input type="file" class="form-control" name="image" id="clientImage">
                    <span class="text-danger small" id="imageError"></span>
                    </div>
                </div>

                <!-- Client Info -->
                <div class="mb-3 col-md-6">
                    <label for="clientName" class="form-label">Client Name<span class="text-danger ms-1">*</span></label>
                    <input type="text" class="form-control" id="clientName" name="first_name" value="<?= htmlspecialchars($client['first_name']) ?>" >
                    <span class="text-danger small" id="nameError"></span>
                </div>

                <div class="mb-3 col-md-6">
                    <label for="clientEmail" class="form-label">Email<span class="text-danger ms-1">*</span></label>
                    <input type="email" class="form-control" id="clientEmail" name="email" value="<?= htmlspecialchars($client['email']) ?>">
                    <span class="text-danger small" id="emailError"></span>
                </div>

                <div class="mb-3 col-md-6">
                    <label for="clientPhone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="clientPhone" name="phone_number" value="<?= htmlspecialchars($client['phone_number']) ?>">
                </div>

                <div class="mb-3 col-md-6">
                    <label for="clientCompany" class="form-label">Company Name<span class="text-danger ms-1">*</span></label>
                    <input type="text" class="form-control" id="clientCompany" name="company_name" value="<?= htmlspecialchars($client['company_name']) ?>">
                    <span class="text-danger small" id="companyError"></span>
                </div>

                <div class="mb-3 col-md-12">
                    <label for="clientWebsite" class="form-label">Website</label>
                    <input type="text" class="form-control" id="clientWebsite" name="website_url" value="<?= htmlspecialchars($client['website_url']) ?>">
                </div>
                </div>

                <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Client</button>
                </div>
            </div>
            </form>
        </div>
        </div>


        <!-- Delete Modal Start -->
        <div class="modal fade" id="delete_modal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <img src="assets/img/icons/delete.svg" alt="img">
                        </div>
                        <h6 class="mb-1">Delete Customer</h6>
                        <p class="mb-3">Are you sure, you want to delete Customer?</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
                            <a href="customer-details.php" class="btn btn-primary">Yes, Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Delete Modal  End -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("editClientForm");

    if (!form) return; // safety check

    form.addEventListener("submit", function (e) {
        let isValid = true;

        // Clear previous errors
        document.getElementById("emailError").textContent = "";
        document.getElementById("imageError").textContent = "";
        document.getElementById("companyError").textContent = "";
        document.getElementById("nameError").textContent = "";
        // Get fields
        const email = document.getElementById("clientEmail").value.trim();
        const image = document.getElementById("clientImage").value;
        const companyName = document.getElementById("clientCompany").value.trim();
         const clientName = document.getElementById("clientName").value.trim();

        // Validate email
        if (email === "") {
            document.getElementById("emailError").textContent = "Email is required.";
            isValid = false;
        } else {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                document.getElementById("emailError").textContent = "Please enter a valid email address.";
                isValid = false;
            }
        }

        // Validate image (if selected)
        if (image !== "") {
            const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
            if (!allowedExtensions.exec(image)) {
                document.getElementById("imageError").textContent = "Only JPG, JPEG, and PNG image files are allowed.";
                isValid = false;
            }
        }

        // Validate company name
        if (companyName === "") {
            document.getElementById("companyError").textContent = "Company name is required.";
            isValid = false;
        }
          if (clientName === "") {
            document.getElementById("nameError").textContent = "Name is required.";
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const imageInput = document.getElementById("clientImage");
    const imageError = document.getElementById("imageError");

    // Real-time validation when file changes
    imageInput.addEventListener("change", function () {
        imageError.textContent = ""; // clear old error
        const file = imageInput.value;

        if (file !== "") {
            const allowedExtensions = /\.(jpg|jpeg|png)$/i;
            if (!allowedExtensions.test(file)) {
                imageError.textContent = "Only JPG, JPEG, and PNG image files are allowed.";
                imageInput.value = ""; // clear invalid file
            }
        }
    });
});

</script>

</body>

</html>        