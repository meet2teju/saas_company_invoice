<?php
include 'layouts/session.php';
include '../config/config.php';

// Initialize filter conditions
$filterSql = "WHERE e.is_deleted = 0";

// Collect and sanitize POST parameters
$start_date = $end_date = '';
$selected_customers = [];
$selected_categories = [];
$date_range = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Date range filter
    if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
        $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
        $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
        $filterSql .= " AND DATE(e.date) BETWEEN '$start_date' AND '$end_date'";
        $date_range = $_POST['date_range'] ?? '';
    }

    // Customer filter
    if (!empty($_POST['customer']) && is_array($_POST['customer'])) {
        $selected_customers = array_map('intval', $_POST['customer']);
        $customer_ids_str = implode(',', $selected_customers);
        $filterSql .= " AND e.client_id IN ($customer_ids_str)";
    }

    // Expense category filter
    if (!empty($_POST['categories']) && is_array($_POST['categories'])) {
        $selected_categories = array_map('intval', $_POST['categories']);
        $category_ids_str = implode(',', $selected_categories);
        $filterSql .= " AND e.ecategory_id IN ($category_ids_str)";
    }
}

// Fetch clients for filter dropdown
$clientsResult = mysqli_query($conn, "SELECT id, first_name, customer_image FROM client WHERE is_deleted = 0 ORDER BY first_name ASC");

// Fetch categories for filter dropdown
$categoriesResult = mysqli_query($conn, "SELECT id, name FROM expense_category WHERE is_deleted = 0 ORDER BY name ASC");

// Fetch filtered expenses
$query = "
    SELECT 
        e.id, 
        c.name AS category_name, 
        e.date, 
        
        e.amount, 
        cl.first_name,
        cl.customer_image,
        e.description
    FROM expenses e
    LEFT JOIN expense_category c ON e.ecategory_id = c.id
    LEFT JOIN client cl ON e.client_id = cl.id
    $filterSql
    ORDER BY e.date DESC
";

$result = mysqli_query($conn, $query);
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

            <!-- Page Header -->
          <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>

            <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
                <div><h6>Expenses</h6></div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                    <?php if (check_is_access_new("add_expense") == 1) { ?> 
                    <div>
                        <a href="add-expense.php" class="btn btn-primary d-flex align-items-center">
                            <i class="isax isax-add-circle5 me-1"></i>New Expense
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>

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
                          <?php if (!empty($selected_customers) || !empty($selected_categories) || !empty($date_range)): ?>
                            <a href="expense.php" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                            </a>
                        <?php endif; ?>

                        <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                            <i class="fa-regular fa-trash-can me-1"></i>Delete
                        </a>

                    </div>
                </div>
            </div>

          

            <!-- Table -->
            <div class="table-responsive">
                    <table class="table table-nowrap datatable">
                        <thead class="thead-light">
                            <tr>
                                <th class="no-sort"><input class="form-check-input" type="checkbox" id="select-all"></th>
                                <th>Expense Category</th>
                                <th>Client</th>
                                <th>Date</th>
                                
                                <th>Amount</th>
                                <th>Notes</th>
                                <th class="no-sort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { 
                                 $clientImg = !empty($row['customer_image']) ? '../uploads/' . htmlspecialchars($row['customer_image']) : 'assets/img/users/user-16.jpg';
?>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input user-checkbox" value="<?= $row['id'] ?>"></td>
                                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                                    <td>
                                        <img src="<?= $clientImg ?>" alt="Customer" class="rounded-circle me-2" width="30" height="30">
                                        <?= htmlspecialchars($row['first_name']) ?>
                                    </td>                                    
                                    <td><?= date('d-m-Y', strtotime($row['date'])) ?></td>
                                    
                                    <td>$&nbsp;<?= number_format($row['amount'], 2) ?></td>
                                    <td style="white-space: normal; word-wrap: break-word;">
                                        <?= htmlspecialchars($row['description']) ?>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a href="#" data-bs-toggle="dropdown"><i class="isax isax-more"></i></a>
                                            <ul class="dropdown-menu">
                                                <?php if (check_is_access_new("view_expense") == 1) { ?>
                                                    <li><a href="expense-details.php?id=<?= $row['id'] ?>" class="dropdown-item"><i class="isax isax-eye"></i>&nbsp;&nbsp;&nbsp;View</a></li>
                                                <?php } ?>
                                                <?php if (check_is_access_new("edit_expense") == 1) { ?>
                                                    <li><a href="edit-expense.php?id=<?= $row['id'] ?>" class="dropdown-item"><i class="isax isax-edit me-2"></i>Edit</a></li>
                                                <?php } ?>
                                                <?php if (check_is_access_new("delete_expense") == 1) { ?>
                                                    <li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#delete_modal<?= $row['id'] ?>"><i class="isax isax-trash me-2"></i>Delete</a></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="delete_modal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST" action="process/action_delete_expense.php">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <div class="modal-body text-center">
                                                    <img src="assets/img/icons/delete.svg" alt="Delete Icon" class="mb-3">
                                                    <h6>Delete Expense</h6>
                                                    <p>Are you sure you want to delete this Expense?</p>
                                                    <div class="d-flex justify-content-center">
                                                        <button type="button" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Yes, Delete</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </tbody>
                    </table>

                                </div>
                            </div>

                            <?php include 'layouts/footer.php'; ?>
                        </div>
                    </div>
        <!-- Start Filter -->
        <div class="offcanvas offcanvas-offset offcanvas-end" tabindex="-1" id="customcanvas">
    <div class="offcanvas-header d-block pb-0">
        <div class="border-bottom d-flex align-items-center justify-content-between pb-3">
            <h6 class="offcanvas-title">Filter</h6>
            <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-x"></i></button>
        </div>
    </div>
    <div class="offcanvas-body pt-3">
        <form action="expense.php" method="post">
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
if (!empty($selectedClientNames)) {
    if (count($selectedClientNames) > 3) {
        $clientText = implode(", ", array_slice($selectedClientNames, 0, 3)) 
                    . " +" . (count($selectedClientNames) - 3);
    } else {
        $clientText = implode(", ", $selectedClientNames);
    }
} else {
    $clientText = "Select";
}
                ?>
                <div class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border customer-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
                        <?= $clientText ?>
                    </a>
                    <div class="dropdown-menu shadow-lg w-100 dropdown-info">                            
                        <div class="mb-3">
                            <div class="input-icon-start position-relative">
                                <span class="input-icon-addon fs-12"><i class="isax isax-search-normal"></i></span>
                                <input type="text" class="form-control form-control-sm search-customer" placeholder="Search">
                            </div>
                        </div>
                        <ul class="mb-3 customer-list">
                            <li class="d-flex align-items-center justify-content-between mb-3">
                                <label class="d-inline-flex align-items-center text-gray-9">
                                    <input class="form-check-input select-all m-0 me-2" type="checkbox" 
                                        <?= count($selected_customers) > 0 ? 'checked' : '' ?>>
                                    Select All
                                </label>
                                <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline reset-customer">Reset</a>
                            </li>
                            <?php 
                            mysqli_data_seek($clientsResult, 0);
                            while ($client = mysqli_fetch_assoc($clientsResult)) {
                                $checked = in_array($client['id'], $selected_customers) ? 'checked' : '';
                                $clientImg = !empty($client['customer_image']) ? '../uploads/' . $client['customer_image'] : 'assets/img/users/user-16.jpg';
                                echo '<li>
                                    <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                        <input class="form-check-input m-0 me-2 customer-checkbox" type="checkbox" name="customer[]" value="'.$client['id'].'" '.$checked.'>
                                        <span class="avatar avatar-sm rounded-circle me-2">
                                            <img src="'.$clientImg.'" class="flex-shrink-0 rounded-circle" width="24" height="24" alt="'.htmlspecialchars($client['first_name']).'">
                                        </span>
                                        '.htmlspecialchars($client['first_name']).'
                                    </label>
                                </li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Expense Categories -->
            <div class="mb-3">
                <label class="form-label">Expense Categories</label>
                <?php
                $selectedCategoryNames = [];
                if (!empty($selected_categories)) {
                    $ids = implode(",", array_map('intval', $selected_categories));
                    $res = mysqli_query($conn, "SELECT name FROM expense_category WHERE id IN ($ids)");
                    while ($row = mysqli_fetch_assoc($res)) {
                        $selectedCategoryNames[] = htmlspecialchars($row['name']);
                    }
                }
if (!empty($selectedCategoryNames)) {
    if (count($selectedCategoryNames) > 3) {
        $categoryText = implode(", ", array_slice($selectedCategoryNames, 0, 3)) 
                      . " +" . (count($selectedCategoryNames) - 3);
    } else {
        $categoryText = implode(", ", $selectedCategoryNames);
    }
} else {
    $categoryText = "Select";
}
                ?>
                <div class="dropdown">
                    <a href="javascript:void(0);" 
                       class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border category-toggle" 
                       data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <?= $categoryText ?>
                    </a>
                    <div class="dropdown-menu shadow-lg w-100 dropdown-info p-3">
                        <div class="mb-3">
                            <div class="input-icon-start position-relative">
                                <span class="input-icon-addon fs-12">
                                    <i class="isax isax-search-normal"></i>
                                </span>
                                <input type="text" class="form-control form-control-sm search-category" placeholder="Search Categories">
                            </div>
                        </div>
                        <ul class="mb-3 category-list list-unstyled">
                            <li class="d-flex align-items-center justify-content-between mb-3">
                                <label class="d-inline-flex align-items-center text-gray-9">
                                    <input class="form-check-input select-all m-0 me-2" type="checkbox" 
                                        <?= count($selected_categories) > 0 ? 'checked' : '' ?>> Select All
                                </label>
                                <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline reset-Expense">Reset</a>
                            </li>

                            <?php 
                            mysqli_data_seek($categoriesResult, 0);
                            while ($category = mysqli_fetch_assoc($categoriesResult)) {
                                $checked = in_array($category['id'], $selected_categories) ? 'checked' : '';
                                echo '<li>
                                    <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                        <input class="form-check-input m-0 me-2 category-checkbox" type="checkbox" name="categories[]" value="' . $category['id'] . '" ' . $checked . '>
                                        ' . htmlspecialchars($category['name']) . '
                                    </label>
                                </li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Date Range -->
            <div class="mb-3">
                <label class="form-label">Date Range</label>
                <div class="input-group position-relative">
                    <input type="text" class="form-control date-range bookingrange rounded-end" name="date_range" value="<?= $date_range ?>">
                    <input type="hidden" name="start_date" id="start_date" value="<?= $start_date ?>">
                    <input type="hidden" name="end_date" id="end_date" value="<?= $end_date ?>">
                    <span class="input-icon-addon fs-16 text-gray-9">
                        <i class="isax isax-calendar-2"></i>
                    </span>
                </div>
            </div>

             <div class="offcanvas-footer">
                        <div class="row g-2">
                            <div class="col-6"><a href="expense.php" class="btn btn-outline-white w-100">Reset</a></div>
                            <div class="col-6"><button type="submit" class="btn btn-primary w-100" id="filter-submit">Apply</button></div>
                        </div>
                    </div>
        </form>
    </div>
</div>
        <!-- End Filter -->
        <!-- Multi Delete Modal -->
        <div class="modal fade" id="multideleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form method="POST" id="multiDeleteForm" action="process/action_multi_delete_expense.php">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Expenses</h6>
                            <p class="mb-3">Are you sure you want to delete the selected Expenses?</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-white me-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

<?php include 'layouts/vendor-scripts.php'; ?>

<script>
    const multiDeleteModal = new bootstrap.Modal(document.getElementById('multideleteModal'));
const deleteBtn = document.querySelector('.delete-multiple');

// Toggle delete button visibility
function toggleDeleteBtn() {
    const anyChecked = document.querySelectorAll('.user-checkbox:checked').length > 0;
    deleteBtn.classList.toggle('d-none', !anyChecked);
}

// Delete button click — open modal directly
deleteBtn.addEventListener('click', function (e) {
    e.preventDefault();
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    const form = document.getElementById('multiDeleteForm');

    // Clear previous hidden inputs
    form.querySelectorAll('input[name="expense_ids[]"]').forEach(el => el.remove());

    // Add selected ids
    checkboxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'expense_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    // Update modal text
    const modalTitle = document.querySelector('#multideleteModal h6');
    const modalMessage = document.querySelector('#multideleteModal p');

    if (checkboxes.length === 1) {
        modalTitle.textContent = 'Delete Expense';
        modalMessage.textContent = 'Are you sure you want to delete the selected expense?';
    } else {
        modalTitle.textContent = 'Delete Expenses';
        modalMessage.textContent = `Are you sure you want to delete the ${checkboxes.length} selected expenses?`;
    }

    multiDeleteModal.show();
});

// Select All functionality
document.getElementById('select-all').addEventListener('change', function () {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    toggleDeleteBtn(); // update Delete button
});

// Individual checkbox change (works for dynamic or static checkboxes)
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('user-checkbox')) {
        toggleDeleteBtn();
    }
});

// Initial check on page load (in case some boxes are pre-checked)
toggleDeleteBtn();

</script>
<script>
$(document).ready(function() {
    // Initialize date range picker with preserved values
    if ($('.bookingrange').length > 0) {
        var start = '<?= $start_date ?>' ? moment('<?= $start_date ?>') : moment().subtract(6, 'days');
        var end = '<?= $end_date ?>' ? moment('<?= $end_date ?>') : moment();

        function booking_range(start, end) {
            $('.bookingrange').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
            $('#start_date').val(start.format('YYYY-MM-DD'));
            $('#end_date').val(end.format('YYYY-MM-DD'));
        }

        $('.bookingrange').daterangepicker({
            startDate: start,
            endDate: end,
            locale: { 
                format: 'MM/DD/YYYY', 
                cancelLabel: 'Clear',
                applyLabel: 'Apply',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, booking_range);

        booking_range(start, end);

        $('.bookingrange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#start_date').val('');
            $('#end_date').val('');
        });
    }

    // Update dropdown labels based on selected values
function updateDropdownLabels() {
    // Customers
    let customerLabels = [];
    $('.customer-checkbox:checked').each(function() {
        customerLabels.push($(this).closest('label').text().trim());
    });
    if (customerLabels.length > 3) {
        $('.customer-toggle').text(customerLabels.slice(0, 3).join(', ') + ' +' + (customerLabels.length - 3));
    } else {
        $('.customer-toggle').text(customerLabels.length > 0 ? customerLabels.join(', ') : 'Select');
    }

    // Categories
    let categoryLabels = [];
    $('.category-checkbox:checked').each(function() {
        categoryLabels.push($(this).closest('label').text().trim());
    });
    if (categoryLabels.length > 2) {
        $('.category-toggle').text(categoryLabels.slice(0, 2).join(', ') + ' +' + (categoryLabels.length - 3));
    } else {
        $('.category-toggle').text(categoryLabels.length > 0 ? categoryLabels.join(', ') : 'Select');
    }
}

    // Initialize dropdown labels on page load
    updateDropdownLabels();

    // Update dropdown labels when checkboxes change
    $('.customer-checkbox, .category-checkbox').change(function() {
        updateDropdownLabels();
        
        // Update "Select All" checkbox for each section
        if ($(this).hasClass('customer-checkbox')) {
            const allChecked = $('.customer-checkbox:not(:checked)').length === 0;
            $('.customer-list .select-all').prop('checked', allChecked);
        }
        
        if ($(this).hasClass('category-checkbox')) {
            const allChecked = $('.category-checkbox:not(:checked)').length === 0;
            $('.category-list .select-all').prop('checked', allChecked);
        }
    });

    // Select All functionality for customers
    $('.customer-list .select-all').change(function() {
        $('.customer-checkbox').prop('checked', this.checked);
        updateDropdownLabels();
    });

    // Select All functionality for categories
    $('.category-list .select-all').change(function() {
        $('.category-checkbox').prop('checked', this.checked);
        updateDropdownLabels();
    });

    // Reset functionality for customers
    $('.reset-customer').click(function() {
        $('.customer-checkbox, .customer-list .select-all').prop('checked', false);
        updateDropdownLabels();
    });

    // Reset functionality for categories
    $('.reset-Expense').click(function() {
        $('.category-checkbox, .category-list .select-all').prop('checked', false);
        updateDropdownLabels();
    });

    // Search functionality for customers
    $(".search-customer").on("keyup", function() {
        const value = $(this).val().toLowerCase();
        $(".customer-list li").each(function() {
            // Skip the first li (Select All and Reset)
            if ($(this).find('.select-all').length > 0) return;
            
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(value) > -1);
        });
    });

    // Search functionality for categories
    $(".search-category").on("keyup", function() {
        const value = $(this).val().toLowerCase();
        $(".category-list li").each(function() {
            // Skip the first li (Select All and Reset)
            if ($(this).find('.select-all').length > 0) return;
            
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(value) > -1);
        });
    });
});
</script>
</body>
</html>