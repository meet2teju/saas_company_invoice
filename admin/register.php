<?php include 'layouts/session.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php include 'layouts/title-meta.php'; ?> 

	<?php include 'layouts/head-css.php'; ?>
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

							<?php
							$errors = $_SESSION['errors'] ?? [];
							$old = $_SESSION['old'] ?? [];
							unset($_SESSION['errors'], $_SESSION['old']);
							?>

						<form action="process/action_register.php" method="POST" class="d-flex justify-content-center align-items-center">
							<div class="d-flex flex-column justify-content-lg-center p-4 p-lg-0 pt-lg-4 pb-0 flex-fill">
								<div class="mx-auto mb-5 text-center">
									<!-- <img src="assets/img/logo.svg"
										class="img-fluid" alt="Logo"> -->
								</div>
								<div class="card border-0 p-lg-3 shadow-lg rounded-2">
									<div class="card-body">
										<div class="text-center mb-3">
											<h5 class="mb-2">Sign Up</h5>
											<p class="mb-0">Please enter your details to create account</p>
										</div>
										<div class="mb-3">
											<label class="form-label">Full Name</label>
											<div class="input-group">
												<span class="input-group-text border-end-0">
													<i class="isax isax-profile"></i>
												</span>
												<input type="text" value="" name="name" class="form-control border-start-0 ps-0" placeholder="Name">
											</div>
										</div>
										<!-- Email -->
								<div class="mb-3">
									<label class="form-label">Email Address <span style="color: red;">*</span></label>
									<div class="input-group">
										<span class="input-group-text border-end-0">
											<i class="isax isax-sms-notification"></i>
										</span>
										<input type="text" name="email" id="email" class="form-control border-start-0 ps-0" placeholder="Enter Email Address">
										
									</div>
									<span id="email-error" class="text-danger small d-block mt-1"><?= $errors['email'] ?? '' ?></span>
								</div>

								<!-- Password -->
								<div class="mb-3">
									<label class="form-label">Password <span style="color: red;">*</span></label>
									<div class="pass-group input-group">
										<span class="input-group-text border-end-0">
											<i class="isax isax-lock"></i>
										</span>
										<span class="isax toggle-password isax-eye-slash"></span>
										<input type="password" name="password" id="password" class="pass-input form-control border-start-0 ps-0" placeholder="****************">
									</div>
									<span id="password-error" class="text-danger small d-block mt-1"><?= $errors['password'] ?? '' ?></span>
								</div>

								<!-- Confirm Password -->
								<div class="mb-3">
									<label class="form-label">Confirm Password <span style="color: red;">*</span></label>
									<div class="pass-group input-group">
										<span class="input-group-text border-end-0">
											<i class="isax isax-lock"></i>
										</span>
										<span class="isax toggle-passwords isax-eye-slash"></span>
										<input type="password" name="cpassword" id="cpassword" class="pass-input form-control border-start-0 ps-0" placeholder="****************">
									</div>
									<span id="cpassword-error" class="text-danger small d-block mt-1"><?= $errors['cpassword'] ?? '' ?></span>
								</div>

										<div class="d-flex align-items-center justify-content-between mb-3">
											<div class="d-flex align-items-center">
												<div class="form-check form-check-md mb-0">
													<input class="form-check-input" id="remember_me" type="checkbox">
													<label for="remember_me" class="form-check-label mt-0">I agree to the</label>
													<div class="d-inline-flex"><a href="#" class="text-decoration-underline me-1">Terms of Service</a> and <a href="#" class="text-decoration-underline ms-1"> Privacy Policy</a></div>
												</div>
											</div>
										</div>
										<div class="mb-1">
											<button type="submit" name="register" class="btn bg-primary-gradient text-white w-100">Sign Up</button>
										</div>
										
										<div class="text-center">
											<h6 class="fw-normal fs-14 text-dark mb-0">Already have an account?
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

</body>

</html>     