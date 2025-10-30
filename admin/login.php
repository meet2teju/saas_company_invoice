<?php include 'layouts/session.php';
if (isset($_SESSION['crm_is_login']) && $_SESSION['crm_is_login'] === 1) {
    header("Location: admin-dashboard.php");
    exit;
}

?>
<?php
// session_start();
include '../config/config.php';

// Initialize variables
$email = '';
$password = '';

// Load cookies if they exist
// if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
//     $email = $_COOKIE['email'];
//      $password = $_COOKIE['password'];
// }

// session_start();
// if (isset($_SESSION['crm_user_id'])) {
//     header("Location: admin-dashboard.php"); // already logged in
//     exit();
// }

// Load cookies if available
$email = isset($_COOKIE['email']) ? $_COOKIE['email'] : '';
$password = isset($_COOKIE['password']) ? $_COOKIE['password'] : '';
$remember_checked = ($email && $password) ? 'checked' : '';


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layouts/title-meta.php'; ?> 
    <?php include 'layouts/head-css.php'; ?>
</head>

<body class="bg-white">
    <div class="main-wrapper auth-bg">

        <div class="container-fuild">
            <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
                <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
                    <div class="col-lg-4 mx-auto">

                  
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                        <?= $_SESSION['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>
                        <form action="process/action_login.php" method="POST" class="d-flex justify-content-center align-items-center">
                            <div class="d-flex flex-column justify-content-lg-center p-4 p-lg-0 pb-0 flex-fill">
                                <div class="mx-auto mb-5 text-center"></div>
                                <div class="card border-0 p-lg-3 shadow-lg">
                                    <div class="card-body">
                                                <?php if (isset($_SESSION['login_error']) && $_SESSION['login_error'] === 'inactive'): ?>
    <div class="alert alert-danger">Your account is inactive. You cannot login.</div>
<?php endif; ?>
                                        <div class="text-center mb-3">
                                            <h5 class="mb-2">Sign In</h5>
                                            <p class="mb-0">Please enter below details to access the dashboard</p>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Email Address<span style="color: red;">*</span></label></label>
                                            <div class="input-group">
												<span class="input-group-text border-end-0"><i class="isax isax-sms-notification"></i></span>
												<input type="text" name="email" class="form-control border-start-0 ps-0" placeholder="Enter Email Address" value="<?php echo htmlspecialchars($email); ?>">
											</div>
											<span id="email-error" style="color:red; display:none;" class="small mt-1">Invalid email address.</span> 

                                        </div>

                                     <div class="mb-3">
											<label class="form-label">Password<span style="color: red;">*</span></label></label>
											<div class="pass-group input-group">
												<span class="input-group-text border-end-0"><i class="isax isax-lock"></i></span>
												<span class="isax toggle-password isax-eye-slash"></span>
												<input type="password" name="password" class="pass-inputs form-control border-start-0 ps-0" placeholder="****************"  value="<?php echo htmlspecialchars($password); ?>">
											</div>
											<span id="password-error" style="color:red; display:none;" class="small mt-1">Incorrect password.</span>
										</div>


                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="form-check form-check-md mb-0">
                                                <input class="form-check-input" id="remember_me" name="remember" type="checkbox"
                                                    <?php if (isset($_COOKIE['email'])) echo "checked"; ?>>
                                                <label for="remember_me" class="form-check-label mt-0">Remember Me</label>
                                            </div>
                                            <div class="text-end">
                                                <a href="forgot-password.php">Forgot Password</a>
                                            </div>
                                        </div>

                                        <div class="mb-1">
                                            <button type="submit" name="login" class="btn bg-primary-gradient text-white w-100">Sign In</button>
                                        </div>

                                        <div class="text-center">
                                            <h6 class="fw-normal fs-14 text-dark mb-0">Don’t have an account yet?
                                                <a href="register.php" class="hover-a"> Register</a>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layouts/vendor-scripts.php'; ?>
</body>

</html>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.querySelector(".toggle-password");
    const passwordInput = document.querySelector(".pass-inputs");

    toggle.addEventListener("click", function () {
      const isHidden = passwordInput.type === "password";
      passwordInput.type = isHidden ? "text" : "password";
      toggle.classList.toggle("isax-eye-slash", !isHidden);
      toggle.classList.toggle("isax-eye", isHidden);
    });



      <?php if (isset($_SESSION['login_error'])): ?>
        const errorType = "<?php echo $_SESSION['login_error']; ?>";
        if (errorType === "email") {
            document.getElementById("email-error").style.display = "block";
        } else if (errorType === "password") {
            document.getElementById("password-error").style.display = "block";
        }
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>
  });
</script>
