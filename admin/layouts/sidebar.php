<?php 
include "function.php";

// Fetch user role ID from session
$user_id = $_SESSION['crm_user_id'] ?? 0;
$role_id = 0;
$role_name = '';

if ($user_id > 0) {
    $login_result = mysqli_query($conn, "SELECT role_id FROM login WHERE id = '$user_id'");
    if ($login_row = mysqli_fetch_assoc($login_result)) {
        $role_id = $login_row['role_id'];
        $role_result = mysqli_query($conn, "SELECT name FROM user_role WHERE id = '$role_id'");
        $role_data = mysqli_fetch_assoc($role_result);
        $role_name = strtolower($role_data['name']??'');
    }
}
?>

<div class="two-col-sidebar" id="two-col-sidebar">
   
	<div class="twocol-mini">

		<!-- Add -->
		<div class="dropdown">
			<a class="btn btn-primary bg-gradient btn-sm btn-icon rounded-circle d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" href="javascript:void(0);" role="button" data-bs-display="static" data-bs-reference="parent">
				<i class="isax isax-add"></i>
			</a>
			<ul class="dropdown-menu dropdown-menu-start">
                 <?php if (check_is_access_new("invoice", $role_id)) { ?>
				<li>
                                <a href="add-invoice.php" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-receipt-item5"></i>&nbsp;Invoice
                                </a>
                            </li>
                    <?php } ?>
  <?php if (check_is_access_new("expense", $role_id)) { ?>
                            <li>
                                <a href="add-expense.php" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-money-send5"></i>&nbsp;Expenses
                                </a>
                            </li>
                         <?php } ?>
                        <?php if (check_is_access_new("quotation", $role_id)) { ?>
                            <li>
                                <a href="add-quotation.php" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-strongbox5"></i>&nbsp;Quotation
                                </a>
                            </li>
                        <?php } ?>
                         <?php if (check_is_access_new("product", $role_id)) { ?>
                             <li>
                                <a href="add-product.php" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-box5"></i>&nbsp;Product
                                </a>
                            </li>
                       <?php } ?>
                        <?php if (check_is_access_new("client", $role_id)) { ?>
                             <li>
                                <a href="add-customer.php" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-profile-2user5"></i>&nbsp;Client
                                </a>
                            </li>
                             <?php } ?>

                              <?php if (check_is_access_new("project", $role_id)) { ?>
                            <li>
                                <a href="add-projects.php" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-diagram"></i>&nbsp;Project
                                </a>
                            </li>
                          <?php } ?>
                         <?php if (check_is_access_new("user", $role_id)) { ?>
                            <li>
                                <a href="users.php?open=add_user" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-profile-2user5"></i>&nbsp;Users
                                </a>
                            </li>
                         <?php } ?>
                         <?php if (check_is_access_new("tax", $role_id)) { ?>

                            <li>
                                <a href="tax-rates.php?open=add_tax_rates" class="dropdown-item d-flex align-items-center">
                                    <i class="isax isax-receipt-text"></i>&nbsp;Tax-Rates

                                </a>
                            </li>
                            <?php } ?>
                             <li>
                                <a href="bank.php?open=add_bank_modal" class="dropdown-item d-flex align-items-center">
                                   <i class="isax isax-building"></i>&nbsp;Bank
                                </a>
                            </li>
			</ul>
		</div>
	
	</div>

    <div class="sidebar" id="sidebar-two">
        <?php

// Fetch logo from profile_info
$logoQuery = "SELECT company_logo FROM company_info WHERE is_deleted = 0 LIMIT 1";
$logoResult = mysqli_query($conn, $logoQuery);
$logoRow = mysqli_fetch_assoc($logoResult);

// Use default if not set
$logo = !empty($logoRow['company_logo']) ? '../uploads/' . $logoRow['company_logo'] : 'assets/img/logocrm.png';
?>
<div class="sidebar-logo">
    <a href="admin-dashboard.php" class="logo logo-normal">
        <img src="<?php echo $logo; ?>" alt="Logo" style="max-height:40px;">
    </a>
    <a href="admin-dashboard.php" class="logo-small">
        <img src="<?php echo $logo; ?>" alt="Logo">
    </a>
    <a id="toggle_btn" href="javascript:void(0);">
        <i class="isax isax-menu-1"></i>
    </a>
</div>


        <div class="sidebar-search">
            <div class="input-icon-end position-relative">
                <input type="text" class="form-control" placeholder="Search">
                <span class="input-icon-addon"><i class="isax isax-search-normal"></i></span>
            </div>
        </div>

        <div class="sidebar-inner" data-simplebar>
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="menu-title"><span>Main</span></li>
                    <li>
                        <ul>
                            <?php
                            $dashboardUrl = '#';
                            if ($role_name === 'admin') {
                                $dashboardUrl = 'admin-dashboard.php';
                            } else {
                                $dashboardUrl = 'customer-dashboard.php';
                            }
                            ?>
                            <li>
                                <a href="<?= $dashboardUrl ?>">
                                    <i class="isax isax-element-45"></i><span>Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Inventory & Sales -->
                    <li class="menu-title"><span>Inventory & Sales</span></li>
                    <li>
                        <ul>

                           <?php if (check_is_access_new("client", $role_id)) { ?>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="<?= in_array($page, ['customers.php','edit-customer.php','add-customer.php','customer-details.php']) ? 'active subdrop' : '' ?>">
                                    <i class="isax isax-profile-2user5"></i><span>Clients</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="customers.php" class="<?= ($page == 'customers.php') ? 'active' : '' ?>">Clients</a></li>
                                </ul>
                            </li>
                            <?php } ?>
                            <?php if (check_is_access_new("product", $role_id)) { ?>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="<?= in_array($page, ['products.php','edit-product.php', 'category.php', 'units.php']) ? 'active subdrop' : '' ?>">
                                    <i class="isax isax-box5"></i><span>Product</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="products.php" class="<?= ($page == 'products.php') ? 'active' : '' ?>">Products</a></li>
                                    <li><a href="category.php" class="<?= ($page == 'category.php') ? 'active' : '' ?>">Category</a></li>
                                    <li><a href="units.php" class="<?= ($page == 'units.php') ? 'active' : '' ?>">Units</a></li>
                                </ul>
                            </li>
                            <?php } ?>
                              <?php if (check_is_access_new("quotation", $role_id)) { ?>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="<?= in_array($page, ['quotations.php','edit-quotation.php','add-quotation.php','view-quotation.php']) ? 'active subdrop' : '' ?>">
                                    <i class="isax isax-strongbox5"></i><span>Quotations</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="quotations.php" class="<?= ($page == 'quotations.php') ? 'active' : '' ?>">Quotations</a></li>
                                </ul>
                            </li>
                            <?php } ?>

                            <?php if (check_is_access_new("invoice", $role_id)) { ?>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="<?= in_array($page, ['invoices.php', 'add-invoice.php', 'edit-invoice.php','invoice-details.php', 'invoice-templates.php', 'recurring-invoices.php']) ? 'active subdrop' : '' ?>">
                                    <i class="isax isax-receipt-item5"></i><span>Invoices</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="invoices.php" class="<?= ($page == 'invoices.php') ? 'active' : '' ?>">Invoices</a></li>
                                    <li><a href="add-invoice.php" class="<?= ($page == 'add-invoice.php') ? 'active' : '' ?>">Create Invoice</a></li>
                                    <!-- <li><a href="invoice-templates.php" class="<?= ($page == 'invoice-templates.php') ? 'active' : '' ?>">Invoice Templates</a></li>
                                    <li><a href="recurring-invoices.php" class="<?= ($page == 'recurring-invoices.php') ? 'active' : '' ?>">Recurring Invoices</a></li> -->
                                </ul>
                            </li>
                            <?php } ?>

                 
                          

                         

                            <!-- <?php if (check_is_access_new("project", $role_id)) { ?>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="<?= in_array($page, ['projects.php','add-projects','edit-project.php','project-details.php']) ? 'active subdrop' : '' ?>">
                                    <i class="isax isax-diagram"></i><span>Projects</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="projects.php" class="<?= ($page == 'projects.php') ? 'active' : '' ?>">Projects</a></li>
                                </ul>
                            </li>
                            <?php } ?>
                        </ul>
                    </li> -->
                    <?php if (check_is_access_new("project", $role_id)) { ?>
    <li class="submenu">
        <a href="javascript:void(0);" class="<?= in_array($page, ['projects.php','add-projects','edit-project.php','project-details.php','project-tasks.php','add-project-task.php','edit-project-task.php']) ? 'active subdrop' : '' ?>">
            <i class="isax isax-diagram"></i><span>Projects</span><span class="menu-arrow"></span>
        </a>
        <ul>
            <li><a href="projects.php" class="<?= ($page == 'projects.php') ? 'active' : '' ?>">Projects</a></li>
            <li><a href="project-tasks.php" class="<?= ($page == 'project-tasks.php') ? 'active' : '' ?>">Tasks</a></li>
        </ul>
    </li>
<?php } ?>

                    <!-- Finance & Accounts -->
                    <?php if (check_is_access_new("expense", $role_id)) { ?>
                    <li class="menu-title"><span>Finance & Accounts</span></li>
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="<?= in_array($page, ['expense.php','add-expense.php','expense-details.php','edit-expense.php']) ? 'active subdrop' : '' ?>">
                                    <i class="isax isax-money-send5"></i><span>Expenses</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="expense.php" class="<?= ($page == 'expense.php') ? 'active' : '' ?>">Expenses</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- Manage Users -->
                    <?php if (check_is_access_new("user", $role_id)) { ?>
                    <li class="menu-title"><span>Manage</span></li>
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="<?= in_array($page, ['users.php', 'roles-permissions.php', 'permission.php']) ? 'active subdrop' : '' ?>">
                                    <i class="isax isax-profile-2user5"></i><span>Manage Users</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="users.php" class="<?= ($page == 'users.php') ? 'active' : '' ?>">Users</a></li>
                                    <li><a href="roles-permissions.php" class="<?= ($page == 'roles-permissions.php' || $page == 'permission.php') ? 'active' : '' ?>">Roles & Permissions</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- Settings -->
                    <?php if (check_is_access_new("tax", $role_id)) { ?>
                    <li class="menu-title"><span>Settings</span></li>
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="<?= in_array($page, ['tax-rates.php', 'bank.php', 'company-settings.php']) ? 'active subdrop' : '' ?>">
                                    <i class="isax isax-profile-2user5"></i><span>General Settings</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="tax-rates.php" class="<?= ($page == 'tax-rates.php') ? 'active' : '' ?>">Tax Rates</a></li>
                                    <li><a href="bank.php" class="<?= ($page == 'bank.php') ? 'active' : '' ?>">Bank</a></li>
                                    <li><a href="company-settings.php" class="<?= ($page == 'company-settings.php') ? 'active' : '' ?>">Company Profile</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- Logout -->
                    <li>
                        <ul>
                            <li><a href="logout.php" class="<?= ($page == 'logout.php') ? 'active' : '' ?>"><i class="isax isax-logout"></i><span>Logout</span></a></li>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</div>
