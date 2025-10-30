<?php include 'layouts/session.php'; ?>
<?php include '../config/config.php'; ?>
<?php
$users = [];

$query = "
    SELECT login.id, login.name, login.email 
    FROM login
    JOIN user_role ON login.role_id = user_role.id
    WHERE  login.is_deleted = 0
    ORDER BY login.name ASC
";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}
$statuses = [];
$result = mysqli_query($conn, "SELECT id, status_name FROM project_status WHERE is_deleted = 0");
while ($row = mysqli_fetch_assoc($result)) {
    $statuses[] = $row;
}
// Get all countries for dropdown
$country_query = "SELECT * FROM countries ORDER BY name";
$country_result = mysqli_query($conn, $country_query);

// Reset country_result pointer for the dropdown
mysqli_data_seek($country_result, 0);
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
            <div class="content content-two">
                <!-- Page Header -->
               <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6>Add Projects</h6>
                            <a href="#" class="btn btn-outline-white d-inline-flex align-items-center"><i class="isax isax-eye me-1"></i>Preview</a>
                </div>
                <!-- End Page Header -->

                <!-- Project Form -->
                <form action="process/action_add_project.php" method="POST" id="form">
                 <input type="hidden" name="user_id" value="<?php echo $_SESSION['crm_user_id'] ?? 1; ?>">

                    <div class="card">
                        <div class="card-body">
                            <!-- Project Details Section -->
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Project Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="project_name" id="project_name">
                                        <span class="text-danger error-text" id="name_error"></span>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Project Code</label>
                                        <input type="text" class="form-control" name="project_code" id="project_code">
                                    </div>
                                    
                                   <div class="col-md-6 mb-3">
                                    <label class="form-label">Client Name <span class="text-danger">*</span></label>
                                        <select class="form-select select2" name="client_id[]" multiple="multiple" id="client_id">
                                            <?php
                                            $query = "SELECT * FROM client WHERE is_deleted = 0 ORDER BY first_name ASC";
                                            $result = mysqli_query($conn, $query);
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['first_name']) . '</option>';
                                            }
                                            ?>
                                            <!-- <option value="add_new_client" id="add_new_client" class="text-primary">+ Add New Client</option> -->
                                        </select>
                                    <span class="text-danger error-text" id="clientname_error"></span>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Billing Method <span class="text-danger">*</span></label>
                                        <select class="form-select" name="billing_method" id="billingMethod" onchange="showBillingFields()">
                                            <option value="">Select billing method</option>
                                            <option value="1">Fixed Price</option>
                                            <option value="2">Project Hourly</option>
                                            <option value="3">Task Hourly</option>
                                            <option value="4">Staff Hourly</option>
                                        </select>
                                    <span class="text-danger error-text" id="method_error"></span>
                                    </div>

                                    <!-- Dynamic Fields Container -->
                                    <div id="billingFieldsContainer">
                                        <!-- Fields will be inserted here based on selection -->
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3" ></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Users Section -->
                         <div class="mb-4">
                                <h5 class="mb-3">Users</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="contactTable">
                                        <thead>
                                            <tr>
                                                <th>S.no</th>
                                                <th>User</th>
                                                <th>Email</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="projectuserTableBody">
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    <select name="user_id[]" class="form-select user-select" onchange="updateEmail(this)">
                                                        <option value="">Select User</option>
                                                        <?php foreach ($users as $user): ?>
                                                            <option value="<?= htmlspecialchars($user['id']) ?>" data-email="<?= htmlspecialchars($user['email']) ?>">
                                                                <?= htmlspecialchars($user['name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td><input type="email" name="email[]" class="form-control" placeholder="Email" readonly></td>
                                                <td><button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(this)">
                                                    <i class="fas fa-trash"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-start mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addContactRow()">
                                        <i class="fas fa-plus me-1"></i> Add More
                                    </button>
                                </div>
                            </div>

                            <hr class="my-4">
                            
                            <!-- Project Tasks Section -->
                           <div class="mb-4">
                                <h5 class="mb-3">Project Tasks</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tasksTable">
                                        <thead>
                                            <tr>
                                                <th>S.no</th>
                                                <th>Task name</th>
                                                <th>Description</th>
                                                <th>Start date</th>
                                                <th>End date</th>
                                                <th>Hour</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tasksTableBody">
                                            <tr>
                                                <td>1</td>
                                                <td><input type="text" name="task_name[]" class="form-control" placeholder="Task Name"></td>
                                                <td>
                                                    <textarea name="task_description[]" class="form-control" placeholder="Description" rows="2"></textarea>
                                                </td>
                                                <td><input type="text" name="start_date[]" class="form-control task-start-date datepicker" placeholder="Start Date" readonly></td>
                                                <td><input type="text" name="end_date[]" class="form-control task-end-date datepicker" placeholder="End Date" readonly></td>
                                                <td><input type="number" name="hour[]" class="form-control" placeholder="Hour" step="0.01" min="0"></td>
                                                <td>
                                                    <select name="status_id[]" class="form-control select2">
                                                        <option value="">Select Status</option>
                                                        <?php foreach ($statuses as $status): ?>
                                                            <option value="<?= $status['id'] ?>"><?= htmlspecialchars($status['status_name']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>

                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteTaskRow(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-start mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addTaskBtn" onclick="addTaskRow()">
                                        <i class="fas fa-plus me-1"></i> Add Project Task
                                    </button>
                                </div>
                            </div>

                            
                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='projects.php'">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
                <?php include 'layouts/footer.php'; ?>
            </div>
        
        </div>
        <!-- End Content -->

 
    </div>
    <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addClientModalLabel">Add New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="clientForm" action="process/action_modeladd_client.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <div class="d-flex align-items-center">
                            <div id="modal_image_preview" class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0">
                                <i class="isax isax-image text-primary fs-24"></i>
                            </div>
                            <div class="d-inline-flex flex-column align-items-start">
                                <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                    <i class="isax isax-image me-1"></i>Upload Image
                                    <input type="file" class="form-control image-sign" name="customer_image" id="modal_image" accept="image/*">
                                </div>
                                <span id="modal_image_error" class="text-danger error-text"></span>
                                <span class="text-gray-9">JPG or PNG format, not exceeding 5MB.</span>
                            </div>
                        </div>
                        
                        <label class="form-label">Client Type</label>
                        <div class="d-flex align-items-center mb-3">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="client_type" value="1" checked>
                                <label class="form-check-label">Business</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="client_type" value="0">
                                <label class="form-check-label">Individual</label>
                            </div>
                        </div>
                        
                        <div class="row gx-3">
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Salutation<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select" name="salutation" id="modal_salutation" onchange="updateModalDisplayName()">
                                        <option value="Mr">Mr</option>
                                        <option value="Mrs">Mrs</option>
                                        <option value="Ms">Ms</option>
                                        <option value="Miss">Miss</option>
                                        <option value="Dr">Dr</option>
                                    </select>
                                    <span id="modal_salutation_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name <span class="text-danger ms-1">*</span></label>
                                    <input type="text" class="form-control" name="first_name" id="modal_first_name">
                                    <span id="modal_first_name_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name <span class="text-danger ms-1">*</span></label>
                                    <input type="text" class="form-control" name="last_name" id="modal_last_name">
                                    <span id="modal_last_name_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Company Name <span class="text-danger ms-1">*</span></label>
                                    <input type="text" class="form-control" name="company_name" id="modal_company_name">
                                    <span id="modal_company_name_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Display Name </label>
                                    <input type="text" class="form-control" name="display_name" id="modal_display_name">
                                    <span id="modal_display_name_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email <span class="text-danger ms-1">*</span></label>
                                    <input type="email" class="form-control" name="email" id="modal_email">
                                    <span id="modal_email_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Work Number <span class="text-danger ms-1">*</span></label>
                                    <input type="text" class="form-control" name="phone_number" id="modal_phone_number">
                                    <span id="modal_phone_number_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mobile Number <span class="text-danger ms-1">*</span></label>
                                    <input type="text" class="form-control" name="business_number" id="modal_business_number">
                                    <span id="modal_business_number_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs Start -->
                        <ul class="nav nav-tabs mb-3" id="modalCustomerTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="modal-other-tab" data-bs-toggle="tab" data-bs-target="#modal-otherTab" type="button" role="tab">Other Details</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="modal-address-tab" data-bs-toggle="tab" data-bs-target="#modal-addressTab" type="button" role="tab">Address</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="modal-contact-tab" data-bs-toggle="tab" data-bs-target="#modal-contactTab" type="button" role="tab">Contact Persons</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="modal-bank-tab" data-bs-toggle="tab" data-bs-target="#modal-bankTab" type="button" role="tab">Banking Details</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="modal-remarks-tab" data-bs-toggle="tab" data-bs-target="#modal-remarksTab" type="button" role="tab">Remarks</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="modalCustomerTabContent">
                            <!-- Other Details Tab -->
                            <div class="tab-pane fade show active" id="modal-otherTab" role="tabpanel">
                                <div class="row gx-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">PAN<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="pan_number" id="modal_pan_number">
                                        <span id="modal_pan_number_error" class="text-danger error-text"></span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Currency</label>
                                        <select class="form-select" name="currency">
                                            <option value="INR">Indian Rupee</option>
                                            <option value="$">US Dollar</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Enable Portal?</label><br>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="modal_enable_portal" name="enable_portal">
                                            <label class="form-check-label" for="modal_enable_portal">
                                                Allow portal access for this customer                                                           
                                            </label>
                                        </div>
                                        <span id="modal_email_required_note" class="text-muted ms-1 d-none">( Email address is mandatory )</span>
                                    </div>

                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Documents</label>
                                        <input type="file" class="form-control" name="documents[]" id="modal_documents" multiple>
                                        <span id="modal_documents_error" class="text-danger error-text"></span>
                                    </div>
                                </div>
                                <a href="#" class="text-primary mt-2 d-inline-block" data-bs-toggle="collapse" data-bs-target="#modal-moreDetails">
                                        Add more details
                                    </a>

                                    <!-- Hidden section -->
                                    <div class="collapse mt-3" id="modal-moreDetails">
                                        <div class="row gx-3">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Website URL</label>
                                                <input type="url" class="form-control" name="website_url" placeholder="https://">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Department</label>
                                                <input type="text" class="form-control" name="department">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Designation</label>
                                                <input type="text" class="form-control" name="designation">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Twitter</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fab fa-x-twitter"></i></span>
                                                    <input type="text" class="form-control" name="twitter" placeholder="http://www.twitter.com/">
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Skype Name/Number</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fab fa-skype"></i></span>
                                                    <input type="text" class="form-control" name="skype_name_number">
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Facebook</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                                                    <input type="text" class="form-control" name="facebook" placeholder="http://www.facebook.com/">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <!-- Address Tab -->
                            <div class="tab-pane fade" id="modal-addressTab" role="tabpanel">
                                <div class="row gx-5">
                                    <!-- Billing Address -->
                                    <div class="col-md-6">
                                        <h6 class="mb-3">Billing Address</h6>
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" id="modal_billing_name" class="form-control" name="billing_name">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Address Line 1</label>
                                                <input type="text" id="modal_billing_address1" class="form-control" name="billing_address1">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Address Line 2</label>
                                                <input type="text" id="modal_billing_address2" class="form-control" name="billing_address2">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Country<span class="text-danger ms-1">*</span></label>
                                                <select class="form-select" id="modal_billing_country" name="billing_country" onchange="getModalStates(this.value, 'modal_billing_state')">
                                                     <option value="">Select Country</option>
                                                    <?php 
                                                    mysqli_data_seek($country_result, 0);
                                                    while ($country = mysqli_fetch_assoc($country_result)) {
                                                        echo "<option value='{$country['id']}'>{$country['name']}</option>";
                                                    } ?>
                                                </select>
                                                <span id="modal_billing_country_error" class="text-danger error-text"></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">State<span class="text-danger ms-1">*</span></label>
                                                <select class="form-select" id="modal_billing_state" name="billing_state" onchange="getModalCities(this.value, 'modal_billing_city')">
                                                    <option value="">Select State</option>
                                                </select>
                                                <span id="modal_billing_state_error" class="text-danger error-text"></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">City<span class="text-danger ms-1">*</span></label>
                                                <select class="form-select" id="modal_billing_city" name="billing_city">
                                                    <option value="">Select City</option>
                                                </select>
                                                <span id="modal_billing_city_error" class="text-danger error-text"></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Pincode</label>
                                                <input type="text" class="form-control" id="modal_billing_pincode" name="billing_pincode">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Shipping Address -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h6>Shipping Address</h6>
                                     <a href="javascript:void(0);" onclick="copyModalBillingToShipping()" class="text-primary text-decoration-underline fs-13">
                                     <i class="isax isax-document-copy me-1"></i>Copy From Billing</a>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" class="form-control" id="modal_shipping_name" name="shipping_name">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Address Line 1</label>
                                                <input type="text" class="form-control" id="modal_shipping_address1" name="shipping_address1">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Address Line 2</label>
                                                <input type="text" class="form-control" id="modal_shipping_address2" name="shipping_address2">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Country<span class="text-danger ms-1">*</span></label>
                                                <select class="form-select" id="modal_shipping_country" name="shipping_country" onchange="getModalStates(this.value, 'modal_shipping_state')">
                                                    <option value="">Select Country</option>
                                                    <?php 
                                                    mysqli_data_seek($country_result, 0);
                                                    while ($country = mysqli_fetch_assoc($country_result)) {
                                                        echo "<option value='{$country['id']}'>{$country['name']}</option>";
                                                    } ?>
                                                </select>
                                                <span id="modal_shipping_country_error" class="text-danger error-text"></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">State<span class="text-danger ms-1">*</span></label>
                                                <select class="form-select" id="modal_shipping_state" name="shipping_state" onchange="getModalCities(this.value, 'modal_shipping_city')">
                                                    <option value="">Select State</option>
                                                </select>
                                                <span id="modal_shipping_state_error" class="text-danger error-text"></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">City<span class="text-danger ms-1">*</span></label>
                                                <select class="form-select" id="modal_shipping_city" name="shipping_city">
                                                    <option value="">Select City</option>
                                                </select>
                                                <span id="modal_shipping_city_error" class="text-danger error-text"></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Pincode</label>
                                                <input type="text" class="form-control" id="modal_shipping_pincode" name="shipping_pincode">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Persons Tab -->
                            <div class="tab-pane fade" id="modal-contactTab" role="tabpanel">
                                <h6 class="mb-3">Contact Persons</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="modalContactTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Salutation</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Email Address</th>
                                                <th>Work Phone</th>
                                                <th>Mobile</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="modalContactTableBody">
                                            <!-- Rows will be added here dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-outline-primary mt-2" onclick="addModalContactRow()">
                                    <i class="isax isax-add"></i> Add Contact Person
                                </button>
                            </div>

                            <!-- Bank Details Tab -->
                            <div class="tab-pane fade" id="modal-bankTab" role="tabpanel">
                                <h6 class="mb-3">Banking Details</h6>
                                <div class="row gx-3">
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">Bank Name<span class="text-danger ms-1">*</span></label>
                                                        <input type="text" class="form-control" name="bank_name" >
                                                         <span id="bank_name_error" class="text-danger error-text"></span>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">Branch<span class="text-danger ms-1">*</span></label>
                                                        <input type="text" class="form-control" name="bank_branch" >
                                                                <span id="bank_branch_error" class="text-danger error-text"></span>                                                   
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">Account Holder<span class="text-danger ms-1">*</span></label>
                                                        <input type="text" class="form-control" name="account_holder">
                                                   <span id="account_holder_error" class="text-danger error-text"></span>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">Account Number<span class="text-danger ms-1">*</span></label>
                                                        <input type="text" class="form-control" name="account_number">
                                                          <span id="account_number_error" class="text-danger error-text"></span>

                                                    </div>
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">IFSC<span class="text-danger ms-1">*</span></label>
                                                        <input type="text" class="form-control" name="IFSC_code" >
                                                       <span id="ifsc_code_error" class="text-danger error-text"></span>

                                                    </div>
                                                </div>
                            </div>

                            <!-- Remarks Tab -->
                            <div class="tab-pane fade" id="modal-remarksTab" role="tabpanel">
                                <h6 class="mb-3">Remarks (For Internal Use)</h6>
                                <div class="mb-3">
                                    <textarea class="form-control" rows="5" name="remark" placeholder="Enter any internal remarks about this customer"></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- Tabs End -->

                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between pt-4 border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="submit" class="btn btn-primary">Save Client</button>
                    </div>
                </form>
            </div>
        </div>
            
    </div>
    </div>
    <!-- ========================
        End Page Content
    ========================= -->

    <?php include 'layouts/vendor-scripts.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
    $(document).ready(function () {
    
        
 $('#selling_price, #project_code, #purchase_price, #quantity, #alert_quantity, #code').on('input', function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });
    // === Allow only text (no digits) ===
    $('#project_name').on('input', function () {
        this.value = this.value.replace(/[0-9]/g, '');
    });

  
});

</script>
    <script>
    // Initialize datepicker
    $(document).ready(function() {
        // Initialize Flatpickr for all datepicker fields
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
    });

    // Embed users into JS
    const users = <?= json_encode($users) ?>;

    function addContactRow() {
        const tableBody = document.getElementById("projectuserTableBody");
        const rowCount = tableBody.rows.length;
        const newRow = document.createElement("tr");

        let options = '<option value="">Select User</option>';
        users.forEach(user => {
            options += `<option value="${user.id}" data-email="${user.email}">${user.name}</option>`;
        });

        newRow.innerHTML = `
            <td>${rowCount + 1}</td>
            <td>
                <select name="user_id[]" class="form-select user-select" onchange="updateEmail(this); filterUserOptions();">
                    ${options}
                </select>
            </td>
            <td><input type="email" name="email[]" class="form-control" placeholder="Email" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(this)">
                <i class="fas fa-trash"></i></button></td>
        `;

        tableBody.appendChild(newRow);
        updateSerialNumbers();
        filterUserOptions(); // apply filtering immediately
    }

    function deleteRow(btn) {
        const row = btn.closest("tr");
        row.remove();
        updateSerialNumbers();
        filterUserOptions(); // re-filter after delete
    }

    function updateSerialNumbers() {
        const rows = document.querySelectorAll("#projectuserTableBody tr");
        rows.forEach((row, index) => {
            row.cells[0].innerText = index + 1;
        });
    }

    function updateEmail(selectElem) {
        const emailInput = selectElem.closest("tr").querySelector('input[name="email[]"]');
        const selectedOption = selectElem.options[selectElem.selectedIndex];
        const email = selectedOption.getAttribute("data-email") || "";
        emailInput.value = email;
    }

    function filterUserOptions() {
        // collect selected user_ids
        const selectedIds = Array.from(document.querySelectorAll('select[name="user_id[]"]'))
            .map(sel => sel.value)
            .filter(v => v !== "");

        // loop through all dropdowns
        document.querySelectorAll('select[name="user_id[]"]').forEach(select => {
            const currentValue = select.value;
            Array.from(select.options).forEach(option => {
                if (option.value === "" || option.value === currentValue) {
                    option.hidden = false; // always show placeholder & current selection
                } else {
                    option.hidden = selectedIds.includes(option.value);
                }
            });
        });
    }

    // Initialize datepicker for all rows
    function getTodayDate() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }

    // Initialize datepicker for all rows
    function initDatePickers() {
        $('.task-start-date, .task-end-date').flatpickr({
            dateFormat: "Y-m-d",
                allowInput: true,
                defaultDate: new Date(),
                clickOpens: true
        });
    }
    // Call on page load
    initDatePickers();
    const statusOptions = `<?php
        $optionsHtml = '<option value="">Select Status</option>';
        foreach ($statuses as $status) {
            $optionsHtml .= '<option value="'.$status['id'].'">'.htmlspecialchars($status['status_name']).'</option>';
        }
        echo $optionsHtml;
    ?>`;

    function addTaskRow() {
        const tableBody = document.getElementById("tasksTableBody");
        const rowCount = tableBody.rows.length;
        const newRow = document.createElement("tr");

        newRow.innerHTML = `
            <td>${rowCount + 1}</td>
            <td><input type="text" name="task_name[]" class="form-control" placeholder="Task Name"></td>
            <td>
                <textarea name="task_description[]" class="form-control" placeholder="Description" rows="2"></textarea>
            </td>
            <td><input type="text" name="start_date[]" class="form-control task-start-date datepicker" placeholder="Start Date" readonly></td>
            <td><input type="text" name="end_date[]" class="form-control task-end-date datepicker" placeholder="End Date" readonly></td>
            <td><input type="number" name="hour[]" class="form-control" placeholder="Hour" step="0.01" min="0"></td>
            <td>
                <select name="status_id[]" class="form-control select2">
                    ${statusOptions}
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="deleteTaskRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tableBody.appendChild(newRow);
        updateTaskSerialNumbers();

        // Initialize Flatpickr for the new date fields
        $(newRow).find('.datepicker').flatpickr({
            dateFormat: "Y-m-d",
                allowInput: true,
                defaultDate: new Date(),
                clickOpens: true
        });
        
        $(newRow).find('.select2').select2({
            width: '100%'   // ensures it matches bootstrap form-control width
        });
    }

    function deleteTaskRow(button) {
        const row = button.closest("tr");
        row.remove();
        updateTaskSerialNumbers();
    }

    function updateTaskSerialNumbers() {
        const rows = document.querySelectorAll("#tasksTableBody tr");
        rows.forEach((row, index) => {
            row.querySelector("td:first-child").innerText = index + 1;
        });
    }

    function showBillingFields() {
        const billingMethod = document.getElementById('billingMethod').value;
        const container = document.getElementById('billingFieldsContainer');
        container.innerHTML = ''; // Clear previous fields

        if (billingMethod === '1') {
            container.innerHTML = `
                <div class="col-md-6 mb-3">
                    <label class="form-label">Total Project Cost <span class="text-danger">*</span></label>
                    <div class="input-group">
                    <select class="form-select" name="currency_type" style="max-width: 100px;">
                            <option value="1">Indian Rupee(₹)</option>
                            <option value="0">US Dollar ($)</option>
                        </select>
                        <input type="number" class="form-control" name="total_project_cost" step="0.01" min="0">
                       
                    </div>
                     <span class="text-danger error-text" id="fixed_error"></span>
                </div>
            `;
        } 
        else if (billingMethod === '2') {
            container.innerHTML = `
                <div class="col-md-6 mb-3">
                    <label class="form-label">Rate Per Hour <span class="text-danger">*</span></label>
                    <div class="input-group">
                    <select class="form-select" name="currency_type" style="max-width: 100px;">
                            <option value="1">Indian Rupee(₹)</option>
                            <option value="0">US Dollar ($)</option>
                        </select>
                        <input type="number" class="form-control" name="rate_per_hour" step="0.01" min="0">
                       
                    </div>
                     <span class="text-danger error-text" id="hour_error"></span>
                </div>
            `;
        }
        else if (billingMethod === '3') {
            container.innerHTML = `
                <div class="col-12 mb-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Billing is calculated based on hourly rate of project tasks.
                    </div>
                </div>
            `;
        }
        else if (billingMethod === '4') {
            container.innerHTML = `
                <div class="col-12 mb-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Billing is calculated based on hourly rate of staff.
                    </div>
                </div>
            `;
        }
    }

    // Initialize fields on page load if a method is already selected
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('billingMethod').value) {
            showBillingFields();
        }
        
        // Initialize select2
        $('.select2').select2();
    });

    // Form validation
    $(document).ready(function () {
        $('#form').on('submit', function (e) {
            let valid = true;
            $('.error-text').text('');

            // Project name validation
            if (!$('#project_name').val().trim()) {
                $('#name_error').text('Project name is required.');
                valid = false;
            }

            // Client validation
            if ($('#client_id').val() === null || $('#client_id').val().length === 0) {
                $('#clientname_error').text('Please select at least one client.');
                valid = false;
            }

            // Billing method validation
            if (!$('#billingMethod').val()) {
                $('#method_error').text('Please select a billing method.');
                valid = false;
            }

            // Additional validation based on billing method
            const billingMethod = $('#billingMethod').val();
            if (billingMethod === '1') {
                if (!$('input[name="total_project_cost"]').val()) {
                    $('#fixed_error').text('Total project cost is required.');
                    valid = false;
                }
            } else if (billingMethod === '2') {
                if (!$('input[name="rate_per_hour"]').val()) {
                    $('#hour_error').text('Rate per hour is required.');
                    valid = false;
                }
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    });
    </script>

    <script>
// Add new client modal functionality
$(document).ready(function() {
    // When "Add New Client" is selected in the dropdown
    $('#client_id').on('select2:select', function(e) {
        if (e.params.data.id === 'add_new_client') {
            $('#addClientModal').modal('show');
            $(this).val(null).trigger('change'); // Reset the select
        }
    });

    // Image preview for modal
    $('#modal_image').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#modal_image_preview').html(`<img src="${e.target.result}" class="img-fluid rounded" alt="Preview">`);
            }
            reader.readAsDataURL(file);
        }
    });

    // Update display name when name fields change
    // Update display name when name fields change
    $('#modal_salutation').on('input', updateModalDisplayName);
});

function updateModalDisplayName() {
    const salutation = $('#modal_salutation').val();
    let displayName = '';

    if (salutation === 'Mr') displayName = 'Mr';
    else if (salutation === 'Mrs') displayName = 'Mrs';
    else if (salutation === 'Ms') displayName = 'Ms';
    else if (salutation === 'Miss') displayName = 'Miss';
    else if (salutation === 'Dr') displayName = 'Dr';

    $('#modal_display_name').val(displayName);
}   

// Contact persons table in modal
let modalContactRowCount = 0;

function addModalContactRow() {
    modalContactRowCount++;
    const tableBody = document.getElementById("modalContactTableBody");
    const newRow = document.createElement("tr");
    
    newRow.innerHTML = `
        <td>
            <select class="form-select" name="contact_salutation[]">
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Ms">Ms</option>
                <option value="Miss">Miss</option>
                <option value="Dr">Dr</option>
            </select>
        </td>
        <td><input type="text" class="form-control" name="contact_first_name[]" required></td>
        <td><input type="text" class="form-control" name="contact_last_name[]" required></td>
        <td><input type="email" class="form-control" name="contact_email[]"></td>
        <td><input type="text" class="form-control" name="contact_work_phone[]"></td>
        <td><input type="text" class="form-control" name="contact_mobile[]"></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="deleteModalContactRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tableBody.appendChild(newRow);
}

function deleteModalContactRow(btn) {
    const row = btn.closest("tr");
    row.remove();
}

// Copy billing to shipping address
function copyModalBillingToShipping() {
    $('#modal_shipping_name').val($('#modal_billing_name').val());
    $('#modal_shipping_address1').val($('#modal_billing_address1').val());
    $('#modal_shipping_address2').val($('#modal_billing_address2').val());
    $('#modal_shipping_country').val($('#modal_billing_country').val()).trigger('change');
    $('#modal_shipping_state').val($('#modal_billing_state').val()).trigger('change');
    $('#modal_shipping_city').val($('#modal_billing_city').val());
    $('#modal_shipping_pincode').val($('#modal_billing_pincode').val());
}

// AJAX functions for country/state/city dropdowns
function getModalStates(countryId, stateDropdownId) {
    if (!countryId) {
        $('#' + stateDropdownId).html('<option value="">Select State</option>');
        return;
    }
    
    $.ajax({
        url: 'ajax/get_states.php',
        type: 'POST',
        data: { country_id: countryId },
        success: function(response) {
            $('#' + stateDropdownId).html(response);
        }
    });
}

function getModalCities(stateId, cityDropdownId) {
    if (!stateId) {
        $('#' + cityDropdownId).html('<option value="">Select City</option>');
        return;
    }
    
    $.ajax({
        url: 'ajax/get_cities.php',
        type: 'POST',
        data: { state_id: stateId },
        success: function(response) {
            $('#' + cityDropdownId).html(response);
        }
    });
}

// Client form validation
$('#clientForm').on('submit', function(e) {
    let valid = true;
    $('.error-text').text('');

    // Validate required fields
    if (!$('#modal_first_name').val().trim()) {
        $('#modal_first_name_error').text('First name is required.');
        valid = false;
    }
    
    if (!$('#modal_last_name').val().trim()) {
        $('#modal_last_name_error').text('Last name is required.');
        valid = false;
    }
    
    if (!$('#modal_company_name').val().trim()) {
        $('#modal_company_name_error').text('Company name is required.');
        valid = false;
    }
    
    if (!$('#modal_email').val().trim()) {
        $('#modal_email_error').text('Email is required.');
        valid = false;
    } else if (!isValidEmail($('#modal_email').val())) {
        $('#modal_email_error').text('Please enter a valid email address.');
        valid = false;
    }
    
    if (!$('#modal_phone_number').val().trim()) {
        $('#modal_phone_number_error').text('Work number is required.');
        valid = false;
    }
    
    if (!$('#modal_business_number').val().trim()) {
        $('#modal_business_number_error').text('Mobile number is required.');
        valid = false;
    }
    
    if (!$('#modal_pan_number').val().trim()) {
        $('#modal_pan_number_error').text('PAN is required.');
        valid = false;
    }
    
    if (!$('#modal_billing_city').val()) {
        $('#modal_billing_city_error').text('City is required.');
        valid = false;
    }
    
    if (!$('#modal_shipping_city').val()) {
        $('#modal_shipping_city_error').text('City is required.');
        valid = false;
    }

    if (!$('#modal_billing_country').val()) {
        $('#modal_billing_country_error').text('Country is required.');
        valid = false;
    }
    
    if (!$('#modal_shipping_country').val()) {
        $('#modal_shipping_country_error').text('Country is required.');
        valid = false;
    }
    if (!$('#modal_billing_state').val()) {
        $('#modal_billing_state_error').text('State is required.');
        valid = false;
    }
    
    if (!$('#modal_shipping_state').val()) {
        $('#modal_shipping_state_error').text('State is required.');
        valid = false;
    }
    if (!valid) {
        e.preventDefault();
    }
});

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// After successful client submission
$('#clientForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    // Add the new client to the select2 dropdown
                    const newOption = new Option(result.client_name, result.client_id, true, true);
                    $('#client_id').append(newOption).trigger('change');
                    
                    // Close the modal
                    $('#addClientModal').modal('hide');
                    
                    // Reset the form
                    $('#clientForm')[0].reset();
                    $('#modal_image_preview').html('<i class="isax isax-image text-primary fs-24"></i>');
                    $('#modalContactTableBody').empty();
                } else {
                    // Display errors
                    $.each(result.errors, function(key, value) {
                        $('#modal_' + key + '_error').text(value);
                    });
                }
            } catch (e) {
                console.error('Error parsing response:', e);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
        }
    });
});
</script>
<script>
$(document).ready(function () {
    // Add CSS for red tab highlight
    $('head').append(`
        <style>
            .nav-link.has-error {
                color: #dc3545 !important;
                border-bottom: 2px solid #dc3545 !important;
            }
        </style>
    `);

    // ---------------- Real-time Validation ----------------
    $('#modal-bankTab input[name="bank_name"], #modal-bankTab input[name="bank_branch"], #modal-bankTab input[name="account_holder"]').on('input', function () {
        this.value = this.value.replace(/[^a-zA-Z\s]/g, ''); // only letters
        validateBankTextField(this);
    });

    $('#modal-bankTab input[name="account_number"]').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, ''); // only digits
        validateBankAccountNumber(this);
    });

    $('#modal-bankTab input[name="IFSC_code"]').on('input', function () {
        this.value = this.value.replace(/[^a-zA-Z0-9]/g, ''); // alphanumeric only
        validateBankIFSC(this);
    });

    // ---------------- Validation Functions ----------------
    function validateBankTextField(input) {
        const val = $(input).val().trim();
        const name = $(input).attr('name');
        const errorId = name + '_error';

        if (!val) {
            $('#' + errorId).text('This field is required.').addClass('error-text');
            $('#modal-bank-tab').addClass('has-error');
        } else {
            $('#' + errorId).text('');
            checkBankTabErrors();
        }
    }

    function validateBankAccountNumber(input) {
        const val = $(input).val().trim();
        const errorId = 'account_number_error';

        if (!val) {
            $('#' + errorId).text('Account number is required.');
            $('#modal-bank-tab').addClass('has-error');
        } else if (!/^\d{6,14}$/.test(val)) {
            $('#' + errorId).text('Account number must be 6–14 digits.');
            $('#modal-bank-tab').addClass('has-error');
        } else {
            $('#' + errorId).text('');
            checkBankTabErrors();
        }
    }

    function validateBankIFSC(input) {
        const val = $(input).val().trim();
        const errorId = 'ifsc_code_error';

        if (!val) {
            $('#' + errorId).text('IFSC code is required.');
            $('#modal-bank-tab').addClass('has-error');
        } else if (!/^[A-Za-z]{4}0[A-Za-z0-9]{6}$/.test(val)) {
            $('#' + errorId).text('Invalid IFSC format (e.g. SBIN0001234).');
            $('#modal-bank-tab').addClass('has-error');
        } else {
            $('#' + errorId).text('');
            checkBankTabErrors();
        }
    }

    function checkBankTabErrors() {
        const hasErrors = $('#modal-bankTab .error-text').filter(function () {
            return $(this).text().trim() !== '';
        }).length > 0;

        if (!hasErrors) $('#modal-bank-tab').removeClass('has-error');
    }

    // ---------------- On Form Submit ----------------
    $('#clientForm').on('submit', function (e) {
        let isValid = true;

        // Validate text fields
        $('#modal-bankTab input[name="bank_name"], #modal-bankTab input[name="bank_branch"], #modal-bankTab input[name="account_holder"]').each(function () {
            validateBankTextField(this);
            if ($(this).val().trim() === '') isValid = false;
        });

        // Validate account number
        validateBankAccountNumber($('#modal-bankTab input[name="account_number"]'));
        if ($('#account_number_error').text().trim() !== '') isValid = false;

        // Validate IFSC
        validateBankIFSC($('#modal-bankTab input[name="IFSC_code"]'));
        if ($('#ifsc_code_error').text().trim() !== '') isValid = false;

        // If invalid, stop submit and show Bank tab
        if (!isValid) {
            e.preventDefault();
            $('#modal-bank-tab').tab('show');

            // Scroll inside modal to first error
            setTimeout(function () {
                const firstError = $('#modal-bankTab .error-text').filter(function () {
                    return $(this).text().trim() !== '';
                }).first();

                if (firstError.length) {
                    $('.modal-body').animate({
                        scrollTop: firstError.offset().top - $('.modal-body').offset().top + $('.modal-body').scrollTop() - 100
                    }, 500);
                }
            }, 300);
        }
    });
});
</script>
<script>
$(document).ready(function () {
    // === Allow only numbers ===
    $(document).on('input', '#phone_number, #business_number, #billing_pincode, #shipping_pincode, #account_number, .contact-workphone, .contact-mobile', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // === Allow only text (no digits) ===
    $(document).on('input', '#first_name, #last_name, #company_name, #billing_name, #shipping_name, #account_holder, #bank_name, #bank_branch, [name="contact_first_name[]"], [name="contact_last_name[]"]', function () {
        this.value = this.value.replace(/[0-9]/g, '');
    });

    // === Allow only alphanumeric for IFSC ===
    $(document).on('input', '#IFSC_code', function () {
        this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
    });
});
</script>
</body>
</html>