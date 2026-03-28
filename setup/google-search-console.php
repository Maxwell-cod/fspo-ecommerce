<?php
/**
 * Google Search Console Setup & Submission Helper
 * Location: /setup/google-search-console.php
 * 
 * This script provides instructions and tools for submitting
 * your FSPO Ltd website to Google Search Console
 */

require_once __DIR__ . '/../includes/config.php';

// Get sitemap URL
$sitemapUrl = SITE_URL . '/sitemap.xml';
$domainUrl = SITE_URL;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Search Console Setup - FSPO Ltd</title>
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
        header p { font-size: 14px; opacity: 0.8; }
        .content { padding: 40px; }
        .section { margin-bottom: 40px; }
        .section h2 { color: #d4af37; font-size: 20px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #d4af37; }
        .section h3 { color: #e0e0e0; font-size: 16px; margin-top: 20px; margin-bottom: 10px; }
        .code-block { background: #1a1a1a; border-left: 4px solid #d4af37; padding: 15px; border-radius: 4px; margin: 15px 0; overflow-x: auto; font-family: 'Courier New', monospace; font-size: 13px; }
        .code-block code { color: #4caf50; }
        .step { background: #2a2a2a; padding: 20px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #d4af37; }
        .step-number { display: inline-block; background: #d4af37; color: #000; width: 30px; height: 30px; border-radius: 50%; text-align: center; line-height: 30px; font-weight: bold; margin-right: 10px; }
        .step p { margin: 10px 0; }
        .url-box { background: #1a1a1a; padding: 15px; border-radius: 8px; border: 1px solid #d4af37; margin: 10px 0; }
        .url-box strong { color: #d4af37; }
        .url-box code { color: #4caf50; }
        .button { display: inline-block; background: #d4af37; color: #000; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: 600; margin: 10px 0; cursor: pointer; border: none; }
        .button:hover { background: #b8960f; }
        .warning { background: #1a1a2e; border-left: 4px solid #ff6b6b; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .success { background: #1a2e1a; border-left: 4px solid #4caf50; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .info { background: #1a1a2e; border-left: 4px solid #2196f3; padding: 15px; border-radius: 4px; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th { background: #d4af37; color: #000; padding: 12px; text-align: left; font-weight: 600; }
        table td { border-bottom: 1px solid #333; padding: 12px; }
        table tr:hover { background: #2a2a2a; }
        footer { background: #1a1a1a; border-top: 1px solid #333; padding: 20px; text-align: center; color: #999; font-size: 12px; }
        .checklist { list-style: none; }
        .checklist li { padding: 10px; margin: 5px 0; background: #2a2a2a; border-radius: 4px; }
        .checklist li:before { content: "☐ "; color: #d4af37; font-weight: bold; margin-right: 10px; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>🔍 Google Search Console Setup</h1>
        <p>Submit your FSPO Ltd website to Google for indexing</p>
    </header>

    <div class="content">

        <!-- Section 1: What is Google Search Console -->
        <div class="section">
            <h2>📌 What is Google Search Console?</h2>
            <p>Google Search Console (GSC) is a FREE tool that helps you monitor and maintain your website's presence in Google Search results. It's essential for SEO.</p>
            
            <h3>What you can do:</h3>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>✅ Submit your sitemap to Google</li>
                <li>✅ Monitor search keywords that drive traffic</li>
                <li>✅ Check for indexing errors</li>
                <li>✅ See how Google crawls your site</li>
                <li>✅ Fix security issues</li>
                <li>✅ View mobile usability issues</li>
                <li>✅ Get alerts about problems</li>
            </ul>
        </div>

        <!-- Section 2: Step-by-Step Setup -->
        <div class="section">
            <h2>🚀 Step-by-Step Setup Guide</h2>

            <div class="step">
                <span class="step-number">1</span>
                <h3>Go to Google Search Console</h3>
                <p>Visit: <strong>https://search.google.com/search-console/</strong></p>
                <p>You'll need a Google account. If you don't have one, create it first at https://accounts.google.com</p>
            </div>

            <div class="step">
                <span class="step-number">2</span>
                <h3>Add Your Domain</h3>
                <p>Click <strong>"Add property"</strong> button</p>
                <p>Choose: <strong>"Domain"</strong> (not URL prefix)</p>
                <p>Enter your domain:</p>
                <div class="url-box">
                    <code><?php echo htmlspecialchars(preg_replace(['#^https?://#', '#/$#'], '', SITE_URL)); ?></code>
                </div>
                <p><small>Note: Remove http:// or https:// and any trailing slashes</small></p>
            </div>

            <div class="step">
                <span class="step-number">3</span>
                <h3>Verify Domain Ownership</h3>
                <p>Google will show you verification options:</p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li><strong>DNS TXT Record</strong> (Best - one-time setup)</li>
                    <li>HTML File Upload</li>
                    <li>HTML Meta Tag</li>
                    <li>Google Analytics</li>
                    <li>Google Tag Manager</li>
                </ul>
                <div class="info">
                    <strong>Recommended:</strong> Use DNS TXT Record (most reliable and permanent)
                </div>
            </div>

            <div class="step">
                <span class="step-number">4</span>
                <h3>Complete Verification</h3>
                <p>Once verified, Google will show a green checkmark</p>
                <p>This may take 24-48 hours to propagate</p>
            </div>

            <div class="step">
                <span class="step-number">5</span>
                <h3>Submit Your Sitemap</h3>
                <p>After verification, go to <strong>Sitemaps</strong> in GSC</p>
                <p>Click <strong>"Add new sitemap"</strong></p>
                <p>Enter your sitemap URL:</p>
                <div class="url-box">
                    <code><?php echo htmlspecialchars($sitemapUrl); ?></code>
                </div>
                <p>Google will fetch and analyze your sitemap automatically</p>
            </div>

        </div>

        <!-- Section 3: Your URLs -->
        <div class="section">
            <h2>🌐 Your Website URLs</h2>
            
            <table>
                <tr>
                    <th>What</th>
                    <th>URL</th>
                </tr>
                <tr>
                    <td>Domain</td>
                    <td><code><?php echo htmlspecialchars($domainUrl); ?></code></td>
                </tr>
                <tr>
                    <td>Sitemap</td>
                    <td><code><?php echo htmlspecialchars($sitemapUrl); ?></code></td>
                </tr>
                <tr>
                    <td>Robots.txt</td>
                    <td><code><?php echo htmlspecialchars(SITE_URL . '/robots.txt'); ?></code></td>
                </tr>
                <tr>
                    <td>Homepage</td>
                    <td><code><?php echo htmlspecialchars(SITE_URL . '/index.php'); ?></code></td>
                </tr>
                <tr>
                    <td>Shop</td>
                    <td><code><?php echo htmlspecialchars(SITE_URL . '/shop.php'); ?></code></td>
                </tr>
            </table>
        </div>

        <!-- Section 4: Post-Verification Tasks -->
        <div class="section">
            <h2>✅ After Verification - Important Tasks</h2>

            <h3>Week 1: Monitor Indexing</h3>
            <div class="step">
                <p>In GSC, check:</p>
                <ul style="margin-left: 20px;">
                    <li>Coverage report - how many pages indexed</li>
                    <li>Mobile usability - any issues?</li>
                    <li>Search performance - which keywords appear</li>
                </ul>
            </div>

            <h3>Week 2: Fix Errors</h3>
            <div class="step">
                <p>Look for and fix:</p>
                <ul style="margin-left: 20px;">
                    <li>❌ Crawl errors (broken links)</li>
                    <li>❌ Mobile usability issues</li>
                    <li>❌ Duplicate content warnings</li>
                    <li>❌ Security issues</li>
                </ul>
            </div>

            <h3>Ongoing: Monitor Performance</h3>
            <div class="step">
                <p>Every week:</p>
                <ul style="margin-left: 20px;">
                    <li>📊 Check performance report</li>
                    <li>🔍 Track keyword rankings</li>
                    <li>📈 Monitor organic traffic</li>
                    <li>⚠️ Check for new errors</li>
                </ul>
            </div>
        </div>

        <!-- Section 5: Google My Business -->
        <div class="section">
            <h2>📍 Setup Google My Business (Local SEO)</h2>

            <div class="step">
                <span class="step-number">1</span>
                <h3>Go to Google My Business</h3>
                <p>Visit: <strong>https://mybusiness.google.com/</strong></p>
            </div>

            <div class="step">
                <span class="step-number">2</span>
                <h3>Verify Your Business</h3>
                <p><strong>Business Name:</strong> FSPO Ltd</p>
                <p><strong>Category:</strong> Hardware Store</p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars(SITE_ADDRESS); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars(SITE_PHONE); ?></p>
                <p><strong>Website:</strong> <?php echo htmlspecialchars($domainUrl); ?></p>
            </div>

            <div class="step">
                <span class="step-number">3</span>
                <h3>Complete Your Profile</h3>
                <ul style="margin-left: 20px;">
                    <li>✅ Add business photos</li>
                    <li>✅ Add business description</li>
                    <li>✅ Add service areas</li>
                    <li>✅ Set business hours</li>
                    <li>✅ Add products/services</li>
                </ul>
            </div>

            <div class="info">
                <strong>Benefit:</strong> Appear in Google Maps and local search results!
            </div>
        </div>

        <!-- Section 6: Monitoring Tools -->
        <div class="section">
            <h2>📊 Tools to Monitor Performance</h2>

            <table>
                <tr>
                    <th>Tool</th>
                    <th>Purpose</th>
                    <th>Cost</th>
                </tr>
                <tr>
                    <td><strong>Google Search Console</strong></td>
                    <td>Monitor indexing, keywords, traffic</td>
                    <td>FREE</td>
                </tr>
                <tr>
                    <td><strong>Google Analytics</strong></td>
                    <td>Track visitor behavior, conversions</td>
                    <td>FREE</td>
                </tr>
                <tr>
                    <td><strong>Google My Business</strong></td>
                    <td>Local search, reviews, map listing</td>
                    <td>FREE</td>
                </tr>
                <tr>
                    <td><strong>PageSpeed Insights</strong></td>
                    <td>Monitor page speed</td>
                    <td>FREE</td>
                </tr>
                <tr>
                    <td><strong>Mobile-Friendly Test</strong></td>
                    <td>Check mobile compatibility</td>
                    <td>FREE</td>
                </tr>
            </table>
        </div>

        <!-- Section 7: Troubleshooting -->
        <div class="section">
            <h2>🔧 Common Issues & Solutions</h2>

            <div class="step">
                <h3>❌ Verification Fails</h3>
                <p><strong>Solution:</strong></p>
                <ul style="margin-left: 20px;">
                    <li>Wait 48 hours for DNS to propagate</li>
                    <li>Check DNS record spelling</li>
                    <li>Try alternative verification method</li>
                    <li>Contact your domain provider for help</li>
                </ul>
            </div>

            <div class="step">
                <h3>❌ Sitemap Not Indexed</h3>
                <p><strong>Solution:</strong></p>
                <ul style="margin-left: 20px;">
                    <li>Check robots.txt allows sitemap</li>
                    <li>Verify sitemap URL is correct</li>
                    <li>Check for XML formatting errors</li>
                    <li>Wait 24-48 hours for Google to crawl</li>
                </ul>
            </div>

            <div class="step">
                <h3>❌ Pages Not Appearing in Search</h3>
                <p><strong>Solution:</strong></p>
                <ul style="margin-left: 20px;">
                    <li>Check Coverage report in GSC</li>
                    <li>Request indexing manually</li>
                    <li>Improve content quality</li>
                    <li>Get more backlinks</li>
                </ul>
            </div>
        </div>

        <!-- Section 8: Quick Reference -->
        <div class="section">
            <h2>📋 Quick Reference Checklist</h2>
            
            <h3>Initial Setup</h3>
            <ul class="checklist">
                <li>Create Google account (if needed)</li>
                <li>Go to Google Search Console</li>
                <li>Add domain property</li>
                <li>Verify domain ownership (DNS TXT)</li>
                <li>Submit sitemap</li>
            </ul>

            <h3>First Week</h3>
            <ul class="checklist">
                <li>Monitor coverage report</li>
                <li>Fix any crawl errors</li>
                <li>Check mobile usability</li>
                <li>Setup Google Analytics</li>
                <li>Create Google My Business</li>
            </ul>

            <h3>Ongoing Maintenance</h3>
            <ul class="checklist">
                <li>Review weekly in GSC</li>
                <li>Monitor keyword rankings</li>
                <li>Fix new errors immediately</li>
                <li>Update content regularly</li>
                <li>Request indexing of new pages</li>
            </ul>
        </div>

        <!-- Section 9: Expected Results -->
        <div class="section">
            <h2>📈 What to Expect</h2>

            <div class="success">
                <strong>Week 1-2:</strong> Google crawls and indexes your site
            </div>

            <div class="success">
                <strong>Week 3-4:</strong> Keywords start appearing in GSC
            </div>

            <div class="success">
                <strong>Month 2-3:</strong> Organic traffic begins to grow
            </div>

            <div class="success">
                <strong>Month 4-6:</strong> Significant rankings and traffic increase
            </div>

            <div class="warning">
                <strong>Note:</strong> Results vary based on content quality and competition. 
                Consistency is key!
            </div>
        </div>

        <!-- Section 10: Support -->
        <div class="section">
            <h2>❓ Need Help?</h2>

            <p><strong>Official Resources:</strong></p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Google Search Console Help: https://support.google.com/webmasters/</li>
                <li>Google My Business Help: https://support.google.com/business/</li>
                <li>Google SEO Guide: https://developers.google.com/search</li>
            </ul>

            <p style="margin-top: 20px;"><strong>Your Contact:</strong></p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Email: <?php echo htmlspecialchars(SITE_EMAIL); ?></li>
                <li>Phone: <?php echo htmlspecialchars(SITE_PHONE); ?></li>
                <li>Address: <?php echo htmlspecialchars(SITE_ADDRESS); ?></li>
            </ul>
        </div>

    </div>

    <footer>
        <p>Generated: <?php echo date('F j, Y @ g:i A'); ?> | FSPO Ltd SEO Setup Guide</p>
        <p style="margin-top: 10px;">Always use HTTPS when accessing this page in production!</p>
    </footer>

</div>

</body>
</html>
