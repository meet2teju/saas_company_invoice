<?php
include 'layouts/session.php';
include '../config/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Invalid Quotation ID.";
    $_SESSION['message_type'] = "danger";
    header("Location: quotations.php");
    exit();
}

$quotationId = intval($_GET['id']);

// Fetch quotation info
$sql = "SELECT q.*, c.first_name, c.last_name, c.email, c.customer_image 
        FROM quotation q
        LEFT JOIN client c ON q.client_id = c.id
        WHERE q.id = $quotationId";
$result = mysqli_query($conn, $sql);
$quotation = mysqli_fetch_assoc($result);


$items_result = mysqli_query($conn, "SELECT ii.*, p.name AS product_name,p.code, t.name AS tax_name, u.name AS unit_name, t.rate AS tax_rate
    FROM quotation_item ii
    LEFT JOIN product p ON p.id = ii.product_id
    LEFT JOIN units u ON u.id = ii.unit_id
    LEFT JOIN tax t ON t.id = ii.tax_id
    WHERE ii.quotation_id = $quotationId AND ii.is_deleted = 0");

if (!$quotation) {
    $_SESSION['message'] = "Quotation not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: quotations.php");
    exit();
}
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
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="mb-0">View Quotation - <?= htmlspecialchars($quotation['quotation_id']) ?></h6>
                <a href="quotations.php" class="btn btn-outline-secondary"><i class="fa fa-arrow-left me-1"></i>Back</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold">Quotation Info</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th>Quotation ID</th>
                            <td><?= htmlspecialchars($quotation['quotation_id']) ?></td>
                        </tr>
                        <tr>
                            <th>Client</th>
                            <td><?= htmlspecialchars($quotation['first_name'] . ' ' . $quotation['last_name']) ?></td>
                        </tr>
                        <tr>
                            <th>Quotation Date</th>
                            <td><?= htmlspecialchars($quotation['quotation_date']) ?></td>
                        </tr>
                        <tr>
                            <th>Expiry Date</th>
                            <td><?= htmlspecialchars($quotation['expiry_date']) ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><?= htmlspecialchars($quotation['status']) ?></td>
                        </tr>
                        <tr>
                            <th>Reference Name</th>
                            <td><?= htmlspecialchars($quotation['reference_name']) ?></td>
                        </tr>
                    </table>

                    <hr>
                    <div class="mb-3">
                        <h6 class="mb-3">Product / Service Items</h6>
                        <div class="table-responsive rounded border-bottom-0 border table-nowrap">
                            <table class="table m-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Product/Service</th>
                                        <th>HSN code</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Selling Price</th>
                                        <th>Tax</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $taxSummary = [];
                                    $subtotal = 0;
                                    $i = 1;
                                    
                                    // Reset pointer to beginning for items result
                                    mysqli_data_seek($items_result, 0);
                                    
                                    while ($item = mysqli_fetch_assoc($items_result)) {
                                        $subtotal += $item['amount'];
                                        
                                        // calculate tax for this item
                                        if (!empty($item['tax_rate'])) {
                                            $lineTax = ($item['amount'] * $item['tax_rate']) / 100;

                                            // build label like GST (18%)
                                            $taxKey = $item['tax_name'] . ' (' . $item['tax_rate'] . '%)';

                                            // add to summary
                                            if (!isset($taxSummary[$taxKey])) {
                                                $taxSummary[$taxKey] = 0;
                                            }
                                            $taxSummary[$taxKey] += $lineTax;
                                        }
                                    ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                                            <td><?= $item['code'] ?></td>
                                            <td><?= $item['quantity'] ?></td>
                                            <td><?= htmlspecialchars($item['unit_name']) ?></td>
                                            <td>$&nbsp;<?= number_format($item['selling_price'], 2) ?></td>
                                            <td>
                                                <?= $item['tax_name'] . (!empty($item['tax_rate']) ? ' (' . $item['tax_rate'] . '%)' : '') ?>
                                            </td>
                                            <td>$&nbsp;<?= number_format($item['amount'], 2) ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-6"></div>
                        <div class="col-lg-6">
                            <div class="mb-3 p-4 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="fs-14 fw-semibold">Sub Amount</h6>
                                    <h6 class="fs-14 fw-semibold">$ <?= number_format($subtotal, 2) ?></h6>
                                </div>

                                <?php 
                                $totalTax = 0;
                                if (!empty($taxSummary)): 
                                    foreach ($taxSummary as $taxLabel => $taxAmount): 
                                        $totalTax += $taxAmount;
                                ?>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="fs-14 fw-semibold"><?= $taxLabel ?></h6>
                                        <h6 class="fs-14 fw-semibold">$ <?= number_format($taxAmount, 2) ?></h6>
                                    </div>
                                <?php 
                                    endforeach; 
                                endif; 
                                ?>

                               <?php if (!empty($quotation['shipping_charge']) && $quotation['shipping_charge'] > 0): ?>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="fs-14 fw-semibold">Shipping Charge</h6>
                                        <h6 class="fs-14 fw-semibold">$ <?= number_format($quotation['shipping_charge'], 2) ?></h6>
                                    </div>
                                <?php endif; ?>


                                <div class="d-flex align-items-center justify-content-between border-top pt-3 mb-3">
                                    <h5 class="fw-bold">Total Amount</h5>
                                    <h5 class="fw-bold">$ <?= number_format($quotation['total_amount'], 2) ?></h5>
                                </div>

                                <div class="mt-4">
                                    <h6 class="fs-14 fw-semibold mb-1">Total In Words</h6>
                                    <p class="fst-italic"><?= numberToWords($quotation['total_amount']) ?> Dollars</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="fw-bold">Attached Documents</h6>
                    <?php
                    $docs = mysqli_query($conn, "SELECT * FROM quotation_document WHERE quotation_id = $quotationId");
                    if (mysqli_num_rows($docs) > 0) {
                        while ($doc = mysqli_fetch_assoc($docs)) {
                            $path = '../uploads' . $doc['document'];
                            echo "<p><a href='$path' target='_blank'>" . basename($doc['document']) . "</a></p>";
                        }
                    } else {
                        echo "<p>No documents attached</p>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php include 'layouts/footer.php'; ?>
    </div>
</div>

<?php include 'layouts/vendor-scripts.php'; ?>
</body>
</html>