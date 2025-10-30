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

                <!-- start row-->
                <div class="row justify-content-center">
                    <div class="col-xl-12">

						<!-- start row -->
                        <div class=" row settings-wrapper d-flex">
							
                            <?php include 'layouts/settings-sidebar.php'; ?>

                            <div class="col-xl-9 col-lg-8">
                                <div class="mb-3">
                                    <div class="pb-3 border-bottom mb-3">
                                        <h6 class="mb-0">Barcode</h6>
                                    </div>
                                    <form action="barcode-settings.php">
                                        <div class="vh-100 border-bottom mb-3">

											<!-- start row -->
                                            <div class="row align-items-center">
                                                <div class="col-8">
                                                    <label class="form-label fw-medium mb-3">Show Package Date </label>
                                                </div><!-- end col -->
                                                <div class="col-4 mb-3">
                                                    <div class="form-check form-switch d-flex justify-content-end">
                                                        <input class="form-check-input" type="checkbox" role="switch" checked>
                                                    </div>
                                                </div><!-- end col -->
                                            </div>
											<!-- end row -->

											<!-- start row -->
                                            <div class="row align-items-center">
                                                <div class="col-md-8 col-sm-12">
                                                    <label class="form-label fw-medium mb-3">MRP Label </label>
                                                </div><!-- end col -->
                                                <div class="col-md-4 col-sm-12">
                                                    <div>
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" value="MRP">
                                                        </div>
                                                    </div>
                                                </div><!-- end col -->
                                            </div>
											<!-- end row -->

											<!-- start row -->
                                            <div class="row align-items-center">
                                                <div class="col-8">
                                                    <label class="form-label fw-medium mb-3">Show Package Date </label>
                                                </div><!-- end col -->
                                                <div class="col-4 mb-3">
                                                    <div class="form-check form-switch d-flex justify-content-end">
                                                        <input class="form-check-input" type="checkbox" role="switch" checked>
                                                    </div>
                                                </div><!-- end col -->
                                            </div>
											<!-- end row -->

											<!-- start row -->
                                            <div class="row align-items-center">
                                                <div class="col-md-8 col-sm-12">
                                                    <label class="form-label fw-medium mb-3">Product Name Font Size </label>
                                                </div><!-- end col -->
                                                <div class="col-md-4 col-sm-12">
                                                    <div>
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" value="16">
                                                        </div>
                                                    </div>
                                                </div><!-- end col -->
                                            </div>
											<!-- end row -->

											<!-- start row -->
                                            <div class="row align-items-center">
                                                <div class="col-md-8 col-sm-12">
                                                    <label class="form-label fw-medium mb-3">MRP Font Size </label>
                                                </div><!-- end col -->
                                                <div class="col-md-4 col-sm-12">
                                                    <div>
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" value="16">
                                                        </div>
                                                    </div>
                                                </div><!-- end col -->
                                            </div>
											<!-- end row -->

											<!-- start row -->
                                            <div class="row align-items-center">
                                                <div class="col-md-8 col-sm-12">
                                                    <label class="form-label fw-medium mb-3">Barcode Size </label>
                                                </div><!-- end col -->
                                                <div class="col-md-4 col-sm-12">
                                                    <div>
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" value="10">
                                                        </div>
                                                    </div>
                                                </div><!-- end col -->
                                            </div>
											<!-- end row -->

                                        </div>

                                        <div class="modal-footer justify-content-between">
                                            <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-back me-2 border">Cancel</a>
                                            <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary">Save Changes</a>
                                        </div>

                                    </form>
                                </div>
                            </div><!-- end col -->
                        </div>
						<!-- end row -->

                    </div><!-- end col -->
                </div>
                <!-- end row-->

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