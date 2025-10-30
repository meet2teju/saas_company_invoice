<?php include 'layouts/session.php';
include '../config/config.php';


?>

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

			<!-- Start conatiner -->
            <div class="content content-two">

                <!-- Page Header -->
                <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <h6>Roles</h6>
                    </div>
                    <?php
                    

                    // Fetch roles
                    $selectedRoleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                    $query = "SELECT * FROM user_role ORDER BY name ASC";
                    $result = mysqli_query($conn, $query);
                    $selectedRoleName = "Select Role";
                        if ($selectedRoleId > 0) {
                            $r = mysqli_query($conn, "SELECT name FROM user_role WHERE id=$selectedRoleId");
                            if ($r && mysqli_num_rows($r) > 0) {
                                $role = mysqli_fetch_assoc($r);
                                $selectedRoleName = htmlspecialchars($role['name']);
                            }
                        }
                    ?>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                        <div class="dropdown me-2">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                Role : <span class="fw-normal ms-1"><?= $selectedRoleName ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <li>
                                    <a href="permission.php?id=<?=$row['id']?>" class="dropdown-item">
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </a>
                                </li>
                            <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- End Page Header -->
                       <!-- <span style="font-size:20px; color:black; font-weight:bold"><?= $selectedRoleName ?>&nbsp;&nbsp;Role</span> -->
                     
                <!-- Start Table List -->
                <form method="POST" action="process/action_permision_access.php"> <!-- Wrap entire accordion in a form -->
                    <input type="hidden" name="role_id" value="<?= $_REQUEST['id'] ?>">
                <div class="">
                    <div class="accordion" id="accordionExample">
                         <?php
                          $sel_pmenu = "SELECT * from crm_admin_menu where pmenu='0' AND is_deleted='0'";
                          $que_pmenu = mysqli_query($conn, $sel_pmenu) or die(mysqli_error($conn));
                          while ($fet_pmenu = mysqli_fetch_array($que_pmenu)) {
                              $mname = $fet_pmenu['mname'];
                              $is_access = check_is_access($mname, $_REQUEST['id']);
                          ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $fet_pmenu['mid'] ?>" aria-expanded="true" aria-controls="collapse<?= $fet_pmenu['mid'] ?>">                                    
                                    <span class="fs-18 fw-bold"><?php echo $mname; ?></span>
                                </button>                                
                            </h2>
                            <div id="collapse<?= $fet_pmenu['mid'] ?>" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <!-- Table List -->
                                    <div class="table-responsive table-nowrap">
                                        <table class="table border mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="w-50">Module</th>
                                                    <th>Create</th>
                                                    <th>Edit</th>
                                                    <th>Delete</th>
                                                    <th>View</th>
                                                    <th>Allow All</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $sel_pmenuu = "SELECT * from crm_admin_menu where org_id='".$fet_pmenu['mid']."' AND is_deleted='0'";
                                            $que_pmenuu = mysqli_query($conn, $sel_pmenuu) or die(mysqli_error($conn));
                                            while ($fet_pmenuu = mysqli_fetch_array($que_pmenuu)) {
                                                $mnameu = $fet_pmenuu['mname'];
                                                $mtitle = $fet_pmenuu['mtitle'];
                                                $sel_smenu = "SELECT * from crm_admin_menu where pmenu='" . $fet_pmenuu['mid'] . "' AND is_deleted='0' LIMIT 4";
                                                $que_smenu = mysqli_query($conn, $sel_smenu);
                                                $has_children = (mysqli_num_rows($que_smenu) > 0);

                                                if ($has_children) {
                                                    $mname_add = "add_" . $fet_pmenuu['mname'];
                                                    $mname_edit = "edit_" . $fet_pmenuu['mname'];
                                                    $mname_delete = "delete_" . $fet_pmenuu['mname'];
                                                    $mname_view = "view_" . $fet_pmenuu['mname'];

                                                    $is_access_add = check_is_access($mname_add, $_REQUEST['id']);
                                                    $is_access_edit = check_is_access($mname_edit, $_REQUEST['id']);
                                                    $is_access_delete = check_is_access($mname_delete, $_REQUEST['id']);
                                                    $is_access_view = check_is_access($mname_view, $_REQUEST['id']);
                                                }
                                            ?>
                                                <tr>
                                                    <td><?php echo $mnameu; ?></td>
                                                   <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input create-checkbox" type="checkbox" 
                                                            name="add_<?php echo $mtitle; ?>" 
                                                            <?= check_is_access("add_".$mtitle, $_REQUEST['id']) ? 'checked' : '' ?>>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input edit-checkbox" type="checkbox" 
                                                            name="edit_<?php echo $mtitle; ?>" 
                                                            <?= check_is_access("edit_".$mtitle, $_REQUEST['id']) ? 'checked' : '' ?>>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input delete-checkbox" type="checkbox" 
                                                            name="delete_<?php echo $mtitle; ?>" 
                                                            <?= check_is_access("delete_".$mtitle, $_REQUEST['id']) ? 'checked' : '' ?>>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input view-checkbox" type="checkbox" 
                                                            name="view_<?php echo $mtitle; ?>" 
                                                            <?= check_is_access("view_".$mtitle, $_REQUEST['id']) ? 'checked' : '' ?>>
                                                    </div>
                                                </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input allow-all"  name="allow_all<?php echo $mtitle; ?>"  type="checkbox" data-module="<?= $mtitle?>"
                                                            <?php
                                                            if ($has_children && $is_access_add && $is_access_edit && $is_access_delete && $is_access_view) {
                                                                echo 'checked';
                                                            }
                                                            ?>>
                                                        </div>
                                                    </td>

                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /Table List -->
                                </div>
                            </div>
                        </div>
                        <?php } ?>             
                    </div>

                    <!-- Save & Cancel Buttons -->
                    <div class="mt-4 text-center">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="roles-permissions.php" class="btn btn-light ms-2">Cancel</a>
                    </div>

                </div>
                </form>
                <!-- End Table List -->

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

<?php
function check_is_access($mname, $role_id = "") {
    global $conn;
    if (empty($role_id) && isset($_REQUEST['id'])) {
        $role_id = $_REQUEST['id'];
    }

    $sel = "SELECT is_access FROM crm_user_access WHERE role_id='$role_id' AND mtitle='$mname' LIMIT 1";
    $qry = mysqli_query($conn, $sel);
    if(mysqli_num_rows($qry) > 0) {
        $fet = mysqli_fetch_assoc($qry);
        return $fet['is_access'];
    }
    return 0;
}
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.allow-all').forEach(function (allowAllCheckbox) {
        const module = allowAllCheckbox.dataset.module;

        // Select individual checkboxes for the same module
        const add = document.querySelector(`input[name="add_${module}"]`);
        const edit = document.querySelector(`input[name="edit_${module}"]`);
        const del = document.querySelector(`input[name="delete_${module}"]`);
        const view = document.querySelector(`input[name="view_${module}"]`);
        const children = [add, edit, del, view].filter(Boolean);

        // Allow All toggle -> update children
        allowAllCheckbox.addEventListener('change', function () {
            children.forEach(chk => chk.checked = this.checked);
        });

        // Child toggle -> update allow all
        children.forEach(chk => {
            chk.addEventListener('change', function () {
                const anyChecked = children.some(cb => cb.checked);
                allowAllCheckbox.checked = anyChecked;
            });
        });

        // On page load, set Allow All if any child is checked
        const anyCheckedOnLoad = children.some(cb => cb.checked);
        allowAllCheckbox.checked = anyCheckedOnLoad;
    });
});
</script>


