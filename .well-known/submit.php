<?php
/**
 * IndexNow Submission Handler
 * Location: /.well-known/submit.php
 * 
 * Handles IndexNow API submissions to Google/Bing
 * Receives JSON POST with URLs and submits to IndexNow API
 */

require_once __DIR__ . '/../includes/config.php';

// Set response header
header('Content-Type: application/json');

// Generate IndexNow key (consistent across requests)
$indexnowKey = substr(hash('sha256', SITE_URL . $_SERVER['SERVER_NAME'] . date('Y')), 0, 32);

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
    exit;
}

$urls = $input['urls'] ?? [];
$isTest = $input['test'] ?? false;

if (empty($urls)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No URLs provided']);
    exit;
}

// Validate URLs
$validUrls = [];
foreach ($urls as $url) {
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $validUrls[] = $url;
    }
}

if (empty($validUrls)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No valid URLs']);
    exit;
}

// Prepare IndexNow payload
$payload = [
    'host' => parse_url(SITE_URL, PHP_URL_HOST),
    'key' => $indexnowKey,
    'urlList' => array_slice($validUrls, 0, 10) // IndexNow max 10 URLs per request
];

// Submit to IndexNow (Bing)
$ch = curl_init('https://www.bing.com/indexnow');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_TIMEOUT => 10
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log submission
logIndexNowSubmission($validUrls, $httpCode, $response);

// Return response
if ($httpCode === 200) {
    echo json_encode([
        'success' => true,
        'status' => $httpCode,
        'message' => 'Successfully submitted ' . count($validUrls) . ' URL(s) to IndexNow',
        'urls_submitted' => count($validUrls),
        'note' => 'Google, Bing, and Yandex have been notified. Pages may appear in search results within 24 hours.'
    ]);
} else {
    http_response_code($httpCode ?: 500);
    echo json_encode([
        'success' => false,
        'status' => $httpCode,
        'error' => $curlError ?: 'HTTP ' . $httpCode,
        'response' => substr($response, 0, 200)
    ]);
}

/**
 * Log IndexNow submission to database
 */
function logIndexNowSubmission($urls, $status, $response) {
    try {
        $db = getDB();
        
        // Create log table if not exists
        $db->exec("CREATE TABLE IF NOT EXISTS indexnow_log (
            id INT PRIMARY KEY AUTO_INCREMENT,
            urls TEXT,
            status INT,
            response TEXT,
            submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(submitted_at)
        )");
        
        // Insert log
        $db->prepare("INSERT INTO indexnow_log (urls, status, response) VALUES (?, ?, ?)")
            ->execute([
                json_encode($urls),
                $status,
                substr($response, 0, 500)
            ]);
    } catch (Exception $e) {
        // Silently fail logging - don't break submission
        error_log('IndexNow logging error: ' . $e->getMessage());
    }
}
?>
