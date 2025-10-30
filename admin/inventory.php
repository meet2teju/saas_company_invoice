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
                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <h6>Inventory</h6>
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
                            <a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_modal">
                                <i class="isax isax-add-circle5 me-1"></i>New Inventory
                            </a>
                        </div>
                    </div>
                </div>
                <!-- End Breadcrumb -->

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
                                <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
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
                                <ul class="dropdown-menu  dropdown-menu">
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Product/Service</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Code</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Unit</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Quantity</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Selling Price</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span>Purchase Price</span>
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
                        <span class="tag bg-light border rounded-1 fs-12 text-dark badge"><span class="num-count d-inline-flex align-items-center justify-content-center bg-success fs-10 me-1">5</span>Product Selected<span class="ms-1 tag-close"><i class="fa-solid fa-x fs-10"></i></span></span>
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
                                <th class="no-sort">Product/Service</th>
                                <th class="no-sort">Code</th>
                                <th class="no-sort">Unit</th>
                                <th>Quantity</th>
                                <th>Selling Price</th>
                                <th>Purchase Price</th>
                                <th class="no-sort"></th>
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
                                            <img src="assets/img/products/product-01.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Apple iPhone 15</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="link-default">PR00025</a>
                                </td>
                                <td class="text-dark">Piece</td>
                                <td>2</td>
                                <td class="text-dark">$100</td>
                                <td class="text-dark">$98</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="#" class="btn btn-sm btn-soft-primary border-0 d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#view_history">
                                            <i class="isax isax-document-sketch5 me-1"></i> History
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-success border-0  d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockin">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock In
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-danger border-0 d-inline-flex align-items-center fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockout">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock Out
                                        </a>
                                    </div>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                            <img src="assets/img/products/product-02.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Dell XPS 13 9310</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="link-default">PR00014</a>
                                </td>
                                <td class="text-dark">Piece</td>
                                <td>12</td>
                                <td class="text-dark">$25</td>
                                <td class="text-dark">$24</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="#" class="btn btn-sm btn-soft-primary border-0 d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#view_history">
                                            <i class="isax isax-document-sketch5 me-1"></i> History
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-success border-0  d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockin">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock In
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-danger border-0 d-inline-flex align-items-center fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockout">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock Out
                                        </a>
                                    </div>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                            <img src="assets/img/products/product-03.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Bose QuietComfort 45</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="link-default">PR00012</a>
                                </td>
                                <td class="text-dark">Piece</td>
                                <td>2</td>
                                <td class="text-dark">$34</td>
                                <td class="text-dark">$58</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="#" class="btn btn-sm btn-soft-primary border-0 d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#view_history">
                                            <i class="isax isax-document-sketch5 me-1"></i> History
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-success border-0  d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockin">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock In
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-danger border-0 d-inline-flex align-items-center fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockout">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock Out
                                        </a>
                                    </div>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                            <img src="assets/img/products/product-04.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Nike Dri-FIT T-shirt</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="link-default">PR00016</a>
                                </td>
                                <td class="text-dark">Pack</td>
                                <td>24</td>
                                <td class="text-dark">$75</td>
                                <td class="text-dark">$72</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="#" class="btn btn-sm btn-soft-primary border-0 d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#view_history">
                                            <i class="isax isax-document-sketch5 me-1"></i> History
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-success border-0  d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockin">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock In
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-danger border-0 d-inline-flex align-items-center fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockout">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock Out
                                        </a>
                                    </div>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                            <img src="assets/img/products/product-05.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Adidas Ultraboost 22 Running Shoe</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="link-default">PR00022</a>
                                </td>
                                <td class="text-dark">Pack</td>
                                <td>13</td>
                                <td class="text-dark">$9</td>
                                <td class="text-dark">$89</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="#" class="btn btn-sm btn-soft-primary border-0 d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#view_history">
                                            <i class="isax isax-document-sketch5 me-1"></i> History
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-success border-0  d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockin">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock In
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-danger border-0 d-inline-flex align-items-center fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockout">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock Out
                                        </a>
                                    </div>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                            <img src="assets/img/products/product-06.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Samsung French Door Refrigerator</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="link-default">PR00047</a>
                                </td>
                                <td class="text-dark">Litre</td>
                                <td>67</td>
                                <td class="text-dark">$120</td>
                                <td class="text-dark">$115</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="#" class="btn btn-sm btn-soft-primary border-0 d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#view_history">
                                            <i class="isax isax-document-sketch5 me-1"></i> History
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-success border-0  d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockin">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock In
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-danger border-0 d-inline-flex align-items-center fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockout">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock Out
                                        </a>
                                    </div>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                            <img src="assets/img/products/product-07.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Dyson V15 Detect  Vacuum Cleaner</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="link-default">PR00014</a>
                                </td>
                                <td class="text-dark">Piece</td>
                                <td>13</td>
                                <td class="text-dark">$250</td>
                                <td class="text-dark">$240</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="#" class="btn btn-sm btn-soft-primary border-0 d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#view_history">
                                            <i class="isax isax-document-sketch5 me-1"></i> History
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-success border-0  d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockin">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock In
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-danger border-0 d-inline-flex align-items-center fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockout">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock Out
                                        </a>
                                    </div>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                            <img src="assets/img/products/product-08.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">HP Spectre x360 14</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="link-default">PR00031</a>
                                </td>
                                <td class="text-dark">Piece</td>
                                <td>25</td>
                                <td class="text-dark">$541</td>
                                <td class="text-dark">$525</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="#" class="btn btn-sm btn-soft-primary border-0 d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#view_history">
                                            <i class="isax isax-document-sketch5 me-1"></i> History
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-success border-0  d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockin">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock In
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-danger border-0 d-inline-flex align-items-center fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockout">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock Out
                                        </a>
                                    </div>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                            <img src="assets/img/products/product-09.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Dyson Supersonic Hair Dryer</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="link-default">PR00077</a>
                                </td>
                                <td class="text-dark">Litre</td>
                                <td>24</td>
                                <td class="text-dark">$741</td>
                                <td class="text-dark">$750</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="#" class="btn btn-sm btn-soft-primary border-0 d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#view_history">
                                            <i class="isax isax-document-sketch5 me-1"></i> History
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-success border-0  d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockin">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock In
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-danger border-0 d-inline-flex align-items-center fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockout">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock Out
                                        </a>
                                    </div>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                            <img src="assets/img/products/product-10.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Apple AirPods Pro</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="link-default">PR00045</a>
                                </td>
                                <td class="text-dark">Piece</td>
                                <td>65</td>
                                <td class="text-dark">$89</td>
                                <td class="text-dark">$49</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="#" class="btn btn-sm btn-soft-primary border-0 d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#view_history">
                                            <i class="isax isax-document-sketch5 me-1"></i> History
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-success border-0  d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockin">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock In
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-danger border-0 d-inline-flex align-items-center fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockout">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock Out
                                        </a>
                                    </div>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="isax isax-edit me-2"></i>Edit</a>
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
                                            <img src="assets/img/products/product-11.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div>
                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Levi’s 501 Original Fit Jeans</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="link-default">PR00045</a>
                                </td>
                                <td class="text-dark">Piece</td>
                                <td>23</td>
                                <td class="text-dark">$34</td>
                                <td class="text-dark">$36</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="#" class="btn btn-sm btn-soft-primary border-0 d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#view_history">
                                            <i class="isax isax-document-sketch5 me-1"></i> History
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-success border-0  d-inline-flex align-items-center me-1 fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockin">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock In
                                        </a>
                                        <a href="#" class="btn btn-sm btn-soft-danger border-0 d-inline-flex align-items-center fs-12 fw-regular" data-bs-toggle="modal" data-bs-target="#add_stockout">
                                            <i class="isax isax-document-sketch5 me-1"></i> Stock Out
                                        </a>
                                    </div>
                                </td>
                                <td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="isax isax-edit me-2"></i>Edit</a>
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
                        <label class="form-label">Product/Service</label>
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
                                            <span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/products/product-01.jpg" class="flex-shrink-0 rounded-circle" alt="img"></span>Apple iPhone 15
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/products/product-02.jpg" class="flex-shrink-0 rounded-circle" alt="img"></span>Dell XPS 13 9310
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/products/product-03.jpg" class="flex-shrink-0 rounded-circle" alt="img"></span>Bose QuietComfort 45
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/products/product-04.jpg" class="flex-shrink-0 rounded-circle" alt="img"></span>Nike Dri-FIT T-shirt
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/products/product-05.jpg" class="flex-shrink-0 rounded-circle" alt="img"></span>Adidas Ultraboost 22 Running Shoe
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/products/product-06.jpg" class="flex-shrink-0 rounded-circle" alt="img"></span>Samsung French Door Refrigerator
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
                        <label class="form-label">Unit</label>
                        <select class="select">
                            <option>Select</option>
                            <option>Kilograms (Kg)</option>
                            <option>Gram (g)</option>
                            <option>Liter (l)</option>
                            <option>Millimetre (mm)</option>
                            <option>Milliliter (ml)</option>
                            <option>Pack (pk)</option>
                            <option>Piece (pc)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Date Range</label>
                        <div class="input-group position-relative">
                            <input type="text" class="form-control date-range bookingrange rounded-end">
                            <span class="input-icon-addon fs-16 text-gray-9">
								<i class="isax isax-calendar-2"></i>
							</span>
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

        <!--  Start Add Modal  -->
        <div id="add_modal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Inventory</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <form action="inventory.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Item Type <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="Radio" id="Radio-sm-1" checked>
                                        <label class="form-check-label" for="Radio-sm-1">
                                            Product
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="Radio" id="Radio-sm-2">
                                        <label class="form-check-label" for="Radio-sm-2">
                                            Service
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Item <span class="text-danger">*</span></label>
                                <select class="select">
                                    <option>Select</option>
                                    <option>Apple iPhone 15</option>
                                    <option>Dell XPS 13 9310</option>
                                    <option>Bose QuietComfort 45</option>
                                    <option>Nike Dri-FIT T-shirt</option>
                                    <option>Adidas Ultraboost </option>
                                    <option>Samsung French</option>
                                    <option>Dyson V15 Detect</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Code</label>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Units</label>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Selling Price ($)</label>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Purchase Price ($)</label>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Stock In</option>
                                            <option>Stock Out</option>
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
        <!-- End Add Modal -->

        <!-- Start Edit Modal  -->
        <div id="edit_modal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit  Category</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <form action="inventory.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Item Type <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="Radio" id="Radio-sm-3" checked>
                                        <label class="form-check-label" for="Radio-sm-3">
                                            Product
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="Radio" id="Radio-sm-4">
                                        <label class="form-check-label" for="Radio-sm-4">
                                            Service
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Item <span class="text-danger">*</span></label>
                                <select class="select">
                                    <option>Select</option>
                                    <option selected>Apple iPhone 15</option>
                                    <option>Dell XPS 13 9310</option>
                                    <option>Bose QuietComfort 45</option>
                                    <option>Nike Dri-FIT T-shirt</option>
                                    <option>Adidas Ultraboost </option>
                                    <option>Samsung French</option>
                                    <option>Dyson V15 Detect</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Code</label>
                                        <input type="text" class="form-control" value="PR00025" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Units</label>
                                        <input type="text" class="form-control" value="Box" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Selling Price ($)</label>
                                        <input type="text" class="form-control" value="98" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Purchase Price ($)</label>
                                        <input type="text" class="form-control" value="78" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="15">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option selected>Stock In</option>
                                            <option>Stock Out</option>
                                        </select>
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

        <!-- Start Add Stockin Modal  -->
        <div id="add_stockin" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Stock In</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <form action="inventory.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Item Type <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="Radio" id="Radio-sm-5" checked>
                                        <label class="form-check-label" for="Radio-sm-5">
                                            Product
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="Radio" id="Radio-sm-6">
                                        <label class="form-check-label" for="Radio-sm-6">
                                            Service
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Item<span class="text-danger ms-1">*</span></label>
                                <select class="select" disabled>
                                    <option>Select</option>
                                    <option selected>Apple iPhone 15</option>
                                    <option>Dell XPS 13 9310</option>
                                    <option>Bose QuietComfort 45</option>
                                    <option>Nike Dri-FIT T-shirt</option>
                                    <option>Adidas Ultraboost </option>
                                    <option>Samsung French</option>
                                    <option>Dyson V15 Detect</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Code</label>
                                        <input type="text" class="form-control" value="PR00025" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Units<span class="text-danger ms-1">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Select</option>
                                            <option selected>Box</option>
                                            <option>Gram (g)</option>
                                            <option>Liter (l)</option>
                                            <option>Millimetre (mm)</option>
                                            <option>Milliliter (ml)</option>
                                            <option>Pack (pk)</option>
                                            <option>Piece (pc)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Quantity<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" value="15">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Quantity</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Add Stockin Modal -->

        <!-- Start Add Stockout Modal  -->
        <div id="add_stockout" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Stock Out</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <form action="inventory.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Item Type<span class="text-danger ms-1">*</span></label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="Radio" id="Radio-sm-7" checked>
                                        <label class="form-check-label" for="Radio-sm-7">
                                            Product
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="Radio" id="Radio-sm-8">
                                        <label class="form-check-label" for="Radio-sm-8">
                                            Service
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Item <span class="text-danger ms-1">*</span></label>
                                <select class="select" disabled>
                                    <option>Select</option>
                                    <option selected>Apple iPhone 15</option>
                                    <option>Dell XPS 13 9310</option>
                                    <option>Bose QuietComfort 45</option>
                                    <option>Nike Dri-FIT T-shirt</option>
                                    <option>Adidas Ultraboost </option>
                                    <option>Samsung French</option>
                                    <option>Dyson V15 Detect</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Code</label>
                                        <input type="text" class="form-control" value="PR00025" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Units<span class="text-danger ms-1">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Select</option>
                                            <option selected>Box</option>
                                            <option>Gram (g)</option>
                                            <option>Liter (l)</option>
                                            <option>Millimetre (mm)</option>
                                            <option>Milliliter (ml)</option>
                                            <option>Pack (pk)</option>
                                            <option>Piece (pc)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Quantity<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" value="15">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                            <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Remove Quantity</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Add Stockout Modal -->

        <!-- Start View History Modal  -->
        <div id="view_history" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Inventory History</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="bg-light d-flex align-items-center justify-content-between flex-wrap row-gap-3 p-3 rounded mb-3">
                            <div>
                                <h6 class="fs-14 fw-semibold mb-1">Apple iPhone 15</h6>
                                <span class="text-primary">PR00014</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-white me-3"><i class="isax isax-document-like me-1"></i>Download PDF</button>
                                <button type="button" class="btn btn-outline-white"><i class="isax isax-printer me-1"></i>Print</button>
                            </div>
                        </div>
                        <!-- Table List -->
                        <div class="table-responsive border border-bottom-0">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Unit</th>
                                        <th>Adjustments</th>
                                        <th>Stock</th>
                                        <th class="no-sort">Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <h6 class="fw-medium fs-14">06 Jan 2025, 04:12 AM</h6>
                                        </td>
                                        <td class="text-dark">Piece</td>
                                        <td class="text-success fw-medium">+2</td>
                                        <td>2</td>
                                        <td class="text-dark">Transfer</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h6 class="fw-medium fs-14">12 Jan 2025, 06:12 AM</h6>
                                        </td>
                                        <td class="text-dark">Piece</td>
                                        <td class="text-danger fw-medium">-4</td>
                                        <td>12</td>
                                        <td class="text-dark">Sale</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h6 class="fw-medium fs-14">13 Jan 2025, 05:13 PM</h6>
                                        </td>
                                        <td class="text-dark">Piece</td>
                                        <td class="text-success fw-medium">+14</td>
                                        <td>34</td>
                                        <td class="text-dark">Sale</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h6 class="fw-medium fs-14">18 Jan 2025, 08:16 AM</h6>
                                        </td>
                                        <td class="text-dark">Pack</td>
                                        <td class="text-success fw-medium">+45</td>
                                        <td>24</td>
                                        <td class="text-dark">Sale</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h6 class="fw-medium fs-14">22 Jan 2025, 09:20 PM</h6>
                                        </td>
                                        <td class="text-dark">Pack</td>
                                        <td class="text-danger fw-medium">-2</td>
                                        <td>13</td>
                                        <td class="text-dark">Damage</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h6 class="fw-medium fs-14">26 Jan 2025, 04:45 AM</h6>
                                        </td>
                                        <td class="text-dark">Liter</td>
                                        <td class="text-success fw-medium">+55</td>
                                        <td>67</td>
                                        <td class="text-dark">Transfer</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h6 class="fw-medium fs-14">31 Jan 2025, 09:22 APM</h6>
                                        </td>
                                        <td class="text-dark">Piece</td>
                                        <td class="text-success fw-medium">+47</td>
                                        <td>13</td>
                                        <td class="text-dark">Damage</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h6 class="fw-medium fs-14">02 Feb 2025, 05:16 AM</h6>
                                        </td>
                                        <td class="text-dark">Piece</td>
                                        <td class="text-success fw-medium">+32</td>
                                        <td>25</td>
                                        <td class="text-dark">Sale</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h6 class="fw-medium fs-14">04 Feb 2025, 08:12 AM</h6>
                                        </td>
                                        <td class="text-dark">Piece</td>
                                        <td class="text-danger fw-medium">-6</td>
                                        <td>24</td>
                                        <td class="text-dark">Damage</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h6 class="fw-medium fs-14">06 Feb 2025, 09:22 AM</h6>
                                        </td>
                                        <td class="text-dark">Piece</td>
                                        <td class="text-success fw-medium">+5</td>
                                        <td>65</td>
                                        <td class="text-dark">Sale</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /Table List -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End View History Modal -->

        <!-- Start Delete Modal  -->
        <div class="modal fade" id="delete_modal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <img src="assets/img/icons/delete.svg" alt="img">
                        </div>
                        <h6 class="mb-1">Delete Inventory</h6>
                        <p class="mb-3">Are you sure, you want to delete Inventory?</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
                            <a href="inventory.php" class="btn btn-primary">Yes, Delete</a>
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