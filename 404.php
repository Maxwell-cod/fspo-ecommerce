<?php
require_once 'includes/config.php';
http_response_code(404);
$pageTitle = '404 – Page Not Found';
include 'includes/header.php';
?>
<div class="section" style="text-align:center;padding:100px 40px">
    <div style="font-size:80px;margin-bottom:20px">🔍</div>
    <h1 style="font-family:'Playfair Display',serif;font-size:48px;color:var(--gold);margin-bottom:14px">404</h1>
    <h2 style="font-size:22px;margin-bottom:14px">Page Not Found</h2>
    <p style="color:var(--text-muted);font-size:16px;max-width:400px;margin:0 auto 32px;line-height:1.7">
        Sorry, the page you're looking for doesn't exist or has been moved.
    </p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap">
        <a href="<?= SITE_URL ?>/index.php" class="btn btn-gold">🏠 Go Home</a>
        <a href="<?= SITE_URL ?>/shop.php"  class="btn btn-outline">🛍 Browse Shop</a>
        <a href="<?= SITE_URL ?>/contact.php" class="btn btn-outline">✉ Contact Us</a>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
