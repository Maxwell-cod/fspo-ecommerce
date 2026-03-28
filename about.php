<?php
require_once 'includes/config.php';
$pageTitle = 'About Us';
include 'includes/header.php';
?>
<div class="page-hero">
    <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> About Us</div>
    <h1>About FSPO Ltd</h1>
    <p>Rwanda's trusted hardware and construction supply store</p>
</div>

<div class="about-grid">
    <div class="about-img">
        <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=700&h=500&fit=crop" alt="FSPO Ltd Store">
    </div>
    <div class="about-text">
        <h2>Who We Are</h2>
        <p>FSPO Ltd is a leading supplier of building materials, electricals, plumbing supplies, hardware and lighting solutions based in Kigali, Rwanda. Since our founding, we have been committed to providing high-quality products at competitive prices to individuals, contractors, and businesses across Rwanda.</p>
        <p>Our team of experienced professionals is dedicated to helping our customers find exactly what they need — from a single light bulb to full construction supply orders. We stock thousands of products from trusted international and local brands.</p>
        <p>We serve our customers both in-store at our Kigali-Gakinjiro location and online through our e-commerce platform, with reliable delivery across Rwanda.</p>
        <div class="about-stats">
            <div class="about-stat"><h3>500+</h3><p>Products</p></div>
            <div class="about-stat"><h3>5+</h3><p>Years in Business</p></div>
            <div class="about-stat"><h3>1000+</h3><p>Happy Customers</p></div>
        </div>
    </div>
</div>

<section class="section section-dark">
    <div class="section-header"><h2>Our Values</h2></div>
    <div class="why-cards" style="max-width:1000px">
        <div class="why-card"><div class="why-icon">🏆</div><h3>Quality First</h3><p>We source only verified, quality products from reputable brands to ensure your projects succeed.</p></div>
        <div class="why-card"><div class="why-icon">🤝</div><h3>Customer Service</h3><p>Our friendly team is here to help you choose the right products. Visit us or call anytime during business hours.</p></div>
        <div class="why-card"><div class="why-icon">💰</div><h3>Best Prices</h3><p>We offer competitive pricing on all our products. Buy in bulk and save even more on your construction supplies.</p></div>
        <div class="why-card"><div class="why-icon">🚚</div><h3>Fast Delivery</h3><p>We deliver across Kigali and Rwanda quickly and reliably, ensuring your projects are never delayed.</p></div>
        <div class="why-card"><div class="why-icon">🔒</div><h3>Secure Shopping</h3><p>Shop confidently online with secure MTN MoMo, Airtel Money and bank payment options.</p></div>
        <div class="why-card"><div class="why-icon">🇷🇼</div><h3>Proudly Rwandan</h3><p>We are a Rwandan company, committed to supporting local development and infrastructure growth.</p></div>
    </div>
</section>

<section class="section">
    <div class="section-header"><h2>Visit Our Store</h2></div>
    <div style="max-width:800px;margin:0 auto;text-align:center">
        <div style="background:var(--dark2);border:1px solid var(--border);border-radius:14px;padding:40px">
            <div style="font-size:40px;margin-bottom:16px">📍</div>
            <h3 style="font-size:20px;margin-bottom:16px;color:var(--gold)">FSPO Ltd Store</h3>
            <p style="color:var(--text-muted);font-size:15px;line-height:1.8">
                Kigali-Gakinjiro, Rwanda<br>
                📞 <?= SITE_PHONE ?><br>
                ✉ <?= SITE_EMAIL ?><br>
                ⏰ <?= SITE_HOURS ?>
            </p>
            <div style="margin-top:24px;display:flex;gap:12px;justify-content:center">
                <a href="contact.php" class="btn btn-gold">Contact Us</a>
                <a href="https://wa.me/250785723677" target="_blank" class="btn btn-outline">WhatsApp Us</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
