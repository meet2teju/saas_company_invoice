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
				<div class="mb-3">
					<h4>Contacts</h4>
				</div>
                <div class=" d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                    <div class="search-set mb-0">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="table-search d-flex align-items-center mb-0">
                                <div class="search-input">
                                    <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex table-dropdown mb-3 pb-1 right-content align-items-center flex-wrap row-gap-3">

                        <div class="dropdown me-2">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-white btn-md d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                Status
                            </a>
                            <ul class="dropdown-menu  dropdown-menu-end p-3">
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1">Active</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1">Inactive</a>
                                </li>

                            </ul>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-white btn-md d-inline-flex align-items-center fw-medium" data-bs-toggle="dropdown">
                                Sort By : Last 7 Days
                            </a>
                            <ul class="dropdown-menu  dropdown-menu-end p-3">
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1">Recently Added</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1">Ascending</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1">Desending</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1">Last Month</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1">Last 7 Days</a>
                                </li>
                            </ul>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-primary text-white ms-2" data-bs-toggle="modal" data-bs-target="#add-contact"><i class="ti ti-circle-plus me-1"></i>Add Contact</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-nowrap datatable">
                        <thead class="thead-light">
                            <tr>
                                <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                    </div>
                                </th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="no-sort"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar avatar-sm me-2">
                                            <img src="assets/img/users/user-33.jpg" alt="product" class="rounded-circle">
                                        </a>
                                        <a href="javascript:void(0);">Carl Evans</a>
                                    </div>
                                </td>
                                <td>carlevans@example.com</td>
                                <td>+12163547758</td>
                                <td>Admin</td>
                                <td><span class="d-inline-flex align-items-center p-1 pe-2 rounded-1 badge badge-soft-success text-success fs-10"><i class="ti ti-point-filled me-1 fs-11"></i>Active</span></td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit-contact"><i class="isax isax-eye me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar avatar-sm me-2">
                                            <img src="assets/img/users/user-02.jpg" alt="product" class="rounded-circle">
                                        </a>
                                        <a href="javascript:void(0);">Minerva Rameriz</a>
                                    </div>
                                </td>
                                <td>rameriz@example.com</td>
                                <td>+11367529510 </td>
                                <td>Manager</td>
                                <td><span class="d-inline-flex align-items-center p-1 pe-2 rounded-1 badge badge-soft-success text-success fs-10"><i class="ti ti-point-filled me-1 fs-11"></i>Active</span></td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit-contact"><i class="isax isax-eye me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar avatar-sm me-2">
                                            <img src="assets/img/users/user-34.jpg" alt="product" class="rounded-circle">
                                        </a>
                                        <a href="javascript:void(0);">Robert Lamon</a>
                                    </div>
                                </td>
                                <td>robert@example.com</td>
                                <td>+15362789414</td>
                                <td>Salesman</td>
                                <td><span class="d-inline-flex align-items-center p-1 pe-2 rounded-1 badge badge-soft-success text-success fs-10"><i class="ti ti-point-filled me-1 fs-11"></i>Active</span></td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit-contact"><i class="isax isax-eye me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar avatar-sm me-2">
                                            <img src="assets/img/users/user-35.jpg" alt="product" class="rounded-circle">
                                        </a>
                                        <a href="javascript:void(0);">Patricia Lewis</a>
                                    </div>
                                </td>
                                <td>patricia@example.com</td>
                                <td>+18513094627</td>
                                <td>Supervisor</td>
                                <td><span class="d-inline-flex align-items-center p-1 pe-2 rounded-1 badge badge-soft-success text-success fs-10"><i class="ti ti-point-filled me-1 fs-11"></i>Active</span></td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit-contact"><i class="isax isax-eye me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar avatar-sm  me-2">
                                            <img src="assets/img/users/user-36.jpg" alt="product" class="rounded-circle">
                                        </a>
                                        <a href="javascript:void(0);">Mark Joslyn</a>
                                    </div>
                                </td>
                                <td>markjoslyn@example.com</td>
                                <td>+14678219025</td>
                                <td>Store Keeper</td>
                                <td><span class="d-inline-flex align-items-center p-1 pe-2 rounded-1 badge badge-soft-success text-success fs-10"><i class="ti ti-point-filled me-1 fs-11"></i>Active</span></td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit-contact"><i class="isax isax-eye me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar avatar-sm me-2">
                                            <img src="assets/img/users/user-37.jpg" alt="product" class="rounded-circle">
                                        </a>
                                        <a href="javascript:void(0);">Marsha Betts</a>
                                    </div>
                                </td>
                                <td>marshabetts@example.com</td>
                                <td>+10913278319</td>
                                <td>Purchase</td>
                                <td><span class="d-inline-flex align-items-center p-1 pe-2 rounded-1 badge badge-soft-success text-success fs-10"><i class="ti ti-point-filled me-1 fs-11"></i>Active</span></td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit-contact"><i class="isax isax-eye me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar avatar-sm me-2">
                                            <img src="assets/img/users/user-28.jpg" alt="product" class="rounded-circle">
                                        </a>
                                        <a href="javascript:void(0);">Daniel Jude</a>
                                    </div>
                                </td>
                                <td>daieljude@example.com</td>
                                <td>+19125852947</td>
                                <td>Delivery Biker</td>
                                <td><span class="d-inline-flex align-items-center p-1 pe-2 rounded-1 badge badge-soft-success text-success fs-10"><i class="ti ti-point-filled me-1 fs-11"></i>Active</span></td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit-contact"><i class="isax isax-eye me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar avatar-sm me-2">
                                            <img src="assets/img/users/user-38.jpg" alt="product" class="rounded-circle">
                                        </a>
                                        <a href="javascript:void(0);">Emma Bates</a>
                                    </div>
                                </td>
                                <td>emmabates@example.com</td>
                                <td>+13671835209</td>
                                <td>Maintenance</td>
                                <td><span class="d-inline-flex align-items-center p-1 pe-2 rounded-1 badge badge-soft-success text-success fs-10"><i class="ti ti-point-filled me-1 fs-11"></i>Active</span></td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit-contact"><i class="isax isax-eye me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar avatar-sm me-2">
                                            <img src="assets/img/users/user-39.jpg" alt="product" class="rounded-circle">
                                        </a>
                                        <a href="javascript:void(0);">Richard Fralick</a>
                                    </div>
                                </td>
                                <td>richard@example.com</td>
                                <td>+19756194733</td>
                                <td>Quality Analyst</td>
                                <td><span class="d-inline-flex align-items-center p-1 pe-2 rounded-1 badge badge-soft-success text-success fs-10"><i class="ti ti-point-filled me-1 fs-11"></i>Active</span></td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit-contact"><i class="isax isax-eye me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar avatar-sm me-2">
                                            <img src="assets/img/users/user-03.jpg" alt="product" class="rounded-circle">
                                        </a>
                                        <a href="javascript:void(0);">Michelle Robison</a>
                                    </div>
                                </td>
                                <td>robinson@example.com</td>
                                <td>+19167850925</td>
                                <td>Accountant</td>
                                <td><span class="d-inline-flex align-items-center p-1 pe-2 rounded-1 badge badge-soft-success text-success fs-10"><i class="ti ti-point-filled me-1 fs-11"></i>Active</span></td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit-contact"><i class="isax isax-eye me-2"></i>Edit</a>
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
            </div>
			<!-- End Content -->

            <!-- Start Footer-->
            <div class="footer d-sm-flex align-items-center justify-content-between bg-white py-2 px-4 border-top">
                <p class="text-dark mb-0">&copy; 2025 <a href="javascript:void(0);" class="link-primary">Kanakku</a>, All Rights Reserved</p>
                <p class="text-dark">Version : 1.3.8</p>
            </div>
            <!-- End Footer-->
        </div>

        <!-- ========================
			End Page Content
		========================= -->

        <!-- Start Add Customer -->
        <div class="modal fade" id="add-contact">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="page-title">
                            <h5>Add Contact</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>

                    </div>
                    <form action="contacts.php">
                        <div class="modal-body">
                            <div class="new-employee-field">
                                <div class="profile-pic-upload bg-light">
                                    <div class="profile-pic">
                                        <span><i data-feather="plus-circle" class="plus-down-add"></i> Add Image</span>
                                    </div>
                                    <div class="mb-3">
                                        <div class="image-upload mb-0">
                                            <input type="file">
                                            <div class="image-uploads">
                                                <h4>Upload Image</h4>
                                            </div>
                                        </div>
                                        <p class="mt-2">JPEG, PNG up to 2 MB</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">First Name<span class="text-danger ms-1">*</span></label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Last Name<span class="text-danger ms-1">*</span></label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Email<span class="text-danger ms-1">*</span></label>
                                    <input type="email" class="form-control">
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Phone<span class="text-danger ms-1">*</span></label>
                                    <input type="tel" class="form-control">
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-0">
                                        <label class="form-label">Contact Type <span class="text-danger">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Admin</option>
                                            <option>Salesman</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn me-2 btn-light fs-13 fw-medium p-2 px-3 shadow-none" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fs-13 fw-medium p-2 px-3">Add Contact</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Add Customer -->

        <!-- Start Edit Customer -->
        <div class="modal fade" id="edit-contact">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="page-title">
                            <h5>Edit Contact</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <form action="contacts.php">
                        <div class="modal-body">
                            <div class="new-employee-field">
                                <div class="profile-pic-upload image-field">
                                    <div class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0">
                                        <div class="position-relative d-flex align-items-center">
                                            <img src="assets/img/users/user-01.jpg" class="avatar avatar-xl " alt="User Img">
                                            <a href="javascript:void(0);" class="rounded-trash trash-top d-flex align-items-center justify-content-center"><i class="isax isax-trash"></i></a>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="image-upload mb-0">
                                            <input type="file">
                                            <div class="image-uploads">
                                                <h4>Change Image</h4>
                                            </div>
                                        </div>
                                        <p class="mt-2">JPEG, PNG up to 2 MB</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">First Name<span class="text-danger ms-1">*</span></label>
                                    <input type="text" class="form-control" value="Carl">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Last Name<span class="text-danger ms-1">*</span></label>
                                    <input type="text" class="form-control" value="Evans">
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Email<span class="text-danger ms-1">*</span></label>
                                    <input type="email" class="form-control" value="carlevans@example.com">
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Phone<span class="text-danger ms-1">*</span></label>
                                    <input type="tel" class="form-control" value="+12163547758 ">
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-0">
                                        <label class="form-label">Contact Type <span class="text-danger">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option selected>Admin</option>
                                            <option>Salesman</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn me-2 btn-light fs-13 fw-medium p-2 px-3 shadow-none" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fs-13 fw-medium p-2 px-3">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Edit Customer -->

        <!-- Start Delete Modal  -->
        <div class="modal fade" id="delete_modal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <img src="assets/img/icons/delete.svg" alt="img">
                        </div>
                        <h6 class="mb-1">Delete Contact</h6>
                        <p class="mb-3">Are you sure, you want to delete ontact?</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
                            <a href="contacts.php" class="btn btn-primary">Yes, Delete</a>
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