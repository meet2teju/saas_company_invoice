<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';


// Get company information if it exists
$company_info = [];
$company_query = "SELECT * FROM company_info LIMIT 1";
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
                                
                                <form id="companyForm" action="process/action_company_profile.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?= !empty($company_info['id']) ? $company_info['id'] : '' ?>">
                                    
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
                                                    <input type="text" name="name" id="name" class="form-control" value="<?= !empty($company_info['name']) ? $company_info['name'] : '' ?>">
                                                    <span id="company_name_error" class="text-danger error-text"></span>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-xl-6 col-lg-6 col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Email Address <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="email" id="email" name="email" class="form-control" value="<?= !empty($company_info['email']) ? $company_info['email'] : '' ?>">
                                                    <span id="email_error" class="text-danger error-text"></span>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Mobile Number <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="<?= !empty($company_info['mobile_number']) ? $company_info['mobile_number'] : '' ?>">
                                                    <span id="mobile_number_error" class="text-danger error-text"></span>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        PAN Number 
                                                    </label>
                                                    <input type="text" name="pan_number" id="pan_number" class="form-control" value="<?= !empty($company_info['pan_number']) ? $company_info['pan_number'] : '' ?>">
                                                </div>
                                            </div><!-- end col -->
                                             <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        GST Number 
                                                    </label>
                                                    <input type="text" id="gst_number" name="gst_number" class="form-control" value="<?= !empty($company_info['gst_number']) ? $company_info['gst_number'] : '' ?>">
                                                </div>
                                           </div>
                                            <!-- end col -->
                                             <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                         Currency<span class="text-danger">*</span>
                                                    </label>
                                                    <select class="select2" id="currency" name="currency_symbol_id">
                                                        <option value="">Select Currency</option>
                                                        <?php 
                                                        mysqli_data_seek($currency_result, 0);
                                                        while ($currency = mysqli_fetch_assoc($currency_result)) {
                                                            $selected = (!empty($company_info['currency_symbol_id']) && $company_info['currency_symbol_id'] == $currency['id']) ? 'selected' : '';
                                                            echo "<option value='{$currency['id']}' $selected>{$currency['currency_name']} ({$currency['currency_symbol']})</option>";
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
                                                            <h6 class="fs-14 fw-medium mb-1">Logo</h6>
                                                            <p class="fs-12">Upload Icon of your Company</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="profile-pic-upload mb-0 justify-content-lg-end">
                                                            <div class="new-employee-field">
                                                                <div class="mb-0">
                                                                    <div class="image-upload mb-1">
                                                                        <input type="file" id="company_logo" name="company_logo">
                                                                        <div class="image-uploads">
                                                                             <h4 style="color: #f0f0f0;"><i class="ti ti-upload me-1"></i>Change Photo</h4>
                                                                        </div>
                                                                       
                                                                        <span id="company_logo_error" class="text-danger error-text fs-12"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
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
                                                            <h6 class="fs-14 fw-medium mb-1">Mini Logo</h6>
                                                            <p class="fs-12">Upload Logo of your company </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="profile-pic-upload mb-0 justify-content-lg-end">
                                                            <div class="new-employee-field">
                                                                <div class="mb-0">
                                                                    <div class="image-upload mb-1">
                                                                        <input type="file" id="mini_logo" name="mini_logo">
                                                                        <div class="image-uploads">
                                                                            <!-- <h4><i class="ti ti-upload me-1"></i>Change Photo</h4> -->
                                                                             <h4 style="color: #f0f0f0;"><i class="ti ti-upload me-1"></i>Change Photo</h4>
                                                                      
                                                                         <span id="mini_logo_error" class="text-danger error-text fs-12"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
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
                                                            <h6 class="fs-14 fw-medium mb-1">Invoice Logo</h6>
                                                            <p class="fs-12">Upload Logo of your company </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="profile-pic-upload mb-0 justify-content-lg-end">
                                                            <div class="new-employee-field">
                                                                <div class="mb-0">
                                                                    <div class="image-upload mb-1">
                                                                        <input type="file" id="invoice_logo" name="invoice_logo">
                                                                        <div class="image-uploads">
                                                                             <h4 style="color: #f0f0f0;"><i class="ti ti-upload me-1"></i>Change Photo</h4>
                                                                        </div>
                                                                        
                                                                         <span id="invoice_logo_error" class="text-danger error-text fs-12"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
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
                                                Address Information
                                            </h6>
                                        </div>

                                        <!-- start row -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Address 
                                                    </label>
                                                    <input type="text" class="form-control" name="address" value="<?= !empty($company_info['address']) ? $company_info['address'] : '' ?>">
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Country <span class="text-danger">*</span>
                                                    </label>
                                                   <select class="select2" id="country" name="country_id" onchange="getStates(this.value, 'state')">
                                                        <option value="">Select Country</option>
                                                        <?php 
                                                        mysqli_data_seek($country_result, 0);
                                                        while ($country = mysqli_fetch_assoc($country_result)) {
                                                            $selected = (!empty($company_info['country_id']) && $company_info['country_id'] == $country['id']) ? 'selected' : '';
                                                            echo "<option value='{$country['id']}' $selected>{$country['name']}</option>";
                                                        } ?>
                                                    </select>
                                                        <span id="country_name_error" class="text-danger error-text"></span>

                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        State <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="select2" id="state" name="state_id" onchange="getCities(this.value, 'city')">
                                                        <option value="">Select State</option>
                                                        <?php foreach ($states as $state): ?>
                                                            <?php $selected = (!empty($company_info['state_id']) && $company_info['state_id'] == $state['id']) ? 'selected' : ''; ?>
                                                            <option value="<?= $state['id'] ?>" <?= $selected ?>><?= $state['name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <span id="state_name_error" class="text-danger error-text"></span>

                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        City <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="select2" id="city" name="city_id">
                                                        <option value="">Select City</option>
                                                        <?php foreach ($cities as $city): ?>
                                                            <?php $selected = (!empty($company_info['city_id']) && $company_info['city_id'] == $city['id']) ? 'selected' : ''; ?>
                                                            <option value="<?= $city['id'] ?>" <?= $selected ?>><?= $city['name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <span id="city_name_error" class="text-danger error-text"></span>

                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Postal Code <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" name="zipcode" value="<?= !empty($company_info['zipcode']) ? $company_info['zipcode'] : '' ?>">
                                                    <span id="zipcode_name_error" class="text-danger error-text"></span>
                     
                                                </div>
                                            </div><!-- end col -->
                                        </div>
                                        <!-- end row -->
                                    </div>
                                    
                                    <div class="d-flex align-items-center justify-content-between settings-bottom-btn mt-0">
                                        <button type="button" class="btn btn-outline-white me-2">Cancel</button>
                                        <button type="submit" name="submit" class="btn btn-primary">Save Changes</button>
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
    <script>
$(document).ready(function () {
    $("#companyForm").on("submit", function (e) {
        let isValid = true;

        // Reset previous errors
        $(".error-text").text("");

        // Validate only if elements exist
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

        // Company Name
        isValid = validateElement("name", "company_name_error", "Company name is required") && isValid;

        // Email
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

        // Mobile
        isValid = validateElement("mobile_number", "mobile_number_error", "Mobile number is required", /^[0-9]{10,15}$/) && isValid;

        // Currency
        isValid = validateElement("currency", "currency_error", "Currency is required") && isValid;

        // Country
        isValid = validateElement("country", "country_name_error", "Country is required") && isValid;

        // State
        isValid = validateElement("state", "state_name_error", "State is required") && isValid;

        // City
        isValid = validateElement("city", "city_name_error", "City is required") && isValid;

        // Postal Code
        isValid = validateElement("zipcode", "zipcode_name_error", "Postal code is required", /^[0-9]{6}$/) && isValid;

        // Prevent submit if not valid
        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = $(".error-text:visible:first");
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }
    });

    // Image preview functionality - safe version that checks if elements exist
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

    // Add listeners for all image previews
    addImagePreviewListener('company_logo', 'logoPreview');
    addImagePreviewListener('mini_logo', 'minilogoPreview');
    addImagePreviewListener('favicon_logo', 'faviconlogoPreview');
    addImagePreviewListener('invoice_logo', 'invoicelogoPreview');
});

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
</script>
<script>
function validateImage(inputId, errorId, allowedTypes, msg) {
    const fileInput = document.getElementById(inputId);
    const errorSpan = document.getElementById(errorId);
    errorSpan.textContent = "";

    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        if (!allowedTypes.includes(file.type)) {
            errorSpan.textContent = msg;
            fileInput.value = ""; // clear invalid file
        }
    }
}

// JPG + PNG
document.getElementById("company_logo")
    .addEventListener("change", () => validateImage("company_logo", "company_logo_error", ["image/jpeg", "image/png"], "Only JPG/PNG allowed"));

document.getElementById("mini_logo")
    .addEventListener("change", () => validateImage("mini_logo", "mini_logo_error", ["image/jpeg", "image/png"], "Only JPG/PNG allowed"));

document.getElementById("invoice_logo")
    .addEventListener("change", () => validateImage("invoice_logo", "invoice_logo_error", ["image/jpeg", "image/png"], "Only JPG/PNG allowed"));

// ICO only
// document.getElementById("favicon_logo")
//     .addEventListener("change", () => validateImage("favicon_logo", "favicon_logo_error", ["image/x-icon", "image/vnd.microsoft.icon"], "Only ICO allowed"));
</script>


</body>
</html>