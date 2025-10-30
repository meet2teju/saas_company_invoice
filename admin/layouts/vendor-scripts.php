<?php
$link = $_SERVER[ 'PHP_SELF' ];
$link_array = explode( '/', $link );
$page = end( $link_array );
?>

<!-- jQuery -->
<script src="assets/js/jquery-3.7.1.min.js"></script>

<!-- Bootstrap Core JS -->
<script src="assets/js/bootstrap.bundle.min.js"></script> 

<!-- Simplebar JS -->
<script src="assets/plugins/simplebar/simplebar.min.js"></script>

<!-- Daterangepikcer JS -->
<script src="assets/js/moment.min.js"></script>
<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>	

<?php  if ($page == 'account-settings.php'||$page == 'add-credit-notes.php'||$page == 'add-debit-notes.php'||$page == 'add-delivery-challan.php'||$page == 'add-invoice.php'||$page == 'add-purchases-orders.php'||$page == 'add-purchases.php'||$page == 'add-quotation.php'||$page == 'calendar.php'||$page == 'companies.php'||$page == 'customer-account-settings.php'||$page == 'customer-add-quotation.php'||$page == 'customer-invoice-details.php'||$page == 'customer-invoices.php'||$page == 'customer-plans-settings.php'||$page == 'customers.php'||$page == 'edit-credit-notes.php'||$page == 'edit-debit-notes.php'||$page == 'edit-delivery-challan.php'||$page == 'edit-invoice.php'||$page == 'edit-purchases-orders.php'||$page == 'edit-purchases.php'||$page == 'edit-quotation.php'||$page == 'expenses.php'||$page == 'incomes.php'||$page == 'index.php'||$page == 'invoice.php'||$page == 'layout-default'||$page == 'layout-dark.php'||$page == 'layout-mini.php'||$page == 'layout-rtl.php'||$page == 'layout-single.php'||$page == 'layout-transparent.php'||$page == 'layout-without-header.php'||$page == 'membership-addons.php'||$page == 'notes.php'||$page == 'payments.php'||$page == 'plans-billings.php'||$page == 'profile.php'||$page == 'security-settings.php'||$page == 'super-admin-dashboard.php'||$page == 'supplier-payments.php'||$page == 'suppliers.php'||$page == 'ticket-kanban.php'||$page == 'tickets-list.php'||$page == 'tickets.php'||$page == 'todo-list.php'||$page == 'todo.php') {   ?>	
<!-- Datetimepicker JS -->
<script src="assets/js/bootstrap-datetimepicker.min.js"></script>
<?php }?>	

<?php  if ($page == 'add-blog.php'||$page == 'admin-dashboard.php'||$page == 'blog-tags.php'||$page == 'cronjob.php'||$page == 'customer-dashboard.php'||$page == 'edit-blog.php'||$page == 'email-reply.php'||$page == 'email-templates.php'||$page == 'email.php'||$page == 'localization-settings.php'||$page == 'maintenance-mode.php'||$page == 'pages.php'||$page == 'preference-settings.php'||$page == 'prefixes-settings.php'||$page == 'seo-setup.php'||$page == 'sitemap.php'||$page == 'sms-gateways.php'||$page == 'storage.php'||$page == 'super-admin-dashboard.php'||$page == 'system-backup.php'||$page == 'system-update.php'||$page == 'tax-rates.php') {   ?>	
<!-- Bootstrap Tagsinput JS -->
<script src="assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
<?php }?>

<?php  if ($page == 'contact-messages.php'||$page == 'security-settings.php'||$page == 'suppliers.php'||$page == 'users.php') {   ?>		
<!-- intel Input -->
<script src="assets/plugins/intltelinput/js/intlTelInput.js"></script>
<?php }?>

<?php  if ($page == 'add-blog.php'||$page == 'add-product.php'||$page == 'bank-accounts-settings.php'||$page == 'edit-blog.php'||$page == 'edit-product.php'||$page == 'file-manager.php'||$page == 'form-editors.php'||$page == 'invoice-settings.php'||$page == 'invoice-templates-settings.php'||$page == 'invoice-templates.php'||$page == 'maintenance-mode.php'||$page == 'pages.php'||$page == 'payment-methods.php'||$page == 'sass-settings.php'||$page == 'seo-setup.php'||$page == 'tax-rates.php'||$page == 'thermal-printer.php'||$page == 'todo-list.php'||$page == 'todo.php') {   ?>
<!-- Quill JS -->
<script src="assets/plugins/quill/quill.min.js"></script>
<?php }?>

<?php  if ($page == 'gdpr-cookies.php') {   ?>
<!-- Summernote JS -->
<script src="assets/plugins/summernote/summernote-lite.min.js"></script>
<?php }?>

<?php  if ($page == 'email-reply.php'||$page == 'file-manager.php'||$page == 'gallery.php'||$page == 'search-list.php'||$page == 'social-feed.php') {   ?>	
<!-- Fancybox JS -->
<script src="assets/plugins/fancybox/jquery.fancybox.min.js"></script>
<?php }?>

<?php  if ($page == 'account-statement.php'||$page == 'add-credit-notes.php'||$page == 'balance-sheet.php'||$page == 'bank-accounts-settings.php'||$page == 'bank-accounts.php'||$page == 'best-seller.php'||$page == 'cash-flow.php'||$page == 'credit-notes.php'||$page == 'customer-due-report.php'||$page == 'customer-invoice-report.php'||$page == 'customer-payment-summary.php'||$page == 'customer-recurring-invoices.php'||$page == 'customer-transactions.php'||$page == 'customers-report.php'||$page == 'customers.php'||$page == 'debit-notes.php'||$page == 'edit-credit-notes.php'||$page == 'expense-report.php'||$page == 'expenses.php'||$page == 'income-report.php'||$page == 'incomes.php'||$page == 'inventory-report.php'||$page == 'low-stock.php'||$page == 'membership-addons.php'||$page == 'membership-transactions.php'||$page == 'money-transfer.php'||$page == 'payment-summary.php'||$page == 'payments.php'||$page == 'profit-loss-report.php'||$page == 'purchase-order-report.php'||$page == 'purchase-orders-report.php'||$page == 'purchase-return-report.php'||$page == 'purchases-report.php'||$page == 'recurring-invoices.php'||$page == 'sales-orders.php'||$page == 'sales-report.php'||$page == 'sales-returns.php'||$page == 'sold-stock.php'||$page == 'stock-history.php'||$page == 'stock-summary.php'||$page == 'supplier-payments.php'||$page == 'supplier-report.php'||$page == 'suppliers.php'||$page == 'tax-report.php'||$page == 'transactions.php'||$page == 'trial-balance.php'||$page == 'ui-rangeslider.php') {   ?>	
<!-- Rangeslider JS -->
<script src="assets/plugins/ion-rangeslider/js/ion.rangeSlider.js"></script>
<script src="assets/plugins/ion-rangeslider/js/custom-rangeslider.js"></script>
<script src="assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>
<?php }?>

<?php  if ($page == 'file-manager.php'||$page == 'notes.php'||$page == 'social-feed.php') {   ?>
<!-- Owl Carousel -->
<script src="assets/js/owl.carousel.min.js"></script>
<?php }?>

<?php  if ($page == 'file-manager.php') {   ?>
<!-- Player JS -->
<script src="assets/js/plyr-js.js"></script>
<?php }?>

<?php  if ($page == 'calendar.php'||$page == 'email-reply.php'||$page == 'email.php'||$page == 'todo-list.php'||$page == 'todo.php') {   ?>
<!-- Fullcalendar JS -->
<script src="assets/plugins/fullcalendar/index.global.min.js"></script>
<script src="assets/plugins/fullcalendar/calendar-data.js"></script>
<?php }?>

<?php  if ($page == 'account-statement.php'||$page == 'chat-peity.php'||$page == 'subscriptions.php'||$page == 'kanban-view.php'||$page == 'ticket-kanban.php') {   ?>
<!-- Drag Card -->
<script src="assets/js/jquery-ui.min.js"></script>
<script src="assets/js/jquery.ui.touch-punch.min.js"></script>
<?php }?>

<?php  if ($page == 'chat.php' || $page == 'email-reply.php' || $page == 'email.php' || $page == 'video-call.php') {   ?>
<!-- Slimscroll JS -->
<script src="assets/js/jquery.slimscroll.min.js"></script>	
<?php }?>

<?php  if ($page == 'form-editors.php') {   ?>
<!-- Quill Demo js -->
<script src="assets/js/form-quilljs.js"></script>
<?php }?>

<?php  if ($page == 'form-fileupload.php') {   ?>
<!-- Dropzone File Js -->
<script src="assets/plugins/dropzone/dropzone-min.js"></script>

<!-- File Upload Demo js -->
<script src="assets/js/form-fileupload.js"></script>
<?php }?>

<?php  if ($page == 'form-mask.php') {   ?>
<!-- Mask JS -->
<script src="assets/js/jquery.maskedinput.min.js"></script>
<script src="assets/js/mask.js"></script>
<?php }?>

<?php  if ($page == 'form-pickers.php') {   ?>
<!-- Vendor js -->
<script src="assets/js/vendor.min.js"></script>
<?php }?>	

<?php  if ($page == 'form-pickers.php' || $page == 'form-select2.php' || $page == 'ui-popovers.php') {   ?>
<!-- App js -->
<script src="assets/js/app.js"></script>
<?php }?>	

<?php  if ($page == 'form-range-slider.php') {   ?>
<!-- noUiSlider js -->
<script src="assets/plugins/nouislider/nouislider.min.js"></script>
<script src="assets/plugins/wnumb/wNumb.min.js"></script>

<!-- Plugins only -->
<script src="assets/js/extended-range-slider.js"></script>
<?php }?>	

<?php  if ($page == 'form-select2.php') {   ?>
<script src="assets/plugins/choices.js/public/assets/scripts/choices.min.js"></script>
<?php }?>

<?php  if ($page == 'form-validation.php') {   ?>
<script src="assets/js/form-validation.js"></script>
<?php }?>

<?php  if ($page == 'form-wizard.php') {   ?>
<!-- Wizrd JS -->
<script src="assets/plugins/vanilla-wizard/js/wizard.min.js"></script>	

<!-- Wizard JS -->
<script src="assets/js/form-wizard.js"></script>
<?php }?>

<?php  if ($page == 'admin-dashboard.php'||$page == 'chart-apex.php'||$page == 'customer-dashboard.php'||$page == 'email-templates.php'||$page == 'expense-report.php'||$page == 'file-manager.php'||$page == 'form-elements.php'||$page == 'index.php'||$page == 'layout-dark.php'||$page == 'layout-default.php'||$page == 'layout-mini.php'||$page == 'layout-rtl.php'||$page == 'layout-single.php'||$page == 'layout-transparent.php'||$page == 'layout-without-header.php'||$page == 'payment-summary.php'||$page == 'purchase-orders-report.php'||$page == 'purchase-return-report.php'||$page == 'purchases-report.php'||$page == 'sales-returns.php'||$page == 'sitemap.php'||$page == 'sms-gateways.php'||$page == 'storage.php'||$page == 'super-admin-dashboard.php'||$page == 'system-backup.php'||$page == 'system-update.php'||$page == 'tax-report.php'||$page == 'trial-balance.php'||$page == 'ui-breadcrumb.php') {   ?>
<!-- Chart JS -->
<script src="assets/plugins/apexchart/apexcharts.min.js"></script>
<script src="assets/plugins/apexchart/chart-data.js"></script>	
<?php }?>

<?php  if ($page == 'chart-c3.php') {   ?>
<!-- Chart JS -->
<script src="assets/plugins/c3-chart/d3.v5.min.js"></script>
<script src="assets/plugins/c3-chart/c3.min.js"></script>
<script src="assets/plugins/c3-chart/chart-data.js"></script>
<?php }?>

<?php  if ($page == 'chart-flot.php') {   ?>
<!-- Chart JS -->
<script src="assets/plugins/flot/jquery.flot.js"></script>
<script src="assets/plugins/flot/jquery.flot.fillbetween.js"></script>
<script src="assets/plugins/flot/jquery.flot.pie.js"></script>
<script src="assets/plugins/flot/chart-data.js"></script>
<?php }?>

<?php  if ($page == 'admin-dashboard.php'||$page == 'chart-js.php'||$page == 'email-templates.php'||$page == 'sitemap.php'||$page == 'sms-gateways.php'||$page == 'storage.php'||$page == 'system-backup.php'||$page == 'system-update.php'||$page == 'tax-report.php') {   ?>
<!-- Chart JS -->
<script src="assets/plugins/chartjs/chart.min.js"></script>
<script src="assets/plugins/chartjs/chart-data.js"></script>	
<?php }?>

<?php  if ($page == 'chart-morris.php') {   ?>
<!-- Chart JS -->
<script src="assets/plugins/morris/raphael-min.js"></script>
<script src="assets/plugins/morris/morris.min.js"></script>
<script src="assets/plugins/morris/chart-data.js"></script>
<?php }?>

<?php  if ($page == 'account-statement.php'||$page == 'chart-peity.php'||$page == 'subscriptions.php') {   ?>
<!-- Chart JS -->
<script src="assets/plugins/peity/jquery.peity.min.js"></script>
<script src="assets/plugins/peity/chart-data.js"></script>
<?php }?>

<?php  if ($page == 'maps-leaflet.php') {   ?>
<!-- Leaflet Maps JS -->
<script src="assets/plugins/leaflet/leaflet.js"></script>
<script src="assets/js/leaflet.js"></script>
<?php }?>

<?php  if ($page == 'maps-vector.php') {   ?>
<script src="assets/plugins/jsvectormap/js/jsvectormap.min.js"></script>

<!-- JSVector Maps MapsJS -->
<script src="assets/plugins/jsvectormap/maps/world-merc.js"></script>
<script src="assets/js/us-merc-en.js"></script>
<script src="assets/js/russia.js"></script>
<script src="assets/js/spain.js"></script>
<script src="assets/js/canada.js"></script>
<script src="assets/js/jsvectormap.js"></script>	
<?php }?>

<?php  if ($page == 'ui-rating.php') {   ?>
<!-- Rater JS -->
<script src="assets/plugins/rater-js/index.js"></script>

<!-- Internal Ratings JS -->
<script src="assets/js/ratings.js"></script>	
<?php }?>

<?php  if ($page == 'ui-clipboard.php') {   ?>
<!-- Clipboard JS -->
<script src="assets/plugins/clipboard/clipboard.min.js"></script>

<script src="assets/js/clipboard.js"></script>	
<?php }?>

<?php  if ($page == 'ui-counter.php') {   ?>
<!-- counter JS -->
<script src="assets/plugins/countup/jquery.counterup.min.js"></script>
<script src="assets/plugins/countup/jquery.waypoints.min.js"></script>
<script src="assets/plugins/countup/jquery.missofis-countdown.js"></script>

<!-- Custom Counter JS -->
<script src="assets/js/counter.js"></script>
<?php }?>

<?php  if ($page == 'extended-dragula.php') {   ?>
<!-- Dragula js-->
<script src="assets/plugins/dragula/dragula.min.js"></script>

<!-- Dragula Demo Component js -->
<script src="assets/js/dragula.js"></script>
<?php }?>

<?php  if ($page == 'ui-lightbox.php') {   ?>
<!-- Lightbox JS -->
<script src="assets/plugins/lightbox/glightbox.min.js"></script>
<script src="assets/plugins/lightbox/lightbox.js"></script>	
<?php }?>

<?php  if ($page == 'ui-scrollbar.php' || $page == 'ui-scrollspy.php') {   ?>	
<!-- Plyr JS -->
<script src="assets/plugins/scrollbar/scrollbar.min.js"></script>
<script src="assets/plugins/scrollbar/custom-scroll.js"></script>
<?php }?>

<?php  if ($page == 'ui-sortable.php') {   ?>
<!-- Sortable JS -->
<script src="assets/plugins/sortablejs/Sortable.min.js"></script>

<!-- Internal Sortable JS -->
<script src="assets/js/sortable.js"></script>
<?php }?>

<?php  if ($page == 'ui-sweetalerts.php') {   ?>
<!-- Sweetalert 2 -->
<script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
<script src="assets/plugins/sweetalert/sweetalerts.min.js"></script>
<?php }?>

<?php  if ($page == 'ui-toasts.php') {   ?>
<!-- Toastr JS -->
<script src="assets/plugins/toastr/toastr.min.js"></script>
<script src="assets/plugins/toastr/toastr.js"></script>
<?php }?>

<?php  if ($page == 'ui-tooltips.php') {   ?>
<!-- Custom JS -->
<script src="assets/js/popover.js"></script>
<?php }?>

<!-- Datatable JS -->
<script src="assets/js/jquery.dataTables.min.js"></script>
<script src="assets/js/dataTables.bootstrap5.min.js"></script>

<!-- Select2 JS -->
<script src="assets/plugins/select2/js/select2.min.js"></script>

<?php  if ($page == 'chat.php') {   ?>
<!-- Custom - Chat JS -->
<script src="assets/js/chat.js"></script>
<?php }?>

<?php  if ($page == 'coming-soon.php') {   ?>
<!-- Custom - Coming soon JS -->
<script src="assets/js/coming-soon.js"></script>
<?php }?>

<?php  if ($page == 'email-reply.php' || $page == 'email.php' || $page == 'social-feed.php') {   ?>
<!-- Custom - Email JS -->
<script src="assets/js/email.js"></script>
<?php }?>

<?php  if ($page == 'file-manager.php') {   ?>
<!-- Custom - File Manager JS -->
<script src="assets/js/file-manager.js"></script>
<?php }?>

<?php  if ($page == 'kanban-view.php') {   ?>
<!-- Custom - Kanban JS -->
<script src="assets/js/kanban.js"></script>
<?php }?>	

<?php  if ($page == 'notes.php') {   ?>
<!-- Custom - Notes JS -->
<script src="assets/js/notes.js"></script>
<?php }?>	

<?php  if ($page == 'social-feed.php') {   ?>
<!-- Sticky Sidebar JS -->
<script src="assets/plugins/theia-sticky-sidebar/ResizeSensor.js"></script>
<script src="assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js"></script>

<script src="assets/js/social-feed.js"></script>	
<?php }?>	

<?php  if ($page == 'todo.php' || $page == 'todo-list.php') {   ?>
<!-- Custom - Todo JS -->
<script src="assets/js/todo.js"></script>
<?php }?>

<?php  if ($page == 'two-step-verification.php') {   ?>
<!-- Custom JS -->
<script src="assets/js/otp.js"></script>
<?php }?>

<!-- Custom JS -->
<script src="assets/js/script.js"></script>
