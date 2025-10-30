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

        <!-- Start Invoice -->
        <div class="content p-4">

            <!-- start row -->
			<div class="row">
				<div class="col-md-10 mx-auto">
                    <div class="d-flex align-items-center justify-content-between border-bottom flex-wrap row-gap-3 mb-3 pb-3">
                        <div class="">
                            <p class="mb-1">Original For Recipient</p>
                            <h6 class="text-primary mb-2">TAX INVOICE</h6>
                            <div>
                                <h6 class="mb-1">Dreamguys Internet Pvt Ltd.,</h6>
                                <div> 
                                    <p class="mb-1">Address : <span>15 Hodges Mews, High Wycombe HP12 3JL, United Kingdom.</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <div class="mb-1 text-end"><img src="assets/img/invoice-logo.svg" alt="User Img"></div>
                            <p class="mb-1 text-end">Date: <span class="text-dark">05/12/2024</span></p>
                            <div class="inv-details">
                                <div class="inv-date-rest">
                                    <p class="text-start text-white">Invoice No: <span>INV 000500</span></p>                                
                                </div>
                                <div class="triangle-right"></div>                            
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <p class="mb-1">Customer Info :</p>
                            <h6 class="mb-1 fs-16">John Williams</h6>
                        </div>                
                    </div>

                    <!-- start row -->
                    <div class="row mb-3">
                        <div class="col-6 md-12 d-flex align-items-center justify-content-between">
                            <p class="mb-1">Client ID :</p>
                            <span class="mb-1 fs-13 fw-noraml text-dark">AS2534568</span>
                        </div><!-- end col -->                
                        <div class="col-6 md-12 d-flex align-items-center justify-content-between">
                            <p class="mb-1">Outstanding Balance :</p>
                            <span class="mb-1 fs-13 fw-noraml text-dark">$3600</span>
                        </div><!-- end col -->                
                        <div class="col-6 md-12 d-flex align-items-center justify-content-between">
                            <p class="mb-1">Invoice Date : </p>
                            <span class="mb-1 fs-13 fw-noraml text-dark">Johan Smith</span>
                        </div><!-- end col -->                
                        <div class="col-6 md-12 d-flex align-items-center justify-content-between">
                            <p class="mb-1">Due Date :</p>
                            <span class="mb-1 fs-13 fw-noraml text-dark">Winter</span>
                        </div><!-- end col -->                
                        <div class="col-6 md-12 d-flex align-items-center justify-content-between">
                            <p class="mb-1">Total Curent Charges :</p>
                            <span class="mb-1 fs-13 fw-noraml text-dark">SI2534687</span>
                        </div><!-- end col -->                
                        <div class="col-6 md-12 d-flex align-items-center justify-content-between">
                            <p class="mb-1">Total Balance Due :</p>
                            <span class="mb-1 fs-13 fw-noraml text-dark">2024 Spring</span>
                        </div><!-- end col -->                
                    </div>
                    <!-- end row -->

                    <!-- start row -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <table class="table">
                                <thead class="thead-light border-top border-start-0 border-end-0 border-bottom border-3 border-dark p-2">
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Due Date</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-dark">
                                        <td class="text-dark">1</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark">Additional monthly usages - 125 GB</span>
                                            </div>
                                        </td>
                                        <td><span class="text-dark">01Jan 2024 To 31 Jan 2024</span></td>
                                        <td class="text-end"><span class="text-dark">$350</span></td>
                                    </tr>
                                    <tr class="border-dark">
                                        <td class="text-dark">2</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark">Equpment rental</span>
                                            </div>
                                        </td>
                                        <td><span class="text-dark">01Jan 2024 To 31 Jan 2024</span></td>
                                        <td class="text-end"><span class="text-dark">$600</span></td>
                                    </tr>
                                    <tr class="border-dark">
                                        <td class="text-dark">3</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark">Xtreme5</span>
                                            </div>
                                        </td>
                                        <td><span class="text-dark">01Jan 2024 To 31 Jan 2024</span></td>
                                        <td class="text-end"><span class="text-dark">$400</span></td>
                                    </tr>
                                    <tr class="border-dark">
                                        <td class="text-dark">4</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark">Govement fee & taxes</span>
                                            </div>
                                        </td>
                                        <td><span class="text-dark">01Jan 2024 To 31 Jan 2024</span></td>
                                        <td class="text-end"><span class="text-dark">$300</span></td>
                                    </tr>
                                    <tr class="border-dark">
                                        <td class="text-dark">4</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark">Monthly services</span>
                                            </div>
                                        </td>
                                        <td><span class="text-dark">01Jan 2024 To 31 Jan 2024</span></td>
                                        <td class="text-end"><span class="text-dark">$300</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->

                    <!-- start row -->
                    <div class="row mb-2">
                        <div class="col-md-8">
                            
                        </div><!-- end col -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-center justify-content-between ">
                                <div class="d-flex flex-column">
                                    <span class="text-dark text-end fw-semibold mb-1">Taxable Amount</span>
                                    <span class="text-dark text-end fw-semibold">IGST 18.0%</span>
                                </div>
                                <div class="d-flex flex-column text-end">
                                    <span class="text-dark fw-semibold mb-1">$1650.00</span>
                                    <span class="text-dark fw-semibold">$165.00</span>
                                </div>
                            </div>
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->

                    <!-- start row -->
                    <div class="row border-top border-bottom border-3 border-dark p-3 align-items-center">
                        <div class="col-md-8">
                            <span class="text-dark">Total Items / Qty : 5 / 5.00</span>
                        </div><!-- end col -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class=" text-end">
                                    <span class="fw-bold fs-18 text-end text-dark">Total</span>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold fs-18 text-dark">$1,815.00</span>
                                </div>
                            </div>
                        </div><!-- end col -->                
                    </div>
                    <!-- end row -->

                    <!-- start row -->
                    <div class="row py-3 border-bottom  border-bottom border-3 border-dark mb-3 d-flex align-items-center">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center justify-content-center">
                                <p class="text-gary">Total amount ( in words):<span class="text-dark"> One Thousand Eight Hundred Fifteen Dollars Only.</span></p>
                            </div>
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="d-flex align-items-center flex-wrap border-bottom mb-3">
                        <div class="mb-3">
                            <h6 class="mb-2">Payment Info:</h6>
                            <div>
                                <p class="mb-1">Debit Card :  <span class="text-dark">465 *************645</span></p>
                                <p class="mb-1">Amount :  <span class="text-dark">$1,815</span></p>
                            </div>
                        </div>                      
                    </div>

                    <!-- start row -->
                    <div class="row border border-start-0 border-end-0 border-dark text-center text-white bg-light p-2">
                        <div class="col-md-12">
                            <p class="text-gray">Thanks for your Business</p>
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->

                </div><!-- end col -->
            </div>
            <!-- end row -->

        </div>
        <!-- End Invoice -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>

</body>

</html>    