<?php include 'layouts/session.php'; ?>
<?php include '../config/config.php'; ?>
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
         
<?php if (isset($_SESSION['message'])): ?>
    <?php 
        $alertClass = ($_SESSION['message_type'] === 'success') ? 'alert-success' : 'alert-danger';
    ?>
    <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>

			<!-- Start Container  -->
			<div class="content content-two">



				<!-- Page Header -->
				<div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3">
					<div>
						<h6>Products</h6>
					</div>
					<div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                        <div class="table-search d-flex align-items-center mb-0">
                            <div class="search-input">
                                <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                            </div>
                        </div>
                        <a class="btn btn-outline-white fw-normal d-inline-flex align-items-center" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#customcanvas">
                            <i class="isax isax-filter me-1"></i>Filter
                        </a>
						<div class="dropdown">
							<a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center"  data-bs-toggle="dropdown">
								<i class="isax isax-export-1 me-1"></i>Export
							</a>
							<ul class="dropdown-menu">
								<li>
                                    <a class="dropdown-item" href="process/export_pdf_product.php?<?php echo http_build_query($_GET); ?>">Download as PDF</a>
								</li>
								<li>
                                    <a class="dropdown-item" href="process/export_excle_product.php?<?php echo http_build_query($_GET); ?>">Download as Excel</a>
                                </li>
							</ul>
						</div>
                        <?php if (check_is_access_new("add_product") == 1) { ?> 
                        <div>
							<a href="add-product.php" class="btn btn-primary d-flex align-items-center"><i class="isax isax-add-circle5 me-1"></i>New Product</a>
						</div>
                        <?php } ?>
					</div>
				</div>
				<!-- End Page Header -->
				
				<!-- Start Table Search -->
				<!-- Search & Actions -->
<div class="mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <!-- <div class="table-search d-flex align-items-center mb-0">
                <div class="search-input">
                    <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                </div>
            </div>
            <a class="btn btn-outline-white fw-normal d-inline-flex align-items-center" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#customcanvas">
                <i class="isax isax-filter me-1"></i>Filter
            </a> -->
            
            <!-- Display Active Filters -->
            <?php 
            $active_filters = [];
            
            // Product filters
            if (!empty($_GET['product'])) {
                $product_names = [];
                $selectedProducts = is_array($_GET['product']) ? $_GET['product'] : [$_GET['product']];
                $ids = implode(",", array_map('intval', $selectedProducts));
                $res = mysqli_query($conn, "SELECT name FROM product WHERE id IN ($ids)");
                while ($row = mysqli_fetch_assoc($res)) {
                    $product_names[] = htmlspecialchars($row['name']);
                }
                if (!empty($product_names)) {
                    $active_filters[] = "Product: " . (count($product_names) > 2 ? 
                        implode(", ", array_slice($product_names, 0, 2)) . " +" . (count($product_names) - 2) : 
                        implode(", ", $product_names));
                }
            }
            
            // Category filters
            if (!empty($_GET['category'])) {
                $category_names = [];
                $selectedCategories = is_array($_GET['category']) ? $_GET['category'] : [$_GET['category']];
                $ids = implode(",", array_map('intval', $selectedCategories));
                $res = mysqli_query($conn, "SELECT name FROM category WHERE id IN ($ids)");
                while ($row = mysqli_fetch_assoc($res)) {
                    $category_names[] = htmlspecialchars($row['name']);
                }
                if (!empty($category_names)) {
                    $active_filters[] = "Category: " . (count($category_names) > 2 ? 
                        implode(", ", array_slice($category_names, 0, 2)) . " +" . (count($category_names) - 2) : 
                        implode(", ", $category_names));
                }
            }
            
            // Unit filters
            if (!empty($_GET['unit'])) {
                $unit_names = [];
                $selectedUnits = is_array($_GET['unit']) ? $_GET['unit'] : [$_GET['unit']];
                $ids = implode(",", array_map('intval', $selectedUnits));
                $res = mysqli_query($conn, "SELECT name FROM units WHERE id IN ($ids)");
                while ($row = mysqli_fetch_assoc($res)) {
                    $unit_names[] = htmlspecialchars($row['name']);
                }
                if (!empty($unit_names)) {
                    $active_filters[] = "Unit: " . (count($unit_names) > 2 ? 
                        implode(", ", array_slice($unit_names, 0, 2)) . " +" . (count($unit_names) - 2) : 
                        implode(", ", $unit_names));
                }
            }
            
            // Status filter
            if (isset($_GET['status']) && $_GET['status'] !== '') {
                $status_text = ($_GET['status'] == '1') ? 'Active' : 'Inactive';
                $active_filters[] = "Status: " . $status_text;
            }
            ?>
            
            <!-- Display active filters and clear button -->
            <?php if (!empty($active_filters)): ?>
                <div class="d-flex align-items-center gap-2">
                    <!-- Active Filters Display -->
                    <div class="active-filters bg-light px-3 py-2 rounded d-flex align-items-center gap-2">
                        <small class="text-muted fw-bold">Active Filters:</small>
                        <?php foreach ($active_filters as $filter): ?>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                <?= $filter ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Clear Filter Button -->
                    <a href="products.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                    </a>
                </div>
            <?php endif; ?>

            <!-- Multiple Delete Button -->
            <a href="#" class="btn btn-outline-danger delete-multiple d-none">
                <i class="fa-regular fa-trash-can me-1"></i>Delete
            </a>
        </div>
    </div>
</div>
				<!-- End Table Search -->
				
				<!-- Start Table List -->
				<div class="table-responsive">
                <table class="table table-nowrap datatable">
                    <thead>
                    <tr>
                        <th class="no-sort">
                            <div class="form-check form-check-md">
                                <input class="form-check-input" type="checkbox" id="select-all">
                            </div>
                        </th>
                        <th >HSN Code</th>
                        <th >Product</th>
                        <th >Category</th>
                        <th >Unit</th>
                        <th>Quantity</th>
                        <th>Selling Price</th>
                        <th>Purchase Price</th>
                        <th class="no-sort">Status</th>
                        <th class="no-sort"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Get user role ID and user ID from session
                    $currentUserId = $_SESSION['crm_user_id'] ?? 0;
                    $userRoleId = $_SESSION['role_id'] ?? 0;
                    
                    $filterQuery = [];
                    if (!empty($_GET['product'])) {
                        $filterQuery[] = "p.id = " . intval($_GET['product']);
                    }
                    if (!empty($_GET['category'])) {
                        $filterQuery[] = "p.category_id = " . intval($_GET['category']);
                    }
                    if (!empty($_GET['unit'])) {
                        $filterQuery[] = "p.unit_id = " . intval($_GET['unit']);
                    }
                    if (isset($_GET['status']) && $_GET['status'] !== '') {
                        $filterQuery[] = "p.status = " . intval($_GET['status']);
                    }
                    
                    // Add user-specific filtering - ONLY ADDED THIS CONDITION
                    $whereClause = "WHERE p.is_deleted = 0";
                    if ($userRoleId != 1) {
                        // For non-admin users (role_id != 1), show only their own products
                        $whereClause .= " AND p.user_id = $currentUserId";
                    }
                    if (!empty($filterQuery)) {
                        $whereClause .= " AND " . implode(" AND ", $filterQuery);
                    }
                    
                    $query = "SELECT p.*, c.name AS category_name, u.name AS unit_name 
                        FROM product p 
                        LEFT JOIN category c ON p.category_id = c.id 
                        LEFT JOIN units u ON p.unit_id = u.id 
                        $whereClause 
                        ORDER BY p.id DESC";


                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                         $status = $row['status'] ? 'checked' : '';
                        $productId = $row['id'];
                        $productImg = !empty($row['product_img']) ? '../uploads/' . htmlspecialchars($row['product_img']) : 'assets/img/users/user-16.jpg';
                        ?>
                        <tr>
                            <td>
                                <div class="form-check form-check-md">
                            <input type="checkbox" class="form-check-input user-checkbox" name="product_ids[]" value="<?= htmlspecialchars($row['id']) ?>">
                                </div>
                            </td>
                            <td><a href="javascript:void(0);"><?= htmlspecialchars($row['code']) ?></a></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                        <img src="<?= $productImg ?>" onerror="this.src='assets/img/users/user-16.jpg';">
                                    </a>
                                    <div>
                                        <h6 class="fs-14 fw-medium mb-0"><?= htmlspecialchars($row['name']) ?></h6>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($row['category_name']) ?></td>
                            <td><?= htmlspecialchars($row['unit_name']) ?></td>
                            <td><?= htmlspecialchars($row['quantity']) ?></td>
                            <td class="text-dark">$ &nbsp;<?= htmlspecialchars($row['selling_price']) ?></td>
                            <td class="text-dark">$ &nbsp;<?= htmlspecialchars($row['purchase_price']) ?></td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input status-toggle" type="checkbox" role="switch" <?= $status ? 'checked' : '' ?> data-id="<?= $row['id'] ?>">
                                </div>
                            </td>
                           <td class="action-item">
    <div class="dropdown">
        <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown">
            <i class="isax isax-more"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <?php if (check_is_access_new("edit_product") == 1) { ?>
            <li>
                <a href="edit-product.php?id=<?= $productId ?>" class="dropdown-item d-flex align-items-center">
                    <i class="isax isax-edit me-2"></i>Edit
                </a>
            </li>
            <?php } ?>

            <?php if (check_is_access_new("delete_product") == 1) { ?>
            <li>
                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center delete-link" 
                   data-bs-toggle="modal" data-bs-target="#deleteModal<?= $productId ?>">
                    <i class="isax isax-trash me-2"></i>Delete
                </a>
            </li>
            <?php } ?>
        </ul>
    </div>
</td>
                        </tr>

                        <!-- Delete Modal -->
                       <!-- Delete Modal -->
<div class="modal fade" id="deleteModal<?= $productId ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-m">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="process/action_delete_product.php">
                <input type="hidden" name="id" value="<?= $productId ?>">
                <div class="modal-body text-center pt-0">
                    <div class="mb-3">
                        <img src="assets/img/icons/delete.svg" alt="img" width="60">
                    </div>
                    <h6 class="mb-1">Delete Product</h6>
                    <p class="mb-3">Are you sure you want to delete this product?</p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-white me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
                        <!-- End Delete Modal -->

                        <?php
                    } // end while
                    ?>
                    </tbody>
                </table>
            </div>
				
				<!-- End Table List -->

			</div>
			<!-- container  -->
			
			<?php include 'layouts/footer.php'; ?>

		</div>
        
		<!-- ========================
			End Page Content
		========================= -->

		<!-- Start Filter -->
		<div class="offcanvas offcanvas-offset offcanvas-end" tabindex="-1" id="customcanvas">
    <div class="offcanvas-header d-block pb-0">
        <div class="border-bottom d-flex align-items-center justify-content-between pb-3">
            <h6 class="offcanvas-title">Filter</h6>
            <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fa-solid fa-x"></i>
            </button>
        </div>
    </div>
    <div class="offcanvas-body pt-3">
        <form action="products.php" method="GET">

            <!-- Product -->
            <div class="mb-3">
                <label class="form-label">Product</label>
                <?php
                $selectedProducts = $_GET['product'] ?? [];
                if (!is_array($selectedProducts)) {
                    $selectedProducts = [$selectedProducts];
                }
                $selectedProductNames = [];
                if (!empty($selectedProducts)) {
                    $ids = implode(",", array_map('intval', $selectedProducts));
                    $res = mysqli_query($conn, "SELECT name FROM product WHERE id IN ($ids)");
                    while ($row = mysqli_fetch_assoc($res)) {
                        $selectedProductNames[] = htmlspecialchars($row['name']);
                    }
                }
                $productText = !empty($selectedProductNames) ? implode(", ", $selectedProductNames) : "Select";
                ?>
                <div class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border product-toggle"
                       data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <?= $productText ?>
                    </a>
                    <div class="dropdown-menu shadow-lg w-100 dropdown-info p-3">
                        <div class="mb-3">
                            <div class="input-icon-start position-relative">
                                <span class="input-icon-addon fs-12"><i class="isax isax-search-normal"></i></span>
                                <input type="text" class="form-control form-control-sm search-product" placeholder="Search Products">
                            </div>
                        </div>
                        <ul class="mb-3 list-unstyled product-list">
                            <li class="d-flex align-items-center justify-content-between mb-2">
                                <label class="d-inline-flex align-items-center text-gray-9">
                                    <input class="form-check-input select-all m-0 me-2" type="checkbox" <?= count($selectedProducts) > 0 ? 'checked' : '' ?>>
                                    Select All
                                </label>
                                <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline reset-product">Reset</a>
                            </li>
                            <?php
                            $products = mysqli_query($conn, "SELECT id, name FROM product WHERE is_deleted = 0");
                            while ($p = mysqli_fetch_assoc($products)) {
                                $isChecked = in_array($p['id'], $selectedProducts) ? 'checked' : '';
                                echo '<li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2 product-checkbox" type="checkbox" name="product[]" value="'.$p['id'].'" '.$isChecked.'>
                                            '.htmlspecialchars($p['name']).'
                                        </label>
                                    </li>';
                            }
                            ?>
                        </ul>
                         <div class="row g-2">
                                    <div class="col-6">
                                        <a href="javascript:void(0);" class="btn btn-outline-white w-100 close-filter">Cancel</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="javascript:void(0);" class="btn btn-primary w-100 user-apply">Select</a>
                                    </div>
                                </div>
                    </div>
                </div>
            </div>

            <!-- Category -->
            <div class="mb-3">
                <label class="form-label">Category</label>
                <?php
                $selectedCategories = $_GET['category'] ?? [];
                if (!is_array($selectedCategories)) {
                    $selectedCategories = [$selectedCategories];
                }
                $selectedCategoryNames = [];
                if (!empty($selectedCategories)) {
                    $ids = implode(",", array_map('intval', $selectedCategories));
                    $res = mysqli_query($conn, "SELECT name FROM category WHERE id IN ($ids)");
                    while ($row = mysqli_fetch_assoc($res)) {
                        $selectedCategoryNames[] = htmlspecialchars($row['name']);
                    }
                }
                $categoryText = !empty($selectedCategoryNames) ? implode(", ", $selectedCategoryNames) : "Select";
                ?>
                <div class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border category-toggle"
                       data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <?= $categoryText ?>
                    </a>
                    <div class="dropdown-menu shadow-lg w-100 dropdown-info p-3">
                        <div class="mb-3">
                            <div class="input-icon-start position-relative">
                                <span class="input-icon-addon fs-12"><i class="isax isax-search-normal"></i></span>
                                <input type="text" class="form-control form-control-sm search-category" placeholder="Search Categories">
                            </div>
                        </div>
                        <ul class="mb-3 list-unstyled category-list">
                            <li class="d-flex align-items-center justify-content-between mb-2">
                                <label class="d-inline-flex align-items-center text-gray-9">
                                    <input class="form-check-input select-all m-0 me-2" type="checkbox" <?= count($selectedCategories) > 0 ? 'checked' : '' ?>>
                                    Select All
                                </label>
                                <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline reset-category">Reset</a>
                            </li>
                            <?php
                            $categories = mysqli_query($conn, "SELECT id, name FROM category");
                            while ($c = mysqli_fetch_assoc($categories)) {
                                $isChecked = in_array($c['id'], $selectedCategories) ? 'checked' : '';
                                echo '<li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2 category-checkbox" type="checkbox" name="category[]" value="'.$c['id'].'" '.$isChecked.'>
                                            '.htmlspecialchars($c['name']).'
                                        </label>
                                    </li>';
                            }
                            ?>
                        </ul>
                         <div class="row g-2">
                                    <div class="col-6">
                                        <a href="javascript:void(0);" class="btn btn-outline-white w-100 close-filter">Cancel</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="javascript:void(0);" class="btn btn-primary w-100 user-apply">Select</a>
                                    </div>
                                </div>
                    </div>
                </div>
            </div>

            <!-- Unit -->
            <div class="mb-3">
                <label class="form-label">Unit</label>
                <?php
                $selectedUnits = $_GET['unit'] ?? [];
                if (!is_array($selectedUnits)) {
                    $selectedUnits = [$selectedUnits];
                }
                $selectedUnitNames = [];
                if (!empty($selectedUnits)) {
                    $ids = implode(",", array_map('intval', $selectedUnits));
                    $res = mysqli_query($conn, "SELECT name FROM units WHERE id IN ($ids)");
                    while ($row = mysqli_fetch_assoc($res)) {
                        $selectedUnitNames[] = htmlspecialchars($row['name']);
                    }
                }
                $unitText = !empty($selectedUnitNames) ? implode(", ", $selectedUnitNames) : "Select";
                ?>
                <div class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border unit-toggle"
                       data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <?= $unitText ?>
                    </a>
                    <div class="dropdown-menu shadow-lg w-100 dropdown-info p-3">
                        <div class="mb-3">
                            <div class="input-icon-start position-relative">
                                <span class="input-icon-addon fs-12"><i class="isax isax-search-normal"></i></span>
                                <input type="text" class="form-control form-control-sm search-unit" placeholder="Search Units">
                            </div>
                        </div>
                        <ul class="mb-3 list-unstyled unit-list">
                            <li class="d-flex align-items-center justify-content-between mb-2">
                                <label class="d-inline-flex align-items-center text-gray-9">
                                    <input class="form-check-input select-all m-0 me-2" type="checkbox" <?= count($selectedUnits) > 0 ? 'checked' : '' ?>>
                                    Select All
                                </label>
                                <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline reset-unit">Reset</a>
                            </li>
                            <?php
                            $units = mysqli_query($conn, "SELECT id, name FROM units");
                            while ($u = mysqli_fetch_assoc($units)) {
                                $isChecked = in_array($u['id'], $selectedUnits) ? 'checked' : '';
                                echo '<li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2 unit-checkbox" type="checkbox" name="unit[]" value="'.$u['id'].'" '.$isChecked.'>
                                            '.htmlspecialchars($u['name']).'
                                        </label>
                                    </li>';
                            }
                            ?>
                        </ul>
                         <div class="row g-2">
                                    <div class="col-6">
                                        <a href="javascript:void(0);" class="btn btn-outline-white w-100 close-filter">Cancel</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="javascript:void(0);" class="btn btn-primary w-100 user-apply">Select</a>
                                    </div>
                                </div>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label">Status</label>
                <?php
                $selectedStatus = $_GET['status'] ?? '';
                $statusText = $selectedStatus === "1" ? "Active" : ($selectedStatus === "0" ? "Inactive" : "Select");
                ?>
                <div class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border status-toggle"
                       data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <?= $statusText ?>
                    </a>
                    <div class="dropdown-menu shadow-lg w-100 dropdown-info p-3">
                        <ul class="mb-3 list-unstyled status-list">
                            <li>
                                <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                    <input class="form-check-input m-0 me-2" type="radio" name="status" value="1" <?= $selectedStatus === "1" ? 'checked' : '' ?>>
                                    Active
                                </label>
                            </li>
                            <li>
                                <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                    <input class="form-check-input m-0 me-2" type="radio" name="status" value="0" <?= $selectedStatus === "0" ? 'checked' : '' ?>>
                                    Inactive
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="offcanvas-footer">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="products.php" class="btn btn-outline-white w-100">Reset</a>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary w-100" id="filter-submit">Apply</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

		<!-- End Filter -->
        <!-- Multi Delete Modal -->
        <div class="modal fade" id="multideleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form method="POST" id="multiDeleteForm" action="process/action_multi_delete_product.php">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Products</h6>
                            <p class="mb-3">Are you sure you want to delete the selected Products?</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-white me-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
		
		<!-- End Delete -->

    </div>
    <!-- End Main Wrapper -->

	<?php include 'layouts/vendor-scripts.php'; ?>
<script>
	   // Multi-delete functionality
const multiDeleteModal = new bootstrap.Modal(document.getElementById('multideleteModal'));
const deleteBtn = document.querySelector('.delete-multiple');

// Toggle delete button visibility
function toggleDeleteBtn() {
    const anyChecked = document.querySelectorAll('.user-checkbox:checked').length > 0;
    deleteBtn.classList.toggle('d-none', !anyChecked);
}

// Delete button click
deleteBtn.addEventListener('click', function(e) {
    e.preventDefault();
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    const form = document.getElementById('multiDeleteForm');

    // Clear old hidden inputs
    form.querySelectorAll('input[name="product_ids[]"]').forEach(el => el.remove());

    // Add selected ids
    checkboxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'product_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    // Update modal text
    const modalTitle = document.querySelector('#multideleteModal h6');
    const modalMessage = document.querySelector('#multideleteModal p');

    if (checkboxes.length === 1) {
        modalTitle.textContent = 'Delete Product';
        modalMessage.textContent = 'Are you sure you want to delete the selected product?';
    } else {
        modalTitle.textContent = 'Delete Products';
        modalMessage.textContent = `Are you sure you want to delete the ${checkboxes.length} selected products?`;
    }

    multiDeleteModal.show();
});

// Select All functionality
document.getElementById('select-all').addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    toggleDeleteBtn();
});

// Individual checkbox change
document.querySelectorAll('.user-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', toggleDeleteBtn);
});

// Run once on page load (in case some boxes are pre-checked)
toggleDeleteBtn();

</script>
   <script>
$(document).ready(function() {
    $('.status-toggle').on('change', function() {
        var id = $(this).data('id');
        var status = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: 'process/action_toggle_product_status.php',
            type: 'POST',
            data: {
                id: id,
                status: status
            },
            success: function(response) {
                console.log('Status updated');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
});
</script>
<script>
let previousProducts = [];
let previousCategories = [];
let previousUnits = [];
function summarizeLabels(labels, limit = 3) {
    if (!labels || labels.length === 0) return 'Select';
    if (labels.length <= limit) return labels.join(', ');
    return labels.slice(0, limit).join(', ') + '+' + (labels.length - limit);
}

// Function to update dropdown labels
function updateDropdownLabels() {
    // ---------- PRODUCT ----------
    let productLabels = [];
    $('.product-checkbox:checked').each(function() {
        productLabels.push($(this).closest('label').text().trim());
    });
    const productSummary = summarizeLabels(productLabels, 3);
    $('.product-toggle').text(productSummary);
    $('#product-summary').val(productSummary === 'Select' ? '' : productSummary);

    // ---------- CATEGORY ----------
    let categoryLabels = [];
    $('.category-checkbox:checked').each(function() {
        categoryLabels.push($(this).closest('label').text().trim());
    });
    const categorySummary = summarizeLabels(categoryLabels, 3);
    $('.category-toggle').text(categorySummary);
    $('#category-summary').val(categorySummary === 'Select' ? '' : categorySummary);

    // ---------- UNIT ----------
    let unitLabels = [];
    $('.unit-checkbox:checked').each(function() {
        unitLabels.push($(this).closest('label').text().trim());
    });
    const unitSummary = summarizeLabels(unitLabels, 3);
    $('.unit-toggle').text(unitSummary);
    $('#unit-summary').val(unitSummary === 'Select' ? '' : unitSummary);

    // ---------- STATUS ----------
    let statusLabels = [];
    $('input[name="status"]:checked').each(function() {
        statusLabels.push($(this).closest('label').text().trim());
    });
    const statusSummary = summarizeLabels(statusLabels, 1); // usually 1 only
    $('.status-toggle').text(statusSummary);
    $('#status-summary').val(statusSummary === 'Select' ? '' : statusSummary);
}

// Initialize labels on page load
updateDropdownLabels();

// -------------------- PRODUCT --------------------
$(document).on('change', '.product-checkbox', function() {
    updateDropdownLabels();
    const allChecked = $('.product-checkbox:not(:checked)').length === 0;
    $('.product-list .select-all').prop('checked', allChecked);
});

$(document).on('change', '.product-list .select-all', function() {
    $('.product-checkbox').prop('checked', this.checked);
    updateDropdownLabels();
});

$(document).on('click', '.reset-product', function() {
    $('.product-checkbox, .product-list .select-all').prop('checked', false);
    updateDropdownLabels();
});

$(".search-product").on("keyup", function() {
    const value = $(this).val().toLowerCase();
    $(".product-list li").each(function() {
        if ($(this).find('.select-all').length > 0) return;
        const text = $(this).text().toLowerCase();
        $(this).toggle(text.indexOf(value) > -1);
    });
});

// Store previous product selections when dropdown opens
$('.product-toggle').on('click', function() {
    previousProducts = $('.product-checkbox:checked').map(function(){ return $(this).val(); }).get();
});

// -------------------- CATEGORY --------------------
$(document).on('change', '.category-checkbox', function() {
    updateDropdownLabels();
    const allChecked = $('.category-checkbox:not(:checked)').length === 0;
    $('.category-list .select-all').prop('checked', allChecked);
});

$(document).on('change', '.category-list .select-all', function() {
    $('.category-checkbox').prop('checked', this.checked);
    updateDropdownLabels();
});

$(document).on('click', '.reset-category', function() {
    $('.category-checkbox, .category-list .select-all').prop('checked', false);
    updateDropdownLabels();
});

$(".search-category").on("keyup", function() {
    const value = $(this).val().toLowerCase();
    $(".category-list li").each(function() {
        if ($(this).find('.select-all').length > 0) return;
        const text = $(this).text().toLowerCase();
        $(this).toggle(text.indexOf(value) > -1);
    });
});

// Store previous category selections when dropdown opens
$('.category-toggle').on('click', function() {
    previousCategories = $('.category-checkbox:checked').map(function(){ return $(this).val(); }).get();
});

// -------------------- UNIT --------------------
$(document).on('change', '.unit-checkbox', function() {
    updateDropdownLabels();
    const allChecked = $('.unit-checkbox:not(:checked)').length === 0;
    $('.unit-list .select-all').prop('checked', allChecked);
});

$(document).on('change', '.unit-list .select-all', function() {
    $('.unit-checkbox').prop('checked', this.checked);
    updateDropdownLabels();
});

$(document).on('click', '.reset-unit', function() {
    $('.unit-checkbox, .unit-list .select-all').prop('checked', false);
    updateDropdownLabels();
});

$(".search-unit").on("keyup", function() {
    const value = $(this).val().toLowerCase();
    $(".unit-list li").each(function() {
        if ($(this).find('.select-all').length > 0) return;
        const text = $(this).text().toLowerCase();
        $(this).toggle(text.indexOf(value) > -1);
    });
});

// Store previous unit selections when dropdown opens
$('.unit-toggle').on('click', function() {
    previousUnits = $('.unit-checkbox:checked').map(function(){ return $(this).val(); }).get();
});

// -------------------- STATUS --------------------
$(document).on('change', 'input[name="status"]', function() {
    updateDropdownLabels();
});

// -------------------- APPLY BUTTON --------------------
$('.dropdown').on('click', '.user-apply', function() {
    updateDropdownLabels();
    $(this).closest('.dropdown-menu').removeClass('show'); // Close dropdown
});

// -------------------- CANCEL BUTTON --------------------
$('.dropdown').on('click', '.close-filter', function() {
    const $dropdown = $(this).closest('.dropdown-menu');

    if ($dropdown.find('.product-checkbox').length) {
        $('.product-checkbox').prop('checked', false);
        previousProducts.forEach(val => $('.product-checkbox[value="'+val+'"]').prop('checked', true));
    }
    if ($dropdown.find('.category-checkbox').length) {
        $('.category-checkbox').prop('checked', false);
        previousCategories.forEach(val => $('.category-checkbox[value="'+val+'"]').prop('checked', true));
    }
    if ($dropdown.find('.unit-checkbox').length) {
        $('.unit-checkbox').prop('checked', false);
        previousUnits.forEach(val => $('.unit-checkbox[value="'+val+'"]').prop('checked', true));
    }

    updateDropdownLabels();
    $dropdown.removeClass('show'); // Close dropdown
});
</script>


</body>

</html>