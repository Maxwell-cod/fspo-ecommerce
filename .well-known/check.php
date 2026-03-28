<?php
/**
 * IndexNow Setup Verification
 * Location: /.well-known/check.php
 * 
 * Verifies IndexNow configuration and checks setup status
 */

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? null;

$indexnowKey = substr(hash('sha256', SITE_URL . $_SERVER['SERVER_NAME'] . date('Y')), 0, 32);

switch ($action) {
    case 'check_indexnow':
        checkIndexNowFile($indexnowKey);
        break;
    
    case 'create_key_file':
        createKeyFile($indexnowKey);
        break;
    
    case 'verify_domain':
        verifyDomainOwnership();
        break;
    
    case 'test_api':
        testIndexNowAPI();
        break;
    
    case 'get_stats':
        getSubmissionStats();
        break;
    
    default:
        echo json_encode([
            'success' => false,
            'error' => 'Unknown action. Valid actions: check_indexnow, create_key_file, verify_domain, test_api, get_stats'
        ]);
}

/**
 * Check if IndexNow key file exists
 */
function checkIndexNowFile($key) {
    $keyFile = __DIR__ . '/IndexNow-' . $key;
    
    if (file_exists($keyFile) && file_get_contents($keyFile) === $key) {
        echo json_encode([
            'status' => 'found',
            'message' => '✅ IndexNow key file found',
            'file' => '/.well-known/IndexNow-' . $key,
            'verified' => true
        ]);
    } else {
        echo json_encode([
            'status' => 'not_found',
            'message' => '❌ IndexNow key file not found',
            'file' => '/.well-known/IndexNow-' . $key,
            'key' => $key,
            'action_needed' => 'Create the file with key as content'
        ]);
    }
}

/**
 * Create IndexNow key file
 */
function createKeyFile($key) {
    $keyFile = __DIR__ . '/IndexNow-' . $key;
    
    // Ensure directory exists
    if (!is_dir(__DIR__)) {
        mkdir(__DIR__, 0755, true);
    }
    
    // Write key file
    if (file_put_contents($keyFile, $key) !== false) {
        chmod($keyFile, 0644);
        echo json_encode([
            'success' => true,
            'message' => '✅ IndexNow key file created',
            'file' => '/.well-known/IndexNow-' . $key,
            'key' => $key,
            'next_step' => 'Key file is now accessible at ' . SITE_URL . '/.well-known/IndexNow-' . $key
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to create key file. Check directory permissions.'
        ]);
    }
}

/**
 * Verify domain ownership
 */
function verifyDomainOwnership() {
    $domain = parse_url(SITE_URL, PHP_URL_HOST);
    
    echo json_encode([
        'domain' => $domain,
        'site_url' => SITE_URL,
        'verification_methods' => [
            'dns' => [
                'type' => 'TXT Record',
                'host' => $domain,
                'recommended' => true
            ],
            'html_file' => [
                'type' => 'HTML File Upload',
                'location' => '/.well-known/google-site-verification-[TOKEN].html'
            ],
            'meta_tag' => [
                'type' => 'Meta Tag',
                'location' => '<head> section'
            ]
        ],
        'status' => 'ready_for_verification'
    ]);
}

/**
 * Test IndexNow API connection
 */
function testIndexNowAPI() {
    $testUrl = SITE_URL . '/index.php';
    $key = substr(hash('sha256', SITE_URL . $_SERVER['SERVER_NAME'] . date('Y')), 0, 32);
    $domain = parse_url(SITE_URL, PHP_URL_HOST);
    
    $payload = [
        'host' => $domain,
        'key' => $key,
        'urlList' => [$testUrl]
    ];
    
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
    $error = curl_error($ch);
    curl_close($ch);
    
    echo json_encode([
        'test_url' => $testUrl,
        'api_endpoint' => 'https://www.bing.com/indexnow',
        'http_code' => $httpCode,
        'status' => ($httpCode === 200) ? 'success' : 'failed',
        'error' => $error ?: null,
        'response' => substr($response, 0, 200),
        'message' => $httpCode === 200 
            ? '✅ API connection successful!'
            : '❌ API connection failed'
    ]);
}

/**
 * Get submission statistics
 */
function getSubmissionStats() {
    try {
        $db = getDB();
        
        // Check if log table exists
        $result = $db->query("SHOW TABLES LIKE 'indexnow_log'");
        if ($result->rowCount() === 0) {
            echo json_encode([
                'total_submissions' => 0,
                'last_submission' => null,
                'today_submissions' => 0,
                'this_week_submissions' => 0,
                'message' => 'No submissions logged yet'
            ]);
            return;
        }
        
        // Get statistics
        $stats = $db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN DATE(submitted_at) = CURDATE() THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as week,
            MAX(submitted_at) as last_submission,
            AVG(status) as avg_status
        FROM indexnow_log")->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'total_submissions' => (int)$stats['total'],
            'today_submissions' => (int)$stats['today'],
            'this_week_submissions' => (int)$stats['week'],
            'last_submission' => $stats['last_submission'],
            'average_status' => (int)$stats['avg_status'],
            'status' => 'success'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'error' => $e->getMessage(),
            'status' => 'error'
        ]);
    }
}
?>
