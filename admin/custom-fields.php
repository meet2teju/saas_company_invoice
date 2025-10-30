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
                    <div class="col-xl-12">
						<!-- start row -->
                        <div class=" row settings-wrapper d-flex">

                            <?php include 'layouts/settings-sidebar.php'; ?>

                            <div class="col-xl-9 col-lg-8">
                                <div class="mb-3">
                                    <div class="pb-3 border-bottom mb-3">
                                        <h6 class="mb-0">Custom Fields</h6>
                                    </div>
                                    <form action="esignatures.php">
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    <div class="input-icon-start position-relative">
                                                        <span class="input-icon-addon">
                                                            <i class="isax isax-search-normal"></i>
                                                        </span>
                                                        <input type="text" class="form-control form-control-sm bg-white" placeholder="Search">
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add_customfield" class="btn btn-primary d-flex align-items-center"><i class="isax isax-add-circle5 me-2"></i>New Field</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive table-nowrap">
                                            <table class="table border">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="no-sort">Module</th>
                                                        <th>Label</th>
                                                        <th>Type</th>
                                                        <th>Default Value</th>
                                                        <th>Required</th>
                                                        <th>Status</th>
                                                        <th class="no-sort"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <a href="javascript:void(0);" class="text-dark">Customers</a>
                                                        </td>
                                                        <td>Type </td>
                                                        <td>Select </td>
                                                        <td>Retail</td>
                                                        <td>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" role="switch" checked="">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" role="switch" checked="">
                                                            </div>
                                                        </td>
                                                        <td class="action-item">
                                                            <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                                <i class="isax isax-more"></i>
                                                            </a>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_customfield"><i class="isax isax-edit me-2"></i>Edit</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <a href="javascript:void(0);" class="text-dark">Supplier</a>
                                                        </td>
                                                        <td>Payment Method </td>
                                                        <td>Select </td>
                                                        <td>PayPal</td>
                                                        <td>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" role="switch">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" role="switch" checked="">
                                                            </div>
                                                        </td>
                                                        <td class="action-item">
                                                            <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                                <i class="isax isax-more"></i>
                                                            </a>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_customfield"><i class="isax isax-edit me-2"></i>Edit</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>
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

        <!-- Start Add Modal  -->
        <div id="add_customfield" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Custom Field</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <form action="custom-fields.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Module <span class="text-danger">*</span></label>
                                <select class="select">
                                    <option>Select</option>
                                    <option>Customers</option>
                                    <option>Supplier</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Input Type <span class="text-danger">*</span></label>
                                <select class="select">
                                    <option>Select</option>
                                    <option>Select</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Label <span class="text-danger">*</span></label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Default Value <span class="text-danger">*</span></label>
                                <input type="text" class="form-control">
                            </div>
                            <div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label">Required <span class="text-danger">*</span></label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" checked="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add New</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Add Modal -->

        <!-- Start Edit Modal  -->
        <div id="edit_customfield" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Custom Field</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <form action="custom-fields.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Module <span class="text-danger">*</span></label>
                                <select class="select">
                                    <option>Select</option>
                                    <option selected>Customers</option>
                                    <option>Supplier</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Input Type <span class="text-danger">*</span></label>
                                <select class="select">
                                    <option selected>Select</option>
                                    <option>Select</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Label <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="Type">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Default Value <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="Retail">
                            </div>
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label">Required <span class="text-danger">*</span></label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" checked="">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" checked="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Edit Modal -->

        <!-- Start Delete Modal  -->
        <div class="modal fade" id="delete_modal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <img src="assets/img/icons/delete.svg" alt="img">
                        </div>
                        <h6 class="mb-1">Delete Custom Field</h6>
                        <p class="mb-3">Are you sure, you want to delete custom field?</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
                            <a href="custom-fields.php" class="btn btn-primary">Yes, Delete</a>
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