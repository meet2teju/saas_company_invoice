<?php
session_start();

// Include session file
// include 'layouts/session.php';

// Check if user is logged in using your session variable
$isLoggedIn = isset($_SESSION['crm_is_login']) && $_SESSION['crm_is_login'] === 1;

// If NOT logged in, show LANDING PAGE
if (!$isLoggedIn):
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <link href="assets/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="assets/aos/aos.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="assets/css/slick.css">
    <link rel="stylesheet" type="text/css" href="assets/css/slick-theme.css">
    <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
</head>
<body>
  <div class="wrapper">
    <header class="header">
      <nav class="navbar navbar-expand-lg">
        <div class="container">
          <a class="navbar-brand" href="index.php"><img src="assets/images/logo.png" alt="logo"></a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav w-100 justify-content-center">
              <li class="nav-item">
                <a class="nav-link" href="#">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#how-to-use">How To Use</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#pricing">Pricing</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#faq">Faq</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#contact">Contact US</a>
              </li>
            </ul>
            <div id="navbarText" class="header-btnbox">
              <a type="button" href="admin/login.php" class="secondary-btn">Sign In</a>
              <a type="button" href="admin/register.php" class="primary-btn">Sign Up</a>
            </div>
          </div>
        </div>
      </nav>
    </header>
    <section class="main-part">
      <div class="hero-banner">
        <div class="container">
          <img src="assets/images/banner-bac-img.png" alt="banner-img" class="bac-shape">
          <div class="row align-items-center row-gap-4">
            <div class="col-lg-6 col-md-12" data-aos="fade-right" data-aos-duration="1000">
              <h1 class="banner-title">Free Online <span class="hilight-color">Invoice</span>  Generator</h1>
              <p class="banner-p">Create unlimited professional invoices in minutes with our free invoice maker. Streamline and boost your billing process with just one click. Download and send invoices - no signup required.</p>
              <a type="button" href="admin/register.php" class="primary-btn mt-50">Generate Free Invoice</a>
            </div>
            <div class="col-lg-6 col-md-12" data-aos="fade-left" data-aos-duration="1000">
              <img src="assets/images/banner-img.png" alt="hreo-img" class="img-box banner-img">
            </div>
          </div>
        </div>
      </div>
      <div class="invoice-logo-section mt-100" data-aos="flip-up" data-aos-duration="1000">
        <div class="container">
          <h3 class="invoice-logo-title text-center">Free Online Invoice Generator</h3>
          <div class="line-box">
            <div class="line-1"></div>
            <div class="line-2"></div>
          </div>
          <div class="logo-slider">
            <div class="logo-card">
                <img src="assets/images/Invoice Generator-1.png" alt="about-img">
            </div>
            <div class="logo-card">
                <img src="assets/images/Invoice Generator-2.png" alt="about-img">
            </div>
            <div class="logo-card">
              <img src="assets/images/Invoice Generator-3.png" alt="about-img">
            </div>
            <div class="logo-card">
                <img src="assets/images/Invoice Generator-4.png" alt="about-img">
            </div>
            <div class="logo-card">
              <img src="assets/images/Invoice Generator-5.png" alt="about-img">
            </div>
            <div class="logo-card">
              <img src="assets/images/Invoice Generator-4.png" alt="about-img">
            </div>
          </div>
        </div>
      </div>
      <div class="container pt-100" id="how-to-use" data-aos="zoom-in" data-aos-duration="1000">
        <div class="info-section">
          <h3 class="invoice-step-title">How to use our free <span class="hilight-color">Invoice Generator</span> in 3 simple steps?</h3>
          <p class="banner-p text-center mt-0">Automate your invoicing with the best AI Invoice Generator and create custom Invoices effortlessly.</p>
          <div class="row mt-50 row-gap-4 justify-content-between align-items-center">
            <div class="col-lg-5 col-md-12">
              <img src="assets/images/invoise-steps-img.png" alt="hreo-img" class="img-box banner-img">
            </div>
            <div class="col-lg-6 col-md-12">
              <div class="step-box">
                  <div class="outer-icon-box">
                    <div class="icon-box">
                      <i class="bi bi-send"></i>
                    </div> 
                  </div>
                  <div class="step-content">
                    <p class="step-no">STEP 1</p>   
                    <p class="step-title">Access Invoice Generator</p>     
                    <p class="step-subtitle">Click on the "Generate Free Invoice".</p>
                  </div> 
              </div>
              <div class="step-box">
                  <div class="outer-icon-box">
                    <div class="icon-box">
                      <i class="bi bi-send"></i>
                    </div> 
                  </div>
                  <div class="step-content">
                    <p class="step-no">STEP 2</p>   
                    <p class="step-title">Enter your details</p>     
                    <p class="step-subtitle">Drop your custom brand logo and start filling in your invoice details.</p>
                  </div> 
              </div>
              <div class="step-box">
                  <div class="outer-icon-box">
                    <div class="icon-box">
                      <i class="bi bi-send"></i>
                    </div> 
                  </div>
                  <div class="step-content">
                    <p class="step-no">STEP 3</p>   
                    <p class="step-title">Download and share</p>     
                    <p class="step-subtitle">Now, click on the download invoice button & get your invoice PDF in seconds!</p>
                  </div>
              </div> 
            </div>
          </div>
          <div class="text-center"><a type="button" href="admin/register.php" class="primary-btn mt-30">Generate Free Invoice</a></div>
        </div>
      </div>
      <div class="container pt-100" id="pricing">
        <div class="pricing-section">
          <h3 class="invoice-step-title">Pricing</h3>
          <div class="line-box">
            <div class="line-1"></div>
            <div class="line-2"></div>
          </div>
          <div class="row mt-50 row-gap-4">
            <div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-duration="1000">
              <div class="pricing-left-box">
                <div class="price">$0.00 <span class="month">/ Month</span></div>
                <div class="plan-title">Basic Plan</div>
                <div class="subtitle">If you're just starting out and want the basics</div>
                <a type="button" href="admin/register.php" class="primary-btn">Generate Free Invoice</a>
                <div class="features-box">
                  <div class="feature"><i class="bi bi-check-circle-fill"></i>Unlimited invoices</div>
                  <div class="feature"><i class="bi bi-check-circle-fill"></i>Unlimited clients</div>
                  <div class="feature"><i class="bi bi-check-circle-fill"></i>Unlimited products</div>
                  <div class="feature"><i class="bi bi-check-circle-fill"></i>Upload your logo</div>
                  <div class="feature"><i class="bi bi-check-circle-fill"></i>Send directly to your clients</div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-duration="1000">
              <div class="pricing-right-box">
                  <p class="pricing-title">Try the quick and easy create-anything app.</p>
                  <a type="button" href="admin/register.php" class="primary-btn">Get Adobe Express Free</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container pt-100" id="faq" data-aos="flip-left" data-aos-duration="2000">
        <div class="pricing-section">
            <h3 class="invoice-step-title">
                Frequently <span class="hilight-color">asked</span> questions
            </h3>
            <div class="line-box">
                <div class="line-1"></div>
                <div class="line-2"></div>
            </div>
            <div class="accordion faq-box mt-5" id="faqAccordion">
                <div class="accordion-item faq-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button faq-question" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq1">
                            Which is the best invoice generator?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body faq-answer">
                            Procol offers the best online Invoice Generator. For free use, anyone can use it,
                            whether you are a business or an individual. It automatically streamlines your
                            workflow and improves efficiency.
                        </div>
                    </div>
                </div>
                <div class="accordion-item faq-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button faq-question collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq2">
                            How do I create my invoice?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body faq-answer">
                            You can create your invoice using any online invoice generator or billing tool.
                        </div>
                    </div>
                </div>
                <div class="accordion-item faq-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button faq-question collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq3">
                            Where to create an invoice for free?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body faq-answer">
                            Several free invoice tools allow you to generate invoices without cost.
                        </div>
                    </div>
                </div>
                <div class="accordion-item faq-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button faq-question collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq4">
                            Can Google create invoices?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body faq-answer">
                            Yes! You can create invoices using Google Docs, Google Sheets, or templates.
                        </div>
                    </div>
                </div>
                <div class="accordion-item faq-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button faq-question collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq5">
                            Which invoice app is free?
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body faq-answer">
                            Many apps offer free invoice creation with basic features.
                        </div>
                    </div>
                </div>
                <div class="accordion-item faq-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button faq-question collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq6">
                            Can AI generate an invoice?
                        </button>
                    </h2>
                    <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body faq-answer">
                            Yes, AI can generate invoices automatically using user inputs.
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <div class="container pt-100" id="contact">
        <div class="row row-gap-4">
          <div class="col-lg-6 col-md-12" data-aos="fade-right" data-aos-duration="1000">
            <img src="assets/images/contact-img.png" alt="hreo-img" class="contact-img">
          </div>
          <div class="col-lg-6 col-md-12" data-aos="fade-left" data-aos-duration="1000">
            <div class="contact-form">
              <h3 class="contact-title">How much can you save with <span class="hilight-color">Invoice</span> e-procurement platform?</h3>
              <form class="lead-form mt-40">
                <div class="row">
                  <div class="col-md-6">
                    <input type="text" placeholder="First Name*" required>
                  </div>
                  <div class="col-md-6">
                    <input type="text" placeholder="Last Name">
                  </div>
                  <div class="col-md-6">
                    <input type="text" placeholder="Phone No*" required>
                  </div>
                  <div class="col-md-6">
                    <input type="email" placeholder="Email*" required>
                  </div>
                  <div class="col-md-12">
                    <textarea rows="4" id="message" placeholder="Message"></textarea>
                  </div>
                </div>
                <button type="submit" class="primary-btn">Get Started Now</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <footer class="footer mt-100">
        <div class="container">
          <div class="footer-top">
            <img src="assets/images/logo.png" alt="Logo" class="footer-logo">
            <ul class="footer-menu">
                <li><a href="#">Home</a></li>
                <li><a href="#how-to-use">How To Use</a></li>
                <li><a href="#pricing">Pricing</a></li>
                <li><a href="#faq">Faq</a></li>
                <li><a href="#contact">Contact Us</a></li>
            </ul>
          </div>
          <div class="footer-bottom">
            <p>© 2025 invoice. All rights reserved.</p>
            <div class="social-icons">
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-instagram"></i></a>
                <a href="#"><i class="bi bi-linkedin"></i></a>
                <a href="#"><i class="bi bi-twitter-x"></i></a>
            </div>
          </div>
        </div>
    </footer>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/slick.min.js"></script>
  <script src="assets/aos/aos.js"></script>
  <script>
    AOS.init();
  </script>
  <script>
    window.addEventListener('scroll', function() {
      const header = document.querySelector('.header');
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
  </script>
  <script>
    $('.logo-slider').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2500,
        dots: false,
        arrows: false,
        responsive: [
            {
                breakpoint: 992,
                settings: { slidesToShow: 3 }
            },
            {
                breakpoint: 768,
                settings: { slidesToShow: 2 }
            }
            ,
            {
                breakpoint: 425,
                settings: { slidesToShow: 1 }
            }
        ]
    });
  </script>
</body>
</html>

<?php
// If user IS logged in, show DASHBOARD
else:
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php include 'admin/layouts/title-meta.php'; ?> 
	<?php include 'admin/layouts/head-css.php'; ?>
</head>

<body>

    <!-- Start Main Wrapper -->
    <div class="main-wrapper">

		<?php include 'admin/layouts/menu.php'; ?>

		<!-- ========================
			Start Page Content
		========================= -->

		<div class="page-wrapper">

			<!-- Start Content -->
			<div class="content">

				<!-- Start Breadcrumb -->
				<div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
					<div>
						<h6>Dashboard</h6>
					</div>
					<div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
						<div id="reportrange" class="reportrange-picker d-flex align-items-center">
							<i class="isax isax-calendar text-gray-5 fs-14 me-1"></i><span class="reportrange-picker-field">16 Apr 25 - 16 Apr 25</span>
						</div>
						<div class="dropdown">
							<a class="btn btn-primary d-flex align-items-center justify-content-center dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);" role="button">
								Create New
							</a>
							<ul class="dropdown-menu dropdown-menu-start">
								<li>
									<a href="admin/add-invoice.php" class="dropdown-item d-flex align-items-center">
										<i class="isax isax-document-text-1 me-2"></i>Invoice
									</a>
								</li>
								<li>
									<a href="admin/expenses.php" class="dropdown-item d-flex align-items-center">
										<i class="isax isax-money-send me-2"></i>Expense
									</a>
								</li>
								<li>
									<a href="admin/add-credit-notes.php" class="dropdown-item d-flex align-items-center">
										<i class="isax isax-money-add me-2"></i>Credit Notes
									</a>
								</li>
								<li>
									<a href="admin/add-debit-notes.php" class="dropdown-item d-flex align-items-center">
										<i class="isax isax-money-recive me-2"></i>Debit Notes
									</a>
								</li>
								<li>
									<a href="admin/add-purchases-orders.php" class="dropdown-item d-flex align-items-center">
										<i class="isax isax-document me-2"></i>Purchase Order
									</a>
								</li>
								<li>
									<a href="admin/add-quotation.php" class="dropdown-item d-flex align-items-center">
										<i class="isax isax-document-download me-2"></i>Quotation
									</a>
								</li>
								<li>
									<a href="admin/add-delivery-challan.php" class="dropdown-item d-flex align-items-center">
										<i class="isax isax-document-forward me-2"></i>Delivery Challan
									</a>
								</li>
							</ul>
						</div>
						<div class="dropdown">
							<a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center"  data-bs-toggle="dropdown">
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
				<!-- End Breadcrumb -->

				<div class="bg-primary rounded welcome-wrap position-relative mb-3">

					<!-- start row -->
					<div class="row">
						<div class="col-lg-8 col-md-9 col-sm-7">
							<div>
								<h5 class="text-white mb-1">Good Morning, Jafna Cremson</h5>
								<p class="text-white mb-3">You have 15+ invoices saved to draft that has to send to customers</p>
								<div class="d-flex align-items-center flex-wrap gap-3">
									<p class="d-flex align-items-center fs-13 text-white mb-0"><i class="isax isax-calendar5 me-1"></i>Friday, 24 Mar 2025</p>
									<p class="d-flex align-items-center fs-13 text-white mb-0"><i class="isax isax-clock5 me-1"></i>11:24 AM</p>
								</div>
							</div>
						</div><!-- end col -->
					</div>
					<!-- end row -->

					<div class="position-absolute end-0 top-50 translate-middle-y p-2 d-none d-sm-block">
						<img src="admin/assets/img/icons/dashboard.svg" alt="img">
					</div>
				</div>

				<!-- start row -->
				<div class="row">
					<div class="col-md-4 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="mb-3">
									<h6 class="d-flex align-items-center mb-1"><i class="isax isax-category5 text-default me-2"></i>Overview</h6>
								</div>
								<div class="row g-4">
									<div class="col-xl-6">
										<div class="d-flex align-items-center">
											<span class="avatar avatar-44 avatar-rounded bg-primary-subtle text-primary flex-shrink-0 me-2">
												<i class="isax isax-document-text-1 fs-20"></i>
											</span>
											<div>
												<p class="mb-1 text-truncate">Invoices</p>
												<h6 class="fs-16 fw-semibold mb-0 text-truncate">1,041</h6>
											</div>
										</div>
									</div>
									<div class="col-xl-6">
										<div class="d-flex align-items-center me-2">
											<span class="avatar avatar-44 avatar-rounded bg-success-subtle text-success-emphasis flex-shrink-0 me-2">
												<i class="isax isax-profile-2user fs-20"></i>
											</span>
											<div>
												<p class="mb-1 text-truncate">Customers</p>
												<h6 class="fs-16 fw-semibold mb-0 text-truncate">3,462</h6>
											</div>
										</div>
									</div>
									<div class="col-xl-6">
										<div class="d-flex align-items-center">
											<span class="avatar avatar-44 avatar-rounded bg-warning-subtle text-warning-emphasis flex-shrink-0 me-2">
												<i class="isax isax-dcube fs-20"></i>
											</span>
											<div>
												<p class="mb-1 text-truncate">Amount Due</p>
												<h6 class="fs-16 fw-semibold mb-0 text-truncate">$1,642</h6>
											</div>
										</div>
									</div>
									<div class="col-xl-6">
										<div class="d-flex align-items-center me-2">
											<span class="avatar avatar-44 avatar-rounded bg-info-subtle text-info-emphasis flex-shrink-0 me-2">
												<i class="isax isax-document-text fs-20"></i>
											</span>
											<div>
												<p class="mb-1 text-truncate">Quotations</p>
												<h6 class="fs-16 fw-semibold mb-0 text-truncate">2,150</h6>
											</div>
										</div>
									</div>
								</div>
							</div> <!-- end card body -->
						</div> <!-- end card -->
					</div> <!-- end col -->
					<div class="col-md-4 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="mb-3">
									<h6 class="d-flex align-items-center mb-1"><i class="isax isax-chart-215 text-default me-2"></i>Sales Analytics</h6>
								</div>
								<div class="row g-4">
									<div class="col-xl-6">
										<div class="d-flex align-items-center">
											<span class="avatar avatar-44 avatar-rounded bg-primary-subtle text-primary flex-shrink-0 me-2">
												<i class="isax isax-document-forward fs-20"></i>
											</span>
											<div>
												<p class="mb-1 text-truncate">Total Sales</p>
												<h6 class="fs-16 fw-semibold mb-0">$40,569</h6>
											</div>
										</div>
									</div>
									<div class="col-xl-6">
										<div class="d-flex align-items-center me-2">
											<span class="avatar avatar-44 avatar-rounded bg-success-subtle text-success-emphasis flex-shrink-0 me-2">
												<i class="isax isax-programming-arrow fs-20"></i>
											</span>
											<div>
												<p class="mb-1 text-truncate">Purchase</p>
												<h6 class="fs-16 fw-semibold mb-0 text-truncate">$1,54,220</h6>
											</div>
										</div>
									</div>
									<div class="col-xl-6">
										<div class="d-flex align-items-center">
											<span class="avatar avatar-44 avatar-rounded bg-warning-subtle text-warning-emphasis flex-shrink-0 me-2">
												<i class="isax isax-dollar-circle fs-20"></i>
											</span>
											<div>
												<p class="mb-1 mb-0">Expenses</p>
												<h6 class="fs-16 fw-semibold text-truncate">$10,041</h6>
											</div>
										</div>
									</div>
									<div class="col-xl-6">
										<div class="d-flex align-items-center me-2">
											<span class="avatar avatar-44 avatar-rounded bg-info-subtle text-info-emphasis flex-shrink-0 me-2">
												<i class="isax isax-flag fs-20"></i>
											</span>
											<div>
												<p class="mb-1 text-truncate">Credits</p>
												<h6 class="fs-16 fw-semibold mb-0 text-truncate">$12,150</h6>
											</div>
										</div>
									</div>
								</div>
							</div> <!-- end card body -->
						</div> <!-- end card -->
					</div> <!-- end col -->
					<div class="col-md-4 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="mb-3">
									<h6 class="d-flex align-items-center mb-1"><i class="isax isax-chart-success5 text-default me-2"></i>Invoice Statistics</h6>
								</div>
								<div class="row g-4">
									<div class="col-xl-6">
										<div class="d-flex align-items-center">
											<span class="avatar avatar-44 avatar-rounded bg-primary-subtle text-primary flex-shrink-0 me-2">
												<i class="isax isax-document fs-20"></i>
											</span>
											<div>
												<p class="mb-1 text-truncate">Invoiced</p>
												<h6 class="fs-16 fw-semibold mb-0">$21,132</h6>
											</div>
										</div>
									</div>
									<div class="col-xl-6">
										<div class="d-flex align-items-center me-2">
											<span class="avatar avatar-44 avatar-rounded bg-success-subtle text-success-emphasis flex-shrink-0 me-2">
												<i class="isax isax-document-forward fs-20"></i>
											</span>
											<div>
												<p class="mb-1 text-truncate">Received</p>
												<h6 class="fs-16 fw-semibold mb-0 text-truncate">$10,763</h6>
											</div>
										</div>
									</div>
									<div class="col-xl-6">
										<div class="d-flex align-items-center">
											<span class="avatar avatar-44 avatar-rounded bg-warning-subtle text-warning-emphasis flex-shrink-0 me-2">
												<i class="isax isax-document-previous fs-20"></i>
											</span>
											<div>
												<p class="mb-1 text-truncate">Outstanding</p>
												<h6 class="fs-16 fw-semibold mb-0 text-truncate">$8041</h6>
											</div>
										</div>
									</div>
									<div class="col-xl-6">
										<div class="d-flex align-items-center me-2">
											<span class="avatar avatar-44 avatar-rounded bg-info-subtle text-info-emphasis flex-shrink-0 me-2">
												<i class="isax isax-dislike fs-20"></i>
											</span>
											<div>
												<p class="mb-1 text-truncate">Overdue</p>
												<h6 class="fs-16 fw-semibold text-truncate mb-0">$41,811.2</h6>
											</div>
										</div>
									</div>
								</div>
							</div> <!-- end card body -->
						</div> <!-- end card -->
					</div> <!-- end col -->
				</div>
				<!-- end row -->

				<!-- start row -->
				 <div class="row">
					<div class="col-md-4 d-flex flex-column">
						<div class="card overflow-hidden z-1 flex-fill">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between border-bottom mb-2 pb-2">
									<div>										
										<p class="mb-1">Total Products</p>
										<div class="d-flex align-items-center">
											<h6 class="fs-16 fw-semibold me-2">897</h6>
											<span class="badge badge-sm badge-soft-success">+45<i class="isax isax-arrow-up-15 ms-1"></i></span>
										</div>
									</div>
									<span class="avatar avatar-lg bg-light text-dark avatar-rounded">
										<i class="isax isax-document-text fs-16"></i>
									</span>
								</div>
								<a href="admin/inventory.php" class="fw-medium text-decoration-underline">View Inventory</a>
							</div> <!-- end card body -->
							<div class="position-absolute end-0 bottom-0 z-n1">
								<img src="admin/assets/img/bg/card-bg-01.svg" alt="img">
							</div>
						</div> <!-- end card -->
					</div> <!-- end col -->
					<div class="col-md-4 d-flex flex-column">
						<div class="card overflow-hidden z-1 flex-fill">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between border-bottom mb-2 pb-2">
									<div>										
										<p class="mb-1">Total Sales</p>
										<div class="d-flex align-items-center">
											<h6 class="fs-16 fw-semibold me-2">645</h6>
											<span class="badge badge-sm badge-soft-success">+45<i class="isax isax-arrow-up-15 ms-1"></i></span>
										</div>
									</div>
									<span class="avatar avatar-lg bg-light text-dark avatar-rounded">
										<i class="isax isax-document-text fs-16"></i>
									</span>
								</div>
								<a href="admin/invoices.php" class="fw-medium text-decoration-underline">View Invoices</a>
							</div> <!-- end card body -->
							<div class="position-absolute end-0 bottom-0 z-n1">
								<img src="admin/assets/img/bg/card-bg-02.svg" alt="img">
							</div>
						</div> <!-- end card -->
					</div> <!-- end col -->
					<div class="col-md-4 d-flex flex-column">
						<div class="card overflow-hidden z-1 flex-fill">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between border-bottom mb-2 pb-2">
									<div>										
										<p class="mb-1">Total Quotations</p>
										<div class="d-flex align-items-center">
											<h6 class="fs-16 fw-semibold me-2">128</h6>
											<span class="badge badge-sm badge-soft-success">+45<i class="isax isax-arrow-up-15 ms-1"></i></span>
										</div>
									</div>
									<span class="avatar avatar-lg bg-light text-dark avatar-rounded">
										<i class="isax isax-document-text fs-16"></i>
									</span>
								</div>
								<a href="admin/quotations.php" class="fw-medium text-decoration-underline">View All</a>
							</div> <!-- end card body -->
							<div class="position-absolute end-0 bottom-0 z-n1">
								<img src="admin/assets/img/bg/card-bg-03.svg" alt="img">
							</div>
						</div> <!-- end card -->
					</div>
				 </div>
				<!-- end row -->

				<!-- start row -->
				<div class="row">
					<div class="col-xl-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body pb-0">
								<div class="mb-3">
									<h6 class="mb-1">Revenue</h6>
								</div>
								<div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
									<div>
										<p class="mb-1">Total Revenue</p>
										<div class="d-flex align-items-center">
											<h6 class="fs-16 fw-semibold me-2">897</h6>
											<span class="badge badge-sm badge-soft-success">+45<i class="isax isax-arrow-up-15 ms-1"></i></span>
										</div>
									</div>
									<div class="d-flex align-items-center gap-2">
										<p class="fs-13 text-dark d-flex align-items-center mb-0"><i class="fa-solid fa-circle text-primary-transparent fs-12 me-1"></i>Received </p>
										<p class="fs-13 text-dark d-flex align-items-center mb-0"><i class="fa-solid fa-circle text-primary fs-12 me-1"></i>Outstanding</p>
									</div>
								</div>
								<div id="revenue_chart"></div>
							</div> <!-- end card body -->
						</div> <!-- end card -->
					</div> <!-- end col -->
					<div class="col-xl-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="mb-3">
									<h6 class="mb-1">Customers</h6>
								</div>
								<div class="table-responsive">
									<table class="table table-nowrap table-borderless custom-table">
										<tbody>
											<tr>
												<td>
													<div class="d-flex align-items-center">
														<a href="admin/customer-details.php" class="avatar avatar-lg rounded-circle me-2 flex-shrink-0">
															<img src="admin/assets/img/users/user-06.jpg" class="rounded-circle" alt="img">
														</a>
														<div>
															<h6 class="fs-14 fw-medium mb-1"><a href="admin/customer-details.php">Emily Clark</a></h6>
															<p class="fs-13">No of Invoices : 45</p>
														</div>
													</div>
												</td>
												<td>
													<p class="mb-1">Outstanding </p>
													<h6 class="fs-14 fw-semibold">$3589</h6>
												</td>
												<td>
													<div class="d-flex align-items-center justify-content-end gap-2">
														<a href="admin/add-invoice.php" class="btn btn-icon btn-sm btn-light" data-bs-toggle="tooltip" data-bs-title="New Invoice"><i class="isax isax-add-circle"></i></a>
														<div data-bs-toggle="tooltip" data-bs-title="Add Ledger">
															<a href="#" class="btn btn-icon btn-sm btn-light"  data-bs-toggle="modal" data-bs-target="#add_ledger"><i class="isax isax-document-text-1"></i></a>															
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<div class="d-flex align-items-center">
														<a href="admin/customer-details.php" class="avatar avatar-lg rounded-circle me-2 flex-shrink-0">
															<img src="admin/assets/img/users/user-01.jpg" class="rounded-circle" alt="img">
														</a>
														<div>
															<h6 class="fs-14 fw-medium mb-1"><a href="admin/customer-details.php">John Smith</a></h6>
															<p class="fs-13">No of Invoices : 16</p>
														</div>
													</div>
												</td>
												<td>
													<p class="mb-1">Outstanding </p>
													<h6 class="fs-14 fw-semibold">$5426</h6>
												</td>
												<td>
													<div class="d-flex align-items-center justify-content-end gap-2">
														<a href="admin/add-invoice.php" class="btn btn-icon btn-sm btn-light" data-bs-toggle="tooltip" data-bs-title="New Invoice"><i class="isax isax-add-circle"></i></a>
														<div data-bs-toggle="tooltip" data-bs-title="Add Ledger">
															<a href="#" class="btn btn-icon btn-sm btn-light"  data-bs-toggle="modal" data-bs-target="#add_ledger"><i class="isax isax-document-text-1"></i></a>															
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<div class="d-flex align-items-center">
														<a href="admin/customer-details.php" class="avatar avatar-lg rounded-circle me-2 flex-shrink-0">
															<img src="admin/assets/img/users/user-38.jpg" class="rounded-circle" alt="img">
														</a>
														<div>
															<h6 class="fs-14 fw-medium mb-1"><a href="admin/customer-details.php">Olivia Harris</a></h6>
															<p class="fs-13">No of Invoices : 23</p>
														</div>
													</div>
												</td>
												<td>
													<p class="mb-1">Outstanding </p>
													<h6 class="fs-14 fw-semibold">$1493</h6>
												</td>
												<td>
													<div class="d-flex align-items-center justify-content-end gap-2">
														<a href="admin/add-invoice.php" class="btn btn-icon btn-sm btn-light" data-bs-toggle="tooltip" data-bs-title="New Invoice"><i class="isax isax-add-circle"></i></a>
														<div data-bs-toggle="tooltip" data-bs-title="Add Ledger">
															<a href="#" class="btn btn-icon btn-sm btn-light"  data-bs-toggle="modal" data-bs-target="#add_ledger"><i class="isax isax-document-text-1"></i></a>															
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<div class="d-flex align-items-center">
														<a href="admin/customer-details.php" class="avatar avatar-lg rounded-circle me-2 flex-shrink-0">
															<img src="admin/assets/img/users/user-12.jpg" class="rounded-circle" alt="img">
														</a>
														<div>
															<h6 class="fs-14 fw-medium mb-1"><a href="admin/customer-details.php">William Parker</a></h6>
															<p class="fs-13">No of Invoices : 58</p>
														</div>
													</div>
												</td>
												<td>
													<p class="mb-1">Outstanding </p>
													<h6 class="fs-14 fw-semibold">$7854</h6>
												</td>
												<td>
													<div class="d-flex align-items-center justify-content-end gap-2">
														<a href="admin/add-invoice.php" class="btn btn-icon btn-sm btn-light" data-bs-toggle="tooltip" data-bs-title="New Invoice"><i class="isax isax-add-circle"></i></a>
														<div data-bs-toggle="tooltip" data-bs-title="Add Ledger">
															<a href="#" class="btn btn-icon btn-sm btn-light"  data-bs-toggle="modal" data-bs-target="#add_ledger"><i class="isax isax-document-text-1"></i></a>															
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<div class="d-flex align-items-center">
														<a href="admin/customer-details.php" class="avatar avatar-lg rounded-circle me-2 flex-shrink-0">
															<img src="admin/assets/img/users/user-02.jpg" class="rounded-circle" alt="img">
														</a>
														<div>
															<h6 class="fs-14 fw-medium mb-1"><a href="admin/customer-details.php">Charlotte Brown</a></h6>
															<p class="fs-13">No of Invoices : 09</p>
														</div>
													</div>
												</td>
												<td>
													<p class="mb-1">Outstanding </p>
													<h6 class="fs-14 fw-semibold">$4989</h6>
												</td>
												<td>
													<div class="d-flex align-items-center justify-content-end gap-2">
														<a href="admin/add-invoice.php" class="btn btn-icon btn-sm btn-light" data-bs-toggle="tooltip" data-bs-title="New Invoice"><i class="isax isax-add-circle"></i></a>
														<div data-bs-toggle="tooltip" data-bs-title="Add Ledger">
															<a href="#" class="btn btn-icon btn-sm btn-light"  data-bs-toggle="modal" data-bs-target="#add_ledger"><i class="isax isax-document-text-1"></i></a>															
														</div>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<a href="admin/customers.php" class="btn btn-light btn-lg w-100 text-decoration-underline mt-3">All Customers</a>
							</div> <!-- end card body -->
						</div> <!-- end card -->
					</div> <!-- end col -->
				</div>
				<!-- end row -->

				<!-- start row -->
				<div class="row">
					<div class="col-md-12">
						<div class="card"> 
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
									<h6 class="mb-1">Invoices</h6>
									<a href="admin/invoices.php" class="btn btn-primary mb-1">View all Invoices</a>
								</div>
								<div class="table-responsive no-filter no-pagination">
									<table class="table table-nowrap border mb-0">
										<thead>
											<tr>
												<th>ID</th>
												<th>Customer</th>
												<th>Created On</th>
												<th>Amount</th>
												<th>Paid</th>
												<th>Payment Mode</th>
												<th>Due Date</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<a href="admin/invoice-details.php" class="link-default">INV00025</a>
												</td>
												<td>
													<div class="d-flex align-items-center">
														<a href="admin/customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
															<img src="admin/assets/img/users/user-22.jpg" class="rounded-circle" alt="img">
														</a>
														<div>
															<h6 class="fs-14 fw-medium mb-0"><a href="admin/customer-details.php">Emily Clark</a></h6>
														</div>
													</div>
												</td>
												<td>22 Feb 2025</td>
												<td class="text-dark">$10,000</td>
												<td>$5,000</td>
												<td class="text-dark">Cash</td>	
												<td>04 Mar 2025</td>
											</tr>
											<tr>
												<td>
													<a href="admin/invoice-details.php" class="link-default">INV00024</a>
												</td>
												<td>
													<div class="d-flex align-items-center">
														<a href="admin/customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
															<img src="admin/assets/img/users/user-07.jpg" class="rounded-circle" alt="img">
														</a>
														<div>
															<h6 class="fs-14 fw-medium mb-0"><a href="admin/customer-details.php">John Carter</a></h6>
														</div>
													</div>
												</td>
												<td>07 Feb 2025</td>
												<td class="text-dark">$25,750</td>
												<td>$5,000</td>
												<td class="text-dark">Check</td>
												<td>20 Feb 2025</td>
											</tr>
											<tr>
												<td>
													<a href="admin/invoice-details.php" class="link-default">INV00023</a>
												</td>
												<td>
													<div class="d-flex align-items-center">
														<a href="admin/customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
															<img src="admin/assets/img/users/user-16.jpg" class="rounded-circle" alt="img">
														</a>
														<div>
															<h6 class="fs-14 fw-medium mb-0"><a href="admin/customer-details.php">Sophia White</a></h6>
														</div>
													</div>
												</td>
												<td>09 Dec 2024</td>
												<td class="text-dark">$1,20,500</td>
												<td>$60,000</td>
												<td class="text-dark">Check</td>
												<td>12 Nov 2024</td>
											</tr>
											<tr>
												<td>
													<a href="admin/invoice-details.php" class="link-default">INV00022</a>
												</td>
												<td>
													<div class="d-flex align-items-center">
														<a href="admin/customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
															<img src="admin/assets/img/users/user-08.jpg" class="rounded-circle" alt="img">
														</a>
														<div>
															<h6 class="fs-14 fw-medium mb-0"><a href="admin/customer-details.php">Michael Johnson</a></h6>
														</div>
													</div>
												</td>
												<td>30 Nov 2024</td>
												<td class="text-dark">$7,50,300</td>
												<td>$60,000</td>
												<td class="text-dark">Check</td>
												<td>25 Oct 2024</td>
											</tr>
											<tr>
												<td>
													<a href="admin/invoice-details.php" class="link-default">INV00016</a>
												</td>
												<td>
													<div class="d-flex align-items-center">
														<a href="admin/customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
															<img src="admin/assets/img/users/user-15.jpg" class="rounded-circle" alt="img">
														</a>
														<div>
															<h6 class="fs-14 fw-medium mb-0"><a href="admin/customer-details.php">Daniel Martinez</a></h6>
														</div>
													</div>
												</td>
												<td>12 Oct 2024</td>
												<td class="text-dark">$9,99,999</td>
												<td>$4,00,000</td>
												<td class="text-dark">Cash</td>
												<td>18 Oct 2024</td>
											</tr>
											<tr>
												<td>
													<a href="admin/invoice-details.php" class="link-default">INV00015</a>
												</td>
												<td>
													<div class="d-flex align-items-center">
														<a href="admin/customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
															<img src="admin/assets/img/users/user-27.jpg" class="rounded-circle" alt="img">
														</a>
														<div>
															<h6 class="fs-14 fw-medium mb-0"><a href="admin/customer-details.php">Charlotte Brown</a></h6>
														</div>
													</div>
												</td>
												<td>05 Oct 2024</td>
												<td class="text-dark">$87,650</td>
												<td>$40,000</td>
												<td class="text-dark">Check</td>
												<td>22 Sep 2024</td>
											</tr>
											<tr>
												<td>
													<a href="admin/invoice-details.php" class="link-default">INV00014</a>
												</td>
												<td>
													<div class="d-flex align-items-center">
														<a href="admin/customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
															<img src="admin/assets/img/users/user-14.jpg" class="rounded-circle" alt="img">
														</a>
														<div>
															<h6 class="fs-14 fw-medium mb-0"><a href="admin/customer-details.php">William Parker</a></h6>
														</div>
													</div>
												</td>
												<td>09 Sep 2024</td>
												<td class="text-dark">$69,420</td>
												<td>$30,000</td>
												<td class="text-dark">Cash</td>
												<td>15 Sep 2024</td>
											</tr>
											<tr>
												<td>
													<a href="admin/invoice-details.php" class="link-default">INV00013</a>
												</td>
												<td>
													<div class="d-flex align-items-center">
														<a href="admin/customer-details.php" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
															<img src="admin/assets/img/users/user-25.jpg" class="rounded-circle" alt="img">
														</a>
														<div>
															<h6 class="fs-14 fw-medium mb-0"><a href="admin/customer-details.php">Mia Thompson</a></h6>
														</div>
													</div>
												</td>
												<td>02 Sep 2024</td>
												<td class="text-dark">$33,210</td>
												<td>$15,000</td>
												<td class="text-dark">Check</td>
												<td>20 Aug 2024</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div> <!-- end card body -->
						</div> <!-- end card -->
					</div> <!-- end col -->
				</div>
				<!-- end row -->

				<!-- start row -->
				<div class="row">
					<div class="col-lg-12 col-xl-4 d-flex">
						<div class="card flex-fill">
							<div class="card-body pb-1">
								<div class="mb-3">
									<h6 class="mb-1">Recent Transactions</h6>
								</div>
								<h6 class="fs-14 fw-semibold mb-3">Today</h6>
								<div class="d-flex align-items-center justify-content-between mb-3">
									<div class="d-flex align-items-center">
										<a href="javascript:void(0);" class="avatar avatar-md flex-shrink-0 me-2">
											<img src="admin/assets/img/icons/transaction-01.svg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-semibold mb-1"><a href="javascript:void(0);">Andrew James</a></h6>
											<p class="fs-13"><a href="admin/invoice-details.php" class="link-default">#INV45478</a></p>
										</div>
									</div>
									<div class="text-end">
										<span class="badge badge-lg badge-soft-success">+ $989.15</span>
									</div>
								</div>
								<div class="d-flex align-items-center justify-content-between mb-3">
									<div class="d-flex align-items-center">
										<a href="javascript:void(0);" class="avatar avatar-md flex-shrink-0 me-2">
											<img src="admin/assets/img/icons/transaction-02.svg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-semibold mb-1"><a href="javascript:void(0);">John Carter</a></h6>
											<p class="fs-13"><a href="admin/invoice-details.php" class="link-default">#INV45477</a></p>
										</div>
									</div>
									<div class="text-end">
										<span class="badge badge-lg badge-soft-danger">- $300.12</span>
									</div>
								</div>
								<hr>
								<h6 class="fs-14 fw-semibold mb-3">Yesterday</h6>
								<div class="d-flex align-items-center justify-content-between mb-3">
									<div class="d-flex align-items-center">
										<a href="javascript:void(0);" class="avatar avatar-md flex-shrink-0 me-2">
											<img src="admin/assets/img/icons/transaction-02.svg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-semibold mb-1"><a href="javascript:void(0);">Sophia White</a></h6>
											<p class="fs-13"><a href="admin/invoice-details.php" class="link-default">#INV45476</a></p>
										</div>
									</div>
									<div class="text-end">
										<span class="badge badge-lg badge-soft-success"> + $669</span>
									</div>
								</div>
								<div class="d-flex align-items-center justify-content-between mb-3">
									<div class="d-flex align-items-center">
										<a href="javascript:void(0);" class="avatar avatar-md flex-shrink-0 me-2">
											<img src="admin/assets/img/icons/transaction-02.svg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-semibold mb-1"><a href="javascript:void(0);">Daniel Martinez</a></h6>
											<p class="fs-13"><a href="admin/invoice-details.php" class="link-default">#INV45475</a></p>
										</div>
									</div>
									<div class="text-end">
										<span class="badge badge-lg badge-soft-success"> + $474.22</span>
									</div>
								</div>
								<div class="d-flex align-items-center justify-content-between mb-3">
									<div class="d-flex align-items-center">
										<a href="javascript:void(0);" class="avatar avatar-md flex-shrink-0 me-2">
											<img src="admin/assets/img/icons/transaction-01.svg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-semibold mb-1"><a href="javascript:void(0);">Amelia Robinson</a></h6>
											<p class="fs-13"><a href="admin/invoice-details.php" class="link-default">#INV45474</a></p>
										</div>
									</div>
									<div class="text-end">
										<span class="badge badge-lg badge-soft-success"> + $339.79</span>
									</div>
								</div>
							</div> <!-- end card body -->
						</div> <!-- end card -->
					</div> <!-- end col -->

					<div class="col-md-6 col-xl-4 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="mb-3">
									<h6 class="mb-1">Quotations</h6>
								</div>
								<div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
									<div class="d-flex align-items-center">
										<a href="admin/customer-details.php" class="avatar avatar-lg flex-shrink-0 me-2">
											<img src="admin/assets/img/users/user-02.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-semibold mb-1"><a href="admin/customer-details.php">Emily Clark</a></h6>
											<p class="fs-13">QU0014</p>
										</div>
									</div>
									<div class="text-end">
										<span class="badge badge-sm badge-soft-success d-inline-flex align-items-center mb-1">Accepted<i class="isax isax-tick-circle ms-1"></i></span>
										<p class="fs-13">25 Mar 2025</p>
									</div>
								</div>
								<div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
									<div class="d-flex align-items-center">
										<a href="admin/customer-details.php" class="avatar avatar-lg flex-shrink-0 me-2">
											<img src="admin/assets/img/users/user-07.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-semibold mb-1"><a href="admin/customer-details.php">David Anderson</a></h6>
											<p class="fs-13">QU0147</p>
										</div>
									</div>
									<div class="text-end">
										<span class="badge badge-sm badge-soft-info d-inline-flex align-items-center mb-1">Sent<i class="isax isax-arrow-right-24 ms-1"></i></span>
										<p class="fs-13">12 Feb 2025</p>
									</div>
								</div>
								<div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
									<div class="d-flex align-items-center">
										<a href="admin/customer-details.php" class="avatar avatar-lg flex-shrink-0 me-2">
											<img src="admin/assets/img/users/user-16.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-semibold mb-1"><a href="admin/customer-details.php">Sophia White</a></h6>
											<p class="fs-13">QU1947</p>
										</div>
									</div>
									<div class="text-end">
										<span class="badge badge-sm badge-soft-light d-inline-flex align-items-center text-dark mb-1">Expired<i class="isax isax-timer-pause ms-1"></i></span>
										<p class="fs-13">08 Mar 2025</p>
									</div>
								</div>
								<div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
									<div class="d-flex align-items-center">
										<a href="admin/customer-details.php" class="avatar avatar-lg flex-shrink-0 me-2">
											<img src="admin/assets/img/users/user-08.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-semibold mb-1"><a href="admin/customer-details.php">Michael Johnson</a></h6>
											<p class="fs-13">QU2842</p>
										</div>
									</div>
									<div class="text-end">
										<span class="badge badge-sm badge-soft-danger d-inline-flex align-items-center mb-1">Declined<i class="isax isax-close-circle ms-1"></i></span>
										<p class="fs-13">31 Jan 2025</p>
									</div>
								</div>
								<div class="d-flex align-items-center justify-content-between">
									<div class="d-flex align-items-center">
										<a href="admin/customer-details.php" class="avatar avatar-lg flex-shrink-0 me-2">
											<img src="admin/assets/img/users/user-22.jpg" class="rounded-circle" alt="img">
										</a>
										<div>
											<h6 class="fs-14 fw-semibold mb-1"><a href="admin/customer-details.php">Emily Clark</a></h6>
											<p class="fs-13">QU7868</p>
										</div>
									</div>
									<div class="text-end">
										<span class="badge badge-sm badge-soft-success d-inline-flex align-items-center mb-1">Accepted<i class="isax isax-tick-circle ms-1"></i></span>
										<p class="fs-13">18 Jan 2025</p>
									</div>
								</div>
							</div> <!-- end card body -->
						</div> <!-- end card -->
					</div> <!-- end col -->
					<div class="col-md-6 col-xl-4 d-flex flex-column">
						<div class="card d-flex">
							<div class="card-body flex-fill">
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<p class="mb-1">Total Income on Invoice</p>
										<h6 class="fs-16 fw-semibold">$98,545</h6>
									</div>
									<div>
										<h6 class="fs-14 fw-semibold mb-1">30.2 <i class="isax isax-arrow-circle-up4 text-success"></i></h6>
										<p class="fs-13">Vs Last Week</p>
									</div>
								</div>
							</div> <!-- end card body -->
							<div id="invoice_income"></div>
						</div> <!-- end card -->
						<div class="card d-flex">
							<div class="card-body flex-fill">
								<h6 class="mb-3">Top Sales Statistics</h6>
								<div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-3">
									<p class="d-flex align-items-center fs-13 text-dark mb-0"><i class="fa-solid fa-circle fs-8 me-1 text-pink"></i>Dell XPS 13</p>
									<p class="d-flex align-items-center fs-13 text-dark mb-0"><i class="fa-solid fa-circle fs-8 me-1 text-secondary"></i>Nike T-shirt</p>
									<p class="d-flex align-items-center fs-13 text-dark mb-0"><i class="fa-solid fa-circle fs-8 me-1 text-success"></i>Apple iPhone 15</p>
								</div>								
								<div id="total_sales"></div>
							</div> <!-- end card body -->
						</div> <!-- end card -->
					</div> <!-- end col -->
				</div>
				<!-- end row -->

			</div>
			<!-- End Content -->

			<?php include 'admin/layouts/footer.php'; ?>

		</div>

		<!-- ========================
			End Page Content
		========================= -->

		<!-- Start Add Ledger  -->
		<div id="add_ledger" class="modal fade">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Add New Ledger</h4>
						<button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
					</div>
					<form action="index.php">
						<div class="modal-body pb-1">
							<div class="mb-3">
								<label class="form-label">Amount</label>
								<input type="text" class="form-control">
							</div>
							<div class="mb-3">
								<label class="form-label">Date</label>
								<div class="input-group position-relative">
									<input type="text" class="form-control datetimepicker rounded-end" placeholder="dd/mm/yyyy">
									<span class="input-icon-addon fs-16 text-gray-9">
										<i class="isax isax-calendar-2"></i>
									</span>
								</div>
							</div>
							<div class="mb-3">
								<label class="form-label">Mode</label>
								<div class="d-flex align-items-center">
									<div class="form-check me-3">
										<input class="form-check-input" type="radio" name="Radio" id="Radio-sm-1">
										<label class="form-check-label" for="Radio-sm-1">
											Credit
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="Radio" id="Radio-sm-2" checked="">
										<label class="form-check-label" for="Radio-sm-2">
											Debit
										</label>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer d-flex align-items-center justify-content-between gap-1">
							<button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">Create</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- End Add Ledger -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'admin/layouts/vendor-scripts.php'; ?>

</body>

</html>

<?php endif; ?>