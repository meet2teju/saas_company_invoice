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
						<div class="row settings-wrapper d-flex">

							<?php include 'layouts/settings-sidebar.php'; ?>

							<div class="col-xl-9 col-lg-8">
								<div class="mb-0">
									<div class="pb-3 border-bottom mb-3">
										<h6 class="mb-0">Invoice Templates</h6>
									</div>
									<form action="invoice-templates-settings.php">
										<ul class="nav nav-tabs nav-tabs-bottom border-bottom mb-3">
											<li class="nav-item">
												<a id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoice_tab" type="button" role="tab" aria-controls="invoice_tab" aria-selected="true" href="javascript:void(0);" class="nav-link active">Invoice</a>
											</li>
											<li class="nav-item">
												<a id="purchases-tab" data-bs-toggle="tab" data-bs-target="#purchases_tab" type="button" role="tab" aria-controls="purchases_tab" aria-selected="true" class="nav-link"href="javascript:void(0);">Purchases</a>
											</li>
											<li class="nav-item">
												<a id="receipt-tab" data-bs-toggle="tab" data-bs-target="#receipt_tab" type="button" role="tab" aria-controls="receipt_tab" aria-selected="true" class="nav-link" href="javascript:void(0);">Receipt</a>
											</li>
										</ul>
										<div class="tab-content">
											<div class="tab-pane active" id="invoice_tab" role="tabpanel" aria-labelledby="invoice-tab" tabindex="0">
												
												<!-- start row -->
												<div class="row gx-3">
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-01.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_1"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="general-invoice-1.php">General Invoice 1</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-02.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_2"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="general-invoice-2.php">General Invoice 2</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-03.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_3"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="general-invoice-3.php">General Invoice 3</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-04.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_4"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="general-invoice-4.php">General Invoice 4</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-05.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_5"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="general-invoice-5.php">General Invoice 5</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-06.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_6"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="general-invoice-6.php">General Invoice 6</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-07.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_7"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="general-invoice-7.php">General Invoice 7</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-08.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_8"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="general-invoice-8.php">General Invoice 8</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-09.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_9"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="general-invoice-9.php">General Invoice 9</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-10.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_10"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="general-invoice-10.php">General Invoice 10</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
												</div>
												<!-- end row -->

											</div>
											<div class="tab-pane" id="purchases_tab" role="tabpanel" aria-labelledby="purchases-tab" tabindex="0">
												
												<!-- start row -->
												<div class="row gx-3">
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-11.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_11"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="bus-booking-invoice.php">Bus Booking</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-12.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_12"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="car-booking-invoice.php">Car Booking</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-13.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_13"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="coffee-shop-invoice.php">Coffee Shop</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-14.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_14"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="domain-hosting-invoice.php">Domain & Hosting</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-15.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_15"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="ecommerce-invoice.php">Ecommerce</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-16.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_16"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="fitness-center-invoice.php">Fitness</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-17.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_17"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="flight-booking-invoice.php">Dream Flights</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-18.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_18"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="hotel-booking-invoice.php">Hotel Booking</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-19.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_19"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="internet-billing-invoice.php">Internet Billing</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-20.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_20"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="invoice-medical.php">Medical</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-21.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_21"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="money-exchange-invoice.php">Money Exchange</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-22.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_22"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="movie-ticket-booking-invoice.php">Movie Ticket</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-23.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_23"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="restaurants-invoice.php">Restaurant</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-24.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_24"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="student-billing-invoice.php">Student Billing</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-25.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_25"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="train-ticket-invoice.php">Train Ticket</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div><!-- end card body -->
														</div><!-- end card -->
													</div><!-- end col -->
												</div>
												<!-- end row -->

											</div>
											<div class="tab-pane" id="receipt_tab" role="tabpanel" aria-labelledby="receipt-tab" tabindex="0">
												<div class="row gx-3">
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-26.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_26"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="receipt-invoice-1.php">Receipt Invoice 1</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div>
														</div>
													</div>
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-27.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_27"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="receipt-invoice-2.php">Receipt Invoice 2</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div>
														</div>
													</div>
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-28.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_28"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="receipt-invoice-3.php">Receipt Invoice 3</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div>
														</div>
													</div>
													<div class="col-xl-3 col-md-6">
														<div class="card invoice-template">
															<div class="card-body p-2">
																<div class="invoice-img">
																	<a href="#">
																		<img src="assets/img/invoice/general-invoice-29.svg" alt="invoice" class="w-100">
																	</a>
																	<a href="#" class="invoice-view-icon" data-bs-toggle="modal" data-bs-target="#invoice_view_29"><i class="isax isax-eye"></i></a>
																</div>
																<div class="d-flex justify-content-between align-items-center">
																	<a href="receipt-invoice-4.php">Receipt Invoice 4</a>
																	<a href="javascript:void(0);" class="invoice-star d-flex align-items-center justify-content-center">
																		<i class="isax isax-star"></i>
																	</a>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
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

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_1">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">General Invoice 1</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-61.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_2">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">General Invoice 2</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-62.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_3">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">General Invoice 3</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-63.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_4">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">General Invoice 4</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-64.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_5">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">General Invoice 5</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div class="bg-dark"><img src="assets/img/invoice/general-invoice-65.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_6">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">General Invoice 6</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-66.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_7">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">General Invoice 7</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-67.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_8">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">General Invoice 8</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-68.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_9">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">General Invoice 9</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-69.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_10">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">General Invoice 10</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-70.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_11">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Bus Booking</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-71.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_12">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Car Booking</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-72.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_13">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Coffee Shop</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-73.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_14">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Domain & Hosting</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-74.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_15">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Ecommerce</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-75.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_16">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Fitness</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-76.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_17">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Dream Flights</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-77.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_18">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Hotel Booking</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-78.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_19">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Internet Billing</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-79.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_20">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Medical</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-80.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_21">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Money Exchange</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-81.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_22">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Movie Ticket</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-82.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_23">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Restaurant</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-83.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_24">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Student Billing</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-84.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_25">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Train Ticket</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div><img src="assets/img/invoice/general-invoice-85.svg" class="img-fluid invoice-template-img" alt="User Img"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_26">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Receipt Invoice 1</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div class="d-flex align-items-center justify-content-center">
							<img src="assets/img/invoice/general-invoice-86.svg" class="img-fluid invoice-template-img" alt="User Img">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_27">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Receipt Invoice 2</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div class="d-flex align-items-center justify-content-center">
							<img src="assets/img/invoice/general-invoice-87.svg" class="img-fluid invoice-template-img" alt="User Img">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_28">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Receipt Invoice 3</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div class="d-flex align-items-center justify-content-center">
							<img src="assets/img/invoice/general-invoice-88.svg" class="img-fluid invoice-template-img" alt="User Img">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

		<!-- Start Invoivce View -->
		<div class="modal fade addmodal" id="invoice_view_29">
			<div class="modal-dialog modal-dialog-centered modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="mb-0">Receipt Invoice 4</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div class="d-flex align-items-center justify-content-center">
							<img src="assets/img/invoice/general-invoice-89.svg" class="img-fluid invoice-template-img" alt="User Img">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Invoivce View -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>

</body>

</html>        