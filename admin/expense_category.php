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
            <div class="content content-two">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                        <?= $_SESSION['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h6>Expense Category</h6>
                    </div>

                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                        <div class="table-search d-flex align-items-center mb-0">
                            <div class="search-input position-relative">
                                <a href="javascript:void(0);" class="btn-searchset">
                                    <i class="isax isax-search-normal fs-12"></i>
                                </a>
                            </div>
                        </div>
                        <a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_modal">
                            <i class="isax isax-add-circle5 me-1"></i>New Expense Category
                        </a>
                    </div>
                </div>

                <!-- Table Search -->
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                                <i class="fa-regular fa-trash-can me-1"></i>Delete
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Table List -->
                <div class="table-responsive">
                    <table class="table table-nowrap datatable">
                        <thead class="thead-light">
                            <tr>
                                <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                    </div>
                                </th>
                                <th>Name</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th class="no-sort">Status</th>
                                <th class="no-sort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($conn, "
                                SELECT ec.*, 
                                       uc.name as created_name,
                                       uu.name as updated_name
                                FROM expense_category ec
                                LEFT JOIN login uc ON uc.id = ec.created_by
                                LEFT JOIN login uu ON uu.id = ec.updated_by
                                WHERE ec.is_deleted = 0
                                ORDER BY ec.created_at DESC
                            ");

                            while ($row = mysqli_fetch_assoc($result)) {
                                $status = $row['status'] ? 'checked' : ''; // Use status column instead of is_deleted
                            ?>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input expense-category-checkbox" type="checkbox" value="<?= $row['id'] ?>">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);"><?= htmlspecialchars($row['name']) ?></a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td><?= date('M d, Y H:i', strtotime($row['created_at'])) ?></td>
                                <td><?= $row['updated_at'] ? date('M d, Y H:i', strtotime($row['updated_at'])) : 'Never' ?></td>
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
                                        <li>
                                            <a href="#" class="dropdown-item edit-btn" 
                                               data-id="<?= $row['id'] ?>"
                                               data-name="<?= htmlspecialchars($row['name']) ?>">
                                               <i class="isax isax-edit me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-item delete-btn" 
                                               data-id="<?= $row['id'] ?>">
                                               <i class="isax isax-trash me-2"></i>Delete
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

            <?php include 'layouts/footer.php'; ?>
        </div>

        <!-- Add Modal -->
        <div id="add_modal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Expense Category</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="addForm" method="POST" action="process/action_add_expense_category.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name<span class="text-danger ms-1">*</span></label>
                                <input type="text" name="name" id="add_name" class="form-control">
                                <span class="text-danger error-msg" id="add_name_error"></span>
                            </div>
                        </div>
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_expense_category" class="btn btn-primary">Add New</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="edit_modal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Expense Category</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editForm" method="POST" action="process/action_edit_expense_category.php">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name<span class="text-danger ms-1">*</span></label>
                                <input type="text" name="name" id="edit_name" class="form-control">
                                <span class="text-danger error-msg" id="edit_name_error"></span>
                            </div>
                        </div>
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="edit_expense_category" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div id="delete_modal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form id="deleteForm" method="GET" action="process/action_delete_expense_category.php">
                        <input type="hidden" name="id" id="delete_id">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Expense Category</h6>
                            <p class="mb-3">Are you sure you want to delete this expense category?</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Multi Delete Modal -->
        <div class="modal fade" id="multideleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form method="POST" id="multiDeleteForm" action="process/action_multi_delete_expense_category.php">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Expense Categories</h6>
                            <p class="mb-3">Are you sure you want to delete the selected expense categories?</p>
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
    $(document).ready(function() {
        // Add form validation
        $('#addForm').on('submit', function(e) {
            let valid = true;
            
            // Reset errors
            $('#add_name_error').text('');
            
            // Validate name
            if ($('#add_name').val().trim() === '') {
                $('#add_name_error').text('Name is required');
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            $(this).find('[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Adding...');
            
            return true;
        });

        // Edit form validation
        $('#editForm').on('submit', function(e) {
            let valid = true;
            
            // Reset errors
            $('#edit_name_error').text('');
            
            // Validate name
            if ($('#edit_name').val().trim() === '') {
                $('#edit_name_error').text('Name is required');
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            $(this).find('[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Saving...');
            
            return true;
        });

        // Handle edit button click to populate modal
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();

            const id = $(this).data('id');
            const name = $(this).data('name');

            // Set values
            $('#edit_id').val(id);
            $('#edit_name').val(name);

            // Clear previous error messages
            $('#edit_name_error').text('');

            // Open modal
            $('#edit_modal').modal('show');
        });

        // Handle delete button click
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#delete_id').val(id);
            $('#delete_modal').modal('show');
        });

        // Reset forms when modals are closed
        $('#add_modal').on('hidden.bs.modal', function() {
            $('#addForm')[0].reset();
            $('#add_name_error').text('');
            $('#addForm').find('[type="submit"]').prop('disabled', false).text('Add New');
        });

        $('#edit_modal').on('hidden.bs.modal', function() {
            $('#edit_name_error').text('');
            $('#editForm').find('[type="submit"]').prop('disabled', false).text('Save Changes');
        });

        // Multi-delete functionality
        const multiDeleteModal = new bootstrap.Modal(document.getElementById('multideleteModal'));

        // Toggle delete button visibility
        function toggleDeleteBtn() {
            if ($('.expense-category-checkbox:checked').length > 0) {
                $('.delete-multiple').removeClass('d-none');
            } else {
                $('.delete-multiple').addClass('d-none');
            }
        }

        // Delete button click
        $('.delete-multiple').on('click', function(e) {
            e.preventDefault();
            const checkboxes = $('.expense-category-checkbox:checked');
            const form = $('#multiDeleteForm');

            // Clear old hidden inputs
            form.find('input[name="expense_category_ids[]"]').remove();

            // Add selected ids
            checkboxes.each(function() {
                form.append(`<input type="hidden" name="expense_category_ids[]" value="${$(this).val()}">`);
            });

            // Update modal text
            const modalTitle = $('#multideleteModal h6');
            const modalMessage = $('#multideleteModal p');

            if (checkboxes.length === 1) {
                modalTitle.text('Delete Expense Category');
                modalMessage.text('Are you sure you want to delete the selected expense category?');
            } else {
                modalTitle.text('Delete Expense Categories');
                modalMessage.text(`Are you sure you want to delete the ${checkboxes.length} selected expense categories?`);
            }

            multiDeleteModal.show();
        });

        // Select All functionality
        $('#select-all').on('change', function() {
            $('.expense-category-checkbox').prop('checked', $(this).prop('checked'));
            toggleDeleteBtn();
        });

        // Individual checkbox change
        $(document).on('change', '.expense-category-checkbox', function() {
            toggleDeleteBtn();
        });

        // Run once on page load
        toggleDeleteBtn();
    });
    </script>

    <!-- Status Toggle Script -->
    <script>
    $(document).ready(function() {
        $('.status-toggle').on('change', function() {
            var id = $(this).data('id');
            var status = $(this).is(':checked') ? 1 : 0; // 1 = Active, 0 = Inactive

            $.ajax({
                url: 'process/action_toggle_expense_category_status.php',
                type: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function(response) {
                    console.log('Status updated successfully');
                    // Optional: Show success message
                    // You can add a toast notification here if needed
                },
                error: function(xhr, status, error) {
                    console.error('Error updating status:', error);
                    // Revert the toggle on error
                    $(this).prop('checked', !$(this).prop('checked'));
                }
            });
        });
    });
    </script>
</body>
</html>