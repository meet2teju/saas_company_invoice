<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include "../../config/config.php";
$role_id = $_SESSION['role_id'];
if (!isset($_REQUEST['role_id'])) {
    $_SESSION['error'] = "Role ID is required";
    header("Location: ../permission.php");
    exit;
}

$role_id = mysqli_real_escape_string($conn, $_REQUEST['role_id']);
$created_at = date('Y-m-d H:i:s');
$created_by = $_SESSION['crm_user_id'] ?? 0;
$updated_at = $created_at;
$updated_by = $created_by;

// Fetch all modules
$modules = [];
$res = mysqli_query($conn, "SELECT * FROM crm_admin_menu WHERE is_deleted = 0");
while ($row = mysqli_fetch_assoc($res)) {
    $modules[] = $row;
}

// Track parent modules that need is_access = 1
$parentModulesToSet = [];

// Loop through each module
foreach ($modules as $row) {
    $mid     = $row['mid'];
    $mname   = $row['mname'];   // e.g. "Add Product"
    $mtitle  = $row['mtitle'];  // e.g. "add_product"
    $is_access = isset($_REQUEST[$mtitle]) ? 1 : 0;

    // Store parent if child is checked (like add/edit/delete/view)
    if (
        preg_match('/^(add|edit|delete|view)_(.+)$/', $mtitle, $matches) &&
        $is_access === 1
    ) {
        $parent_name = $matches[2]; // e.g. "product"
        $parentModulesToSet[$parent_name] = 1;
    }

    // Insert or update record
    $check = mysqli_query($conn, "SELECT id FROM crm_user_access WHERE role_id='$role_id' AND mtitle='$mtitle'");
    if (mysqli_num_rows($check) > 0) {
        $update = "UPDATE crm_user_access SET 
            is_access = '$is_access',
            updated_at = '$updated_at',
            updated_by = '$updated_by'
            WHERE role_id = '$role_id' AND mtitle = '$mtitle'";
        mysqli_query($conn, $update);
    } else {
        $insert = "INSERT INTO crm_user_access 
            (role_id, mid, mname, mtitle, is_access, created_at, created_by, updated_at, updated_by)
            VALUES
            ('$role_id', '$mid', '$mname', '$mtitle', '$is_access', '$created_at', '$created_by', '$updated_at', '$updated_by')";
        mysqli_query($conn, $insert);
    }
}

// Now ensure parent modules have is_access = 1
// Ensure parent and grandparent modules have is_access = 1
foreach ($parentModulesToSet as $parentMtitle => $val) {
    // Find parent module (e.g. 'product')
    $q = mysqli_query($conn, "SELECT * FROM crm_admin_menu WHERE mtitle='$parentMtitle' AND is_deleted=0 LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $parent = mysqli_fetch_assoc($q);
        $parent_mid    = $parent['mid'];
        $parent_mname  = $parent['mname'];
        $parent_mtitle = $parent['mtitle'];
        $org_id        = $parent['org_id']; // This links to grandparent

        // Set parent access = 1
        $check = mysqli_query($conn, "SELECT id FROM crm_user_access WHERE role_id='$role_id' AND mtitle='$parent_mtitle'");
        if (mysqli_num_rows($check) > 0) {
            $update = "UPDATE crm_user_access SET 
                is_access = 1,
                updated_at = '$updated_at',
                updated_by = '$updated_by'
                WHERE role_id = '$role_id' AND mtitle = '$parent_mtitle'";
            mysqli_query($conn, $update);
        } else {
            $insert = "INSERT INTO crm_user_access 
                (role_id, mid, mname, mtitle, is_access, created_at, created_by, updated_at, updated_by)
                VALUES
                ('$role_id', '$parent_mid', '$parent_mname', '$parent_mtitle', '1', '$created_at', '$created_by', '$updated_at', '$updated_by')";
            mysqli_query($conn, $insert);
        }

        // Now find and set grandparent module (via org_id)
        if (!empty($org_id)) {
            $gq = mysqli_query($conn, "SELECT * FROM crm_admin_menu WHERE mid='$org_id' AND is_deleted=0 LIMIT 1");
            if ($gq && mysqli_num_rows($gq) > 0) {
                $grand = mysqli_fetch_assoc($gq);
                $g_mid    = $grand['mid'];
                $g_mname  = $grand['mname'];
                $g_mtitle = $grand['mtitle'];

                // Set grandparent access = 1
                $check = mysqli_query($conn, "SELECT id FROM crm_user_access WHERE role_id='$role_id' AND mtitle='$g_mtitle'");
                if (mysqli_num_rows($check) > 0) {
                    $update = "UPDATE crm_user_access SET 
                        is_access = 1,
                        updated_at = '$updated_at',
                        updated_by = '$updated_by'
                        WHERE role_id = '$role_id' AND mtitle = '$g_mtitle'";
                    mysqli_query($conn, $update);
                } else {
                    $insert = "INSERT INTO crm_user_access 
                        (role_id, mid, mname, mtitle, is_access, created_at, created_by, updated_at, updated_by)
                        VALUES
                        ('$role_id', '$g_mid', '$g_mname', '$g_mtitle', '1', '$created_at', '$created_by', '$updated_at', '$updated_by')";
                    mysqli_query($conn, $insert);
                }
            }
        }
    }
}


$_SESSION['msg'] = "Permissions updated successfully.";
header("Location: ../permission.php?id=$role_id");
exit;
