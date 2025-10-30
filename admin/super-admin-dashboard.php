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

                <!-- Page Header -->
                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <h6>Dashboard</h6>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                        <div class="input-icon-end position-relative">
                            <input type="text" class="form-control form-control-sm bookingrange">
                            <span class="input-icon-addon text-dark">
								<i class="isax isax-calendar-2"></i>
							</span>
                        </div>
                    </div>
                </div>
                <!-- End Page Header -->

                <!-- start row -->
                <div class="row">
                    <div class="col-xl-4">
                        <div class="card bg-primary rounded-3 px-3 position-relative z-0">
                            <img src="assets/img/icons/dashboard-icon-02.svg" alt="img" class="dashboard-bg-2 d-lg-flex d-none">
                            
                            <!-- start row -->
                            <div class="row">
                                <div class="col-lg-12 py-3">
                                    <h5 class="text-white d-inline-flex align-items-center mb-2 text-truncate line-clamb-1"><i class="isax isax-sun-1 fs-20 me-1"></i>Good Morning, John</h5>
                                    <p class="fs-16 text-white mb-lg-5 mb-3 text-truncate">14 New Companies Subscribed Today</p>
                                    <div class="d-flex align-items-center">
                                        <a href="companies.php" class="btn btn-sm btn-blue fw-medium me-2 px-xl-2 px-lg-3">View Companies</a>
                                        <a href="packages.php" class="btn btn-sm btn-outline-blue fw-medium px-xl-2 px-lg-3">All Packages</a>
                                    </div>
                                </div><!-- end col -->
                            </div>
                            <!-- end row -->

                        </div>
                    </div><!-- end col -->
                    <div class="col-xl-8">

                        <!-- start row -->
                        <div class="row">
                            <div class="col-md-3 d-flex">
                                <div class="card bg-light shadow-none flex-fill w-100 rounded-3">
                                    <div class="card-body p-3">
                                        <div class="avatar avatar-xl bg-white rounded-3 mb-3">
                                            <img src="assets/img/icons/info-icon-01.svg" alt="img" class="rounded-3 img-fluid w-auto h-auto">
                                        </div>
                                        <p class="mb-1 text-gray-9 text-truncate">Total Companies</p>
                                        <h6 class="mb-1 fs-16 fw-semibold">987</h6>
                                        <p class="fs-13 mb-0 text-truncate"><span class="text-success fs-14"><i class="isax isax-send text-success me-1"></i>14%</span> last month</p>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->
                            <div class="col-md-3 d-flex">
                                <div class="card bg-light shadow-none flex-fill w-100 rounded-3">
                                    <div class="card-body p-3">
                                        <div class="avatar avatar-xl bg-white rounded-3 mb-3">
                                            <img src="assets/img/icons/info-icon-02.svg" alt="img" class="rounded-3 img-fluid w-auto h-auto">
                                        </div>
                                        <p class="mb-1 text-gray-9 text-truncate">Active Companies</p>
                                        <h6 class="mb-1 fs-16 fw-semibold">154</h6>
                                        <p class="fs-13 mb-0 text-truncate"><span class="text-success fs-14"><i class="isax isax-send text-success me-1"></i>8.36%</span> last month</p>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->
                            <div class="col-md-3 d-flex">
                                <div class="card bg-light shadow-none flex-fill w-100 rounded-3">
                                    <div class="card-body p-3">
                                        <div class="avatar avatar-xl bg-white rounded-3 mb-3">
                                            <img src="assets/img/icons/info-icon-03.svg" alt="img" class="rounded-3 img-fluid w-auto h-auto">
                                        </div>
                                        <p class="mb-1 text-gray-9 text-truncate">Inactive Companies</p>
                                        <h6 class="mb-1 fs-16 fw-semibold">2</h6>
                                        <p class="fs-13 mb-0 text-truncate"><span class="text-success fs-14"><i class="isax isax-send text-success me-1"></i>12.8%</span> last month</p>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->
                            <div class="col-md-3 d-flex">
                                <div class="card bg-light shadow-none flex-fill w-100 rounded-3">
                                    <div class="card-body p-3">
                                        <div class="avatar avatar-xl bg-white rounded-3 mb-3">
                                            <img src="assets/img/icons/info-icon-04.svg" alt="img" class="rounded-3 img-fluid w-auto h-auto">
                                        </div>
                                        <p class="mb-1 text-gray-9 text-truncate">Total Active Plans</p>
                                        <h6 class="mb-1 fs-16 fw-semibold">6</h6>
                                        <p class="fs-13 mb-0 text-truncate"><span class="text-success fs-14"><i class="isax isax-send text-success me-1"></i>16%</span> last month</p>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->

                    </div>
                </div>
                <!-- end row -->

                <!-- start row -->
                <div class="row">
                    <div class="col-xl-4 d-flex">
                        <div class="card rounded-3 shadow-none flex-fill w-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-3">
                                    <h6 class="fs-16 fw-semibold text-truncate">Most Ordered Plan</h6>
                                    <div class="dropdown flex-shrink-0">
                                        <a href="javascript:void(0);" class="btn btn-light fw-normal d-inline-flex align-items-center border" data-bs-toggle="dropdown" aria-expanded="false">This Month<i class="isax isax-calendar-2 ms-2 text-gray-9"></i></a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">Today</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">This Week</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">This Year</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="bg-light rounded-3 p-3">
                                    <div class="d-flex align-items-center mb-2 justify-content-between gap-2 flex-wrap flex-md-nowrap">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-lg bg-white rounded-3">
                                            <img src="assets/img/icons/company-logo-01.svg" alt="img" class="rounded-3 img-fluid w-auto h-auto">
                                        </span>
                                            <div class="ms-2">
                                                <p class="mb-1"><span class="text-gray-9 fw-medium">Enterprise</span> (Monthly)</p>
                                                <p class="mb-0">Total Order : 201</p>
                                            </div>
                                        </div>                                        
                                    </div>
                                    <div class="text-end">
                                        <p class="text-gray-9">$549.00</p>
                                    </div>                                    
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-xl-4 d-flex">
                        <div class="card rounded-3 shadow-none flex-fill w-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-3">
                                    <h6 class="fs-16 fw-semibold text-truncate">Top Company with Plan</h6>
                                    <div class="dropdown flex-shrink-0">
                                        <a href="javascript:void(0);" class="btn btn-light border fw-normal d-inline-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
                                    Today<i class="isax isax-calendar-2 ms-2 text-gray-9"></i>
                                    </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">This Month</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">This Week</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">This Year</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="bg-light rounded-3 p-3">
                                    <div class="d-flex align-items-center mb-2 justify-content-between gap-2 flex-wrap flex-lg-nowrap">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-lg bg-white rounded-3">
                                            <img src="assets/img/icons/company-logo-02.svg" alt="img" class="rounded-3 img-fluid w-auto h-auto">
                                        </span>
                                            <div class="ms-2">
                                                <p class="mb-1 fw-medium text-gray-9">Tech Bazaar</p>
                                                <p class="mb-0 text-truncate">rebazaar@example.com</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <p class="text-gray-9 flex-shrink-0">10 Plans</p>
                                    </div> 
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-xl-4 d-flex">
                        <div class="card rounded-3 shadow-none flex-fill w-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-3">
                                    <h6 class="fs-16 fw-semibold text-truncate">Most Domains</h6>
                                    <div class="dropdown flex-shrink-0">
                                        <a href="javascript:void(0);" class="btn btn-light border fw-normal d-inline-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">This Week<i class="isax isax-calendar-2 ms-2 text-gray-9"></i></a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">Today</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">This Week</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">This Year</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="bg-light rounded-3 p-3">
                                    <div class="d-flex align-items-center mb-2 justify-content-between gap-2 flex-wrap">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-lg bg-white rounded-3">
                                            <img src="assets/img/icons/company-logo-03.svg" alt="img" class="rounded-3 img-fluid w-auto h-auto">
                                        </span>
                                            <div class="ms-2">
                                                <p class="mb-1 fw-medium text-gray-9">Quick Cart</p>
                                                <p class="mb-0">qc.example.com</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <p class="text-gray-9">150 Users</p>
                                    </div> 
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                </div>
                <!-- end row -->

                <!-- start row -->
                <div class="row">
                    <div class="col-xl-6 d-flex">
                        <div class="card shadow-none rounded-3 flex-fill w-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <h6>Latest Registered Companies</h6>
                                    <a href="companies.php" class="btn btn-sm btn-dark">View all</a>
                                </div>
                                <!-- Table List -->
                                <div class="table-responsive no-filter no-paginaion">
                                    <table class="table table-nowrap datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Company</th>
                                                <th>Plan</th>
                                                <th>Due Date</th>
                                                <th class="no-sort">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
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
                                                <td>04 Mar 2025</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex align-item-center justify-content-center"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>20 Feb 2025</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex align-item-center justify-content-center"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                                            <img src="assets/img/icons/company-03.svg" class="rounded-0" alt="img">
                                                        </a>
                                                        <div>
                                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Tech Bazaar</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Advanced (Monthly)</td>
                                                <td>12 Nov 2024</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex align-item-center justify-content-center"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>25 Oct 2024</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex align-item-center justify-content-center"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>18 Oct 2024</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex align-item-center justify-content-center"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>22 Sep 2024</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex align-item-center justify-content-center"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>Enterprise (Monthly)</td>
                                                <td>15 Sep 2024</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex align-item-center justify-content-center"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /Table List -->
                            </div><!-- end card body -->
                        </div><!-- card end  -->
                    </div>

                    <!-- earnings -->
                    <div class="col-xl-6 d-flex">
                        <div class="card shadow-none rounded-3 flex-fill w-100">
                            <div class="card-body pb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-semibold fs-16">Earnings</h6>
                                    <div class="d-flex align-items-center">
                                        <p class="d-inline-flex align-items-center me-4 mb-0"><i class="fa-solid fa-square text-primary fs-12 me-1"></i>Income</p>
                                        <div class="input-icon-end position-relative">
                                            <input type="text" class="form-control form-control-sm yearpicker" value="2024">
                                            <span class="input-icon-addon text-dark">
                                                <i class="isax isax-calendar-2"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div id="earnings-chart" class="chart-set"></div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                </div>
                <!-- end row -->

                <!-- start row -->
                <div class="row">
                    <div class="col-lg-7 d-flex">
                        <div class="card shadow-none w-100 rounded-3">
                            <div class="card-body">
                                <h6 class="mb-3">Recent Plan Expired</h6>
                                <!-- Table List -->
                                <div class="table-responsive no-filter no-paginaion">
                                    <table class="table table-nowrap datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Company</th>
                                                <th>Plan</th>
                                                <th>Expired On</th>
                                                <th class="no-sort">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
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
                                                <td>Advanced (Monthly)</td>
                                                <td>04 Mar 2025</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>20 Feb 2025</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex align-item-center justify-content-center"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                                            <img src="assets/img/icons/company-10.svg" class="rounded-0" alt="img">
                                                        </a>
                                                        <div>
                                                            <h6 class="fs-14 fw-medium mb-0"><a href="javascript:void(0);">Dream Space</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Advanced (Monthly)</td>
                                                <td>12 Nov 2024</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex align-item-center justify-content-center"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>25 Oct 2024</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex align-item-center justify-content-center"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>Enterprise (Monthly)</td>
                                                <td>18 Oct 2024</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex align-item-center justify-content-center"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>22 Sep 2024</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex align-item-center justify-content-center"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>15 Sep 2024</td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" class="rounded-2 w-auto h-auto bg-light fs-14 border-0 p-1 d-inline-flex align-items-center justify-content-around">
                                                        <i class="isax isax-eye d-inline-flex align-item-center justify-content-center"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /Table List -->
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-lg-5 d-flex">
                        <div class="card shadow-none w-100 rounded-3">
                            <div class="card-body">
                                <h6 class="mb-3">Recent Domain</h6>
                                <!-- Table List -->
                                <div class="table-responsive no-filter no-paginaion">
                                    <table class="table table-nowrap datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Company</th>
                                                <th>Plan</th>
                                                <th class="no-sort">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
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
                                                <td>Advanced (Monthly)</td>
                                                <td class="action-item">
                                                    <div class="d-flex align-item-center">
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-soft-danger d-inline-flex align-item-center justify-content-center p-1 fs-14 border-0 me-2">
                                                            <i class="isax isax-close-circle"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-soft-success d-inline-flex align-item-center justify-content-center p-1 fs-14 border-0">
                                                            <i class="isax isax-tick-square"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>Basic (Yearly)</td>
                                                <td class="action-item">
                                                    <div class="d-flex align-item-center">
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-soft-danger d-inline-flex align-item-center justify-content-center p-1 fs-14 border-0 me-2">
                                                            <i class="isax isax-close-circle"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-soft-success d-inline-flex align-item-center justify-content-center p-1 fs-14 border-0">
                                                            <i class="isax isax-tick-square"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>Advanced (Monthly)</td>
                                                <td class="action-item">
                                                    <div class="d-flex align-item-center">
                                                        <a href="javascript:void(0);" class="btn p-1 rounded-2 bg-danger-subtle fs-14 border-0 me-1 d-flex align-items-center justify-content-center">
                                                            <i class="isax isax-close-circle"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn p-1 rounded-2 bg-success-subtle fs-14 border-0 d-flex align-items-center justify-content-center">
                                                            <i class="isax isax-tick-square"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td class="action-item">
                                                    <div class="d-flex align-item-center">
                                                        <a href="javascript:void(0);" class="btn p-1 rounded-2 bg-danger-subtle fs-14 border-0 me-1 d-flex align-items-center justify-content-center">
                                                            <i class="isax isax-close-circle"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn p-1 rounded-2 bg-success-subtle fs-14 border-0 d-flex align-items-center justify-content-center">
                                                            <i class="isax isax-tick-square"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>Enterprise (Monthly)</td>
                                                <td class="action-item">
                                                    <div class="d-flex align-item-center">
                                                        <a href="javascript:void(0);" class="btn p-1 rounded-2 bg-danger-subtle fs-14 border-0 me-1 d-flex align-items-center justify-content-center">
                                                            <i class="isax isax-close-circle"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn p-1 rounded-2 bg-success-subtle fs-14 border-0 d-flex align-items-center justify-content-center">
                                                            <i class="isax isax-tick-square"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td class="action-item">
                                                    <div class="d-flex align-item-center">
                                                        <a href="javascript:void(0);" class="btn p-1 rounded-2 bg-danger-subtle fs-14 border-0 me-1 d-flex align-items-center justify-content-center">
                                                            <i class="isax isax-close-circle"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn p-1 rounded-2 bg-success-subtle fs-14 border-0 d-flex align-items-center justify-content-center">
                                                            <i class="isax isax-tick-square"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                <td>Enterprise (Monthly)</td>
                                                <td class="action-item">
                                                    <div class="d-flex align-item-center">
                                                        <a href="javascript:void(0);" class="btn p-1 rounded-2 bg-danger-subtle fs-14 border-0 me-1 d-flex align-items-center justify-content-center">
                                                            <i class="isax isax-close-circle"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn p-1 rounded-2 bg-success-subtle fs-14 border-0 d-flex align-items-center justify-content-center">
                                                            <i class="isax isax-tick-square"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /Table List -->
                            </div><!-- end card body -->
                        </div><!-- card end  -->
                    </div>
                </div>
                <!-- end row -->

                <!-- start row -->
                <div class="row">
                    <div class="col-lg-6 d-flex">
                        <div class="card shadow-none w-100 rounded-3">
                            <div class="card-body pb-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6>Companies Registered</h6>
                                    <div class="dropdown flex-shrink-0">
                                        <a href="javascript:void(0);" class="btn btn-light border fw-normal d-inline-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
									 This Week<i class="isax isax-calendar-2 ms-2"></i>
									</a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">Today</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">This Week</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">This Year</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div id="register-chart" class="chart-set"></div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-lg-6 d-flex">
                        <div class="card shadow-none w-100 rounded-3">
                            <div class="card-body">
                                <div class="d-flex align-item-center justify-content-between mb-2">
                                    <h6>Top Plans</h6>
                                    <a href="subscriptions.php" class="btn btn-dark btn-sm">View all</a>
                                </div>
                                <div id="plane-chart"></div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                </div>
                <!-- end row -->

                <!-- start row -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow-none w-100 rounded-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6>Invoices</h6>
                                    <a href="invoice.php" class="btn btn-sm btn-dark">View all Invoices</a>
                                </div>
                                <!-- Table List -->
                                <div class="table-responsive no-filter no-paginaion">
                                    <table class="table table-nowrap datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="no-sort">ID</th>
                                                <th>Company</th>
                                                <th>Plan</th>
                                                <th>Created On</th>
                                                <th>Expiring On</th>
                                                <th>Amount</th>
                                                <th>Payment Mode</th>
                                                <th class="no-sort">Status</th>
                                                <th class="no-sort"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><a href="javascript:void(0);" class="link-default">INV00025</a></td>
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
                                                <td>22 Feb 2025</td>
                                                <td>22 Mar 2025</td>
                                                <td>$200</td>
                                                <td>Cash</td>
                                                <td>
                                                    <span class="badge badge-sm badge-soft-success d-inline-flex align-items-center">Paid
													<i class="isax isax-tick-circle ms-1"></i>
												</span>
                                                </td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                        <i class="isax isax-more"></i>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-eye me-2"></i>View</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-trash me-2"></i>Delete</a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="javascript:void(0);" class="link-default">INV00024</a></td>
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
                                                <td>07 Feb 2025</td>
                                                <td>07 Feb 2026</td>
                                                <td>$600</td>
                                                <td>Check</td>
                                                <td>
                                                    <span class="badge badge-sm badge-soft-success d-inline-flex align-items-center">Paid
													<i class="isax isax-tick-circle ms-1"></i>
												</span>
                                                </td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                        <i class="isax isax-more"></i>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-eye me-2"></i>View</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-trash me-2"></i>Delete</a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="javascript:void(0);" class="link-default">INV00023</a></td>
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
                                                <td>09 Dec 2024</td>
                                                <td>09 Jan 2025</td>
                                                <td>$400</td>
                                                <td>Check</td>
                                                <td>
                                                    <span class="badge badge-sm badge-soft-success d-inline-flex align-items-center">Paid
													<i class="isax isax-tick-circle ms-1"></i>
												</span>
                                                </td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                        <i class="isax isax-more"></i>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-eye me-2"></i>View</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"><i class="isax isax-trash me-2"></i>Delete</a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="javascript:void(0);" class="link-default">INV00022</a></td>
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
                                                <td>Basic (Monthly)</td>
                                                <td>30 Nov 2024</td>
                                                <td>30 Dec 2024</td>
                                                <td>$50</td>
                                                <td>Check</td>
                                                <td>
                                                    <span class="badge badge-sm badge-soft-success d-inline-flex align-items-center">Paid
													<i class="isax isax-tick-circle ms-1"></i>
												</span>
                                                </td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                        <i class="isax isax-more"></i>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-eye me-2"></i>View</a>
                                                        </li>
                                                        <li>
                                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="javascript:void(0);" class="link-default">INV00016</a></td>
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
                                                <td>Advanced (Monthly)</td>
                                                <td>12 Oct 2024</td>
                                                <td>12 Nov 2024</td>
                                                <td>$400</td>
                                                <td>Cash</td>
                                                <td>
                                                    <span class="badge badge-sm badge-soft-success d-inline-flex align-items-center">Paid
													<i class="isax isax-tick-circle ms-1"></i>
												</span>
                                                </td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                        <i class="isax isax-more"></i>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-eye me-2"></i>View</a>
                                                        </li>
                                                        <li>
                                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="javascript:void(0);" class="link-default">INV00015</a></td>
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
                                                <td>05 Oct 2024</td>
                                                <td>05 Nov 2024</td>
                                                <td>$200</td>
                                                <td>Check</td>
                                                <td>
                                                    <span class="badge badge-sm badge-soft-danger d-inline-flex align-items-center">Unpaid
													<i class="isax isax-close-circle ms-1"></i>
												</span>
                                                </td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                        <i class="isax isax-more"></i>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-eye me-2"></i>View</a>
                                                        </li>
                                                        <li>
                                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="javascript:void(0);" class="link-default">INV00014</a></td>
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
                                                <td>Premium (Yearly)</td>
                                                <td>09 Sep 2024</td>
                                                <td>09 Sep 2025</td>
                                                <td>$3600</td>
                                                <td>Cash</td>
                                                <td>
                                                    <span class="badge badge-sm badge-soft-success d-inline-flex align-items-center">Paid
													<i class="isax isax-tick-circle ms-1"></i>
												</span>
                                                </td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                        <i class="isax isax-more"></i>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-eye me-2"></i>View</a>
                                                        </li>
                                                        <li>
                                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="isax isax-trash me-2"></i>Delete</a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="javascript:void(0);" class="link-default">INV00013</a></td>
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
                                                <td>Premium (Monthly)</td>
                                                <td>02 Sep 2024</td>
                                                <td>02 Oct 2024</td>
                                                <td>$300</td>
                                                <td>Check</td>
                                                <td>
                                                    <span class="badge badge-sm badge-soft-success d-inline-flex align-items-center">Paid
													<i class="isax isax-tick-circle ms-1"></i>
												</span>
                                                </td>
                                                <td class="action-item">
                                                    <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                                        <i class="isax isax-more"></i>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-eye me-2"></i>View</a>
                                                        </li>
                                                        <li>
                                                            <a href="edit-invoice.php" class="dropdown-item d-flex align-items-center"><i class="isax isax-edit me-2"></i>Edit</a>
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
                            <!-- end card body -->
                        </div>
                        <!-- card end  -->
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- End Content -->

            <?php include 'layouts/footer.php'; ?>

        </div>

        <!-- ========================
			End Page Content
		========================= -->

        <!-- Edit Modal Start -->
        <div id="edit_companies" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Category</h4>
                        <button type="button" class="btn-close custom-btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
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
                                        <label class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
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
                                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Website <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <div class="pass-group input-group">
                                            <span class="isax toggle-password isax-eye-slash"></span>
                                            <input type="password" class="pass-inputs form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                        <div class="pass-group input-group">
                                            <span class="isax toggle-password isax-eye-slash"></span>
                                            <input type="password" class="pass-inputs form-control">
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
                                        <label class="form-label">Plan <span class="text-danger">*</span></label>
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
                                        <label class="form-label">Plan Type <span class="text-danger">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Monthly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Currencies <span class="text-danger">*</span></label>
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
                                        <label class="form-label">Language <span class="text-danger">*</span></label>
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
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
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
        <!-- Edit Modal End -->

        <!-- View Companies Start -->
        <div class="modal fade" id="view_companies">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upgrade Package</h4>
                        <button type="button" class="btn-close custom-btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
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
                                        <label class="form-label">Plan Name <span class="text-danger">*</span></label>
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
                                        <label class="form-label">Plan Type <span class="text-danger">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Monthly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Amount <span class="text-danger">*</span></label>
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
        <!-- View Companies End -->

        <!-- Delete Modal Start -->
        <div class="modal fade" id="delete_modal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <img src="assets/img/icons/delete.svg" alt="img">
                        </div>
                        <h6 class="mb-1">Delete Invoices</h6>
                        <p class="mb-3">Are you sure, you want to delete Invoices?</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-outline-white me-3" data-bs-dismiss="modal">Cancel</a>
                            <a href="super-admin-dashboard.php" class="btn btn-primary">Yes, Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete Modal End -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>

</body>

</html>         