<?php include 'layouts/session.php'; ?>

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
                    <div class="col-xl-11">

						<!-- start row -->
                        <div class=" row settings-wrapper d-flex">
                            <div class="col-xl-3 col-lg-4">
                                <div class="card settings-card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="sidebars settings-sidebar">
                                            <div class="sidebar-inner">
                                                <div class="sidebar-menu p-0">
                                                    <ul>
                                                        <li>
                                                            <a href="customer-account-settings.php" class="fs-14 fw-medium d-flex align-items-center"><i class="isax isax-user-octagon fs-18 me-1"></i>Account Settings</a>
                                                        </li>
                                                        <li>
                                                            <a href="customer-security-settings.php" class="fs-14 fw-medium d-flex align-items-center active"><i class="isax isax-security-safe fs-18 me-1"></i>Security</a>
                                                        </li>
                                                        <li>
                                                            <a href="customer-plans-settings.php" class="fs-14 fw-medium d-flex align-items-center"><i class="isax isax-transaction-minus fs-18 me-1"></i>Plans & Billings</a>
                                                        </li>
                                                        <li>
                                                            <a href="customer-notification-settings.php" class="fs-14 fw-medium d-flex align-items-center"><i class="isax isax-notification fs-18 me-1"></i>Notifications</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end col -->
                            <div class="col-xl-9 col-lg-8">
                                <div class="mb-3">
                                    <div class="pb-3 border-bottom mb-3">
                                        <h6 class="mb-0">Security</h6>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 border-bottom mb-3 pb-3">
                                        <div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="p-1 bg-dark rounded text-white d-inline-flex align-items-center justify-content-center me-2">
													<i class="isax isax-eye fs-16"></i>
												</span>
                                                <h5 class="fs-16 fw-semibold">Password</h5>
                                            </div>
                                            <p class="fs-14">Set a unique password to secure the account</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-md badge-soft-danger me-3">Last Changed, Jan 16, 2025</span>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#change_password"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center"><i class="isax isax-edit"></i></span></a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 border-bottom mb-3 pb-3">
                                        <div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="p-1 bg-dark rounded text-white d-inline-flex align-items-center justify-content-center me-2">
													<i class="isax isax-security-safe fs-16"></i>
												</span>
                                                <h5 class="fs-16 fw-semibold">Two Factor Authentication</h5>
                                            </div>
                                            <p class="fs-14">Use your mobile phone to receive security PIN.</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-md badge-soft-danger">Enabled, Jan 16, 2025</span>
                                            <label class="d-flex align-items-center form-switch ps-3">
                                                <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            </label>
                                            <a href="javascript:void(0);"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#two-factor"><i class="isax isax-setting-2"></i></span></a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 border-bottom mb-3 pb-3">
                                        <div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="p-1 bg-dark rounded text-white d-inline-flex align-items-center justify-content-center me-2">
													<i class="isax isax-lock fs-16"></i>
												</span>
                                                <h5 class="fs-16 fw-semibold mb-1">Google Authentication</h5>
                                            </div>
                                            <p class="fs-14">Connect to Google</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-outline-light text-dark border d-flex align-items-center"><i class="fa fa-circle text-success fs-8 me-1"></i>Connected</span>
                                            <label class="d-flex align-items-center form-switch ps-3">
                                                <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 border-bottom mb-3 pb-3">
                                        <div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="p-1 bg-dark rounded text-white d-inline-flex align-items-center justify-content-center me-2">
													<i class="isax isax-call fs-16"></i>
												</span>
                                                <h5 class="fs-16 fw-semibold">Phone Number Verification</h5>
                                            </div>
                                            <p class="fs-14">Phone Number associated with the account</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-md badge-soft-success me-3">Verified<i class="isax isax-tick-circle ms-1"></i></span>
                                            <a href="javascript:void(0);" class="me-3" data-bs-toggle="modal" data-bs-target="#phone_verification"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center"><i class="isax isax-edit"></i></span></a>
                                            <a href="javascript:void(0);"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center"><i class="isax isax-trash"></i></span></a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 border-bottom mb-3 pb-3">
                                        <div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="p-1 bg-dark rounded text-white d-inline-flex align-items-center justify-content-center me-2">
													<i class="isax isax-sms-tracking fs-16"></i>
												</span>
                                                <h5 class="fs-16 fw-semibold">Email Verification</h5>
                                            </div>
                                            <p class="fs-14">Email Address associated with the account</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-md badge-soft-success me-3">Verified<i class="isax isax-tick-circle ms-1"></i></span>
                                            <a href="javascript:void(0);" class="me-3" data-bs-toggle="modal" data-bs-target="#email_verification"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center"><i class="isax isax-edit"></i></span></a>
                                            <a href="javascript:void(0);"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center"><i class="isax isax-trash"></i></span></a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 border-bottom mb-3 pb-3">
                                        <div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="p-1 bg-dark rounded text-white d-inline-flex align-items-center justify-content-center me-2">
													<i class="isax isax-device-message fs-16"></i>
												</span>
                                                <h5 class="fs-16 fw-semibold">Browsers & Devices</h5>
                                            </div>
                                            <p class="fs-14">The browsers & devices associated with the account</p>
                                        </div>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#view_device"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center"><i class="isax isax-eye"></i></span></a>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 border-bottom mb-3 pb-3">
                                        <div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="p-1 bg-dark rounded text-white d-inline-flex align-items-center justify-content-center me-2">
													<i class="isax isax-close-circle fs-16"></i>
												</span>
                                                <h5 class="fs-16 fw-semibold">Deactivate Account</h5>
                                            </div>
                                            <p class="fs-14">This will shutdown your account. Your account will be reactive when you sign in again</p>
                                        </div>
                                        <a href="javascript:void(0);"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center"><i class="isax isax-slash"></i></span></a>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                        <div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="p-1 bg-dark rounded text-white d-inline-flex align-items-center justify-content-center me-2">
													<i class="isax isax-info-circle fs-16"></i>
												</span>
                                                <h5 class="fs-16 fw-semibold">Delete Account</h5>
                                            </div>
                                            <p class="fs-14">Your account will be permanently deleted</p>
                                        </div>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_modal"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center"><i class="isax isax-trash"></i></span></a>
                                    </div>
                                </div>
                            </div><!-- end col -->
                        </div>
						<!-- end row -->

                    </div><!-- end col -->
                </div>
				<!-- end row -->

            </div>
			<!-- End Content -->

            <!-- Start Footer-->
            <div class="footer d-sm-flex align-items-center justify-content-between bg-white py-2 px-4">
                <p class="text-dark mb-0">&copy; 2025 <a href="javascript:void(0);" class="link-primary">Kanakku</a>, All Rights Reserved</p>
                <p class="text-dark">Version : 1.3.8</p>
            </div>
            <!-- End Footer-->
        </div>

        <!-- ========================
			End Page Content
		========================= -->

        <!-- Start Change Password Modal  -->
        <div id="change_password" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Start modal header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Change Password</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <!-- End modal header -->
                    <form action="security-settings.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Current Password<span class="text-danger ms-1">*</span></label>
                                <div class="pass-group input-group">
                                    <span class="input-group-text border-end-0">
										<i class="isax isax-lock"></i>
									</span>
                                    <span class="isax toggle-password isax-eye-slash"></span>
                                    <input type="password" class="pass-input form-control border-start-0 ps-0" placeholder="****************">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password<span class="text-danger ms-1">*</span></label>
                                <div class="pass-group input-group mb-3">
                                    <span class="input-group-text border-end-0">
										<i class="isax isax-lock"></i>
									</span>
                                    <span class="isax toggle-passwords isax-eye-slash"></span>
                                    <input type="password" class="pass-inputs form-control border-start-0 ps-0" placeholder="****************">
                                </div>
                                <div class="password-strength d-flex" id="passwordStrength">
                                    <span id="poor"></span>
                                    <span id="weak"></span>
                                    <span id="strong"></span>
                                    <span id="heavy"></span>
                                </div>
                                <div id="passwordInfo" class="mb-2"></div>
                                <p class="text-gray-5">Use 8 or more characters with a mix of letters, numbers & symbols.</p>
                            </div>
                            <div>
                                <label class="form-label">Confirm Password<span class="text-danger ms-1">*</span></label>
                                <div class="pass-group input-group">
                                    <span class="input-group-text border-end-0">
										<i class="isax isax-lock"></i>
									</span>
                                    <span class="isax toggle-passworda isax-eye-slash"></span>
                                    <input type="password" class="pass-inputa form-control border-start-0 ps-0" placeholder="****************">
                                </div>
                            </div>
                        </div>
                        <!-- End modal body -->
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                        <!-- End modal footer -->
                    </form>
                </div>
                <!-- End modal content-->
            </div>
            <!-- End modal dialog-->
        </div>
        <!-- / End Change Password Modal -->

        <!-- Start Phone Verification Modal  -->
        <div id="phone_verification" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Start modal header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Change Phone Number</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <!-- End modal header -->
                    <form action="security-settings.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Current Phone Number<span class="text-danger ms-1">*</span></label>
                                <input type="text" class="form-control" id="phone">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Phone Number<span class="text-danger ms-1">*</span></label>
                                <input type="text" class="form-control" id="phone2">
                                <p class="mt-2 d-inline-flex align-items-center"><i class="isax isax-info-circle me-1"></i>New phone number only updated once you verified </p>
                            </div>
                            <div>
                                <label class="form-label">Current Password<span class="text-danger ms-1">*</span></label>
                                <div class="pass-group input-group">
                                    <span class="input-group-text border-end-0">
										<i class="isax isax-lock"></i>
									</span>
                                    <span class="isax toggle-password isax-eye-slash"></span>
                                    <input type="password" class="pass-input form-control border-start-0 ps-0" placeholder="****************">
                                </div>
                            </div>
                        </div>
                        <!-- End modal body -->
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                        <!-- End modal footer -->
                    </form>
                </div>
                <!-- End modal content-->
            </div>
            <!-- End modal dialog-->
        </div>
        <!-- / End Phone Verification Modal -->

        <!-- Start Email Verification Modal  -->
        <div id="email_verification" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- End modal header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Change Email Address</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <!-- End modal header -->
                    <form action="security-settings.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Current Email Address<span class="text-danger ms-1">*</span></label>
                                <input type="email" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Email Address<span class="text-danger ms-1">*</span></label>
                                <input type="email" class="form-control">
                                <p class="mt-2 d-inline-flex align-items-center"><i class="isax isax-info-circle me-1"></i>New email address only updated once you verified </p>
                            </div>
                            <div>
                                <label class="form-label">Current Password<span class="text-danger ms-1">*</span></label>
                                <div class="pass-group input-group">
                                    <span class="input-group-text border-end-0">
										<i class="isax isax-lock"></i>
									</span>
                                    <span class="isax toggle-password isax-eye-slash"></span>
                                    <input type="password" class="pass-input form-control border-start-0 ps-0" placeholder="****************">
                                </div>
                            </div>
                        </div>
                        <!-- End modal body -->
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                        <!-- End modal footer -->
                    </form>
                </div>
                <!-- End modal content-->
            </div>
            <!-- End modal dialog-->
        </div>
        <!-- / End Email Verification Modal -->

        <!-- Start two step -->
        <div id="two-factor" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <!-- Start modal header -->
                    <div class="modal-header">
                        <h4 class="modal-title">SMS Two Factor Authentication</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <!-- End modal header -->
                    <form action="security-settings.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Phone Number<span class="text-danger ms-1">*</span></label>
                                <input type="text" class="form-control" id="phone3">
                            </div>
                            <p class="fs-13 mb-0">By providing your phone number, you agree to receive text messages from Figma to enable two-factor authentication when you log in. </p>
                        </div>
                        <!-- End modal body -->
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Verify</button>
                        </div>
                        <!-- End modal footer -->
                    </form>
                </div>
                <!-- End modal content-->
            </div>
            <!-- End modal dialog-->
        </div>
        <!-- / End two step -->

        <!-- Start View Device Modal  -->
        <div id="view_device" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <!-- Start modal header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Browsers & Devices</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <!-- End modal header -->
                    <div class="modal-body">
                        <!-- Table List -->
                        <div class="table-responsive border border-bottom-0">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Device</th>
                                        <th>Date</th>
                                        <th>IP Address</th>
                                        <th>Location</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-dark">Chrome - Windows</td>
                                        <td>24 Jan 2025, 10:00 AM</td>
                                        <td>232.222.12.72</td>
                                        <td>Newyork / USA</td>
                                        <td>
                                            <a href="javascript:void(0);"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center"><i class="isax isax-logout"></i></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark">Safari Macos</td>
                                        <td>19 Dec 2024, 09:30 AM</td>
                                        <td>224.111.12.75</td>
                                        <td>Newyork / USA</td>
                                        <td>
                                            <a href="javascript:void(0);"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center"><i class="isax isax-logout"></i></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark">Firefox Windows</td>
                                        <td>11 Dec 2024, 05:20 PM</td>
                                        <td>111.222.13.28</td>
                                        <td>Newyork / USA</td>
                                        <td>
                                            <a href="javascript:void(0);"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center"><i class="isax isax-logout"></i></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark">Safari Macos</td>
                                        <td>29 Nov 2024, 04:45 PM</td>
                                        <td>333.555.10.54</td>
                                        <td>Newyork / USA</td>
                                        <td>
                                            <a href="javascript:void(0);"><span class="badge badge-soft-light text-dark d-inline-flex align-items-center"><i class="isax isax-logout"></i></span></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /Table List -->
                    </div>
                    <!-- End modal body -->
                </div>
                <!-- End modal content-->
            </div>
            <!-- End modal dialog-->
        </div>
        <!-- / End View Device Modal -->

        <!-- Start Delete Account  -->
        <div id="delete_modal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <!-- Start modal header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Delete Account</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <!-- End modal header -->
                    <form action="security-settings.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <p class="text-dark fw-semibold mb-0">Why Are You Deleting Your Account?</p>
                                <p class="fs-13">We're sorry to see you go! To help us improve, please let us know your reason for deleting your account</p>
                            </div>
                            <div>
                                <div class="form-check mb-3 d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="Radio-2" id="Radio-sm-1">
                                    <div class="ms-2">
                                        <p class="text-dark fw-semibold mb-0">No longer using the service</p>
                                        <label class="form-check-label fs-13" for="Radio-sm-1">
                                            I no longer need this service and won’t be using it in the future.
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check mb-3 d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="Radio-2" id="Radio-sm-2">
                                    <div class="ms-2">
                                        <p class="text-dark fw-semibold mb-0">Privacy concerns</p>
                                        <label class="form-check-label fs-13" for="Radio-sm-2">
                                            I am concerned about how my data is handled and want to remove
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check mb-3 d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="Radio-2" id="Radio-sm-3">
                                    <div class="ms-2">
                                        <p class="text-dark fw-semibold mb-0">Too many notifications/emails</p>
                                        <label class="form-check-label fs-13" for="Radio-sm-3">
                                            I’m overwhelmed by the volume of notifications or emails
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check mb-3 d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="Radio-2" id="Radio-sm-4">
                                    <div class="ms-2">
                                        <p class="text-dark fw-semibold mb-0">Poor user experience</p>
                                        <label class="form-check-label fs-13" for="Radio-sm-4">
                                            I’ve had difficulty using the platform, and it didn’t meet my expectations
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="Radio-2" id="Radio-sm-5" checked>
                                    <label class="form-check-label text-dark fw-semibold" for="Radio-sm-5">
                                        Other (Please specify)
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Reason<span class="text-danger ms-1">*</span></label>
                                <textarea class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <!-- End modal body -->
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Confirm & Delete</button>
                        </div>
                        <!-- End modal footer -->
                    </form>
                </div>
                <!-- End modal content-->
            </div>
            <!-- End modal dialog-->
        </div>
        <!-- / End Delete Account  -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>

</body>

</html>        