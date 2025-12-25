<?php
// Start output buffering at the VERY beginning
ob_start();

session_start();

// Turn off error display for production, but log errors
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

include '../../config/config.php';

// Check for any output before this point
if (ob_get_length() > 0) {
    ob_clean();
}

// Include Composer's autoloader
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);
    $agree_terms = $_POST['agree_terms'] ?? '0';

    $_SESSION['old'] = $_POST;

    $errors = [];

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($password) || strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    }

    if ($password !== $cpassword) {
        $errors['cpassword'] = "Confirm Password do not match.";
    }

    // Validate terms agreement
    if (!isset($_POST['agree_terms']) || $_POST['agree_terms'] != '1') {
        $errors['agree_terms'] = "You must agree to the Terms of Service and Privacy Policy";
    }

    // Check if email already exists
    $checkQuery = "SELECT * FROM login WHERE email = ?";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $checkResult = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        $errors['email'] = "Email already registered.";
    }
    mysqli_stmt_close($stmt);

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        // Clear buffer before redirect
        ob_end_clean();
        header("Location: ../register.php");
        exit();
    }

    // Start transaction for data consistency
    mysqli_begin_transaction($conn);

    try {
        // Generate organization name from user's name
        $orgName = $name . "'s Organization";
        
        // Insert into organization table first
        $orgInsertQuery = "INSERT INTO organizations (company_name, email, created_at, updated_at) VALUES (?, ?, NOW(), NOW())";
        $stmtOrg = mysqli_prepare($conn, $orgInsertQuery);
        mysqli_stmt_bind_param($stmtOrg, "ss", $orgName, $email);
        
        if (!mysqli_stmt_execute($stmtOrg)) {
            throw new Exception("Failed to create organization");
        }
        
        $org_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmtOrg);

        // Save user with organization ID
        $hashedPassword = md5($password);
        $insertQuery = "INSERT INTO login (name, email, password, org_id, role_id, created_at) VALUES (?, ?, ?, ?, 1, NOW())";
        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $hashedPassword, $org_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to create user");
        }
        
        $user_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // Update organization with created_by
        $updateOrgQuery = "UPDATE organizations SET created_by = ? WHERE id = ?";
        $stmtUpdate = mysqli_prepare($conn, $updateOrgQuery);
        mysqli_stmt_bind_param($stmtUpdate, "ii", $user_id, $org_id);
        mysqli_stmt_execute($stmtUpdate);
        mysqli_stmt_close($stmtUpdate);
        
        // ==================== CREATE DEFAULT COMPANY PROFILE ====================
        // Get default currency (INR or first available)
        $defaultCurrencyQuery = "SELECT id FROM currency WHERE isocode = 'INR' LIMIT 1";
        $currencyResult = mysqli_query($conn, $defaultCurrencyQuery);

        if (mysqli_num_rows($currencyResult) > 0) {
            $currencyRow = mysqli_fetch_assoc($currencyResult);
            $default_currency_id = $currencyRow['id'];
        } else {
            // Fallback: get first currency
            $fallbackCurrencyQuery = "SELECT id FROM currency ORDER BY id LIMIT 1";
            $fallbackResult = mysqli_query($conn, $fallbackCurrencyQuery);
            if (mysqli_num_rows($fallbackResult) > 0) {
                $fallbackRow = mysqli_fetch_assoc($fallbackResult);
                $default_currency_id = $fallbackRow['id'];
            } else {
                $default_currency_id = 1;
            }
        }

        // Insert default company profile
        $companyProfileQuery = "INSERT INTO company_info 
            (user_id, name, email, currency_symbol_id, org_id, created_at, created_by, status)
        VALUES (?, ?, ?, ?, ?, NOW(), ?, '1')";

        $stmtCompany = mysqli_prepare($conn, $companyProfileQuery);
        $companyName = $name . " Company";
        mysqli_stmt_bind_param($stmtCompany, "issiii", 
            $user_id, 
            $companyName, 
            $email, 
            $default_currency_id, 
            $org_id, 
            $user_id
        );

        if (!mysqli_stmt_execute($stmtCompany)) {
            throw new Exception("Failed to create company profile");
        }

        mysqli_stmt_close($stmtCompany);
        // ==================== END COMPANY PROFILE CREATION ====================
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Send welcome email (but don't wait for it)
        try {
            sendWelcomeEmail($name, $email, $password);
            $_SESSION['success'] = "Registration successful. Welcome email sent. Please login.";
        } catch (Exception $e) {
            $_SESSION['success'] = "Registration successful. Please login.";
        }
        
        // Close database connection
        mysqli_close($conn);
        
        // Clear output buffer completely
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Clear session buffer
        session_write_close();
        
        // Use JavaScript redirect as fallback
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Redirecting...</title>
            <script>
                window.location.href = "../login.php";
            </script>
            <meta http-equiv="refresh" content="0;url=../login.php">
        </head>
        <body>
            <p>Registration successful! Redirecting to login page...</p>
            <p>If you are not redirected, <a href="../login.php">click here</a>.</p>
        </body>
        </html>';
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        mysqli_close($conn);
        
        $_SESSION['errors']['general'] = "Something went wrong. Please try again.";
        
        // Clear output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header("Location: ../register.php");
        exit();
    }
}

function sendWelcomeEmail($name, $email, $plainPassword) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration (keep your existing SMTP settings)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'daxachudasmaoe@gmail.com';
        $mail->Password = 'jhkg aneq xyhh emfm';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Sender and recipient (keep your existing settings)
        $mail->setFrom('no-reply@invoice.com', 'Invoice CRM');
        $mail->addAddress($email, $name);
        $mail->addReplyTo('no-reply@invoice.com', 'Invoice CRM');

        // Email content with new design
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Invoice CRM!';
        $mail->Body = generateEmailTemplate($name, $email, $plainPassword);
        $mail->AltBody = "Welcome to Invoice CRM!\n\nDear $name,\n\nYour account has been successfully created.\n\nEmail: $email\nPassword: $plainPassword\n\nPlease login at: https://saas-invoice.simplecrm365.com/admin/login.php\n\nThank you!";
        
        // Send email (non-blocking)
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Log error but don't fail registration
        error_log("Email error: " . $e->getMessage());
        return false;
    }
}

function generateEmailTemplate($name, $email, $plainPassword) {
    $loginUrl = "saas-invoice.simplecrm365.com";
    
    return '
    <!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Welcome to Invoice CRM</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, Helvetica, sans-serif;">
 
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:40px 0;">
<tr>
<td align="center">
 
        <!-- Email Container -->
<table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
 
          <!-- Logo -->
<tr>
<td align="left" style="padding:30px 40px 10px;">
<img src="saas-invoice.simplecrm365.com/assets/images/org.jpg" alt="Invoice CRM Logo" width="150" height="40" style="display:block;">
</td>
</tr>
 
          <!-- Title -->
<tr>
<td style="padding:10px 40px 0;">
<h2 style="margin:0; font-size:24px; color:#000000;">
                Welcome to Invoice CRM!
</h2>
</td>
</tr>
 
          <!-- Divider -->
<tr>
<td style="padding:15px 40px;">
<hr style="border:none; border-top:1px solid #e5e7eb;">
</td>
</tr>
 
          <!-- Content -->
<tr>
<td style="padding:0 40px 20px; font-size:16px; color:#333333; line-height:1.6;">
              Dear ' . htmlspecialchars($name) . ',<br><br>
              Your account has been successfully created. Welcome to <strong>Invoice CRM</strong>!<br><br>
</td>
</tr>

          <!-- Account Details -->
<tr>
<td style="padding:0 40px 20px;">
<table width="100%" cellpadding="10" cellspacing="0" style="background-color:#f8f9fa; border-radius:6px;">
<tr>
<td style="font-size:15px; color:#333333;">
<strong>Account Details:</strong><br><br>
<strong>Name:</strong> ' . htmlspecialchars($name) . '<br>
<strong>Email:</strong> ' . htmlspecialchars($email) . '<br>
<strong>Password:</strong> ••••••••<br><br>
</td>
</tr>
</table>
</td>
</tr>
 
          <!-- Button -->
<tr>
<td align="left" style="padding:10px 40px 30px;">
<p style="margin:0 0 15px 0; font-size:16px; color:#333333;">Click the button below to login to your account:</p>
<a href="' . $loginUrl . '"
                 style="display:inline-block; background-color:#2563eb; color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:24px; font-size:16px; font-weight:bold;">
                Login to Your Account
</a>
</td>
</tr>

          <!-- Password Note -->
<tr>
<td style="padding:0 40px 20px; font-size:14px; color:#666666;">
<p style="margin:0; font-style:italic;">
<strong>Note:</strong> For security reasons, your password is not shown in this email.<br>
If you forgot your password, please use the "Forgot Password" feature on the login page.
</p>
</td>
</tr>
 
          <!-- Security Note -->
<tr>
<td style="padding:20px 40px 20px; font-size:14px; color:#666666; background-color:#fef3f2; border-radius:6px; margin:0 40px;">
<strong>🔒 Security Note:</strong><br>
Never share your password with anyone. Our team will never ask for your password.
</td>
</tr>

          <!-- Divider -->
<tr>
<td style="padding:30px 40px 10px;">
<hr style="border:none; border-top:1px solid #e5e7eb;">
</td>
</tr>
 
          <!-- Footer -->
<tr>
<td style="padding:10px 40px 30px; font-size:14px; color:#666666;">
<strong>Invoice CRM</strong>
<span style="margin-left:15px; color:#9ca3af;">
<a href="saas-invoice.simplecrm365.com/support" style="color:#2563eb; text-decoration:none;">
                  Contact Support
</a>
</span>
<br><br>
<div style="color:#9ca3af; font-size:13px;">
© ' . date('Y') . ' Invoice CRM. All rights reserved.<br>
<a href="saas-invoice.simplecrm365.com/privacy" style="color:#9ca3af; text-decoration:none;">Privacy Policy</a> | 
<a href="saas-invoice.simplecrm365.com/terms" style="color:#9ca3af; text-decoration:none;">Terms of Service</a>
</div>
</td>
</tr>
 
        </table>
<!-- End Container -->
 
      </td>
</tr>
</table>
 
</body>
</html>';
}

// Make sure no output at the end
ob_end_flush();
?>