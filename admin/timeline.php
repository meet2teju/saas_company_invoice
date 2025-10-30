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
            <div class="content content-two">

                <!-- start row -->
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="mb-3 border-bottom pb-3">
                            <h6 class="mb-0">Timeline</h6>
                        </div>
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <p class="text-dark me-4 mb-0 timeline-date flex-shrink-0">07 Apr 2025</p>
                                    <div class="border-start ps-4 py-4 border-circle position-relative">
                                        <p class="text-dark fw-semibold mb-1">Invoice Marked as Paid</p>
                                        <p>Status updated to Paid</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <p class="text-dark me-4 mb-0 timeline-date flex-shrink-0">07 Apr 2025</p>
                                    <div class="border-start ps-4 py-4 border-circle position-relative">
                                        <p class="text-dark fw-semibold mb-1">Payment Received</p>
                                        <p>Payment received for Invoice #INV-1025</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <p class="text-dark me-4 mb-0 timeline-date flex-shrink-0">03 Apr 2025</p>
                                    <div class="border-start ps-4 py-4 border-circle position-relative">
                                        <p class="text-dark fw-semibold mb-1">Invoice Sent to Client</p>
                                        <p>Invoice #INV-1025 emailed to billing@abccorp.com</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <p class="text-dark me-4 mb-0 timeline-date flex-shrink-0">02 Apr 2025</p>
                                    <div class="border-start ps-4 py-4 border-circle position-relative">
                                        <p class="text-dark fw-semibold mb-1">Invoice Approved</p>
                                        <p>Invoice #INV-1025 approved for processing</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <p class="text-dark me-4 mb-0 timeline-date flex-shrink-0">01 Apr 2025</p>
                                    <div class="border-start ps-4 py-4 border-circle position-relative">
                                        <p class="text-dark fw-semibold mb-1">Invoice Created</p>
                                        <p>Invoice #INV-1025 was generated for Client: ABC Corp.</p>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
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