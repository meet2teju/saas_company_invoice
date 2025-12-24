<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

// Get current organization ID from session
$org_id = $_SESSION['org_id'] ?? 1;
$user_id = $_SESSION['crm_user_id'] ?? 0;

// CORRECTED: Use the same session variable as in your reference code
$user_role_id = $_SESSION['role_id'] ?? 0; // Changed from crm_user_role_id to role_id

// Check if user is admin (role_id = 1)
$is_admin = ($user_role_id == 1);

// Get company information for the current organization
$company_info = [];
$company_query = "SELECT * FROM company_info WHERE org_id = '$org_id' LIMIT 1";
$company_result = mysqli_query($conn, $company_query);
if (mysqli_num_rows($company_result) > 0) {
    $company_info = mysqli_fetch_assoc($company_result);
}

// Get all countries for dropdown
$country_query = "SELECT * FROM countries ORDER BY name";
$country_result = mysqli_query($conn, $country_query);

// Get currency options
$currency_query = "SELECT * FROM currency ORDER BY currency_name";
$currency_result = mysqli_query($conn, $currency_query);

// Get country codes for phone numbers from database
$country_codes_query = "SELECT id, name, phonecode, iso2 FROM countries ORDER BY name";
$country_codes_result = mysqli_query($conn, $country_codes_query);

// Get states if country is selected
$states = [];
if (!empty($company_info['country_id'])) {
    $state_query = "SELECT * FROM states WHERE country_id = " . $company_info['country_id'] . " ORDER BY name";
    $state_result = mysqli_query($conn, $state_query);
    while ($state = mysqli_fetch_assoc($state_result)) {
        $states[] = $state;
    }
}

// Get cities if state is selected
$cities = [];
if (!empty($company_info['state_id'])) {
    $city_query = "SELECT * FROM cities WHERE state_id = " . $company_info['state_id'] . " ORDER BY name";
    $city_result = mysqli_query($conn, $city_query);
    while ($city = mysqli_fetch_assoc($city_result)) {
        $cities[] = $city;
    }
}
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
        width: 140px !important;
        min-width: 140px;
        border: none;
        border-right: 1px solid #dee2e6;
        border-radius: 0;
        background: #f8f9fa;
        flex-shrink: 0;
    }
    
    /* Select2 customization for country code */
    .country-code-select + .select2 {
        width: 140px !important;
        min-width: 140px;
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
    
    /* Style for readonly/disabled fields */
    .readonly-field {
        background-color: #f8f9fa !important;
        cursor: not-allowed !important;
        opacity: 0.8;
    }
</style>
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
                <!-- start row -->
                <div class="row justify-content-center mb-3">
                    <div class="col-lg-12">
                        <!-- start row -->
                        <div class=" row settings-wrapper d-flex">
                            <?php include 'layouts/settings-sidebar.php'; ?>

                            <div class="col-xl-9 col-lg-8">
                                <div class="mb-3 pb-3 border-bottom">
                                    <h6 class="fw-bold mb-0">Company Profile</h6>
                                </div>
                                <!-- Flash message -->
                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                                <?php elseif (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                                <?php endif; ?>
                                
                                <?php if (!$is_admin): ?>
                                    <div class="alert alert-warning">
                                        <i class="fa fa-lock me-2"></i>You have read-only access. Only administrators can edit company information.
                                    </div>
                                <?php endif; ?>
                                
                                <form id="companyForm" action="process/action_company_profile.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?= !empty($company_info['id']) ? $company_info['id'] : '' ?>">
                                    <input type="hidden" name="org_id" value="<?= $org_id ?>">
                                    
                                    <div class="border-bottom mb-3">
                                        <div class="card-title-head">
                                            <h6 class="fs-16 fw-semibold mb-3 d-flex align-items-center">
                                                <span class="fs-16 me-2 p-1 rounded bg-dark text-white d-inline-flex align-items-center justify-content-center"><i class="isax isax-info-circle"></i></span> 
                                                General Information
                                            </h6>
                                        </div>

                                        <!-- start row -->
                                        <div class="row">
                                            <div class="col-xl-6 col-lg-6 col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Company Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="name" id="name" class="form-control <?= !$is_admin ? 'readonly-field' : '' ?>" 
                                                           value="<?= !empty($company_info['name']) ? $company_info['name'] : '' ?>" 
                                                           <?= !$is_admin ? 'readonly' : '' ?>>
                                                    <span id="company_name_error" class="text-danger error-text"></span>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-xl-6 col-lg-6 col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                      Company Email Address <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="email" id="email" name="email" class="form-control <?= !$is_admin ? 'readonly-field' : '' ?>" 
                                                           value="<?= !empty($company_info['email']) ? $company_info['email'] : '' ?>" 
                                                           <?= !$is_admin ? 'readonly' : '' ?>>
                                                    <span id="email_error" class="text-danger error-text"></span>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-md-6">
    <div class="mb-3">
        <label class="form-label">Company Mobile Number</label>
        <div class="phone-input-group">
            <select class="country-code-select select" name="mobile_country_code" id="mobile_country_code" <?= !$is_admin ? 'disabled' : '' ?>>
                <?php 
                $mobile_country_code = $company_info['mobile_country_code'] ?? '+91';
                // Reset pointer for country codes
                mysqli_data_seek($country_codes_result, 0);
                while ($country = mysqli_fetch_assoc($country_codes_result)): 
                    $phonecode = $country['phonecode'];
                    if (!empty($phonecode) && $phonecode[0] !== '+') {
                        $phonecode = '+' . $phonecode;
                    }
                    $selected = ($phonecode == $mobile_country_code) ? 'selected' : '';
                ?>
                    <option value="<?= $phonecode ?>" <?= $selected ?> data-country="<?= $country['iso2'] ?>">
                        <?= $phonecode ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="text" class="form-control phone-number-input <?= !$is_admin ? 'readonly-field' : '' ?>" 
                   name="mobile_number" id="mobile_number" 
                   value="<?= !empty($company_info['mobile_number']) ? $company_info['mobile_number'] : '' ?>" 
                   placeholder="Mobile Number" 
                   maxlength="15"
                   <?= !$is_admin ? 'readonly' : '' ?>>
        </div>
        <span id="mobile_number_error" class="text-danger error-text"></span>
    </div>
</div><!-- end col -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Company PAN Number 
                                                    </label>
                                                    <input type="text" name="pan_number" id="pan_number" class="form-control <?= !$is_admin ? 'readonly-field' : '' ?>" 
                                                           value="<?= !empty($company_info['pan_number']) ? $company_info['pan_number'] : '' ?>" 
                                                           <?= !$is_admin ? 'readonly' : '' ?>>
                                                </div>
                                            </div><!-- end col -->
                                             <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                       Company GST Number 
                                                    </label>
                                                    <input type="text" id="gst_number" name="gst_number" class="form-control <?= !$is_admin ? 'readonly-field' : '' ?>" 
                                                           value="<?= !empty($company_info['gst_number']) ? $company_info['gst_number'] : '' ?>" 
                                                           <?= !$is_admin ? 'readonly' : '' ?>>
                                                </div>
                                           </div>
                                            <!-- end col -->
                                            <div class="col-md-6">
    <div class="mb-3">
        <label class="form-label">
           Company Currency<span class="text-danger">*</span>
        </label>
        <select class="select2" id="currency" name="currency_symbol_id" <?= !$is_admin ? 'disabled' : '' ?>>
            <option value="">Select Currency</option>
            <?php 
            mysqli_data_seek($currency_result, 0);
            while ($currency = mysqli_fetch_assoc($currency_result)) {
                $selected = (!empty($company_info['currency_symbol_id']) && $company_info['currency_symbol_id'] == $currency['id']) ? 'selected' : '';
                echo "<option value='{$currency['id']}' $selected>{$currency['currency_name']} ({$currency['currency_symbol']}) - {$currency['isocode']}</option>";
            } ?>
        </select>
        <span id="currency_error" class="text-danger error-text"></span>
    </div>
</div><!-- end col -->
                                        </div>
                                        <!-- end row -->
                                    </div>
                                    
                                    <div class="border-bottom mb-3 pb-3">
                                        <div class="card-title-head">
                                            <h6 class="fs-16 fw-semibold mb-3 d-flex align-items-center">
                                                <span class="fs-16 me-2 p-1 rounded bg-dark text-white d-inline-flex align-items-center justify-content-center"><i class="isax isax-image"></i></span> 
                                                Company Images
                                            </h6>
                                        </div>

                                        <!-- start row -->
                                        <div class="row align-items-center pb-3 mb-3 border-bottom">
                                            <div class="col-xl-9">
                                                <div class="row gy-3 align-items-center">
                                                    <div class="col-lg-6">
                                                        <div class="logo-info">
                                                            <h6 class="fs-14 fw-medium mb-1">Company Logo</h6>
                                                            <p class="fs-12">Upload Icon of your Company</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <?php if ($is_admin): ?>
                                                        <div class="profile-pic-upload mb-0 justify-content-lg-end">
                                                            <div class="new-employee-field">
                                                                <div class="mb-0">
                                                                    <div class="image-upload mb-1">
                                                                        <input type="file" id="company_logo" name="company_logo" <?= !$is_admin ? 'disabled' : '' ?>>
                                                                        <div class="image-uploads">
                                                                             <h4 style="color: #f0f0f0;"><i class="ti ti-upload me-1"></i>Change Photo</h4>
                                                                        </div>
                                                                       
                                                                        <span id="company_logo_error" class="text-danger error-text fs-12"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php else: ?>
                                                        <div class="text-muted fs-12">
                                                            <i class="fa fa-lock me-1"></i>Admin access required
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-xl-3">
                                            <div class="new-logo ms-xl-auto bg-light border">
                                                <?php if (!empty($company_info['company_logo'])): ?>
                                                    <img id="logoPreview" src="../uploads/<?= $company_info['company_logo'] ?>" alt="Logo" width="100">
                                                <?php else: ?>
                                                    <img id="logoPreview" src="assets/img/settings/company-setting-1.svg" alt="Logo" width="100">
                                                <?php endif; ?>
                                            </div>
                                        </div>
<!-- end col -->
                                        </div>
                                        <!-- end row -->
                                     
                                        
                                        <!-- Mini Logo -->
                                        <div class="row align-items-center pb-3 mb-3 border-bottom">
                                            <div class="col-xl-9">
                                                <div class="row gy-3 align-items-center">
                                                    <div class="col-lg-6">
                                                        <div class="logo-info">
                                                            <h6 class="fs-14 fw-medium mb-1">Company Mini Logo</h6>
                                                            <p class="fs-12">Upload Logo of your company </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <?php if ($is_admin): ?>
                                                        <div class="profile-pic-upload mb-0 justify-content-lg-end">
                                                            <div class="new-employee-field">
                                                                <div class="mb-0">
                                                                    <div class="image-upload mb-1">
                                                                        <input type="file" id="mini_logo" name="mini_logo" <?= !$is_admin ? 'disabled' : '' ?>>
                                                                        <div class="image-uploads">
                                                                             <h4 style="color: #f0f0f0;"><i class="ti ti-upload me-1"></i>Change Photo</h4>
                                                                      
                                                                         <span id="mini_logo_error" class="text-danger error-text fs-12"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php else: ?>
                                                        <div class="text-muted fs-12">
                                                            <i class="fa fa-lock me-1"></i>Admin access required
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-xl-3">
                                                <div class="new-logo ms-xl-auto bg-light border">
                                                    <?php if (!empty($company_info['mini_logo'])): ?>
                                                        <img id="minilogoPreview" src="../uploads/<?= $company_info['mini_logo'] ?>" alt="Mini Logo" width="100">
                                                    <?php else: ?>
                                                        <img id="minilogoPreview" src="assets/img/settings/company-setting-1.svg" alt="Mini Logo" width="100">
                                                    <?php endif; ?>
                                                </div>
                                            </div><!-- end col -->
                                        </div>
                                        <!-- end row -->

                                        <!-- Invoice Logo -->
                                        <div class="row align-items-center">
                                            <div class="col-xl-9">
                                                <div class="row gy-3 align-items-center">
                                                    <div class="col-lg-6">
                                                        <div class="logo-info">
                                                            <h6 class="fs-14 fw-medium mb-1">Company Invoice Logo</h6>
                                                            <p class="fs-12">Upload Logo of your company </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <?php if ($is_admin): ?>
                                                        <div class="profile-pic-upload mb-0 justify-content-lg-end">
                                                            <div class="new-employee-field">
                                                                <div class="mb-0">
                                                                    <div class="image-upload mb-1">
                                                                        <input type="file" id="invoice_logo" name="invoice_logo" <?= !$is_admin ? 'disabled' : '' ?>>
                                                                        <div class="image-uploads">
                                                                             <h4 style="color: #f0f0f0;"><i class="ti ti-upload me-1"></i>Change Photo</h4>
                                                                        </div>
                                                                        
                                                                         <span id="invoice_logo_error" class="text-danger error-text fs-12"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php else: ?>
                                                        <div class="text-muted fs-12">
                                                            <i class="fa fa-lock me-1"></i>Admin access required
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-xl-3">
                                                <div class="new-logo ms-xl-auto bg-light border">
                                                    <?php if (!empty($company_info['invoice_logo'])): ?>
                                                        <img id="invoicelogoPreview" src="../uploads/<?= $company_info['invoice_logo'] ?>" alt="Invoice Logo" width="100">
                                                    <?php else: ?>
                                                        <img id="invoicelogoPreview" src="assets/img/settings/company-setting-3.svg" alt="Invoice Logo" width="100">
                                                    <?php endif; ?>
                                                </div>
                                            </div><!-- end col -->
                                        </div>
                                        <!-- end row -->
                                    </div>
                                    
                                    <div class="company-address pb-2 mb-3 border-bottom">
                                        <div class="card-title-head">
                                            <h6 class="fs-16 fw-bold mb-3 d-flex align-items-center">
                                                <span class="fs-16 me-2 p-1 rounded bg-dark text-white d-inline-flex align-items-center justify-content-center"><i class="isax isax-map"></i></span> 
                                                Company Address Information
                                            </h6>
                                        </div>

                                        <!-- start row -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Address 
                                                    </label>
                                                    <input type="text" class="form-control <?= !$is_admin ? 'readonly-field' : '' ?>" 
                                                           name="address" value="<?= !empty($company_info['address']) ? $company_info['address'] : '' ?>"
                                                           <?= !$is_admin ? 'readonly' : '' ?>>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Country 
                                                    </label>
                                                   <select class="select2" id="country" name="country_id" onchange="getStates(this.value, 'state')" <?= !$is_admin ? 'disabled' : '' ?>>
                                                        <option value="">Select Country</option>
                                                        <?php 
                                                        mysqli_data_seek($country_result, 0);
                                                        while ($country = mysqli_fetch_assoc($country_result)) {
                                                            $selected = (!empty($company_info['country_id']) && $company_info['country_id'] == $country['id']) ? 'selected' : '';
                                                            echo "<option value='{$country['id']}' $selected>{$country['name']}</option>";
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        State 
                                                    </label>
                                                    <select class="select2" id="state" name="state_id" onchange="getCities(this.value, 'city')" <?= !$is_admin ? 'disabled' : '' ?>>
                                                        <option value="">Select State</option>
                                                        <?php foreach ($states as $state): ?>
                                                            <?php $selected = (!empty($company_info['state_id']) && $company_info['state_id'] == $state['id']) ? 'selected' : ''; ?>
                                                            <option value="<?= $state['id'] ?>" <?= $selected ?>><?= $state['name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        City 
                                                    </label>
                                                    <select class="select2" id="city" name="city_id" <?= !$is_admin ? 'disabled' : '' ?>>
                                                        <option value="">Select City</option>
                                                        <?php foreach ($cities as $city): ?>
                                                            <?php $selected = (!empty($company_info['city_id']) && $company_info['city_id'] == $city['id']) ? 'selected' : ''; ?>
                                                            <option value="<?= $city['id'] ?>" <?= $selected ?>><?= $city['name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Postal Code
                                                    </label>
                                                    <input type="text" class="form-control <?= !$is_admin ? 'readonly-field' : '' ?>" 
                                                           name="zipcode" value="<?= !empty($company_info['zipcode']) ? $company_info['zipcode'] : '' ?>"
                                                           <?= !$is_admin ? 'readonly' : '' ?>>
                     
                                                </div>
                                            </div><!-- end col -->
                                        </div>
                                        <!-- end row -->
                                    </div>
                                    
                                    <div class="d-flex align-items-center justify-content-between settings-bottom-btn mt-0">
                                        <button type="button" class="btn btn-outline-white me-2" id="cancelBtn">Cancel</button>
                                        <?php if ($is_admin): ?>
                                        <button type="submit" name="submit" class="btn btn-primary">Save Changes</button>
                                        <?php else: ?>
                                        <button type="button" class="btn btn-secondary" disabled><i class="fa fa-lock me-1"></i>Read Only</button>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <!-- Start Footer-->
                <div class="footer d-sm-flex align-items-center justify-content-between bg-white py-2 px-4 border-top">
                    <p class="text-dark mb-0">&copy; <script>document.write(new Date().getFullYear())</script> <a href="javascript:void(0);" class="link-primary">Oddeven Infotech Pvt.Ltd</a>, All Rights Reserved</p>
                    <p class="text-dark">Version : 1.3.8</p>
                </div>
                <!-- End Footer-->
            </div>
            <!-- End Content -->
        </div>
        <!-- ========================
            End Page Content
        ========================= -->
    </div>
    <!-- End Main Wrapper -->

    <?php include 'layouts/vendor-scripts.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
$(document).ready(function () {
    // Initialize country code dropdown only if user is admin
    <?php if ($is_admin): ?>
    $('.country-code-select').select2({
        width: '100%',
        minimumResultsForSearch: 6,
        dropdownParent: $('.phone-input-group').parent(),
        templateResult: formatCountryCode,
        templateSelection: formatCountryCode
    });
    <?php else: ?>
    // For non-admin users, initialize select2 but keep it disabled
    $('.country-code-select').select2({
        width: '100%',
        minimumResultsForSearch: 6,
        dropdownParent: $('.phone-input-group').parent(),
        templateResult: formatCountryCode,
        templateSelection: formatCountryCode,
        disabled: true
    });
    // Initialize other select2 fields for non-admin users
    $('.select2').select2({
        disabled: true
    });
    <?php endif; ?>
    
    $("#cancelBtn").on("click", function () {
        window.location.href = 'admin-dashboard.php';
    });
    
    <?php if ($is_admin): ?>
    $("#companyForm").on("submit", function (e) {
        let isValid = true;

        $(".error-text").text("");

        function validateElement(elementId, errorId, errorMessage, validationRegex = null) {
            const element = $("#" + elementId);
            if (element.length) {
                const value = element.val().trim();
                if (value === "") {
                    $("#" + errorId).text(errorMessage);
                    return false;
                }
                if (validationRegex && !validationRegex.test(value)) {
                    $("#" + errorId).text("Enter a valid " + elementId.replace('_', ' '));
                    return false;
                }
            }
            return true;
        }

        isValid = validateElement("name", "company_name_error", "Company name is required") && isValid;

        const emailElement = $("#email");
        if (emailElement.length) {
            const email = emailElement.val().trim();
            if (email === "") {
                $("#email_error").text("Email is required");
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                $("#email_error").text("Enter a valid email");
                isValid = false;
            }
        }

        isValid = validateElement("currency", "currency_error", "Currency is required") && isValid;

        // Mobile number validation with country code
        const mobileNumber = $("#mobile_number").val().trim();
        if (mobileNumber && !/^[0-9]{7,10}$/.test(mobileNumber)) {
            $('#mobile_number_error').text('Please enter a valid mobile number (7-10 digits)');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            const firstError = $(".error-text:visible:first");
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }
    });

    function addImagePreviewListener(inputId, previewId) {
        const inputElement = document.getElementById(inputId);
        const previewElement = document.getElementById(previewId);
        
        if (inputElement && previewElement) {
            inputElement.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewElement.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    addImagePreviewListener('company_logo', 'logoPreview');
    addImagePreviewListener('mini_logo', 'minilogoPreview');
    addImagePreviewListener('invoice_logo', 'invoicelogoPreview');
    
    // Phone number validation - allow only numbers
    $('#mobile_number').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    <?php endif; ?>
});

// Format country code display
function formatCountryCode(state) {
    if (!state.id) {
        return state.text;
    }
    return state.text;
}

<?php if ($is_admin): ?>
function getStates(countryId, targetDropdown) {
    if (!countryId) {
        $('#' + targetDropdown).html('<option value="">Select State</option>').trigger('change');
        $('#city').html('<option value="">Select City</option>').trigger('change');
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
<?php endif; ?>
</script>
<script>
<?php if ($is_admin): ?>
function validateImage(inputId, errorId, allowedTypes, msg) {
    const fileInput = document.getElementById(inputId);
    const errorSpan = document.getElementById(errorId);
    errorSpan.textContent = "";

    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        if (!allowedTypes.includes(file.type)) {
            errorSpan.textContent = msg;
            fileInput.value = "";
        }
    }
}

document.getElementById("company_logo")
    .addEventListener("change", () => validateImage("company_logo", "company_logo_error", ["image/jpeg", "image/png"], "Only JPG/PNG allowed"));

document.getElementById("mini_logo")
    .addEventListener("change", () => validateImage("mini_logo", "mini_logo_error", ["image/jpeg", "image/png"], "Only JPG/PNG allowed"));

document.getElementById("invoice_logo")
    .addEventListener("change", () => validateImage("invoice_logo", "invoice_logo_error", ["image/jpeg", "image/png"], "Only JPG/PNG allowed"));
<?php endif; ?>
</script>
</body>
</html>