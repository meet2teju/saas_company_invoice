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
                        <h6>Subscriptions</h6>
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
                    </div>
                </div>
                <!-- End Page Header -->

                <!-- start row -->
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-1">
                                    <div>
                                        <p class="mb-1">Total Transactions</p>
                                        <h6 class="fs-16 fw-semibold">$5,340</h6>
                                    </div>
                                    <div>
                                        <p class="data-attributes">
                                            <span data-peity='{ "fill": ["#7539FF", "rgba(67, 87, 133, .09)"], "innerRadius": 16, "radius": 32 }'>6/7</span>
                                        </p>
                                    </div>
                                </div>
                                <p class="fs-13 text-center border rounded-1 p-2 bg-light mb-0">
                                    <span class="text-success">
                                        <i class="isax isax-send text-success me-1"></i>5.62%
                                    </span> from last month
                                </p>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-1">
                                    <div>
                                        <p class="mb-1">Total Subscribers</p>
                                        <h6 class="fs-16 fw-semibold">600</h6>
                                    </div>
                                    <div>
                                        <p class="data-attributes">
                                            <span data-peity='{ "fill": ["#3F8BFE", "rgba(67, 87, 133, .09)"], "innerRadius": 16, "radius": 32 }'>4/7</span>
                                        </p>
                                    </div>
                                </div>
                                <p class="fs-13 text-center border rounded-1 p-2 bg-light mb-0">
                                    <span class="text-success">
                                        <i class="isax isax-send text-success me-1"></i>11.4%
                                    </span> from last month
                                </p>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-1">
                                    <div>
                                        <p class="mb-1">Active Subscribers</p>
                                        <h6 class="fs-16 fw-semibold">560</h6>
                                    </div>
                                    <div>
                                        <p class="data-attributes">
                                            <span data-peity='{ "fill": ["#27AE60", "rgba(67, 87, 133, .09)"], "innerRadius": 16, "radius": 32 }'>2/7</span>
                                        </p>
                                    </div>
                                </div>
                                <p class="fs-13 text-center border rounded-1 p-2 bg-light mb-0">
                                    <span class="text-success">
                                        <i class="isax isax-send text-success me-1"></i>8.12%
                                    </span> from last month
                                </p>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-1">
                                    <div>
                                        <p class="mb-1">Expired Subscribers</p>
                                        <h6 class="fs-16 fw-semibold">40</h6>
                                    </div>
                                    <div>
                                        <p class="data-attributes">
                                            <span data-peity='{ "fill": ["#EF1E1E", "rgba(67, 87, 133, .09)"], "innerRadius": 16, "radius": 32 }'>1/7</span>
                                        </p>
                                    </div>
                                </div>
                                <p class="fs-13 text-center border rounded-1 p-2 bg-light mb-0">
                                    <span class="text-success">
                                        <i class="isax isax-send text-success me-1"></i>7.15%
                                    </span> from last month
                                </p>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <!-- Start Table Search -->
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
                                            <span>Plan</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Billing Cycle</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span>Payment Mode</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Amount</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Created On</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Expiring Date</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
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
                <!-- End Table Search -->

                <!-- Start Table List -->
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
                                <th>Plan</th>
                                <th>Billing Cycle</th>
                                <th>Payment Mode</th>
                                <th>Amount</th>
                                <th>Created On</th>
                                <th>Expiring On</th>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-01.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Trend Hive</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Advanced (Monthly)</td>
                                <td>30 Days</td>
                                <td>
                                    <p class="text-dark">Cash</p>
                                </td>
                                <td>
                                    <p class="text-dark">$200</p>
                                </td>
                                <td>22 Feb 2025</td>
                                <td>22 Mar 2025</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid
                                        <i class="isax isax-tick-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-02.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Quick Cart</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Basic (Yearly)</td>
                                <td>365 Days</td>
                                <td>
                                    <p class="text-dark">Cheque</p>
                                </td>
                                <td>
                                    <p class="text-dark">$600</p>
                                </td>
                                <td>07 Feb 2025</td>
                                <td>07 Mar 2025</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid
                                        <i class="isax isax-tick-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-03.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Tech Bazaar</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Advanced (Monthly)</td>
                                <td>30 Days</td>
                                <td>
                                    <p class="text-dark">Cash</p>
                                </td>
                                <td>
                                    <p class="text-dark">$200</p>
                                </td>
                                <td>30 Jan 2025</td>
                                <td>30 Feb 2025</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid
                                        <i class="isax isax-tick-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-04.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Harvest Basket</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Advanced (Monthly)</td>
                                <td>30 Days</td>
                                <td>
                                    <p class="text-dark">Cheque</p>
                                </td>
                                <td>
                                    <p class="text-dark">$200</p>
                                </td>
                                <td>17 Jan 2025</td>
                                <td>17 Feb 2025</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid
                                        <i class="isax isax-tick-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-05.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Elite Mart</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Enterprise (Monthly)</td>
                                <td>30 Days</td>
                                <td>
                                    <p class="text-dark">Cheque</p>
                                </td>
                                <td>
                                    <p class="text-dark">$400</p>
                                </td>
                                <td>04 Jan 2025</td>
                                <td>04 Feb 2025</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid
                                        <i class="isax isax-tick-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-06.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Prime Mart</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Advanced (Monthly)</td>
                                <td>30 Days</td>
                                <td>
                                    <p class="text-dark">Cash</p>
                                </td>
                                <td>
                                    <p class="text-dark">$200</p>
                                </td>
                                <td>09 Dec 2024</td>
                                <td>09 Jan 2025</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid
                                        <i class="isax isax-tick-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-07.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Trend Crafters</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Enterprise (Yearly)</td>
                                <td>365 Days</td>
                                <td>
                                    <p class="text-dark">Cash</p>
                                </td>
                                <td>
                                    <p class="text-dark">$4800</p>
                                </td>
                                <td>02 Dec 2024</td>
                                <td>02 Jan 2025</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid
                                        <i class="isax isax-tick-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-08.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Fresh Nest</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Basic (Monthly)</td>
                                <td>30 Days</td>
                                <td>
                                    <p class="text-dark">Cheque</p>
                                </td>
                                <td>
                                    <p class="text-dark">$50</p>
                                </td>
                                <td>30 Nov 2024</td>
                                <td>30 Dec 2025</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid
                                        <i class="isax isax-tick-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-09.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Gizmo Mart</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Basic (Yearly)</td>
                                <td>365 Days</td>
                                <td>
                                    <p class="text-dark">Cheque</p>
                                </td>
                                <td>
                                    <p class="text-dark">$600</p>
                                </td>
                                <td>15 Nov 2024</td>
                                <td>15 Dec 2025</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid
                                        <i class="isax isax-tick-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-10.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Dream Space</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Enterprise (Monthly)</td>
                                <td>30 Days</td>
                                <td>
                                    <p class="text-dark">Cash</p>
                                </td>
                                <td>
                                    <p class="text-dark">$400</p>
                                </td>
                                <td>12 Oct 2024</td>
                                <td>12 Nov 2024</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid
                                        <i class="isax isax-tick-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-11.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Mega Mart</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Advanced (Monthly)</td>
                                <td>30 Days</td>
                                <td>
                                    <p class="text-dark">Cheque</p>
                                </td>
                                <td>
                                    <p class="text-dark">$200</p>
                                </td>
                                <td>05 Oct 2024</td>
                                <td>05 Nov 2024</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid
                                        <i class="isax isax-tick-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-12.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Decor Ease</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Basic (Yearly)</td>
                                <td>365 Days</td>
                                <td>
                                    <p class="text-dark">Cash</p>
                                </td>
                                <td>
                                    <p class="text-dark">$600</p>
                                </td>
                                <td>09 Sep 2024</td>
                                <td>09 Oct 2024</td>
                                <td>
                                    <span class="badge badge-soft-danger d-inline-flex align-items-center">Unpaid
                                        <i class="isax isax-close-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-13.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Electro World</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Advanced (Monthly)</td>
                                <td>30 Days</td>
                                <td>
                                    <p class="text-dark">Cheque</p>
                                </td>
                                <td>
                                    <p class="text-dark">$200</p>
                                </td>
                                <td>02 Sep 2024</td>
                                <td>02 Oct 2024</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid
                                        <i class="isax isax-tick-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                            <img src="assets/img/icons/company-14.svg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Urban Home</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>Enterprise (Monthly)</td>
                                <td>30 Days</td>
                                <td>
                                    <p class="text-dark">Cheque</p>
                                </td>
                                <td>
                                    <p class="text-dark">$400</p>
                                </td>
                                <td>07 Aug 2024</td>
                                <td>07 Sep 2024</td>
                                <td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid
                                        <i class="isax isax-tick-circle ms-1"></i>
                                    </span>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice_modal"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
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
                <!-- End Table List -->

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
                                </ul>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="#" class="btn btn-outline-white w-100 close-filter">Cancel</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="#" class="btn btn-primary w-100">Select</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label id="dateRangePicker" class="form-label">Date Range</label>
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
                                <ul class="mb-3">
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
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Mode</label>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light  d-flex align-items-center justify-content-start fs-13 fw-normal border" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
								Select
							</a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info">
                                <ul class="mb-3">
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox"> Cash
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox"> Cheque
                                        </label>
                                    </li>
                                </ul>
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
                                            <i class="fa-solid fa-circle fs-6 text-success me-1"></i>Paid
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <i class="fa-solid fa-circle fs-6 text-danger me-1"></i>Unpaid
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
        <!-- end Filter -->

        <!-- Invoice View Modal Start -->
        <div id="view_invoice_modal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Preview</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <form action="#">
                        <div class="modal-body">
                            <!-- start row -->
                            <div class="row">
                                <div class="mx-auto">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-end flex-wrap row-gap-3 mb-3">
                                            <div class="d-flex align-items-center flex-wrap row-gap-3">
                                                <a href="#" class="btn btn-outline-white d-inline-flex align-items-center me-3"><i class="isax isax-document-like me-1"></i>Download PDF</a>
                                                <a href="#" class="btn btn-outline-white d-inline-flex align-items-center me-3"><i class="isax isax-message-notif me-1"></i>Send Email</a>
                                                <a href="#" class="btn btn-outline-white d-inline-flex align-items-center me-3"><i class="isax isax-printer me-1"></i>Print</a>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="bg-light p-4 rounded position-relative mb-3">
                                                    <div class="position-absolute top-0 end-0">
                                                        <img src="assets/img/bg/card-bg.png" alt="User Img">
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between border-bottom flex-wrap mb-3 pb-2 position-relative z-1">
                                                        <div class="mb-3">
                                                            <h4 class="mb-1">Invoice</h4>
                                                            <div class="d-flex align-items-center flex-wrap row-gap-3">
                                                                <div class="me-4">
                                                                    <h6 class="fs-14 fw-semibold mb-1">Dreams Technologies Pvt Ltd.,</h6>
                                                                    <p>15 Hodges Mews, High Wycombe HP12 3JL, United Kingdom</p>
                                                                </div>
                                                                <span><img src="assets/img/icons/not-paid.png" alt="User Img" width="48" height="48"></span>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <img src="assets/img/invoice-logo.svg" class="invoice-logo-dark" alt="img">
									                        <img src="assets/img/invoice-logo-white-2.svg" class="invoice-logo-white" alt="img">
                                                        </div>
                                                    </div>
                                                    <div class="row gy-3 position-relative z-1">
                                                        <div class="col-lg-4">
                                                            <div>
                                                                <h6 class="mb-2 fs-16 fw-semibold">Invoice Details</h6>
                                                                <div>
                                                                    <p class="mb-1">Invoice Number : <span class="text-dark">INV215654</span></p>
                                                                    <p class="mb-1">Issued On : <span class="text-dark">25 Jan 2025</span></p>
                                                                    <p class="mb-1">Due Date : <span class="text-dark">31 Jan 2025</span></p>
                                                                    <p class="mb-1">Recurring Invoice : <span class="text-dark">Monthly</span></p>
                                                                    <span class="badge bg-danger">Due in 8 days</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div>
                                                                <h6 class="mb-2 fs-16 fw-semibold">Billing From</h6>
                                                                <div>
                                                                    <h6 class="fs-14 fw-semibold mb-1">Kanakku Invoice Management</h6>
                                                                    <p class="mb-1">15 Hodges Mews, HP12 3JL, United Kingdom</p>
                                                                    <p class="mb-1">Phone : +1 54664 75945</p>
                                                                    <p class="mb-1">Email : info@example.com</p>
                                                                    <p class="mb-1">GST : 243E45767889</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div>
                                                                <h6 class="mb-2 fs-16 fw-semibold">Billing To</h6>
                                                                <div class="bg-white rounded p-3">
                                                                    <div class="d-flex align-items-center mb-1">
                                                                        <div>
                                                                            <span class="avatar avatar-lg flex-shrink-0 me-2">
                                                                                <img src="assets/img/billing-icon.jpg" alt="User Img">
                                                                            </span>
                                                                        </div>
                                                                        <p class="fs-14 fw-semibold text-dark">Timesquare Tech</p>
                                                                    </div>
                                                                    <p class="mb-1">299 Star Trek Drive, Florida, 3240, USA</p>
                                                                    <p class="mb-1">Phone : +1 54664 75945</p>
                                                                    <p class="mb-1">Email : info@example.com</p>
                                                                    <p class="mb-1">GST : 243E45767889</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <h6 class="mb-3">Product / Service Items</h6>
                                                    <div class="table-responsive rounded border-bottom-0 border">
                                                        <table class="table">
                                                            <thead class="thead-dark">
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Plan Name</th>
                                                                    <th>Rate</th>
                                                                    <th>Discount</th>
                                                                    <th>Tax</th>
                                                                    <th>Amount</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>1</td>
                                                                    <td class="text-dark">Basic</td>
                                                                    <td>$99.00</td>
                                                                    <td>0%</td>
                                                                    <td>$0.00</td>
                                                                    <td>$99.00</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="border-bottom mb-3">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                                                <div class="me-3">
                                                                    <p class="mb-2">Scan to the pay</p>
                                                                    <span><img src="assets/img/icons/qr.png" alt="User Img"></span>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-2">Bank Details</h6>
                                                                    <div>
                                                                        <p class="mb-1">Bank Name : <span class="text-dark">ABC Bank</span></p>
                                                                        <p class="mb-1">Account Number : <span class="text-dark">782459739212</span></p>
                                                                        <p class="mb-1">IFSC Code : <span class="text-dark">ABC0001345</span></p>
                                                                        <p class="mb-0">Payment Reference : <span class="text-dark">INV-20250220-001</span></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                                    <h6 class="fs-14 fw-semibold">Amount</h6>
                                                                    <h6 class="fs-14 fw-semibold">$99.00</h6>
                                                                </div>
                                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                                    <h6 class="fs-14 fw-semibold">CGST (0%)</h6>
                                                                    <h6 class="fs-14 fw-semibold">$0.00</h6>
                                                                </div>
                                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                                    <h6 class="fs-14 fw-semibold">SGST (0%)</h6>
                                                                    <h6 class="fs-14 fw-semibold">$0.00</h6>
                                                                </div>
                                                                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                                                    <h6 class="fs-14 fw-semibold">Discount (0%)</h6>
                                                                    <h6 class="fs-14 fw-semibold text-danger">- $0</h6>
                                                                </div>
                                                                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                                                    <h6>Total (USD)</h6>
                                                                    <h6>$99.00</h6>
                                                                </div>
                                                                <div>
                                                                    <h6 class="fs-14 fw-semibold mb-1">Total In Words</h6>
                                                                    <p>Ninety Nine Dollars</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-light d-flex align-items-center justify-content-between p-4 rounded card-bg">
                                                    <div>
                                                        <h6 class="fs-14 fw-semibold mb-1">Dreams Technologies Pvt Ltd.,</h6>
                                                        <p>15 Hodges Mews, High Wycombe HP12 3JL, United Kingdom</p>
                                                    </div>
                                                    <div>
                                                        <img src="assets/img/invoice-logo.svg" alt="User Img">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end row -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Invoice View Modal End -->

        <!-- Start Delete Modal  -->
        <div class="modal fade" id="delete_modal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <img src="assets/img/icons/delete.svg" alt="img">
                        </div>
                        <h6 class="mb-1">Delete Subscriptions</h6>
                        <p class="mb-3">Are you sure, you want to delete subscriptions?</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
                            <a href="subscriptions.php" class="btn btn-primary">Yes, Delete</a>
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