<?php include 'layouts/session.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php include 'layouts/title-meta.php'; ?> 
	<?php include 'layouts/head-css.php'; ?>
	
	<style>
		.error-message {
			color: #dc3545;
			font-size: 0.875rem;
			margin-top: 0.25rem;
			display: none;
		}
		.is-invalid {
			border-color: #dc3545 !important;
		}
		.is-invalid:focus {
			box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
		}
	</style>
</head>

<body class="bg-white">

    <!-- Start Main Wrapper -->
	<div class="main-wrapper auth-bg">

		<!-- Start Content -->
		<div class="container-fuild">
			<div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">

				<!-- start row -->
				<div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
					<div class="col-lg-4 mx-auto">
						<form id="forgotPasswordForm" action="process/action_sent_forgotpassword.php" method="POST" class="d-flex justify-content-center align-items-center" novalidate>
							<div class="d-flex flex-column justify-content-lg-center p-4 p-lg-0 pb-0 flex-fill">
								<div class=" mx-auto mb-5 text-center">
									<!-- <img src="assets/img/logo.svg" class="img-fluid" alt="Logo"> -->
								</div>
								<div class="card border-0 p-lg-3 shadow-lg rounded-2">
									<div class="card-body">
										<div class="text-center mb-3">
											<h5 class="mb-2">Forgot Password</h5>
											<p class="mb-0">No worries, we'll send you reset instructions</p>
										</div>
										<div class="mb-3">
											<label class="form-label">Email Address<span class="text-danger">*</span></label>
											<div class="input-group">
												<span class="input-group-text border-end-0">
													<i class="isax isax-sms-notification"></i>
												</span>
												<input type="email" name="email" id="email" value="" class="form-control border-start-0 ps-0" placeholder="Enter Email Address" required>
											</div>
											<span id="emailError" class="error-message">Please enter a valid email address</span>
											<?php 
												// Display server-side validation errors if they exist
												if (isset($_SESSION['error'])) {
													echo '<span class="error-message" style="display:block">' . $_SESSION['error'] . '</span>';
													unset($_SESSION['error']);
												}
											?>
										</div>
										<div class="mb-3">
											<button type="submit" name="submit" class="btn bg-primary-gradient text-white w-100">Reset Password</button>
										</div>
										<div class="text-center">
											<h6 class="fw-normal fs-14 text-dark mb-0">Return to
												<a href="login.php" class="hover-a"> Sign In</a>
											</h6>
										</div>
									</div><!-- end card body -->
								</div><!-- end card -->
							</div>
						</form>
					</div><!-- end col -->
				</div>
				<!-- end row -->

			</div>
		</div>
		<!-- End Content -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>
	
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const form = document.getElementById('forgotPasswordForm');
			const emailInput = document.getElementById('email');
			const emailError = document.getElementById('emailError');
			
			// Validate email format
			function isValidEmail(email) {
				const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				return re.test(String(email).toLowerCase());
			}
			
			// Validate form on submit
			form.addEventListener('submit', function(event) {
				let isValid = true;
				
				// Reset previous errors
				emailInput.classList.remove('is-invalid');
				emailError.style.display = 'none';
				
				// Validate email
				if (!emailInput.value.trim()) {
					emailError.textContent = 'Email address is required';
					emailError.style.display = 'block';
					
					isValid = false;
				} else if (!isValidEmail(emailInput.value.trim())) {
					emailError.textContent = 'Please enter a valid email address';
					emailError.style.display = 'block';
					
					isValid = false;
				}
				
				// Prevent form submission if validation fails
				if (!isValid) {
					event.preventDefault();
					event.stopPropagation();
				}
			});
			
			// Clear error when user starts typing
			emailInput.addEventListener('input', function() {
				if (this.classList.contains('is-invalid')) {
					this.classList.remove('is-invalid');
					emailError.style.display = 'none';
				}
			});
		});
	</script>
</body>

</html>