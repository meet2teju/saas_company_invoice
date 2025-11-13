<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "Invalid expense ID.";
    $_SESSION['message_type'] = "danger";
    header("Location: expense.php");
    exit();
}

$expenseId = intval($_GET['id']);

// Fetch expense data
$expenseQuery = mysqli_query($conn, "SELECT * FROM expenses WHERE id = $expenseId");
if (mysqli_num_rows($expenseQuery) == 0) {
    $_SESSION['message'] = "Expense not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: expense.php");
    exit();
}
$expense = mysqli_fetch_assoc($expenseQuery);

// Fetch related documents
$documents = mysqli_query($conn, "SELECT * FROM expense_document WHERE expense_id = $expenseId");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <?php include 'layouts/title-meta.php'; ?> 
    <?php include 'layouts/head-css.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- <style>
        .error-text { color: red; font-size: 0.85rem; }
        .is-invalid { border-color: red !important; }
    </style> -->
</head>
<body>
<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6>Edit Expense</h6>
                            <!-- <a href="" class="btn btn-outline-white d-inline-flex align-items-center">
                                <i class="isax isax-eye me-1"></i>Preview
                            </a> -->
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <form action="process/action_edit_expense.php" method="POST" enctype="multipart/form-data" id="form">
                                    <input type="hidden" name="id" value="<?= $expense['id'] ?>">
                                    <div class="border-bottom mb-3 pb-1">
                                        <div class="row gx-3">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Expense Category <span class="text-danger">*</span></label>
                                                    <select class="form-select select2" name="ecategory_id" id="category_id">
                                                        <option value="">Select Category</option>
                                                        <?php
                                                        $catResult = mysqli_query($conn, "SELECT id, name FROM expense_category WHERE is_deleted = 0");
                                                        while ($row = mysqli_fetch_assoc($catResult)) {
                                                            $selected = ($expense['ecategory_id'] == $row['id']) ? 'selected' : '';
                                                            echo '<option value="' . $row['id'] . '" ' . $selected . '>' . htmlspecialchars($row['name']) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                    <span class="text-danger error-text" id="category_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="title" id="title" value="<?= htmlspecialchars($expense['title']) ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Client Name</label>
                                                    <select class="form-select select2" name="client_id" id="client_id">
                                                        <option value="">Select Client</option>
                                                        <?php
                                                        $clientResult = mysqli_query($conn, "SELECT id, first_name, last_name FROM client");
                                                        while ($row = mysqli_fetch_assoc($clientResult)) {
                                                            $selected = ($expense['client_id'] == $row['id']) ? 'selected' : '';
                                                            echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['first_name'] . ' ' . ($row['last_name'] ?? '') . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Invoice Number</label>
                                                    <input type="text" class="form-control" name="invoice_id" id="invoice_id" value="<?= htmlspecialchars($expense['invoice_id']) ?>" readonly>
                                                </div>
                                            </div> -->
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Expense Date <span class="text-danger">*</span></label>
                                                    <div class="input-group position-relative">
                                                        <input type="text" class="form-control datepicker" id="expense_date" name="date" value="<?= htmlspecialchars($expense['date']) ?>">
                                                        <span class="input-group-text"><i class="isax isax-calendar-2"></i></span>
                                                    </div>
                                                    <span class="text-danger error-text" id="expense_date_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Amount <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="amount" id="amount" value="<?= htmlspecialchars($expense['amount']) ?>">
                                                    <span class=" text-danger error-text" id="amount_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($expense['description']) ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Existing Documents</label>
                                                <ul>
                                                    <?php while ($doc = mysqli_fetch_assoc($documents)): ?>
                                                        <li id="doc-<?= $doc['id'] ?>">
                                                            <a href="../uploads/<?= htmlspecialchars($doc['document']) ?>" target="_blank"><?= htmlspecialchars($doc['document']) ?></a>
                                                            <div class="mt-1">
                                                                <label class="btn btn-sm btn-outline-primary btn-icon" title="Replace">
                                                                    <i class="bi bi-pencil"></i>
                                                                    <input type="file" class="d-none replace-doc" 
                                                                        data-doc-id="<?= $doc['id']; ?>" 
                                                                        data-old-name="<?= htmlspecialchars($doc['document']); ?>">
                                                                </label>
                                                                <button type="button" class="btn btn-sm btn-outline-danger btn-icon delete-doc" 
                                                                        data-doc-id="<?= $doc['id']; ?>">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </li>
                                                    <?php endwhile; ?>
                                                </ul>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Upload New Document</label>
                                                    <input type="file" class="form-control" name="document[]" id="expense_document" multiple>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <a href="expense.php" class="btn btn-outline-white">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Update</button>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>
</div>
<?php include 'layouts/vendor-scripts.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr(".datepicker", { dateFormat: "Y-m-d" });
</script>
  <script>
    $(document).ready(function () {
    // === Allow only numbers ===
    $('#amount').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

   
});

</script>
<script>
$(document).ready(function () {

    // Form validation
    $('#form').on('submit', function (e) {
        let isValid = true;

        // Reset errors
        $('.error-text').text('');
        $('.form-control, .form-select').removeClass('is-invalid');

        if ($('#category_id').val().trim() === '') {
            $('#category_error').text('Please select a category.');
            $('#category_id').addClass('is-invalid');
            isValid = false;
        }
        if ($('#expense_date').val().trim() === '') {
            $('#expense_date_error').text('Please select an expense date.');
            $('#expense_date').addClass('is-invalid');
            isValid = false;
        }
        if ($('#amount').val().trim() === '' || isNaN($('#amount').val())) {
            $('#amount_error').text('Please enter a valid amount.');
            $('#amount').addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    // Delete document
    $('.delete-doc').on('click', function () {
        const docId = $(this).data('doc-id');
        $.ajax({
            url: 'process/action_delete_expense_document.php',
            type: 'POST',
            data: { id: docId },
            success: function (response) {
                if (response.trim() === 'success') {
                    $('#doc-' + docId).remove();
                }
            }
        });
    });

    // Replace document
    $('.replace-doc').on('change', function () {
        const input = this;
        const file = input.files[0];
        if (!file) return;

        const allowed = ['pdf', 'xls', 'xlsx', 'csv', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        const ext = file.name.split('.').pop().toLowerCase();
        if (!allowed.includes(ext)) {
            alert("Invalid file type.");
            return;
        }

        const docId = $(input).data('doc-id');
        const oldName = $(input).data('old-name');

        const formData = new FormData();
        formData.append('doc_id', docId);
        formData.append('old_name', oldName);
        formData.append('new_file', file);

        $.ajax({
            url: 'process/action_edit_expense_document.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                const result = JSON.parse(res);
                if (result.status === 'success') {
                    const listItem = $('#doc-' + docId);
                    const link = listItem.find('a');
                    link.text(result.new_name);
                    link.attr('href', '../uploads/' + result.new_name);
                    listItem.find('.replace-doc').attr('data-old-name', result.new_name);
                }
            }
        });
    });

});
</script>
<script>
$(document).ready(function () {
    // When client changes, fetch invoice number
    $('#client_id').on('change', function () {
        var clientId = $(this).val();

        if (clientId !== '') {
            $.ajax({
                url: 'process/action_fetch_expense_invoice_number.php',
                type: 'POST',
                data: { client_id: clientId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#invoice_id').val(response.invoice_id);
                    } else {
                        $('#invoice_id').val('');
                    }
                }
            });
        } else {
            $('#invoice_id').val('');
        }
    });
});
</script>

</body>
</html>
