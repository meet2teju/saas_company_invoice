<?php include 'layouts/session.php'; ?>
<?php include '../config/config.php'; ?>

<?php
// Get company currency (added this line)
$companyCurrency = getCompanyCurrency($conn);

// Get current user info
$currentUserId = $_SESSION['crm_user_id'] ?? 0;
$currentOrgId = $_SESSION['org_id'] ?? 0;
$userRoleId = $_SESSION['role_id'] ?? 0;

// Get the correct org_id from database if session org_id is 0
if ($currentOrgId == 0 && $currentUserId > 0) {
    $fixQuery = "SELECT org_id, role_id FROM login WHERE id = $currentUserId";
    $fixResult = mysqli_query($conn, $fixQuery);
    if ($fixResult && mysqli_num_rows($fixResult) > 0) {
        $userData = mysqli_fetch_assoc($fixResult);
        $_SESSION['org_id'] = $userData['org_id'];
        $_SESSION['role_id'] = $userData['role_id'];
        $currentOrgId = $userData['org_id'];
        $userRoleId = $userData['role_id'];
    }
}

$login_id = $_SESSION['crm_user_id']; // login.id
$role_id  = $_SESSION['role_id'];     // login.role_id

// Get role name from user_role
$role_query = mysqli_query($conn, "SELECT name FROM user_role WHERE id = $role_id LIMIT 1");
$role_row   = mysqli_fetch_assoc($role_query);
$role_name  = strtolower(trim($role_row['name'] ?? ''));

// Build the base query with organization filtering (Organization Isolation)
$query = "SELECT * FROM bank WHERE 1=1";

// Add organization filter - No one can see banks from other organizations
if ($currentOrgId > 0) {
    $query .= " AND org_id = $currentOrgId";
}

// User-based filtering - IMPLEMENTING THE THREE CONDITIONS
if ($userRoleId == 1) {
    // Admin users (role_id = 1): Can see all banks from their organization
    // No additional conditions needed as org filter already handles organization isolation
} else {
    // Non-admin users: Can see their own banks AND banks created by admin users
    $query .= " AND (
        created_by = $currentUserId 
        OR 
        created_by IN (
            SELECT id FROM login 
            WHERE org_id = $currentOrgId AND role_id = 1
        )
    )";
}

$query .= " ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layouts/title-meta.php'; ?> 
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 2px;
        }
        .action-item {
            position: relative;
        }
        .action-item .dropdown-menu {
            position: absolute;
            inset: 0px auto auto 0px;
            margin: 0px;
            transform: translate(-120px, 30px);
        }
        .form-check.form-switch {
            display: inline-block;
        }
        /* Add currency styling like in products page */
        .price-currency {
            display: inline-block;
            min-width: 20px;
            text-align: left;
        }
        /* Highlight duplicate error */
        .duplicate-error {
            border-color: #dc3545 !important;
            background-color: #fff5f5 !important;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="page-wrapper">
            <div class="content">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif;
                
                // Display duplicate error message if exists
                if (isset($_GET['error']) && $_GET['error'] == 'duplicate_account' && isset($_SESSION['old_bank_data'])): 
                ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Account number already exists!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h6 class="mb-0">Bank Details</h6>
                        <!-- Display current company currency -->
                        <!-- <small class="text-muted">
                            Company Currency: <?php echo $companyCurrency['currency_name'] . ' (' . $companyCurrency['currency_symbol'] . ')'; ?>
                        </small> -->
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                        <div class="table-search d-flex align-items-center mb-0">
                            <div class="search-input">
                                <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                            </div>
                          
                        </div>
                          <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                                <i class="fa-regular fa-trash-can me-1"></i>Delete
                            </a>
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add_bank_modal" class="btn btn-primary">
                            <i class="isax isax-add-circle5 me-2"></i>New Bank
                        </a>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <!-- <div class="table-search d-flex align-items-center mb-0">
                                <div class="search-input">
                                    <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                                </div>
                                <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                                    <i class="fa-regular fa-trash-can me-1"></i>Delete
                                </a>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="table-responsive table-nowrap pb-3 border-bottom">
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
                                <th>Bank Name</th>
                                <th>Account Holder</th>
                                <th>Account No.</th>
                                <th>IFSC Code</th>
                                <th>SWIFT Code</th>
                                <th>Opening Balance</th>
                                <th class="no-sort">Status</th>
                                <th class="no-sort">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status = $row['status'] ? 'checked' : '';
                                // Format opening balance with company currency symbol
                                $opening_balance = htmlspecialchars(number_format($row['opening_balance'], 2));
                            ?>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input type="checkbox" class="form-check-input bank-checkbox" name="bank_ids[]" value="<?= $row['id'] ?>">
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['bank_name']) ?></td>
                                <td><?= htmlspecialchars($row['account_holder']) ?></td>
                                <td><?= htmlspecialchars($row['account_number']) ?></td>
                                <td><?= htmlspecialchars($row['ifsc_code']) ?></td>
                                <td><?= htmlspecialchars($row['swift_code']) ?></td>
                                <!-- Updated Opening Balance Column -->
                                <td class="text-dark">
                                    <span class="price-currency"><?= $companyCurrency['currency_symbol'] ?></span>&nbsp;<?= $opening_balance ?>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" type="checkbox" role="switch" <?= $status ?> data-id="<?= $row['id'] ?>">
                                    </div>
                                </td>
                                <td class="action-item">
                                    <a href="#" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="#" class="dropdown-item edit-bank-btn" 
                                                data-id="<?= $row['id'] ?>"
                                                data-bank-name="<?= htmlspecialchars($row['bank_name']) ?>"
                                                data-account-holder="<?= htmlspecialchars($row['account_holder']) ?>"
                                                data-account-number="<?= htmlspecialchars($row['account_number']) ?>"
                                                data-routing-number="<?= htmlspecialchars($row['routing_number']) ?>" 
                                                data-ifsc-code="<?= htmlspecialchars($row['ifsc_code']) ?>"
                                                data-swift-code="<?= htmlspecialchars($row['swift_code']) ?>"
                                                data-opening-balance="<?= htmlspecialchars($row['opening_balance']) ?>">
                                                <i class="isax isax-edit me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-item" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal<?= $row['id']; ?>">
                                                <i class="isax isax-trash me-2"></i>Delete
                                            </a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="process/action_delete_bank.php">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <div class="modal-body text-center">
                                                <div class="mb-3"><img src="assets/img/icons/delete.svg" alt="delete"></div>
                                                <h6 class="mb-1">Delete Bank</h6>
                                                <p class="mb-3">Are you sure you want to delete this bank?</p>
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

        <!-- Add Bank Modal -->
        <div class="modal fade" id="add_bank_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Bank</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="add-bank-form" method="POST" action="process/action_add_bank.php">
                        <input type="hidden" name="org_id" value="<?php echo $currentOrgId; ?>">
                        <input type="hidden" name="created_by" value="<?php echo $currentUserId; ?>">
                        <div class="modal-body">
                            <div class="mb-2">
                                <label class="form-label">Bank Name<span class="text-danger">*</span></label>
                                <input type="text" id="bank_name" name="bank_name" class="form-control" 
                                    value="<?php echo isset($_SESSION['old_bank_data']['bank_name']) ? htmlspecialchars($_SESSION['old_bank_data']['bank_name']) : ''; ?>">
                                <div class="error-message" id="bank_name_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Account Holder Name<span class="text-danger">*</span></label>
                                <input type="text" id="account_holder" name="account_holder" class="form-control"
                                    value="<?php echo isset($_SESSION['old_bank_data']['account_holder']) ? htmlspecialchars($_SESSION['old_bank_data']['account_holder']) : ''; ?>">
                                <div class="error-message" id="account_holder_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Account Number<span class="text-danger">*</span></label>
                                <input type="text" id="account_number" name="account_number" class="form-control"
                                    value="<?php echo isset($_SESSION['old_bank_data']['account_number']) ? htmlspecialchars($_SESSION['old_bank_data']['account_number']) : ''; ?>">
                                <div class="error-message" id="account_number_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Routing Number</label>
                                <input type="text" id="routing_number" name="routing_number" class="form-control"
                                    value="<?php echo isset($_SESSION['old_bank_data']['routing_number']) ? htmlspecialchars($_SESSION['old_bank_data']['routing_number']) : ''; ?>">
                                <div class="error-message" id="routing_number_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">IFSC Code</label>
                                <input type="text" id="ifsc_code" name="ifsc_code" class="form-control"
                                    value="<?php echo isset($_SESSION['old_bank_data']['ifsc_code']) ? htmlspecialchars($_SESSION['old_bank_data']['ifsc_code']) : ''; ?>">
                                <div class="error-message" id="ifsc_code_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">SWIFT Code</label>
                                <input type="text" id="swift_code" name="swift_code" class="form-control"
                                    value="<?php echo isset($_SESSION['old_bank_data']['swift_code']) ? htmlspecialchars($_SESSION['old_bank_data']['swift_code']) : ''; ?>">
                                <div class="error-message" id="swift_code_error"></div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Opening Balance</label>
                                <div class="input-group">
                                    <span class="input-group-text"><?= $companyCurrency['currency_symbol'] ?></span>
                                    <input type="number" id="opening_balance" name="opening_balance" class="form-control" step="0.01"
                                        value="<?php echo isset($_SESSION['old_bank_data']['opening_balance']) ? htmlspecialchars($_SESSION['old_bank_data']['opening_balance']) : ''; ?>">
                                </div>
                                <div class="error-message" id="opening_balance_error"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Bank Modal -->
        <div class="modal fade" id="edit_bank_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Bank</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="edit-bank-form" method="POST" action="process/action_edit_bank.php">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="modal-body">
                            <div class="mb-2">
                                <label class="form-label">Bank Name<span class="text-danger">*</span></label>
                                <input type="text" id="edit_bank_name" name="bank_name" class="form-control">
                                <div class="error-message" id="edit_bank_name_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Account Holder<span class="text-danger">*</span></label>
                                <input type="text" id="edit_account_holder" name="account_holder" class="form-control">
                                <div class="error-message" id="edit_account_holder_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Account Number<span class="text-danger">*</span></label>
                                <input type="text" id="edit_account_number" name="account_number" class="form-control">
                                <div class="error-message" id="edit_account_number_error"></div>
                            </div>
                             <div class="mb-2">
                                <label class="form-label">Routing Number</label>
                                <input type="text" id="edit_routing_number" name="routing_number" class="form-control">
                                <div class="error-message" id="edit_routing_number_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">IFSC Code</label>
                                <input type="text" id="edit_ifsc_code" name="ifsc_code" class="form-control">
                                <div class="error-message" id="edit_ifsc_code_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">SWIFT Code</label>
                                <input type="text" id="edit_swift_code" name="swift_code" class="form-control">
                                <div class="error-message" id="edit_swift_code_error"></div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Opening Balance</label>
                                <div class="input-group">
                                    <span class="input-group-text"><?= $companyCurrency['currency_symbol'] ?></span>
                                    <input type="number" id="edit_opening_balance" name="opening_balance" class="form-control" step="0.01">
                                </div>
                                <div class="error-message" id="edit_opening_balance_error"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Multiple Delete Bank Modal -->
        <div class="modal fade" id="multiDeleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form id="multiDeleteForm" method="POST" action="process/action_multi_delete_bank.php">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Banks</h6>
                            <p class="mb-3">Are you sure you want to delete the selected banks?</p>
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
    
    <!-- JavaScript for Account Number Duplicate Validation -->
    <script>
    $(document).ready(function() {
        // Clear old session data when page loads
        <?php if (isset($_SESSION['old_bank_data'])): ?>
        setTimeout(function() {
            $.ajax({
                url: 'process/clear_session_data.php',
                type: 'POST',
                data: { clear_bank_data: 1 }
            });
        }, 100);
        <?php endif; ?>

        // Multiple Delete Functionality
        const multiDeleteModal = new bootstrap.Modal(document.getElementById('multiDeleteModal'));
        const deleteBtn = document.querySelector('.delete-multiple');
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.bank-checkbox');

        // Function to toggle delete button visibility
        function toggleDeleteBtn() {
            const anyChecked = document.querySelectorAll('.bank-checkbox:checked').length > 0;
            deleteBtn.classList.toggle('d-none', !anyChecked);
        }

        // Show modal on delete button click
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const selectedCheckboxes = document.querySelectorAll('.bank-checkbox:checked');
            const form = document.getElementById('multiDeleteForm');

            // Remove previous hidden inputs
            form.querySelectorAll('input[name="bank_ids[]"]').forEach(el => el.remove());

            // Add new hidden inputs
            selectedCheckboxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'bank_ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });

            // Update modal text based on selection count
            const modalTitle = document.querySelector('#multiDeleteModal h6');
            const modalMessage = document.querySelector('#multiDeleteModal p');

            if (selectedCheckboxes.length === 1) {
                modalTitle.textContent = 'Delete Bank';
                modalMessage.textContent = 'Are you sure you want to delete the selected bank?';
            } else {
                modalTitle.textContent = 'Delete Banks';
                modalMessage.textContent = `Are you sure you want to delete the ${selectedCheckboxes.length} selected banks?`;
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

        // Status toggle functionality
        $('.status-toggle').on('change', function() {
            var id = $(this).data('id');
            var status = $(this).is(':checked') ? 1 : 0;
            $.ajax({
                url: 'process/action_toggle_bank_status.php',
                type: 'POST',
                data: { id: id, status: status },
                success: function() { console.log('Status updated'); },
                error: function(xhr, status, error) { console.error('Error:', error); }
            });
        });

        // Populate edit modal
        $('.edit-bank-btn').on('click', function() {
            $('#edit_id').val($(this).data('id'));
            $('#edit_bank_name').val($(this).data('bank-name'));
            $('#edit_account_holder').val($(this).data('account-holder'));
            $('#edit_account_number').val($(this).data('account-number'));
            $('#edit_routing_number').val($(this).data('routing-number'));
            $('#edit_ifsc_code').val($(this).data('ifsc-code'));
            $('#edit_swift_code').val($(this).data('swift-code'));
            $('#edit_opening_balance').val($(this).data('opening-balance'));
            $('.error-message').text('');
            $('#edit_bank_modal').modal('show');
        });

        // Input restrictions
        $('#account_number, #edit_account_number').on('input', function() { 
            this.value = this.value.replace(/[^0-9]/g,''); 
            $(this).removeClass('duplicate-error');
        });
        $('#routing_number, #edit_routing_number').on('input', function() { 
            this.value = this.value.replace(/[^0-9]/g,''); 
        });
        $('#opening_balance, #edit_opening_balance').on('input', function() { 
            this.value = this.value.replace(/[^0-9.]/g,''); 
        });
        $('#bank_name, #account_holder, #edit_bank_name, #edit_account_holder').on('input', function() { 
            this.value = this.value.replace(/[^a-zA-Z\s]/g,''); 
        });

        // Field validation function
        function validateField(value, fieldName, required, type) {
            if(required && !value) return fieldName + ' is required.';
            if(value){
                switch(type){
                    case 'text': if(!/^[a-zA-Z\s]+$/.test(value)) return fieldName + ' should contain only letters and spaces.'; break;
                    case 'number': if(!/^\d+$/.test(value)) return fieldName + ' should contain only numbers.'; break;
                    case 'decimal': if(!/^\d+(\.\d{1,2})?$/.test(value)) return fieldName + ' should be a valid decimal number.'; break;
                    case 'ifsc': if(!/^[A-Za-z]{4}0[A-Za-z0-9]{6}$/.test(value)) return 'Invalid IFSC format.'; break;
                }
            }
            return '';
        }

        // Check duplicates via AJAX - UPDATED FOR ACCOUNT NUMBER
        function checkDuplicate(field, value, id=0) {
            return $.ajax({
                url: 'process/check_bank_duplicate.php',
                type: 'POST',
                dataType: 'json',
                data: { 
                    field: field, 
                    value: value, 
                    id: id,
                    org_id: <?php echo $currentOrgId; ?> // Include organization ID
                }
            });
        }

        // Validate account number for duplicates
        async function validateAccountNumber(formType) {
            var prefix = formType === 'add' ? '' : 'edit_';
            var fieldId = prefix + 'account_number';
            var inputElement = $('#' + fieldId);
            var value = inputElement.val().trim();
            var id = formType === 'add' ? 0 : $('#edit_id').val();
            var errorElement = $('#' + fieldId + '_error');
            
            // Clear previous error
            errorElement.text('');
            inputElement.removeClass('duplicate-error');
            
            if (value) {
                try {
                    var res = await checkDuplicate('account_number', value, id);
                    if (res.status === 'exists') {
                        errorElement.text(res.message);
                        inputElement.addClass('duplicate-error');
                        return false;
                    } else {
                        return true;
                    }
                } catch(err) {
                    console.error('Error checking duplicate:', err);
                    errorElement.text('Error checking account number. Please try again.');
                    return false;
                }
            }
            return true;
        }

        // Validate entire form (async for duplicate check)
        async function validateForm(formType){
            var isValid = true;
            var prefix = formType === 'add' ? '' : 'edit_';
            var id = formType === 'add' ? 0 : $('#edit_id').val();

            var fields = [
                {id: prefix+'bank_name', name:'Bank name', type:'text', required: true},
                {id: prefix+'account_holder', name:'Account holder', type:'text', required: true},
                {id: prefix+'account_number', name:'Account number', type:'number', required: true},
                {id: prefix+'routing_number', name:'Routing number', type:'number', required: false},
                {id: prefix+'ifsc_code', name:'IFSC code', type:'ifsc', required: false},
                {id: prefix+'opening_balance', name:'Opening balance', type:'decimal', required: false}
            ];

            // Validate each field
            for(var i=0; i<fields.length; i++){
                var val = $('#'+fields[i].id).val();
                var error = validateField(val, fields[i].name, fields[i].required, fields[i].type);
                if(error){ 
                    $('#'+fields[i].id+'_error').text(error); 
                    isValid=false; 
                } else { 
                    $('#'+fields[i].id+'_error').text(''); 
                }
            }

            // Check for duplicate account number
            const accountValid = await validateAccountNumber(formType);
            if (!accountValid) {
                isValid = false;
            }

            return isValid;
        }

        // Add/Edit form submission
        $('#add-bank-form, #edit-bank-form').on('submit', async function(e){
            e.preventDefault();
            var formType = $(this).attr('id') === 'add-bank-form' ? 'add' : 'edit';
            if(await validateForm(formType)){ 
                this.submit(); 
            }
        });

        // Real-time validation on blur for account number
        $('#account_number, #edit_account_number').on('blur', async function(){
            var formType = $(this).closest('form').attr('id') === 'add-bank-form' ? 'add' : 'edit';
            await validateAccountNumber(formType);
        });

        // Clear error when user starts typing
        $('#account_number, #edit_account_number').on('input', function() {
            var currentError = $(this).next('.error-message').text();
            if (currentError === 'Account number already exists!' || currentError.includes('Error checking')) {
                $(this).next('.error-message').text('');
                $(this).removeClass('duplicate-error');
            }
        });
    });
    </script>

    <script>
        $(document).ready(function() {
    // Function to validate specific fields in real-time
    function validateFieldRealtime(input, type) {
        var value = $(input).val();
        var fieldName = '';
        var errorSelector = '';

        if ($(input).attr('id') === 'account_number' || $(input).attr('id') === 'edit_account_number') {
            fieldName = 'Account number';
            errorSelector = '#' + $(input).attr('id') + '_error';
            if (!/^\d*$/.test(value)) {
                $(errorSelector).text(fieldName + ' should contain only numbers.');
            } else {
                $(errorSelector).text('');
            }
        }
        if ($(input).attr('id') === 'routing_number' || $(input).attr('id') === 'edit_routing_number') {
            fieldName = 'Routing number';
            errorSelector = '#' + $(input).attr('id') + '_error';
            if (!/^\d*$/.test(value)) {
                $(errorSelector).text(fieldName + ' should contain only numbers.');
            } else {
                $(errorSelector).text('');
            }
        }
        if ($(input).attr('id') === 'ifsc_code' || $(input).attr('id') === 'edit_ifsc_code') {
            fieldName = 'IFSC code';
            errorSelector = '#' + $(input).attr('id') + '_error';
            if (value && !/^[A-Za-z]{4}0[A-Za-z0-9]{6}$/.test(value)) {
                $(errorSelector).text('Invalid IFSC format.');
            } else {
                $(errorSelector).text('');
            }
        }
    }

    // Attach real-time validation for add and edit fields
    $('#account_number, #edit_account_number,#routing_number, #edit_routing_number,  #ifsc_code, #edit_ifsc_code, #swift_code, #edit_swift_code').on('input', function() {
        validateFieldRealtime(this);
    });
});
    </script>
    
    <script>
        $(document).ready(function () {
    // Clear errors and reset form when modal is closed
    $('#add_bank_modal, #edit_bank_modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();  // Reset all fields
        $(this).find('.error-message').text(''); // Clear all error messages
        $(this).find('input').removeClass('duplicate-error'); // Remove duplicate error styling
    });

    // Also clear when modal is opened (optional, to be extra safe)
    $('#add_bank_modal, #edit_bank_modal').on('show.bs.modal', function () {
        $(this).find('.error-message').text('');
        $(this).find('input').removeClass('duplicate-error');
    });
});
    </script>
    <?php if (isset($_GET['open']) && $_GET['open'] === 'add_bank_modal') { ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var myModal = new bootstrap.Modal(document.getElementById('add_bank_modal'));
        myModal.show();
    });
</script>
<script>
    // Handle edit form repopulation from session
$(document).ready(function() {
    // Check if there's edit session data and we need to open edit modal
    <?php if (isset($_GET['edit_id']) && isset($_SESSION['edit_bank_data']) && $_SESSION['edit_bank_data']['id'] == $_GET['edit_id']): ?>
        var editData = <?php echo json_encode($_SESSION['edit_bank_data']); ?>;
        
        // Populate edit form
        $('#edit_id').val(editData.id);
        $('#edit_bank_name').val(editData.bank_name);
        $('#edit_account_holder').val(editData.account_holder);
        $('#edit_account_number').val(editData.account_number);
        $('#edit_routing_number').val(editData.routing_number);
        $('#edit_ifsc_code').val(editData.ifsc_code);
        $('#edit_swift_code').val(editData.swift_code);
        $('#edit_opening_balance').val(editData.opening_balance);
        
        // Show error messages based on error type
        <?php if (isset($_GET['error']) && $_GET['error'] == 'duplicate_account'): ?>
            $('#edit_account_number_error').text('Account number already exists!');
            $('#edit_account_number').addClass('duplicate-error');
        <?php elseif (isset($_GET['error']) && $_GET['error'] == 'duplicate_ifsc'): ?>
            $('#edit_ifsc_code_error').text('IFSC code already exists!');
            $('#edit_ifsc_code').addClass('duplicate-error');
        <?php elseif (isset($_GET['error']) && $_GET['error'] == 'duplicate_swift'): ?>
            $('#edit_swift_code_error').text('SWIFT code already exists!');
            $('#edit_swift_code').addClass('duplicate-error');
        <?php endif; ?>
        
        // Show the edit modal
        var editModal = new bootstrap.Modal(document.getElementById('edit_bank_modal'));
        editModal.show();
        
        // Clear session data after showing
        setTimeout(function() {
            $.ajax({
                url: 'process/clear_session_data.php',
                type: 'POST',
                data: { clear_edit_bank_data: 1 }
            });
        }, 100);
    <?php endif; ?>
});
</script>
<?php } ?>
</body>
</html>
<?php 
// Clear old bank data after displaying
if (isset($_SESSION['old_bank_data'])) {
    unset($_SESSION['old_bank_data']);
}
?>