<?php

session_start();
include '../../config/config.php';

// Include Composer's autoloader
require '../../vendor/autoload.php'; // Adjust path based on your file structure

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

    // Save user
    $hashedPassword = md5($password); // Keep md5 for now, but consider upgrading to password_hash()
    $insertQuery = "INSERT INTO login (name, email, password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPassword);
    
    if (mysqli_stmt_execute($stmt)) {
        $user_id = mysqli_insert_id($conn);
        
        // Send welcome email
        if (sendWelcomeEmail($name, $email, $password)) {
            $_SESSION['success'] = "Registration successful. Welcome email sent. Please login.";
        } else {
            $_SESSION['success'] = "Registration successful, but email could not be sent. Please login.";
        }
        
        mysqli_stmt_close($stmt);
        header("Location: ../login.php");
        exit;
    } else {
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
        $mail->Host = 'smtp.gmail.com'; // Your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'daxachudasmaoe@gmail.com'; // Your email
        $mail->Password = 'jhkg aneq xyhh emfm'; // Your app password
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
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

function generateEmailTemplate($name, $email, $plainPassword) {
    $loginUrl = "https://invoice.yuglogix.com/admin/login.php";
    
    // Your existing HTML template here
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
            <p>Your account has been successfully created.</p>
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