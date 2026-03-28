<?php
require_once __DIR__ . '/config.php';

// ─── Security Headers ───────────────────────────────────────
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

$cartCount = getCartCount();
$flash = getFlash();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- ─── SEO Meta Tags ─────────────────────────────────────────── -->
<title><?= isset($pageTitle) ? sanitize($pageTitle) . ' – ' . SITE_NAME : SITE_NAME . ' – Quality Hardware & Supplies in Rwanda' ?></title>
<meta name="description" content="<?= isset($pageDescription) ? sanitize($pageDescription) : 'FSPO Ltd – Your trusted hardware store in Rwanda. Quality building materials, electricals, plumbing supplies, and tools delivered across Kigali.' ?>">
<meta name="keywords" content="<?= isset($pageKeywords) ? sanitize($pageKeywords) : 'hardware store Rwanda, building materials, electricals, plumbing supplies, tools, Kigali' ?>">
<meta name="author" content="FSPO Ltd">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<link rel="canonical" href="<?= SITE_URL . $_SERVER['REQUEST_URI'] ?>">

<!-- ─── Open Graph Tags (Social Media Sharing) ────────────────── -->
<meta property="og:locale" content="en_US">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= isset($pageTitle) ? sanitize($pageTitle) . ' – ' . SITE_NAME : SITE_NAME ?>">
<meta property="og:description" content="<?= isset($pageDescription) ? sanitize($pageDescription) : 'Quality hardware and supplies for Rwanda' ?>">
<meta property="og:url" content="<?= SITE_URL . $_SERVER['REQUEST_URI'] ?>">
<meta property="og:site_name" content="<?= SITE_NAME ?>">
<meta property="og:image" content="<?= SITE_URL ?>/assets/og-image.jpg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<!-- ─── Twitter Card Tags ────────────────────────────────────── -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= isset($pageTitle) ? sanitize($pageTitle) . ' – ' . SITE_NAME : SITE_NAME ?>">
<meta name="twitter:description" content="<?= isset($pageDescription) ? sanitize($pageDescription) : 'Quality hardware and supplies for Rwanda' ?>">
<meta name="twitter:image" content="<?= SITE_URL ?>/assets/og-image.jpg">

<!-- ─── Additional SEO Tags ──────────────────────────────────── -->
<meta name="language" content="English">
<meta name="revisit-after" content="7 days">
<meta name="distribution" content="global">
<meta property="business:contact_data:street_address" content="<?= SITE_ADDRESS ?>">
<meta property="business:contact_data:locality" content="Kigali">
<meta property="business:contact_data:region" content="Rwanda">
<meta property="business:contact_data:postal_code" content="RW">
<meta property="business:contact_data:country_name" content="Rwanda">
<meta property="business:contact_data:phone_number" content="<?= SITE_PHONE ?>">
<meta property="business:contact_data:email" content="<?= SITE_EMAIL ?>">

<!-- ─── Structured Data (JSON-LD) ────────────────────────────── -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "<?= SITE_NAME ?>",
  "image": "<?= SITE_URL ?>/assets/logo.jpg",
  "description": "Quality hardware store in Rwanda with building materials, electricals, plumbing supplies, and tools",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "<?= SITE_ADDRESS ?>",
    "addressLocality": "Kigali",
    "addressRegion": "Rwanda",
    "postalCode": "RW",
    "addressCountry": "RW"
  },
  "telephone": "<?= SITE_PHONE ?>",
  "email": "<?= SITE_EMAIL ?>",
  "url": "<?= SITE_URL ?>",
  "openingHoursSpecification": {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
    "opens": "07:00",
    "closes": "20:00"
  },
  "priceRange": "RWF"
}
</script>

<!-- ─── Stylesheet & Fonts ──────────────────────────────────── -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
</head>
<body>

<!-- FLASH MESSAGE -->
<?php if ($flash): ?>
<div class="flash flash-<?= $flash['type'] ?>" id="flashMsg">
    <?= sanitize($flash['msg']) ?>
    <button onclick="this.parentElement.remove()">×</button>
</div>
<?php endif; ?>

<!-- TOP BAR -->
<div class="topbar">
    <span>📍 <?= SITE_ADDRESS ?> &nbsp;|&nbsp; <?= SITE_HOURS ?></span>
    <div class="topbar-right">
        <a href="mailto:<?= SITE_EMAIL ?>">✉ <?= SITE_EMAIL ?></a>
        <a href="tel:<?= str_replace(' ','',$_SERVER['HTTP_HOST'] ?? '') ?><?= SITE_PHONE ?>">📞 <?= SITE_PHONE ?></a>
        <?php if (isLoggedIn()): ?>
            <a href="<?= SITE_URL ?>/<?= isAdmin() ? 'admin/dashboard.php' : 'client/dashboard.php' ?>">👤 <?= sanitize($_SESSION['user_name']) ?></a>
            <a href="<?= SITE_URL ?>/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?= SITE_URL ?>/login.php">Login</a>
            <a href="<?= SITE_URL ?>/register.php">Register</a>
        <?php endif; ?>
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar">
    <a class="logo" href="<?= SITE_URL ?>/index.php">
        <div class="logo-text">
            <span class="logo-name">FSPO <span>Ltd</span></span>
            <span class="logo-sub">Quality Hardware &amp; Supplies</span>
        </div>
    </a>

    <ul class="nav-links">
        <li><a href="<?= SITE_URL ?>/index.php" class="<?= $currentPage==='index'?'active':'' ?>">Home</a></li>
        <li><a href="<?= SITE_URL ?>/shop.php" class="<?= $currentPage==='shop'?'active':'' ?>">Shop</a></li>
        <li><a href="<?= SITE_URL ?>/about.php" class="<?= $currentPage==='about'?'active':'' ?>">About Us</a></li>
        <li><a href="<?= SITE_URL ?>/contact.php" class="<?= $currentPage==='contact'?'active':'' ?>">Contact</a></li>
    </ul>

    <div class="nav-actions">
        <form action="<?= SITE_URL ?>/shop.php" method="GET" class="nav-search">
            <input type="text" name="q" placeholder="Search products…" value="<?= isset($_GET['q']) ? sanitize($_GET['q']) : '' ?>">
            <button type="submit">🔍</button>
        </form>
        <a href="<?= SITE_URL ?>/wishlist.php" class="nav-icon" title="Wishlist">🤍</a>
        <a href="<?= SITE_URL ?>/cart.php" class="nav-icon cart-link" title="Cart">
            🛒 <?php if ($cartCount > 0): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
        </a>
        <button class="nav-hamburger" onclick="document.querySelector('.nav-links').classList.toggle('open')">☰</button>
    </div>
</nav>
