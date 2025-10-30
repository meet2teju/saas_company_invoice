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
                <div class="row">
                    <div class="col-lg-12 mx-auto">

						<!-- start row -->
                        <div class="row settings-wrapper">

                            <?php include 'layouts/settings-sidebar.php'; ?>

                            <div class="col-xl-9 col-lg-8">
                                <form action="#cronjob.php">
                                    <div class="pb-3 border-bottom mb-3">
                                        <h6 class="mb-0">Cronjob</h6>
                                    </div>
                                    <div class="mb-3">
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-4">
                                                <p class="text-dark fw-medium">Cronjob Link</p>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" value="https://example.com/cronjob">
                                            </div>
                                        </div>
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <p class="text-dark fw-medium">Execution Interval</p>
                                            </div>
                                            <div class="col-md-8">
                                                <input class="input-tags form-control" id="inputBox" type="text" data-role="tagsinput" value="1 Day, 1 Hour">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between border-top pt-4">
                                        <a href="javascript:void(0);" class="btn btn-outline-white">Cancel</a>
                                        <a href="javascript:void(0);" class="btn btn-primary">Save Changes</a>
                                    </div>
                                </form>
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

	<?php include 'layouts/vendor-scripts.php'; ?>

</body>

</html>        