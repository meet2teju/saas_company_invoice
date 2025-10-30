<?php include 'layouts/session.php'; ?>

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
                        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div><h6>Roles & Permission</h6></div>
                    
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                        <div class="table-search d-flex align-items-center mb-0">
                            <div class="search-input">
                                <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                            </div>
                            
                            <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                                <i class="fa-regular fa-trash-can me-1"></i>Delete
                            </a>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                <i class="isax isax-export-1 me-1"></i>Export
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="process/export_excle_userrole.php">Download as Excel</a></li>
                            </ul>
                        </div>
                        
                      
                        
                        <?php if (check_is_access_new("add_role") == 1) { ?> 
                        <div>
                            <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_modal">
                                <i class="isax isax-add-circle5 me-1"></i>New Role
                            </a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <!-- <div class="table-search d-flex align-items-center mb-0">
                        <div class="search-input">
                            <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                        </div>
                         
                        <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                            <i class="fa-regular fa-trash-can me-1"></i>Delete
                        </a>
                    </div> -->
                     
                    <table class="table table-nowrap datatable">
                        <thead class="table-light">
                            <tr>
                                <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                    </div>
                                </th>
                                <th>Role</th>
                                <th>Create On</th>
                                <th class="no-sort">Permission</th>
                                <th class="no-sort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include '../config/config.php';
                            $query = "SELECT * FROM user_role WHERE is_deleted = 0 ORDER BY id asc";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input type="checkbox" class="form-check-input role-checkbox" name="role_ids[]" value="<?= $row['id'] ?>">
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
                                <td><?= !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '' ?></td>
                                <td>
                                    <a href="permission.php?id=<?=$row['id']; ?>" class="btn btn-outline-white d-inline-flex align-items-center">Permission</a>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php if (check_is_access_new("edit_role") == 1) { ?>
                                        <li>
                                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">
                                                <i class="isax isax-edit me-2"></i>Edit
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <?php if (check_is_access_new("delete_role") == 1) { ?>
                                        <li>
                                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id'] ?>">
                                                <i class="isax isax-trash me-2"></i>Delete
                                            </a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </td>
                            </tr>
                            
                            <!-- Edit Modal -->
                            <div id="editModal<?= $row['id'] ?>" class="modal fade">
                                <div class="modal-dialog modal-dialog-centered modal-md">
                                    <div class="modal-content">
                                        <form action="process/action_edit_userrole.php" method="POST">
                                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Edit Role</h4>
                                                <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal"><i class="fa-solid fa-x"></i></button>
                                            </div>
                                            <div class="modal-body">
                                                <label class="form-label">Role Name<span class="text-danger ms-1">*</span></label>
                                                <input type="text" name="name" class="form-control" id="edit_role_name_<?= $row['id'] ?>" value="<?= htmlspecialchars($row['name']) ?>">
                                                <span class="text-danger error-msg" id="edit_role_name_error_<?= $row['id'] ?>"></span>
                                            </div>
                                            <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                                                <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-m">
                                    <div class="modal-content">
                                        <form method="POST" action="process/action_delete_userrole.php">
                                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                            <div class="modal-body text-center">
                                                <div class="mb-3">
                                                    <img src="assets/img/icons/delete.svg" alt="img">
                                                </div>
                                                <h6 class="mb-1">Delete Role</h6>
                                                <p class="mb-3">Are you sure, you want to delete this role?</p>
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
    
    <!-- Add Role Modal -->
    <div id="add_modal" class="modal fade">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Role</h4>
                    <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal"><i class="fa-solid fa-x"></i></button>
                </div>
                <form action="process/action_add_role.php" method="POST">
                    <div class="modal-body">
                        <label class="form-label">Role Name<span class="text-danger ms-1">*</span></label>
                        <input type="text" name="name" class="form-control" id="add_role_name">
                        <span class="text-danger error-msg" id="add_role_name_error"></span>
                    </div>
                    <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                        <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Multiple Delete Modal -->
    <div class="modal fade" id="multiDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-m">
            <div class="modal-content">
                <form method="POST" id="multiDeleteForm" action="process/action_multi_delete_role.php">
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <img src="assets/img/icons/delete.svg" alt="img">
                        </div>
                        <h6 class="mb-1">Delete Roles</h6>
                        <p class="mb-3">Are you sure you want to delete the selected roles?</p>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Yes, Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'layouts/vendor-scripts.php'; ?>
    
    <script>
        // Multiple Delete Functionality
        const multiDeleteModal = new bootstrap.Modal(document.getElementById('multiDeleteModal'));
        const deleteBtn = document.querySelector('.delete-multiple');
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.role-checkbox');

        // Function to toggle delete button visibility
        function toggleDeleteBtn() {
            const anyChecked = document.querySelectorAll('.role-checkbox:checked').length > 0;
            deleteBtn.classList.toggle('d-none', !anyChecked);
        }

        // Show modal on delete button click
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const selectedCheckboxes = document.querySelectorAll('.role-checkbox:checked');
            const form = document.getElementById('multiDeleteForm');

            // Remove previous hidden inputs
            form.querySelectorAll('input[name="role_ids[]"]').forEach(el => el.remove());

            // Add new hidden inputs
            selectedCheckboxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'role_ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });

            // Update modal text based on selection count
            const modalTitle = document.querySelector('#multiDeleteModal h6');
            const modalMessage = document.querySelector('#multiDeleteModal p');

            if (selectedCheckboxes.length === 1) {
                modalTitle.textContent = 'Delete Role';
                modalMessage.textContent = 'Are you sure you want to delete the selected role?';
            } else {
                modalTitle.textContent = 'Delete Roles';
                modalMessage.textContent = `Are you sure you want to delete the ${selectedCheckboxes.length} selected roles?`;
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

        // Existing validation code
        $(document).on('input', '#add_role_name, [id^="edit_role_name_"]', function () {
            this.value = this.value.replace(/[0-9]/g, '');
        });

        document.addEventListener("DOMContentLoaded", () => {
            // Add Role Validation
            document.querySelector('#add_modal form').addEventListener("submit", function(e) {
                let roleName = document.getElementById("add_role_name").value.trim();
                let errorEl = document.getElementById("add_role_name_error");
                errorEl.textContent = "";

                if (roleName === "") {
                    e.preventDefault();
                    errorEl.textContent = "Role name is required";
                }
            });

            // Edit Role Validation (multiple modals)
            document.querySelectorAll('[id^="editModal"]').forEach(modal => {
                modal.querySelector("form").addEventListener("submit", function(e) {
                    let input = modal.querySelector("input[name='name']");
                    let roleName = input.value.trim();
                    let errorEl = modal.querySelector(".error-msg");
                    errorEl.textContent = "";

                    if (roleName === "") {
                        e.preventDefault();
                        errorEl.textContent = "Role name is required";
                    }
                });
            });
        });

        // Reset Add Role modal on close
        $('#add_modal').on('hidden.bs.modal', function () {
            let form = $(this).find('form')[0];
            form.reset();
            $(this).find('.error-msg').text('');
            $(this).find('.form-control').removeClass('is-invalid');
        });

        // Reset Edit Role modals on close
        $('[id^="editModal"]').on('hidden.bs.modal', function () {
            let form = $(this).find('form')[0];
            form.reset();
            $(this).find('.error-msg').text('');
            $(this).find('.form-control').removeClass('is-invalid');
        });
    </script>
</body>
</html>