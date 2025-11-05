<?php

session_start();
include '../../config/config.php';

// Include Composer's autoloader
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);

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
        header("Location: ../register.php");
        exit;
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
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Send welcome email
        if (sendWelcomeEmail($name, $email, $password)) {
            $_SESSION['success'] = "Registration successful. Welcome email sent. Please login.";
        } else {
            $_SESSION['success'] = "Registration successful, but email could not be sent. Please login.";
        }
        
        header("Location: ../login.php");
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        $_SESSION['errors']['general'] = "Something went wrong. Please try again.";
        header("Location: ../register.php");
        exit;
    }
}

function sendWelcomeEmail($name, $email, $plainPassword) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'daxachudasmaoe@gmail.com';
        $mail->Password = 'jhkg aneq xyhh emfm';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('no-reply@invoice.com', 'Invoice CRM');
        $mail->addAddress($email, $name);
        $mail->addReplyTo('no-reply@invoice.com', 'Invoice CRM');

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Invoice CRM!';
        $mail->Body = generateEmailTemplate($name, $email, $plainPassword);
        
        // Send email
        return $mail->send();
        
    } catch (Exception $e) {
        return false;
    }
}

function generateEmailTemplate($name, $email, $plainPassword) {
    $loginUrl = "https://invoice.yuglogix.com/admin/login.php";
    
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Welcome to Invoice CRM</title>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
            .button { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Welcome to Invoice CRM!</h2>
            <p>Dear ' . htmlspecialchars($name) . ',</p>
            <p>Your account and organization have been successfully created.</p>
            <p><strong>Login Details:</strong></p>
            <ul>
                <li><strong>Email:</strong> ' . htmlspecialchars($email) . '</li>
                <li><strong>Password:</strong> ' . htmlspecialchars($plainPassword) . '</li>
            </ul>
            <p><a href="' . $loginUrl . '" class="button">Login to Your Account</a></p>
        </div>
    </body>
    </html>';
}
?>