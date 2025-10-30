<?php include 'layouts/session.php'; ?>
<?php include '../config/config.php'; ?>

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
            <div class="content">
                <!-- Display messages -->
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>

                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3">
                    <h6 class="mb-0">Tax Rates</h6>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                        <div class="table-search d-flex align-items-center mb-0">
                            <div class="search-input">
                                <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                            </div>

                            <div class="d-flex align-items-center flex-wrap gap-4">
                                
                                <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                                    <i class="fa-regular fa-trash-can me-1"></i>Delete
                                </a>
                            </div>
                        </div>
                        <?php if (check_is_access_new("add_tax") == 1) { ?>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add_tax_rates" class="btn btn-primary d-flex align-items-center">
                                <i class="isax isax-add-circle5 me-2"></i>New Tax Rate
                            </a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <!-- <div class="table-search d-flex align-items-center mb-0">
                                <div class="search-input">
                                    <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                                </div>

                                <div class="d-flex align-items-center flex-wrap gap-4">
                                    
                                    <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                                        <i class="fa-regular fa-trash-can me-1"></i>Delete
                                    </a>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-nowrap datatable">
                        <thead class="table-light">
                            <tr>
                                <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                    </div>
                                </th>
                                <th>Name</th>
                                <th>Tax Rate</th>
                                <th>Created On</th>
                                <th class="no-sort">Status</th>
                                <th class="no-sort">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM tax ORDER BY created_at DESC";
                            $result = mysqli_query($conn, $query);
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status = $row['status'] ? 'checked' : '';
                                $created_at = date('d M Y', strtotime($row['created_at']));
                            ?>
                            <tr data-id="<?= $row['id'] ?>">
                                <td>
                                    <div class="form-check form-check-md">
                                        <input type="checkbox" class="form-check-input tax-checkbox" name="tax_ids[]" value="<?= $row['id'] ?>">
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['rate']) ?>%</td>
                                <td><?= $created_at ?></td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" type="checkbox" role="switch" <?= $status ?> data-id="<?= $row['id'] ?>">
                                    </div>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php if (check_is_access_new("edit_tax") == 1) { ?>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item edit-tax-rate" 
                                            data-id="<?= $row['id'] ?>"
                                            data-name="<?= htmlspecialchars($row['name']) ?>"
                                            data-rate="<?= htmlspecialchars($row['rate']) ?>">
                                            <i class="isax isax-edit me-2"></i>Edit
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <?php if (check_is_access_new("delete_tax") == 1) { ?>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item delete-tax-rate" 
                                            data-id="<?= $row['id'] ?>">
                                            <i class="isax isax-trash me-2"></i>Delete
                                            </a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php include 'layouts/footer.php'; ?>
        </div>

        <!-- Add Tax Rate Modal -->
        <div id="add_tax_rates" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Tax Rate</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="addTaxRateForm" method="POST" action="process/action_add_tax.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Tax Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="tax_name" class="form-control" >
                                <div class="invalid-feedback" id="tax_name_error"></div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Tax Rate (%) <span class="text-danger">*</span></label>
                                <input type="number" name="rate" id="tax_rate" class="form-control" step="0.01" >
                                <div class="invalid-feedback" id="tax_rate_error"></div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add New</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Tax Rate Modal -->
        <div id="edit_tax_rates" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Tax Rate</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editTaxRateForm" method="POST" action="process/action_edit_tax.php">
                        <input type="hidden" name="id" id="edit_tax_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Tax Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit_tax_name" class="form-control" >
                                <div class="invalid-feedback" id="edit_tax_name_error"></div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Tax Rate (%) <span class="text-danger">*</span></label>
                                <input type="number" name="rate" id="edit_tax_rate" class="form-control" step="0.01" >
                                <div class="invalid-feedback" id="edit_tax_rate_error"></div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Tax Rate Modal -->
        <div class="modal fade" id="delete_tax_rates">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form id="deleteTaxRateForm" method="POST" action="process/action_delete_tax.php">
                        <input type="hidden" name="id" id="delete_tax_id">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Tax Rate</h6>
                            <p class="mb-3">Are you sure you want to delete this tax rate?</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Multiple Delete Tax Rate Modal -->
        <div class="modal fade" id="multiDeleteModal">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form id="multiDeleteForm" method="POST" action="process/action_multi_delete_tax.php">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Tax Rates</h6>
                            <p class="mb-3">Are you sure you want to delete the selected tax rates?</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layouts/vendor-scripts.php'; ?>

    <script>
         $('#tax_name,#edit_tax_name').on('input', function () {
        this.value = this.value.replace(/[0-9]/g, '');
    });
    $(document).ready(function() {
        // Multiple Delete Functionality
        const multiDeleteModal = new bootstrap.Modal(document.getElementById('multiDeleteModal'));
        const deleteBtn = document.querySelector('.delete-multiple');
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.tax-checkbox');

        // Function to toggle delete button visibility
        function toggleDeleteBtn() {
            const anyChecked = document.querySelectorAll('.tax-checkbox:checked').length > 0;
            deleteBtn.classList.toggle('d-none', !anyChecked);
        }

        // Show modal on delete button click
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const selectedCheckboxes = document.querySelectorAll('.tax-checkbox:checked');
            const form = document.getElementById('multiDeleteForm');

            // Remove previous hidden inputs
            form.querySelectorAll('input[name="tax_ids[]"]').forEach(el => el.remove());

            // Add new hidden inputs
            selectedCheckboxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'tax_ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });

            // Update modal text based on selection count
            const modalTitle = document.querySelector('#multiDeleteModal h6');
            const modalMessage = document.querySelector('#multiDeleteModal p');

            if (selectedCheckboxes.length === 1) {
                modalTitle.textContent = 'Delete Tax Rate';
                modalMessage.textContent = 'Are you sure you want to delete the selected tax rate?';
            } else {
                modalTitle.textContent = 'Delete Tax Rates';
                modalMessage.textContent = `Are you sure you want to delete the ${selectedCheckboxes.length} selected tax rates?`;
            }

            multiDeleteModal.show();
        });

        // Select All functionality
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            toggleDeleteBtn();
        });

        // Individual checkbox change
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', toggleDeleteBtn);
        });

        // Initialize button visibility on page load
        toggleDeleteBtn();

        // Search functionality
        $('#searchInput').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#taxRatesTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Handle edit button click
        $(document).on('click', '.edit-tax-rate', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const rate = $(this).data('rate');
            
            $('#edit_tax_id').val(id);
            $('#edit_tax_name').val(name);
            $('#edit_tax_rate').val(rate);
            
            $('#edit_tax_rates').modal('show');
        });

        // Handle delete button click
        $(document).on('click', '.delete-tax-rate', function() {
            const id = $(this).data('id');
            $('#delete_tax_id').val(id);
            $('#delete_tax_rates').modal('show');
        });

        // Status toggle
        $(document).on('change', '.status-toggle', function() {
            const id = $(this).data('id');
            const status = $(this).is(':checked') ? 1 : 0;
            
            $.ajax({
                url: 'process/action_toggle_tax_status.php',
                type: 'POST',
                data: { id: id, status: status },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        toastr.success(result.message);
                    } else {
                        toastr.error(result.message);
                        // Revert the toggle if failed
                        $(this).prop('checked', !status);
                    }
                },
                error: function() {
                    toastr.error('An error occurred while updating status');
                    $(this).prop('checked', !status);
                }
            });
        });

        // Add form validation
        $('#addTaxRateForm').on('submit', function(e) {
            let valid = true;
            
            // Reset validation
            $('#tax_name, #tax_rate').removeClass('is-invalid');
            $('#tax_name_error, #tax_rate_error').text('');
            
            // Validate name
            if ($('#tax_name').val().trim() === '') {
                $('#tax_name').addClass('is-invalid');
                $('#tax_name_error').text('Tax name is required');
                valid = false;
            }
            
            // Validate rate
            const rate = parseFloat($('#tax_rate').val());
            if (isNaN(rate) || rate < 0 || rate > 100) {
                $('#tax_rate').addClass('is-invalid');
                $('#tax_rate_error').text('Please enter a valid rate between 0 and 100');
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            $(this).find('[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Adding...');
        });

        // Edit form validation
        $('#editTaxRateForm').on('submit', function(e) {
            let valid = true;
            
            // Reset validation
            $('#edit_tax_name, #edit_tax_rate').removeClass('is-invalid');
            $('#edit_tax_name_error, #edit_tax_rate_error').text('');
            
            // Validate name
            if ($('#edit_tax_name').val().trim() === '') {
                $('#edit_tax_name').addClass('is-invalid');
                $('#edit_tax_name_error').text('Tax name is required');
                valid = false;
            }
            
            // Validate rate
            const rate = parseFloat($('#edit_tax_rate').val());
            if (isNaN(rate) || rate < 0 || rate > 100) {
                $('#edit_tax_rate').addClass('is-invalid');
                $('#edit_tax_rate_error').text('Please enter a valid rate between 0 and 100');
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            $(this).find('[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Saving...');
        });

// Real-time validation for Add Tax Rate
$('#tax_rate').on('input', function () {
    const rate = parseFloat($(this).val());
    if (isNaN(rate) || rate < 0 || rate > 100) {
        $('#tax_rate').addClass('is-invalid');
        $('#tax_rate_error').text('Tax rate must be between 0 and 100');
    } else {
        $('#tax_rate').removeClass('is-invalid');
        $('#tax_rate_error').text('');
    }
});

// Real-time validation for Edit Tax Rate
$('#edit_tax_rate').on('input', function () {
    const rate = parseFloat($(this).val());
    if (isNaN(rate) || rate < 0 || rate > 100) {
        $('#edit_tax_rate').addClass('is-invalid');
        $('#edit_tax_rate_error').text('Tax rate must be between 0 and 100');
    } else {
        $('#edit_tax_rate').removeClass('is-invalid');
        $('#edit_tax_rate_error').text('');
    }
});

        
        $('#add_tax_rates').on('hidden.bs.modal', function() {
    const form = $('#addTaxRateForm');
    form[0].reset();
    form.find('.is-invalid').removeClass('is-invalid'); // clear error borders
    form.find('.invalid-feedback').text(''); // clear error messages
    form.find('[type="submit"]').prop('disabled', false).text('Add New');
});

$('#edit_tax_rates').on('hidden.bs.modal', function() {
    const form = $('#editTaxRateForm');
    form[0].reset();
    form.find('.is-invalid').removeClass('is-invalid'); // clear error borders
    form.find('.invalid-feedback').text(''); // clear error messages
    form.find('[type="submit"]').prop('disabled', false).text('Save Changes');
});

    });
    </script>
         <script>
$(document).ready(function() {
    $('.status-toggle').on('change', function() {
        var id = $(this).data('id');
        var status = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: 'process/action_toggle_tax_status.php',
            type: 'POST',
            data: {
                id: id,
                status: status
            },
            success: function(response) {
                console.log('Status updated');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
});
</script>
<?php if (isset($_GET['open']) && $_GET['open'] === 'add_tax_rates') { ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var myModal = new bootstrap.Modal(document.getElementById('add_tax_rates'));
        myModal.show();
    });
</script>
<?php } ?>
</body>
</html>