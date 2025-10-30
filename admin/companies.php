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

                <!-- Page Header -->
                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <h6>Companies</h6>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                <i class="isax isax-export-1 me-1"></i>Export
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#">Download as PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">Download as Excel</a>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_companies">
                                <i class="isax isax-add-circle5 me-1"></i>New Company
                            </a>
                        </div>
                    </div>
                </div>
                <!-- End Page Header -->

                <!-- start row -->
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center pb-0">
                                    <div class="me-2">
                                        <span class="avatar avatar-lg bg-soft-info">
											<i class="isax isax-buildings-25 text-info fs-28"></i>
										</span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Total Companies</p>
                                        <h6 class="fs-16 fw-semibold">987</h6>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center pb-0">
                                    <div class="me-2">
                                        <span class="avatar avatar-lg bg-success-subtle">
											<i class="isax isax-menu-board5 text-success fs-28"></i>
										</span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Active Companies</p>
                                        <h6 class="fs-16 fw-semibold">920</h6>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center pb-0">
                                    <div class="me-2">
                                        <span class="avatar avatar-lg bg-danger-subtle">
											<i class="isax isax-flash-slash5 text-danger fs-28"></i>
										</span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Inactive Company</p>
                                        <h6 class="fs-16 fw-semibold">92</h6>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center pb-0">
                                    <div class="me-2">
                                        <span class="avatar avatar-lg bg-primary-subtle">
											<i class="isax isax-map5 text-primary fs-28"></i>
										</span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Company Locations</p>
                                        <h6 class="fs-16 fw-semibold">200</h6>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <!-- Table Search Start -->
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="table-search d-flex align-items-center mb-0">
                                <div class="search-input">
                                    <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                                </div>
                            </div>
                            <a class="btn btn-outline-white fw-normal d-inline-flex align-items-center" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#customcanvas">
                                <i class="isax isax-filter me-1"></i>Filter
                            </a>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-white d-inline-flex align-items-center fw-medium" data-bs-toggle="dropdown">
                                    <i class="isax isax-sort me-1"></i>Sort By : <span class="fw-normal ms-1">Latest</span>
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Latest</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Oldest</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <i class="isax isax-grid-3 me-1"></i>Column
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-lg">
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Company</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Domain URL</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Plan</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span>Created On</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span>Status</span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Info -->
                    <div class="align-items-center gap-2 flex-wrap filter-info mt-3">
                        <h6 class="fs-13 fw-semibold">Filters</h6>
                        <span class="tag bg-light border rounded-1 fs-12 text-dark badge"><span class="num-count d-inline-flex align-items-center justify-content-center bg-success fs-10 me-1">5</span>Companies Selected<span class="ms-1 tag-close"><i class="fa-solid fa-x fs-10"></i></span></span>
                        <span class="tag bg-light border rounded-1 fs-12 text-dark badge"><span class="num-count d-inline-flex align-items-center justify-content-center bg-success fs-10 me-1">2</span>Plans Selected<span class="ms-1 tag-close"><i class="fa-solid fa-x fs-10"></i></span></span>
                        <a href="#" class="link-danger fw-medium text-decoration-underline ms-md-1">Clear All</a>
                    </div>
                    <!-- /Filter Info -->

                </div>
                <!-- Table Search End -->

                <!-- Table List Start -->
                <div class="table-responsive">
                    <table class="table table-nowrap datatable">
                        <thead class="thead-light">
                            <tr>
                                <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                    </div>
                                </th>
                                <th class="no-sort">Company</th>
                                <th class="no-sort">Email</th>
                                <th class="no-sort">Account URL</th>
                                <th>Plan</th>
                                <th>Created On</th>
                                <th class="no-sort">Status</th>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-01.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Trend Hive</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>trendhive@example.com</td>
                                <td>th.example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Advanced (Monthly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>22 Feb 2025</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Active
										<i class="isax isax-tick-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-02.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Quick Cart</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>quickcart@example.com</td>
                                <td>qc.example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Basic (Yearly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>07 Feb 2025</td>
                                <td>
                                    <span class="badge badge-soft-danger d-inline-flex align-items-center">Inactive
										<i class="isax isax-close-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-03.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Tech Bazaar</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>techbazaar@example.com</td>
                                <td>tb.example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Advanced (Monthly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>30 Jan 2025</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Active
										<i class="isax isax-tick-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-04.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Harvest Basket</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>haervestbasket@example.com</td>
                                <td>hb.example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Advanced (Monthly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>17 Jan 2025</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Active
										<i class="isax isax-tick-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-05.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Elite Mart</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>elitemart@example.com</td>
                                <td>em.example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Enterprise (Monthly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>04 Jan 2025</td>
                                <td>
                                    <span class="badge badge-soft-danger d-inline-flex align-items-center">Inactive
										<i class="isax isax-close-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-06.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Prime Mart</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>primemart@example.com</td>
                                <td>pm.example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Advanced (Monthly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>09 Dec 2024</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Active
										<i class="isax isax-tick-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-07.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Trend Crafters</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>trendcreafters@example.com</td>
                                <td>tc.example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Enterprise (Yearly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>02 Dec 2024</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Active
										<i class="isax isax-tick-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-08.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Fresh Nest</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>freshnest@example.com</td>
                                <td>fn.example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Basic (Monthly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>30 Nov 2024</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Active
										<i class="isax isax-tick-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-09.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Gizmo Mart</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>gizmomart@example.com</td>
                                <td>gm.@example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Basic (Yearly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>15 Nov 2024</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Active
										<i class="isax isax-tick-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-10.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Dream Space</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>dreamspace@example.com</td>
                                <td>ds.@example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Enterprise (Monthly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>12 Oct 2024</td>
                                <td>
                                    <span class="badge badge-soft-danger d-inline-flex align-items-center">Inactive
										<i class="isax isax-close-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-11.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Mega Mart</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>megamart@example.com</td>
                                <td>mm.@example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Advanced (Monthly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>05 Oct 2024</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Active
										<i class="isax isax-tick-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-12.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Decor Ease</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>decorease@example.com</td>
                                <td>de.@example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Basic (Yearly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>09 Sep 2024</td>
                                <td>
                                    <span class="badge badge-soft-danger d-inline-flex align-items-center">Inactive
										<i class="isax isax-close-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-13.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Electro World</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>electroworld@example.com</td>
                                <td>ew.@example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Advanced (Monthly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>02 Sep 2024</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Active
										<i class="isax isax-tick-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#companies_details">
                                            <img src="assets/img/icons/company-14.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Urban Home</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>urbanhome@example.com</td>
                                <td>uh.@example.com</td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Enterprise (Monthly)</p>
                                        <a href="javascript:void(0);" class="ms-3"><span class="btn btn-sm btn-light p-1 d-inline-flex align-items-center"><i class="isax isax-candle"></i></span></a>
                                    </div>
                                </td>
                                <td>07 Aug 2024</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Active
										<i class="isax isax-tick-circle ms-1"></i>
									</span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_companies"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_companies"><i class="isax isax-edit me-2"></i>Edit</a>
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
                <!-- Table List End -->

            </div>
            <!-- End Content -->

            <?php include 'layouts/footer.php'; ?>

        </div>

        <!-- ========================
			End Page Content
		========================= -->

        <!-- Start Filter -->
        <div class="offcanvas offcanvas-offset offcanvas-end" tabindex="-1" id="customcanvas">
            <div class="offcanvas-header d-block pb-0">
                <div class="border-bottom d-flex align-items-center justify-content-between pb-3">
                    <h6 class="offcanvas-title">Filter</h6>
                    <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                </div>
            </div>
            <div class="offcanvas-body pt-3">
                <form action="#">
                    <div class="mb-3">
                        <label class="form-label">Company</label>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light  d-flex align-items-center justify-content-start fs-13 fw-normal border" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
								Select
							</a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info">
                                <div class="mb-3">
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon fs-12">
											<i class="isax isax-search-normal"></i>
										</span>
                                        <input type="text" class="form-control form-control-sm" placeholder="Search">
                                    </div>
                                </div>
                                <ul class="mb-3">
                                    <li class="d-flex align-items-center justify-content-between mb-3">
                                        <label class="d-inline-flex align-items-center text-gray-9">
                                            <input class="form-check-input select-all m-0 me-2" type="checkbox"> Select All
                                        </label>
                                        <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline">Reset</a>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/icons/company-01.svg" class="flex-shrink-0 rounded-circle" alt="img"></span>Trend Hive
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/icons/company-02.svg" class="flex-shrink-0 rounded-circle" alt="img"></span>Quick Cart
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/icons/company-03.svg" class="flex-shrink-0 rounded-circle" alt="img"></span>Tech Bazaar
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/icons/company-04.svg" class="flex-shrink-0 rounded-circle" alt="img"></span>Harvest Basket
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/icons/company-05.svg" class="flex-shrink-0 rounded-circle" alt="img"></span>Elite Mart
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/icons/company-06.svg" class="flex-shrink-0 rounded-circle" alt="img"></span>Prime Mart
                                        </label>
                                    </li>
                                </ul>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="#" class="btn btn-outline-white w-100" id="close-filter">Cancel</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="#" class="btn btn-primary w-100">Select</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="input-group position-relative">
                            <input type="text" class="form-control date-range bookingrange rounded-end">
                            <span class="input-icon-addon fs-16 text-gray-9">
								<i class="isax isax-calendar-2"></i>
							</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Plan</label>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light  d-flex align-items-center justify-content-start fs-13 fw-normal border" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
								Select
							</a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info">
                                <div class="mb-3">
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon fs-12">
											<i class="isax isax-search-normal"></i>
										</span>
                                        <input type="text" class="form-control form-control-sm" placeholder="Search">
                                    </div>
                                </div>
                                <ul class="mb-3">
                                    <li class="d-flex align-items-center justify-content-between mb-3">
                                        <label class="d-inline-flex align-items-center text-gray-9">
                                            <input class="form-check-input select-all m-0 me-2" type="checkbox"> Select All
                                        </label>
                                        <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline">Reset</a>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox"> Advanced (Monthly)
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox"> Basic (Yearly)
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox"> Enterprise (Monthly)
                                        </label>
                                    </li>
                                </ul>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="#" class="btn btn-outline-white w-100" id="close-filter1">Cancel</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="#" class="btn btn-primary w-100">Select</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light  d-flex align-items-center justify-content-start fs-13 fw-normal border" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
								Select
							</a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info">
                                <ul class="mb-3">
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <i class="fa-solid fa-circle fs-6 text-success me-1"></i>Active
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <i class="fa-solid fa-circle fs-6 text-danger me-1"></i>Inactive
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="offcanvas-footer">
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="#" class="btn btn-outline-white w-100">Reset</a>
                            </div>
                            <div class="col-6">
                                <button data-bs-dismiss="offcanvas" class="btn btn-primary w-100" id="filter-submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Filter -->

        <!-- Start Add Modal  -->
        <div id="add_companies" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Company</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <form action="companies.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <span class="text-gray-9 fw-bold mb-2 d-flex">Image</span>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0">
                                        <i class="isax isax-image text-primary fs-24"></i>
                                    </div>
                                    <div class="d-inline-flex flex-column align-items-start">
                                        <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                            <i class="isax isax-image me-1"></i>Upload Image
                                            <input type="file" class="form-control image-sign" multiple="">
                                        </div>
                                        <span class="text-gray-9">JPG or PNG format, not exceeding 5MB.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Name<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email Address<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Account URL</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Website<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <div class="position-relative" id="passwordInput">
                                            <input type="password" class="pass-inputs form-control form-control-lg">
                                            <span class="isax toggle-passwords isax-eye-slash text-gray-7 fs-14"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password<span class="text-danger ms-1">*</span></label>
                                        <div class="position-relative">
                                            <input type="password" class="pass-inputa form-control">
                                            <span class="isax toggle-passworda isax-eye-slash text-gray-7 fs-14"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Plan<span class="text-danger ms-1">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Basic</option>
                                            <option>Standard</option>
                                            <option>Business</option>
                                            <option>Enterprise</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Plan Type<span class="text-danger ms-1">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Monthly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Currencies<span class="text-danger ms-1">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Dollar</option>
                                            <option>Euro</option>
                                            <option>Pound</option>
                                            <option>Rupees</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Language<span class="text-danger ms-1">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>English</option>
                                            <option>French</option>
                                            <option>German</option>
                                            <option>Arabic</option>
                                        </select>
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
        <!-- End Add Modal  -->

        <!-- Start Edit Modal  -->
        <div id="edit_companies" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Company</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <form action="companies.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <span class="text-gray-9 fw-bold mb-2 d-flex">Image</span>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0">
                                        <div class="position-relative d-flex align-items-center">
                                            <img src="assets/img/icons/shoes.jpg" class="avatar avatar-xl " alt="User Img">
                                            <a href="javascript:void(0);" class="rounded-trash trash-top d-flex align-items-center justify-content-center"><i class="isax isax-trash"></i></a>
                                        </div>
                                    </div>
                                    <div class="d-inline-flex flex-column align-items-start">
                                        <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                            <i class="isax isax-image me-1"></i>Upload Image
                                            <input type="file" class="form-control image-sign" multiple="">
                                        </div>
                                        <span class="text-gray-9">JPG or PNG format, not exceeding 5MB.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Name<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email Address<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Account URL</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Website<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <div class="position-relative" id="passwordInput">
                                            <input type="password" class="pass-inputs form-control form-control-lg">
                                            <span class="isax toggle-passwords isax-eye-slash text-gray-7 fs-14"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password<span class="text-danger ms-1">*</span></label>
                                        <div class="position-relative">
                                            <input type="password" class="pass-inputa form-control">
                                            <span class="isax toggle-passworda isax-eye-slash text-gray-7 fs-14"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Plan<span class="text-danger ms-1">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Basic</option>
                                            <option>Standard</option>
                                            <option>Business</option>
                                            <option>Enterprise</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Plan Type<span class="text-danger ms-1">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Monthly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Currencies<span class="text-danger ms-1">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Dollar</option>
                                            <option>Euro</option>
                                            <option>Pound</option>
                                            <option>Rupees</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Language<span class="text-danger ms-1">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>English</option>
                                            <option>French</option>
                                            <option>German</option>
                                            <option>Arabic</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <label class="form-label">Status<span class="text-danger ms-1">*</span></label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" checked="">
                                        </div>
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
        <!-- End Edit Modal  -->

        <!-- Start View Companies -->
        <div class="modal fade" id="view_companies">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upgrade Package</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="bg-transparent-light rounded border mb-3 p-3 mx-1">
                            <div class="row ">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <h6 class="fs-14 fw-semibold">Current Plan Details</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <span class="fs-13">Company Name</span>
                                        <h6 class="fs-14 fw-medium mb-0">Trend Hive</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <span class="fs-13">Plan Name</span>
                                        <h6 class="fs-14 fw-medium mb-0">Advanced</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <span class="fs-13">Plan Type</span>
                                        <h6 class="fs-14 fw-medium mb-0">Monthly</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="">
                                        <span class="fs-13">Price</span>
                                        <h6 class="fs-14 fw-medium mb-0">$200</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="">
                                        <span class="fs-13">Register Date</span>
                                        <h6 class="fs-14 fw-medium mb-0">03 Jan 2025</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="">
                                        <span class="fs-13">Expiring On</span>
                                        <h6 class="fs-14 fw-medium mb-0">03 Feb 2025</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <h6 class="fs-14 fw-bold">Change Plan</h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Plan Name<span class="text-danger ms-1">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Basic</option>
                                            <option>Standard</option>
                                            <option>Business</option>
                                            <option>Enterprise</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Plan Type<span class="text-danger ms-1">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Monthly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Amount<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Date </label>
                                        <div class="input-group position-relative mb-3">
                                            <input type="text" class="form-control datetimepicker rounded-end" placeholder="dd/mm/yyyy">
                                            <span class="input-icon-addon fs-16 text-gray-9">
                                                <i class="isax isax-calendar-2"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Next Payment Date </label>
                                        <div class="input-group position-relative mb-3">
                                            <input type="text" class="form-control datetimepicker rounded-end" placeholder="dd/mm/yyyy">
                                            <span class="input-icon-addon fs-16 text-gray-9">
                                                <i class="isax isax-calendar-2"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Expiring On </label>
                                        <div class="input-group position-relative mb-3">
                                            <input type="text" class="form-control datetimepicker rounded-end" placeholder="dd/mm/yyyy">
                                            <span class="input-icon-addon fs-16 text-gray-9">
                                                <i class="isax isax-calendar-2"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End View Companies -->

        <!-- Start Companies Detail-->
        <div class="modal fade" id="companies_details">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title d-flex align-items-center">
							Company Detail
						</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <form action="domain.php">
                        <div class="modal-body pb-0">
                            <div class="border-bottom mb-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="p-3 mb-3 br-5 bg-transparent-light">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="d-flex align-items-center file-name-icon justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <a href="#" class="avatar avatar-xxl bg-white border rounded-2">
                                                                    <img src="assets/img/icons/company-01.svg" class="img-fluid h-75" alt="img">
                                                                </a>
                                                                <div class="ms-2">
                                                                    <h6 class="fw-bold fs-14 mb-2"><a href="#">Trend Hive</a></h6>
                                                                    <span><i class="isax isax-location me-1"></i>4712 Cherry Ridge Drive Rochester, NY 14620.</span>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <a href="#" class="btn btn-outline-white d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_companies">
                                                                    <i class="isax isax-edit me-1"></i>Edit Profile
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <h6 class="fs-14 fw-bold">Basic Info</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <span class="fs-14">Email Address</span>
                                            <h6 class="fs-14 fw-medium mb-0">trendhive@example.com</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <span class="fs-14">Phone</span>
                                            <h6 class="fs-14 fw-medium mb-0">Advanced</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <span class="fs-14">Website</span>
                                            <h6 class="fs-14 fw-medium mb-0">www.example.com</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <span class="fs-14">Currency</span>
                                            <h6 class="fs-14 fw-medium mb-0">United Stated Dollar (USD)</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <span class="fs-14">Language</span>
                                            <h6 class="fs-14 fw-medium mb-0">English</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <p class="fs-14 mb-0">Status</p>
                                            <span class="badge badge-soft-success d-inline-flex align-items-center">Active
                                                <i class="isax isax-tick-circle ms-1"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <h6 class="fs-14 fw-bold">Plan Details</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <span class="fs-14">Plan Name</span>
                                        <h6 class="fs-14 fw-medium mb-0">Advanced</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <span class="fs-14">Plan Type</span>
                                        <h6 class="fs-14 fw-medium mb-0">Monthly</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <span class="fs-14">Price</span>
                                        <h6 class="fs-14 fw-medium mb-0">$200</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <span class="fs-14">Register Date</span>
                                        <h6 class="fs-14 fw-medium mb-0">03 Jan 2025</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <span class="fs-14">Expiring On</span>
                                        <h6 class="fs-14 fw-medium mb-0">03 Feb 2025</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Companies Details -->

        <!-- Start Delete Modal  -->
        <div class="modal fade" id="delete_modal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <img src="assets/img/icons/delete.svg" alt="img">
                        </div>
                        <h6 class="mb-1">Delete Company</h6>
                        <p class="mb-3">Are you sure, you want to delete company?</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
                            <a href="companies.php" class="btn btn-primary">Yes, Delete</a>
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