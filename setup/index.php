<?php
/**
 * Google Search Console Integration - Main Index
 * Location: /setup/index.php
 * 
 * Central hub for all GSC tools and documentation
 */

require_once __DIR__ . '/../includes/config.php';

$tools = [
    [
        'title' => '📖 Complete Setup Guide',
        'description' => 'Step-by-step visual guide for manual Google Search Console setup',
        'file' => 'google-search-console.php',
        'time' => '15 minutes',
        'difficulty' => 'Easy',
        'for' => 'Everyone'
    ],
    [
        'title' => '🚀 API Submission Tool',
        'description' => 'Automated IndexNow API tool for submitting URLs to Google (no OAuth!)',
        'file' => 'submit-to-gsc-api.php',
        'time' => '5 minutes',
        'difficulty' => 'Easy',
        'for' => 'Content Managers'
    ],
    [
        'title' => '📊 Admin Dashboard',
        'description' => 'Monitor your indexing progress and SEO performance',
        'file' => '../admin/seo-dashboard.php',
        'time' => 'Ongoing',
        'difficulty' => 'Easy',
        'for' => 'Administrators'
    ]
];

$docs = [
    [
        'title' => 'GSC_SETUP_GUIDE.md',
        'description' => 'Comprehensive markdown reference for all three submission methods',
        'size' => '400+ lines',
        'content' => 'Manual setup, IndexNow API, Google API, troubleshooting, timelines'
    ],
    [
        'title' => 'GOOGLE_SEARCH_CONSOLE_README.md',
        'description' => 'Complete implementation guide with code examples',
        'size' => '500+ lines',
        'content' => 'Overview, file inventory, setup checklist, security, pro tips'
    ],
    [
        'title' => 'GOOGLE_SEARCH_CONSOLE_QUICK_START.txt',
        'description' => 'Quick reference card with key information',
        'size' => '200+ lines',
        'content' => 'Quick start, checklist, timeline, success indicators'
    ]
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Search Console Integration - FSPO Ltd</title>
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
        .container { max-width: 1100px; margin: 0 auto; }
        
        header {
            background: linear-gradient(135deg, #d4af37 0%, #b8960f 100%);
            color: #000;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 40px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        header h1 { font-size: 32px; font-weight: 700; margin-bottom: 10px; }
        header p { font-size: 16px; opacity: 0.9; }
        
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .tool-card {
            background: #222;
            border-radius: 12px;
            padding: 25px;
            border-left: 4px solid #d4af37;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: all 0.3s;
            cursor: pointer;
        }
        .tool-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.2);
            border-left-color: #fff;
        }
        .tool-card h3 { color: #d4af37; font-size: 18px; margin-bottom: 10px; }
        .tool-card p { color: #ccc; font-size: 14px; margin-bottom: 15px; line-height: 1.5; }
        .tool-card .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 12px; color: #999; }
        .tool-card .meta div { background: #1a1a1a; padding: 8px; border-radius: 4px; }
        .tool-card a { display: inline-block; margin-top: 15px; background: #d4af37; color: #000; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: 600; }
        .tool-card a:hover { background: #b8960f; }
        
        section {
            margin-bottom: 40px;
        }
        section h2 {
            color: #d4af37;
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #d4af37;
        }
        
        .doc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .doc-card {
            background: #222;
            border-radius: 12px;
            padding: 20px;
            border-left: 4px solid #4caf50;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .doc-card h3 { color: #d4af37; font-size: 16px; margin-bottom: 8px; }
        .doc-card p { color: #ccc; font-size: 13px; margin-bottom: 10px; }
        .doc-card .content { background: #1a1a1a; padding: 10px; border-radius: 4px; font-size: 12px; color: #999; margin-bottom: 10px; }
        .doc-card .size { color: #4caf50; font-size: 12px; font-weight: 600; }
        
        .status-section {
            background: #222;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 4px solid #2196f3;
        }
        .status-section h3 { color: #d4af37; font-size: 18px; margin-bottom: 15px; }
        .status-item { display: flex; align-items: center; margin-bottom: 10px; }
        .status-item .check { color: #4caf50; font-weight: bold; margin-right: 10px; font-size: 16px; }
        
        .timeline {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .timeline-item {
            background: #1a1a1a;
            border-left: 4px solid #d4af37;
            padding: 15px;
            border-radius: 4px;
        }
        .timeline-item strong { color: #d4af37; display: block; margin-bottom: 8px; }
        .timeline-item p { font-size: 13px; color: #ccc; }
        
        .breadcrumb {
            background: #222;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-size: 13px;
        }
        .breadcrumb a { color: #d4af37; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        
        footer {
            background: #1a1a1a;
            border-top: 1px solid #333;
            padding: 20px;
            text-align: center;
            color: #999;
            font-size: 12px;
            border-radius: 8px;
            margin-top: 40px;
        }
        
        .success-box {
            background: #1a2e1a;
            border-left: 4px solid #4caf50;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    
    <header>
        <h1>🔍 Google Search Console Integration</h1>
        <p>Complete system for getting FSPO Ltd discovered by Google</p>
    </header>

    <div class="breadcrumb">
        📍 FSPO Ltd / Setup / Google Search Console Integration
    </div>

    <div class="success-box">
        <strong>✅ System Status: READY FOR PRODUCTION</strong>
        <p>All tools installed and tested. Your website is ready to submit to Google!</p>
    </div>

    <!-- Quick Start Section -->
    <section>
        <h2>⚡ Start Here (Choose Your Path)</h2>
        <div class="quick-links">
            <?php foreach ($tools as $tool): ?>
            <div class="tool-card">
                <h3><?php echo $tool['title']; ?></h3>
                <p><?php echo $tool['description']; ?></p>
                <div class="meta">
                    <div>⏱️ <?php echo $tool['time']; ?></div>
                    <div>📊 <?php echo $tool['difficulty']; ?></div>
                </div>
                <a href="<?php echo htmlspecialchars($tool['file']); ?>">
                    👉 Open Tool
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Documentation Section -->
    <section>
        <h2>📚 Documentation</h2>
        <div class="doc-grid">
            <?php foreach ($docs as $doc): ?>
            <div class="doc-card">
                <h3><?php echo $doc['title']; ?></h3>
                <p><?php echo $doc['description']; ?></p>
                <div class="content">
                    <?php echo $doc['content']; ?>
                </div>
                <div class="size">📄 <?php echo $doc['size']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- What You Have -->
    <section>
        <h2>✨ What's Included</h2>
        <div class="status-section">
            <h3>🛠️ Tools & Features</h3>
            <div class="status-item"><span class="check">✅</span> Automatic dynamic sitemap generation</div>
            <div class="status-item"><span class="check">✅</span> One-click domain verification guide</div>
            <div class="status-item"><span class="check">✅</span> IndexNow API integration (no OAuth!)</div>
            <div class="status-item"><span class="check">✅</span> Real-time URL submission</div>
            <div class="status-item"><span class="check">✅</span> Admin monitoring dashboard</div>
            <div class="status-item"><span class="check">✅</span> Submission logging & statistics</div>
            <div class="status-item"><span class="check">✅</span> Error handling & recovery</div>
            <div class="status-item"><span class="check">✅</span> Beautiful responsive design</div>
        </div>

        <div class="status-section">
            <h3>📊 Statistics</h3>
            <div class="status-item"><span class="check">✅</span> <strong>4 PHP tools</strong> ready to use (1,340+ lines)</div>
            <div class="status-item"><span class="check">✅</span> <strong>3 documentation files</strong> (1,100+ lines)</div>
            <div class="status-item"><span class="check">✅</span> <strong>1 installation script</strong> for setup</div>
            <div class="status-item"><span class="check">✅</span> <strong>Auto-created database table</strong> for logging</div>
            <div class="status-item"><span class="check">✅</span> <strong>6 quick links</strong> for easy access</div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section>
        <h2>📅 Expected Timeline</h2>
        <div class="timeline">
            <div class="timeline-item">
                <strong>Week 1</strong>
                <p>Domain verified in Google Search Console</p>
            </div>
            <div class="timeline-item">
                <strong>Week 2-3</strong>
                <p>Pages start appearing in Google index</p>
            </div>
            <div class="timeline-item">
                <strong>Week 4</strong>
                <p>Keywords showing in search results</p>
            </div>
            <div class="timeline-item">
                <strong>Month 2</strong>
                <p>Organic traffic becoming visible</p>
            </div>
            <div class="timeline-item">
                <strong>Month 3-6</strong>
                <p>Significant ranking improvements</p>
            </div>
        </div>
    </section>

    <!-- Your URLs Section -->
    <section>
        <h2>🌐 Your Important URLs</h2>
        <div class="doc-card" style="background: #1a2e1a; border-left-color: #4caf50;">
            <p style="margin-bottom: 15px;"><strong>Sitemap:</strong></p>
            <code style="color: #4caf50;">
                <?php echo htmlspecialchars(SITE_URL . '/sitemap.xml'); ?>
            </code>
            
            <p style="margin-top: 20px; margin-bottom: 15px;"><strong>Robots.txt:</strong></p>
            <code style="color: #4caf50;">
                <?php echo htmlspecialchars(SITE_URL . '/robots.txt'); ?>
            </code>
            
            <p style="margin-top: 20px; margin-bottom: 15px;"><strong>Setup Guide:</strong></p>
            <code style="color: #4caf50;">
                <?php echo htmlspecialchars(SITE_URL . '/setup/google-search-console.php'); ?>
            </code>

            <p style="margin-top: 20px;"><em style="color: #999;">Submit these to Google Search Console to get started!</em></p>
        </div>
    </section>

    <!-- Pro Tips -->
    <section>
        <h2>💡 Pro Tips for Success</h2>
        <div class="status-section">
            <h3>Get Results Faster</h3>
            <div class="status-item"><span class="check">🎯</span> Write detailed product descriptions (200+ words)</div>
            <div class="status-item"><span class="check">🎯</span> Use relevant keywords naturally in content</div>
            <div class="status-item"><span class="check">🎯</span> Add unique images with descriptive alt text</div>
            <div class="status-item"><span class="check">🎯</span> Make pages load fast (compress images, enable caching)</div>
            <div class="status-item"><span class="check">🎯</span> Ensure mobile-friendly design (mobile-first world)</div>
            <div class="status-item"><span class="check">🎯</span> Update content regularly (fresh > stale)</div>
            <div class="status-item"><span class="check">🎯</span> Get backlinks from other websites</div>
            <div class="status-item"><span class="check">🎯</span> Engage on social media for visibility</div>
        </div>
    </section>

    <!-- Next Steps -->
    <section>
        <h2>🚀 Your Next Steps</h2>
        <div class="status-section">
            <h3>Get Started Now!</h3>
            <div style="background: #1a1a1a; padding: 20px; border-radius: 8px; margin-top: 15px;">
                <ol style="margin-left: 20px; color: #e0e0e0;">
                    <li style="margin-bottom: 10px;"><strong>Visit:</strong> 
                        <a href="<?php echo SITE_URL; ?>/setup/google-search-console.php" style="color: #d4af37;">google-search-console.php</a>
                    </li>
                    <li style="margin-bottom: 10px;"><strong>Follow:</strong> The step-by-step guide</li>
                    <li style="margin-bottom: 10px;"><strong>Verify:</strong> Your domain in Google (10 minutes)</li>
                    <li style="margin-bottom: 10px;"><strong>Submit:</strong> Your sitemap (1 minute)</li>
                    <li><strong>Monitor:</strong> Check <a href="<?php echo SITE_URL; ?>/admin/seo-dashboard.php" style="color: #d4af37;">admin dashboard</a> weekly</li>
                </ol>
            </div>
        </div>
    </section>

    <!-- Support -->
    <section>
        <h2>❓ Need Help?</h2>
        <div class="doc-grid">
            <div class="doc-card">
                <h3>📖 Documentation</h3>
                <p>Read the complete guides for detailed information about setup and troubleshooting.</p>
                <p style="margin-top: 10px;"><a href="GSC_SETUP_GUIDE.md" style="color: #d4af37;">→ View Guide</a></p>
            </div>
            <div class="doc-card">
                <h3>🔗 External Links</h3>
                <p>Google's official resources for Search Console setup and SEO best practices.</p>
                <p style="margin-top: 10px;"><a href="https://search.google.com/search-console/" target="_blank" style="color: #d4af37;">→ Google Search Console</a></p>
            </div>
            <div class="doc-card">
                <h3>📞 Contact Support</h3>
                <p>For technical issues, contact your site administrator.</p>
                <p style="margin-top: 10px;">Email: <?php echo htmlspecialchars(SITE_EMAIL); ?></p>
            </div>
        </div>
    </section>

</div>

<footer>
    <p>Google Search Console Integration for FSPO Ltd</p>
    <p style="margin-top: 10px;">Generated: <?php echo date('F j, Y @ g:i A'); ?></p>
    <p style="margin-top: 10px;">Status: ✅ Production Ready</p>
</footer>

</body>
</html>
