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
                        <div class="row">

                            <?php include 'layouts/settings-sidebar.php'; ?>

                            <div class="col-xl-9 col-lg-8">
                                <div>
                                    <div class="pb-3 border-bottom mb-3">
                                        <h6 class="mb-0">Database Backup</h6>
                                    </div>

                                    <!-- Table Search -->
                                    <div class="row justify-content-between align-items-center pb-1">
                                        <div class="col-md-5 mb-3">
                                            <div class="input-icon-start position-relative">
                                                <span class="input-icon-addon">
													<i class="isax isax-search-normal"></i>
												</span>
                                                <input type="text" class="form-control form-control-sm bg-white" placeholder="Search">

                                            </div>
                                        </div>
                                        <div class="col-md-7 text-end mb-3">
                                            <a href="javascript:void(0);" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#generate_modal"><i class="isax isax-folder-connection5 me-1"></i>Generate Backup</a>
                                        </div>
                                    </div>
                                    <!-- /Table Search -->
									 
                                    <!-- Table List -->
                                    <div class="table-responsive table-nowrap">
                                        <table class="table border">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="no-sort">Template Name</th>
                                                    <th class="no-sort">Created On</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p class="text-dark">sales_db_backup_20250312.sql</p>
                                                    </td>
                                                    <td>
                                                        22 Feb 2025
                                                    </td>
                                                    <td class="action-item">
                                                        <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                            <i class="isax isax-more"></i>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <p class="text-dark">invoice_db_backup_2025-03-12_1430.sql</p>
                                                    </td>
                                                    <td>
                                                        07 Feb 2025
                                                    </td>
                                                    <td class="action-item">
                                                        <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                            <i class="isax isax-more"></i>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <p class="text-dark">customer_db_backup_2025-03-12.sql</p>
                                                    </td>
                                                    <td>
                                                        30 Jan 2025
                                                    </td>
                                                    <td class="action-item">
                                                        <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                            <i class="isax isax-more"></i>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <p class="text-dark">full_backup_2025-03-12.sql</p>
                                                    </td>
                                                    <td>
                                                        02 Jan 2025
                                                    </td>
                                                    <td class="action-item">
                                                        <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                            <i class="isax isax-more"></i>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /Table List -->
                                </div>
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

        <!-- Generate Modal Start -->
        <div class="modal fade" id="generate_modal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <i class="isax isax-folder-24 fs-48 text-primary fw-normal"></i>
                        </div>
                        <h6 class="mb-1">Generate Backup</h6>
                        <p class="mb-3">Are you sure you want to generate database backup?</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
                            <a href="system-backup.php" class="btn btn-primary">Generate</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Generate Modal End -->

        <!-- Delete Modal Start -->
        <div class="modal fade" id="delete_modal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <img src="assets/img/icons/delete.svg" alt="img">
                        </div>
                        <h6 class="mb-1">Delete Databse</h6>
                        <p class="mb-3">Are you sure, you want to delete Databse?</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
                            <a href="database-backup.php" class="btn btn-primary">Yes, Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Delete Modal End -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>

</body>

</html>        