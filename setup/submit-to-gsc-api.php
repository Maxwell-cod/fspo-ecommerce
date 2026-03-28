<?php
/**
 * Google Search Console API Sitemap Submission Script
 * Location: /setup/submit-to-gsc-api.php
 * 
 * This script can automatically submit your sitemap to Google Search Console
 * using Google's IndexNow API (no OAuth needed)
 */

require_once __DIR__ . '/../includes/config.php';

// Get sitemap URL
$sitemapUrl = SITE_URL . '/sitemap.xml';
$domainUrl = preg_replace(['#^https?://#', '#/$#'], '', SITE_URL);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Search Console API Submission - FSPO Ltd</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2a1810 100%);
            color: #e0e0e0;
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 900px; margin: 0 auto; background: #222; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.5); }
        header { background: linear-gradient(135deg, #d4af37 0%, #b8960f 100%); color: #000; padding: 30px; text-align: center; }
        header h1 { font-size: 28px; font-weight: 700; margin-bottom: 8px; }
        .content { padding: 40px; }
        .section { margin-bottom: 40px; }
        .section h2 { color: #d4af37; font-size: 20px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #d4af37; }
        .section h3 { color: #e0e0e0; font-size: 16px; margin-top: 15px; margin-bottom: 10px; }
        .code-block { background: #1a1a1a; border-left: 4px solid #d4af37; padding: 15px; border-radius: 4px; margin: 15px 0; overflow-x: auto; font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.5; }
        .code-block code { color: #4caf50; }
        .form-group { margin: 20px 0; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #d4af37; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; background: #1a1a1a; border: 1px solid #d4af37; border-radius: 4px; color: #e0e0e0; font-family: monospace; }
        .form-group textarea { min-height: 100px; }
        .button { background: #d4af37; color: #000; padding: 12px 25px; border-radius: 4px; border: none; cursor: pointer; font-weight: 600; margin: 10px 0; }
        .button:hover { background: #b8960f; }
        .button:disabled { background: #666; cursor: not-allowed; }
        .success-message { background: #1a2e1a; border-left: 4px solid #4caf50; padding: 15px; border-radius: 4px; margin: 15px 0; color: #4caf50; }
        .error-message { background: #1a1a1a; border-left: 4px solid #ff6b6b; padding: 15px; border-radius: 4px; margin: 15px 0; color: #ff6b6b; }
        .info-box { background: #1a1a2e; border-left: 4px solid #2196f3; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .warning-box { background: #2a1810; border-left: 4px solid #ff9800; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .method { background: #2a2a2a; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #d4af37; }
        footer { background: #1a1a1a; border-top: 1px solid #333; padding: 20px; text-align: center; color: #999; font-size: 12px; }
        .tab-buttons { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab-button { background: #333; color: #e0e0e0; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        .tab-button.active { background: #d4af37; color: #000; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>🔄 Google Search Console API Submission</h1>
        <p>Automatically submit your sitemap to Google</p>
    </header>

    <div class="content">

        <!-- Section 1: Overview -->
        <div class="section">
            <h2>📌 Overview</h2>
            <p>There are multiple ways to submit your sitemap to Google:</p>
            
            <div class="method">
                <h3>✅ Method 1: Manual (Recommended First Time)</h3>
                <p><strong>Steps:</strong> Use Google Search Console web interface</p>
                <p><strong>Time:</strong> 5-10 minutes</p>
                <p><strong>Best For:</strong> Initial setup and verification</p>
                <p><strong>Note:</strong> See our complete guide at <code>setup/google-search-console.php</code></p>
            </div>

            <div class="method">
                <h3>🔄 Method 2: IndexNow API (Instant Notifications)</h3>
                <p><strong>Steps:</strong> Send real-time indexing hints</p>
                <p><strong>Time:</strong> Automatic on page publish</p>
                <p><strong>Best For:</strong> Notifying Google of new content</p>
                <p><strong>Note:</strong> Works with Google, Bing, Yandex</p>
            </div>

            <div class="method">
                <h3>🔐 Method 3: Google Search Console API (Advanced)</h3>
                <p><strong>Steps:</strong> OAuth authentication + API calls</p>
                <p><strong>Time:</strong> 30 minutes setup, then automated</p>
                <p><strong>Best For:</strong> Full programmatic control</p>
                <p><strong>Note:</strong> Requires OAuth 2.0 credentials</p>
            </div>
        </div>

        <!-- Section 2: IndexNow API (No Authentication Needed!) -->
        <div class="section">
            <h2>🚀 Method 2: IndexNow API (EASIEST - No OAuth!)</h2>
            
            <div class="info-box">
                <strong>Why IndexNow?</strong> It's the easiest method! No OAuth needed. Just send a simple HTTPS request and Google will be notified immediately.
            </div>

            <div class="method">
                <h3>Step 1: Generate IndexNow Key</h3>
                <p>Your unique IndexNow API key:</p>
                <div class="code-block">
                    <code id="indexnow-key"><?php 
                        // Generate a unique key for this domain
                        $key = hash('sha256', SITE_URL . $_SERVER['SERVER_NAME'] . date('Y'));
                        echo substr($key, 0, 32); 
                    ?></code>
                </div>
                <button class="button" onclick="copyToClipboard('indexnow-key')">📋 Copy Key</button>
            </div>

            <div class="method">
                <h3>Step 2: Create IndexNow Configuration File</h3>
                <p>Create this file on your server:</p>
                <div class="code-block">
                    <code>/.well-known/IndexNow-<?php echo substr(hash('sha256', SITE_URL . $_SERVER['SERVER_NAME'] . date('Y')), 0, 32); ?></code>
                </div>
                <p><strong>File content:</strong></p>
                <div class="code-block">
                    <code><?php echo substr(hash('sha256', SITE_URL . $_SERVER['SERVER_NAME'] . date('Y')), 0, 32); ?></code>
                </div>

                <div class="info-box">
                    <strong>Note:</strong> You can use the code below to automatically create this file.
                </div>
            </div>

            <div class="method">
                <h3>Step 3: Submit Your URLs</h3>
                <p>Use the form below to submit URLs to IndexNow:</p>

                <form id="indexnow-form" style="margin-top: 15px;">
                    <div class="form-group">
                        <label for="indexnow-urls">URLs to Submit (one per line)</label>
                        <textarea id="indexnow-urls" placeholder="<?php echo SITE_URL . '/index.php&#10;'; echo SITE_URL . '/shop.php&#10;'; echo $sitemapUrl; ?>" required></textarea>
                    </div>
                    <button type="button" class="button" onclick="submitIndexNow()">🚀 Submit to IndexNow</button>
                    <div id="indexnow-response" style="margin-top: 15px;"></div>
                </form>
            </div>
        </div>

        <!-- Section 3: Google Search Console API (Full Control) -->
        <div class="section">
            <h2>🔐 Method 3: Google Search Console API (Full Control)</h2>

            <div class="warning-box">
                <strong>⚠️ More Complex:</strong> This method requires OAuth 2.0 setup. Use IndexNow method above instead for simplicity!
            </div>

            <div class="method">
                <h3>Step 1: Get OAuth Credentials</h3>
                <ol style="margin-left: 20px; margin-top: 10px;">
                    <li>Go to <strong>Google Cloud Console:</strong> https://console.cloud.google.com/</li>
                    <li>Create new project: "FSPO Ltd SEO"</li>
                    <li>Enable APIs:
                        <ul style="margin-left: 20px; margin-top: 5px;">
                            <li>Search Console API</li>
                            <li>Google Drive API (for verification)</li>
                        </ul>
                    </li>
                    <li>Create OAuth 2.0 credentials (Service Account)</li>
                    <li>Download JSON file and save to <code>/setup/gsc-credentials.json</code></li>
                </ol>
            </div>

            <div class="method">
                <h3>Step 2: Install Required Library</h3>
                <p>Run in your project root:</p>
                <div class="code-block">
                    <code>composer require google/apiclient google/apiclient-services</code>
                </div>
            </div>

            <div class="method">
                <h3>Step 3: Submit Sitemap via API</h3>
                <p>Use the form below:</p>

                <form id="gsc-api-form" style="margin-top: 15px;">
                    <div class="form-group">
                        <label for="gsc-domain">Domain (without http://)</label>
                        <input type="text" id="gsc-domain" value="<?php echo htmlspecialchars($domainUrl); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="gsc-sitemap">Sitemap URL</label>
                        <input type="text" id="gsc-sitemap" value="<?php echo htmlspecialchars($sitemapUrl); ?>" readonly>
                    </div>
                    <button type="button" class="button" onclick="submitGSCAPI()">📤 Submit to GSC API</button>
                    <div id="gsc-api-response" style="margin-top: 15px;"></div>
                </form>

                <div class="info-box" style="margin-top: 20px;">
                    <strong>Note:</strong> This requires OAuth credentials and Composer installation. 
                    If you haven't set up credentials, stick with IndexNow method above.
                </div>
            </div>
        </div>

        <!-- Section 4: Automatic Submission Script -->
        <div class="section">
            <h2>⚡ Bonus: Auto-Submit New Pages to IndexNow</h2>

            <p>Add this code to your product/category creation script to automatically notify Google:</p>

            <div class="code-block"><code>// Add this after saving product to database
$pageUrl = SITE_URL . '/product.php?id=' . $product_id;
submitToIndexNow([$pageUrl]);

// Function to submit URLs to IndexNow
function submitToIndexNow($urls) {
    $indexnowKey = '<?php echo substr(hash('sha256', SITE_URL . $_SERVER['SERVER_NAME'] . date('Y')), 0, 32); ?>';
    
    $payload = [
        'host' => parse_url(SITE_URL, PHP_URL_HOST),
        'key' => $indexnowKey,
        'urlList' => $urls
    ];
    
    $ch = curl_init('https://www.bing.com/indexnow');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_SSL_VERIFYPEER => true
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 200; // Success if status 200
}</code></div>

            <p style="margin-top: 15px;"><strong>Where to add this:</strong> In your product/category creation functions, after you save to database.</p>
        </div>

        <!-- Section 5: Monitoring -->
        <div class="section">
            <h2>📊 Monitor Submissions</h2>

            <p>Create a simple logging function to track submissions:</p>

            <div class="code-block"><code>// Log IndexNow submissions to database
function logIndexNowSubmission($url, $status) {
    $db = getDB();
    $db->prepare("INSERT INTO indexnow_log 
        (url, status, submitted_at) 
        VALUES (?, ?, NOW())")
        ->execute([$url, $status]);
}</code></div>

            <p style="margin-top: 15px;"><strong>Create this table:</strong></p>

            <div class="code-block"><code>CREATE TABLE indexnow_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    url VARCHAR(500),
    status VARCHAR(50),
    submitted_at TIMESTAMP,
    INDEX(submitted_at)
);</code></div>
        </div>

        <!-- Section 6: Verification -->
        <div class="section">
            <h2>✅ Verify Setup</h2>

            <h3>Check IndexNow Configuration</h3>
            <button class="button" onclick="checkIndexNowFile()">🔍 Check .well-known File</button>
            <div id="indexnow-check-result" style="margin-top: 15px;"></div>

            <h3 style="margin-top: 20px;">Check Sitemap</h3>
            <button class="button" onclick="checkSitemap()">🔍 Check Sitemap</button>
            <div id="sitemap-check-result" style="margin-top: 15px;"></div>

            <h3 style="margin-top: 20px;">Test Submission</h3>
            <button class="button" onclick="testIndexNowSubmission()">🚀 Test IndexNow</button>
            <div id="test-result" style="margin-top: 15px;"></div>
        </div>

        <!-- Section 7: Troubleshooting -->
        <div class="section">
            <h2>🔧 Troubleshooting</h2>

            <div class="method">
                <h3>❌ "Network error" when submitting</h3>
                <p><strong>Solutions:</strong></p>
                <ul style="margin-left: 20px;">
                    <li>Check if your server has outbound HTTPS access</li>
                    <li>Verify cURL is enabled in PHP: <?php echo function_exists('curl_init') ? '✅ Enabled' : '❌ Disabled'; ?></li>
                    <li>Check firewall doesn't block bing.com</li>
                </ul>
            </div>

            <div class="method">
                <h3>❌ "Invalid key" error</h3>
                <p><strong>Solutions:</strong></p>
                <ul style="margin-left: 20px;">
                    <li>Verify .well-known file exists</li>
                    <li>Check key matches exactly</li>
                    <li>Wait 24 hours for key to propagate</li>
                </ul>
            </div>

            <div class="method">
                <h3>❌ Sitemap not being indexed</h3>
                <p><strong>Solutions:</strong></p>
                <ul style="margin-left: 20px;">
                    <li>Check robots.txt doesn't block /sitemap.xml</li>
                    <li>Verify sitemap has valid XML structure</li>
                    <li>Check URLs in sitemap are accessible</li>
                </ul>
            </div>
        </div>

        <!-- Section 8: Dashboard -->
        <div class="section">
            <h2>📈 Quick Stats</h2>

            <table style="width: 100%; margin-top: 15px;">
                <tr>
                    <td><strong>Domain:</strong></td>
                    <td><code><?php echo htmlspecialchars($domainUrl); ?></code></td>
                </tr>
                <tr>
                    <td><strong>Sitemap URL:</strong></td>
                    <td><code><?php echo htmlspecialchars($sitemapUrl); ?></code></td>
                </tr>
                <tr>
                    <td><strong>cURL Support:</strong></td>
                    <td><?php echo function_exists('curl_init') ? '✅ Yes' : '❌ No'; ?></td>
                </tr>
                <tr>
                    <td><strong>PHP Version:</strong></td>
                    <td><?php echo PHP_VERSION; ?></td>
                </tr>
                <tr>
                    <td><strong>OpenSSL:</strong></td>
                    <td><?php echo defined('OPENSSL_VERSION_TEXT') ? '✅ Yes' : '❌ No'; ?></td>
                </tr>
            </table>
        </div>

    </div>

    <footer>
        <p>Generated: <?php echo date('F j, Y @ g:i A'); ?> | FSPO Ltd Google Submission Tool</p>
        <p style="margin-top: 10px;">For manual setup guide, visit <strong>setup/google-search-console.php</strong></p>
    </footer>

</div>

<script>
// Copy to clipboard
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent;
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied: ' + text);
    });
}

// Check IndexNow file
async function checkIndexNowFile() {
    const result = document.getElementById('indexnow-check-result');
    result.innerHTML = '⏳ Checking...';
    
    try {
        const response = await fetch('/.well-known/check.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'check_indexnow'})
        });
        const data = await response.json();
        result.innerHTML = data.status === 'found' 
            ? '<div class="success-message">✅ IndexNow file found!</div>'
            : '<div class="error-message">❌ IndexNow file not found. Please create /.well-known/IndexNow-KEY file</div>';
    } catch(e) {
        result.innerHTML = '<div class="error-message">❌ Error: ' + e.message + '</div>';
    }
}

// Check sitemap
async function checkSitemap() {
    const result = document.getElementById('sitemap-check-result');
    result.innerHTML = '⏳ Checking...';
    
    try {
        const response = await fetch('/sitemap.xml');
        const text = await response.text();
        const hasProducts = text.includes('<url>');
        result.innerHTML = hasProducts 
            ? '<div class="success-message">✅ Sitemap found with ' + (text.match(/<url>/g) || []).length + ' URLs</div>'
            : '<div class="error-message">❌ Sitemap invalid or empty</div>';
    } catch(e) {
        result.innerHTML = '<div class="error-message">❌ Error: ' + e.message + '</div>';
    }
}

// Submit to IndexNow
async function submitIndexNow() {
    const urls = document.getElementById('indexnow-urls').value.trim().split('\n').filter(u => u);
    const response = document.getElementById('indexnow-response');
    
    if (urls.length === 0) {
        response.innerHTML = '<div class="error-message">❌ Please enter at least one URL</div>';
        return;
    }
    
    response.innerHTML = '⏳ Submitting ' + urls.length + ' URL(s)...';
    
    try {
        const result = await fetch('/.well-known/submit.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({urls: urls})
        });
        const data = await result.json();
        response.innerHTML = data.success 
            ? '<div class="success-message">✅ Submitted! Status: ' + data.status + '</div>'
            : '<div class="error-message">❌ Error: ' + data.error + '</div>';
    } catch(e) {
        response.innerHTML = '<div class="error-message">❌ Network error: ' + e.message + '</div>';
    }
}

// Submit to GSC API
async function submitGSCAPI() {
    alert('GSC API submission requires:\n1. Google Cloud credentials\n2. OAuth setup\n\nFor now, use the manual Google Search Console or IndexNow method above.');
}

// Test IndexNow
async function testIndexNowSubmission() {
    const result = document.getElementById('test-result');
    const testUrl = '<?php echo SITE_URL; ?>/test-page.php';
    result.innerHTML = '⏳ Testing...';
    
    try {
        const testResult = await fetch('/.well-known/submit.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({urls: [testUrl], test: true})
        });
        const data = await testResult.json();
        if (data.success) {
            result.innerHTML = '<div class="success-message">✅ Test successful! Response: ' + JSON.stringify(data) + '</div>';
        } else {
            result.innerHTML = '<div class="error-message">❌ Test failed: ' + data.error + '</div>';
        }
    } catch(e) {
        result.innerHTML = '<div class="error-message">❌ Network error: ' + e.message + '</div>';
    }
}

// Auto-load on page
window.addEventListener('load', () => {
    console.log('Google Search Console API tool loaded');
});
</script>

</body>
</html>
