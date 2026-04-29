$hardcodedGemini = 'AIzaSyDyh1m33tGRMJKdHtJd90k9GovO9Mq4WRA';<?php
header('Content-Type: application/json');
define('SKIP_VISITOR_TRACKING', true);
@require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

// 1. Receive the message from your website frontend (JavaScript)
$input = file_get_contents('php://input');
$requestData = json_decode($input, true);
$userMessage = isset($requestData['message']) ? $requestData['message'] : '';

// 2. YOUR PYTHONANYWHERE URL (Replace 'yourusername' with yours!)
$python_api_url = 'https://www.pythonanywhere.com/user/Sonofall/files/home/Sonofall/mysite/flask_app.py';

// 3. Prepare the data to send to Python
$postData = json_encode([
    "message" => $userMessage,
    "profile" => [
        "name" => "Njabulo B Mavuso",
        "bio" => "Portfolio owner"
    ]
]);

// 4. Initialize cURL (The "Messenger")
$ch = curl_init($python_api_url);

// 5. Configure cURL options
curl_setopt($ch, CURLOPT_POST, true);                 // Tell it this is a POST request
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);       // Attach your JSON data
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);       // Return the response as a string
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',                  // Essential for your Python Flask app
    'Content-Length: ' . strlen($postData)
));

// 6. Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for errors
if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    echo json_encode(["response" => "Connection Error: $error_msg"]);
} else {
    // 7. Send the Python API response directly back to your frontend
    if ($httpCode === 200) {
        echo $response;
    } else {
        echo json_encode(["response" => "AI Server Error (Status $httpCode). Check PythonAnywhere logs."]);
    }
}

curl_close($ch);

function get_session_save_path_value(): string {
    $raw = (string) ini_get('session.save_path');
    if ($raw === '') return '';
    if (strpos($raw, ';') === false) return $raw;
    $parts = array_values(array_filter(array_map('trim', explode(';', $raw)), static fn($p) => $p !== ''));
    return $parts ? $parts[count($parts) - 1] : '';
}

$sessionSavePath = get_session_save_path_value();
if ($sessionSavePath === '' || !is_dir($sessionSavePath) || !is_writable($sessionSavePath)) {
    $fallback = sys_get_temp_dir();
    if ($fallback && is_dir($fallback) && is_writable($fallback)) {
        ini_set('session.save_path', $fallback);
    } else {
        $localTmp = __DIR__ . DIRECTORY_SEPARATOR . 'tmp';
        if (!is_dir($localTmp)) {
            @mkdir($localTmp, 0777, true);
        }
        if (is_dir($localTmp) && is_writable($localTmp)) {
            ini_set('session.save_path', $localTmp);
        }
    }
}

session_start();

function normalize_text(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^\p{L}\p{N}\s\-\_@\.]/u', ' ', $text);
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

function tokenize(string $text): array {
    $text = normalize_text($text);
    if ($text === '') return [];
    $parts = preg_split('/\s+/', $text);
    $stop = [
        'a','an','the','and','or','but','to','for','of','in','on','at','with','from','about','me','my','your','you','i',
        'is','are','was','were','be','been','being','do','does','did','can','could','would','should','please','tell','show',
        'what','who','where','when','how','why'
    ];
    $stopSet = array_fill_keys($stop, true);
    $out = [];
    foreach ($parts as $p) {
        if ($p === '' || isset($stopSet[$p])) continue;
        $out[] = $p;
    }
    return $out;
}

function score_intents(string $message, array $intents): array {
    $msg = normalize_text($message);
    $tokens = tokenize($message);
    $tokenSet = array_fill_keys($tokens, true);
    $scores = [];
    foreach ($intents as $intent => $keywords) {
        $score = 0;
        foreach ($keywords as $kw) {
            $kw = normalize_text($kw);
            if ($kw === '') continue;
            if (strpos($msg, $kw) !== false) $score += 3;
            if (isset($tokenSet[$kw])) $score += 4;
            foreach ($tokens as $t) {
                if ($t === $kw) continue;
                $dist = levenshtein($t, $kw);
                $maxLen = max(strlen($t), strlen($kw));
                if ($maxLen > 0 && ($dist / $maxLen) <= 0.25) {
                    $score += 1;
                    break;
                }
            }
        }
        $scores[$intent] = $score;
    }
    arsort($scores);
    return $scores;
}

function google_lookup(string $query): array {
    if (!function_exists('curl_init')) return [];
    $apiKey = (string) (defined('GOOGLE_API_KEY') ? GOOGLE_API_KEY : getenv('GOOGLE_API_KEY'));
    $cseId = (string) (defined('GOOGLE_CSE_ID') ? GOOGLE_CSE_ID : getenv('GOOGLE_CSE_ID'));
    $apiKey = trim($apiKey);
    $cseId = trim($cseId);
    if ($apiKey === '' || $cseId === '') return [];

    $url = 'https://www.googleapis.com/customsearch/v1?key=' . rawurlencode($apiKey) . '&cx=' . rawurlencode($cseId) . '&q=' . rawurlencode($query);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 6,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0
    ]);
    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($raw === false || $code < 200 || $code >= 300) return [];
    $json = json_decode($raw, true);
    if (!is_array($json) || !isset($json['items']) || !is_array($json['items'])) return [];
    $items = [];
    foreach (array_slice($json['items'], 0, 3) as $it) {
        $items[] = [
            'title' => (string) ($it['title'] ?? ''),
            'snippet' => (string) ($it['snippet'] ?? ''),
            'link' => (string) ($it['link'] ?? '')
        ];
    }
    return $items;
}

function http_post_json(string $url, array $payload, array $headers = []): array {
    $json = json_encode($payload);
    if ($json === false) return ['ok' => false, 'status' => 0, 'body' => ''];

    $headers = array_merge([
        'Content-Type: application/json',
        'Accept: application/json'
    ], $headers);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0
        ]);
        $raw = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['ok' => $raw !== false && $code >= 200 && $code < 300, 'status' => $code, 'body' => $raw === false ? '' : $raw];
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $json,
            'timeout' => 10
        ]
    ]);
    $raw = @file_get_contents($url, false, $context);
    $status = 0;
    if (isset($http_response_header) && is_array($http_response_header)) {
        foreach ($http_response_header as $h) {
            if (preg_match('/^HTTP\/\S+\s+(\d+)/i', $h, $m)) {
                $status = (int) $m[1];
                break;
            }
        }
    }
    return ['ok' => $raw !== false && $status >= 200 && $status < 300, 'status' => $status, 'body' => $raw === false ? '' : $raw];
}

function python_ai_generate(string $question, array $profile): array {
    $pythonApiUrl = 'https://sonofall.pythonanywhere.com/mysite/';
    
    $payload = [
        'message' => $question,
        'profile' => $profile
    ];

    $res = http_post_json($pythonApiUrl, $payload);
    if (!$res['ok']) {
        return ['response' => '', 'sources' => []];
    }
    
    $json = json_decode($res['body'], true);
    if (!is_array($json)) return ['response' => '', 'sources' => []];
    
    return [
        'response' => (string) ($json['response'] ?? ''),
        'sources' => (array) ($json['sources'] ?? [])
    ];
}

function build_profile(): array {
    return [
        'name' => 'Njabulo Mavuso',
        'headline' => 'CSPro Programmer & IT Officer',
        'summary' => 'I build data collection and web systems, specializing in Population & Housing Census (PHC) applications using CSPro and CSWeb.',
        'education' => 'Associate Degree in Information Technology from Limkokwing University of Creative Technology.',
        'current_role' => 'IT Officer & CSPro Programmer at the Central Statistical Office (CSO).',
        'experience' => [
            'Central Statistical Office (CSO) — IT Officer & CSPro Programmer (PHC project).',
            'Ministry of Economic Planning — System Administrator & Developer (PIMS, Visitor Management System).',
            'Ministry of Economic Planning & Development — IT Intern.'
        ],
        'skills' => [
            'CSPro / CSWeb','PHP','JavaScript','HTML/CSS','System Administration','Network Configuration','Database Management'
        ],
        'projects' => [
            ['name' => 'PHC Dashboard', 'desc' => 'Real-time census dashboard for data visualization and analytics.', 'link' => 'https://sonofall.great-site.net/phc_dashboard/'],
            ['name' => 'SmartSchoolHubsz', 'desc' => 'Announcement app for educational institutions with real-time notifications.', 'link' => 'https://smartschoolhubsz.netlify.app'],
            ['name' => 'Visitor Management System', 'desc' => 'Visitor registration and tracking system for the Ministry of Economic Planning.', 'link' => ''],
            ['name' => 'EBIS Inventory System', 'desc' => 'Inventory management system (in progress).', 'link' => ''],
            ['name' => 'Online Voting System', 'desc' => 'Secure digital voting platform (in progress).', 'link' => '']
        ],
        'contact' => [
            'email' => 'njabulob.mavuso@gmail.com',
            'linkedin' => 'https://www.linkedin.com/in/njabulob-mavuso-95b677256',
            'github' => 'https://github.com/son0fall',
            'whatsapp' => 'https://wa.me/26879792770'
        ],
        'cv' => [
            'pdf' => 'assets/cv-njabulo-mavuso.pdf',
            'docx' => 'assets/cv-njabulo-mavuso.docx'
        ]
    ];
}

function answer_from_intent(string $intent, string $message, array $profile): string {
    $msg = normalize_text($message);
    if ($intent === 'contact') {
        if (strpos($msg, 'email') !== false) return 'Email: ' . $profile['contact']['email'];
        if (strpos($msg, 'linkedin') !== false) return 'LinkedIn: ' . $profile['contact']['linkedin'];
        if (strpos($msg, 'github') !== false) return 'GitHub: ' . $profile['contact']['github'];
        if (strpos($msg, 'whatsapp') !== false || strpos($msg, 'phone') !== false) return 'WhatsApp: ' . $profile['contact']['whatsapp'];
        return 'Email: ' . $profile['contact']['email'] . "\nLinkedIn: " . $profile['contact']['linkedin'] . "\nGitHub: " . $profile['contact']['github'] . "\nWhatsApp: " . $profile['contact']['whatsapp'];
    }

    if ($intent === 'projects') {
        $lines = ["Here are some projects:"];
        foreach ($profile['projects'] as $p) {
            $line = '- ' . $p['name'] . ': ' . $p['desc'];
            if (!empty($p['link'])) $line .= ' (' . $p['link'] . ')';
            $lines[] = $line;
        }
        $lines[] = 'You can also check the Projects section on this page.';
        return implode("\n", $lines);
    }

    if ($intent === 'skills') {
        return 'My main skills: ' . implode(', ', $profile['skills']) . '.';
    }

    if ($intent === 'experience') {
        return "Experience:\n- " . implode("\n- ", $profile['experience']);
    }

    if ($intent === 'education') {
        return $profile['education'];
    }

    if ($intent === 'cv') {
        return 'Download my CV here: ' . $profile['cv']['pdf'] . ' (PDF) or ' . $profile['cv']['docx'] . ' (Word).';
    }

    if ($intent === 'greeting') {
        return "Hi! I'm " . $profile['name'] . ". Ask me about my skills, experience, projects, or how to contact me.";
    }

    if ($intent === 'about') {
        return $profile['name'] . ' — ' . $profile['headline'] . ".\n" . $profile['summary'];
    }

    return "I can help with: about me, skills, experience, projects, CV, and contact details. What do you want to know?";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = (string) ($_POST['message'] ?? '');
    $userMessage = trim($userMessage);
    
    if (empty($userMessage)) {
        echo json_encode(['response' => 'Please ask me something about Njabulo!']);
        exit;
    }

    $profile = build_profile();
    $intents = [
        'greeting' => ['hi','hello','hey','good morning','good afternoon','good evening'],
        'about' => ['who are you','about','introduce','name','njabulo','bio'],
        'skills' => ['skills','cspro','csweb','php','javascript','html','css','network','administration','database'],
        'experience' => ['experience','work','job','role','cso','ministry','intern','system administrator'],
        'education' => ['education','degree','university','limkokwing','graduate'],
        'projects' => ['projects','project','portfolio','github','dashboard','smartschool','visitor','inventory','voting'],
        'contact' => ['contact','email','linkedin','github','whatsapp','phone','reach'],
        'cv' => ['cv','resume','download']
    ];

    $scores = score_intents($userMessage, $intents);
    $topIntent = array_key_first($scores);
    $topScore = $topIntent !== null ? (int) $scores[$topIntent] : 0;

    $normalized = normalize_text($userMessage);
    $aboutNjabulo = false;
    if ($topIntent !== null && in_array($topIntent, ['about','skills','experience','education','projects','contact','cv'], true)) {
        $aboutNjabulo = true;
    }
    if (strpos($normalized, 'njabulo') !== false || strpos($normalized, 'mavuso') !== false) {
        $aboutNjabulo = true;
    }

    $aiResult = python_ai_generate($userMessage, $profile);
    $response = $aiResult['response'];
    $sources = $aiResult['sources'];

    if ($response === '') {
        if ($aboutNjabulo) {
            $response = "I couldn't connect to my AI brain on PythonAnywhere right now. Here is what I can confirm from the portfolio:\n\n" . answer_from_intent($topIntent ?: 'about', $userMessage, $profile);
        } else {
            if ($topScore <= 0) {
                $lastIntent = (string) ($_SESSION['chatbot_last_intent'] ?? '');
                if ($lastIntent !== '' && $lastIntent !== 'unknown') {
                    $response = answer_from_intent($lastIntent, $userMessage, $profile);
                } else {
                    $response = answer_from_intent('unknown', $userMessage, $profile);
                }
                $topIntent = $lastIntent !== '' ? $lastIntent : 'unknown';
            } else {
                $response = answer_from_intent($topIntent, $userMessage, $profile);
            }
        }
    }

    $_SESSION['chatbot_last_intent'] = $topIntent ?: 'unknown';
    if (!isset($_SESSION['chatbot_history']) || !is_array($_SESSION['chatbot_history'])) {
        $_SESSION['chatbot_history'] = [];
    }
    $_SESSION['chatbot_history'][] = ['u' => $userMessage, 'i' => $topIntent, 't' => time()];
    if (count($_SESSION['chatbot_history']) > 12) {
        $_SESSION['chatbot_history'] = array_slice($_SESSION['chatbot_history'], -12);
    }

    echo json_encode(['response' => $response, 'intent' => $topIntent, 'sources' => $sources]);
} else {
    echo json_encode(['response' => 'Invalid request']);
}
?>
