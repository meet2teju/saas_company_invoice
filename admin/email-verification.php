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
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="d-flex flex-column justify-content-lg-center p-4 p-lg-0 pb-0 flex-fill">
                                <!-- <div class=" mx-auto mb-5 text-center">
                                    <img src="assets/img/logo.svg" class="img-fluid" alt="Logo">
                                </div> -->
                                <div class="card border-0 p-lg-3 shadow-lg rounded-2">
                                    <div class="card-body">
                                        <div class="mb-3 text-center">
                                            <span><i class="isax isax-tick-circle5 text-success fs-48"></i></span>
                                        </div>
                                        <div class="text-center mb-3">
                                            <h5 class="mb-2">Email Sent!</h5>
                                            <p class="mb-0">Check your email & change your password</p>
                                        </div>
                                        <div>
                                            <a href="login.php" class="btn bg-primary-gradient text-white w-100">Bake To Login</a>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div>
                        </div>
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