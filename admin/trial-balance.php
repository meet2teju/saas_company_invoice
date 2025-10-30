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
                        <h6 class="mb-0">Trial Balance Report</h6>
                    </div>
                    <div class="my-xl-auto">
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                <i class="isax isax-export-1 me-1"></i>Export
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);">Download as PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);">Download as Excel</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- End Page Header -->

                <div class="border-bottom mb-3">

                    <!-- start row -->
                    <div class="row">
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="card position-relative shadow-lg">
                                <div class="card-body">
                                    <div class="mb-1">
                                        <span class="p-2 badge badge-soft-primary d-inline-flex align-items-center justify-content-center rounded border border-primary">
											<i class="isax isax-dollar-circle fs-16"></i>
										</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="mb-0">
                                                <p class="mb-1 text-truncate">Total Debits</p>
                                                <div>
                                                    <h6 class="fs-16 fw-semibold me-2 mb-1">$750,000</h6>
                                                    <span class="badge badge-soft-success">+5.62%<i class="isax isax-arrow-up-15"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chart-set" id="radial-chart3"></div>
                                    </div>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div><!-- end col -->
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="card position-relative shadow-lg">
                                <div class="card-body">
                                    <div class="mb-1">
                                        <span class="p-2 badge badge-soft-success d-inline-flex align-items-center justify-content-center rounded border border-success">
											<i class="isax isax-money-2 fs-16"></i>
										</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="mb-0">
                                                <p class="mb-1 text-truncate">Total Credits</p>
                                                <div>
                                                    <h6 class="fs-16 fw-semibold me-2 mb-1">$550,000</h6>
                                                    <span class="badge badge-soft-success">+11.4%<i class="isax isax-arrow-up-15"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chart-set" id="radial-chart4"></div>
                                    </div>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div><!-- end col -->
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="card position-relative shadow-lg">
                                <div class="card-body">
                                    <div class="mb-1">
                                        <span class="p-2 badge badge-soft-warning d-inline-flex align-items-center justify-content-center rounded border border-warning">
											<i class="isax isax-wallet-3 fs-16"></i>
										</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="mb-0">
                                                <p class="mb-1 text-truncate">Cash & Bank Balance</p>
                                                <div>
                                                    <h6 class="fs-16 fw-semibold me-2 mb-1">$150,000</h6>
                                                    <span class="badge badge-soft-success">+8.12%<i class="isax isax-arrow-up-15"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chart-set" id="radial-chart5"></div>
                                    </div>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div><!-- end col -->
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="card position-relative shadow-lg">
                                <div class="card-body">
                                    <div class="mb-1">
                                        <span class="p-2 badge badge-soft-danger d-inline-flex align-items-center justify-content-center rounded border border-danger">
											<i class="isax isax-dollar-circle fs-16"></i>
										</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="mb-0">
                                                <p class="mb-1 text-truncate">Cash & Bank Balance</p>
                                                <div>
                                                    <h6 class="fs-16 fw-semibold me-2 mb-1">$50,000</h6>
                                                    <span class="badge badge-soft-success">7.45%<i class="isax isax-arrow-up-15"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chart-set" id="radial-chart6"></div>
                                    </div>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->

                </div>

                <!-- Table Search -->
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="table-search d-flex align-items-center mb-0">
                                <div class="search-input">
                                    <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                                </div>
                            </div>
                            <div id="reportrange" class="reportrange-picker d-flex align-items-center">
                                <i class="isax isax-calendar text-gray-5 fs-14 me-1"></i><span class="reportrange-picker-field">16 Apr 25 - 16 Apr 25</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <i class="isax isax-grid-3 me-1"></i>Column
                                </a>
                                <ul class="dropdown-menu  dropdown-menu">
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Account Holder Name</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Bank & Account No</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Credit</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox">
                                            <span>Debit</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item d-flex align-items-center form-switch">
                                            <i class="fa-solid fa-grip-vertical me-3 text-default"></i>
                                            <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                            <span>Balance</span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
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
                                <th>Account Name</th>
                                <th>Credit</th>
                                <th>Debit</th>
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
                                <td>PNB - 5475878970090</td>
                                <td>$22,500</td>
                                <td>$7,500</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>SFB - 3255465758698</td>
                                <td>$30,000</td>
                                <td>$10,000</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>HSB - 4353689870544</td>
                                <td>$12,000</td>
                                <td>$3,000</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>ETB - 4324356677889</td>
                                <td>$18,000</td>
                                <td>$6,000</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>NPB - 2343547586900</td>
                                <td>$25,000</td>
                                <td>$8,000</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>SDB - 3354456565687</td>
                                <td>$35,000</td>
                                <td>$12,000</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>FEB - 3453647664889</td>
                                <td>$40,000</td>
                                <td>$15,000</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>CCB - 9876543210123</td>
                                <td>$27,500</td>
                                <td>$9,500</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>CCB - 9876543210123</td>
                                <td>$20,000</td>
                                <td>$7,000</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>ICB - 6543217896543</td>
                                <td>$45,000</td>
                                <td>$18,000</td>
                                <td></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-light border-top">
                                <td></td>
                                <td class="fw-semibold">Total</td>
                                <td class="fw-semibold">$425,500.</td>
                                <td class="fw-semibold">$154,000.</td>
                                <td class="fw-semibold text-end">$154,000.</td>
                            </tr>
                        </tfoot>
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

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>

</body>

</html>           