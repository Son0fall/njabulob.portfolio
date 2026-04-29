<?php
// contact-process.php
header('Content-Type: application/json');

// 1. IMPORT PHPMAILER CLASSES
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// 2. INCLUDE THE FILES (Adjust paths if your folder name is different)
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get and sanitize form data
$name    = htmlspecialchars(trim($_POST['name'] ?? ''));
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$subject = htmlspecialchars(trim($_POST['subject'] ?? 'Portfolio Inquiry'));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));

// Validate required fields
if (empty($name) || !$email || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
    exit;
}

// HTML email body
$emailBody = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8fafc; }
        .message { background: white; padding: 15px; border-left: 4px solid #2563eb; margin: 15px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>New Contact Form Submission</h2>
        </div>
        <div class='content'>
            <p><strong>From:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Subject:</strong> $subject</p>
            <div class='message'>
                <strong>Message:</strong><br>
                " . nl2br($message) . "
            </div>
            <p><em>This message was sent from your portfolio website contact form.</em></p>
        </div>
    </div>
</body>
</html>
";

// 3. START PHPMAILER PROCESS
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();                                            
    $mail->Host       = 'smtp.gmail.com';                     
    $mail->SMTPAuth   = true;                                   
    $mail->Username   = 'njabulob.mavuso@gmail.com';           // Your Gmail
    $mail->Password   = 'xhpb uwet ngcm rqed';                  // YOUR 16-DIGIT APP PASSWORD
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
    $mail->Port       = 587;                                    

    // Recipients
    $mail->setFrom('njabulob.mavuso@gmail.com', 'Portfolio Site'); 
    $mail->addAddress('njabulob.mavuso@gmail.com');           // Where you receive the mail
    $mail->addReplyTo($email, $name);                           // User's email for replies

    // Content
    $mail->isHTML(true);                                  
    $mail->Subject = "Portfolio Contact: $subject";
    $mail->Body    = $emailBody;
    $mail->AltBody = strip_tags($message); // Plain text version for non-HTML mail clients

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);

} catch (Exception $e) {
    // If it fails, send the specific error back for debugging
    echo json_encode(['success' => false, 'message' => "Mailer Error: {$mail->ErrorInfo}"]);
}
?>