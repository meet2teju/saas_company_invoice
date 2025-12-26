<?php
session_start();
include '../../config/config.php';

// Include Composer's autoloader for PHPMailer
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function uploadFile($file, $uploadDir) {
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $fileName;
    }
    return null;
}

// Function to send welcome email
function sendWelcomeEmailToUser($name, $email, $plainPassword) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration (use your existing SMTP settings)
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

        // Sender and recipient
        $mail->setFrom('no-reply@invoice.com', 'Invoice CRM');
        $mail->addAddress($email, $name);
        $mail->addReplyTo('no-reply@invoice.com', 'Invoice CRM');

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Invoice CRM! Your Account is Ready';
        $mail->Body = generateUserWelcomeEmailTemplate($name, $email);
        $mail->AltBody = "Welcome to Invoice CRM!\n\nDear $name,\n\nYour account has been successfully created by the administrator.\n\nEmail: $email\n\nPlease login at: " . getLoginUrl() . "\n\nYour password has been set. Please use the password provided by your administrator or use the 'Forgot Password' feature if needed.\n\nThank you!";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Welcome email error for $email: " . $e->getMessage());
        return false;
    }
}

// Function to generate email HTML template (without showing password)
function generateUserWelcomeEmailTemplate($name, $email) {
    $loginUrl = getLoginUrl();
    
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
              Your account has been successfully created by the administrator. Welcome to <strong>Invoice CRM</strong>!<br><br>
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
Your administrator has set up your account password. Please contact your administrator for the password or use the "Forgot Password" feature on the login page.
</p>
</td>
</tr>

          <!-- Security Note -->
<tr>
<td style="padding:20px 40px 20px; font-size:14px; color:#666666; background-color:#fef3f2; border-radius:6px; margin:0 40px;">
<strong>🔒 Important Security Note:</strong><br>
Please change your password after first login. Never share your credentials with anyone.
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

// Function to get login URL
function getLoginUrl() {
    // Detect if we're in localhost or production
    if (isset($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
            return 'saas-invoice.simplecrm365.com/admin/login.php';
        }
    }
    // Default to your production URL
    return 'saas-invoice.simplecrm365.com/admin/login.php';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get organization ID from session (current user's organization)
    $orgId = $_SESSION['org_id'] ?? 1;

    mysqli_begin_transaction($conn);

    try {
        $name       = trim($_POST['name'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $phone      = trim($_POST['phone_number'] ?? '');
        $password   = trim($_POST['password'] ?? '');
        $mobile_country_code = trim($_POST['mobile_country_code'] ?? '+91');
        $type_id    = $_POST['role_id'] ?? '';
        $status     = $_POST['status'] ?? '';
        $image_name = '';

        // Check duplicate email WITH org_id filter
        $checkEmail = mysqli_query($conn, "SELECT id FROM login WHERE email = '$email' AND org_id = '$orgId' AND is_deleted = 0");
        if (mysqli_num_rows($checkEmail) > 0) {
            $_SESSION['message'] = 'Email already exists. Please use another email.';
            $_SESSION['message_type'] = 'error';
            header("Location: ../users.php");
            exit();
        }

        // Handle file upload
        if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0) {
            $image_name = uploadFile($_FILES['profile_img'], '../../uploads/');
        }

        $hashedPassword = md5($password);

        // Insert user into database
        $query = "INSERT INTO login (
            name, email, phone_number, password, mobile_country_code, role_id, status, profile_img, 
            org_id, is_deleted, created_at, updated_at,
            reset_token, reset_token_expire
        ) VALUES (
            '$name', '$email', '$phone', '$hashedPassword', '$mobile_country_code', '$type_id', '$status', '$image_name',
            '$orgId', 0, NOW(), NOW(),
            NULL, NULL
        )";

        if (!mysqli_query($conn, $query)) {
            throw new Exception("User insert failed: " . mysqli_error($conn));
        }

        $userId = mysqli_insert_id($conn);

        // Commit transaction
        mysqli_commit($conn);
        
        // Send welcome email (asynchronously - don't wait for it)
        $emailSent = false;
        try {
            $emailSent = sendWelcomeEmailToUser($name, $email, $password);
        } catch (Exception $e) {
            error_log("Email sending failed for user $userId: " . $e->getMessage());
        }
        
        if ($emailSent) {
            $_SESSION['message'] = 'User added successfully. Welcome email sent to ' . $email;
        } else {
            $_SESSION['message'] = 'User added successfully. Welcome email could not be sent.';
        }
        
        $_SESSION['message_type'] = 'success';

    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        $_SESSION['message'] = 'Error adding User: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
    }

    header("Location: ../users.php");
    exit();
} else {
    echo "Invalid request.";
}
?>