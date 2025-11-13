<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

$login_user_id = $_SESSION['crm_user_id'];

// Fetch password update date
$query = "SELECT password_updated_at FROM login WHERE id = '$login_user_id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Format date
$lastChanged = ($row['password_updated_at']) ? date("M d, Y", strtotime($row['password_updated_at'])) : "Never";
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

        <!-- Page Content -->
        <div class="page-wrapper">    
            <div class="content">
                <div class="row justify-content-center">
                    <div class="col-xl-12">
                        <div class="row settings-wrapper d-flex">
                            <?php include 'layouts/settings-sidebar.php'; ?>
                                
                            <div class="col-xl-9 col-lg-8">
                                <div class="mb-3">
                                    <div class="pb-3 border-bottom mb-3">
                                        <h6 class="mb-0">Security</h6>
                                    </div>
                                
                                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 border-bottom mb-3 pb-3">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-lg border bg-light me-2">
                                                <i class="isax isax-lock-circle text-dark fs-24"></i>
                                            </span>
                                            <div>
                                                <h5 class="fs-16 fw-semibold mb-1">Password</h5>
                                                <p class="fs-14">Set a unique password to secure the account</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-md badge-soft-danger me-3">
                                                Last Changed, <?= $lastChanged ?>
                                            </span>
                                            <a href="javascript:void(0);" id="openPasswordModal"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center"><i class="isax isax-edit"></i></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'layouts/footer.php'; ?>
        </div>

        <!-- Change Password Modal -->
        <div class="modal fade" id="change_password" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Change Password</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                
                    <form id="passwordForm" method="POST">
                        <div class="modal-body">
                          <div class="mb-3">
								<label class="form-label">Current Password<span class="text-danger ms-1">*</span></label>
								<div class="pass-group input-group">
									<span class="input-group-text border-end-0">
										<i class="isax isax-lock"></i>
									</span>
									<span class="isax toggle-password isax-eye-slash"cdata-target="#password"></span>
									<input type="password" name="password" id="password" class="pass-input form-control border-start-0 ps-0" placeholder="****************">
								</div>
                                    <span class="text-danger error-message" id="passwordError"></span>


							</div>
							<div class="mb-3">
								<label class="form-label">New Password<span class="text-danger ms-1">*</span></label>
								<div class="pass-group input-group mb-3">
									<span class="input-group-text border-end-0">
										<i class="isax isax-lock"></i>
									</span>
									<span class="isax toggle-passwords isax-eye-slash" data-target="#newpassword"></span>
								
                                <input type="password" name="newpassword" id="newpassword" class="pass-inputs form-control border-start-0 ps-0" placeholder="****************">
							
                            </div>
								    
<span class="text-danger" id="newpasswordError"></span>
								<div id="passwordInfo" class="mb-2"></div>
								<!-- <p class="text-gray-5">Use 8 or more characters with a mix of letters, numbers & symbols.</p> -->
							</div>
							<div>
								<label class="form-label">Confirm Password<span class="text-danger ms-1">*</span></label>
								<div class="pass-group input-group">
									<span class="input-group-text border-end-0">
										<i class="isax isax-lock"></i>
									</span>
									<span class="isax toggle-passworda isax-eye-slash" data-target="#renewpassword"></span>
									<input type="password" name="renewpassword"  name="renewpassword" class="pass-inputa form-control border-start-0 ps-0" placeholder="****************">
								</div>
                                    <span class="text-danger" id="renewpasswordError"></span>

							</div>
                        </div>
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layouts/vendor-scripts.php'; ?>
    


    <script>
$(document).ready(function() {
    // Open modal
    $('#openPasswordModal').click(function() {
        $('#change_password').modal('show');
    });

    // Toggle password visibility
    $('.toggle-password').click(function() {
        $(this).toggleClass('isax-eye isax-eye-slash');
        var input = $('#password');
        input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
    });

    $('.toggle-passwords').click(function() {
        $(this).toggleClass('isax-eye isax-eye-slash');
        var input = $('#newpassword');
        input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
    });

    $('.toggle-passworda').click(function() {
        $(this).toggleClass('isax-eye isax-eye-slash');
        var input = $('#renewpassword');
        input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
    });

    // Form submission with AJAX
    $('#passwordForm').submit(function(e) {
        e.preventDefault();
        
        // Clear previous errors - ONLY clear the error spans, not asterisks
        $('#passwordError, #newpasswordError, #renewpasswordError').empty();
        $('#generalError').remove();
        
        // Disable submit button
        $('#submitBtn').prop('disabled', true);
        
        $.ajax({
            url: 'process/action_change_password.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Success - reload page
                    $('#change_password').modal('hide');
                    location.reload();
                } else {
                    // Show errors
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            $('#' + key + 'Error').text(value);
                        });
                    }
                    
                    // Show general message if no field-specific errors
                    if (response.message && (!response.errors || Object.keys(response.errors).length === 0)) {
                        $('.modal-body').prepend('<div class="alert alert-danger" id="generalError">' + response.message + '</div>');
                    }
                }
            },
            error: function(xhr, status, error) {
                $('.modal-body').prepend('<div class="alert alert-danger" id="generalError">An error occurred. Please try again.</div>');
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false);
            }
        });
    });

    // Real-time password match validation
    $('#newpassword, #renewpassword').on('keyup', function() {
        const newPass = $('#newpassword').val();
        const confirmPass = $('#renewpassword').val();

        if (confirmPass && newPass !== confirmPass) {
            $('#renewpasswordError').text('Passwords do not match.');
        } else {
            $('#renewpasswordError').text('');
        }
    });
    
    // Clear errors when modal is closed
    $('#change_password').on('hidden.bs.modal', function () {
        $('#passwordError, #newpasswordError, #renewpasswordError').empty();
        $('#generalError').remove();
        $('#passwordForm')[0].reset();
    });
});
</script>


</body>
</html>