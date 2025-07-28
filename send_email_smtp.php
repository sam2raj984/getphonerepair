<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'path/to/PHPMailer/src/Exception.php';
require 'path/to/PHPMailer/src/PHPMailer.php';
require 'path/to/PHPMailer/src/SMTP.php';

// Collect form data
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$service = $_POST['service'];
$message = $_POST['message'];

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'mail.phonerepairscolumbus.com'; // SiteGround SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'wa@phonerepairscolumbus.com'; // SMTP username
    $mail->Password   = 'getphonerepairs';           // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
    $mail->Port       = 587;                            // TCP port to connect to

    // Recipients
    $mail->setFrom('wa@phonerepairscolumbus.com', 'Phone Repairs Columbus');
    $mail->addAddress('getphonerepairs10@gmail.com', 'Phone Repairs Admin');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission';
    
    $mail->Body = "
    <html>
    <head>
        <title>New Contact Form Submission</title>
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
                <h2>New Contact Form Submission</h2>
            </div>
            <div class='content'>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Phone:</strong> $phone</p>
                <p><strong>Service Needed:</strong> $service</p>
                <p><strong>Message:</strong></p>
                <p>$message</p>
            </div>
            <div class='footer'>
                <p>This email was sent from the contact form on Phone Repairs Columbus website.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $mail->send();
    header('Location: /#contact?status=success');
} catch (Exception $e) {
    header('Location: /#contact?status=error');
}
exit;
?>
