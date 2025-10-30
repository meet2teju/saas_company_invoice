<?php include 'layouts/session.php'; ?>
<?php include '../config/config.php'; ?>

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
 ?>

                <div class="row justify-content-center">
                    <div class="col-xl-12">
                        <div class="row settings-wrapper d-flex">
                            <div class="col-12">

                                <div class="mb-3">
                                    <div class="pb-3 border-bottom mb-3">
                                        <h6 class="mb-0">Bank Details</h6>
                                    </div>

                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <div class="input-icon-start position-relative">
                                            <!-- Search input if needed -->
                                        </div>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add_bank_modal" class="btn btn-primary">
                                            <i class="isax isax-add-circle5 me-2"></i>New Bank
                                        </a>
                                    </div>

                                    <div class="table-responsive table-nowrap pb-3 border-bottom">
                                        <div class="table-search d-flex align-items-center mb-0">
                            <div class="search-input">
                                <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                            </div>
                        </div><br>
                                        <table class="table table-nowrap datatable">
                                            <thead class="table-light">
                                                <tr>
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
                                                $query = "SELECT * FROM bank ORDER BY created_at DESC";
                                                $result = mysqli_query($conn, $query);
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    $status = $row['status'] ? 'checked' : '';
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['bank_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['account_holder']) ?></td>
                                                    <td><?= htmlspecialchars($row['account_number']) ?></td>
                                                    <td><?= htmlspecialchars($row['ifsc_code']) ?></td>
                                                    <td><?= htmlspecialchars($row['swift_code']) ?></td>
                                                    <td>$&nbsp;<?= number_format($row['opening_balance'], 2) ?></td>
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
                            </div>
                        </div>
                    </div>
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
                        <div class="modal-body">
                            <div class="mb-2">
                                <label class="form-label">Bank Name<span class="text-danger">*</span></label>
                                <input type="text" id="bank_name" name="bank_name" class="form-control">
                                <div class="error-message" id="bank_name_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Account Holder Name<span class="text-danger">*</span></label>
                                <input type="text" id="account_holder" name="account_holder" class="form-control">
                                <div class="error-message" id="account_holder_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Account Number<span class="text-danger">*</span></label>
                                <input type="text" id="account_number" name="account_number" class="form-control">
                                <div class="error-message" id="account_number_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">IFSC Code<span class="text-danger">*</span></label>
                                <input type="text" id="ifsc_code" name="ifsc_code" class="form-control">
                                <div class="error-message" id="ifsc_code_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">SWIFT Code</label>
                                <input type="text" id="swift_code" name="swift_code" class="form-control">
                                <div class="error-message" id="swift_code_error"></div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Opening Balance<span class="text-danger">*</span></label>
                                <input type="number" id="opening_balance" name="opening_balance" class="form-control" step="0.01">
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
                                <label class="form-label">IFSC Code<span class="text-danger">*</span></label>
                                <input type="text" id="edit_ifsc_code" name="ifsc_code" class="form-control">
                                <div class="error-message" id="edit_ifsc_code_error"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">SWIFT Code</label>
                                <input type="text" id="edit_swift_code" name="swift_code" class="form-control">
                                <div class="error-message" id="edit_swift_code_error"></div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Opening Balance<span class="text-danger">*</span></label>
                                <input type="number" id="edit_opening_balance" name="opening_balance" class="form-control" step="0.01">
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

    </div>
    <?php include 'layouts/vendor-scripts.php'; ?>
    <script>
$(document).ready(function() {

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
        $('#edit_ifsc_code').val($(this).data('ifsc-code'));
        $('#edit_swift_code').val($(this).data('swift-code'));
        $('#edit_opening_balance').val($(this).data('opening-balance'));
        $('.error-message').text('');
        $('#edit_bank_modal').modal('show');
    });

    // Input restrictions
    $('#account_number, #edit_account_number').on('input', function() { this.value = this.value.replace(/[^0-9]/g,''); });
    $('#opening_balance, #edit_opening_balance').on('input', function() { this.value = this.value.replace(/[^0-9.]/g,''); });
    $('#bank_name, #account_holder, #edit_bank_name, #edit_account_holder').on('input', function() { this.value = this.value.replace(/[^a-zA-Z\s]/g,''); });

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

    // Check duplicates via AJAX
    function checkDuplicate(field, value, id=0) {
        return $.ajax({
            url: 'process/check_bank_duplicate.php',
            type: 'POST',
            dataType: 'json',
            data: { field: field, value: value, id: id }
        });
    }

    // Validate entire form (async for duplicate check)
    async function validateForm(formType){
        var isValid = true;
        var prefix = formType === 'add' ? '' : 'edit_';
        var id = formType === 'add' ? 0 : $('#edit_id').val();

        var fields = [
            {id: prefix+'bank_name', name:'Bank name', type:'text'},
            {id: prefix+'account_holder', name:'Account holder', type:'text'},
            {id: prefix+'account_number', name:'Account number', type:'number'},
            {id: prefix+'ifsc_code', name:'IFSC code', type:'ifsc'},
            {id: prefix+'opening_balance', name:'Opening balance', type:'decimal'}
        ];

        // Validate each field
        for(var i=0; i<fields.length; i++){
            var val = $('#'+fields[i].id).val();
            var error = validateField(val, fields[i].name, true, fields[i].type);
            if(error){ $('#'+fields[i].id+'_error').text(error); isValid=false; }
            else{ $('#'+fields[i].id+'_error').text(''); }
        }

        // Duplicate check for account_number and ifsc_code
        var dupFields = ['account_number','ifsc_code'];
        for(var j=0;j<dupFields.length;j++){
            var fieldId = prefix + dupFields[j];
            var value = $('#'+fieldId).val().trim();
            if(value){
                try{
                    var res = await checkDuplicate(dupFields[j], value, id);
                    if(res.status === 'exists'){
                        $('#'+fieldId+'_error').text(res.message);
                        isValid = false;
                    } else{ $('#'+fieldId+'_error').text(''); }
                }catch(err){ console.error(err); isValid=false; }
            }
        }

        return isValid;
    }

    // Add/Edit form submission
    $('#add-bank-form, #edit-bank-form').on('submit', async function(e){
        e.preventDefault();
        var formType = $(this).attr('id') === 'add-bank-form' ? 'add' : 'edit';
        if(await validateForm(formType)){ this.submit(); }
    });

    // Real-time validation on blur
    $('#bank_name, #account_holder, #account_number, #ifsc_code, #opening_balance, #edit_bank_name, #edit_account_holder, #edit_account_number, #edit_ifsc_code, #edit_opening_balance').on('blur', async function(){
        var formType = $(this).closest('form').attr('id') === 'add-bank-form' ? 'add' : 'edit';
        await validateForm(formType);
    });

});
</script>

    <!-- <script>
    $(document).ready(function() {
        // Status toggle functionality
        $('.status-toggle').on('change', function() {
            var id = $(this).data('id');
            var status = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: 'process/action_toggle_bank_status.php',
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

        // Edit bank button click handler
        $('.edit-bank-btn').on('click', function() {
            var id = $(this).data('id');
            var bankName = $(this).data('bank-name');
            var accountHolder = $(this).data('account-holder');
            var accountNumber = $(this).data('account-number');
            var ifscCode = $(this).data('ifsc-code');
            var swiftCode = $(this).data('swift-code');
            var openingBalance = $(this).data('opening-balance');
            
            $('#edit_id').val(id);
            $('#edit_bank_name').val(bankName);
            $('#edit_account_holder').val(accountHolder);
            $('#edit_account_number').val(accountNumber);
            $('#edit_ifsc_code').val(ifscCode);
            $('#edit_swift_code').val(swiftCode);
            $('#edit_opening_balance').val(openingBalance);
            
            // Clear any previous error messages
            $('.error-message').text('');
            
            $('#edit_bank_modal').modal('show');
        });

        // Input restrictions
        $('#account_number, #edit_account_number').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        $('#opening_balance, #edit_opening_balance').on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });
        
        $('#bank_name, #account_holder, #edit_bank_name, #edit_account_holder').on('input', function() {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });

        // Validation functions
        function validateField(value, fieldName, isRequired, validationType) {
            if (isRequired && !value) {
                return fieldName + ' is required.';
            }
            
            if (value) {
                switch(validationType) {
                    case 'text':
                        if (!/^[a-zA-Z\s]+$/.test(value)) {
                            return fieldName + ' should contain only letters and spaces.';
                        }
                        break;
                    case 'number':
                        if (!/^\d+$/.test(value)) {
                            return fieldName + ' should contain only numbers.';
                        }
                        break;
                    case 'decimal':
                        if (!/^\d+(\.\d{1,2})?$/.test(value)) {
                            return fieldName + ' should be a valid decimal number.';
                        }
                        break;
                    case 'ifsc':
                        if (!/^[A-Za-z]{4}0[A-Za-z0-9]{6}$/.test(value)) {
                            return 'Invalid IFSC format.';
                        }
                        break;
                    // case 'swift':
                    //     if (!/^[A-Za-z0-9]{8}([A-Za-z0-9]{3})?$/.test(value)) {
                    //         return 'Invalid SWIFT format.';
                    //     }
                    //     break;
                }
            }
            
            return '';
        }

        // Form validation
        function validateForm(formType) {
            var isValid = true;
            var prefix = formType === 'add' ? '' : 'edit_';
            
            // Bank Name validation
            var bankName = $('#' + prefix + 'bank_name').val();
            var bankNameError = validateField(bankName, 'Bank name', true, 'text');
            if (bankNameError) {
                $('#' + prefix + 'bank_name_error').text(bankNameError);
                isValid = false;
            } else {
                $('#' + prefix + 'bank_name_error').text('');
            }
            
            // Account Holder validation
            var accountHolder = $('#' + prefix + 'account_holder').val();
            var accountHolderError = validateField(accountHolder, 'Account holder', true, 'text');
            if (accountHolderError) {
                $('#' + prefix + 'account_holder_error').text(accountHolderError);
                isValid = false;
            } else {
                $('#' + prefix + 'account_holder_error').text('');
            }
            
            // Account Number validation
            var accountNumber = $('#' + prefix + 'account_number').val();
            var accountNumberError = validateField(accountNumber, 'Account number', true, 'number');
            if (accountNumberError) {
                $('#' + prefix + 'account_number_error').text(accountNumberError);
                isValid = false;
            } else {
                $('#' + prefix + 'account_number_error').text('');
            }
            
            // IFSC Code validation
            var ifscCode = $('#' + prefix + 'ifsc_code').val();
            var ifscCodeError = validateField(ifscCode, 'IFSC code', true, 'ifsc');
            if (ifscCodeError) {
                $('#' + prefix + 'ifsc_code_error').text(ifscCodeError);
                isValid = false;
            } else {
                $('#' + prefix + 'ifsc_code_error').text('');
            }
            
            // SWIFT Code validation
            // var swiftCode = $('#' + prefix + 'swift_code').val();
            // var swiftCodeError = validateField(swiftCode, 'SWIFT code', true, 'swift');
            // if (swiftCodeError) {
            //     $('#' + prefix + 'swift_code_error').text(swiftCodeError);
            //     isValid = false;
            // } else {
            //     $('#' + prefix + 'swift_code_error').text('');
            // }
            
            // Opening Balance validation
            var openingBalance = $('#' + prefix + 'opening_balance').val();
            var openingBalanceError = validateField(openingBalance, 'Opening balance', true, 'decimal');
            if (openingBalanceError) {
                $('#' + prefix + 'opening_balance_error').text(openingBalanceError);
                isValid = false;
            } else {
                $('#' + prefix + 'opening_balance_error').text('');
            }
            
            return isValid;
        }

        // Add form submission
        $('#add-bank-form').on('submit', function(e) {
            if (!validateForm('add')) {
                e.preventDefault();
            }
        });

        // Edit form submission
        $('#edit-bank-form').on('submit', function(e) {
            if (!validateForm('edit')) {
                e.preventDefault();
            }
        });

        // Real-time validation for add form
        $('#bank_name, #account_holder, #account_number, #ifsc_code, #swift_code, #opening_balance').on('blur', function() {
            validateForm('add');
        });

        // Real-time validation for edit form
        $('#edit_bank_name, #edit_account_holder, #edit_account_number, #edit_ifsc_code, #edit_swift_code, #edit_opening_balance').on('blur', function() {
            validateForm('edit');
        });
    });
    </script> -->
    
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

        if ($(input).attr('id') === 'ifsc_code' || $(input).attr('id') === 'edit_ifsc_code') {
            fieldName = 'IFSC code';
            errorSelector = '#' + $(input).attr('id') + '_error';
            if (value && !/^[A-Za-z]{4}0[A-Za-z0-9]{6}$/.test(value)) {
                $(errorSelector).text('Invalid IFSC format.');
            } else {
                $(errorSelector).text('');
            }
        }

        // if ($(input).attr('id') === 'swift_code' || $(input).attr('id') === 'edit_swift_code') {
        //     fieldName = 'SWIFT code';
        //     errorSelector = '#' + $(input).attr('id') + '_error';
        //     if (value && !/^[A-Za-z0-9]{8}([A-Za-z0-9]{3})?$/.test(value)) {
        //         $(errorSelector).text('Invalid SWIFT format.');
        //     } else {
        //         $(errorSelector).text('');
        //     }
        // }
    }

    // Attach real-time validation for add and edit fields
    $('#account_number, #edit_account_number, #ifsc_code, #edit_ifsc_code, #swift_code, #edit_swift_code').on('input', function() {
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
    });

    // Also clear when modal is opened (optional, to be extra safe)
    $('#add_bank_modal, #edit_bank_modal').on('show.bs.modal', function () {
        $(this).find('.error-message').text('');
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

<?php } ?>
</body>
</html>