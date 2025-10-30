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

			<!-- Start conatiner -->
			<div class="content">

				<!-- start row -->
				<div class="row justify-content-center">
					<div class="col-xl-12">
						<!-- start row -->
						<div class=" row settings-wrapper d-flex">

							<?php include 'layouts/settings-sidebar.php'; ?>

							<div class="col-xl-9 col-lg-8">
								<div class="mb-0">
									<div class="pb-3 border-bottom mb-3">
										<h6 class="mb-0">Payments Method</h6>
									</div>
									<form action="sass-settings.php">
										<div class="card-body">
											<!-- start row -->
											<div class="row align-items-center saas-settings">
												<div class="col-md-6">
													<div class="card shadow-none">
														<div class="card-body">
																<div class="d-flex align-items-center justify-content-between mb-2">
																	<span><img src="assets/img/icons/paypal-name.svg" alt="image"></span>
																	<span class="badge badge-soft-success d-inline-flex align-items-center ms-2"><span class="badge-dot bg-success me-1"></span>Connected</span>
																</div>
															<p class="text-truncate line-clamb-2">PayPal is the faster, safer way to send and receive money </p>
														</div> <!-- end card body -->
														<div class="card-footer bg-light d-flex align-items-center justify-content-between ">
															<div class="d-flex align-items-center">
																<a class="btn btn-sm btn-dark rounded-2 d-inline-flex align-items-center justify-content-center p-1 me-2" href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash"></i></a>
																<a class="btn btn-sm btn-dark rounded-2 d-inline-flex align-items-center justify-content-center p-1" href="#" data-bs-toggle="modal" data-bs-target="#add_paypall"><i class="isax isax-setting-2"></i></a>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input m-0" type="checkbox" checked="">
															</div>
														</div> <!-- end card footer -->
													</div> <!-- end card -->
												</div> <!-- end col -->

												<div class="col-md-6">
													<div class="card shadow-none">
														<div class="card-body">
																<div class="d-flex align-items-center justify-content-between mb-2">
																	<span><img src="assets/img/icons/stripe-icon.svg" alt="image"></span>
																	<span class="badge badge-soft-success d-inline-flex align-items-center ms-2"><span class="badge-dot bg-success me-1"></span>Connected</span>
																</div>
															<p class="text-truncate line-clamb-2">APIs to accept cards, manage subscriptions, send money. </p>
														</div> <!-- end card body -->
														<div class="card-footer bg-light d-flex align-items-center justify-content-between ">
															<div class="d-flex align-items-center">
																<a class="btn btn-sm btn-dark rounded-2 d-inline-flex align-items-center justify-content-center p-1 me-2" href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash"></i></a>
																<a class="btn btn-sm btn-dark rounded-2 d-inline-flex align-items-center justify-content-center p-1" href="#" data-bs-toggle="modal" data-bs-target="#add_strip"><i class="isax isax-setting-2"></i></a>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input m-0" type="checkbox" checked="">
															</div>
														</div> <!-- end card footer -->
													</div> <!-- end card -->
												</div> <!-- end col -->

												<div class="col-md-6">
													<div class="card shadow-none">
														<div class="card-body">
																<div class="d-flex align-items-center justify-content-between mb-2">
																	<span><img src="assets/img/icons/razorpay-icon.svg" alt="image"></span>
																	<span class="badge badge-soft-success d-inline-flex align-items-center ms-2"><span class="badge-dot bg-success me-1"></span>Connected</span>
																</div>
															<p class="text-truncate line-clamb-2">Razorpay is an India's all in one payment solution. </p>
														</div> <!-- end card body -->
														<div class="card-footer bg-light d-flex align-items-center justify-content-between ">
															<div class="d-flex align-items-center">
																<a class="btn btn-sm btn-dark rounded-2 d-inline-flex align-items-center justify-content-center p-1 me-2" href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash"></i></a>
																<a class="btn btn-sm btn-dark rounded-2 d-inline-flex align-items-center justify-content-center p-1" href="#" data-bs-toggle="modal" data-bs-target="#add_razorpay"><i class="isax isax-setting-2"></i></a>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input m-0" type="checkbox" checked="">
															</div>
														</div> <!-- end card footer -->
													</div> <!-- end card -->
												</div> <!-- end col -->

												<div class="col-md-6">
													<div class="card shadow-none">
														<div class="card-body">
															<div class="d-flex align-items-center justify-content-between mb-2">
																<span><img src="assets/img/icons/applepay-icon.svg" alt="image"></span>
																<span class="badge badge-soft-primary d-inline-flex align-items-center ms-2"><span class="badge-dot bg-dark me-1"></span>Not Connected</span>
															</div>
															<p class="text-truncate line-clamb-2">Replaces your physical cards and cash with private and secure </p>
														</div> <!-- end col -->
														<div class="card-footer bg-light d-flex align-items-center justify-content-between ">
															<div class="d-flex align-items-center">
																<a class="btn btn-sm btn-dark rounded-2 d-inline-flex align-items-center justify-content-center p-1 me-2" href="#" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash"></i></a>
																<a class="btn btn-sm btn-dark rounded-2 d-inline-flex align-items-center justify-content-center p-1" href="#" data-bs-toggle="modal" data-bs-target="#add_applepay"><i class="isax isax-setting-2"></i></a>
															</div>
														</div>  <!-- end card footer -->
													</div> <!-- end card -->
												</div> <!-- end col -->
											</div>
											<!-- end row -->
										</div> <!-- end card body -->
									</form>
								</div> 
							</div> <!-- end col -->
						</div>
						<!-- end row -->
					</div> <!-- end col -->
				</div> 
				<!-- end row -->

			</div>
			<!-- End Content -->
			
			<?php include 'layouts/footer.php'; ?>

		</div>

		<!-- ========================
			End Page Content
		========================= -->

		<!-- Start Add Modal  -->
		<div id="add_paypall" class="modal fade">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">PayPal</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
					</div>
					<form action="payment-methods.php">
						<div class="modal-body">
							<div class="mb-3">
								<label class="form-label">From Email Address <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-3">
								<label class="form-label">API Key <span class="text-danger">*</span></label>
								<input type="text" class="form-control">                           
							</div>
							<div class="mb-0">
								<label class="form-label">Secret Key <span class="text-danger">*</span></label>
								<input type="text" class="form-control">                           
							</div>
						</div>
						<div class="modal-footer d-flex align-items-center justify-content-between gap-1">
							<button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">Submit</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- End Add Modal -->

		<!-- Start Add Modal  -->
		<div id="add_strip" class="modal fade">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Strip</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
					</div>
					<form action="payment-methods.php">
						<div class="modal-body">
							<div class="mb-3">
								<label class="form-label">From Email Address <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-3">
								<label class="form-label">API Key <span class="text-danger">*</span></label>
								<input type="text" class="form-control">                           
							</div>
							<div class="mb-0">
								<label class="form-label">Secret Key <span class="text-danger">*</span></label>
								<input type="text" class="form-control">                           
							</div>
						</div>
						<div class="modal-footer d-flex align-items-center justify-content-between gap-1">
							<button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">Submit</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- End Add Modal -->

		<!-- Start Add Modal  -->
		<div id="add_razorpay" class="modal fade">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Razorpay</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
					</div>
					<form action="payment-methods.php">
						<div class="modal-body">
							<div class="mb-3">
								<label class="form-label">From Email Address <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-3">
								<label class="form-label">API Key <span class="text-danger">*</span></label>
								<input type="text" class="form-control">                           
							</div>
							<div class="mb-0">
								<label class="form-label">Secret Key <span class="text-danger">*</span></label>
								<input type="text" class="form-control">                           
							</div>
						</div>
						<div class="modal-footer d-flex align-items-center justify-content-between gap-1">
							<button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">Submit</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- End Add Modal -->

		<!-- Start Add Modal  -->
		<div id="add_applepay" class="modal fade">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Apple Pay</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
					</div>
					<form action="payment-methods.php">
						<div class="modal-body">
							<div class="mb-3">
								<label class="form-label">From Email Address <span class="text-danger">*</span></label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-3">
								<label class="form-label">API Key <span class="text-danger">*</span></label>
								<input type="text" class="form-control">                           
							</div>
							<div class="mb-0">
								<label class="form-label">Secret Key <span class="text-danger">*</span></label>
								<input type="text" class="form-control">                           
							</div>
						</div>
						<div class="modal-footer d-flex align-items-center justify-content-between gap-1">
							<button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">Submit</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- End Add Modal -->

		<!-- Start Delete Modal  -->
        <div class="modal fade" id="delete_modal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <img src="assets/img/icons/delete.svg" alt="img">
                        </div>
                        <h6 class="mb-1">Delete Payment Method</h6>
                        <p class="mb-3">Are you sure, you want to delete payment method?</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
                            <a href="payment-methods.php" class="btn btn-primary">Yes, Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Delete Modal  -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>

</body>

</html>        