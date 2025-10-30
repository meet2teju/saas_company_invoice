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
                    <div class="col-xl-11">
                        <div class=" row settings-wrapper d-flex">
                            <div class="col-xxl-3 col-lg-4">
                                <div class="card settings-card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="sidebars settings-sidebar">
                                            <div class="sidebar-inner">
                                                <div class="sidebar-menu p-0">
                                                    <ul>
                                                        <li>
                                                            <a href="customer-account-settings.php" class="fs-14 fw-medium d-flex align-items-center"><i class="isax isax-user-octagon fs-18 me-1"></i>Account Settings</a>
                                                        </li>
                                                        <li>
                                                            <a href="customer-security-settings.php" class="fs-14 fw-medium d-flex align-items-center"><i class="isax isax-security-safe fs-18 me-1"></i>Security</a>
                                                        </li>
                                                        <li>
                                                            <a href="customer-plans-settings.php" class="active fs-14 fw-medium d-flex align-items-center"><i class="isax isax-transaction-minus fs-18 me-1"></i>Plans & Billings</a>
                                                        </li>
                                                        <li>
                                                            <a href="customer-notification-settings.php" class="fs-14 fw-medium d-flex align-items-center"><i class="isax isax-notification fs-18 me-1"></i>Notifications</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end col -->
                            <div class="col-xxl-9 col-lg-8">
                                <div class="mb-3">
                                    <div class="pb-3 border-bottom mb-3">
                                        <h6 class="mb-0">Plans & Billings</h6>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="bg-dark avatar avatar-sm me-2 flex-shrink-0"><i class="isax isax-info-circle fs-14"></i></span>
                                        <h6 class="fs-16 fw-semibold">Current Plan Information</h6>
                                    </div>
                                    <form action="account-settings.php">
                                        <div class="mb-3">
                                            <div class="card shadow-none">
                                                <div class="card-body">
                                                    <div class="mb-0">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div class="">
                                                                <h6 class="fw-bold mb-2">Basic Plan</h6>
                                                                <div class="progress-container">
                                                                    <svg class="progress-circle me-2" viewBox="0 0 36 36">
                                                                        <circle class="progress-bar" cx="18" cy="18" r="16"></circle>
                                                                        <circle class="progress-bar-fill" cx="18" cy="18" r="16"></circle>
                                                                    </svg>
                                                                    <span>20 Days Left</span>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <button type="button" class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#upgrade"> <i class="ti ti-crown me-1"></i> Upgrade</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="bg-dark avatar avatar-sm me-2 flex-shrink-0"><i class="isax isax-card fs-14"></i></span>
                                                <h6 class="fs-16 fw-semibold">Saved Cards</h6>
                                            </div>
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="card shadow-none">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <a href="javascript:void(0);">
                                                                    <img src="assets/img/settings/payment-icon-01.svg" class="img-fluid me-2" alt="clock">
                                                                </a>
                                                                <div>
                                                                    <span class="fs-12">James Peterson</span>
                                                                    <h6 class="fs-14 fw-medium mb-1">Visa •••• 1568</h6>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <a href="javascript:void(0);" class="badge bg-success">Default</a>
                                                                <div class="d-flex align-items-center">
                                                                    <a href="javascript:void(0);" class="avatar bg-light text-dark avatar-md border rounded-circle me-2"><i class="isax isax-edit text-gray"></i></a>
                                                                    <a href="javascript:void(0);" class="avatar bg-light text-dark avatar-md border rounded-circle" data-bs-toggle="modal" data-bs-target="#delete_card"><i class="isax isax-trash text-gray"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="card shadow-none">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <a href="javascript:void(0);">
                                                                    <img src="assets/img/settings/payment-icon-02.svg" class="img-fluid me-2" alt="clock">
                                                                </a>
                                                                <div>
                                                                    <span class="fs-12">Raymond Rowley</span>
                                                                    <h6 class="fs-14 fw-medium mb-1">Mastercard •••• 1279</h6>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <a href="javascript:void(0);" class="text-primary text-decoration-underline">Set as Default</a>
                                                                <div class="d-flex align-items-center">
                                                                    <a href="javascript:void(0);" class="avatar bg-light text-dark avatar-md border rounded-circle me-2"><i class="isax isax-edit text-gray"></i></a>
                                                                    <a href="javascript:void(0);" class="avatar bg-light text-dark avatar-md border rounded-circle" data-bs-toggle="modal" data-bs-target="#delete_card"><i class="isax isax-trash text-gray"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="bg-dark avatar avatar-sm me-2 flex-shrink-0"><i class="isax isax-transaction-minus fs-14"></i></span>
                                                <h6 class="fs-16 fw-semibold">Transactions</h6>
                                            </div>
                                            <div class="row">
                                                <!-- Table Search Start -->
                                                <div class="mb-3">

                                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                                            <div class="table-search d-flex align-items-center mb-0">
                                                                <div class="search-input">
                                                                    <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center flex-wrap gap-2">
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

                                                </div>
                                                <!-- /Table Search End -->

                                                <!-- Table List Start -->
                                                <div class="table-responsive no-pagination">
                                                    <table class="table table-nowrap datatable">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Plan Name</th>
                                                                <th>Amount</th>
                                                                <th>Purchased Date</th>
                                                                <th>End Date</th>
                                                                <th>Status</th>
                                                                <th class="no-sort"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <p class="text-dark">Basic</p>
                                                                </td>
                                                                <td>$99</td>
                                                                <td>22 Feb 2025</td>
                                                                <td>22 Mar 2025</td>
                                                                <td>
                                                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Completed
																		<i class="isax isax-tick-circle ms-1"></i>
																	</span>
                                                                </td>
                                                                <td class="action-item">
                                                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                                        <i class="isax isax-more"></i>
                                                                    </a>
                                                                    <ul class="dropdown-menu">
                                                                        <li>
                                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p class="text-dark">Premium</p>
                                                                </td>
                                                                <td>$199</td>
                                                                <td>22 Jan 2025</td>
                                                                <td>22 Feb 2025</td>
                                                                <td>
                                                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Completed
																		<i class="isax isax-tick-circle ms-1"></i>
																	</span>
                                                                </td>
                                                                <td class="action-item">
                                                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                                        <i class="isax isax-more"></i>
                                                                    </a>
                                                                    <ul class="dropdown-menu">
                                                                        <li>
                                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p class="text-dark">Enterprise</p>
                                                                </td>
                                                                <td>$299</td>
                                                                <td>22 Dec 2025</td>
                                                                <td>22 Jan 2025</td>
                                                                <td>
                                                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Completed
																		<i class="isax isax-tick-circle ms-1"></i>
																	</span>
                                                                </td>
                                                                <td class="action-item">
                                                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                                        <i class="isax isax-more"></i>
                                                                    </a>
                                                                    <ul class="dropdown-menu">
                                                                        <li>
                                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p class="text-dark">Premium</p>
                                                                </td>
                                                                <td>$199</td>
                                                                <td>22 Nov 2025</td>
                                                                <td>22 Dec 2025</td>
                                                                <td>
                                                                    <span class="badge badge-soft-success d-inline-flex align-items-center">Completed
																		<i class="isax isax-tick-circle ms-1"></i>
																	</span>
																</td>
																<td class="action-item">
																	<a href="javascript:void(0);" data-bs-toggle="dropdown">
																		<i class="isax isax-more"></i>
																	</a>
																	<ul class="dropdown-menu">
																		<li>
																			<a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
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
												<!-- /Table List End -->
											</div>
										</div>
										<div class="d-flex align-items-center justify-content-between">
											<button type="button" class="btn btn-outline-white">Cancel</button>
											<button type="submit" class="btn btn-primary">Save Changes</button>
										</div>
									</form>
                                </div>
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div> <!-- end col -->
                </div>
                <!-- end row -->

            </div>
			<!-- End Content -->

            <?php include 'layouts/footer.php'; ?>

        </div>

        <!-- ========================
			End Page Content
		========================= -->

        <!-- Upgrade Start -->
        <div class="modal fade" id="upgrade">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-body pb-0">
                        <!-- Pricing -->
                        <div>
                            <div class="d-flex justify-content-center align-items-center mb-3">
                                <p class="mb-0 me-2">Monthly</p>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" checked>
                                </div>
                                <p>Yearly</p>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="card border rounded mb-3">
                                        <div class="card-body">
                                            <div class="pricing-content mb-3">
                                                <div class="mb-3">
                                                    <h6 class="fs-14">Basic</h6>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <h3>$99<span class="fs-14 fw-normal text-gray me-2">/month</span></h3>
                                                    <span class="badge badge-sm bg-info text-white p-1 border rounded text-truncate">Only 10 Users</span>
                                                </div>
                                                <p class="mb-2 text-truncate line-clamb-2">Best for Freelancers & small businesses needs simple invoicing.</p>
                                                <a href="#" class="d-flex align-items-center justify-content-center btn border taxt-gray-100 rounded w-100 mb-3" data-bs-toggle="modal" data-bs-target="#checkout">
                                                    <i class="isax isax-shopping-cart me-1"></i> Buy Plan
                                                </a>
                                                <div class="price-hdr">
                                                    <h6 class="fs-14 fw-medium text-gray me-2 ms-2">Features</h6>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <div>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														1 Business Account + 1 User
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														14+ Invoice templates
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Collect Online Payments
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														40+ Reports & Insights
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Variants
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Add custom fields & charges
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-0">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Convert documents
													</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="card border rounded mb-3">
                                        <div class="card-body">
                                            <div class="pricing-content mb-3">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <h6 class="fs-14">Standard</h6>
                                                    <span class="badge bg-primary text-white">Most Popular</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <h3>$199<span class="fs-14 fw-normal text-gray me-2">/month</span></h3>
                                                    <span class="badge badge-sm bg-info text-white p-1 border rounded text-truncate">Only 50 Users</span>
                                                </div>
                                                <p class="mb-2 text-truncate line-clamb-2">Growing businesses managing recurring invoices & reports.</p>
                                                <a href="#" class="d-flex align-items-center justify-content-center btn bg-primary border text-white rounded w-100 mb-3">
                                                    <i class="isax isax-bill me-1"></i> Current Plan
                                                </a>
                                                <div class="price-hdr">
                                                    <h6 class="fs-14 fw-medium text-gray me-2 ms-2">Features</h6>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <div>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														1 Business Account + 1 User
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Bulk downloads
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Multiple Price lists
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														User Activity
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Bulk edits
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Multiple Warehouses
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-0">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Online Store
													</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="card border rounded mb-3">
                                        <div class="card-body">
                                            <div class="pricing-content mb-3">
                                                <div class="mb-3">
                                                    <h6 class="fs-14">Business</h6>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <h3>$299<span class="fs-14 fw-normal text-gray me-2">/month</span></h3>
                                                    <span class="badge badge-sm bg-info text-white p-1 border rounded text-truncate">Only 75 Users</span>
                                                </div>
                                                <p class="mb-2 text-truncate line-clamb-2">Best for Large sales teams requiring automation & integrations.</p>
                                                <a href="#" class="d-flex align-items-center justify-content-center btn border taxt-gray-100 rounded w-100 mb-3" data-bs-toggle="modal" data-bs-target="#checkout">
                                                    <i class="isax isax-shopping-cart me-1"></i> Buy Plan</a>
                                                <div class="price-hdr">
                                                    <h6 class="fs-14 fw-medium text-gray me-2 ms-2">Features</h6>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <div>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														POS Billing
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Batch & Expiry
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Serial Number/ IMEI Tracking
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Subscription/ Recurring
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Product Grouping
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Additional CESS
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-0">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Bank Reconciliation
													</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="card border rounded mb-3">
                                        <div class="card-body">
                                            <div class="pricing-content mb-3">
                                                <div class="mb-3">
                                                    <h6 class="fs-14">Enterprice</h6>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <h3>$399<span class="fs-14 fw-normal text-gray me-2">/month</span></h3>
                                                    <span class="badge badge-sm bg-info text-white p-1 border rounded text-truncate">Unlimited</span>
                                                </div>
                                                <p class="mb-2 text-truncate line-clamb-2">Enterprises with AI insights & advanced workflows.</p>
                                                <a href="#" class="d-flex align-items-center justify-content-center btn border taxt-gray-100 rounded w-100 mb-3" data-bs-toggle="modal" data-bs-target="#checkout">
                                                    <i class="isax isax-shopping-cart me-1"></i> Buy Plan</a>
                                                <div class="price-hdr">
                                                    <h6 class="fs-14 fw-medium text-gray me-2 ms-2">Features</h6>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <div>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Add Custom Features
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Custom Column Linking
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Multi Businesses / Branches
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Online Store
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Shiprocket Integration
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-2">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Multiple Users
													</span>
                                                    <span class="text-dark d-flex align-items-center mb-0">
														<i class="isax isax-tick-circle5 text-success me-2"></i>
														Multiple Warehouses
													</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Pricing -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Upgrade End -->

        <!-- Checkout Start -->
        <div class="modal fade" id="checkout">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Checkout</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="bg-dark avatar avatar-sm me-2 flex-shrink-0"><i class="isax isax-info-circle fs-14"></i></span>
                                <h6 class="fs-16 fw-semibold">General Information</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="border-bottom mb-3">
                                        <div class="row gx-3">
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">First Name<span class="text-danger ms-1">*</span></label>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Last Name<span class="text-danger ms-1">*</span></label>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Email<span class="text-danger ms-1">*</span></label>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Mobile Number<span class="text-danger ms-1">*</span></label>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border-bottom mb-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="bg-dark avatar avatar-sm me-2 flex-shrink-0"><i class="isax isax-info-circle fs-14"></i></span>
                                            <h6 class="fs-16 fw-semibold">Address Information</h6>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Address</label>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">County</label>
                                                    <select class="select">
                                                        <option>Select</option>
                                                        <option>United States of America</option>
                                                        <option>Canada</option>
                                                        <option>Germany</option>
                                                        <option>France</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">State</label>
                                                    <select class="select">
                                                        <option>Select</option>
                                                        <option>California</option>
                                                        <option>New York</option>
                                                        <option>Texas</option>
                                                        <option>Florida</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">City</label>
                                                    <select class="select">
                                                        <option>Select</option>
                                                        <option>Los Angeles</option>
                                                        <option>New York</option>
                                                        <option>Fresno</option>
                                                        <option>San Francisco</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Postal Code</label>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex">
                                    <div class="d-flex flex-column justify-content-between flex-fill">
                                        <div class="card shadow-none mb-0">
                                            <div class="card-header">
                                                <h6 class="fw-bold">Subscription Details</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span>Plan Name:</span>
                                                    <h6 class="fw-medium">Basic</h6>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span>Plan Amount:</span>
                                                    <h6 class="fw-medium">$99.00</h6>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span>Tax:</span>
                                                    <h6 class="fw-medium">$0.00</h6>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span>Total:</span>
                                                    <h6 class="fw-medium">$99.00</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="bg-success-100 p-2 d-flex align-items-center justify-content-center mb-3">
                                                <i class="isax isax-security-safe5 text-success fs-40 me-2"></i>
                                                <div>
                                                    <p class="text-dark fw-semibold mb-0">100% Cashback Guarantee</p>
                                                    <p class="fs-13">We Protect Your Money</p>
                                                </div>
                                            </div>
                                            <a href="javascript:void(0);" class="btn btn-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#success_modal">Pay $99.00</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Checkout End -->

        <!-- Billing Success Start -->
        <div class="modal fade" id="success_modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="text-center p-3">
                            <span class="avatar avatar-lg avatar-rounded bg-success mb-3"><i class="isax isax-tick-circle fs-24"></i></span>
                            <h5 class="mb-2 fw-bold">Payment Successfully</h5>
                            <p class="mb-3">Your purchase of the Basic Plan has been completed with Reference Number <a href="javascript:void(0);">#12559845</a>
                            </p>
                            <div class="d-flex align-items-center justify-content-center">
                                <a href="customer-plans-settings.php" class="btn btn-md btn-white">Back to Plan</a>
                                <a href="javascript:void(0);" class="btn btn-md btn-primary" data-bs-toggle="modal" data-bs-target="#purchase-details">Purchase Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Billing Success End -->

        <!-- Invoice View Modal Start -->
        <div id="purchase-details" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Preview</h4>
                        <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <form action="#">
                        <div class="modal-body">
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
                                                    <div class="d-flex align-items-center justify-content-between border-bottom flex-wrap mb-3 pb-2">
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
                                                    <div class="row gy-3">
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
                                                        <img src="assets/img/invoice-logo.svg" class="invoice-logo-dark" alt="img">
									                    <img src="assets/img/invoice-logo-white-2.svg" class="invoice-logo-white" alt="img">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Invoice View Modal End -->

        <!-- Start Delete -->
		<div class="modal fade" id="delete_modal">
			<div class="modal-dialog modal-dialog-centered modal-sm">
				<div class="modal-content">
					<div class="modal-body text-center">
						<div class="mb-3">
							<img src="assets/img/icons/delete.svg" alt="img">
						</div>
						<h6 class="mb-1">Delete Plan</h6>
						<p class="mb-3">Are you sure,  you want to delete Plan?</p>
						<div class="d-flex justify-content-center">
							<a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
							<a href="customer-plans-settings.php" class="btn btn-primary">Yes, Delete</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Delete -->

		<!-- Start Delete -->
		<div class="modal fade" id="delete_card">
			<div class="modal-dialog modal-dialog-centered modal-sm">
				<div class="modal-content">
					<div class="modal-body text-center">
						<div class="mb-3">
							<img src="assets/img/icons/delete.svg" alt="img">
						</div>
						<h6 class="mb-1">Delete Cards</h6>
						<p class="mb-3">Are you sure,  you want to delete Cards?</p>
						<div class="d-flex justify-content-center">
							<a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
							<a href="customer-plans-settings.php" class="btn btn-primary">Yes, Delete</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Delete -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>

</body>

</html>        