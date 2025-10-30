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
                        <h6>Units</h6>
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
                            <i class="isax isax-add-circle5 me-1"></i>New Unit
                        </a>
                    </div>
                </div>

                <!-- Search -->
             <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <!-- Search Input -->
                            <!-- <div class="table-search d-flex align-items-center mb-0">
                                <div class="search-input position-relative">
                                    <a href="javascript:void(0);" class="btn-searchset">
                                        <i class="isax isax-search-normal fs-12"></i>
                                    </a>
                                </div>
                            </div> -->

                            <!-- Delete Button -->
                             <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                        <i class="fa-regular fa-trash-can me-1"></i>Delete
                        </a>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-nowrap datatable">
                        <thead class="table-light">
                            <tr>
                                <th class="no-sort">
                            <div class="form-check form-check-md">
                                <input class="form-check-input" type="checkbox" id="select-all">
                            </div>
                        </th>
                                <th>Unit Name</th>
                                <th class>Short Name</th>
                                <th class="no-sort">Status</th>
                                <th class="no-sort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($conn, "SELECT * FROM units ORDER BY id DESC");
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status = $row['status'] ? 'checked' : '';
                            ?>
                                <tr>
                                      <td>
                                <div class="form-check form-check-md">
                            <input type="checkbox" class="form-check-input units-checkbox" name="units_ids[]" value="<?= htmlspecialchars($row['id']) ?>">
                                </div>
                            </td>
                                    <td><h6 class="fs-14 fw-medium mb-0"><?= htmlspecialchars($row['name']) ?></h6></td>
                                    <td><?= htmlspecialchars($row['short_name']) ?></td>
                                    <td>
                                      <div class="form-check form-switch">
                                    <input class="form-check-input status-toggle" type="checkbox" role="switch" <?= $status ? 'checked' : '' ?> data-id="<?= $row['id'] ?>">
                                </div>
                                    <td class="action-item">
                                        <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                            <i class="isax isax-more"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="#" class="dropdown-item edit-btn" 
                                                   data-id="<?= $row['id'] ?>"
                                                   data-name="<?= htmlspecialchars($row['name']) ?>"
                                                   data-short="<?= htmlspecialchars($row['short_name']) ?>">
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
                        <h4 class="modal-title">Add Unit</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="addForm" method="POST" action="process/action_productunit.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name<span class="text-danger ms-1">*</span></label>
                                <input type="text" name="name" id="add_name" class="form-control">
                                <span class="text-danger error-msg" id="add_name_error"></span>
                            </div>
                            <div>
                                <label class="form-label">Short Name<span class="text-danger ms-1">*</span></label>
                                <input type="text" name="short_name" id="add_short_name" class="form-control">
                                <span class="text-danger error-msg" id="add_short_name_error"></span>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_unit" class="btn btn-primary">Add New</button>
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
                        <h4 class="modal-title">Edit Unit</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editForm" method="POST" action="process/action_editproductunit.php">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name<span class="text-danger ms-1">*</span></label>
                                <input type="text" name="name" id="edit_name" class="form-control">
                                <span class="text-danger error-msg" id="edit_name_error"></span>
                            </div>
                            <div>
                                <label class="form-label">Short Name<span class="text-danger ms-1">*</span></label>
                                <input type="text" name="short_name" id="edit_short_name" class="form-control">
                                <span class="text-danger error-msg" id="edit_short_name_error"></span>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="edit_unit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div id="delete_modal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form id="deleteForm" method="GET" action="process/action_deleteproductunit.php">
                        <input type="hidden" name="id" id="delete_id">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Unit</h6>
                            <p class="mb-3">Are you sure you want to delete this unit?</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


          <div class="modal fade" id="multideleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form method="POST" id="multiDeleteForm" action="process/action_multi_delete_units.php">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Units</h6>
                            <p class="mb-3">Are you sure you want to delete the selected units?</p>
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
            $('#add_name,#add_short_name').on('input', function () {
        this.value = this.value.replace(/[0-9]/g, '');
            });
         $('#edit_name,#edit_short_name').on('input', function () {
        this.value = this.value.replace(/[0-9]/g, '');
            });
        // Add form validation
        $('#addForm').on('submit', function(e) {
            let valid = true;
            
            // Reset errors
            $('#add_name_error, #add_short_name_error').text('');
            
            // Validate name
            if ($('#add_name').val().trim() === '') {
                $('#add_name_error').text('Name is required');
                valid = false;
            }
            
            // Validate short name
            if ($('#add_short_name').val().trim() === '') {
                $('#add_short_name_error').text('Short name is required');
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });

        // Edit form validation
        $('#editForm').on('submit', function(e) {
            let valid = true;
            
            // Reset errors
            $('#edit_name_error, #edit_short_name_error').text('');
            
            // Validate name
            if ($('#edit_name').val().trim() === '') {
                $('#edit_name_error').text('Name is required');
                valid = false;
            }
            
            // Validate short name
            if ($('#edit_short_name').val().trim() === '') {
                $('#edit_short_name_error').text('Short name is required');
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });

        // Handle edit button click
        $(document).on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const short = $(this).data('short');
            
            $('#edit_id').val(id);
            $('#edit_name').val(name);
            $('#edit_short_name').val(short);
            
            // Clear previous errors
            $('#edit_name_error, #edit_short_name_error').text('');
            
            $('#edit_modal').modal('show');
        });

        // Handle delete button click
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            $('#delete_id').val(id);
            $('#delete_modal').modal('show');
        });

        // Reset add form when modal is closed
        $('#add_modal').on('hidden.bs.modal', function() {
            $('#addForm')[0].reset();
            $('#add_name_error, #add_short_name_error').text('');
        });

        // Reset edit form when modal is closed
        $('#edit_modal').on('hidden.bs.modal', function() {
            $('#edit_name_error, #edit_short_name_error').text('');
        });

        // Search filter for units
        $('#unitSearch').on('keyup', function () {
            const searchText = $(this).val().toLowerCase();

            $('#unitTable tbody tr').each(function () {
                const rowText = $(this).text().toLowerCase();
                $(this).toggle(rowText.indexOf(searchText) > -1);
            });
        });
   const multiDeleteModal = new bootstrap.Modal(document.getElementById('multideleteModal'));

// Toggle delete button visibility
function toggleDeleteBtn() {
    if ($('.units-checkbox:checked').length > 0) {
        $('.delete-multiple').removeClass('d-none'); // show
    } else {
        $('.delete-multiple').addClass('d-none'); // hide
    }
}

// Delete button click
$('.delete-multiple').on('click', function(e) {
    e.preventDefault();
    const checkboxes = $('.units-checkbox:checked');
    const form = $('#multiDeleteForm');

    // Clear old hidden inputs
    form.find('input[name="units_ids[]"]').remove();

    // Add selected ids
    checkboxes.each(function() {
        form.append(`<input type="hidden" name="units_ids[]" value="${$(this).val()}">`);
    });

    // Update modal text
    const modalTitle = $('#multideleteModal h6');
    const modalMessage = $('#multideleteModal p');

    if (checkboxes.length === 1) {
        modalTitle.text('Delete Unit');
        modalMessage.text('Are you sure you want to delete the selected unit?');
    } else {
        modalTitle.text('Delete Units');
        modalMessage.text(`Are you sure you want to delete the ${checkboxes.length} selected units?`);
    }

    multiDeleteModal.show();
});

// Select All functionality
$('#select-all').on('change', function() {
    $('.units-checkbox').prop('checked', $(this).prop('checked'));
    toggleDeleteBtn();
});

// Individual checkbox change
$(document).on('change', '.units-checkbox', function() {
    toggleDeleteBtn();
});

// Run once on page load (in case some boxes are pre-checked)
toggleDeleteBtn();

    });
    
    </script>
               <script>
$(document).ready(function() {
    $('.status-toggle').on('change', function() {
        var id = $(this).data('id');
        var status = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: 'process/action_toggle_units_status.php',
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
</body>
</html>