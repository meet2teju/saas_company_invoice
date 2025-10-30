<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $rawPassword = trim($_POST['password']);
    $password = md5($rawPassword);

    $sql = "SELECT login.*, user_role.name AS role_name 
            FROM login 
            LEFT JOIN user_role ON login.role_id = user_role.id 
            WHERE login.email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if ($user['password'] === $password) {
            
            // ✅ Check status before allowing login
            if ($user['status'] == 1) {

                if (isset($_POST['remember'])) {
                    setcookie('email', $email, time() + (86400 * 30), "/");
                    setcookie('password', $rawPassword, time() + (86400 * 30), "/");
                } else {
                    setcookie('email', '', time() - 3600, "/");
                    setcookie('password', '', time() - 3600, "/");
                }
                
                $_SESSION['crm_is_login'] = 1;
                $_SESSION['crm_user_id'] = $user['id'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['crm_user_role'] = $user['role_name']; 
                $_SESSION['crm_user_name'] = $user['name'];
                $_SESSION['crm_user_email'] = $user['email'];
                $_SESSION['crm_user_phone'] = $user['phone_number'];
                $_SESSION['crm_profile_img'] = $user['profile_img'];

                $role = strtolower($user['role_name']); 
                // if ($role === 'admin') {
                //     header("Location: ../admin-dashboard.php");
                // } elseif ($role === !'admin') {
                //     header("Location: ../customer-dashboard.php");
                // } else {
                //     $_SESSION['login_error'] = 'unauthorized';
                //     header("Location: ../login.php");
                // }
                // exit;
                $role = strtolower($user['role_name']); 
                if ($role === 'admin') {
                    header("Location: ../admin-dashboard.php");
                } else {
                    // Any role other than 'admin'
                    header("Location: ../customer-dashboard.php");
                }
                exit;


            } else {
                
                $_SESSION['login_error'] = 'inactive';
                header("Location: ../login.php");
                exit;
            }

        } else {
            $_SESSION['login_error'] = 'password';
            header("Location: ../login.php");
            exit;
        }
    } else {
        $_SESSION['login_error'] = 'email';
        header("Location: ../login.php");
        exit;
    }
}
?>
