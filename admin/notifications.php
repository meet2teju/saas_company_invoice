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

                <!-- Start Breadcrumb -->
                <div class="mb-3">
					<h6>All Notifications</h6>
				</div>
                <!-- End Breadcrumb -->

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="d-flex me-2">
                                <a href="profile.php" class="avatar avatar-lg avatar-rounded">
                                    <img src="assets/img/profiles/avatar-19.jpg" alt="Elwis Mathew">
                                </a>
                            </div>
                            <div class="flex-fill ml-3">
                                <p class=" mb-0"><a href="profile.php" class="fs-14 fw-semibold">Elwis Mathew</a> <span>added a new product</span> <a href="profile.php" class="fs-14 fw-semibold">Redmi Pro 7 Mobile</a></p>
                                <span><i class="ti ti-clock me-1"></i>Just Now</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="d-flex me-2">
                                <a href="profile.php" class="avatar avatar-lg avatar-rounded">
                                    <img src="assets/img/profiles/avatar-18.jpg" alt="Elizabeth Olsen">
                                </a>
                            </div>
                            <div class="flex-fill ml-3">
                                <p class=" mb-0"><a href="profile.php" class="fs-14 fw-semibold">Elizabeth Olsen</a> <span>added a new product category</span> <a href="profile.php" class="fs-14 fw-semibold">Desktop Computers</a></p>
                                <span class="fs-12 d-flex align-items-center"><i class="ti ti-clock me-1"></i>4 min ago</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="d-flex me-2">
                                <a href="profile.php" class="avatar avatar-lg avatar-rounded">
                                    <img src="assets/img/profiles/avatar-17.jpg" alt="William Smith">
                                </a>
                            </div>
                            <div class="flex-fill ml-3">
                                <p class=" mb-0"><a href="profile.php" class="fs-14 fw-semibold">William Smith</a> <span>added a new sales list for</span> <a href="profile.php" class="fs-14 fw-semibold">January Month</a></p>
                                <span class="fs-12 d-flex align-items-center"><i class="ti ti-clock me-1"></i>6 min ago</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="d-flex me-2">
                                <a href="profile.php" class="avatar avatar-lg avatar-rounded">
                                    <img src="assets/img/profiles/avatar-15.jpg" alt="Lesley Grauer">
                                </a>
                            </div>
                            <div class="flex-fill ml-3">
                                <p class=" mb-0"><a href="profile.php" class="fs-14 fw-semibold">Lesley Grauer</a> <span>has updated invoice</span> <a href="profile.php" class="fs-14 fw-semibold">#987654</a></p>
                                <span class="fs-12 d-flex align-items-center"><i class="ti ti-clock me-1"></i>12 min ago</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="d-flex me-2">
                                <a href="profile.php" class="avatar avatar-lg bg-success avatar-rounded">
                                    <span class="avatar-title text-white">CE</span>
                                </a>
                            </div>
                            <div class="flex-fill ml-3">
                                <p class=" mb-0"><a href="profile.php" class="fs-14 fw-semibold">Carl Evans</a> <span>adjust the stock</span> <a href="profile.php" class="fs-14 fw-semibold">Apple Series 5 Watch</a></p>
                                <span class="fs-12 d-flex align-items-center"><i class="ti ti-clock me-1"></i>2 days ago</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="d-flex me-2">
                                <a href="profile.php" class="avatar avatar-lg bg-primary avatar-rounded">
                                    <span class="avatar-title text-white">MR</span>
                                </a>
                            </div>
                            <div class="flex-fill ml-3">
                                <p class=" mb-0"><a href="profile.php" class="fs-14 fw-semibold">Minerva Rameriz</a> <span>accepted Quotation</span><a href="profile.php" class="fs-14 fw-semibold"> #QUO0001</a></p>
                                <span class="fs-12 d-flex align-items-center"><i class="ti ti-clock me-1"></i>1 month ago</span>
                            </div>
                        </div>
                    </div>
                </div>

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