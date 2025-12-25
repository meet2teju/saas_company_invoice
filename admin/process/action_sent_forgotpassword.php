<?php
session_start();
include '../../config/config.php';

// Use Composer autoloader
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($email, $reset_token) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'daxachudasmaoe@gmail.com';
        $mail->Password   = 'jhkg aneq xyhh emfm';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('no-reply@invoice.com', 'Invoice CRM');
        $mail->addAddress($email);
        $mail->addReplyTo('no-reply@invoice.com', 'Invoice CRM');

        // Content
        $reset_link = "saas-invoice.simplecrm365.com/admin/reset-password.php?email=" . urlencode($email) . "&reset_token=" . urlencode($reset_token);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request - Invoice CRM';
        $mail->Body    = generatePasswordResetTemplate($email, $reset_link);
        
        // Plain text version for non-HTML email clients
        $mail->AltBody = "Password Reset Link\n\nWe received a request to reset your password for Invoice CRM.\n\nClick here to reset: {$reset_link}\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, please ignore this email.\n\nBest regards,\nInvoice CRM Support Team";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $e->getMessage());
        return false;
    }
}

function generatePasswordResetTemplate($email, $reset_link) {
    return '
    <!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Password Reset Request</title>
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
                Password Reset Request
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
              Hello,<br><br>
              We received a request to reset your password for your <strong>Invoice CRM</strong> account.<br><br>
</td>
</tr>

          <!-- Reset Details -->
<tr>
<td style="padding:0 40px 20px;">
<table width="100%" cellpadding="15" cellspacing="0" style="background-color:#f8f9fa;">
<tr>
<td style="font-size:15px; color:#333333;">
<strong>Click the button below to reset your password:</strong><br><br>
<div style="text-align: center;">
<a href="' . $reset_link . '"
style="display:inline-block; background-color:#2563eb; color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:24px; font-size:16px; font-weight:bold;">
                Reset Password
</a>
</div>
<br>
<em style="color:#dc2626;">This link will expire in 1 hour for security reasons.</em>
</td>
</tr>
</table>
</td>
</tr>
 
          <!-- Alternative Link -->
<tr>
<td style="padding:10px 40px 20px; font-size:14px; color:#666666;">
<p style="margin:0 0 10px 0; font-weight:bold;">Or copy and paste this link in your browser:</p>
<div style="background-color:#f8f9fa; padding:12px; border-radius:6px; word-break:break-all; font-family: monospace; font-size:12px; color:#495057;">
' . $reset_link . '
</div>
</td>
</tr>

          <!-- Security Note -->
<tr>
<td style="padding:0 40px 20px; font-size:14px; color:#666666;">
<p style="margin:0; font-style:italic;">
<strong>Important:</strong> If you didn\'t request this password reset, please ignore this email and your password will remain unchanged.
</p>
</td>
</tr>
 
          <!-- Security Warning -->
<tr>
<td style="padding:20px 40px 20px; font-size:14px; color:#666666; background-color:#fef3f2; border-radius:6px; margin:0 40px;">
<strong>🔒 Security Note:</strong><br>
For your security, this link will expire in 1 hour. Never share this link with anyone.
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
<a href="https://saas-invoice.simplecrm365.com/support" style="color:#2563eb; text-decoration:none;">
                  Contact Support
</a>
</span>
<br><br>
<div style="color:#9ca3af; font-size:13px;">
© ' . date('Y') . ' Invoice CRM. All rights reserved.<br>
<a href="https://saas-invoice.simplecrm365.com/privacy" style="color:#9ca3af; text-decoration:none;">Privacy Policy</a> | 
<a href="https://saas-invoice.simplecrm365.com/terms" style="color:#9ca3af; text-decoration:none;">Terms of Service</a>
</div>
<br>
<p style="color:#9ca3af; font-size:12px; margin-top:10px;">
This is an automated message, please do not reply to this email.
</p>
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

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Please enter a valid email address.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../login.php");
        exit;
    }

    // Check if email exists using prepared statement
    $query = "SELECT id, name FROM login WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            // Generate secure random token
            $reset_token = bin2hex(random_bytes(32));
            $expire = date("Y-m-d H:i:s", strtotime('+1 hour'));

            // Store token in database using prepared statement
            $updateQuery = "UPDATE login SET reset_token = ?, reset_token_expire = ? WHERE email = ?";
            $updateStmt = mysqli_prepare($conn, $updateQuery);
            
            if ($updateStmt) {
                mysqli_stmt_bind_param($updateStmt, "sss", $reset_token, $expire, $email);
                $updateResult = mysqli_stmt_execute($updateStmt);
                
                if ($updateResult) {
                    if (sendMail($email, $reset_token)) {
                        $_SESSION['message'] = "Password reset link has been sent to your email.";
                        $_SESSION['message_type'] = "success";
                        
                        // Log the reset request
                        error_log("Password reset requested for email: " . $email . " at " . date('Y-m-d H:i:s'));
                    } else {
                        $_SESSION['message'] = "Failed to send reset email. Please try again later.";
                        $_SESSION['message_type'] = "danger";
                        
                        // Log the email failure
                        error_log("Failed to send reset email to: " . $email);
                    }
                } else {
                    $_SESSION['message'] = "Database error. Please try again.";
                    $_SESSION['message_type'] = "danger";
                }
                
                mysqli_stmt_close($updateStmt);
            } else {
                $_SESSION['message'] = "Database preparation error.";
                $_SESSION['message_type'] = "danger";
            }
        } else {
            // For security, don't reveal if email exists or not
            $_SESSION['message'] = "If your email exists in our system, you will receive a password reset link shortly.";
            $_SESSION['message_type'] = "info";
            
            // Log the attempt for non-existent email
            error_log("Password reset attempt for non-existent email: " . $email . " at " . date('Y-m-d H:i:s'));
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = "Database connection error.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "Invalid request method.";
    $_SESSION['message_type'] = "danger";
}

// Redirect back to login page
header("Location: ../login.php");
exit;
?>