<?php
$link = $_SERVER[ 'PHP_SELF' ];
$link_array = explode( '/', $link );
$page = end( $link_array );
?>

<!-- Favicon -->
<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

<!-- Apple Touch Icon -->
<link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">

<?php  if ($page !== 'signin.php' && $page !== 'signup.php' && $page !== 'coming-soon.php' && $page !== 'error-404.php' && $page !== 'error-500.php' && $page !== 'free-trial.php' && $page !== 'under-construction.php' && $page !== 'bus-booking-invoice.php' && $page !== 'car-booking-invoice.php'  && $page !== 'forgot-password.php' && $page !== 'lock-screen.php' && $page !== 'general-invoice-1.php' && $page !== 'general-invoice-1a.php' && $page !== 'general-invoice-2.php' && $page !== 'general-invoice-2a.php' && $page !== 'general-invoice-3.php' && $page !== 'general-invoice-4.php' && $page !== 'general-invoice-5.php' && $page !== 'general-invoice-6.php' && $page !== 'general-invoice-7.php' && $page !== 'general-invoice-8.php' && $page !== 'general-invoice-9.php' && $page !== 'general-invoice-10.php' && $page !== 'domain-hosting-invoice.php' && $page !== 'coffee-shop-invoice.php' && $page !== 'ecommerce-invoice.php' && $page !== 'invoice-medical.php' && $page !== 'email-verification.php' && $page !== 'layout-default.php'  && $page !== 'layout-dark.php' && $page !== 'layout-mini.php' && $page !== 'layout-rtl.php' && $page !== 'layout-single.php' && $page !== 'layout-transparent.php' && $page !== 'layout-without-header.php' && $page !== 'login.php' && $page !== 'money-exchange-invoice.php' && $page !== 'movie-ticket-booking-invoice.php' && $page !== 'change-password.php' && $page !== 'student-billing-invoice.php' && $page !== 'success.php' && $page !== 'train-ticket-invoice.php' && $page !== 'two-step-verification.php' && $page !== 'under-maintenance.php' && $page !== 'fitness-center-invoice.php' && $page !== 'flight-booking-invoice.php' && $page !== 'hotel-booking-invoice.php' && $page !== 'restaurants-invoice.php' && $page !== 'reset-password.php' && $page !== 'register.php' && $page !== 'receipt-invoice-1.php' && $page !== 'receipt-invoice-2.php' && $page !== 'receipt-invoice-3.php' && $page !== 'receipt-invoice-4.php' && $page !== 'internet-billing-invoice.php') {   ?>	
<!-- Theme Script js -->
<!-- <script src="assets/js/theme-script.js"></script> -->
<?php }?>

<?php  if ($page !== 'layout-rtl.php') {   ?>		
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<?php }?>

<?php  if ($page == 'layout-rtl.php') {   ?>		
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="assets/css/bootstrap.rtl.min.css">
<?php }?>

<!-- Tabler Icon CSS -->
<link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.min.css">

<?php  if ($page == 'form-vertical.php'||$page == 'tables-basic.php') {   ?>	
<!-- Feathericon CSS -->
<link rel="stylesheet" href="assets/css/feather.css">
<?php }?>

<?php  if ($page == 'icon-feather.php'||$page == 'notes.php') {   ?>	
<!-- Feather CSS -->
<link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
<?php }?>

<!-- Daterangepikcer CSS -->
<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">

<?php  if ($page == 'account-settings.php'||$page == 'add-credit-notes.php'||$page == 'add-debit-notes.php'||$page == 'add-delivery-challan.php'||$page == 'add-invoice.php'||$page == 'add-purchases-orders.php'||$page == 'add-purchases.php'||$page == 'add-quotation.php'||$page == 'calendar.php'||$page == 'companies.php'||$page == 'customer-account-settings.php'||$page == 'customer-add-quotation.php'||$page == 'customer-invoice-details.php'||$page == 'customer-invoices.php'||$page == 'customer-plans-settings.php'||$page == 'customers.php'||$page == 'edit-credit-notes.php'||$page == 'edit-debit-notes.php'||$page == 'edit-delivery-challan.php'||$page == 'edit-invoice.php'||$page == 'edit-purchases-orders.php'||$page == 'edit-purchases.php'||$page == 'edit-quotation.php'||$page == 'expenses.php'||$page == 'incomes.php'||$page == 'index.php'||$page == 'invoice.php'||$page == 'layout-dark.php'||$page == 'layout-default.php'||$page == 'layout-mini.php'||$page == 'layout-rtl.php'||$page == 'layout-single.php'||$page == 'layout-transparent.php'||$page == 'layout-without-header.php'||$page == 'membership-addons.php'||$page == 'notes.php'||$page == 'payments.php'||$page == 'plans-billings.php'||$page == 'profile.php'||$page == 'security-settings.php'||$page == 'super-admin-dashboard.php'||$page == 'supplier-payments.php'||$page == 'suppliers.php'||$page == 'ticket-kanban.php'||$page == 'tickets-list.php'||$page == 'tickets.php'||$page == 'todo-list.php'||$page == 'todo.php') {   ?>
<!-- Datetimepicker CSS -->
<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
<?php }?>

<!-- Fontawesome CSS -->
<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

<!-- Tabler Icon CSS -->
<link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.min.css">

<?php  if ($page == 'icon-bootstrap.php') {   ?>
<!-- Bootstrap Icon CSS -->
<link rel="stylesheet" href="assets/plugins/icons/bootstrap/bootstrap-icons.min.css">
<?php }?>

<?php  if ($page == 'icon-flag.php') {   ?>
<!-- Flag CSS -->
<link rel="stylesheet" href="assets/plugins/icons/flags/flags.css">
<?php }?>

<?php  if ($page == 'icon-ionic.php') {   ?>
<!-- Ionic CSS -->
<link rel="stylesheet" href="assets/plugins/icons/ionic/ionicons.css">
<?php }?>	

<?php  if ($page == 'icon-material.php') {   ?>
<!-- Material CSS -->
<link rel="stylesheet" href="assets/plugins/material/materialdesignicons.css">
<?php }?>

<?php  if ($page == 'icon-pe7.php') {   ?>
<!-- Pe7 CSS -->
<link rel="stylesheet" href="assets/plugins/icons/pe7/pe-icon-7.css">
<?php }?>		

<?php  if ($page == 'icon-remix.php') {   ?>
<!-- Remix Icon CSS -->
<link rel="stylesheet" href="assets/plugins/icons/remix/remixicon.css">
<?php }?>

<?php  if ($page == 'icon-simpleline.php') {   ?>
<!-- Simpleline CSS -->
<link rel="stylesheet" href="assets/plugins/simpleline/simple-line-icons.css">
<?php }?>		

<?php  if ($page == 'icon-themify.php') {   ?>
<!-- Themify CSS -->
<link rel="stylesheet" href="assets/plugins/icons/themify/themify.css">
<?php }?>

<?php  if ($page == 'icon-typicon.php') {   ?>
<!-- Typicon CSS -->
<link rel="stylesheet" href="assets/plugins/icons/typicons/typicons.css">
<?php }?>		

<?php  if ($page == 'icon-weather.php') {   ?>
<!-- Weather CSS -->
<link rel="stylesheet" href="assets/plugins/icons/weather/weathericons.css">
<?php }?>	

<!-- Datatable CSS -->
<link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">

<!-- Simplebar CSS -->
<link rel="stylesheet" href="assets/plugins/simplebar/simplebar.min.css">

<?php  if ($page == 'add-blog.php'||$page == 'admin-dashboard.php'||$page == 'blog-tags.php'||$page == 'cronjob.php'||$page == 'customer-dashboard.php'||$page == 'edit-blog.php'||$page == 'email-reply.php'||$page == 'email-templates.php'||$page == 'email.php'||$page == 'localization-settings.php'||$page == 'maintenance-mode.php'||$page == 'pages.php'||$page == 'preference-settings.php'||$page == 'prefixes-settings.php'||$page == 'seo-setup.php'||$page == 'sitemap.php'||$page == 'sms-gateways.php'||$page == 'storage.php'||$page == 'super-admin-dashboard.php'||$page == 'system-backup.php'||$page == 'system-update.php'||$page == 'tax-rates.php') {   ?>	
<!-- Bootstrap Tagsinput CSS -->
<link rel="stylesheet" href="assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css">
<?php }?>

<?php  if ($page == 'contact-messages.php'||$page == 'security-settings.php'||$page == 'suppliers.php'||$page == 'users.php') {   ?>		
<!-- intltelinput CSS -->
<link rel="stylesheet" href="assets/plugins/intltelinput/css/intlTelInput.min.css">
<?php }?>

<?php  if ($page == 'add-blog.php'||$page == 'add-product.php'||$page == 'bank-accounts-settings.php'||$page == 'edit-blog.php'||$page == 'edit-product.php'||$page == 'file-manager.php'||$page == 'form-editors.php'||$page == 'invoice-settings.php'||$page == 'invoice-templates-settings.php'||$page == 'invoice-templates.php'||$page == 'maintenance-mode.php'||$page == 'pages.php'||$page == 'payment-methods.php'||$page == 'sass-settings.php'||$page == 'seo-setup.php'||$page == 'tax-rates.php'||$page == 'thermal-printer.php'||$page == 'todo-list.php'||$page == 'todo.php') {   ?>
<!-- Quill CSS -->
<link rel="stylesheet" href="assets/plugins/quill/quill.snow.css">
<?php }?>

<?php  if ($page == 'gdpr-cookies.php') {   ?>
<!-- Summernote CSS -->
<link rel="stylesheet" href="assets/plugins/summernote/summernote-lite.min.css">
<?php }?>

<?php  if ($page == 'email-reply.php'||$page == 'file-manager.php'||$page == 'gallery.php'||$page == 'search-list.php'||$page == 'social-feed.php') {   ?>	
<!-- Fancybox CSS -->
<link rel="stylesheet" href="assets/plugins/fancybox/jquery.fancybox.min.css">
<?php }?>

<?php  if ($page == 'account-statement.php'||$page == 'add-credit-notes.php'||$page == 'balance-sheet.php'||$page == 'bank-accounts-settings.php'||$page == 'bank-accounts.php'||$page == 'best-seller.php'||$page == 'cash-flow.php'||$page == 'credit-notes.php'||$page == 'customer-due-report.php'||$page == 'customer-invoice-report.php'||$page == 'customer-payment-summary.php'||$page == 'customer-recurring-invoices.php'||$page == 'customer-transactions.php'||$page == 'customers-report.php'||$page == 'customers.php'||$page == 'debit-notes.php'||$page == 'edit-credit-notes.php'||$page == 'expense-report.php'||$page == 'expenses.php'||$page == 'income-report.php'||$page == 'incomes.php'||$page == 'inventory-report.php'||$page == 'low-stock.php'||$page == 'membership-addons.php'||$page == 'membership-transactions.php'||$page == 'money-transfer.php'||$page == 'payment-summary.php'||$page == 'payments.php'||$page == 'profit-loss-report.php'||$page == 'purchase-order-report.php'||$page == 'purchase-orders-report.php'||$page == 'purchase-return-report.php'||$page == 'purchases-report.php'||$page == 'recurring-invoices.php'||$page == 'sales-orders.php'||$page == 'sales-report.php'||$page == 'sales-returns.php'||$page == 'sold-stock.php'||$page == 'stock-history.php'||$page == 'stock-summary.php'||$page == 'supplier-payments.php'||$page == 'supplier-report.php'||$page == 'suppliers.php'||$page == 'tax-report.php'||$page == 'transactions.php'||$page == 'trial-balance.php'||$page == 'ui-rangeslider.php') {   ?>	
<!-- Rangeslider CSS -->
<link rel="stylesheet" href="assets/plugins/ion-rangeslider/css/ion.rangeSlider.css">
<link rel="stylesheet" href="assets/plugins/ion-rangeslider/css/ion.rangeSlider.min.css">
<?php }?>

<?php  if ($page == 'file-manager.php'||$page == 'notes.php'||$page == 'social-feed.php') {   ?>
<!-- Owl Carousel -->
<link rel="stylesheet" href="assets/css/owl.carousel.min.css">
<?php }?>

<?php  if ($page == 'file-manager.php') {   ?>
<!-- Player CSS -->
<link rel="stylesheet" href="assets/css/plyr.css">
<?php }?>

<?php  if ($page == 'chart-c3.php') {   ?>
<!-- ChartC3 CSS -->
<link rel="stylesheet" href="assets/plugins/c3-chart/c3.min.css">
<?php }?>

<?php  if ($page == 'chart-morris.php') {   ?>
<!-- Morris CSS -->
<link rel="stylesheet" href="assets/plugins/morris/morris.css">	
<?php }?>

<?php  if ($page == 'form-pickers.php' || $page == 'maps-leaflet.php' || $page == 'ui-sortable.php') {   ?>
<!-- Dragula CSS -->
<link rel="stylesheet" href="assets/plugins/dragula/css/dragula.min.css">
<?php }?>

<?php  if ($page == 'form-editors.php') {   ?>
<!-- Quill css -->
<link href="assets/plugins/quill/quill.core.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/quill/quill.snow.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/quill/quill.bubble.css" rel="stylesheet" type="text/css" />
<?php }?>

<?php  if ($page == 'form-pickers.php' || $page == 'form-range-slider.php') {   ?>
<!-- Vendor css -->
<link href="assets/css/vendor.min.css" rel="stylesheet">
<?php }?>

<?php  if ($page == 'form-range-slider.php') {   ?>
<!-- nouisliderribute css -->
<link rel="stylesheet" href="assets/plugins/nouislider/nouislider.min.css">
<?php }?>

<?php  if ($page == 'form-select2.php') {   ?>
<!-- Custom Select2 CSS -->
<link rel="stylesheet" href="assets/plugins/choices.js/public/assets/styles/choices.min.css">
<?php }?>

<?php  if ($page == 'form-wizard.php') {   ?>
<!-- Wizard CSS -->
<link rel="stylesheet" href="assets/plugins/twitter-bootstrap-wizard/form-wizard.css">
<?php }?>

<?php  if ($page == 'maps-leaflet.php') {   ?>
<!-- Leaflet Maps CSS -->
<link rel="stylesheet" href="assets/plugins/leaflet/leaflet.css">
<?php }?>

<?php  if ($page == 'maps-vector.php') {   ?>
<!-- Jsvector Maps -->
<link rel="stylesheet" href="assets/plugins/jsvectormap/css/jsvectormap.min.css">
<?php }?>

<?php  if ($page == 'ui-lightbox.php') {   ?>
<!-- Glightbox CSS -->
<link rel="stylesheet" href="assets/plugins/lightbox/glightbox.min.css">	
<?php }?>

<?php  if ($page == 'ui-sortable.php') {   ?>
<link rel="stylesheet" href="assets/plugins/swiper/swiper-bundle.min.css">
<?php }?>

<?php  if ($page == 'ui-toasts.php') {   ?>
<!-- Toatr CSS -->
<link rel="stylesheet" href="assets/plugins/toastr/toatr.css">	
<?php }?>

<!-- Select2 CSS -->
<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

<!-- Iconsax CSS -->
<link rel="stylesheet" href="assets/css/iconsax.css">

<!-- Main CSS -->
<link rel="stylesheet" href="assets/css/style.css">
	
