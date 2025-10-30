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
        $mail->setFrom('maniyamansioe@gmail.com', 'CRM Support');
        $mail->addAddress($email);

        // Content
        $reset_link = "https://invoice.yuglogix.com/admin/reset-password.php?email=" . urlencode($email) . "&reset_token=" . urlencode($reset_token);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Link - CRM System';
       $mail->Body    = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                margin: 0;
                padding: 0;
            }
            .container { 
                max-width: 600px; 
                margin: 0 auto; 
                background: #ffffff;
            }
            .header { 
                background: #7539ff; 
                color: white; 
                padding: 20px; 
                text-align: center; 
            }
            .header h2 {
                margin: 0;
                font-size: 24px;
                font-weight: 600;
            }
            .content { 
                padding: 30px; 
                background: #f9f9f9; 
            }
            /* Exact same styling as your View Details button */
            .btn-primary { 
                display: inline-flex !important;
                align-items: center !important;
                padding: 12px 24px !important;
                background: #7539ff !important;
                color: white !important;
                text-decoration: none !important;
                border-radius: 6px !important;
                margin: 20px 0 !important;
                font-weight: 500 !important;
                font-size: 14px !important;
                border: 1px solid #0d6efd !important;
                cursor: pointer !important;
                text-align: center !important;
                transition: all 0.3s ease !important;
                font-family: Arial, sans-serif !important;
            }
            .btn-primary:hover {
                background: #0b5ed7 !important;
                border-color: #0a58ca !important;
                color: white !important;
                transform: translateY(-1px);
                box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
                text-decoration: none !important;
            }
            .btn-icon {
                margin-right: 8px !important;
            }
            .footer { 
                text-align: center; 
                padding: 20px; 
                font-size: 12px; 
                color: #666;
                background: #fff;
                border-top: 1px solid #eee;
            }
            .info-box {
                background: #fff;
                border-left: 4px solid #007bff;
                padding: 15px;
                margin: 15px 0;
                border-radius: 4px;
            }
            .link-text {
                word-break: break-all;
                background: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
                font-size: 12px;
                color: #495057;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Password Reset Request</h2>
            </div>
            <div class='content'>
                <p>Hello,</p>
                <p>We received a request to reset your password for your CRM account.</p>
                
                <div class='info-box'>
                    <p><strong>Click the button below to reset your password:</strong></p>
                    <p style='text-align: center;'>
                        <a href='{$reset_link}' class='btn-primary'>
                            <i class='btn-icon'>🔒</i>Reset Password
                        </a>
                    </p>
                </div>

                <p><strong>Or copy and paste this link in your browser:</strong></p>
                <p class='link-text'>{$reset_link}</p>
                
                <p><strong>Important:</strong> This link will expire in 1 hour for security reasons.</p>
                <p>If you didn't request this password reset, please ignore this email and your password will remain unchanged.</p>
                
                <p>Best regards,<br>CRM Support Team</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " CRM System. All rights reserved.</p>
                <p>This is an automated message, please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
";

        // Plain text version for non-HTML email clients
        $mail->AltBody = "Password Reset Link\n\nWe received a request to reset your password.\n\nClick here to reset: {$reset_link}\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, please ignore this email.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $e->getMessage());
        return false;
    }
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