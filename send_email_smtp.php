<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Collect and sanitize form data
$name = htmlspecialchars($_POST['name']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars($_POST['phone']);
$service = htmlspecialchars($_POST['service']);
$message = htmlspecialchars($_POST['message']);

// Validate inputs
if (empty($name) || empty($email) || empty($message)) {
    header('Location: /#contact?status=error');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /#contact?status=error');
    exit;
}

$mail = new PHPMailer(true);

try {
    // Server settings with your specific configuration
    $mail->isSMTP();
    $mail->Host       = 'gvam1004.siteground.biz';  // Your SiteGround outgoing server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'wa@phonerepairscolumbus.com'; // Your email username
    $mail->Password   = 'getphonerepairs';          // Your email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL encryption
    $mail->Port       = 465;                        // SMTP port for SSL
    
    // Enable debugging (0 = off, 1 = client messages, 2 = client and server messages)
    $mail->SMTPDebug = 0; // Set to 2 for testing, then 0 for production

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
    file_put_contents('mail_errors.log', date('Y-m-d H:i:s') . " - Error: " . $mail->ErrorInfo . "\n", FILE_APPEND);
    header('Location: /#contact?status=error');
}
exit;
?>
