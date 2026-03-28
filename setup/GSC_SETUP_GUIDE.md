# Google Search Console & Indexing Setup Guide

## Quick Start (5 Minutes)

**If you want to get started immediately:**

1. Open: `http://localhost:8000/setup/google-search-console.php`
2. Follow the step-by-step guide
3. Submit your sitemap
4. Done! Google will start indexing

---

## 📋 Files Created

### 1. **setup/google-search-console.php** (Manual Setup Guide)
- Complete visual guide for manual Google Search Console setup
- Step-by-step domain verification instructions
- URL references and checklists
- Troubleshooting guide
- Best practices for post-verification

**Access:** `http://localhost:8000/setup/google-search-console.php`

### 2. **setup/submit-to-gsc-api.php** (Automated Submission)
- IndexNow API submission (easiest, no OAuth!)
- Google Search Console API setup (advanced)
- Form to submit multiple URLs
- Verification tools
- Automatic submission code snippets

**Access:** `http://localhost:8000/setup/submit-to-gsc-api.php`

### 3. **.well-known/submit.php** (IndexNow Handler)
- Receives JSON POST requests
- Submits URLs to IndexNow API
- Notifies Google/Bing/Yandex automatically
- Logs submissions to database
- Returns JSON response

### 4. **.well-known/check.php** (Verification & Diagnostics)
- Checks IndexNow key file exists
- Creates key file automatically
- Tests API connection
- Gets submission statistics
- Verifies domain ownership

---

## 🚀 Setup Methods (Choose One)

### Method 1: Manual Setup (Recommended First Time)
**Time:** 10 minutes | **Difficulty:** Easy | **Best For:** Initial verification

```
1. Go to: https://search.google.com/search-console/
2. Add property (use Domain option)
3. Verify with DNS TXT record
4. Submit sitemap manually
5. Monitor in GSC
```

**Guide:** Visit `setup/google-search-console.php` for detailed walkthrough

---

### Method 2: IndexNow API (Easiest - No OAuth!)
**Time:** 2 minutes | **Difficulty:** Very Easy | **Best For:** Real-time notifications

```bash
# Your unique IndexNow key:
<?php echo substr(hash('sha256', 'YOUR_SITE_URL' . $_SERVER['SERVER_NAME'] . date('Y')), 0, 32); ?>

# Create .well-known file:
/.well-known/IndexNow-[YOUR_KEY]

# Submit URLs via POST:
curl -X POST https://your-domain.com/.well-known/submit.php \
  -H "Content-Type: application/json" \
  -d '{
    "urls": [
      "https://your-domain.com/product.php?id=1",
      "https://your-domain.com/product.php?id=2"
    ]
  }'
```

**Advantages:**
- ✅ No authentication needed
- ✅ Works with Google, Bing, Yandex
- ✅ Instant notifications
- ✅ Can auto-submit new pages

---

### Method 3: Google Search Console API (Full Control)
**Time:** 30 minutes | **Difficulty:** Advanced | **Best For:** Enterprise automation

```
1. Create Google Cloud project
2. Enable Search Console API
3. Create OAuth 2.0 credentials
4. Download credentials JSON
5. Use with submit-to-gsc-api.php
```

**Documentation:** Google's official guide
https://developers.google.com/webmaster-tools/v1/

---

## 🔧 Implementation Steps

### Step 1: Create IndexNow Key File (5 minutes)

Visit: `http://localhost:8000/setup/submit-to-gsc-api.php`

Click "🔍 Check .well-known File" → Follow instructions to create it

**Manual Creation:**

```bash
# SSH into your server and run:
cd /your-site/.well-known/

# Create key file (replace KEY with your actual key)
echo "YOUR_GENERATED_KEY" > IndexNow-YOUR_GENERATED_KEY

# Set permissions
chmod 644 IndexNow-YOUR_GENERATED_KEY

# Verify
curl https://your-domain.com/.well-known/IndexNow-YOUR_GENERATED_KEY
# Should return the key
```

---

### Step 2: Verify Your Domain on Google

1. Go to: https://search.google.com/search-console/
2. Click "Add property"
3. Select "Domain"
4. Enter: `your-domain.com` (without https://)
5. Choose verification method:
   - **Recommended:** DNS TXT Record
   - Alternative: HTML file, Meta tag, Google Analytics

**DNS Method (Most Reliable):**
```
Type:  TXT
Name:  your-domain.com
Value: google-site-verification=XXXXXXXXXXXXX
```

Wait 24-48 hours for DNS to propagate, then verify in GSC.

---

### Step 3: Submit Sitemap

After verification:

1. Go to GSC → Sitemaps
2. Click "Add new sitemap"
3. Enter: `https://your-domain.com/sitemap.xml`
4. Submit!

Google will automatically:
- ✅ Fetch your sitemap
- ✅ Discover all your products
- ✅ Start indexing pages
- ✅ Monitor for errors

---

### Step 4: Auto-Submit New Pages (Optional)

When you add a new product, automatically notify Google:

```php
<?php
// After saving product to database
$productUrl = SITE_URL . '/product.php?id=' . $product_id;

// Submit to IndexNow
submitToIndexNow([$productUrl]);

function submitToIndexNow($urls) {
    $payload = [
        'host' => parse_url(SITE_URL, PHP_URL_HOST),
        'key' => 'YOUR_INDEXNOW_KEY',
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
    
    return $httpCode === 200; // Success!
}
?>
```

---

## 📊 Monitoring Your Progress

### In Google Search Console:

1. **Coverage Report**
   - Shows which pages are indexed
   - Identifies crawl errors
   - Suggests fixes

2. **Performance Report**
   - Search keywords driving traffic
   - Click-through rates (CTR)
   - Average position
   - Search impressions

3. **URL Inspection**
   - Check if specific page is indexed
   - Request re-indexing manually
   - See rendering issues

### Timeline:

```
Week 1:    Google crawls your site
Week 2:    Pages start appearing in index
Week 3-4:  Keywords appear in search results
Month 2-3: Organic traffic visible
Month 3-6: Significant ranking improvements
```

---

## 🔍 Verification Checklist

### Before Going Live:

- [ ] Domain verified in Google Search Console
- [ ] Sitemap submitted successfully
- [ ] robots.txt allows crawling
- [ ] No crawl errors in GSC
- [ ] Meta tags present (check page source)
- [ ] Meta descriptions unique for each page
- [ ] Images have alt text
- [ ] Internal links working
- [ ] Mobile version responsive
- [ ] Page load speed acceptable

### After Launch:

- [ ] Monitor GSC weekly
- [ ] Check for new errors
- [ ] Update content regularly
- [ ] Request indexing of new pages
- [ ] Track keyword rankings
- [ ] Get backlinks from other sites
- [ ] Engage on social media

---

## 🐛 Troubleshooting

### Problem: "Domain verification failed"

**Solutions:**
1. Wait 24-48 hours for DNS to propagate
2. Check DNS record is exact (copy-paste value)
3. Try alternative verification method (HTML file)
4. Contact domain provider for help

---

### Problem: "Sitemap not being indexed"

**Solutions:**
1. Check robots.txt: `Allow: /sitemap.xml`
2. Verify sitemap XML structure: `http://localhost:8000/sitemap.xml`
3. Check all URLs in sitemap are accessible
4. Wait 24-48 hours for Google to crawl

---

### Problem: "My pages aren't appearing in Google"

**Solutions:**
1. Use URL Inspection in GSC to debug
2. Request indexing manually
3. Improve content quality
4. Get backlinks from other sites
5. Wait longer (can take weeks for new sites)

---

### Problem: "IndexNow submission failed"

**Solutions:**
1. Check .well-known file exists and is accessible
2. Verify cURL is enabled: `php -m | grep curl`
3. Check HTTPS/SSL working
4. Verify key matches exactly
5. Check firewall allows outbound HTTPS

---

## 📈 Expected Results

### After 1 Month:
- ✅ 80%+ of pages indexed
- ✅ Keywords appearing in search results
- ✅ 10-50 organic visits (varies by competition)

### After 3 Months:
- ✅ Nearly all pages indexed
- ✅ Multiple keywords ranking
- ✅ 100-500 organic visits

### After 6 Months:
- ✅ Stable rankings
- ✅ 500-5000+ organic visits
- ✅ Organic traffic becoming significant revenue

---

## 🎯 Quick Links

| Resource | URL |
|----------|-----|
| Manual Setup Guide | `http://localhost:8000/setup/google-search-console.php` |
| API Submission Tool | `http://localhost:8000/setup/submit-to-gsc-api.php` |
| Your Sitemap | `http://localhost:8000/sitemap.xml` |
| Robots.txt | `http://localhost:8000/robots.txt` |
| Google Search Console | https://search.google.com/search-console/ |
| Google My Business | https://mybusiness.google.com/ |
| Google Analytics | https://analytics.google.com/ |
| Google PageSpeed Insights | https://pagespeed.web.dev/ |
| Mobile-Friendly Test | https://search.google.com/test/mobile-friendly |

---

## 💡 Pro Tips

1. **Update Content Regularly**
   - New content ranks better than stale
   - Update product descriptions every 2-3 months
   - Add new categories/products frequently

2. **Improve Page Speed**
   - Faster pages rank higher
   - Compress images
   - Use caching
   - Minimize JavaScript

3. **Get Quality Backlinks**
   - Link from other websites to yours
   - More links = higher rankings
   - Write content worth linking to

4. **Use Long-Tail Keywords**
   - "Best hardware store in Rwanda" not just "hardware"
   - Less competition
   - Higher conversion rates

5. **Engage on Social Media**
   - Share products on social
   - More traffic to site
   - Better engagement signals

6. **Monitor Competitors**
   - See what keywords they rank for
   - What content they publish
   - Find gaps to fill

---

## 📞 Support Resources

**For GSC Issues:**
- Email: <?php echo SITE_EMAIL; ?>
- Phone: <?php echo SITE_PHONE; ?>
- Address: <?php echo SITE_ADDRESS; ?>

**For Google Help:**
- GSC Help Center: https://support.google.com/webmasters/
- Search Console Twitter: https://twitter.com/googlesearchc/
- Stack Overflow: https://stackoverflow.com/questions/tagged/google-search-console

---

## 🎓 Further Learning

**Recommended Reading:**
1. Google's SEO Starter Guide: https://developers.google.com/search
2. Search Console Help: https://support.google.com/webmasters/
3. SEO Best Practices: https://moz.com/beginners-guide-to-seo
4. Technical SEO: https://backlinko.com/technical-seo

**Video Tutorials:**
- Google Search Central: https://www.youtube.com/c/GoogleSearchCentral
- SEO 101: https://developers.google.com/search

---

**Generated:** <?php echo date('F j, Y @ g:i A'); ?>  
**For:** FSPO Ltd  
**System:** Google Search Console & IndexNow Integration
