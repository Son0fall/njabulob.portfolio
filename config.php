<?php
// config.php - Single configuration and functions file

// ======================
// SMTP Configuration
// ======================
define('SITE_NAME', 'Njabulo Mavuso | CSPro Programmer');
define('SITE_EMAIL', 'portfolio@njabulomavuso.com');
define('MY_EMAIL', 'njabulomavuso0271@gmail.com');
define('SMTP_HOST', 'smtp.yourhost.com');     // Your hosting SMTP
define('SMTP_PORT', 587);                     // 587 for TLS, 465 for SSL
define('SMTP_USER', 'your-smtp-username');
define('SMTP_PASS', 'your-smtp-password');

// ======================
// Security & Visitor Tracking
// ======================
define('VISITOR_LOG', 'visitor-log.json');
define('ADMIN_KEY', 'njabulo-admin-2024');    // Change this!

$envGoogleKey = trim((string) getenv('GOOGLE_API_KEY'));
if (!defined('GOOGLE_API_KEY')) {
    if ($envGoogleKey !== '' && $envGoogleKey !== 'YOUR_GOOGLE_KEY' && strpos($envGoogleKey, 'AIza') === 0) {
        define('GOOGLE_API_KEY', $envGoogleKey);
    } else {
        define('GOOGLE_API_KEY', '');
    }
}

$envCseId = trim((string) getenv('GOOGLE_CSE_ID'));
if (!defined('GOOGLE_CSE_ID')) {
    if ($envCseId !== '' && $envCseId !== 'YOUR_CSE_ID') {
        define('GOOGLE_CSE_ID', $envCseId);
    } else {
        define('GOOGLE_CSE_ID', '');
    }
}
$envGemini = trim((string) getenv('GEMINI_API_KEY'));
$hardcodedGemini = 'AIzaSyDyh1m33tGRMJKdHtJd90k9GovO9Mq4WRA';

if (!defined('GEMINI_API_KEY')) {
    if ($envGemini !== '' && $envGemini !== 'YOUR_GEMINI_KEY' && strpos($envGemini, 'AIza') === 0) {
        define('GEMINI_API_KEY', $envGemini);
    } else {
        define('GEMINI_API_KEY', $hardcodedGemini);
    }
}

// ======================
// Chatbot Knowledge Base
// ======================
$CHATBOT_KNOWLEDGE = [
    'who are you' => 'I\'m Njabulo Mavuso, IT graduate from Limkokwing University, currently working as a CSPro/CSWeb programmer at Central Statistical Office on the PHC project.',
    'what is phc' => 'PHC stands for Population and Housing Census - I\'m developing applications for this national project using CSPro and CSWeb technologies.',
    'your skills' => 'CSPro/CSWeb programming, PHP development, system administration (PIMS), networking, Visitor Management Systems, and web application development.',
    'your education' => 'Associate Degree in Information Technology from Limkokwing University of Creative Technology.',
    'your experience' => 'Started as IT intern at Ministry of Economic Planning, developed Visitor Management System, now IT Officer at Central Statistical Office (12-month contract).',
    'your projects' => 'PHC Dashboard (https://sonofall.great-site.net/phc_dashboard/), SmartSchoolBubsz announcement app, Visitor Management System, and this portfolio.',
    'contact you' => 'Email: njabulomavuso0271@gmail.com | LinkedIn: Njabulo Mavuso | GitHub: son0fall | WhatsApp available through contact form.',
    'download cv' => 'You can download my ATS-friendly CV in PDF or Word format from the Download section below.',
    'default' => 'I can tell you about my background, skills, PHC project work, or how to contact me. What would you like to know?'
];

// ======================
// Visitor Tracking Function
// ======================
function trackVisitor() {
    $log = file_exists(VISITOR_LOG) ? json_decode(file_get_contents(VISITOR_LOG), true) : [
        'total' => 0,
        'daily' => [],
        'monthly' => [],
        'countries' => []
    ];
    
    $today = date('Y-m-d');
    $thisMonth = date('Y-m');
    
    $log['total']++;
    $log['daily'][$today] = ($log['daily'][$today] ?? 0) + 1;
    $log['monthly'][$thisMonth] = ($log['monthly'][$thisMonth] ?? 0) + 1;
    
    // Get approximate country from IP (simplified)
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $country = 'Unknown';
    
    // Simple IP to country mapping (in real project, use API)
    $log['countries'][$country] = ($log['countries'][$country] ?? 0) + 1;
    $log['last_visit'] = date('Y-m-d H:i:s');
    
    file_put_contents(VISITOR_LOG, json_encode($log, JSON_PRETTY_PRINT));
    
    return $log;
}

// ======================
// Chatbot Response Function
// ======================
function getChatbotResponse($message) {
    global $CHATBOT_KNOWLEDGE;
    $message = strtolower(trim($message));
    
    foreach ($CHATBOT_KNOWLEDGE as $key => $response) {
        if ($key !== 'default' && strpos($message, $key) !== false) {
            return $response;
        }
    }
    
    // Check for similar questions
    if (preg_match('/(what.*do.*you.*do|your.*job|occupation)/', $message)) {
        return $CHATBOT_KNOWLEDGE['who are you'];
    }
    
    if (preg_match('/(skill|programming|cspro|csweb)/', $message)) {
        return $CHATBOT_KNOWLEDGE['your skills'];
    }
    
    if (preg_match('/(project|github|portfolio)/', $message)) {
        return $CHATBOT_KNOWLEDGE['your projects'];
    }
    
    if (preg_match('/(contact|email|linkedin|whatsapp)/', $message)) {
        return $CHATBOT_KNOWLEDGE['contact you'];
    }
    
    return $CHATBOT_KNOWLEDGE['default'];
}

// ======================
// SMTP Email Function
// ======================
function sendEmail($name, $email, $message) {
    // In production, use PHPMailer: composer require phpmailer/phpmailer
    $to = MY_EMAIL;
    $subject = "Portfolio Contact: " . substr($message, 0, 50) . "...";
    $headers = "From: " . SITE_EMAIL . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $body = "
    <!DOCTYPE html>
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>New Contact Form Submission</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Message:</strong></p>
        <div style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>
            " . nl2br(htmlspecialchars($message)) . "
        </div>
        <hr>
        <p><small>Sent from Njabulo's Portfolio Website</small></p>
    </body>
    </html>
    ";
    
    return mail($to, $subject, $body, $headers);
}

// Track visitor on include
if (!defined('SKIP_VISITOR_TRACKING') || SKIP_VISITOR_TRACKING !== true) {
    trackVisitor();
}
?>
