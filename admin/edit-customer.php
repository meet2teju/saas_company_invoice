<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

// Get company currency with database backup (SAME AS ADD PAGE)
$companyCurrency = getCompanyCurrency($conn);

// Method 2: Direct query as backup (SAME AS ADD PAGE)
$org_id = $_SESSION['org_id'] ?? 1;
$directQuery = "SELECT c.id, c.currency_name, c.currency_symbol, c.isocode as currency_code 
                FROM company_info ci 
                LEFT JOIN currency c ON ci.currency_symbol_id = c.id 
                WHERE ci.org_id = '$org_id' 
                LIMIT 1";
$directResult = mysqli_query($conn, $directQuery);

if ($directResult && mysqli_num_rows($directResult) > 0) {
    $directCurrency = mysqli_fetch_assoc($directResult);
    
    // If session currency doesn't match database, update it
    if ($companyCurrency['id'] != $directCurrency['id']) {
        $companyCurrency = $directCurrency;
        $_SESSION['company_currency'] = $directCurrency;
    }
}

// Get all currencies for dropdown (ADDED THIS)
$currency_query = "SELECT * FROM currency ORDER BY currency_name";
$currency_result = mysqli_query($conn, $currency_query);

// Get row ID from URL
$clientid = $_GET['id'];
$query = "SELECT * FROM client WHERE id = $clientid";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$bankquery = "SELECT * FROM client_bank WHERE client_id = $clientid";
$bankresult = mysqli_query($conn, $bankquery);
$bankrow = mysqli_fetch_assoc($bankresult);

$client_id = $_GET['id'];
$doc_query = "SELECT * FROM client_document WHERE client_id = $client_id";
$doc_result = mysqli_query($conn, $doc_query);

$addressquery = "SELECT * FROM client_address WHERE client_id = $clientid";
$addressresult = mysqli_query($conn, $addressquery);
$addressrow = mysqli_fetch_assoc($addressresult);

// Get all countries for dropdown
$country_query = "SELECT * FROM countries ORDER BY name";
$country_result = mysqli_query($conn, $country_query);

// Reset country_result pointer for the dropdown
mysqli_data_seek($country_result, 0);

// Fetch contact persons
$client_id = $_GET['id'];
$contacts_query = "SELECT * FROM client_contact_persons WHERE client_id = $clientid AND is_deleted = 0";
$contacts_result = mysqli_query($conn, $contacts_query);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
                      <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo ($_SESSION['message_type'] == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show mb-4" role="alert">
                            <?php echo $_SESSION['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php 
                        unset($_SESSION['message'], $_SESSION['message_type']); 
                    endif; ?>
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6>Edit Client</h6>
                            <!-- <a href="#" class="btn btn-outline-white d-inline-flex align-items-center"><i class="isax isax-eye me-1"></i>Preview</a> -->
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <form id="form" action="process/action_edit_client.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="client_id" value="<?php echo $clientid; ?>">
                                    <!-- Add this ONE LINE for active tab -->
                                    <input type="hidden" name="active_tab" id="activeTabField" value="other-tab">
                                    
                                    <!-- In your edit form -->
                                    <input type="hidden" name="existing_image" value="<?php echo $row['customer_image']; ?>">

                                    <div class="mb-3">
                                        <div class="row gx-3">
                                            <div class="col-lg-6 col-md-6">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div id="add_image_preview" class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0" style="cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                                        <?php if (!empty($row['customer_image'])): ?>
                                                            <img src="../uploads/<?php echo $row['customer_image']; ?>" class="avatar avatar-xl" alt="Customer Image" style="width: 100%; height: 100%; object-fit: cover;">
                                                        <?php else: ?>
                                                            <i class="isax isax-image text-primary fs-24"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="d-inline-flex flex-column align-items-start">
                                                        <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                                            <i class="isax isax-image me-1"></i>Upload Image
                                                            <input type="file" class="form-control image-sign" name="customer_image" id="add_image" accept="image/*">
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
                                                            <input class="form-check-input" type="radio" name="client_type" value="1" <?= ($row['client_type'] == 1) ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Business</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="client_type" value="0" <?= ($row['client_type'] == 0) ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Individual</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Salutation<span class="text-danger ms-1">*</span></label>
                                                    <select class="select" name="salutation" id="salutation" onchange="updateDisplayName()">
                                                        <option value="Mr" <?php echo $row['salutation'] == 'Mr' ? 'selected' : ''; ?>>Mr</option>
                                                        <option value="Mrs" <?php echo $row['salutation'] == 'Mrs' ? 'selected' : ''; ?>>Mrs</option>
                                                        <option value="Ms" <?php echo $row['salutation'] == 'Ms' ? 'selected' : ''; ?>>Ms</option>
                                                        <option value="Miss" <?php echo $row['salutation'] == 'Miss' ? 'selected' : ''; ?>>Miss</option>
                                                        <option value="Dr" <?php echo $row['salutation'] == 'Dr' ? 'selected' : ''; ?>>Dr</option>
                                                    </select>
                                                    <span id="salutation_error" class="text-danger error-text"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">First Name <span class="text-danger ms-1">*</span></label>
                                                    <input type="text" class="form-control" name="first_name" id="first_name" value="<?= htmlspecialchars($row['first_name']) ?>">
                                                    <span id="first_name_error" class="text-danger error-text"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Last Name <span class="text-danger ms-1">*</span></label>
                                                    <input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo htmlspecialchars($row['last_name']); ?>">
                                                    <span id="last_name_error" class="text-danger error-text"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Company Name </label>
                                                    <input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo htmlspecialchars($row['company_name']); ?>">
                                                    <span id="company_name_error" class="text-danger error-text"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Email </label>
                                                    <input type="email" id="email" class="form-control" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                                    <span id="email_error" class="text-danger error-text"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
    <div class="mb-3">
        <label class="form-label">Work Number</label>
        <div class="phone-input-group">
            <select class="country-code-select select" name="work_country_code" id="work_country_code">
                <?php 
                $work_country_code = $row['work_country_code'] ?? '+91';
                foreach ($country_codes as $country => $code): 
                ?>
                    <option value="<?= $code ?>" <?= $code == $work_country_code ? 'selected' : '' ?> data-country="<?= $country ?>">
                        <?= $code ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" class="form-control phone-number-input" name="phone_number" id="phone_number" value="<?= htmlspecialchars($row['phone_number']); ?>" placeholder="Phone Number">
        </div>
    </div>
</div>
<div class="col-lg-4 col-md-6">
    <div class="mb-3">
        <label class="form-label">Mobile Number</label>
        <div class="phone-input-group">
            <select class="country-code-select select" name="mobile_country_code" id="mobile_country_code">
                <?php 
                $mobile_country_code = $row['mobile_country_code'] ?? '+91';
                foreach ($country_codes as $country => $code): 
                ?>
                    <option value="<?= $code ?>" <?= $code == $mobile_country_code ? 'selected' : '' ?> data-country="<?= $country ?>">
                        <?= $code ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" class="form-control phone-number-input" name="business_number" id="business_number" value="<?= htmlspecialchars($row['business_number']); ?>" placeholder="Mobile Number">
        </div>
    </div>
</div>

                                        <!-- Tabs Start -->
                                        <ul class="nav nav-tabs mb-3" id="customerTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="other-tab" data-bs-toggle="tab" data-bs-target="#otherTab" type="button" role="tab">Other Details</button>
                                            </li>
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
                                        </ul>

                                        <div class="tab-content" id="customerTabContent">
                                            <!-- Other Details Tab -->
                                            <div class="tab-pane fade show active" id="otherTab" role="tabpanel">
                                                <div class="row gx-3">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">PAN</label>
                                                        <input type="text" class="form-control" name="pan_number" id="pan_number" value="<?php echo htmlspecialchars($row['pan_number']); ?>">
                                                        <span id="pan_number_error" class="text-danger error-text"></span>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Currency</label>
                                                        <!-- UPDATED CURRENCY DROPDOWN (SAME AS ADD PAGE) -->
                                                        <select class="form-select" name="currency" id="currency">
                                                            <?php 
                                                            // Reset currency_result pointer
                                                            mysqli_data_seek($currency_result, 0);
                                                            
                                                            // Get client's current currency ID
                                                            $clientCurrencyId = $row['currency_id'] ?? $companyCurrency['id'];
                                                            
                                                            while ($currency = mysqli_fetch_assoc($currency_result)) {
                                                                // Use client's current currency as selected, fallback to company currency
                                                                $selected = ($currency['id'] == $clientCurrencyId) ? 'selected' : '';
                                                                echo "<option value='{$currency['id']}' $selected>
                                                                        {$currency['currency_name']} ({$currency['currency_symbol']})
                                                                      </option>";
                                                            } 
                                                            ?>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">VAT/GST Number</label>
                                                        <input type="text" class="form-control" name="gst_number" id="gst_number" value="<?php echo htmlspecialchars($row['gst_number']); ?>">
                                                        <span id="gst_number_error" class="text-danger error-text"></span>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                            <label class="form-label">Documents</label>
                                                            <input type="file" class="form-control" name="documents[]" multiple>
                                                            <span id="documents_error" class="text-danger error-text"></span>

                                                            <?php if (mysqli_num_rows($doc_result) > 0): ?>
                                                                <ul class="list-unstyled mt-2">
                                                                    <?php while ($doc = mysqli_fetch_assoc($doc_result)): ?>
                                                                        <li class="d-flex align-items-center justify-content-between mb-1" id="doc-<?php echo $doc['id']; ?>">
                                                                            <a href="../uploads/<?php echo htmlspecialchars($doc['document']); ?>" target="_blank">
                                                                                <?php echo htmlspecialchars($doc['document']); ?>
                                                                            </a>
                                                                            <div>
                                                                                <!-- Edit Button (opens file replace input) -->
                                                                                <label class="btn btn-sm btn-outline-primary btn-icon" title="Replace">
                                                                                    <i class="bi bi-pencil"></i>
                                                                                    <input type="file" class="d-none replace-doc" 
                                                                                        data-doc-id="<?php echo $doc['id']; ?>" 
                                                                                        data-old-name="<?php echo htmlspecialchars($doc['document']); ?>">
                                                                                </label>

                                                                                <!-- Delete Button -->
                                                                                <button type="button" class="btn btn-sm btn-outline-danger btn-icon delete-doc" 
                                                                                        data-doc-id="<?php echo $doc['id']; ?>">
                                                                                    <i class="bi bi-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        </li>
                                                                    <?php endwhile; ?>
                                                                </ul>
                                                            <?php else: ?>
                                                                <small class="text-muted">No documents uploaded.</small>
                                                            <?php endif; ?>
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
                                                                <input type="url" class="form-control" name="website_url" id="website_url" placeholder="https://" value="<?php echo htmlspecialchars($row['website_url']); ?>">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Department</label>
                                                                <input type="text" class="form-control" name="department" id="department" value="<?php echo htmlspecialchars($row['department']); ?>">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Designation</label>
                                                                <input type="text" class="form-control" name="designation" id="designation" value="<?php echo htmlspecialchars($row['designation']); ?>">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Twitter</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-x-twitter"></i></span>
                                                                    <input type="text" class="form-control" name="twitter" id="twitter" placeholder="http://www.twitter.com/" value="<?php echo htmlspecialchars($row['twitter']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Skype Name/Number</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-skype"></i></span>
                                                                    <input type="text" class="form-control" name="skype_name_number" id="skype_name_number" value="<?php echo htmlspecialchars($row['skype_name_number']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Facebook</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                                                                    <input type="text" class="form-control" name="facebook" id="facebook" placeholder="http://www.facebook.com/" value="<?php echo htmlspecialchars($row['facebook']); ?>">
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
                                                                <label class="form-label">Address Line 1</label>
                                                                <input type="text" id="billing_address1" class="form-control" name="billing_address1" value="<?php echo htmlspecialchars($addressrow['billing_address1']); ?>">
                                                            </div>
                                                            <div class="col-12 mb-3">
                                                                <label class="form-label">Address Line 2</label>
                                                                <input type="text" id="billing_address2" class="form-control" name="billing_address2" value="<?php echo htmlspecialchars($addressrow['billing_address2']); ?>">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Country</label>
                                                                <select class="select" id="billing_country" name="billing_country" onchange="getStates(this.value, 'billing_state')">
                                                                     <option value="">Select Country</option>
                                                                    <?php 
                                                                    mysqli_data_seek($country_result, 0);
                                                                    while ($country = mysqli_fetch_assoc($country_result)) {
                                                                        $selected = ($country['id'] == $addressrow['billing_country']) ? 'selected' : '';
                                                                        echo "<option value='{$country['id']}' $selected>{$country['name']}</option>";
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">State</label>
                                                                <select class="select2" id="billing_state" name="billing_state" onchange="getCities(this.value, 'billing_city')">
                                                                    <option value="">Select State</option>
                                                                    <?php if (!empty($addressrow['billing_state'])): ?>
                                                                        <?php 
                                                                        $state_query = "SELECT * FROM states WHERE id = {$addressrow['billing_state']}";
                                                                        $state_result = mysqli_query($conn, $state_query);
                                                                        $state = mysqli_fetch_assoc($state_result);
                                                                        if ($state) {
                                                                            echo "<option value='{$state['id']}' selected>{$state['name']}</option>";
                                                                        }
                                                                        ?>
                                                                    <?php endif; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">City</label>
                                                                <select class="select" id="billing_city" name="billing_city">
                                                                    <option value="">Select City</option>
                                                                    <?php if (!empty($addressrow['billing_city'])): ?>
                                                                        <?php 
                                                                        $city_query = "SELECT * FROM cities WHERE id = {$addressrow['billing_city']}";
                                                                        $city_result = mysqli_query($conn, $city_query);
                                                                        $city = mysqli_fetch_assoc($city_result);
                                                                        if ($city) {
                                                                            echo "<option value='{$city['id']}' selected>{$city['name']}</option>";
                                                                        }
                                                                        ?>
                                                                    <?php endif; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Pincode</label>
                                                                <input type="text" class="form-control" id="billing_pincode" name="billing_pincode" value="<?php echo htmlspecialchars($addressrow['billing_pincode']); ?>">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Shipping Address -->
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                                            <h6>Shipping Address</h6>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12 mb-3">
                                                                <label class="form-label">Address Line 1</label>
                                                                <input type="text" class="form-control" id="shipping_address1" name="shipping_address1" value="<?php echo htmlspecialchars($addressrow['shipping_address1']); ?>">
                                                            </div>
                                                            <div class="col-12 mb-3">
                                                                <label class="form-label">Address Line 2</label>
                                                                <input type="text" class="form-control" id="shipping_address2" name="shipping_address2" value="<?php echo htmlspecialchars($addressrow['shipping_address2']); ?>">
                                                            </div>
                                                          <div class="col-md-6 mb-3">
    <label class="form-label">Country</label>
    <select class="select" id="shipping_country" name="shipping_country" onchange="getStates(this.value, 'shipping_state')">
        <option value="">Select Country</option>
        <?php 
        mysqli_data_seek($country_result, 0);
        while ($country = mysqli_fetch_assoc($country_result)) {
            $selected = ($country['id'] == $addressrow['shipping_country']) ? 'selected' : '';
            echo "<option value='{$country['id']}' $selected>{$country['name']}</option>";
        } ?>
    </select>
</div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">State</label>
                                                                <select class="select" id="shipping_state" name="shipping_state" onchange="getCities(this.value, 'shipping_city')">
                                                                    <option value="">Select State</option>
                                                                    <?php if (!empty($addressrow['shipping_state'])): ?>
                                                                        <?php 
                                                                        $state_query = "SELECT * FROM states WHERE id = {$addressrow['shipping_state']}";
                                                                        $state_result = mysqli_query($conn, $state_query);
                                                                        $state = mysqli_fetch_assoc($state_result);
                                                                        if ($state) {
                                                                            echo "<option value='{$state['id']}' selected>{$state['name']}</option>";
                                                                        }
                                                                        ?>
                                                                    <?php endif; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">City</label>
                                                                <select class="select" id="shipping_city" name="shipping_city">
                                                                    <option value="">Select City</option>
                                                                    <?php if (!empty($addressrow['shipping_city'])): ?>
                                                                        <?php 
                                                                        $city_query = "SELECT * FROM cities WHERE id = {$addressrow['shipping_city']}";
                                                                        $city_result = mysqli_query($conn, $city_query);
                                                                        $city = mysqli_fetch_assoc($city_result);
                                                                        if ($city) {
                                                                            echo "<option value='{$city['id']}' selected>{$city['name']}</option>";
                                                                        }
                                                                        ?>
                                                                    <?php endif; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Pincode</label>
                                                                <input type="text" class="form-control" id="shipping_pincode" name="shipping_pincode" value="<?php echo htmlspecialchars($addressrow['shipping_pincode']); ?>">
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
                                                            <!-- Rows added by JS -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <button type="button" class="btn btn-outline-primary mt-2 mb-3" onclick="addContactRow()">
                                                    + Add Contact Person
                                                </button>
                                            </div>

                                            <!-- Bank Details Tab -->
                                            <div class="tab-pane fade" id="bankTab" role="tabpanel">
                                                <h6 class="mb-3">Banking Details</h6>
                                                <div class="row gx-3">
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">Bank Name</label>
                                                        <input type="text" class="form-control" name="bank_name" value="<?php echo htmlspecialchars($bankrow['bank_name']??''); ?>">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">Branch</label>
                                                        <input type="text" class="form-control" name="bank_branch" value="<?php echo htmlspecialchars($bankrow['bank_branch']??''); ?>">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">Account Holder</label>
                                                        <input type="text" class="form-control" name="account_holder" value="<?php echo htmlspecialchars($bankrow['account_holder']??''); ?>">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">Account Number</label>
                                                        <input type="text" class="form-control" name="account_number" value="<?php echo htmlspecialchars($bankrow['account_number']??''); ?>">
                                                    </div>
                                                     <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">Routing Number</label>
                                                        <input type="text" class="form-control" name="routing_number" value="<?php echo htmlspecialchars($bankrow['routing_number']??''); ?>">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 mb-3">
                                                        <label class="form-label">IFSC</label>
                                                        <input type="text" class="form-control" name="IFSC_code" value="<?php echo htmlspecialchars($bankrow['IFSC_code']??''); ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Remarks Tab -->
                                            <div class="tab-pane fade" id="remarksTab" role="tabpanel">
                                                <h6 class="mb-3">Remarks (For Internal Use)</h6>
                                                <div class="mb-3">
                                                    <textarea class="form-control" rows="5" name="remark" placeholder="Enter any internal remarks about this customer"><?php echo htmlspecialchars($row['remark']); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tabs End -->

                                    <div class="d-flex align-items-center justify-content-between pt-4 border-top">
                                    <a href="customers.php" class="btn btn-outline-white">Cancel</a>
                                        <button type="submit" class="btn btn-primary" name="submit">Save Changes</button>
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
});
</script>

<script>
function addContactRow(
    salutation = "", 
    firstName = "", 
    lastName = "", 
    email = "", 
    workPhone = "", 
    mobile = "", 
    skype = "", 
    designation = "", 
    department = "", 
    contactId = ""
) {
    const tbody = document.getElementById("contactTableBody");
    const rowId = Date.now();

    const row = document.createElement("tr");
    row.dataset.rowId = rowId;
    
    // Store contact ID if it exists
    if (contactId) {
        row.dataset.contactId = contactId;
    }
    
    if (tbody.children.length === 0) row.dataset.isFirst = "true";

    row.innerHTML = `
        <td>
            <select class="form-select" name="contact_salutation[]">
                <option value="Mr" ${salutation === "Mr" ? "selected" : ""}>Mr</option>
                <option value="Mrs" ${salutation === "Mrs" ? "selected" : ""}>Mrs</option>
                <option value="Ms" ${salutation === "Ms" ? "selected" : ""}>Ms</option>
                <option value="Dr" ${salutation === "Dr" ? "selected" : ""}>Dr</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control contact-firstname" name="contact_first_name[]" value="${firstName}">
            <small class="text-danger error-firstname"></small>
        </td>
        <td>
            <input type="text" class="form-control contact-lastname" name="contact_last_name[]" value="${lastName}">
            <small class="text-danger error-lastname"></small>
        </td>
        <td>
            <input type="email" class="form-control contact-email" name="contact_email[]" value="${email}">
            <small class="text-danger error-email"></small>
        </td>
        <td>
            <input type="text" class="form-control contact-workphone" name="contact_work_phone[]" value="${workPhone}">
            <small class="text-danger error-workphone"></small>
        </td>
        <td>
            <input type="text" class="form-control contact-mobile" name="contact_mobile[]" value="${mobile}">
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
    
    // Add hidden input for contact ID if it exists
    if (contactId) {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'contact_ids[]';
        hiddenInput.value = contactId;
        row.appendChild(hiddenInput);
    }

    const extraRow = document.createElement("tr");
    extraRow.classList.add("extra-fields-row", "d-none");
    extraRow.dataset.rowId = rowId;
    extraRow.innerHTML = `
        <td colspan="7">
            <div class="p-3 bg-light rounded">
                <div class="row gx-3">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Skype</label>
                        <input type="text" class="form-control" name="contact_skype[]" value="${skype}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Designation</label>
                        <input type="text" class="form-control" name="contact_designation[]" value="${designation}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Department</label>
                        <input type="text" class="form-control" name="contact_department[]" value="${department}">
                    </div>
                </div>
            </div>
        </td>
    `;

    tbody.appendChild(row);
    tbody.appendChild(extraRow);

    // Attach real-time validation listeners
    const emailInput = row.querySelector(".contact-email");
    const workPhoneInput = row.querySelector(".contact-workphone");
    const mobileInput = row.querySelector(".contact-mobile");
    const firstNameInput = row.querySelector(".contact-firstname");
    const lastNameInput = row.querySelector(".contact-lastname");

    if (emailInput) emailInput.addEventListener("input", validateEmail);
    if (workPhoneInput) workPhoneInput.addEventListener("input", validateWorkPhone);
    if (mobileInput) mobileInput.addEventListener("input", validateMobile);
    if (firstNameInput) firstNameInput.addEventListener("input", validateName);
    if (lastNameInput) lastNameInput.addEventListener("input", validateName);
}

// ---------------- Validation Functions ----------------
function validateEmail(e) {
    const email = e.target.value.trim();
    const errorSpan = e.target.closest("td").querySelector(".error-email");
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    errorSpan.textContent = email === "" ? "" : (!regex.test(email) ? "Invalid email format" : "");
}

function validateWorkPhone(e) {
    const phone = e.target.value.trim();
    const errorSpan = e.target.closest("td").querySelector(".error-workphone");
    // allow only digits
    e.target.value = phone.replace(/[^0-9]/g, '');
    errorSpan.textContent = phone === "" ? "" : (!/^[0-9]{7,10}$/.test(phone) ? "Work Phone must be 7-10 digits" : "");
}

function validateMobile(e) {
    const phone = e.target.value.trim();
    const errorSpan = e.target.closest("td").querySelector(".error-mobile");
    // allow only digits
    e.target.value = phone.replace(/[^0-9]/g, '');
    errorSpan.textContent = phone === "" ? "" : (!/^[0-9]{10}$/.test(phone) ? "Mobile must be exactly 10 digits" : "");
}

function validateName(e) {
    const name = e.target.value.trim();
    const errorSpan = e.target.closest("td").querySelector("small");
    // allow only letters and space
    e.target.value = name.replace(/[^a-zA-Z\s]/g, '');
    errorSpan.textContent = "";
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

// function removeRow(button) {
//     const row = button.closest("tr");
//     const rowId = row.dataset.rowId;
//     const contactId = row.dataset.contactId;
//     const extraRow = document.querySelector(`tr.extra-fields-row[data-row-id="${rowId}"]`);

//     if (!confirm("Are you sure you want to delete this contact person?")) return;

//     const deleteBtn = button;
//     deleteBtn.disabled = true;
//     deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

//     if (contactId) {
//         $.ajax({
//             url: 'process/action_delete_client_contact_person.php',
//             type: 'POST',
//             dataType: 'json',
//             data: { id: contactId },
//             success: function (response) {
//                 if (response.status === 'success') {
//                     row.remove();
//                     if (extraRow) extraRow.remove();
//                     showToast("Contact person deleted", 'success');
//                 } else {
//                     showToast("Error: " + response.message, 'danger');
//                     resetDeleteBtn();
//                 }
//             },
//             error: function (xhr, status, error) {
//                 showToast("AJAX Error: " + error, 'danger');
//                 resetDeleteBtn();
//             }
//         });

//         function resetDeleteBtn() {
//             deleteBtn.disabled = false;
//             deleteBtn.innerHTML = '<i class="isax isax-trash"></i>';
//         }
//     } else {
//         row.remove();
//         if (extraRow) extraRow.remove();
//         showToast("Contact removed", 'info');
//     }
// }

// // Helper function for toast notifications
// function showToast(message, type = 'success') {
//     // Implement your toast notification system here
//     // Example with Bootstrap toasts:
//     const toast = new bootstrap.Toast(document.getElementById('notificationToast'));
//     const toastBody = document.getElementById('toastBody');
    
//     toastBody.textContent = message;
//     toastBody.className = `toast-body bg-${type}`;
//     toast.show();
// }
function removeRow(button) {
    const row = button.closest("tr");
    const rowId = row.dataset.rowId;
    const contactId = row.dataset.contactId;
    const extraRow = document.querySelector(`tr.extra-fields-row[data-row-id="${rowId}"]`);

    // REMOVED THE CONFIRMATION DIALOG
    // if (!confirm("Are you sure you want to delete this contact person?")) return;

    const deleteBtn = button;
    deleteBtn.disabled = true;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    if (contactId) {
        // Existing contact - delete via AJAX
        $.ajax({
            url: 'process/action_delete_client_contact_person.php',
            type: 'POST',
            dataType: 'json',
            data: { id: contactId },
            success: function (response) {
                if (response.status === 'success') {
                    row.remove();
                    if (extraRow) extraRow.remove();
                    showToast("Contact person deleted", 'success');
                } else {
                    showToast("Error: " + response.message, 'danger');
                    resetDeleteBtn();
                }
            },
            error: function (xhr, status, error) {
                showToast("AJAX Error: " + error, 'danger');
                resetDeleteBtn();
            }
        });

        function resetDeleteBtn() {
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = '<i class="isax isax-trash"></i>';
        }
    } else {
        // New contact - just remove from DOM
        row.remove();
        if (extraRow) extraRow.remove();
        showToast("Contact removed", 'info');
    }
}
// Load existing contact persons when page loads
document.addEventListener("DOMContentLoaded", function () {
    // Load existing contact persons
    <?php
    $contacts_result = mysqli_query($conn, "SELECT * FROM client_contact_persons WHERE client_id = $client_id AND is_deleted = 0");
    if (mysqli_num_rows($contacts_result) > 0) {
        while ($contact = mysqli_fetch_assoc($contacts_result)) {
            // Prepare the data for JavaScript
            $salutation = addslashes($contact['contact_salutation'] ?? '');
            $firstName = addslashes($contact['contact_first_name'] ?? '');
            $lastName = addslashes($contact['contact_last_name'] ?? '');
            $email = addslashes($contact['contact_email'] ?? '');
            $workPhone = addslashes($contact['contact_work_phone'] ?? '');
            $mobile = addslashes($contact['contact_mobile'] ?? '');
            $skype = addslashes($contact['contact_skype'] ?? '');
            $designation = addslashes($contact['contact_designation'] ?? '');
            $department = addslashes($contact['contact_department'] ?? '');
            $contactId = $contact['id'];
            
            echo "addContactRow(";
            echo "'$salutation', ";
            echo "'$firstName', ";
            echo "'$lastName', ";
            echo "'$email', ";
            echo "'$workPhone', ";
            echo "'$mobile', ";
            echo "'$skype', ";
            echo "'$designation', ";
            echo "'$department', ";
            echo "'$contactId'";
            echo ");\n";
        }
    } else {
        // Add one empty row if no contacts exist
        echo "addContactRow();\n";
    }
    ?>
});
</script>

<script>
// Fetch States based on selected Country
function getStates(countryId, targetDropdown) {
    if (!countryId) {
        $('#' + targetDropdown).html('<option value="">Select State</option>').trigger('change');
        $('#' + targetDropdown.replace('state', 'city')).html('<option value="">Select City</option>').trigger('change');
        return;
    }

    $.ajax({
        url: 'process/action_get_state.php',
        type: 'POST',
        data: { datapost: countryId },
        success: function (result) {
            $('#' + targetDropdown).html('<option value="">Select State</option>' + result).trigger('change');
        },
        error: function (xhr, status, error) {
            console.error("Error fetching states:", error);
        }
    });
}

// Fetch Cities based on selected State
function getCities(stateId, targetDropdown) {
    if (!stateId) {
        $('#' + targetDropdown).html('<option value="">Select City</option>').trigger('change');
        return;
    }

    $.ajax({
        url: 'process/action_get_city.php',
        type: 'POST',
        data: { datapost: stateId },
        success: function (result) {
            $('#' + targetDropdown).html('<option value="">Select City</option>' + result).trigger('change');
        },
        error: function (xhr, status, error) {
            console.error("Error fetching cities:", error);
        }
    });
}
</script>

<script>
$(document).ready(function () {
    $('.delete-doc').on('click', function () {
        const docId = $(this).data('doc-id');

        $.ajax({
            url: 'process/action_delete_client_document.php',
            type: 'POST',
            data: { id: docId },
            success: function (response) {
                if (response.trim() === 'success') {
                    $('#doc-' + docId).remove();
                }
            }
        });
    });
});
</script>
<script>
$(document).ready(function () {
    $('.replace-doc').on('change', function () {
        const input = this;
        const file = input.files[0];
        if (!file) return;

        const allowed = ['pdf', 'xls', 'xlsx', 'csv'];
        const ext = file.name.split('.').pop().toLowerCase();
        if (!allowed.includes(ext)) return;

        const docId = $(input).data('doc-id');
        const oldName = $(input).data('old-name');

        const formData = new FormData();
        formData.append('doc_id', docId);
        formData.append('old_name', oldName);
        formData.append('new_file', file);

        $.ajax({
            url: 'process/action_client_edit_document.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                const result = JSON.parse(res);
                if (result.status === 'success') {
                    const listItem = $('#doc-' + docId);
                    const link = listItem.find('a');

                    // Update the displayed link
                    link.text(result.new_name);
                    link.attr('href', '../uploads/' + result.new_name);

                    // Update the input's data-old-name attribute
                    listItem.find('.replace-doc').attr('data-old-name', result.new_name);
                }
            }
        });
    });
});
</script>

<script>
    $(document).ready(function() {
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
            {name: 'salutation', errorId: 'salutation_error', message: 'Salutation is required', tab: 'otherTab'}
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

    // Real-time validation for phone numbers
    $('[name="phone_number"], [name="business_number"]').on('input', function() {
    let number = $(this).val().trim();

    // Remove anything that is not a digit
    const digitsOnly = number.replace(/\D/g, '');

    const fieldName = $(this).attr('name');
    const errorId = `${fieldName}_error`;

    if (digitsOnly && !/^[0-9]{10,12}$/.test(digitsOnly)) {
        $(`#${errorId}`).text('Please enter a valid number');
        $(`[data-bs-target="#otherTab"]`).addClass('has-error');
    } else {
        $(`#${errorId}`).text('');
        $(`[data-bs-target="#otherTab"]`).removeClass('has-error');
    }
});

    // Real-time validation for city fields
    // Removed address field validations
$(document).ready(function () {
    // Function to validate email
    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    // Real-time validation
    $('[name="email"]').on('input', function () {
        const email = $(this).val().trim();
        const errorEl = $('#email_error');

        if (email === "") {
            errorEl.text(""); // clear if empty (optional: show "required")
        } else if (!validateEmail(email)) {
            errorEl.text("Please enter a valid email address (e.g. user@example.com)");
        } else {
            errorEl.text(""); // valid email → clear error
        }
    });

    // Final check on form submit
    $('form').on('submit', function (e) {
        let isValid = true;
        const email = $('[name="email"]').val().trim();
        const errorEl = $('#email_error');
        errorEl.text("");

        if (email === "") {
            // errorEl.text("Email is required");
            // isValid = false;
        } else if (!validateEmail(email)) {
            errorEl.text("Please enter a valid email address (e.g. user@example.com)");
            isValid = false;
        }

        if (!isValid) e.preventDefault(); // stop submit
    });
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
});

// Function to validate a specific tab
function validateTab(tabId) {
    let isValid = true;
    
    if (tabId === 'otherTab') {
        // Validate other tab fields
        const requiredFields = ['first_name', 'last_name', 'salutation'];
        
        requiredFields.forEach(field => {
            const value = $(`[name="${field}"]`).val();
            if (!value) {
                $(`#${field}_error`).text(`${field.replace('_', ' ')} is required`);
                isValid = false;
            }
        });
    }
    // Removed address tab validation
    
    return isValid;
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
</script>

<script>
$(document).ready(function() {
    // Image preview functionality
    $('#add_image').on('change', function(e) {
        const file = e.target.files[0];
        const preview = $('#add_image_preview');
        
        if (file) {
            // Check file size (5MB limit)
            if (file.size > 5 * 1024 * 1024) {
                $('#add_image_error').text('File size must be less than 5MB');
                $(this).val(''); // Clear the file input
                return;
            }
            
            // Check file type
            if (!file.type.match('image.*')) {
                $('#add_image_error').text('Please select a valid image file');
                $(this).val(''); // Clear the file input
                return;
            }
            
            // Clear any previous errors
            $('#add_image_error').text('');
            
            // Create preview
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.html(`<img src="${e.target.result}" class="avatar avatar-xl" alt="Customer Image">`);
            }
            
            reader.readAsDataURL(file);
        } else {
            // If no file selected, show default icon
            const existingImage = $('input[name="existing_image"]').val();
            if (existingImage) {
                preview.html(`<img src="../uploads/${existingImage}" class="avatar avatar-xl" alt="Customer Image">`);
            } else {
                preview.html('<i class="isax isax-image text-primary fs-24"></i>');
            }
        }
    });
    
    // Also add click handler for better UX
    $('.drag-upload-btn').on('click', function() {
        $('#add_image').click();
    });
    
    // Optional: Drag and drop functionality
    $('#add_image_preview').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('border-primary');
    }).on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('border-primary');
    }).on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('border-primary');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            $('#add_image')[0].files = files;
            $('#add_image').trigger('change');
        }
    });
});
</script>
<script>
$(document).ready(function() {
    // Initialize Select2 for address dropdowns with search functionality
    function initializeAddressSelect2() {
        // Billing address dropdowns
        $('#billing_country').select2({
            placeholder: "Select Country",
            allowClear: true,
            width: '100%'
        });
        
        $('#billing_state').select2({
            placeholder: "Select State", 
            allowClear: true,
            width: '100%'
        });
        
        $('#billing_city').select2({
            placeholder: "Select City",
            allowClear: true,
            width: '100%'
        });

        // Shipping address dropdowns
        $('#shipping_country').select2({
            placeholder: "Select Country",
            allowClear: true,
            width: '100%'
        });
        
        $('#shipping_state').select2({
            placeholder: "Select State",
            allowClear: true,
            width: '100%'
        });
        
        $('#shipping_city').select2({
            placeholder: "Select City",
            allowClear: true,
            width: '100%'
        });
    }

    // Initialize when page loads
    initializeAddressSelect2();

    // Re-initialize when tabs are shown (in case they're dynamically loaded)
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
        setTimeout(function() {
            initializeAddressSelect2();
        }, 100);
    });

    // Also re-initialize when AJAX calls complete for state/city loading
    $(document).ajaxComplete(function() {
        setTimeout(function() {
            initializeAddressSelect2();
        }, 100);
    });
});
</script>

<!-- Tab Persistence JavaScript -->
<script>
$(document).ready(function() {
    // Update hidden field when tab changes
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const tabId = $(e.target).attr('id');
        $('#activeTabField').val(tabId);
    });

    // Restore active tab from session on page load
    <?php if (isset($_SESSION['active_tab'])): ?>
        const storedTabId = '<?php echo $_SESSION['active_tab']; ?>';
        const storedTabButton = $('#' + storedTabId);
        
        if (storedTabButton.length) {
            // Deactivate all tabs
            $('.nav-link').removeClass('active');
            $('.tab-pane').removeClass('show active');
            
            // Activate the stored tab
            storedTabButton.addClass('active');
            $(storedTabButton.data('bs-target')).addClass('show active');
            
            // Update hidden field
            $('#activeTabField').val(storedTabId);
            
            // Clear session after use
            <?php unset($_SESSION['active_tab']); ?>
        }
    <?php endif; ?>
});
</script>

</body>
</html>