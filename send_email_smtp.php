<?php
// File: send_email_smtp.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Start session for CSRF token
session_start();

// Load PHPMailer classes
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verify this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    die('Invalid CSRF token');
}

// Collect and sanitize form data
$name = htmlspecialchars($_POST['name'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars($_POST['phone'] ?? '');
$service = htmlspecialchars($_POST['service'] ?? '');
$message = htmlspecialchars($_POST['message'] ?? '');

// Validate inputs
$errors = [];
if (empty($name)) $errors[] = 'Name is required';
if (empty($email)) $errors[] = 'Email is required';
if (empty($message)) $errors[] = 'Message is required';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';

if (!empty($errors)) {
    file_put_contents('mail_errors.log', date('Y-m-d H:i:s') . " - Validation errors: " . implode(', ', $errors) . "\n", FILE_APPEND);
    header('Location: /#contact?status=error');
    exit;
}

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'gvam1004.siteground.biz';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'wa@phonerepairscolumbus.com';
    $mail->Password   = 'getphonerepairs';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    
    // Enable debugging during testing
    $mail->SMTPDebug = 2; // Change to 0 in production
    $mail->Debugoutput = function($str, $level) {
        file_put_contents('mail_debug.log', date('Y-m-d H:i:s') . " - Level $level: $str\n", FILE_APPEND);
    };

    // Recipients
    $mail->setFrom('wa@phonerepairscolumbus.com', 'Phone Repairs Columbus');
    $mail->addAddress('getphonerepairs10@gmail.com', 'Admin');
    $mail->addReplyTo($email, $name);

    // Content
    $mail->isHTML(true);
    $mail->Subject = "New Service Inquiry: $service";
    
    $mail->Body = <<<EOF
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #4a6bff; color: white; padding: 10px; text-align: center; }
            .content { padding: 20px; background-color: #f5f7ff; }
            .footer { margin-top: 20px; font-size: 0.8em; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Service Request</h2>
            </div>
            <div class='content'>
                <p><strong>From:</strong> $name</p>
                <p><strong>Contact:</strong> $email | $phone</p>
                <p><strong>Service:</strong> $service</p>
                <p><strong>Message:</strong></p>
                <p>$message</p>
            </div>
            <div class='footer'>
                <p>Sent via Phone Repairs Columbus contact form</p>
            </div>
        </div>
    </body>
    </html>
    EOF;

    $mail->AltBody = "New Service Request\n\nFrom: $name\nEmail: $email\nPhone: $phone\nService: $service\n\nMessage:\n$message";

    $mail->send();
    header('Location: /#contact?status=success');
} catch (Exception $e) {
    // Log error to a file for debugging
    $errorMsg = date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\nPHPMailer Error: " . $mail->ErrorInfo . "\n";
    file_put_contents('mail_errors.log', $errorMsg, FILE_APPEND);
    header('Location: /#contact?status=error');
}
exit;
?>
