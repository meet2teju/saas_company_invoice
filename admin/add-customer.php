<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

// Get all countries for dropdown
$country_query = "SELECT * FROM countries ORDER BY name";
$country_result = mysqli_query($conn, $country_query);

// Reset country_result pointer for the dropdown
mysqli_data_seek($country_result, 0);

// Get country codes for phone numbers
$country_codes = [
    'US' => '+1', 'IN' => '+91', 'GB' => '+44', 'CA' => '+1', 'AU' => '+61',
    'DE' => '+49', 'FR' => '+33', 'IT' => '+39', 'ES' => '+34', 'BR' => '+55',
    'CN' => '+86', 'JP' => '+81', 'KR' => '+82', 'SG' => '+65', 'MY' => '+60',
    'AE' => '+971', 'SA' => '+966', 'QA' => '+974', 'KW' => '+965', 'BH' => '+973',
    'OM' => '+968', 'ZA' => '+27', 'NG' => '+234', 'KE' => '+254', 'EG' => '+20',
    'RU' => '+7', 'TR' => '+90', 'NL' => '+31', 'BE' => '+32', 'CH' => '+41',
    'SE' => '+46', 'NO' => '+47', 'DK' => '+45', 'FI' => '+358', 'PL' => '+48'
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layouts/title-meta.php'; ?> 
    <?php include 'layouts/head-css.php'; ?>
    <style>
    .phone-input-group {
        display: flex;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        overflow: hidden;
        background: white;
        width: 100%;
    }
    .phone-input-group:focus-within {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    /* Country code select container */
    .country-code-select {
        width: 120px !important;
        min-width: 120px;
        border: none;
        border-right: 1px solid #dee2e6;
        border-radius: 0;
        background: #f8f9fa;
        flex-shrink: 0;
    }
    
    /* Select2 customization for country code */
    .country-code-select + .select2 {
        width: 120px !important;
        min-width: 120px;
        flex-shrink: 0;
    }
    
    .country-code-select + .select2 .select2-selection {
        border: none !important;
        background: #f8f9fa !important;
        height: 100% !important;
        border-radius: 0 !important;
        border-right: 1px solid #dee2e6 !important;
    }
    
    .country-code-select + .select2 .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: 12px !important;
        padding-right: 25px !important;
        color: #495057 !important;
    }
    
    .country-code-select + .select2 .select2-selection__arrow {
        height: 38px !important;
        right: 5px !important;
    }
    
    /* Phone number input */
    .phone-number-input {
        border: none;
        border-radius: 0;
        flex: 1;
        min-width: 0; /* Important for flexbox shrinking */
        padding-left: 12px;
    }
    
    .phone-number-input:focus {
        outline: none;
        box-shadow: none;
        border-color: transparent;
    }
    
    .select2-container--open .select2-dropdown {
        z-index: 1060;
    }
    
    /* Ensure proper alignment */
    .select2-container .select2-selection--single {
        height: 38px !important;
    }
</style>
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
                            <h6>Add Client</h6>
                            <!-- <a href="#" class="btn btn-outline-white d-inline-flex align-items-center"><i class="isax isax-eye me-1"></i>Preview</a> -->
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <form action="process/action_add_client.php" method="POST" enctype="multipart/form-data" id="form" >
                                <input type="hidden" name="user_id" value="<?php echo $_SESSION['crm_user_id'] ?? 1; ?>">

                                    <div class="mb-3">
                                        <div class="row gx-3">
                                            <div class="col-lg-6 col-md-6">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div  id="add_image_preview" class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0">
                                                        <i class="isax isax-image text-primary fs-24"></i>
                                                    </div>
                                                    <div class="d-inline-flex flex-column align-items-start">
                                                        <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                                            <i class="isax isax-image me-1"></i>Upload Image
                                                            <input type="file" class="form-control image-sign" name="customer_image" id="add_image" accept="image/*" multiple="">
                                                        </div>
                                                        <span id="add_image_error" class="text-danger error-text"></span>
                                                        <span class="text-gray-9">JPG or PNG format, not exceeding 5MB.</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
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
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Salutation<span class="text-danger ms-1">*</span></label>
                                                    <select class="select" name="salutation" id="salutation" onchange="updateDisplayName()">
                                                        <option value="Mr">Mr</option>
                                                        <option value="Mrs">Mrs</option>
                                                        <option value="Ms">Ms</option>
                                                        <option value="Miss">Miss</option>
                                                        <option value="Dr">Dr</option>
                                                    </select>
                                                    <span id="salutation_error" class="text-danger error-text"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">First Name <span class="text-danger ms-1">*</span></label>
                                                    <input type="text" class="form-control" name="first_name" id="first_name">
                                                    <span id="first_name_error" class="text-danger error-text"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Last Name <span class="text-danger ms-1">*</span></label>
                                                    <input type="text" class="form-control" name="last_name" id="last_name">
                                                    <span id="last_name_error" class="text-danger error-text"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Company Name</label>
                                                    <input type="text" class="form-control" name="company_name" id="company_name">
                                                    <span id="company_name_error" class="text-danger error-text"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Display Name </label>
                                                    <input type="text" class="form-control" name="display_name" id="display_name" readonly>
                                                    <span id="display_name_error" class="text-danger error-text"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email" id="email">
                                                    <span id="email_error" class="text-danger error-text"></span>
                                                </div>
                                            </div>
                                           <div class="col-lg-4 col-md-6">
    <div class="mb-3">
        <label class="form-label">Work Number </label>
        <div class="phone-input-group">
            <select class="country-code-select select" name="work_country_code" id="work_country_code">
                <?php foreach ($country_codes as $country => $code): ?>
                    <option value="<?= $code ?>" <?= $country === 'IN' ? 'selected' : '' ?> data-country="<?= $country ?>">
                        <?= $code ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" class="form-control phone-number-input" name="phone_number" id="phone_number" placeholder="Phone Number">
        </div>
        <span id="phone_number_error" class="text-danger error-text">Please enter a valid number (7-15 digits)</span>
    </div>
</div>
<div class="col-lg-4 col-md-6">
    <div class="mb-3">
        <label class="form-label">Mobile Number </label>
        <div class="phone-input-group">
            <select class="country-code-select select" name="mobile_country_code" id="mobile_country_code">
                <?php foreach ($country_codes as $country => $code): ?>
                    <option value="<?= $code ?>" <?= $country === 'IN' ? 'selected' : '' ?> data-country="<?= $country ?>">
                        <?= $code ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" class="form-control phone-number-input" name="business_number" id="business_number" placeholder="Mobile Number">
        </div>
        <span id="business_number_error" class="text-danger error-text">Please enter a valid number (7-15 digits)</span>
    </div>
</div>
                                        <!-- Tabs Start -->
                                        <ul class="nav nav-tabs mb-3" id="customerTab" role="tablist">
                                              <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#addressTab" type="button" role="tab">Address</button>
                                            </li>
                                            
                                          
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contactTab" type="button" role="tab">Contact Persons</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bankTab" type="button" role="tab">Banking Details</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="remarks-tab" data-bs-toggle="tab" data-bs-target="#remarksTab" type="button" role="tab">Remarks</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="other-tab" data-bs-toggle="tab" data-bs-target="#otherTab" type="button" role="tab">Other Details</button>
                                            </li>
                                        </ul>

                                        <div class="tab-content" id="customerTabContent">
                                            <!-- Other Details Tab -->
                                            <div class="tab-pane fade show active" id="otherTab" role="tabpanel">
                                                <div class="row gx-3">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">PAN Card</label>
                                                        <input type="text" class="form-control" name="pan_number" id="pan_number">
                                                        <span id="pan_number_error" class="text-danger error-text"></span>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Currency</label>
                                                        <select class="form-select" name="currency" id="currency">
                                                            <option value="INR">Indian Rupee&nbsp;(₹)</option>
                                                            <option value="$">US Dollar&nbsp;($)</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">VAT/GST Number</label>
                                                        <input type="text" class="form-control" name="gst_number" id="gst_number">
                                                        <span id="gst_number_error" class="text-danger error-text"></span>
                                                    </div>

                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Documents</label>
                                                        <input type="file" class="form-control" name="documents[]" id="documents" multiple>
                                                        <span id="documents_error" class="text-danger error-text"></span>
                                                    </div>
                                                </div>
                                                <a href="#" class="text-primary mt-2 d-inline-block" data-bs-toggle="collapse" data-bs-target="#moreDetails">
                                                        Add more details
                                                    </a>

                                                    <!-- Hidden section -->
                                                    <div class="collapse mt-3" id="moreDetails">
                                                        <div class="row gx-3">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Website URL</label>
                                                                <input type="url" class="form-control" name="website_url" id="website_url" placeholder="https://">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Department</label>
                                                                <input type="text" class="form-control" name="department" id="department">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Designation</label>
                                                                <input type="text" class="form-control" name="designation" id="designation">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Twitter</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-x-twitter"></i></span>
                                                                    <input type="text" class="form-control" name="twitter"  id="twitter" placeholder="http://www.twitter.com/">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Skype Name/Number</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-skype"></i></span>
                                                                    <input type="text" class="form-control" name="skype_name_number" id="skype_name_number">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Facebook</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                                                                    <input type="text" class="form-control" name="facebook" id="facebook" placeholder="http://www.facebook.com/">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>

                                            <!-- Address Tab -->
                                            <div class="tab-pane fade" id="addressTab" role="tabpanel">
                                                <div class="row gx-5">
                                                    <!-- Billing Address -->
                                                    <div class="col-md-6">
                                                        <h6 class="mb-3">Billing Address</h6>
                                                        <div class="row">
                                                            <div class="col-12 mb-3">
                                                                <label class="form-label">Name</label>
                                                                <input type="text" id="billing_name" class="form-control" name="billing_name">
                                                            </div>
                                                            <div class="col-12 mb-3">
                                                                <label class="form-label">Address Line 1</label>
                                                                <input type="text" id="billing_address1" class="form-control" name="billing_address1">
                                                            </div>
                                                            <div class="col-12 mb-3">
                                                                <label class="form-label">Address Line 2</label>
                                                                <input type="text" id="billing_address2" class="form-control" name="billing_address2">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Country</label>
                                                                <select class="select" id="billing_country" name="billing_country" onchange="getStates(this.value, 'billing_state')">
                                                                     <option value="">Select Country</option>
                                                                    <?php 
                                                                    mysqli_data_seek($country_result, 0);
                                                                    while ($country = mysqli_fetch_assoc($country_result)) {
                                                                        echo "<option value='{$country['id']}'>{$country['name']}</option>";
                                                                    } ?>
                                                                </select>
                                                                <!-- <span id="billing_country_error" class="text-danger error-text"></span> -->
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">State</label>
                                                                <select class="select" id="billing_state" name="billing_state" onchange="getCities(this.value, 'billing_city')">
                                                                    <option value="">Select State</option>
                                                                </select>
                                                                <!-- <span id="billing_state_error" class="text-danger error-text"></span> -->
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">City</label>
                                                                <select class="select" id="billing_city" name="billing_city">
                                                                    <option value="">Select City</option>
                                                                </select>
                                                                <!-- <span id="billing_city_error" class="text-danger error-text"></span> -->
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Pincode</label>
                                                                <input type="text" class="form-control" id="billing_pincode" name="billing_pincode">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Shipping Address -->
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                                            <h6>Shipping Address</h6>
                                                     <a href="javascript:void(0);" onclick="copyBillingToShipping()" class="text-primary text-decoration-underline fs-13">
                                                     <i class="isax isax-document-copy me-1"></i>Copy From Billing</a>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12 mb-3">
                                                                <label class="form-label">Name</label>
                                                                <input type="text" class="form-control" id="shipping_name" name="shipping_name">
                                                            </div>
                                                            <div class="col-12 mb-3">
                                                                <label class="form-label">Address Line 1</label>
                                                                <input type="text" class="form-control" id="shipping_address1" name="shipping_address1">
                                                            </div>
                                                            <div class="col-12 mb-3">
                                                                <label class="form-label">Address Line 2</label>
                                                                <input type="text" class="form-control" id="shipping_address2" name="shipping_address2">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Country</label>
                                                                <select class="select" id="shipping_country" name="shipping_country" onchange="getStates(this.value, 'shipping_state')">
                                                                    <option value="">Select Country</option>
                                                                    <?php 
                                                                    mysqli_data_seek($country_result, 0);
                                                                    while ($country = mysqli_fetch_assoc($country_result)) {
                                                                        echo "<option value='{$country['id']}'>{$country['name']}</option>";
                                                                    } ?>
                                                                </select>
                                                                <!-- <span id="shipping_country_error" class="text-danger error-text"></span> -->
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">State</label>
                                                                <select class="select" id="shipping_state" name="shipping_state" onchange="getCities(this.value, 'shipping_city')">
                                                                    <option value="">Select State</option>
                                                                </select>
                                                                <!-- <span id="shipping_state_error" class="text-danger error-text"></span> -->
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">City</label>
                                                                <select class="select" id="shipping_city" name="shipping_city">
                                                                    <option value="">Select City</option>
                                                                </select>
                                                                <!-- <span id="shipping_city_error" class="text-danger error-text"></span> -->
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Pincode</label>
                                                                <input type="text" class="form-control" id="shipping_pincode" name="shipping_pincode">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Contact Persons Tab -->
                                            <div class="tab-pane fade" id="contactTab" role="tabpanel">
                                                <h6 class="mb-3">Contact Persons</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle" id="contactTable">
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
                                                        <tbody id="contactTableBody">
                                                            <!-- Rows will be added here dynamically -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <button type="button" class="btn btn-outline-primary mt-2 mb-3" onclick="addContactRow()">
                                                    <i class="isax isax-add"></i> Add Contact Person
                                                </button>
                                            </div>

                                            <!-- Bank Details Tab -->
                                         <div class="tab-pane fade" id="bankTab" role="tabpanel">
                                                <h6 class="mb-3">Banking Details</h6>
                                                <div class="row gx-3">
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">Bank Name</label>
                                                        <input type="text" class="form-control" name="bank_name" >
                                                         <span id="bank_name_error" class="text-danger error-text"></span>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">Branch</label>
                                                        <input type="text" class="form-control" name="bank_branch" >
                                                                <span id="bank_branch_error" class="text-danger error-text"></span>                                                   
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">Account Holder</label>
                                                        <input type="text" class="form-control" name="account_holder">
                                                   <span id="account_holder_error" class="text-danger error-text"></span>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">Account Number</label>
                                                        <input type="text" class="form-control" name="account_number">
                                                          <span id="account_number_error" class="text-danger error-text"></span>

                                                    </div>
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">IFSC</label>
                                                        <input type="text" class="form-control" name="IFSC_code" >
                                                       <span id="ifsc_code_error" class="text-danger error-text"></span>

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Remarks Tab -->
                                            <div class="tab-pane fade" id="remarksTab" role="tabpanel">
                                                <h6 class="mb-3">Remarks (For Internal Use)</h6>
                                                <div class="mb-3">
                                                    <textarea class="form-control" rows="5" name="remark" placeholder="Enter any internal remarks about this customer"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tabs End -->

                                     <div class="d-flex align-items-center justify-content-between pt-4 border-top">
                                        <a href="customers.php" class="btn btn-outline-white">Cancel</a>
                                        <button type="submit" class="btn btn-primary" name="submit">Create</button>
                                    </div>

                                </form>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                </div><!-- end col -->
            </div><!-- end row -->
        </div>

        <?php include 'layouts/footer.php'; ?>

    </div>

</div>

<?php include 'layouts/vendor-scripts.php'; ?>

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

    // Initialize country code dropdowns
    $('.country-code-select').select2({
        width: '100%',
        minimumResultsForSearch: 6,
        dropdownParent: $('.phone-input-group').parent(),
        templateResult: formatCountryCode,
        templateSelection: formatCountryCode
    });

    // Add CSS for error indicators
    $('head').append(`
        <style>
            .nav-link.has-error {
                color: #dc3545 !important;
                border-bottom: 2px solid #dc3545 !important;
            }
            .error-tab-indicator {
                display: inline-block;
                width: 8px;
                height: 8px;
                background-color: #dc3545;
                border-radius: 50%;
                margin-left: 5px;
            }
        </style>
    `);

    // Form Validation
    $('#form').on('submit', function(e) {
        let isValid = true;
        $('.error-text').text('');
        $('.is-invalid').removeClass('is-invalid');
          $('.nav-link').removeClass('has-error'); // reset previous highlights

        // Required fields validation
        const requiredFields = [
            {name: 'first_name', errorId: 'first_name_error', message: 'First name is required', tab: 'otherTab'},
            {name: 'last_name', errorId: 'last_name_error', message: 'Last name is required', tab: 'otherTab'},
            // {name: 'display_name', errorId: 'display_name_error', message: 'Display name is required', tab: 'otherTab'},
            // {name: 'billing_city', errorId: 'billing_city_error', message: 'Billing city is required', tab: 'addressTab'},
            // {name: 'shipping_city', errorId: 'shipping_city_error', message: 'Shipping city is required', tab: 'addressTab'},
            // {name: 'billing_country', errorId: 'billing_country_error', message: 'Billing country is required', tab: 'addressTab'},
            // {name: 'shipping_country', errorId: 'shipping_country_error', message: 'Shipping country is required', tab: 'addressTab'},
            // {name: 'billing_state', errorId: 'billing_state_error', message: 'Billing state is required', tab: 'addressTab'},
            // {name: 'shipping_state', errorId: 'shipping_state_error', message: 'Shipping state is required', tab: 'addressTab'},
            // {name: 'salutation', errorId: 'salutation_error', message: 'Salutation is required', tab: 'otherTab'}
        ];

        requiredFields.forEach(field => {
            const value = $(`[name="${field.name}"]`).val();
            if (!value) {
                $(`#${field.errorId}`).text(field.message);
                isValid = false;
                // Mark the tab as having errors
                $(`[data-bs-target="#${field.tab}"]`).addClass('has-error');
            }
        });

        // Email format validation
        const email = $('[name="email"]').val().trim();
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#email_error').text('Please enter a valid email address');
            isValid = false;
            $(`[data-bs-target="#otherTab"]`).addClass('has-error');
        }

        // PAN format validation
        const pan = $('[name="pan_number"]').val().trim();
        if (pan && !/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(pan)) {
            $('#pan_number_error').text('Invalid PAN format (e.g. AAAAA9999A)');
            isValid = false;
            $(`[data-bs-target="#otherTab"]`).addClass('has-error');
        }

        // Phone number validation with country code
        const workPhone = $('[name="phone_number"]').val().trim();
        if (workPhone && !/^[0-9]{7,15}$/.test(workPhone)) {
            $('#phone_number_error').text('Please enter a valid phone number (7-15 digits)');
            isValid = false;
            $(`[data-bs-target="#otherTab"]`).addClass('has-error');
        }

        // Mobile number validation with country code
        const mobile = $('[name="business_number"]').val().trim();
        if (mobile && !/^[0-9]{7,15}$/.test(mobile)) {
            $('#business_number_error').text('Please enter a valid mobile number (7-15 digits)');
            isValid = false;
            $(`[data-bs-target="#otherTab"]`).addClass('has-error');
        }

        // Validate contact persons if any exist
        $('[name="contact_email[]"]').each(function(index) {
            const contactEmail = $(this).val().trim();
            if (contactEmail && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(contactEmail)) {
                $(this).next('.invalid-feedback').remove();
                $(this).after('<div class="invalid-feedback">Please enter a valid email address</div>');
                isValid = false;
                $(`[data-bs-target="#contactTab"]`).addClass('has-error');
            }
        });

        // Document type check
        $('#documents_error').text('');
        const files = $('#documents')[0]?.files || [];
        const allowedExtensions = ['pdf', 'xls', 'xlsx', 'csv'];

        for (let i = 0; i < files.length; i++) {
            const fileName = files[i].name.toLowerCase();
            const ext = fileName.split('.').pop();
            if (!allowedExtensions.includes(ext)) {
                $('#documents_error').text('Only PDF, Excel, or CSV files are allowed.');
                isValid = false;
                $(`[data-bs-target="#otherTab"]`).addClass('has-error');
                break;
            }
        }

        // Scroll to first error if validation fails
        if (!isValid) {
            e.preventDefault();
            
            // Find the first tab with errors and activate it
            const firstErrorTab = $('.nav-link.has-error').first();
            if (firstErrorTab.length) {
                firstErrorTab.tab('show');
                
                // Wait for tab to be shown, then scroll to error
                firstErrorTab.on('shown.bs.tab', function() {
                    const firstError = $('.error-text').filter(function() {
                        return $(this).text().length > 0;
                    }).first();
                    
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                    }
                    
                    // Remove the event handler after execution
                    firstErrorTab.off('shown.bs.tab');
                });
            } else {
                // Fallback: scroll to first error
                const firstError = $('.error-text').filter(function() {
                    return $(this).text().length > 0;
                }).first();
                
                if (firstError.length) {
                    $('html, body').animate({
                        scrollTop: firstError.offset().top - 100
                    }, 500);
                }
            }
        }
    });

    // Real-time validation for PAN
    $('[name="pan_number"]').on('input', function() {
        const pan = $(this).val().trim();
        if (pan && !/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(pan)) {
            $('#pan_number_error').text('Invalid PAN format (e.g. AAAAA9999A)');
            $(`[data-bs-target="#otherTab"]`).addClass('has-error');
        } else {
            $('#pan_number_error').text('');
            $(`[data-bs-target="#otherTab"]`).removeClass('has-error');
        }
    });

    // Real-time validation for email
    $('[name="email"]').on('input', function() {
        const email = $(this).val().trim();
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#email_error').text('Please enter a valid email address');
            $(`[data-bs-target="#otherTab"]`).addClass('has-error');
        } else {
            $('#email_error').text('');
            $(`[data-bs-target="#otherTab"]`).removeClass('has-error');
        }
    });

    // Real-time validation for phone numbers with country codes
    $('[name="phone_number"], [name="business_number"]').on('input', function() {
        const number = $(this).val().trim();
        const fieldName = $(this).attr('name');
        const errorId = `${fieldName}_error`;
        
        if (number && !/^[0-9]{7,15}$/.test(number)) {
            $(`#${errorId}`).text('Please enter a valid number (7-15 digits)');
            $(`[data-bs-target="#otherTab"]`).addClass('has-error');
        } else {
            $(`#${errorId}`).text('');
            $(`[data-bs-target="#otherTab"]`).removeClass('has-error');
        }
    });

    // Real-time validation for city fields
    $('#billing_city, #shipping_city').on('change', function() {
        const fieldId = $(this).attr('id');
        const value = $(this).val();
        
        if (!value) {
            $(`#${fieldId}_error`).text(`${fieldId.replace('_', ' ')} is required`);
            $(`[data-bs-target="#addressTab"]`).addClass('has-error');
        } else {
            $(`#${fieldId}_error`).text('');
            $(`[data-bs-target="#addressTab"]`).removeClass('has-error');
        }
    });

    // Validate before switching tabs
    $('button[data-bs-toggle="tab"]').on('click', function(e) {
        const currentTab = $('.nav-link.active').attr('id').replace('-tab', '');
        const targetTab = $(this).data('bs-target').replace('#', '');
        
        // Validate current tab before switching away
        if (!validateTab(currentTab)) {
            e.preventDefault();
            
            // Show error message
            const firstError = $(`#${currentTab} .error-text`).filter(function() {
                return $(this).text().length > 0;
            }).first();
            
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }
    });

    // Initialize select2 and other components
    $('.select').select2();
    addContactRow();
});

// Function to validate a specific tab
function validateTab(tabId) {
    let isValid = true;
    
    if (tabId === 'otherTab') {
        // Validate other tab fields
        const requiredFields = ['first_name', 'last_name', 'company_name', 'display_name', 'email', 'phone_number', 'business_number', 'pan_number', 'salutation'];
        
        requiredFields.forEach(field => {
            const value = $(`[name="${field}"]`).val();
            if (!value) {
                $(`#${field}_error`).text(`${field.replace('_', ' ')} is required`);
                isValid = false;
            }
        });
    } else if (tabId === 'addressTab') {
        // Validate address tab fields
        const billingCity = $('#billing_city').val();
        const shippingCity = $('#shipping_city').val();
        const billingcountry = $('#billing_country').val();
        const shippingcountry = $('#shipping_country').val();
        const billingstate = $('#billing_state').val();
        const shippingstate = $('#shipping_state').val();

        if (!billingCity) {
            $('#billing_city_error').text('Billing city is required');
            isValid = false;
        }
        
        if (!shippingCity) {
            $('#shipping_city_error').text('Shipping city is required');
            isValid = false;
        }
        if (!billingcountry) {
            $('#billing_country_error').text('Billing country is required');
            isValid = false;
        }
        
        if (!shippingcountry) {
            $('#shipping_country_error').text('Shipping country is required');
            isValid = false;
        }
        if (!billingstate) {
            $('#billing_state_error').text('Billing state is required');
            isValid = false;
        }
        
        if (!shippingstate) {
            $('#shipping_state_error').text('Shipping state is required');
            isValid = false;
        }
    }
    
    return isValid;
}

// Format country code display
function formatCountryCode(state) {
    if (!state.id) {
        return state.text;
    }
    return state.text;
}

// Update phone validation to handle country codes
function validatePhoneWithCountryCode(phoneInput, countryCodeSelect, errorSelector) {
    const phone = phoneInput.val().trim();
    const countryCode = countryCodeSelect.val();
    
    if (phone && !/^[0-9]{7,15}$/.test(phone)) {
        $(errorSelector).text('Please enter a valid phone number (7-15 digits)');
        return false;
    } else {
        $(errorSelector).text('');
        return true;
    }
}

// Get full phone number with country code
function getFullPhoneNumber(countryCodeSelect, phoneInput) {
    const countryCode = countryCodeSelect.val();
    const phone = phoneInput.val().trim();
    return countryCode + phone;
}

// Address related functions
function getStates(countryId, targetDropdown) {
    if (!countryId) {
        $('#' + targetDropdown).html('<option value="">Select State</option>').trigger('change');
        $('#' + targetDropdown.replace('state', 'city')).html('<option value="">Select City</option>').trigger('change');
        return;
    }
    
    $.ajax({
        url: 'process/action_get_state.php',
        type: 'POST',
        data: {datapost: countryId},
        success: function(result) {
            $('#' + targetDropdown).html('<option value="">Select State</option>' + result).trigger('change');
        },
        error: function(xhr, status, error) {
            console.error("Error fetching states:", error);
        }
    });
}

function getCities(stateId, targetDropdown) {
    if (!stateId) {
        $('#' + targetDropdown).html('<option value="">Select City</option>').trigger('change');
        return;
    }
    
    $.ajax({
        url: 'process/action_get_city.php',
        type: 'POST',
        data: {datapost: stateId},
        success: function(result) {
            $('#' + targetDropdown).html('<option value="">Select City</option>' + result).trigger('change');
        },
        error: function(xhr, status, error) {
            console.error("Error fetching cities:", error);
        }
    });
}

function copyBillingToShipping() {
    try {
        // Copy basic fields
        $('#shipping_name').val($('#billing_name').val());
        $('#shipping_address1').val($('#billing_address1').val());
        $('#shipping_address2').val($('#billing_address2').val());
        $('#shipping_pincode').val($('#billing_pincode').val());
        
        // Copy country
        const countryVal = $('#billing_country').val();
        if (!countryVal) return;
        
        $('#shipping_country').val(countryVal).trigger('change');
        
        // Wait for states to load
        setTimeout(function() {
            // Copy state
            const stateVal = $('#billing_state').val();
            if (!stateVal) return;
            
            $('#shipping_state').val(stateVal).trigger('change');
            
            // Wait for cities to load
            setTimeout(function() {
                // Copy city
                const cityVal = $('#billing_city').val();
                if (cityVal) {
                    $('#shipping_city').val(cityVal).trigger('change');
                }
            }, 500);
        }, 500);
    } catch (error) {
        console.error("Error copying billing to shipping:", error);
    }
}

// Contact persons functions
function addContactRow() {
    const tbody = document.getElementById("contactTableBody");
    const rowId = Date.now();

    const row = document.createElement("tr");
    row.dataset.rowId = rowId;

    const isFirst = tbody.children.length === 0;
    if (isFirst) row.dataset.isFirst = "true";

    row.innerHTML = `
        <td>
            <select class="form-select" name="contact_salutation[]">
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Ms">Ms</option>
                <option value="Dr">Dr</option>
            </select>
        </td>
        <td><input type="text" class="form-control" name="contact_first_name[]" placeholder="First Name"></td>
        <td><input type="text" class="form-control" name="contact_last_name[]" placeholder="Last Name"></td>
        <td>
            <input type="email" class="form-control contact-email" name="contact_email[]" placeholder="Email">
            <small class="text-danger error-email"></small>
        </td>
        <td>
            <input type="text" class="form-control contact-workphone" name="contact_work_phone[]" placeholder="Work Phone">
            <small class="text-danger error-workphone"></small>
        </td>
        <td>
            <input type="text" class="form-control contact-mobile" name="contact_mobile[]" placeholder="Mobile">
            <small class="text-danger error-mobile"></small>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-light" onclick="toggleExtraFields(this, ${rowId})">
                <i class="isax isax-more"></i>
            </button>
            <button type="button" class="btn btn-sm btn-light text-danger" onclick="removeRow(this)">
                <i class="isax isax-trash"></i>
            </button>
        </td>
    `;

    const extraRow = document.createElement("tr");
    extraRow.classList.add("extra-fields-row", "d-none");
    extraRow.dataset.rowId = rowId;
    extraRow.innerHTML = `
        <td colspan="7">
            <div class="p-3 bg-light rounded">
                <div class="row gx-3">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Skype Name/Number</label>
                        <input type="text" class="form-control" name="contact_skype[]">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Designation</label>
                        <input type="text" class="form-control" name="contact_designation[]">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Department</label>
                        <input type="text" class="form-control" name="contact_department[]">
                    </div>
                </div>
            </div>
        </td>
    `;

    tbody.appendChild(row);
    tbody.appendChild(extraRow);

    // ✅ Attach validation to new inputs
    row.querySelector(".contact-email").addEventListener("input", validateEmail);
    row.querySelector(".contact-workphone").addEventListener("input", validateWorkPhone);
    row.querySelector(".contact-mobile").addEventListener("input", validateMobile);
}

// ---------------- VALIDATION FUNCTIONS ----------------

// ✅ Email validation
function validateEmail(e) {
    const email = e.target.value.trim();
    const errorSpan = e.target.closest("td").querySelector(".error-email");
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email === "") {
        errorSpan.textContent = "";
    } else if (!regex.test(email)) {
        errorSpan.textContent = "Invalid email format";
    } else {
        errorSpan.textContent = "";
    }
}

// ✅ Work Phone validation
function validateWorkPhone(e) {
    const phone = e.target.value.trim();
    const errorSpan = e.target.closest("td").querySelector(".error-workphone");

    if (phone === "") {
        errorSpan.textContent = "";
    } else if (!/^[0-9]{7,10}$/.test(phone)) {
        errorSpan.textContent = "Work Phone must be 10 digits";
    } else {
        errorSpan.textContent = "";
    }
}

// ✅ Mobile validation
function validateMobile(e) {
    const phone = e.target.value.trim();
    const errorSpan = e.target.closest("td").querySelector(".error-mobile");

    if (phone === "") {
        errorSpan.textContent = "";
    } else if (!/^[0-9]{10}$/.test(phone)) { // 👈 Example: exactly 10 digits
        errorSpan.textContent = "Mobile must be exactly 10 digits";
    } else {
        errorSpan.textContent = "";
    }
}

function toggleExtraFields(button, rowId) {
    const extraRow = document.querySelector(`tr.extra-fields-row[data-row-id="${rowId}"]`);
    if (extraRow) {
        extraRow.classList.toggle("d-none");
        if (!extraRow.classList.contains("d-none")) {
            extraRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
}

function removeRow(button) {
    const row = button.closest("tr");
    const rowId = row.dataset.rowId;

    if (row.dataset.isFirst === "true") {
        return;
    }

    const extraRow = document.querySelector(`tr.extra-fields-row[data-row-id="${rowId}"]`);
    if (row) row.remove();
    if (extraRow) extraRow.remove();
}

// Display name function
function updateDisplayName() {
    const salutation = document.getElementById('salutation').value;
    let displayName = '';

    if (salutation === 'Mr') displayName = 'Mr';
    else if (salutation === 'Mrs') displayName = 'Mrs';
    else if (salutation === 'Ms') displayName = 'Ms';
    else if (salutation === 'Miss') displayName = 'Miss';
    else if (salutation === 'Dr') displayName = 'Dr';

    document.getElementById('display_name').value = displayName;
}

// Initialize display name when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateDisplayName();
});
</script>

<script>
$(document).ready(function () {
    $('#add_image').change(function () {
        const file = this.files[0];
        if (file) {
            // Validate file size
            if (file.size > 5 * 1024 * 1024) {
                $('#add_image_error').text('File size must be less than 5MB');
                $(this).val('');
                $('#add_image_preview').html('<i class="isax isax-image text-primary fs-24"></i>');
                return;
            }

            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                $('#add_image_error').text('Only JPG or PNG images are allowed');
                $(this).val('');
                $('#add_image_preview').html('<i class="isax isax-image text-primary fs-24"></i>');
                return;
            }

            // Display preview
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#add_image_preview').html(`
                    <img src="${e.target.result}" class="avatar avatar-xxl border rounded" alt="Preview">
                `);
                $('#add_image_error').text('');
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
</body>
</html>