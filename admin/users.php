<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

// Define timeAgo function first
function timeAgo($datetime) {
    if (empty($datetime)) {
        return 'Never';
    }
    
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) return "Online";
    if ($diff < 3600) return floor($diff / 60) . " mins ago";
    if ($diff < 86400) return floor($diff / 3600) . " hours ago";
    return date('d M Y', $timestamp);
}

// Get filter parameters from request
$status_filter = isset($_GET['status']) ? (array)$_GET['status'] : [];
$user_filter = isset($_GET['users']) ? (array)$_GET['users'] : [];
$date_range = isset($_GET['date_range']) ? $_GET['date_range'] : '';

// Build the base query
$query = "SELECT login.*, user_role.name as role_name FROM login 
          LEFT JOIN user_role ON login.role_id = user_role.id 
          WHERE 1=1";

// Add status filter if provided
if (!empty($status_filter)) {
    $status_filter = array_map(function($status) use ($conn) {
        return mysqli_real_escape_string($conn, $status);
    }, $status_filter);
    
    $status_list = implode("','", $status_filter);
    $query .= " AND login.status IN ('$status_list')";
}

// Add user filter if provided
if (!empty($user_filter)) {
    $user_filter = array_map(function($user) use ($conn) {
        return mysqli_real_escape_string($conn, $user);
    }, $user_filter);
    
    $user_list = implode("','", $user_filter);
    $query .= " AND login.id IN ('$user_list')";
}

// Add date range filter if provided
if (!empty($date_range)) {
    $dates = explode(' - ', $date_range);
    $start_date = date('Y-m-d', strtotime(trim($dates[0])));
    $end_date = date('Y-m-d', strtotime(trim($dates[1])));
    $query .= " AND DATE(login.created_at) BETWEEN '$start_date' AND '$end_date'";
}

$query .= " ORDER BY login.id DESC";

$roles = mysqli_query($conn, "SELECT * FROM user_role");
$all_users = mysqli_query($conn, "SELECT id, name, profile_img FROM login");
$users = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layouts/title-meta.php'; ?> 
    <?php include 'layouts/head-css.php'; ?>
    <style>.password-toggle,
.toggle-pass-edit,
.toggle-pass-confirm-edit {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
    color: #888;
}


</style>
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
                   <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo ($_SESSION['message_type'] == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h6>Users</h6>
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
                            <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                <i class="isax isax-export-1 me-1"></i>Export
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="process/export_excle_users.php?<?php echo http_build_query($_GET); ?>">Download as Excel</a>
                                </li>
                            </ul>
                        </div>
                        <?php if (check_is_access_new("add_user") == 1) { ?> 
                        <div>
                            <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_modal">
                                <i class="isax isax-add-circle5 me-1"></i>New User
                            </a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- End Page Header -->

                <!-- <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="table-search d-flex align-items-center mb-0">
                                <div class="search-input">
                                    <a href="javascript:void(0);" class="btn-searchset"><i class="isax isax-search-normal fs-12"></i></a>
                                </div>
                            </div>
                            
                            <a class="btn btn-outline-white fw-normal d-inline-flex align-items-center" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#customcanvas">
                                <i class="isax isax-filter me-1"></i>Filter
                            </a>
                           <?php if (!empty($user_filter) || !empty($date_range) || !empty($status_filter)): ?>
                            <a href="users.php" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                            </a>
                        <?php endif; ?>


                            <a href="#" class="btn btn-outline-danger d-inline-flex align-items-center delete-multiple d-none">
                            <i class="fa-regular fa-trash-can me-1"></i>Delete
                        </a>

                        </div>
                    </div>
                    <div class="align-items-center gap-2 flex-wrap filter-info mt-3">
                        <h6 class="fs-13 fw-semibold">Filters</h6>
                     <?php if(!empty($status_filter)): ?>
                            <?php foreach($status_filter as $status): ?>
                                <span class="tag bg-light border rounded-1 fs-12 text-dark badge">
                                    <span class="num-count d-inline-flex align-items-center justify-content-center bg-success fs-10 me-1">1</span>
                                    Status: <?= $status == '1' ? 'Active' : 'Inactive' ?>
                                    <a href="users.php?<?= http_build_query(array_merge($_GET, ['status' => array_diff($status_filter, [$status])])) ?>" class="ms-1 tag-close"><i class="fa-solid fa-x fs-10"></i></a>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if(!empty($user_filter)): ?>
                            <?php 
                            $user_names = [];
                            foreach($user_filter as $user_id) {
                                $user_result = mysqli_query($conn, "SELECT name FROM login WHERE id = '$user_id'");
                                if ($user_row = mysqli_fetch_assoc($user_result)) {
                                    $user_names[] = htmlspecialchars($user_row['name']);
                                }
                            }
                            
                            foreach($user_names as $user_name): ?>
                                <span class="tag bg-light border rounded-1 fs-12 text-dark badge">
                                    <span class="num-count d-inline-flex align-items-center justify-content-center bg-success fs-10 me-1">1</span>
                                    User: <?= $user_name ?>
                                    <a href="users.php?<?= http_build_query(array_merge($_GET, ['users' => array_diff($user_filter, [$user_id])])) ?>" class="ms-1 tag-close"><i class="fa-solid fa-x fs-10"></i></a>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if(!empty($date_range)): ?>
                            <span class="tag bg-light border rounded-1 fs-12 text-dark badge">
                                <span class="num-count d-inline-flex align-items-center justify-content-center bg-success fs-10 me-1">1</span>
                                Date Range
                                <a href="users.php?<?= http_build_query(array_diff_key($_GET, ['date_range' => ''])) ?>" class="ms-1 tag-close"><i class="fa-solid fa-x fs-10"></i></a>
                            </span>
                        <?php endif; ?>
                        <?php if(!empty($status_filter) || !empty($user_filter) || !empty($date_range)): ?>
                            <a href="users.php" class="link-danger fw-medium text-decoration-underline ms-md-1">Clear All</a>
                        <?php endif; ?>
                    </div>
                </div> -->

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
            
            // User filters
            if (!empty($user_filter)) {
                $user_names = [];
                foreach($user_filter as $user_id) {
                    $user_result = mysqli_query($conn, "SELECT name FROM login WHERE id = '$user_id'");
                    if ($user_row = mysqli_fetch_assoc($user_result)) {
                        $user_names[] = htmlspecialchars($user_row['name']);
                    }
                }
                if (!empty($user_names)) {
                    $active_filters[] = "User: " . (count($user_names) > 2 ? 
                        implode(", ", array_slice($user_names, 0, 2)) . " +" . (count($user_names) - 2) : 
                        implode(", ", $user_names));
                }
            }
            
            // Status filters
            if (!empty($status_filter)) {
                $status_names = [];
                foreach($status_filter as $status) {
                    $status_names[] = $status == '1' ? 'Active' : 'Inactive';
                }
                if (!empty($status_names)) {
                    $active_filters[] = "Status: " . (count($status_names) > 2 ? 
                        implode(", ", array_slice($status_names, 0, 2)) . " +" . (count($status_names) - 2) : 
                        implode(", ", $status_names));
                }
            }
            
            // Date range filter
            if (!empty($date_range)) {
                $active_filters[] = "Date: " . htmlspecialchars($date_range);
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
                    <a href="users.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-xmark me-1"></i> Clear Filters
                    </a>
                </div>
            <?php endif; ?>

            <!-- Multiple Delete Button -->
            <a href="#" class="btn btn-outline-danger d-inline-flex align-items-center delete-multiple d-none">
                <i class="fa-regular fa-trash-can me-1"></i>Delete
            </a>
        </div>
    </div>
</div>

             <div class="table-responsive">
            <table class="table table-nowrap datatable">
                <thead class="thead-light">
                    <tr>
                        <th class="no-sort">
                            <div class="form-check form-check-md">
                                <input class="form-check-input" type="checkbox" id="select-all">
                            </div>
                        </th>
                        <th>User</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Last Activity</th>
                        <th>Create On</th>
                        <th>Status</th>
                        <th class="no-sort">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($users) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td>
                                <div class="form-check form-check-md">
                                    <input type="checkbox" class="form-check-input user-checkbox" name="user_ids[]" value="<?= htmlspecialchars($row['id']) ?>">
                                </div>
                            </td>
                            <?php
                            $profileImg = !empty($row['profile_img']) ? '../uploads/' . htmlspecialchars($row['profile_img']) : 'assets/img/users/user-16.jpg';
                            ?>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" class="avatar avatar-sm rounded-circle me-2 flex-shrink-0">
                                        <img src="<?= $profileImg ?>" class="rounded-circle" alt="img">
                                    </a>
                                    <div>
                                        <h6 class="fs-14 fw-medium mb-0">
                                            <a href="javascript:void(0);"><?= htmlspecialchars($row['name']) ?></a>
                                        </h6>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($row['phone_number'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['role_name'] ?? '') ?></td>
                            <td><?= timeAgo($row['last_activity']) ?></td>
                            <td><?= !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '' ?></td>
                            <td>
                                <?php if ($row['status'] == '1'): ?>
                                    <a href="process/action_toggle_user_status.php?id=<?= $row['id'] ?>&status=0" 
                                    class="badge bg-success text-white text-decoration-none">
                                    Active
                                    </a>
                                <?php else: ?>
                                    <a href="process/action_toggle_user_status.php?id=<?= $row['id'] ?>&status=1" 
                                    class="badge bg-danger text-white text-decoration-none">
                                    Inactive
                                    </a>
                                <?php endif; ?>
                            </td>

                            <td class="action-item">
                                <a href="#" data-bs-toggle="dropdown">
                                    <i class="isax isax-more"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php if (check_is_access_new("edit_user") == 1) { ?>
                                    <li>
                                        <a href="#" class="dropdown-item" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal<?php echo $row['id']; ?>">
                                        <i class="isax isax-edit me-2"></i>Edit
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (check_is_access_new("delete_user") == 1) { ?>
                                    <li>
                                        <a href="#" class="dropdown-item" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal<?php echo $row['id']; ?>">
                                        <i class="isax isax-trash me-2"></i>Delete
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <!-- Remove this row completely - DataTables will handle empty tables -->
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
            </div>
            <!-- End Content -->
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
                    <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                </div>
            </div>
            <div class="offcanvas-body pt-3">
                <form id="filterForm" method="GET" action="users.php">
                    <div class="mb-3">
                        <label class="form-label">Users</label>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border" id="userDropdownButton" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                <?php
                                $user_text = "Select";
                                if (!empty($user_filter)) {
                                    if (count($user_filter) == 1) {
                                        $user_id = $user_filter[0];
                                        $user_result = mysqli_query($conn, "SELECT name FROM login WHERE id = '$user_id'");
                                        if ($user_row = mysqli_fetch_assoc($user_result)) {
                                            $user_text = htmlspecialchars($user_row['name']);
                                        }
                                    } else {
                                        $user_text = count($user_filter) . ' Selected';
                                    }
                                }
                                ?>
                                <?= $user_text ?>
                            </a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info p-3">
                                <div class="mb-3">
                                    <div class="input-icon-start position-relative">
                                        <span class="input-icon-addon fs-12">
                                            <i class="isax isax-search-normal"></i>
                                        </span>
                                        <input type="text" class="form-control form-control-sm user-search" placeholder="Search users">
                                    </div>
                                </div>
                                <ul class="mb-3 user-list" style="max-height: 200px; overflow-y: auto;">
                                    <li class="d-flex align-items-center justify-content-between mb-3">
                                        <label class="d-inline-flex align-items-center text-gray-9">
                                            <input class="form-check-input user-select-all m-0 me-2" type="checkbox"> Select All
                                        </label>
                                        <a href="javascript:void(0);" class="link-danger fw-medium text-decoration-underline user-reset">Reset</a>
                                    </li>

                                    <?php
                                    mysqli_data_seek($all_users, 0);
                                    while ($user = mysqli_fetch_assoc($all_users)) {
                                        $userId   = $user['id'];
                                        $userName = htmlspecialchars($user['name']);
                                        $userImg  = !empty($user['profile_img']) ? '../uploads/' . $user['profile_img'] : "assets/img/users/user-16.jpg";
                                        $isChecked = in_array($userId, $user_filter) ? 'checked' : '';
                                        ?>
                                        <li class="user-item">
                                            <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                                <input class="form-check-input user-check m-0 me-2" type="checkbox" name="users[]" value="<?= $userId ?>" <?= $isChecked ?>>
                                                <span class="avatar avatar-sm rounded-circle me-2">
                                                    <img src="<?= $userImg ?>" class="flex-shrink-0 rounded-circle" alt="img">
                                                </span>
                                                <?= $userName ?>
                                            </label>
                                        </li>
                                        <?php
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

                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="input-group position-relative">
                            <input type="text" name="date_range" class="form-control date-range bookingrange rounded-end" value="<?= htmlspecialchars($date_range) ?>">
                            <span class="input-icon-addon fs-16 text-gray-9">
                                <i class="isax isax-calendar-2"></i>
                            </span>
                        </div>
                    </div>
                   
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-lg bg-light d-flex align-items-center justify-content-start fs-13 fw-normal border" id="statusDropdownButton" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                <?php
                                $status_text = "Select";
                                if (count($status_filter) == 1) {
                                    $status_text = ($status_filter[0] == '1') ? 'Active' : 'Inactive';
                                } elseif (count($status_filter) == 2) {
                                    $status_text = 'All Selected';
                                }
                                ?>
                                <?= $status_text ?>
                            </a>
                            <div class="dropdown-menu shadow-lg w-100 dropdown-info p-2">
                                <ul class="mb-3">
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2 status-checkbox" type="checkbox" 
                                                   name="status[]" value="1" 
                                                   <?= in_array('1', $status_filter) ? 'checked' : '' ?>>
                                            <i class="fa-solid fa-circle fs-6 text-success me-1"></i>Active
                                        </label>
                                    </li>
                                    <li>
                                        <label class="dropdown-item px-2 d-flex align-items-center text-dark">
                                            <input class="form-check-input m-0 me-2 status-checkbox" type="checkbox" 
                                                   name="status[]" value="0" 
                                                   <?= in_array('0', $status_filter) ? 'checked' : '' ?>>
                                            <i class="fa-solid fa-circle fs-6 text-danger me-1"></i>Inactive
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                     <div class="offcanvas-footer">
                        <div class="row g-2">
                            <div class="col-6"><a href="users.php" class="btn btn-outline-white w-100">Reset</a></div>
                            <div class="col-6"><button type="submit" class="btn btn-primary w-100" id="filter-submit">Apply</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Filter -->

        <!-- Start addModal -->
       <div id="add_modal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New User</h4>
                <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
            </div>
            <form action="process/action_add_users.php" method="POST" enctype="multipart/form-data" id="addUserForm">
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <span class="text-gray-9 fw-bold mb-2 d-flex">Image</span>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0"  id="add_image_preview">
                                        <i class="isax isax-image text-primary fs-24"></i>
                                    </div>
                                    <div class="d-inline-flex flex-column align-items-start">
                                        <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                            <i class="isax isax-image me-1"></i>Upload Image
                                            <input type="file" name="profile_img" id="add_image" class="form-control image-sign" accept="image/jpeg,image/png">
                                        </div>
                                        <span class="text-gray-9 fs-12">JPG or PNG format, not exceeding 5MB.</span>
                                        <span class="text-danger error-msg" id="add_image_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name<span class="text-danger ms-1">*</span></label>
                                <input type="text" name="name" class="form-control" id="name" >
                                <span class="text-danger error-msg" id="add_nameError"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email<span class="text-danger ms-1">*</span></label>
                                <input type="email" name="email" class="form-control" pattern="[^ ]+@[^ ]+\.[a-z]{2,3}" >
                                <span class="text-danger error-msg" id="add_emailError"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Mobile Number<span class="text-danger ms-1">*</span></label></label>
                                <input type="number" name="phone_number" id="phone_number" class="form-control" minlength="10">
                                    <span id="add_phoneError" class="text-danger error-msg"></span>

                            </div>
                        </div>
                        <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Password<span class="text-danger ms-1">*</span></label>
                            <div class="position-relative">
                                <input type="password" name="password" class="form-control pass-input" minlength="6">
                                <span class="isax toggle-pass isax-eye-slash password-toggle"></span>
                            </div>
                            <span class="text-danger error-msg" id="add_passwordError"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Confirm Password<span class="text-danger ms-1">*</span></label>
                            <div class="position-relative">
                                <input type="password" name="cpassword" class="form-control pass-input-confirm">
                                <span class="isax toggle-pass-confirm isax-eye-slash password-toggle"></span>
                            </div>
                            <span class="text-danger error-msg" id="add_cpasswordError"></span>
                        </div>
                    </div>

                        <!-- <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Role<span class="text-danger ms-1">*</span></label>
                                <select class="form-select" name="role_id" >
                                    <option value="">Select Role</option>
                                    <?php
                                    mysqli_data_seek($roles, 0);
                                    while ($role = mysqli_fetch_assoc($roles)) {
                                        echo '<option value="' . htmlspecialchars($role['id']) . '">' . htmlspecialchars($role['name']) . '</option>';
                                    }
                                    ?>
                                </select>
                                <span class="text-danger error-msg" id="add_roleError"></span>
                            </div>
                        </div> -->
                           <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Role<span class="text-danger ms-1">*</span></label>
                                <select class="form-select" name="role_id" >
                                    <option value="">Select Role</option>
                                    <?php
                                    mysqli_data_seek($roles, 0);
                                    while ($role = mysqli_fetch_assoc($roles)) {
                                        // Skip the 4th role ID
                                        if ($role['id'] != 4) {
                                            echo '<option value="' . htmlspecialchars($role['id']) . '">' . htmlspecialchars($role['name']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <span class="text-danger error-msg" id="add_roleError"></span>
                            </div>
                        </div>
                       <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                   
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                
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
        <!-- End Modal -->

        <?php 
        // Reset pointer to beginning for edit modals
        mysqli_data_seek($users, 0);
        while ($row = mysqli_fetch_assoc($users)): 
        ?>
            <!-- Edit Modal for each user -->
       <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit User</h4>
                            <button type="button" class="btn-close btn-close-modal custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa-solid fa-x"></i>
                            </button>
                        </div>

                        <form action="process/action_edit_users.php" method="POST" enctype="multipart/form-data" class="edit-user-form" data-userid="<?php echo $row['id']; ?>">
                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['id']) ?>">
                            <div class="modal-body pb-0">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <span class="text-gray-9 fw-bold mb-2 d-flex">Image</span>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0" id="edit_image_preview">
                                                    <div class="position-relative d-flex align-items-center">
                                                        <?php
                                                            $profile_img = !empty($row['profile_img']) && file_exists('../uploads/' . $row['profile_img']) 
                                                                ? '../uploads/' . htmlspecialchars($row['profile_img']) 
                                                                : 'assets/img/users/default.png';
                                                        ?>
                                                        <img src="<?= $profile_img ?>" class="avatar avatar-xl" alt="User Img" id="edit_display_image">
                                                        <a href="javascript:void(0);" class="rounded-trash trash-top d-flex align-items-center justify-content-center">
                                                            <i class="isax isax-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="d-inline-flex flex-column align-items-start">
                                                    <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                                        <i class="isax isax-image me-1"></i>Upload Image
                                                        <input type="file" name="profile_img" id="edit_image" class="form-control image-sign" accept="image/jpeg,image/png">
                                                    </div>
                                                    <span class="text-gray-9 fs-12">JPG or PNG format, not exceeding 5MB.</span>
                                                    <span class="text-danger error-msg" id="edit_image_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                           
                                 <div class="mb-3">
                                            <label class="form-label">Name<span class="text-danger ms-1">*</span></label>
                                            <input type="text" name="name" class="form-control" id="namee" value="<?= htmlspecialchars($row['name']) ?>" >
                                            <span class="text-danger error-msg" id="edit_nameError_<?php echo $row['id']; ?>"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email<span class="text-danger ms-1">*</span></label>
                                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>" pattern="[^ ]+@[^ ]+\.[a-z]{2,3}" >
                                            <span class="text-danger error-msg" id="edit_emailError_<?php echo $row['id']; ?>"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Phone Number<span class="text-danger ms-1">*</span></label></label>
                                            <input type="number" name="phone_number" class="form-control" value="<?= htmlspecialchars($row['phone_number']) ?>" minlength="10">
                                            <span class="text-danger error-msg" id="edit_phoneError_<?php echo $row['id']; ?>"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <div class="position-relative">
                                                <input type="password" name="password" class="form-control pass-input-edit" data-userid="<?php echo $row['id']; ?>">
                                                <span class="isax toggle-pass-edit isax-eye-slash" data-userid="<?php echo $row['id']; ?>"></span>
                                            </div>
                                            <span class="text-danger error-msg" id="edit_passwordError_<?php echo $row['id']; ?>"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Confirm Password</label>
                                            <div class="position-relative">
                                                <input type="password" name="confirm_password" class="form-control pass-input-confirm-edit" data-userid="<?php echo $row['id']; ?>">
                                                <span class="isax toggle-pass-confirm-edit isax-eye-slash" data-userid="<?php echo $row['id']; ?>"></span>
                                            </div>
                                            <span class="text-danger error-msg" id="edit_cpasswordError_<?php echo $row['id']; ?>"></span>
                                        </div>
                                    </div>

                                    <!-- <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Role<span class="text-danger ms-1">*</span></label>
                                            <select class="form-select" name="role_id" >
                                                <option value="">Select</option>
                                                <?php
                                                mysqli_data_seek($roles, 0);
                                                while ($role = mysqli_fetch_assoc($roles)): ?>
                                                    <option value="<?= htmlspecialchars($role['id']) ?>" <?= ($row['role_id'] == $role['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($role['name']) ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                            <span class="text-danger error-msg" id="edit_roleError_<?php echo $row['id']; ?>"></span>
                                        </div>
                                    </div> -->
<div class="col-md-6">
    <div class="mb-3">
        <label class="form-label">Role<span class="text-danger ms-1">*</span></label>
        <select class="form-select" name="role_id">
            <option value="">Select</option>
            <?php
            mysqli_data_seek($roles, 0);
            while ($role = mysqli_fetch_assoc($roles)): 
                // Skip the 4th role ID
                if ($role['id'] != 4): ?>
                    <option value="<?= htmlspecialchars($role['id']) ?>" <?= ($row['role_id'] == $role['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['name']) ?>
                    </option>
                <?php endif; ?>
            <?php endwhile; ?>
        </select>
        <span class="text-danger error-msg" id="edit_roleError_<?php echo $row['id']; ?>"></span>
    </div>
</div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status">
                                               
                                                <option value="1" <?= ($row['status'] == '1') ? 'selected' : '' ?>>Active</option>
                                                <option value="0" <?= ($row['status'] == '0') ? 'selected' : '' ?>>Inactive</option>
                                            </select>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer d-flex align-items-center justify-content-between gap-1">
                                <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Modal for each user -->
            <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-m">
                    <div class="modal-content">
                        <form method="GET" action="process/action_delete_users.php">
                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['id']) ?>">
                            <div class="modal-body text-center">
                                <div class="mb-3">
                                    <img src="assets/img/icons/delete.svg" alt="img">
                                </div>
                                <h6 class="mb-1">Delete User</h6>
                                <p class="mb-3">Are you sure you want to delete this user?</p>
                                <div class="d-flex justify-content-center">
                                    <button type="button" class="btn btn-white me-3" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Yes, Delete</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
       

        <?php endwhile; ?>

        <!-- Multi Delete Modal -->
        <div class="modal fade" id="multideleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-m">
                <div class="modal-content">
                    <form method="POST" id="multiDeleteForm" action="process/action_multi_delete_user.php">
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <img src="assets/img/icons/delete.svg" alt="img">
                            </div>
                            <h6 class="mb-1">Delete Users</h6>
                            <p class="mb-3">Are you sure you want to delete the selected users?</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-white me-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Wrapper -->

    <?php include 'layouts/vendor-scripts.php'; ?>
<script>
    
</script>
    <script>
 

    // Password toggle functionality
    document.querySelectorAll('.toggle-passwords').forEach(icon => {
        icon.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('isax-eye');
            this.classList.toggle('isax-eye-slash');
        });
    });

    document.querySelectorAll('.toggle-passworda').forEach(icon => {
        icon.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('ti-eye');
            this.classList.toggle('ti-eye-off');
        });
    });

    // Initialize date range picker
    $(document).ready(function() {
        $('.bookingrange').daterangepicker({
            startDate: moment().subtract(6, 'days'),
            endDate: moment(),
            opens: 'left',
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Year': [moment().startOf('year'), moment().endOf('year')],
        'Next Year': [moment().add(1, 'year').startOf('year'), moment().add(1, 'year').endOf('year')]
            },
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        // Multi-delete functionality
      const multiDeleteModal = new bootstrap.Modal(document.getElementById('multideleteModal'));
const deleteBtn = document.querySelector('.delete-multiple');

// Function to toggle Delete button visibility
function toggleDeleteBtn() {
    const anyChecked = document.querySelectorAll('.user-checkbox:checked').length > 0;
    deleteBtn.classList.toggle('d-none', !anyChecked);
}

// Delete button click — open modal directly
deleteBtn.addEventListener('click', function(e) {
    e.preventDefault();
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    const form = document.getElementById('multiDeleteForm');

    // Remove old hidden inputs
    form.querySelectorAll('input[name="user_ids[]"]').forEach(el => el.remove());

    // Add selected user IDs as hidden inputs
    checkboxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    // Update modal text
    const modalTitle = document.querySelector('#multideleteModal h6');
    const modalMessage = document.querySelector('#multideleteModal p');

    if (checkboxes.length === 1) {
        modalTitle.textContent = 'Delete User';
        modalMessage.textContent = 'Are you sure you want to delete the selected user?';
    } else {
        modalTitle.textContent = 'Delete Users';
        modalMessage.textContent = `Are you sure you want to delete the ${checkboxes.length} selected users?`;
    }

    multiDeleteModal.show();
});

// Select All checkbox functionality
document.getElementById('select-all').addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    toggleDeleteBtn(); // update Delete button visibility
});

// Individual checkbox change — works even for dynamically generated checkboxes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('user-checkbox')) {
        toggleDeleteBtn();
    }
});

// Run once on page load (in case some checkboxes are pre-checked)
toggleDeleteBtn();


        // Select All functionality
        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('.user-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Image upload preview
        document.querySelectorAll('.image-sign').forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    const avatarContainer = this.closest('.d-flex').querySelector('.avatar');
                    
                    reader.onload = function(event) {
                        const img = avatarContainer.querySelector('img') || document.createElement('img');
                        img.src = event.target.result;
                        img.className = 'avatar avatar-xl';
                        img.alt = 'Preview';
                        
                        if (!avatarContainer.querySelector('img')) {
                            avatarContainer.innerHTML = '';
                            avatarContainer.appendChild(img);
                        }
                    }
                    
                    reader.readAsDataURL(file);
                }
            });
        });
    });
    </script>
    <script>
        $(document).ready(function() {
    // Update dropdown button text when checkboxes change
    $('.status-checkbox').change(function() {
        const checkedBoxes = $('.status-checkbox:checked');
        let statusText = "Select";
        
        if (checkedBoxes.length === 1) {
            statusText = checkedBoxes.val() == '1' ? 'Active' : 'Inactive';
        } else if (checkedBoxes.length === 2) {
            statusText = 'All Selected';
        }
        
        $(this).closest('.dropdown').find('.dropdown-toggle').text(statusText);
    });

    // Prevent dropdown from closing when clicking checkboxes
    $('.dropdown-menu').on('click', function(e) {
        if ($(e.target).is('.form-check-input')) {
            e.stopPropagation();
        }
    });
});
    </script>
<script>
// Add User Form Validation
// document.getElementById('addUserForm').addEventListener('submit', function(event) {
//     event.preventDefault();
//     if (validateAddUserForm()) {
//         this.submit();
//     }
// });

document.querySelector('#addUserForm').addEventListener('submit', async function (e) {
    e.preventDefault(); // prevent default submit
    const isValid = await validateAddUserForm();
    if (isValid) {
        this.submit(); // only submit if valid
    }
});


function resetAddFormErrors() {
    document.getElementById('add_nameError').textContent = '';
    document.getElementById('add_emailError').textContent = '';
    document.getElementById('add_passwordError').textContent = '';
    document.getElementById('add_cpasswordError').textContent = '';
    document.getElementById('add_roleError').textContent = '';
    
}

// Edit User Form Validation (using event delegation)
// document.addEventListener('submit', function(event) {
//     if (event.target && event.target.classList.contains('edit-user-form')) {
//         event.preventDefault();
//         if (validateEditUserForm(event.target)) {
//             event.target.submit();
//         }
//     }
// });
document.addEventListener('submit', async function(event) {
    if (event.target && event.target.classList.contains('edit-user-form')) {
        event.preventDefault();
        const isValid = await validateEditUserForm(event.target);
        if (isValid) {
            event.target.submit();
        }
    }
});

async function validateEditUserForm(form) {
    const userId = form.dataset.userid;
    let isValid = true;
    resetEditFormErrors(userId);

    const name = form.querySelector('input[name="name"]').value.trim();
    const email = form.querySelector('input[name="email"]').value.trim();
    const password = form.querySelector('input[name="password"]').value;
     const phone = form.querySelector('input[name="phone_number"]').value.trim();
    const confirmPassword = form.querySelector('input[name="confirm_password"]').value;
    const role = form.querySelector('select[name="role_id"]').value;
  

    

    // Name validation
    if (name === '') {
        document.getElementById(`edit_nameError_${userId}`).textContent = 'Name is Required';
        isValid = false;
    }

    // Email validation
    // const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    // if (email === '') {
    //     document.getElementById(`edit_emailError_${userId}`).textContent = 'Email is Required';
    //     isValid = false;
    // } else if (!emailPattern.test(email)) {
    //     document.getElementById(`edit_emailError_${userId}`).textContent = 'Invalid email format';
    //     isValid = false;
    // }
 const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
    if (email === '') {
        document.getElementById(`edit_emailError_${userId}`).textContent = 'Email is Required';
        isValid = false;
    } else if (!emailPattern.test(email)) {
        document.getElementById(`edit_emailError_${userId}`).textContent = 'Invalid email format';
        isValid = false;
    } else {
        // 🔑 Check duplicate email (ignore current user)
        const res = await fetch('process/check_duplicate_user_email.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'email=' + encodeURIComponent(email) + '&user_id=' + encodeURIComponent(userId)
        });
        const data = await res.json();
        if (data.exists) {
            document.getElementById(`edit_emailError_${userId}`).textContent = 'This email is already registered';
            isValid = false;
        }
    }

    // Password validation (only if password is provided)
    if (password !== '') {
        if (password.length < 6) {
            document.getElementById(`edit_passwordError_${userId}`).textContent = 'Password must be at least 6 characters';
            isValid = false;
        }
        
        // Confirm password validation (only if password is provided)
        if (confirmPassword === '') {
            document.getElementById(`edit_cpasswordError_${userId}`).textContent = 'Please confirm your password';
            isValid = false;
        } else if (password !== confirmPassword) {
            document.getElementById(`edit_cpasswordError_${userId}`).textContent = 'Passwords do not match';
            isValid = false;
        }
    }

    // Role validation
    if (role === '') {
        document.getElementById(`edit_roleError_${userId}`).textContent = 'Role is Required';
        isValid = false;
    }

 if (phone === '') {
        document.getElementById(`edit_phoneError_${userId}`).textContent = 'Phone Number is Required';
        isValid = false;
    }


    return isValid
}

 function resetEditFormErrors(userId) {
    document.getElementById(`edit_nameError_${userId}`).textContent = '';
    document.getElementById(`edit_emailError_${userId}`).textContent = '';
    document.getElementById(`edit_passwordError_${userId}`).textContent = '';
    document.getElementById(`edit_cpasswordError_${userId}`).textContent = '';
    document.getElementById(`edit_roleError_${userId}`).textContent = '';
    document.getElementById(`edit_phoneError_${userId}`).textContent = '';
    
}

// Password toggle functionality (using event delegation)
document.addEventListener('click', function(event) {
    // For add form
    if (event.target.classList.contains('toggle-pass')) {
        const input = event.target.previousElementSibling;
        togglePasswordVisibility(input, event.target);
    }
    if (event.target.classList.contains('toggle-pass-confirm')) {
        const input = event.target.previousElementSibling;
        togglePasswordVisibility(input, event.target);
    }
    
    // For edit forms
    if (event.target.classList.contains('toggle-pass-edit')) {
        const userId = event.target.dataset.userid;
        const input = document.querySelector(`.pass-input-edit[data-userid="${userId}"]`);
        togglePasswordVisibility(input, event.target);
    }
    if (event.target.classList.contains('toggle-pass-confirm-edit')) {
        const userId = event.target.dataset.userid;
        const input = document.querySelector(`.pass-input-confirm-edit[data-userid="${userId}"]`);
        togglePasswordVisibility(input, event.target);
    }
});

function togglePasswordVisibility(input, icon) {
    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);
    icon.classList.toggle('isax-eye');
    icon.classList.toggle('isax-eye-slash');
}

// Reset forms when modals are closed
document.getElementById('add_modal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('addUserForm').reset();
    resetAddFormErrors();
});

// For edit modals (using event delegation)
document.addEventListener('hidden.bs.modal', function(event) {
    if (event.target.id.startsWith('editModal')) {
        const userId = event.target.id.replace('editModal', '');
        resetEditFormErrors(userId);
    }
});
</script>
<script>
    $(document).ready(function () {
   

    // === Allow only text (no digits) ===
    $('#name').on('input', function () {
        this.value = this.value.replace(/[0-9]/g, '');
    });
    $('#phone_number').on('input', function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });
   $('#namee').on('input', function () {
        this.value = this.value.replace(/[0-9]/g, '');
    });
    $('#phone_numbere').on('input', function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });
});

</script>
<script>
function resetAddFormErrors() {
    document.querySelectorAll('.error-text').forEach(el => el.textContent = '');
}

async function validateAddUserForm() {
    let isValid = true;
    resetAddFormErrors();

    const name = document.querySelector('#addUserForm input[name="name"]').value.trim();
    const email = document.querySelector('#addUserForm input[name="email"]').value.trim();
    const phone = document.querySelector('#addUserForm input[name="phone_number"]').value.trim();
    const password = document.querySelector('#addUserForm input[name="password"]').value;
    const confirmPassword = document.querySelector('#addUserForm input[name="cpassword"]').value;
    const role = document.querySelector('#addUserForm select[name="role_id"]').value;

    // Name validation
    if (name === '') {
        document.getElementById('add_nameError').textContent = 'Name is Required';
        isValid = false;
    }

    // Email validation
    // const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
    // if (email === '') {
    //     document.getElementById('add_emailError').textContent = 'Email is Required';
    //     isValid = false;
    // } else if (!emailPattern.test(email)) {
    //     document.getElementById('add_emailError').textContent = 'Invalid email format';
    //     isValid = false;
    // }
    // Email validation
    const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
    if (email === '') {
        document.getElementById('add_emailError').textContent = 'Email is Required';
        isValid = false;
    } else if (!emailPattern.test(email)) {
        document.getElementById('add_emailError').textContent = 'Invalid email format';
        isValid = false;
    } else {
        // 🔑 AJAX check for duplicate email
        const res = await fetch('process/check_duplicate_user_email.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'email=' + encodeURIComponent(email)
        });
        const data = await res.json();
        if (data.exists) {
            document.getElementById('add_emailError').textContent = 'This email is already registered';
            isValid = false;
        }
    }
    // Phone validation (10 digits only)
    const phonePattern = /^[0-9]{10}$/;
    if (phone === '') {
        document.getElementById('add_phoneError').textContent = 'Phone Number is Required';
        isValid = false;
    } else if (!phonePattern.test(phone)) {
        document.getElementById('add_phoneError').textContent = 'Phone must be 10 digits';
        isValid = false;
    }

    // Password validation
    if (password === '') {
        document.getElementById('add_passwordError').textContent = 'Password is Required';
        isValid = false;
    } else if (password.length < 6) {
        document.getElementById('add_passwordError').textContent = 'Password must be at least 6 characters';
        isValid = false;
    }

    // Confirm password validation
    if (confirmPassword === '') {
        document.getElementById('add_cpasswordError').textContent = 'Please confirm your password';
        isValid = false;
    } else if (password !== confirmPassword) {
        document.getElementById('add_cpasswordError').textContent = 'Passwords do not match';
        isValid = false;
    }

    // Role validation
    if (role === '') {
        document.getElementById('add_roleError').textContent = 'Role is Required';
        isValid = false;
    }

    return isValid;
}

// -------- Real-time Validation --------
document.querySelector('#addUserForm input[name="email"]').addEventListener('input', function() {
    const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
    if (!this.value.trim()) {
        document.getElementById('add_emailError').textContent = 'Email is Required';
    } else if (!emailPattern.test(this.value.trim())) {
        document.getElementById('add_emailError').textContent = 'Invalid email format';
    } else {
        document.getElementById('add_emailError').textContent = '';
    }
});

document.querySelector('#addUserForm input[name="phone_number"]').addEventListener('input', function() {
    const phonePattern = /^[0-9]{10}$/;
    if (!this.value.trim()) {
        document.getElementById('add_phoneError').textContent = 'Phone Number is Required';
    } else if (!phonePattern.test(this.value.trim())) {
        document.getElementById('add_phoneError').textContent = 'Phone must be 10 digits';
    } else {
        document.getElementById('add_phoneError').textContent = '';
    }
});
</script>

<script>
    // Function to validate file type
function validateImage(file, errorElementId, previewElementId) {
    const allowedTypes = ['image/jpeg', 'image/png'];
    const errorElement = $(errorElementId);

    // File type validation
    if (!allowedTypes.includes(file.type)) {
        errorElement.text('Only JPG and PNG files are allowed');
        return false;
    }

    // File size validation (5MB limit)
    if (file.size > 5 * 1024 * 1024) {
        errorElement.text('File size must be less than 5MB');
        return false;
    }

    // If valid, show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        $(previewElementId).html(`<img src="${e.target.result}" class="avatar avatar-xl" alt="Preview">`);
        errorElement.text('');
    }
    reader.readAsDataURL(file);

    return true;
}

// Add Modal
$('#add_image').change(function() {
    const file = this.files[0];
    if (file) {
        if (!validateImage(file, '#add_image_error', '#add_image_preview')) {
            $(this).val('');
        }
    }
});

// Edit Modal
$('#edit_image').change(function() {
    const file = this.files[0];
    if (file) {
        if (!validateImage(file, '#edit_image_error', '#edit_image_preview')) {
            $(this).val('');
        }
    }
});

</script>
<script>
document.querySelectorAll('.edit-user-form').forEach(form => {
    const userId = form.dataset.userid;

    // Email real-time validation
    form.querySelector('input[name="email"]').addEventListener('input', function() {
        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
        const errorEl = document.getElementById(`edit_emailError_${userId}`);
        if (!this.value.trim()) {
            errorEl.textContent = 'Email is Required';
        } else if (!emailPattern.test(this.value.trim())) {
            errorEl.textContent = 'Invalid email format';
        } else {
            errorEl.textContent = '';
        }
    });

    // Phone real-time validation
    const phoneInput = form.querySelector('input[name="phone_number"]');
    if (phoneInput) {
        // Create error span dynamically if missing
        let phoneErrorEl = document.getElementById(`edit_phoneError_${userId}`);
        if (!phoneErrorEl) {
            phoneErrorEl = document.createElement('span');
            phoneErrorEl.id = `edit_phoneError_${userId}`;
            phoneErrorEl.className = 'text-danger error-msg';
            phoneInput.insertAdjacentElement('afterend', phoneErrorEl);
        }

        phoneInput.addEventListener('input', function() {
            const phonePattern = /^[0-9]{10}$/;
            if (!this.value.trim()) {
                phoneErrorEl.textContent = 'Phone Number is Required';
            } else if (!phonePattern.test(this.value.trim())) {
                phoneErrorEl.textContent = 'Phone must be 10 digits';
            } else {
                phoneErrorEl.textContent = '';
            }
        });
    }
});
</script>

<script>
$(document).ready(function() {
    let previousUserSelection = <?php echo json_encode($user_filter); ?> || [];

    // Initialize the offcanvas with current filter values when it opens
    $('#customcanvas').on('show.bs.offcanvas', function() {
        // Set status checkboxes based on PHP filter
        const statusFilter = <?php echo json_encode($status_filter); ?> || [];
        $('.status-checkbox').each(function() {
            $(this).prop('checked', statusFilter.includes($(this).val()));
        });
        updateStatusDropdownText();

        // Set user checkboxes based on previous selection
        $('.user-check').each(function() {
            $(this).prop('checked', previousUserSelection.includes($(this).val()));
        });
        updateUserDropdownText();
    });

    // Save current user selection when dropdown opens
    $('#userDropdownButton').on('click', function () {
        previousUserSelection = $('.user-check:checked').map(function () {
            return $(this).val();
        }).get();
    });

    // Update status dropdown text
   // Update user dropdown text with truncation
function updateUserDropdownText(limit = 3) {
    const checkedUsers = $('.user-check:checked');
    let userText = "Select";

    if (checkedUsers.length > 0) {
        let names = [];
        checkedUsers.each(function () {
            // Only take the text after the image
            let labelText = $(this).closest('label').clone().children().remove().end().text().trim();
            names.push(labelText);
        });

        if (names.length > limit) {
            userText = names.slice(0, limit).join(", ") + " +" + (names.length - limit);
        } else {
            userText = names.join(", ");
        }
    }
    $('#userDropdownButton').text(userText);
}

    // Update user dropdown text
  function updateStatusDropdownText() {
    const checkedStatuses = $('.status-checkbox:checked');
    let statusText = "Select";

    if (checkedStatuses.length === 1) {
        statusText = checkedStatuses.val() == '1' ? 'Active' : 'Inactive';
    } else if (checkedStatuses.length > 1) {
        statusText = checkedStatuses.length + " Selected";
    }
    $('#statusDropdownButton').text(statusText);
}

    // Update dropdown text when checkboxes change
    $('.status-checkbox').change(updateStatusDropdownText);
    $('.user-check').change(updateUserDropdownText);

    // Select all users functionality
    $('.user-select-all').change(function() {
        $('.user-check').prop('checked', $(this).prop('checked'));
        updateUserDropdownText();
    });

    // User search functionality
    $('.user-search').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        $('.user-item').each(function() {
            const userName = $(this).text().toLowerCase();
            $(this).toggle(userName.includes(searchText));
        });
    });

    // Apply user selection (save changes)
    $('.user-apply').click(function() {
        previousUserSelection = $('.user-check:checked').map(function () {
            return $(this).val();
        }).get();
        updateUserDropdownText();
        $('[data-bs-toggle="dropdown"]').dropdown('hide');
    });

    // Cancel user selection (restore previous state)
    $('.close-filter').click(function() {
        $('.user-check').prop('checked', false);
        previousUserSelection.forEach(function(val) {
            $('.user-check[value="' + val + '"]').prop('checked', true);
        });
        updateUserDropdownText();
        $('[data-bs-toggle="dropdown"]').dropdown('hide');
    });

    // Reset user selection
    $('.user-reset').click(function() {
        $('.user-check, .user-select-all').prop('checked', false);
        updateUserDropdownText();
    });
});
</script>


<?php if (isset($_GET['open']) && $_GET['open'] === 'add_user') { ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var myModal = new bootstrap.Modal(document.getElementById('add_modal'));
        myModal.show();
    });
</script>
<?php } ?>

</body>
</html>