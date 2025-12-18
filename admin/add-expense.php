<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layouts/title-meta.php'; ?> 
    <?php include 'layouts/head-css.php'; ?>
    
    <!-- Additional CSS for datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>

    <!-- Start Main Wrapper -->
    <div class="main-wrapper">

        <?php include 'layouts/menu.php'; ?>

        <!-- ========================
            Start Page Content
        ========================= -->

        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content">

                <!-- Start row  -->
                <div class="row">
                    <div class="col-md-12 mx-auto">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6>Add Expense</h6>
                                <!-- <a href="expense-details.php" class="btn btn-outline-white d-inline-flex align-items-center"><i class="isax isax-eye me-1"></i>Preview</a> -->
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form action="process/action_add_expense.php" method="POST" enctype="multipart/form-data" id="form">
                                     <input type="hidden" name="user_id" value="<?php echo $_SESSION['crm_user_id'] ?? 1; ?>">

                                    <div class="border-bottom mb-3 pb-1">
                                        <div class="row gx-3">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Expense Category <span class="text-danger">*</span></label>
                                                    <select class="form-select select2" name="ecategory_id" id="category_id">
                                                        <option value="">Select Category</option>
                                                        <?php
                                                        
                                                        $query = "SELECT id, name FROM expense_category WHERE is_deleted = 0";
                                                        $result = mysqli_query($conn, $query);

                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                    <span class="text-danger error-text" id="category_error"></span>
                                                </div>

                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Expense Title</label>
                                                    <input type="text" class="form-control" name="title" id="title">
                                                    <span class="text-danger error-text" id="title_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Client Name</label>
                                                  <select class="form-select select2" name="client_id" id="client_id">
    <option value="">Select Client</option>
    <?php
    // Get selected client ID from URL
    $selectedClient = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;

    // MODIFIED: Apply access control to clients dropdown
    $clients_query = "SELECT * FROM client WHERE is_deleted = 0";
    if ($currentOrgId > 0) {
        $clients_query .= " AND org_id = $currentOrgId";
    }
    // Add role-based filtering for non-admin users
    if ($userRoleId != 1) {
        $clients_query .= " AND (user_id = $currentUserId OR EXISTS (
            SELECT 1 FROM login u 
            WHERE u.id = client.user_id 
            AND u.role_id = 1 
            AND u.org_id = $currentOrgId
        ))";
    }
    $clients_query .= " ORDER BY first_name ASC";
    
    $result = mysqli_query($conn, $clients_query);
    while ($row = mysqli_fetch_assoc($result)) {
        $isSelected = ($row['id'] == $selectedClient) ? 'selected' : '';
        
        // Build the display name with salutation, first name, last name, and company
        $displayName = trim($row['salutation'] . ' ' . $row['first_name'] . ' ' . $row['last_name']);
        if (!empty($row['company_name'])) {
            $displayName .= ' - ' . $row['company_name'];
        }
        
        echo '<option value="' . $row['id'] . '" ' . $isSelected . '>' . htmlspecialchars($displayName) . '</option>';
    }
    ?>
</select>
                                                    <span class="text-danger error-text" id="clientname_error"></span>
                                                </div>
                                            </div>
                                            
                                            <!-- <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Invoice Number</label>
                                                    <input type="text" class="form-control" name="invoice_id" id="invoice_id" readonly>
                                                </div>
                                            </div> -->

                                            
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Expense Date<span class="text-danger">*</span></label>
                                                    <div class="input-group position-relative">
                                                        <input type="text" class="form-control datepicker" id="expense_date" name="date">
                                                        <span class="input-group-text">
                                                            <i class="isax isax-calendar-2"></i>
                                                        </span>
                                                    </div>
                                            <span class="text-danger error-text" id="expense_date_error"></span>

                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Amount<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="amount" id="amount">
                                                    <span class="text-danger error-text" id="amount_error"></span>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Upload Document</label>
                                                    <input type="file" class="form-control" name="document[]" id="expense_document" multiple>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                                </div>
                                            </div>
                                            

                                        </div>
                                    </div>
                                        

                                        <div class="d-flex align-items-center justify-content-between">
                                            <button type="button" class="btn btn-outline-white" onclick="window.location.href='expense.php'">Cancel</button>
                                            <button type="submit"  class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

            </div>
            <!-- End Content -->

            <?php include 'layouts/footer.php'; ?>

        </div>

        <!-- ========================
            End Page Content
        ========================= -->

    </div>
    <!-- End Main Wrapper -->

    <?php include 'layouts/vendor-scripts.php'; ?>
    
    <!-- Additional JS for datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize datepicker
        $(document).ready(function() {
            $('.datepicker').flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true,
                defaultDate: new Date(),
                clickOpens: true
            });
            
            // Initialize select2
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
            
            // Form validation
            $('#form').on('submit', function(e) {
                e.preventDefault();
                var isValid = true;
                
                // Validate required fields
                if ($('#category_id').val() === '') {
                    $('#category_error').text('Please select a category');
                    isValid = false;
                } else {
                    $('#category_error').text('');
                }
                
                // if ($('#client_id').val() === '') {
                //     $('#clientname_error').text('Please select a client');
                //     isValid = false;
                // } else {
                //     $('#clientname_error').text('');   
                // }
                
                if ($('#expense_date').val() === '') {
                    $('#expense_date_error').text('Please select a date');
                    isValid = false;
                } else {
                    $('#expense_date_error').text('');
                }
                
                if ($('#amount').val() === '') {
                    $('#amount_error').text('Please enter an amount');
                    isValid = false;
                } else if (isNaN($('#amount').val())) {
                    $('#amount_error').text('Please enter a valid number');
                    isValid = false;
                } else {
                    $('#amount_error').text('');
                }
                
                if (isValid) {
                    this.submit();
                }
            });
        });
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

    </script>
    <script>
$(document).ready(function () {
    $('#form').on('submit', function(e) {
        e.preventDefault();
        var isValid = true;

        if ($('#category_id').val() === '') {
            $('#category_error').text('Please select a category');
            isValid = false;
        } else {
            $('#category_error').text('');
        }

        if ($('#expense_date').val() === '') {
            $('#expense_date_error').text('Please select a date');
            isValid = false;
        } else {
            $('#expense_date_error').text('');
        }

        if ($('#amount').val() === '') {
            $('#amount_error').text('Please enter an amount');
            isValid = false;
        } else if (isNaN($('#amount').val())) {
            $('#amount_error').text('Please enter a valid number');
            isValid = false;
        } else {
            $('#amount_error').text('');
        }

        if (isValid) {
            this.submit();
        }
    });
});
</script>


<script>
$(document).ready(function() {
    $('#category_id').select2({
        placeholder: "Select Category",
        width: '100%',
        dropdownCssClass: 'select2-scrollable'
    });
});
</script>


</body>

</html>