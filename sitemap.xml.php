<?php
/**
 * sitemap.xml - Dynamic XML Sitemap for FSPO Ltd
 * Automatically generates sitemap for all products and pages
 * Location: /sitemap.xml
 */

require_once 'includes/config.php';

header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=86400'); // Cache for 24 hours

$db = getDB();

// Get all active products
$products = $db->query("
    SELECT id, slug, updated_at FROM products 
    WHERE status = 'active' 
    ORDER BY updated_at DESC
")->fetchAll();

// Get all active categories
$categories = $db->query("
    SELECT slug, id FROM categories 
    ORDER BY name
")->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
echo '         xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"' . "\n";
echo '         xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0">' . "\n\n";

// Home page
echo "  <url>\n";
echo "    <loc>" . htmlspecialchars(SITE_URL . '/index.php') . "</loc>\n";
echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
echo "    <changefreq>daily</changefreq>\n";
echo "    <priority>1.0</priority>\n";
echo "  </url>\n\n";

// Static pages
$staticPages = [
    ['url' => '/shop.php', 'priority' => '0.9', 'changefreq' => 'daily'],
    ['url' => '/about.php', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['url' => '/contact.php', 'priority' => '0.7', 'changefreq' => 'monthly'],
];

foreach ($staticPages as $page) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars(SITE_URL . $page['url']) . "</loc>\n";
    echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
    echo "    <changefreq>" . $page['changefreq'] . "</changefreq>\n";
    echo "    <priority>" . $page['priority'] . "</priority>\n";
    echo "  </url>\n\n";
}

// Category pages
foreach ($categories as $cat) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars(SITE_URL . '/shop.php?cat=' . $cat['slug']) . "</loc>\n";
    echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.8</priority>\n";
    echo "  </url>\n\n";
}

// Product pages (limit to 50,000 for performance)
$count = 0;
foreach ($products as $product) {
    if ($count >= 50000) break; // XML Sitemap max URLs
    
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars(SITE_URL . '/product.php?id=' . $product['id']) . "</loc>\n";
    echo "    <lastmod>" . date('Y-m-d', strtotime($product['updated_at'])) . "</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.7</priority>\n";
    echo "  </url>\n\n";
    
    $count++;
}

echo "</urlset>\n";
?>
