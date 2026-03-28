<?php
/**
 * Error Handling & Environment Configuration
 * Location: /includes/error-handler.php
 * 
 * Comprehensive error handling to prevent crashes
 * Handles all types of errors and exceptions
 */

// Only include once
if (defined('ERROR_HANDLER_LOADED')) return;
define('ERROR_HANDLER_LOADED', true);

// ============================================
// ERROR LOGGING CONFIGURATION
// ============================================

// Determine if we're in production
$is_production = ($_SERVER['HTTP_HOST'] ?? 'localhost') !== 'localhost:8000' && 
                 ($_SERVER['HTTP_HOST'] ?? 'localhost') !== '127.0.0.1:8000';

// Set error reporting based on environment
if ($is_production) {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');  // Don't show errors to users
    ini_set('log_errors', '1');      // Log all errors
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');  // Show errors in development
    ini_set('log_errors', '1');      // Also log them
}

// Configure error log location
$log_dir = __DIR__ . '/../logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

ini_set('error_log', $log_dir . '/php-error.log');

// ============================================
// CUSTOM ERROR HANDLER
// ============================================

/**
 * Custom error handler to catch PHP errors
 */
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Log the error
    $error_message = sprintf(
        "[%s] Error %d: %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $errno,
        $errstr,
        $errfile,
        $errline
    );
    
    error_log($error_message);
    
    // Check if error should be handled
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    // In production, show generic error
    if ($is_production && $errno !== E_USER_ERROR) {
        // Log but don't display
        return true;
    }
    
    // In development, show detailed error
    return false; // Use default handler
});

// ============================================
// CUSTOM EXCEPTION HANDLER
// ============================================

/**
 * Custom exception handler to catch all exceptions
 */
set_exception_handler(function($exception) {
    global $is_production;
    
    // Log the exception
    $error_message = sprintf(
        "[%s] Exception: %s in %s on line %d\nStacktrace:\n%s",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );
    
    error_log($error_message);
    
    // Display error based on environment
    if ($is_production) {
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Service Error</title>
            <style>
                body { font-family: Arial; background: #f5f5f5; text-align: center; padding: 50px; }
                h1 { color: #d32f2f; }
                p { color: #666; max-width: 500px; margin: 0 auto; }
            </style>
        </head>
        <body>
            <h1>Service Temporarily Unavailable</h1>
            <p>We're experiencing technical difficulties. Please try again later.</p>
            <p>If problems persist, please contact support.</p>
        </body>
        </html>
        <?php
    } else {
        // Development: show detailed error
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Exception Error</title>
            <style>
                body { font-family: monospace; background: #1a1a1a; color: #e0e0e0; padding: 20px; }
                .error { background: #d32f2f; color: white; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
                .trace { background: #2a2a2a; padding: 15px; border-left: 4px solid #d32f2f; }
                h2 { color: #d32f2f; }
            </style>
        </head>
        <body>
            <div class="error">
                <h2><?php echo get_class($exception); ?></h2>
                <p><?php echo htmlspecialchars($exception->getMessage()); ?></p>
            </div>
            <div class="trace">
                <p><strong>File:</strong> <?php echo htmlspecialchars($exception->getFile()); ?></p>
                <p><strong>Line:</strong> <?php echo $exception->getLine(); ?></p>
                <pre><?php echo htmlspecialchars($exception->getTraceAsString()); ?></pre>
            </div>
        </body>
        </html>
        <?php
    }
    
    exit;
});

// ============================================
// FATAL ERROR HANDLER
// ============================================

/**
 * Catch fatal errors that can't be caught by exception handlers
 */
register_shutdown_function(function() {
    global $is_production;
    
    $error = error_get_last();
    
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $error_message = sprintf(
            "[%s] FATAL: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $error['message'],
            $error['file'],
            $error['line']
        );
        
        error_log($error_message);
        
        // Display error page
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Fatal Error</title>
            <style>
                body { font-family: Arial; background: #f5f5f5; text-align: center; padding: 50px; }
                h1 { color: #d32f2f; }
            </style>
        </head>
        <body>
            <h1>Critical System Error</h1>
            <p>The system encountered a fatal error and must restart.</p>
            <p>Our team has been notified. Please try again later.</p>
        </body>
        </html>
        <?php
    }
});

// ============================================
// DATABASE ERROR HANDLING
// ============================================

/**
 * Safe database query wrapper
 */
function safeQuery($db, $sql, $params = []) {
    try {
        if (empty($params)) {
            return $db->query($sql);
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        
        if ($GLOBALS['is_production'] ?? false) {
            throw new Exception("Database operation failed");
        } else {
            throw $e;
        }
    }
}

// ============================================
// FILE OPERATION SAFETY
// ============================================

/**
 * Safe file write with error handling
 */
function safeFileWrite($filename, $content) {
    try {
        // Check if directory is writable
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        if (!is_writable($dir)) {
            throw new Exception("Directory not writable: $dir");
        }
        
        // Check disk space
        $free_space = disk_free_space($dir);
        if ($free_space === false || $free_space < strlen($content) + 1024) {
            throw new Exception("Insufficient disk space");
        }
        
        // Write file
        $bytes = file_put_contents($filename, $content);
        if ($bytes === false) {
            throw new Exception("Failed to write file");
        }
        
        return true;
    } catch (Exception $e) {
        error_log("File write error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Safe file read with error handling
 */
function safeFileRead($filename) {
    try {
        if (!file_exists($filename)) {
            throw new Exception("File not found: $filename");
        }
        
        if (!is_readable($filename)) {
            throw new Exception("File not readable: $filename");
        }
        
        $content = file_get_contents($filename);
        if ($content === false) {
            throw new Exception("Failed to read file");
        }
        
        return $content;
    } catch (Exception $e) {
        error_log("File read error: " . $e->getMessage());
        throw $e;
    }
}

// ============================================
// API ERROR HANDLING
// ============================================

/**
 * Safe API request with retry logic
 */
function safeApiRequest($url, $options = []) {
    $retries = $options['retries'] ?? 3;
    $timeout = $options['timeout'] ?? 10;
    
    for ($attempt = 1; $attempt <= $retries; $attempt++) {
        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_HTTPHEADER => $options['headers'] ?? []
            ]);
            
            if (isset($options['postfields'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $options['postfields']);
            }
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                throw new Exception("cURL error: $error");
            }
            
            if ($http_code >= 200 && $http_code < 300) {
                return [
                    'success' => true,
                    'code' => $http_code,
                    'data' => $response
                ];
            }
            
            // Retry on server errors
            if ($http_code >= 500) {
                if ($attempt < $retries) {
                    sleep(2 ** ($attempt - 1)); // Exponential backoff
                    continue;
                }
            }
            
            throw new Exception("HTTP $http_code");
        } catch (Exception $e) {
            if ($attempt === $retries) {
                error_log("API request failed after $retries attempts: " . $e->getMessage());
                return [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'attempts' => $attempt
                ];
            }
            
            // Wait before retrying
            sleep(2 ** ($attempt - 1));
        }
    }
}

// ============================================
// RESOURCE MONITORING
// ============================================

/**
 * Check system resources
 */
function checkSystemResources() {
    $resources = [
        'memory' => [
            'used' => memory_get_usage(true),
            'limit' => ini_get('memory_limit'),
            'percent' => 0
        ],
        'disk' => [
            'free' => disk_free_space('/'),
            'total' => disk_total_space('/'),
            'percent' => 0
        ],
        'time' => [
            'elapsed' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
            'limit' => ini_get('max_execution_time'),
            'percent' => 0
        ]
    ];
    
    // Calculate percentages
    if ($resources['memory']['limit'] && strpos($resources['memory']['limit'], 'M')) {
        $limit_bytes = (int)$resources['memory']['limit'] * 1024 * 1024;
        $resources['memory']['percent'] = round(($resources['memory']['used'] / $limit_bytes) * 100);
    }
    
    if ($resources['disk']['total']) {
        $resources['disk']['percent'] = round((($resources['disk']['total'] - $resources['disk']['free']) / $resources['disk']['total']) * 100);
    }
    
    if ($resources['time']['limit']) {
        $resources['time']['percent'] = round(($resources['time']['elapsed'] / $resources['time']['limit']) * 100);
    }
    
    // Log warnings
    if ($resources['memory']['percent'] > 80) {
        error_log("WARNING: Memory usage at " . $resources['memory']['percent'] . "%");
    }
    if ($resources['disk']['percent'] > 90) {
        error_log("WARNING: Disk usage at " . $resources['disk']['percent'] . "%");
    }
    if ($resources['time']['percent'] > 80) {
        error_log("WARNING: Execution time at " . $resources['time']['percent'] . "%");
    }
    
    return $resources;
}

// ============================================
// VALIDATION HELPERS
// ============================================

/**
 * Validate email safely
 */
function validateEmail($email) {
    if (!is_string($email)) return false;
    $email = trim($email);
    if (strlen($email) > 254) return false;
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate URL safely
 */
function validateUrl($url) {
    if (!is_string($url)) return false;
    $url = trim($url);
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Sanitize string safely
 */
function sanitizeString($string, $max_length = 255) {
    if (!is_string($string)) return '';
    $string = trim($string);
    $string = substr($string, 0, $max_length);
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate file upload safely
 */
function validateFileUpload($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!isset($file['tmp_name'])) {
        throw new Exception("No file uploaded");
    }
    
    if ($file['size'] > $max_size) {
        throw new Exception("File too large (max 5MB)");
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, $allowed_types)) {
        throw new Exception("Invalid file type");
    }
    
    return true;
}

// ============================================
// INITIALIZATION
// ============================================

// Create logs directory if it doesn't exist
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

// Clean up old log files (older than 30 days)
$old_logs = glob($log_dir . '/*.log');
foreach ($old_logs as $log_file) {
    if (filemtime($log_file) < time() - (30 * 24 * 60 * 60)) {
        @unlink($log_file);
    }
}

?>
