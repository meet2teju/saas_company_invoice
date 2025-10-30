<?php include 'layouts/session.php'; ?>
<?php
// session_start();
include '../config/config.php';
// print_r($_SESSION);
// $login_user_id = $_SESSION['crm_admin']['crm_user_id'];
$login_user_id = $_SESSION['crm_user_id'];

$query = "SELECT * FROM login WHERE id='$login_user_id'";
$result = mysqli_query($conn, $query);

$country_result = mysqli_query($conn, "SELECT * FROM countries");

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $country_id = $row['country'];
    $state_id = $row['state'];
    $city_id = $row['city'];

    $state_result = mysqli_query($conn, "SELECT * FROM states WHERE country_id = '$country_id'");
    $city_result = mysqli_query($conn, "SELECT * FROM cities WHERE state_id = '$state_id'");

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
                <div class="row justify-content-center">

                    <div class="col-xl-12">

                        <!-- start row -->
                        <div class="row settings-wrapper d-flex">

							<?php include 'layouts/settings-sidebar.php'; ?>

                            <div class="col-xl-9 col-lg-8">
                                <div class="mb-3">
                                    <div class="pb-3 border-bottom mb-3">
                                        <h6 class="mb-0">Account Settings</h6>
                                    </div>

                                 <!-- Flash message -->
                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                                <?php elseif (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                                <?php endif; ?>
                                

                                    <div class="d-flex align-items-center mb-3">
                                        <span class="bg-dark avatar avatar-sm me-2 flex-shrink-0"><i class="isax isax-info-circle fs-14"></i></span>
                                        <h6 class="fs-16 fw-semibold mb-0">General Information</h6>
                                    </div>
                                    <form action="process/action_profile.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <div class="mb-3">
                                            <span class="text-gray-9 fw-bold mb-2 d-flex">Profile Image<span class="text-danger ms-1">*</span></span>
                                            <div class="d-flex align-items-center">
                                                <div id="add_image_preview"  class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0">
                                                    <div class="position-relative d-flex align-items-center">
                                                   <img src="../uploads/<?php echo !empty($row['profile_img']) ? $row['profile_img'] : 'default.jpg'; ?>" 
                                                        alt="Profile Image" 
                                                        class="avatar avatar-xl me-3" 
                                                        style="border-radius: 8px;">

                                                        <!-- <a href="javascript:void(0);" class="rounded-trash trash-top d-flex align-items-center justify-content-center"><i class="isax isax-trash"></i></a> -->
                                                        <?php if (!empty($row['profile_img'])): ?>
                                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteModal" class="rounded-trash trash-top d-flex align-items-center justify-content-center">
                                                                <i class="isax isax-trash"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="d-inline-flex flex-column align-items-start">
                                                    <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                                        <i class="isax isax-image me-1"></i>Upload Image
                                                        <input type="file" class="form-control image-sign" name="profile_img" id="add_image" accept="image/*">
                                                    </div>
                                                    <span class="text-gray-9 fs-12">JPG or PNG format, not exceeding 5MB.</span>
                                                <span id="add_image_error" class="text-danger fs-12"></span>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="border-bottom mb-3 pb-2">

                                            <!-- start row -->
                                            <div class="row gx-3">

                                                <div class="col-lg-4 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" id="name" value="<?php echo $row['name']; ?>" class="form-control">
                                                    </div>
                                                </div><!-- end col -->
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                                        <input type="text"  name="email" value="<?php echo $row['email']; ?>" class="form-control">
                                                           <span class="text-danger" id="emailError"></span>

                                                    </div>
                                                </div><!-- end col -->
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Mobile Number </label>
                                            <input type="tel" name="phone_number" 
                                                value="<?php echo $row['phone_number']; ?>" 
                                                class="form-control" 
                                                maxlength="10" 
                                                pattern="[0-9]{10}" 
                                                title="Enter exactly 10 digits">
                                                    </div>
                                                </div><!-- end col -->
                        
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">DOB</label>
                                                        <div class="input-group position-relative mb-3">
                                                        <input type="date" name="dob" value="<?php echo isset($row['dob']) ? htmlspecialchars($row['dob']) : ''; ?>" class="form-control rounded-end">                                                            <span class="input-icon-addon fs-16 text-gray-9">
																<i class="isax isax-calendar-2"></i>
															</span>
                                                        </div>
                                                    </div>
                                                </div><!-- end col -->
                                            </div><!-- end row -->
                                        </div>
                                        <div class="border-bottom mb-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="bg-dark avatar avatar-sm me-2 flex-shrink-0"><i class="isax isax-info-circle fs-14"></i></span>
                                                <h6 class="fs-16 fw-semibold mb-0">Address Information</h6>
                                            </div>

                                            <!-- start row -->
                                            <div class="row gx-3">
                                                <div class="col-lg-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Address</label>
                                                        <input type="text" name="address"  value="<?php echo $row['address']; ?>" class="form-control">
                                                    </div>
                                                </div><!-- end col -->
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Country</label>
                                                        <select class="form-control select2" id="country" onchange="myfuncountry(this.value)" name="country">
                                                              <option value="">Select Country</option>
                                                             <?php while ($country = mysqli_fetch_assoc($country_result)) {
                                                                $selected = ($country['id'] == $country_id) ? 'selected' : '';
                                                                echo "<option value='{$country['id']}' $selected>{$country['name']}</option>";
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div><!-- end col -->
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">State</label>
                                                        <select class="form-control select2" id="state" name="state" onchange="myfunstate(this.value)">
                                                              <option value="">Select State</option>
                                                                <?php while ($state = mysqli_fetch_assoc($state_result)) {
                                                                    $selected = ($state['id'] == $state_id) ? 'selected' : '';
                                                                    echo "<option value='{$state['id']}' $selected>{$state['name']}</option>";
                                                                } ?>
                                                        </select>
                                                    </div>
                                                </div><!-- end col -->
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">City<span class="text-danger ms-1">*</span></label>
                                                        <select class="form-control select2" id="city" name="city">
                                                         <option value="">Select City</option>
                                                            <?php while ($city = mysqli_fetch_assoc($city_result)) {
                                                                $selected = ($city['id'] == $city_id) ? 'selected' : '';
                                                                echo "<option value='{$city['id']}' $selected>{$city['name']}</option>";
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div><!-- end col -->
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Postal Code</label>
                                                        <input type="number" name="zipcode"  value="<?php echo $row['zipcode']; ?>" class="form-control">
                                                    </div>
                                                </div><!-- end col -->
                                            </div>
                                            <!-- end row -->

                                        </div>

                                        <div class="d-flex align-items-center justify-content-between">
                                            <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='admin-dashboard.php'">
                                    Cancel
                                </button>   
                                            <button type="submit" name="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                        
                                    </form>
                                </div>
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->

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
     <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this profile image?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button type="button" name="delete" class="btn btn-danger" id="confirmDelete">Delete</button>
    </div>
    </div>
  </div>
</div>
<?php } ?>
	<?php include 'layouts/vendor-scripts.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->
<script>
    $(document).ready(function () {
        // $('.select2').select2();
    });

    function myfuncountry(datavalue) {
        $.ajax({
            url: 'process/action_get_state.php',
            type: 'POST',
            data: {datapost: datavalue},
            success: function(result) {
                $('#state').html(result).trigger('change');
                $('#state').select2();
            }
        });
        return false; 
    }

    function myfunstate(datavalue) {
        $.ajax({
            url: 'process/action_get_city.php',
            type: 'POST',
            data: {datapost: datavalue},
            success: function(result) {
                $('#city').html(result).trigger('change');
                $('#city').select2();
            }
        });
        return false; 
    }
</script>

<script>
$(document).ready(function () {
    $('form').on('submit', function (e) {
        let isValid = true;

        // Clear previous errors
        $(this).find('.text-danger.error').remove();

        // Validate Name
        const name = $('[name="name"]');
        if (name.val().trim() === '') {
            name.after('<span class="text-danger error">Name is required</span>');
            isValid = false;
        }

        // Validate Email
        const email = $('[name="email"]');
        if (email.val().trim() === '') {
            email.after('<span class="text-danger error">Email is required</span>');
            isValid = false;
        }

        // Validate Country
        // const country = $('[name="country"]');
        // if (country.val().trim() === '') {
        //     country.after('<span class="text-danger error">Country is required</span>');
        //     isValid = false;
        // }

        // // Validate State
        // const state = $('[name="state"]');
        // if (state.val().trim() === '') {
        //     state.after('<span class="text-danger error">State is required</span>');
        //     isValid = false;
        // }

        // Validate City
        const city = $('[name="city"]');
        if (city.val().trim() === '') {
            city.after('<span class="text-danger error">City is required</span>');
            isValid = false;
        }

        // Validate Profile Image (optional)
        const file = $('input[type="file"][name="profile_img"]')[0].files[0];
        if (file && file.size > 5 * 1024 * 1024) {
            $('input[type="file"][name="profile_img"]').after('<span class="text-danger error">File size must be less than 5MB</span>');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault(); // stop form submission
            // Scroll to first error
            $('html, body').animate({
                scrollTop: $('.text-danger.error:first').offset().top - 100
            }, 500);
        }
    });
});
</script>

<script>
document.getElementById("confirmDelete")?.addEventListener("click", function () {
    fetch("process/action_delete_profile-img.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "action=delete"
    })
    .then(res => res.text())
    .then(response => {
        if (response.trim() === "success") {
            location.reload();
        } else {
            alert("Error deleting profile image.");
        }
    });
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
 <script>
    $(document).ready(function () {
    
        

    // === Allow only text (no digits) ===
    $('#name').on('input', function () {
        this.value = this.value.replace(/[0-9]/g, '');
    });

  
});

</script>
<!-- <script>
    $('button[name="cancle"]').click(function() {
    const form = $(this).closest('form')[0];
    form.reset(); // reset text inputs

    // Reset Select2 dropdowns
    $('#country, #state, #city').val('').trigger('change');

    // Reset profile image preview
    const originalSrc = "../uploads/<?php echo !empty($row['profile_img']) ? $row['profile_img'] : 'default.jpg'; ?>";
    $('#add_image_preview').html(`<img src="${originalSrc}" class="avatar avatar-xl me-3" style="border-radius:8px;">`);

    // Clear image validation error
    $('#add_image_error').text('');
});

</script> -->

</body>

</html>
