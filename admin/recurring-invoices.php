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
						<h6>Recurring Invoices</h6>
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
							<a href="add-invoice.php" class="btn btn-primary d-flex align-items-center">
								<i class="isax isax-add-circle5 me-1"></i>New Invoice
							</a>
						</div>
					</div>
				</div>
				<!-- End Page Header -->

				<!-- start row -->
                <div class="row">
					<div class="col-xl-3 col-lg-4 col-md-6">
						<div class="card position-relative shadow-sm">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between mb-2 pb-2">
									<div class="text-truncate">
										<p class="mb-1 text-truncate">Total Recurring Invoices</p>
										<h6 class="fs-16 fw-semibold">950</h6>
									</div>
									<div>
										<span class="avatar avatar-lg bg-primary-subtle rounded-circle">
											<i class="isax isax-maximize-circle fs-24 text-primary"></i>
										</span>
									</div>
								</div>
                                <div class="progress progress-xs mb-2 progress-animate" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-primary" style="width: 80%">
                                    </div>
                                </div>
								<p class="fs-13 mb-0"><span class="text-success"><i class="isax isax-send text-success me-1"></i>5.62%</span> from last month</p>
							</div><!-- end card body -->
						</div><!-- end card -->
					</div><!-- end col -->
                    <div class="col-xl-3 col-lg-4 col-md-6">
						<div class="card position-relative shadow-sm">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between mb-2 pb-2">
									<div>
										<p class="mb-1">Paid Invoices</p>
										<h6 class="fs-16 fw-semibold">800</h6>
									</div>
									<div>
										<span class="avatar avatar-lg bg-success-subtle rounded-circle">
											<i class="isax isax-tick-circle fs-24 text-success"></i>
										</span>
									</div>
								</div>
                                <div class="progress progress-xs mb-2 progress-animate" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-success" style="width: 80%">
                                    </div>
                                </div>
                                <p class="fs-13 mb-0"><span class="text-success"><i class="isax isax-send text-success me-1"></i>11.4%</span> from last month</p>	
							</div><!-- end card body -->
						</div><!-- end card -->
					</div><!-- end col -->
                    <div class="col-xl-3 col-lg-4 col-md-6">
						<div class="card position-relative shadow-sm">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between mb-2 pb-2">
									<div>
										<p class="mb-1">Expired Invoices</p>
										<h6 class="fs-16 fw-semibold">150</h6>
									</div>
									<div>
										<span class="avatar avatar-lg bg-warning-subtle rounded-circle">
											<i class="isax isax-info-circle fs-24 text-warning"></i>
										</span>
									</div>
								</div>
                                <div class="progress progress-xs mb-2 progress-animate" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-warning" style="width: 80%">
                                    </div>
                                </div>
                                <p class="fs-13 mb-0"><span class="text-success"><i class="isax isax-send text-success me-1"></i>8.52%</span> from last month</p>
                            </div><!-- end card body -->
						</div><!-- end card -->
					</div><!-- end col -->
                    <div class="col-xl-3 col-lg-4 col-md-6">
						<div class="card position-relative shadow-sm">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between mb-2 pb-2">
									<div>
										<p class="mb-1">Total Revenue</p>
										<h6 class="fs-16 fw-semibold">₹500,000</h6>
									</div>
									<div>
										<span class="avatar avatar-lg bg-danger-subtle rounded-circle">
											<i class="isax isax-dollar-circle fs-24 text-danger"></i>
										</span>
									</div>
								</div>
                                <div class="progress progress-xs mb-2 progress-animate" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-danger" style="width: 30%">
                                    </div>
                                </div>
                                <p class="fs-13 mb-0"><span class="text-danger"><i class="isax isax-received text-danger me-1"></i>7.45%</span> from last month</p>
                            </div><!-- end card body -->
						</div><!-- end card -->
					</div><!-- end col -->
				</div>
				<!-- end row -->
				
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
							<div class="dropdown me-2">
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
											<span>ID</span>
										</label>
									</li>
									<li>
										<label class="dropdown-item d-flex align-items-center form-switch">
											<i class="fa-solid fa-grip-vertical me-3 text-default"></i>
											<input class="form-check-input m-0 me-2" type="checkbox" checked>
											<span>Customer</span>
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
											<input class="form-check-input m-0 me-2" type="checkbox">
											<span>Recurring Cycle</span>
										</label>
									</li>
									<li>
										<label class="dropdown-item d-flex align-items-center form-switch">
											<i class="fa-solid fa-grip-vertical me-3 text-default"></i>
											<input class="form-check-input m-0 me-2" type="checkbox">
											<span>Issue Date</span>
										</label>
									</li>
									<li>
										<label class="dropdown-item d-flex align-items-center form-switch">
											<i class="fa-solid fa-grip-vertical me-3 text-default"></i>
											<input class="form-check-input m-0 me-2" type="checkbox">
											<span>Due Date</span>
										</label>
									</li>
                                    <li>
										<label class="dropdown-item d-flex align-items-center form-switch">
											<i class="fa-solid fa-grip-vertical me-3 text-default"></i>
											<input class="form-check-input m-0 me-2" type="checkbox" checked>
											<span>Paid ($)</span>
										</label>
									</li>
                                    <li>
										<label class="dropdown-item d-flex align-items-center form-switch">
											<i class="fa-solid fa-grip-vertical me-3 text-default"></i>
											<input class="form-check-input m-0 me-2" type="checkbox" checked>
											<span>Due Amount ($)</span>
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
						<span class="tag bg-light border rounded-1 fs-12 text-dark badge"><span class="num-count d-inline-flex align-items-center justify-content-center bg-success fs-10 me-1">5</span>Customers Selected<span class="ms-1 tag-close"><i class="fa-solid fa-x fs-10"></i></span></span>					
						<span class="tag bg-light border rounded-1 fs-12 text-dark badge"><span class="num-count d-inline-flex align-items-center justify-content-center bg-success fs-10 me-1">2</span>Status Selected<span class="ms-1 tag-close"><i class="fa-solid fa-x fs-10"></i></span></span>											
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
								<th class="no-sort">ID</th>
                                <th>Customer</th>
								<th>Created On</th>
								<th class="no-sort">Recurring Cycle</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Paid</th>
                                <th>Due Amount</th>
								<th class="no-sort"	>Status</th>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00025</a>
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
								<td>22 Feb 2025</td>
                                <td class="text-gray-9">6 Months</td>
                                <td>25 Feb 2025</td>
                                <td>04 Mar 2025</td>
                                <td>$5,000</td>
                                <td class="text-gray-9">$10,000</td>
								<td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid <i class="isax isax-tick-circle ms-1"></i></span>								
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00024</a>
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
								<td>07 Feb 2025</td>
                                <td class="text-gray-9">1 Year</td>
                                <td>10 Feb 2025</td>
                                <td>20 Feb 2025</td>
                                <td>$10,750</td>
                                <td class="text-gray-9">$25,750</td>
								<td>
                                    <span class="badge badge-soft-warning d-inline-flex align-items-center">Unpaid<i class="isax isax-slash ms-1"></i></span>							
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00023</a>
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
								<td>30 Jan 2025</td>
                                <td class="text-gray-9">9 Months</td>
                                <td>03 Feb 2025</td>
                                <td>13 Feb 2025</td>
                                <td>$20,000</td>
                                <td class="text-gray-9">$50,125</td>
								<td>
                                    <span class="badge badge-soft-danger d-inline-flex align-items-center">Cancelled<i class="isax isax-close-circle ms-1"></i></span>							
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00022</a>
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
								<td>17 Jan 2025</td>
                                <td class="text-gray-9">2 Years</td>
                                <td>20 Jan 2025</td>
                                <td>30 Jan 2025</td>
                                <td>$50,000</td>
                                <td class="text-gray-9">$75,900</td>
								<td>
                                    <span class="badge badge-soft-info d-inline-flex align-items-center">Partially Paid<i class="isax isax-timer ms-1"></i></span>							
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00021</a>
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
								<td>04 Jan 2025</td>
                                <td class="text-gray-9">3 Months</td>
                                <td>07 Jan 2025</td>
                                <td>17 Jan 2025</td>
                                <td>$80,000</td>
                                <td class="text-gray-9">$99,999</td>
								<td>
                                    <span class="badge badge-soft-danger d-inline-flex align-items-center">Uncollectable <i class="isax isax-danger ms-1"></i></span>						
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00020</a>
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
								<td>09 Dec 2024</td>
                                <td class="text-gray-9">3 Years</td>
                                <td>12 Dec 2024</td>
                                <td>22 Dec 2024</td>
                                <td>$60,000</td>
                                <td class="text-gray-9">$1,20,500</td>
								<td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid <i class="isax isax-tick-circle ms-1"></i></span>                               
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00019</a>
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
								<td>02 Dec 2024</td>
                                <td class="text-gray-9">6 Months</td>
                                <td>05 Dec 2024</td>
                                <td>15 Dec 2024</td>
                                <td>$1,25,000</td>
                                <td class="text-gray-9">$2,50,000</td>
								<td>
                                    <span class="badge badge-soft-warning d-inline-flex align-items-center">Unpaid<i class="isax isax-slash ms-1"></i></span>                              
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00018</a>
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
								<td>15 Nov 2024</td>
                                <td class="text-gray-9">1 Year</td>
                                <td>18 Nov 2024</td>
                                <td>28 Nov 2024</td>
                                <td>$5,00,000</td>
                                <td class="text-gray-9">$5,00,750</td>
								<td>
                                    <span class="badge badge-soft-danger d-inline-flex align-items-center">Cancelled<i class="isax isax-close-circle ms-1"></i></span>                              
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00017</a>
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
								<td>30 Nov 2024</td>
                                <td class="text-gray-9">2 Years</td>
                                <td>02 Nov 2024</td>
                                <td>12 Nov 2024</td>
                                <td>$2,50,500</td>
                                <td class="text-gray-9">$7,50,300</td>
								<td>
                                    <span class="badge badge-soft-info d-inline-flex align-items-center">Partially Paid<i class="isax isax-timer ms-1"></i></span>                              
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00016</a>
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
								<td>12 Oct 2024</td>
                                <td class="text-gray-9">6 Months</td>
                                <td>15 Oct 2024</td>
                                <td>25 Oct 2024</td>
                                <td>$4,00,000</td>
                                <td class="text-gray-9">$9,99,999</td>
								<td>
                                    <span class="badge badge-soft-danger d-inline-flex align-items-center">Uncollectable <i class="isax isax-danger ms-1"></i></span>                             
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00015</a>
								</td>
								<td>
                                    <div class="d-flex align-items-center">
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-41.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Charlotte Brown</a></h6>
										</div>
									</div>
                                </td>
								<td>05 Oct 2024</td>
                                <td class="text-gray-9">3 Years</td>
                                <td>08 Oct 2024</td>
                                <td>18 Oct 2024</td>
                                <td>$40,000</td>
                                <td class="text-gray-9">$87,650</td>
								<td>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Paid <i class="isax isax-tick-circle ms-1"></i></span>                            
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00014</a>
								</td>
								<td>
                                    <div class="d-flex align-items-center">
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-42.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">William Parker</a></h6>
										</div>
									</div>
                                </td>
								<td>09 Sep 2024</td>
                                <td class="text-gray-9">1 Year</td>
                                <td>12 Sep 2024</td>
                                <td>22 Sep 2024</td>
                                <td>$30,000</td>
                                <td class="text-gray-9">$69,420</td>
								<td>
                                    <span class="badge badge-soft-warning d-inline-flex align-items-center">Unpaid<i class="isax isax-slash ms-1"></i></span>                           
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00013</a>
								</td>
								<td>
                                    <div class="d-flex align-items-center">
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-43.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Mia Thompson</a></h6>
										</div>
									</div>
                                </td>
								<td>02 Sep 2024</td>
                                <td class="text-gray-9">2 Years</td>
                                <td>05 Sep 2024</td>
                                <td>15 Sep 2024</td>
                                <td>$15,000</td>
                                <td class="text-gray-9">$33,210</td>
								<td>
                                    <span class="badge badge-soft-danger d-inline-flex align-items-center">Cancelled<i class="isax isax-close-circle ms-1"></i></span>                           
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
									<a href="javascript:void(0);" class="link-default" data-bs-toggle="modal" data-bs-target="#view_invoice">INV00012</a>
								</td>
								<td>
                                    <div class="d-flex align-items-center">
										<a href="customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
											<img src="assets/img/profiles/avatar-44.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-medium mb-0"><a href="customer-details.php">Amelia Robinson</a></h6>
										</div>
									</div>
                                </td>
								<td>07 Aug 2024</td>
                                <td class="text-gray-9">6 Months</td>
                                <td>10 Aug 2024</td>
                                <td>20 Aug 2024</td>
                                <td>$1,50,000</td>
                                <td class="text-gray-9">$2,10,000</td>
								<td>
                                    <span class="badge badge-soft-info d-inline-flex align-items-center">Partially Paid<i class="isax isax-timer ms-1"></i></span>                          
                                </td>
								<td class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="isax isax-more"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#view_invoice"><i class="isax isax-eye me-2"></i>View</a>
                                        </li>
                                        <li>
                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                        </li>                                      
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-send-2 me-2"></i>Send</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-document-download me-2"></i>Download</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-stop-circle me-2"></i>Stop Recurring</a>
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
			
			<!-- Start Footer-->
			<div class="footer d-sm-flex align-items-center justify-content-between bg-white py-2 px-4 border-top">
    <p class="text-dark mb-0">&copy; <script>document.write(new Date().getFullYear())</script> <a href="javascript:void(0);" class="link-primary">Oddeven Infotech Pvt.Ltd</a>, All Rights Reserved</p>
				<p class="text-dark">Version : 1.3.8</p>
			</div>
			<!-- / End Footer-->

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
						<label class="form-label">Date Range</label>
						<div class="input-group position-relative">
							<input type="text" class="form-control date-range bookingrange rounded-end" value="dd/mm/yyyy - dd/mm/yyyy">
							<span class="input-icon-addon fs-16 text-gray-9">
								<i class="isax isax-calendar-2"></i>
							</span>
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
						<label class="form-label">Status</label>
						<div class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light  d-flex align-items-center justify-content-start fs-13 fw-normal border" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
								Select
							</a>
							<div class="dropdown-menu shadow-lg w-100 dropdown-info">	
								<ul class="mb-0">
									<li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											<i class="fa-solid fa-circle fs-6 text-success me-1"></i>Paid
										</label>
									</li>
                                    <li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											<i class="fa-solid fa-circle fs-6 text-warning me-1"></i>Pending
										</label>
									</li>
									<li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											<i class="fa-solid fa-circle fs-6 text-danger me-1"></i>Cancelled
										</label>
									</li>	
                                    <li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											<i class="fa-solid fa-circle fs-6 text-purple me-1"></i>Partially Paid
										</label>
									</li>
                                    <li>
										<label class="dropdown-item px-2 d-flex align-items-center text-dark">
											<input class="form-check-input m-0 me-2" type="checkbox">
											<i class="fa-solid fa-circle fs-6 text-orange me-1"></i>Uncollectable
										</label>
									</li>						
								</ul>
							</div>
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

        <!-- Start view notes -->
        <div class="modal fade" id="view_invoice">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">			
                    <div class="modal-body">
                        <div class="d-flex mb-3 pb-3 align-items-center justify-content-between border-bottom">
                            <h5 class="mb-0">Preview</h5>
                            <button type="button" class="text-danger bg-transparent border-0 outline-0 p-0 lh-sm" data-bs-dismiss="modal" aria-label="Close">
                                <i class="isax isax-close-circle5 fs-16"></i>
                            </button>
                        </div>
                        <div>
                            <div class="d-flex align-items-center justify-content-end flex-wrap row-gap-3 mb-3">
                                <div class="d-flex align-items-center flex-wrap row-gap-3">
                                    <a href="#" class="btn btn-outline-white d-inline-flex align-items-center me-3"><i class="isax isax-document-like me-1"></i>Download PDF</a>
                                    <a href="#" class="btn btn-outline-white d-inline-flex align-items-center me-3"><i class="isax isax-message-notif me-1"></i>Send Email</a>
                                    <a href="#" class="btn btn-outline-white d-inline-flex align-items-center me-3"><i class="isax isax-printer me-1"></i>Print</a>								
                                </div>
                            </div>
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
								<!-- start row -->
                                <div class="row gy-3 position-relative z-1">
                                    <div class="col-lg-4">
                                        <div>
                                            <h6 class="mb-2 fs-16 fw-semibold">Invoice Details</h6>
                                            <div>
                                                <p class="mb-1">Invoice Number : <span class="text-dark">INV215654</span></p>
                                                <p class="mb-1">Issued On : <span class="text-dark">25 Jan 2025</span></p>
                                                <p class="mb-1">Due Date :  <span class="text-dark">31 Jan 2025</span></p>
                                                <p class="mb-1">Recurring Invoice  :  <span class="text-dark">Monthly</span></p>
                                                <span class="badge bg-danger">Due in 8 days</span>
                                            </div>
                                        </div>
                                    </div><!-- end col -->
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
                                    </div><!-- end col -->
                                    <div class="col-lg-4">
                                        <div>
                                            <h6 class="mb-2 fs-16 fw-semibold">Billing To</h6>                             
                                            <div class="bg-white rounded p-3">
                                                <div class="d-flex align-items-center mb-1">
                                                    <div class="me-2">
                                                        <span><img src="assets/img/icons/timesquare-icon.svg" alt="image" class="img-fluid rounded"></span>
                                                    </div>
                                                    <h6 class="fs-14 fw-semibold">Timesquare Tech</h6>
                                                </div>
                                                <p class="mb-1">299 Star Trek Drive, Florida, 3240, USA</p>
                                                <p class="mb-1">Phone : +1 54664 75945</p>
                                                <p class="mb-1">Email : info@example.com</p>
                                                <p class="mb-0">GST : 243E45767889</p>
                                            </div>
                                        </div>
                                    </div><!-- end col -->
                                </div>
								<!-- end row -->
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-3">Product / Service Items</h6>
                                <div class="table-responsive rounded border-bottom-0 border">
                                    <table class="table">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Product/Service</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Rate</th>
                                                <th>Discount</th>
                                                <th>Tax (%)</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td class="text-dark">T-Shirt</td>
                                                <td>2</td>
                                                <td>Pcs</td>
                                                <td>$200.00</td>
                                                <td>10%</td>
                                                <td>$36.00</td>
                                                <td>$396.00</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td class="text-dark">Office Chair</td>
                                                <td>1</td>
                                                <td>Pcs</td>
                                                <td>$350.00</td>
                                                <td>5%</td>
                                                <td>$33.25</td>
                                                <td>$365.75</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td class="text-dark">LED Monitor</td>
                                                <td>1</td>
                                                <td>Pcs</td>
                                                <td>$399.00</td>
                                                <td>2%</td>
                                                <td>$39.10</td>
                                                <td>$398.90</td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td class="text-dark">Smartphone</td>
                                                <td>4</td>
                                                <td>Pcs</td>
                                                <td>$100.00</td>
                                                <td>10%</td>
                                                <td>$36.00</td>
                                                <td>$396.00</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="border-bottom mb-3">
								<!-- start row -->
                                <div class="row">
                                    <div class="col-xl-8 col-lg-6">
                                        <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                            <div class="me-3">
                                                <p class="mb-2">Scan to the pay</p>
                                                <span><img src="assets/img/icons/qr.png" alt="User Img"></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-2">Bank Details</h6>
                                                <div>
                                                    <p class="mb-1">Bank Name :  <span class="text-dark">ABC Bank</span></p>
                                                    <p class="mb-1">Account Number :  <span class="text-dark">782459739212</span></p>
                                                    <p class="mb-1">IFSC Code :  <span class="text-dark">ABC0001345</span></p>
                                                    <p class="mb-0">Payment Reference :  <span class="text-dark">INV-20250220-001</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- end col -->
                                    <div class="col-xl-4 col-lg-6">
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="fs-14 fw-semibold">Amount</h6>
                                                <h6 class="fs-14 fw-semibold">$1,793.12</h6>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="fs-14 fw-semibold">CGST (9%)</h6>
                                                <h6 class="fs-14 fw-semibold">$18</h6>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="fs-14 fw-semibold">SGST (9%)</h6>
                                                <h6 class="fs-14 fw-semibold">$18</h6>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                                <h6 class="fs-14 fw-semibold">Discount</h6>
                                                <h6 class="fs-14 fw-semibold text-danger">$18</h6>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                                <h6>Total (USD)</h6>
                                                <h6>$596</h6>
                                            </div>
                                            <div>
                                                <h6 class="fs-14 fw-semibold mb-1">Total In Words</h6>
                                                <p>Five Hundred &amp; Ninety Six Dollars</p>
                                            </div>
                                        </div>
                                    </div><!-- end col -->
                                </div>
								<!-- end row -->
                            </div>
							<!-- start row -->
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <h6 class="fs-14 fw-semibold mb-1">Terms and Conditions</h6>
                                            <p>The Payment must be returned in the same condition.</p>
                                        </div>
                                        <div>
                                            <h6 class="fs-14 fw-semibold mb-1">Notes</h6>
                                            <p>All charges are final and include applicable taxes, fees, and additional costs</p>
                                        </div>
                                    </div>
                                </div><!-- end col -->
                                <div class="col-lg-5">
                                    <div class="text-lg-end mb-3">
                                        <span><img src="assets/img/icons/sign.png" class="sign-dark" alt="img"></span>
                                        <h6 class="fs-14 fw-semibold mb-1">Ted M. Davis</h6>
                                        <p>Manager</p>
                                    </div>
                                </div><!-- end col -->
                            </div>
							<!-- end row -->
                            <div class="bg-light d-flex align-items-center justify-content-between p-4 rounded card-bg flex-wrap gap-2">
                                <div>
                                    <h6 class="fs-14 fw-semibold mb-1">Dreams Technologies Pvt Ltd.,</h6>
                                    <p>15 Hodges Mews, High Wycombe HP12 3JL, United Kingdom</p>
                                </div>
                                <div>
                                    <img src="assets/img/invoice-logo.svg" class="invoice-logo-dark" alt="img">
									<img src="assets/img/invoice-logo-white-2.svg" class="invoice-logo-white" alt="img">
                                </div>
                            </div>
                        </div>
                    </div><!-- End modal body -->
                </div><!-- End modal content-->
			</div><!-- End modal dialog-->
        </div>
        <!-- End view notes -->

		<!-- Start Delete Modal  -->
		<div class="modal fade" id="delete_modal">
			<div class="modal-dialog modal-dialog-centered modal-sm">
				<div class="modal-content">
					<div class="modal-body text-center">
						<div class="mb-3">
							<img src="assets/img/icons/delete.svg" alt="img">
						</div>
						<h6 class="mb-1">Delete Invoice</h6>
						<p class="mb-3">Are you sure,  you want to delete invoice?</p>
						<div class="d-flex justify-content-center">
							<a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
							<a href="recurring-invoices.php" class="btn btn-primary">Yes, Delete</a>
						</div>
					</div><!-- End modal body-->
				</div><!-- End modal content-->
			</div><!-- End modal dialog-->
		</div>
		<!-- End Delete Modal  -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>

</body>

</html>        