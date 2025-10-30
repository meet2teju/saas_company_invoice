<?php 
include 'layouts/session.php';
if (isset($_SESSION['crm_is_login']) && $_SESSION['crm_is_login'] === 1) {
    header("Location: admin-dashboard.php");
    exit;
}

include '../config/config.php';

// Check for existing email error
$email_error = '';
if (isset($_SESSION['register_error']) && $_SESSION['register_error'] === 'email_exists') {
    $email_error = 'Email already exists. Please use a different email.';
    unset($_SESSION['register_error']);
}

// Preserve form data if there was an error
$name = isset($_SESSION['form_data']['name']) ? $_SESSION['form_data']['name'] : '';
$email = isset($_SESSION['form_data']['email']) ? $_SESSION['form_data']['email'] : '';
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
                        
                        <form action="process/action_register.php" method="POST" id="registerForm">
                            <div class="card border-0 p-lg-3 shadow-lg">
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <h5 class="mb-2">Create Account</h5>
                                        <p class="mb-0">Please enter your details to create an account</p>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Full Name<span style="color: red;">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text border-end-0"><i class="isax isax-user"></i></span>
                                            <input type="text" name="name" id="name" class="form-control border-start-0 ps-0" placeholder="Enter Full Name" value="<?php echo htmlspecialchars($name); ?>" required>
                                        </div>
                                        <span id="name-error" style="color:red; display:none;" class="small mt-1">Please enter your name.</span> 
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email Address<span style="color: red;">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text border-end-0"><i class="isax isax-sms-notification"></i></span>
                                            <input type="email" name="email" id="email" class="form-control border-start-0 ps-0" placeholder="Enter Email Address" value="<?php echo htmlspecialchars($email); ?>" required>
                                        </div>
                                        <span id="email-error" style="color:red; <?php echo $email_error ? '' : 'display:none;' ?>" class="small mt-1">
                                            <?php echo $email_error ? $email_error : 'Invalid email address.' ?>
                                        </span> 
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password<span style="color: red;">*</span></label>
                                        <div class="pass-group input-group">
                                            <span class="input-group-text border-end-0"><i class="isax isax-lock"></i></span>
                                            <span class="isax toggle-password isax-eye-slash"></span>
                                            <input type="password" name="password" id="password" class="pass-inputs form-control border-start-0 ps-0" placeholder="****************" required>
                                        </div>
                                        <span id="password-error" style="color:red; display:none;" class="small mt-1">Password must be at least 8 characters.</span>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password<span style="color: red;">*</span></label>
                                        <div class="pass-group input-group">
                                            <span class="input-group-text border-end-0"><i class="isax isax-lock"></i></span>
                                            <span class="isax toggle-confirm-password isax-eye-slash"></span>
                                            <input type="password" name="confirm_password" id="confirm_password" class="pass-inputs form-control border-start-0 ps-0" placeholder="****************" required>
                                        </div>
                                        <span id="confirm-password-error" style="color:red; display:none;" class="small mt-1">Passwords do not match.</span>
                                    </div>

                                    <div class="mb-1">
                                        <button type="submit" name="register" class="btn bg-primary-gradient text-white w-100">Create Account</button>
                                    </div>

                                    <div class="text-center">
                                        <h6 class="fw-normal fs-14 text-dark mb-0">Already have an account?
                                            <a href="index.php" class="hover-a"> Sign In</a>
                                        </h6>
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
    
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Toggle password visibility
        const togglePassword = document.querySelector(".toggle-password");
        const passwordInput = document.getElementById("password");
        
        const toggleConfirmPassword = document.querySelector(".toggle-confirm-password");
        const confirmPasswordInput = document.getElementById("confirm_password");

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener("click", function () {
                const isHidden = passwordInput.type === "password";
                passwordInput.type = isHidden ? "text" : "password";
                togglePassword.classList.toggle("isax-eye-slash", !isHidden);
                togglePassword.classList.toggle("isax-eye", isHidden);
            });
        }

        if (toggleConfirmPassword && confirmPasswordInput) {
            toggleConfirmPassword.addEventListener("click", function () {
                const isHidden = confirmPasswordInput.type === "password";
                confirmPasswordInput.type = isHidden ? "text" : "password";
                toggleConfirmPassword.classList.toggle("isax-eye-slash", !isHidden);
                toggleConfirmPassword.classList.toggle("isax-eye", isHidden);
            });
        }

        // Form validation
        const registerForm = document.getElementById("registerForm");
        
        if (registerForm) {
            registerForm.addEventListener("submit", function(event) {
                let isValid = true;
                
                // Validate name
                const nameInput = document.getElementById("name");
                const nameError = document.getElementById("name-error");
                if (nameInput.value.trim() === "") {
                    nameError.style.display = "block";
                    isValid = false;
                } else {
                    nameError.style.display = "none";
                }
                
                // Validate email
                const emailInput = document.getElementById("email");
                const emailError = document.getElementById("email-error");
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailInput.value)) {
                    emailError.style.display = "block";
                    emailError.textContent = "Invalid email address.";
                    isValid = false;
                } else {
                    emailError.style.display = "none";
                }
                
                // Validate password
                const passwordInput = document.getElementById("password");
                const passwordError = document.getElementById("password-error");
                if (passwordInput.value.length < 8) {
                    passwordError.style.display = "block";
                    isValid = false;
                } else {
                    passwordError.style.display = "none";
                }
                
                // Validate confirm password
                const confirmPasswordInput = document.getElementById("confirm_password");
                const confirmPasswordError = document.getElementById("confirm-password-error");
                if (confirmPasswordInput.value !== passwordInput.value) {
                    confirmPasswordError.style.display = "block";
                    isValid = false;
                } else {
                    confirmPasswordError.style.display = "none";
                }
                
                if (!isValid) {
                    event.preventDefault();
                }
            });
        }
    });
    </script>
</body>
</html>