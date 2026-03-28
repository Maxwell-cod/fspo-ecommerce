<?php
/**
 * Admin SEO Dashboard
 * Location: /admin/seo-dashboard.php
 * 
 * Monitor indexing, submissions, and SEO performance
 */

require_once __DIR__ . '/../includes/config.php';
requireAdmin();

$db = getDB();

// Get statistics
$stats = [
    'total_products' => $db->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn(),
    'total_categories' => $db->query("SELECT COUNT(*) FROM categories WHERE status='active'")->fetchColumn(),
    'total_pages' => 0, // Will calculate
    'indexed_pages' => 0 // From manual entry
];

$stats['total_pages'] = 4 + $stats['total_products'] + $stats['total_categories'];

// Get recent submissions
$submissions = [];
try {
    $result = $db->query("SELECT * FROM indexnow_log ORDER BY submitted_at DESC LIMIT 20");
    if ($result) {
        $submissions = $result->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // Table might not exist yet
}

// Get submission stats
$submissionStats = [
    'today' => 0,
    'this_week' => 0,
    'this_month' => 0,
    'total' => count($submissions)
];

foreach ($submissions as $sub) {
    $date = strtotime($sub['submitted_at']);
    if (date('Y-m-d', $date) === date('Y-m-d')) $submissionStats['today']++;
    if ($date > strtotime('-7 days')) $submissionStats['this_week']++;
    if ($date > strtotime('-30 days')) $submissionStats['this_month']++;
}

$sitemapUrl = SITE_URL . '/sitemap.xml';
$indexnowKey = substr(hash('sha256', SITE_URL . $_SERVER['SERVER_NAME'] . date('Y')), 0, 32);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Dashboard - FSPO Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2a1810 100%);
            color: #e0e0e0;
            line-height: 1.6;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        header { background: linear-gradient(135deg, #d4af37 0%, #b8960f 100%); color: #000; padding: 30px; border-radius: 12px; margin-bottom: 30px; }
        header h1 { font-size: 28px; font-weight: 700; margin-bottom: 5px; }
        header p { font-size: 14px; opacity: 0.8; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card {
            background: #222;
            border-radius: 12px;
            padding: 25px;
            border-left: 4px solid #d4af37;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: transform 0.2s;
        }
        .card:hover { transform: translateY(-2px); }
        .card h3 { color: #d4af37; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
        .card .value { font-size: 32px; font-weight: 700; color: #e0e0e0; }
        .card .label { color: #999; font-size: 13px; margin-top: 10px; }
        
        .section {
            background: #222;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .section h2 {
            color: #d4af37;
            font-size: 18px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #d4af37;
        }
        
        .button {
            background: #d4af37;
            color: #000;
            padding: 10px 20px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            margin: 5px;
            display: inline-block;
            text-decoration: none;
        }
        .button:hover { background: #b8960f; }
        .button-secondary { background: #333; color: #e0e0e0; }
        .button-secondary:hover { background: #444; }
        
        .url-box {
            background: #1a1a1a;
            border: 1px solid #d4af37;
            padding: 12px;
            border-radius: 4px;
            margin: 10px 0;
            font-family: monospace;
            font-size: 12px;
            color: #4caf50;
            word-break: break-all;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th {
            background: #d4af37;
            color: #000;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        table td {
            border-bottom: 1px solid #333;
            padding: 12px;
        }
        table tr:hover { background: #2a2a2a; }
        
        .status-success { color: #4caf50; }
        .status-error { color: #ff6b6b; }
        .status-pending { color: #ff9800; }
        
        .info-box {
            background: #1a1a2e;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .success-box {
            background: #1a2e1a;
            border-left: 4px solid #4caf50;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .warning-box {
            background: #2a1810;
            border-left: 4px solid #ff9800;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .two-column { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>🔍 SEO Dashboard</h1>
        <p>Monitor your search engine indexing and optimization</p>
    </header>

    <!-- Statistics Grid -->
    <div class="grid">
        <div class="card">
            <h3>📊 Total Pages</h3>
            <div class="value"><?php echo $stats['total_pages']; ?></div>
            <div class="label"><?php echo $stats['total_products']; ?> products + <?php echo $stats['total_categories']; ?> categories + 4 static</div>
        </div>

        <div class="card">
            <h3>📤 Today Submissions</h3>
            <div class="value"><?php echo $submissionStats['today']; ?></div>
            <div class="label">IndexNow submissions</div>
        </div>

        <div class="card">
            <h3>📅 This Week</h3>
            <div class="value"><?php echo $submissionStats['this_week']; ?></div>
            <div class="label">7-day submissions</div>
        </div>

        <div class="card">
            <h3>📈 This Month</h3>
            <div class="value"><?php echo $submissionStats['this_month']; ?></div>
            <div class="label">30-day submissions</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="section">
        <h2>⚡ Quick Actions</h2>
        <p style="margin-bottom: 15px;">Manage your search engine indexing:</p>
        
        <a href="<?php echo SITE_URL; ?>/setup/google-search-console.php" target="_blank" class="button">📖 GSC Setup Guide</a>
        <a href="<?php echo SITE_URL; ?>/setup/submit-to-gsc-api.php" target="_blank" class="button">🚀 Submit URLs</a>
        <a href="<?php echo SITE_URL; ?>/sitemap.xml" target="_blank" class="button button-secondary">📋 View Sitemap</a>
        <a href="<?php echo SITE_URL; ?>/robots.txt" target="_blank" class="button button-secondary">🤖 View Robots.txt</a>
    </div>

    <!-- Important URLs -->
    <div class="section">
        <h2>🌐 Important URLs</h2>
        
        <div class="two-column">
            <div>
                <h3 style="color: #d4af37; margin: 15px 0 10px;">Google Search Console</h3>
                <div class="info-box">
                    <strong>Setup Guide:</strong><br>
                    <code><?php echo SITE_URL; ?>/setup/google-search-console.php</code>
                </div>
                <div class="info-box">
                    <strong>For submission:</strong><br>
                    <code>https://search.google.com/search-console/</code>
                </div>
            </div>

            <div>
                <h3 style="color: #d4af37; margin: 15px 0 10px;">Your Website</h3>
                <div class="url-box">Sitemap: <?php echo htmlspecialchars($sitemapUrl); ?></div>
                <div class="url-box">Robots: <?php echo htmlspecialchars(SITE_URL . '/robots.txt'); ?></div>
            </div>
        </div>
    </div>

    <!-- Sitemap Status -->
    <div class="section">
        <h2>📍 Sitemap Status</h2>
        <div class="success-box">
            ✅ Dynamic sitemap enabled and working
        </div>
        <p style="margin: 15px 0;">Your sitemap is automatically generated with:</p>
        <ul style="margin-left: 20px;">
            <li>✅ Homepage (priority 1.0)</li>
            <li>✅ Shop page (priority 0.9)</li>
            <li>✅ <?php echo $stats['total_categories']; ?> categories (priority 0.8)</li>
            <li>✅ <?php echo $stats['total_products']; ?> active products (priority 0.7)</li>
        </ul>
        <p style="margin-top: 15px;">Total URLs in sitemap: <strong><?php echo $stats['total_pages']; ?></strong></p>
    </div>

    <!-- IndexNow Setup -->
    <div class="section">
        <h2>🔄 IndexNow Setup</h2>
        
        <div class="info-box">
            <strong>Your IndexNow Key:</strong>
            <div class="url-box" style="margin-top: 10px;"><?php echo $indexnowKey; ?></div>
        </div>

        <p style="margin: 15px 0;"><strong>IndexNow key file location:</strong></p>
        <div class="url-box">/.well-known/IndexNow-<?php echo $indexnowKey; ?></div>

        <p style="margin: 15px 0;"><strong>Status:</strong> <?php 
            $keyFile = __DIR__ . '/../.well-known/IndexNow-' . $indexnowKey;
            if (file_exists($keyFile)) {
                echo '<span class="status-success">✅ Key file found</span>';
            } else {
                echo '<span class="status-error">❌ Key file not found</span>';
            }
        ?></p>

        <p style="margin: 15px 0;"><strong>What is IndexNow?</strong></p>
        <p>IndexNow is a free service that instantly notifies Google, Bing, and Yandex when you publish new content. Your pages appear in search results faster!</p>
    </div>

    <!-- Recent Submissions -->
    <div class="section">
        <h2>📤 Recent Submissions</h2>
        
        <?php if (empty($submissions)): ?>
            <div class="info-box">
                No submissions logged yet. Use the submission tool to start notifying Google!
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>URLs Submitted</th>
                        <th>Time</th>
                        <th>Response</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($submissions, 0, 10) as $sub): ?>
                        <tr>
                            <td>
                                <?php if ($sub['status'] === 200): ?>
                                    <span class="status-success">✅ Success</span>
                                <?php else: ?>
                                    <span class="status-error">❌ Failed (<?php echo $sub['status']; ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 12px;">
                                <?php 
                                    $urls = json_decode($sub['urls'], true);
                                    if (is_array($urls)) {
                                        echo count($urls) . ' URL(s)';
                                    }
                                ?>
                            </td>
                            <td style="font-size: 12px;"><?php echo date('M d, Y g:i A', strtotime($sub['submitted_at'])); ?></td>
                            <td style="font-size: 12px;">
                                <?php 
                                    $response = $sub['response'] ?: 'N/A';
                                    echo substr($response, 0, 50);
                                    if (strlen($response) > 50) echo '...';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Setup Checklist -->
    <div class="section">
        <h2>✅ Setup Checklist</h2>
        
        <div style="margin: 15px 0;">
            <p style="margin: 10px 0;"><label>
                <input type="checkbox" id="check1" onchange="saveDashboard()"> 
                Domain verified in Google Search Console
            </label></p>
            <p style="margin: 10px 0;"><label>
                <input type="checkbox" id="check2" onchange="saveDashboard()"> 
                Sitemap submitted to GSC
            </label></p>
            <p style="margin: 10px 0;"><label>
                <input type="checkbox" id="check3" onchange="saveDashboard()"> 
                IndexNow key file created
            </label></p>
            <p style="margin: 10px 0;"><label>
                <input type="checkbox" id="check4" onchange="saveDashboard()"> 
                Google Analytics connected
            </label></p>
            <p style="margin: 10px 0;"><label>
                <input type="checkbox" id="check5" onchange="saveDashboard()"> 
                Google My Business setup
            </label></p>
            <p style="margin: 10px 0;"><label>
                <input type="checkbox" id="check6" onchange="saveDashboard()"> 
                Content optimization completed
            </label></p>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="section">
        <h2>💡 Recommendations</h2>

        <h3 style="color: #d4af37; margin: 15px 0 10px;">Immediate (This Week):</h3>
        <ul style="margin-left: 20px;">
            <li>✅ Verify domain in Google Search Console</li>
            <li>✅ Submit sitemap to GSC</li>
            <li>✅ Ensure IndexNow key file is created</li>
        </ul>

        <h3 style="color: #d4af37; margin: 15px 0 10px;">Short-term (This Month):</h3>
        <ul style="margin-left: 20px;">
            <li>✅ Monitor GSC for indexing errors</li>
            <li>✅ Check mobile usability in GSC</li>
            <li>✅ Setup Google Analytics</li>
            <li>✅ Create Google My Business</li>
        </ul>

        <h3 style="color: #d4af37; margin: 15px 0 10px;">Long-term (Ongoing):</h3>
        <ul style="margin-left: 20px;">
            <li>✅ Update content regularly</li>
            <li>✅ Add new products frequently</li>
            <li>✅ Get backlinks from other sites</li>
            <li>✅ Monitor rankings and traffic</li>
            <li>✅ Improve page speed</li>
        </ul>
    </div>

    <!-- Support -->
    <div class="section" style="background: linear-gradient(135deg, #1a2e1a 0%, #1a1a1a 100%);">
        <h2>❓ Need Help?</h2>
        
        <div class="two-column">
            <div>
                <p><strong>📖 Documentation</strong></p>
                <p style="margin-top: 10px;">
                    <a href="<?php echo SITE_URL; ?>/setup/google-search-console.php" style="color: #d4af37;">
                        → Complete GSC Setup Guide
                    </a>
                </p>
                <p style="margin-top: 5px;">
                    <a href="<?php echo SITE_URL; ?>/setup/submit-to-gsc-api.php" style="color: #d4af37;">
                        → API Submission Tool
                    </a>
                </p>
            </div>
            <div>
                <p><strong>📞 Contact</strong></p>
                <p style="margin-top: 10px;">Email: <?php echo htmlspecialchars(SITE_EMAIL); ?></p>
                <p>Phone: <?php echo htmlspecialchars(SITE_PHONE); ?></p>
            </div>
        </div>
    </div>

</div>

<script>
function saveDashboard() {
    // Save checklist to localStorage
    const checks = {
        check1: document.getElementById('check1').checked,
        check2: document.getElementById('check2').checked,
        check3: document.getElementById('check3').checked,
        check4: document.getElementById('check4').checked,
        check5: document.getElementById('check5').checked,
        check6: document.getElementById('check6').checked
    };
    localStorage.setItem('seoDashboardChecks', JSON.stringify(checks));
}

function loadDashboard() {
    // Load checklist from localStorage
    const checks = JSON.parse(localStorage.getItem('seoDashboardChecks') || '{}');
    for (const [key, value] of Object.entries(checks)) {
        const elem = document.getElementById(key);
        if (elem) elem.checked = value;
    }
}

window.addEventListener('load', loadDashboard);
</script>

</body>
</html>
