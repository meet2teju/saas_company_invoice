<?php 
include 'layouts/session.php'; 
include '../config/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "Invalid expense ID.";
    $_SESSION['message_type'] = "danger";
    header("Location: expenses.php");
    exit();
}

$expenseId = intval($_GET['id']);

// Fetch single expense with category name
$expenseQuery = mysqli_query($conn, "
    SELECT 
        e.id, 
        e.title,
        e.ecategory_id,
        c.name AS category_name, 
        e.client_id,
        cl.first_name,
        e.invoice_id, 
        e.date, 
        e.amount, 
        e.description
    FROM expenses e
    LEFT JOIN client cl ON e.client_id = cl.id
    LEFT JOIN expense_category c ON e.ecategory_id = c.id
    WHERE e.is_deleted = 0 AND e.id = $expenseId
");

if (mysqli_num_rows($expenseQuery) == 0) {
    $_SESSION['message'] = "Expense not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: expenses.php");
    exit();
}

$expense = mysqli_fetch_assoc($expenseQuery);

// Fetch related documents
$documents = mysqli_query($conn, "SELECT * FROM expense_document WHERE expense_id = $expenseId");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layouts/title-meta.php'; ?> 
    <?php include 'layouts/head-css.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="page-wrapper">
        <div class="content">

            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h4 class="page-title">Expense Details</h4>
                    </div>
                </div>
            </div>

            <div class="card p-3">
                <table class="table table-bordered">
                    <tr><th>Title</th><td><?= $expense['title'] ?></td></tr>
                    <tr><th>Category</th><td><?= $expense['category_name'] ?></td></tr>
                    <tr><th>Client</th><td><?= $expense['first_name']?></td></tr>
                    <tr><th>Invoice</th><td><?= $expense['invoice_id'] ?></td></tr>
                    <tr><th>Date</th><td><?= date('d-m-Y', strtotime($expense['date'])) ?></td></tr>
                    <tr><th>Amount</th><td><?= number_format($expense['amount'], 2) ?></td></tr>
                    <tr><th>Description</th><td><?= nl2br($expense['description']) ?></td></tr>
                </table>
            </div>

            <div class="card p-3 mt-3">
                <h5>Attached Documents</h5>
                <?php if (mysqli_num_rows($documents) > 0): ?>
                    <ul>
                        <?php while ($doc = mysqli_fetch_assoc($documents)): ?>
                            <li>
                                <a href="../uploads/<?= $doc['document'] ?>" target="_blank">
                                    <?= $doc['document'] ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No documents uploaded for this expense.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
<?php include 'layouts/vendor-scripts.php'; ?>
</body>
</html>
