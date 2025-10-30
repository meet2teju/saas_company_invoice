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

			<!-- Start Conatiner  -->
			<div class="content content-two">

				<!-- Page Header -->
				<div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
					<div>
						<h6>Payments</h6>
					</div>
					<div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
						<div class="dropdown">
							<a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center"  data-bs-toggle="dropdown">
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
							<a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_payment">
								<i class="isax isax-add-circle5 me-1"></i>New payment
							</a>
						</div>
					</div>
				</div>
				<!-- End Page Header -->
				
				<!-- Table Search -->
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
								<ul class="dropdown-menu  dropdown-menu">
                                    <li>
										<label class="dropdown-item d-flex align-items-center form-switch">
											<i class="fa-solid fa-grip-vertical me-3 text-default"></i>
											<input class="form-check-input m-0 me-2" type="checkbox" checked>
											<span>Cusomer</span>
										</label>
									</li>
                                    <li>
										<label class="dropdown-item d-flex align-items-center form-switch">
											<i class="fa-solid fa-grip-vertical me-3 text-default"></i>
											<input class="form-check-input m-0 me-2" type="checkbox" checked>
											<span>Payment ID</span>
										</label>
									</li>
									<li>
										<label class="dropdown-item d-flex align-items-center form-switch">
											<i class="fa-solid fa-grip-vertical me-3 text-default"></i>
											<input class="form-check-input m-0 me-2" type="checkbox" checked>
											<span>Paid Date</span>
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
											<input class="form-check-input m-0 me-2" type="checkbox">
											<span>Payment Mode</span>
										</label>
									</li>
								</ul>
							</div>
						</div>
					</div>	

                    <!-- Filter Info -->
					<div class="align-items-center gap-2 flex-wrap filter-info mt-3">
						<h6 class="fs-13 fw-semibold">Filters</h6>
						<span class="tag bg-light border rounded-1 fs-12 text-dark badge"><span class="num-count d-inline-flex align-items-center justify-content-center bg-success fs-10 me-1">5</span>Customers Selected<span class="ms-1 tag-close"><i class="fa-solid fa-x fs-10"></i></span></span>					
						<span class="tag bg-light border rounded-1 fs-12 text-dark badge"><span class="num-count d-inline-flex align-items-center justify-content-center bg-success fs-10 me-1">1</span>$10,000 - $25,500<span class="ms-1 tag-close"><i class="fa-solid fa-x fs-10"></i></span></span>											
						<a href="#" class="link-danger fw-medium text-decoration-underline ms-md-1">Clear All</a>
					</div>
					<!-- /Filter Info -->			
				</div>
				<!-- /Table Search -->
				
				<!-- Table List -->
				<div class="table-responsive">
					<table class="table table-nowrap datatable">
						<thead class="thead-light">
							<tr>
								<th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                    </div>
                                </th>
								<th class="no-sort">Customer</th>
								<th class="no-sort">Payment ID</th>
								<th>Paid Date</th>
								<th>Amount</th>
								<th>Payment method</th>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-28.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Emily Clark</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00025</a>
								</td>
								<td>22 Feb 2025, 05:30 AM</td>
								<td class="text-dark">$10,000</td>
								<td class="text-dark">Cash</td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-29.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">John Carter</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00024</a>
								</td>
								<td>07 Feb 2025, 03:28 AM</td>								
								<td class="text-dark">$25,750</td>								
								<td class="text-dark">Cheque</td>								
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-12.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Sophia White</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00023</a>
								</td>
								<td>30 Jan 2025, 07:23 AM</td>
								<td class="text-dark">$50,125</td>
								<td class="text-dark">Paypal</td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-06.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Michael Johnson</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00022</a>
								</td>
								<td>24 Jan 2025, 12:48 PM</td>
								<td class="text-dark">$75,900</td>
								<td class="text-dark">Bank Transfer</td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-30.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Olivia Harris</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00021</a>
								</td>
								<td>04 Jan 2025, 02:30 PM</td>
								<td class="text-dark">$99,999</td>
								<td class="text-dark">Stripe</td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-16.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">David Anderson</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00020</a>
								</td>
								<td>09 Dec 2024, 9:45 AM</td>								
								<td class="text-dark">$1,20,500</td>								
								<td class="text-dark">Cash</td>								
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-17.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Emma Lewis</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00019</a>
								</td>
								<td>02 Dec 2024, 11:28 AM</td>								
								<td class="text-dark">$2,50,000</td>								
								<td class="text-dark">Cheque</td>								
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-23.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Robert Thomas</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00018</a>
								</td>
								<td>15 Nov 2024, 05:15 PM</td>								
								<td class="text-dark">$5,00,750</td>								
								<td class="text-dark">Paypal</td>								
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-07.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Isabella Scott</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00017</a>
								</td>
								<td>30 Nov 2024, 04:37 PM</td>								
								<td class="text-dark">$7,50,300</td>								
								<td class="text-dark">Bank Transfer</td>								
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-31.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Daniel Martinez</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00016</a>
								</td>
								<td>12 Oct 2024, 12:28 AM</td>
								<td class="text-dark">$9,99,999</td>
								<td class="text-dark">Stripe</td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-32.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Charlotte Brown</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00015</a>
								</td>
								<td>05 Oct 2024, 09:09 AM</td>
								<td class="text-dark">$87,650</td>
								<td class="text-dark">Cash</td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-33.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">William Parker</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00014</a>
								</td>
								<td>09 Sep 2024, 10:28 AM</td>
								<td class="text-dark">$69,420</td>
								<td class="text-dark">Cheque</td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-34.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Mia Thompson</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00013</a>
								</td>
								<td>02 Sep 2024, 07:13 AM</td>
								<td class="text-dark">$33,210</td>
								<td class="text-dark">Paypal</td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-35.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Amelia Robinson</a></h6>
										</div>
									</div>
                                </td>
                                <td>
									<a href="javascript:void(0);" class="link-default">PAY00012</a>
								</td>
								<td>07 Aug 2024, 05:16 AM</td>
								<td class="text-dark">$2,10,000</td>
								<td class="text-dark">Bank Transfer</td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#edit_payment"><i class="isax isax-edit me-2"></i>Edit</a>
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
				<!-- /Table List -->

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
						<label class="form-label">Customers</label>
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
											<input class="form-check-input select-all m-0 me-2" type="checkbox">
											Select All
										</label>
										<a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline">Reset</a>
									</li>
									<li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											<span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/profiles/avatar-28.jpg" class="flex-shrink-0 rounded-circle" alt="img"></span>Emily Clark
										</label>
									</li>
									<li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											<span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/profiles/avatar-29.jpg" class="flex-shrink-0 rounded-circle" alt="img"></span>John Carter
										</label>
									</li>
									<li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											<span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/profiles/avatar-12.jpg" class="flex-shrink-0 rounded-circle" alt="img"></span>Sophia White
										</label>
									</li>
									<li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											<span class="avatar avatar-sm rounded-circle me-2"><img src="assets/img/profiles/avatar-06.jpg" class="flex-shrink-0 rounded-circle" alt="img"></span>Sophia White
										</label>
									</li>
								</ul>
								<div class="row g-2">
									<div class="col-6">
										<a href="#" class="btn btn-outline-white w-100"  id="close-filter">Cancel</a>
									</div>
									<div class="col-6">
										<a href="#" class="btn btn-primary w-100">Select</a>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <div class="mb-3">
						<label class="form-label">Amount</label>
						<div class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light  d-flex align-items-center justify-content-start fs-13 fw-normal border" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
								Select
							</a>
							<div class="dropdown-menu shadow-lg w-100 dropdown-info">							
								<div class="filter-range">
									<input type="text" id="range_03">
									<p>Range : <span class="text-gray-9">$200 - $5695</span></p>
								</div>
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
											<input class="form-check-input select-all m-0 me-2" type="checkbox">
											Select All
										</label>
										<a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline">Reset</a>
									</li>
									<li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											Cash
										</label>
									</li>
									<li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											Cheque
										</label>
									</li>
									<li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											Bank Transfer
										</label>
									</li>
									<li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											Paypal
										</label>
									</li>
                                    <li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											Stripe
										</label>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="mb-3">
						<label for="dateRangePicker" class="form-label">Date Range</label>
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
								<a href="#"  class="btn btn-outline-white w-100">Reset</a>
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

        <!-- Start Add Customer Modal  -->
		<div id="add_payment" class="modal fade">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Add New Payment</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
					</div>
					<form action="payments.php">
						<div class="modal-body">
							<div class="row gx-3">
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Payment ID <span class="text-danger">*</span></label>
										<input type="text" class="form-control">
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Customer <span class="text-danger">*</span></label>
										<select class="select">
											<option>Select</option>
											<option>Emily Clark</option>
											<option>John Carter</option>
											<option>Sophia White</option>
											<option>Michael Johnson</option>
											<option>Olivia Harris</option>
											<option>David Anderson</option>
											<option>Emma Lewis</option>
										</select>
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Invoice ID <span class="text-danger">*</span></label>
										<select class="select">
											<option>Select</option>
											<option>INC00025</option>
											<option>INC00024</option>
											<option>INC00023</option>
											<option>INC00022</option>
											<option>INC00021</option>
											<option>INC00020</option>
											<option>INC00019</option>
										</select>
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Reference Number <span class="text-danger">*</span></label>
										<input type="text" class="form-control">
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Payment Date <span class="text-danger">*</span></label>
										<div class="input-group position-relative">
											<input type="text" class="form-control datetimepicker rounded-end" placeholder="dd/mm/yyyy">
											<span class="input-icon-addon fs-16 text-gray-9">
												<i class="isax isax-calendar-2"></i>
											</span>
										</div>
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Payment Mode <span class="text-danger">*</span></label>
										<select class="select">
											<option>Select</option>
											<option>Cash</option>
											<option>Cheque</option>
											<option>Bank Transfer</option>
											<option>Paypal</option>
											<option>Stripe</option>
										</select>
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Amount <span class="text-danger">*</span></label>
										<input type="text" class="form-control">
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Paid Amount <span class="text-danger">*</span></label>
										<input type="text" class="form-control" value="$5200" readonly>
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Due Amount <span class="text-danger">*</span></label>
										<input type="text" class="form-control" value="$10000" readonly>
									</div>
								</div>                                    
								<div class="col-lg-12">
									<div class="mb-3">
										<label class="form-label">Notes</label>
										<textarea class="form-control" rows="3"></textarea>
									</div>
								</div>
								<div class="col-lg-12">
									<div>
										<label class="form-label">Attachment</label>
										<div class="file-upload drag-file w-100 d-flex align-items-center justify-content-center flex-column">
											<span class="upload-img d-block mb-2"><i class="isax isax-image text-primary"></i></span>
											<p class="mb-0 text-gray-9">Drop your files here or <a href="#" class="text-primary text-decoration-underline"> Browse</a></p>
											<input type="file" accept="video/image">
											<p>Maximum size : 50 MB</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer d-flex align-items-center justify-content-between">
							<button type="button" class="btn btn-outline-white">Cancel</button>
							<button type="submit" class="btn btn-primary">Create</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- End Add Customer Modal -->

        <!-- Start Edit Customer Modal  -->
		<div id="edit_payment" class="modal fade">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Edit Payment</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
					</div>
					<form action="payments.php">
						<div class="modal-body">
							<div class="row gx-3">
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Payment ID <span class="text-danger">*</span></label>
										<input type="text" class="form-control" value="PAY00025">
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Customer <span class="text-danger">*</span></label>
										<select class="select">
											<option>Select</option>
											<option selected>Emily Clark</option>
											<option>John Carter</option>
											<option>Sophia White</option>
											<option>Michael Johnson</option>
											<option>Olivia Harris</option>
											<option>David Anderson</option>
											<option>Emma Lewis</option>
										</select>
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Invoice ID <span class="text-danger">*</span></label>
										<select class="select">
											<option>Select</option>
											<option selected>INC00025</option>
											<option>INC00024</option>
											<option>INC00023</option>
											<option>INC00022</option>
											<option>INC00021</option>
											<option>INC00020</option>
											<option>INC00019</option>
										</select>
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Reference Number <span class="text-danger">*</span></label>
										<input type="text" class="form-control" value="REF17420">
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Payment Date <span class="text-danger">*</span></label>
										<div class="input-group position-relative">
											<input type="text" class="form-control datetimepicker rounded-end" placeholder="22 Feb 2025">
											<span class="input-icon-addon fs-16 text-gray-9">
												<i class="isax isax-calendar-2"></i>
											</span>
										</div>
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Payment Mode <span class="text-danger">*</span></label>
										<select class="select">
											<option>Select</option>
											<option selected>Cash</option>
											<option>Cheque</option>
											<option>Bank Transfer</option>
											<option>Paypal</option>
											<option>Stripe</option>
										</select>
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Amount <span class="text-danger">*</span></label>
										<input type="text" class="form-control" value="$4800">
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Paid Amount <span class="text-danger">*</span></label>
										<input type="text" class="form-control" value="$5200" readonly>
									</div>
								</div>
								<div class="col-lg-4 col-md-6">
									<div class="mb-3">
										<label class="form-label">Due Amount <span class="text-danger">*</span></label>
										<input type="text" class="form-control" value="$10000" readonly>
									</div>
								</div>                                    
								<div class="col-lg-12">
									<div class="mb-3">
										<label class="form-label">Notes</label>
										<textarea class="form-control" rows="3">Payment for raw materials</textarea>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="mb-3">
										<label class="form-label">Attachment</label>
										<div class="file-upload drag-file w-100 d-flex align-items-center justify-content-center flex-column">
											<span class="upload-img d-block mb-2"><i class="isax isax-image text-primary"></i></span>
											<p class="mb-0 text-gray-9">Drop your files here or <a href="#" class="text-primary text-decoration-underline"> Browse</a></p>
											<input type="file" accept="video/image">
											<p>Maximum size : 50 MB</p>
										</div>
									</div>
								</div>
								<div class="col-4">
									<div class="p-3 border rounded-2">
										<div class="d-flex align-items-center justify-content-between">
											<div class="d-flex align-items-center">
												<img src="assets/img/icons/document-icon.svg" alt="document-icon">
												<div class="ms-2">
													<p class="text-dark fw-medium mb-0">Attachment</p>
													<p>15.45 KB</p>
												</div>
											</div>
											<span class="avatar avatar-sm bg-light text-dark rounded-circle"><i class="isax isax-trash"></i></span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer d-flex align-items-center justify-content-between">
							<button type="button" class="btn btn-outline-white">Cancel</button>
							<button type="submit" class="btn btn-primary">Save Changes</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- End Edit Customer Modal -->

        <!-- Start Delete Modal  -->
		<div class="modal fade" id="delete_modal">
			<div class="modal-dialog modal-dialog-centered modal-sm">
				<div class="modal-content">
					<div class="modal-body text-center">
						<div class="mb-3">
							<img src="assets/img/icons/delete.svg" alt="img">
						</div>
						<h6 class="mb-1">Delete Payment</h6>
						<p class="mb-3">Are you sure,  you want to delete Payment?</p>
						<div class="d-flex justify-content-center">
							<a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
							<a href="payments.php" class="btn btn-primary">Yes, Delete</a>
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