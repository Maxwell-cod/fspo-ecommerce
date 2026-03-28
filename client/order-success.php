<?php
require_once '../includes/config.php';
requireLogin();
$orderNum = sanitize($_GET['order'] ?? '');
$pageTitle = 'Order Placed';
include '../includes/header.php';
?>
<div class="section" style="text-align:center;padding:80px 40px">
    <div style="font-size:70px;margin-bottom:20px">🎉</div>
    <h1 style="font-family:'Playfair Display',serif;font-size:34px;color:var(--gold);margin-bottom:14px">Order Placed Successfully!</h1>
    <p style="font-size:16px;color:var(--text-muted);max-width:500px;margin:0 auto 30px;line-height:1.7">
        Thank you for your order! Your order number is <strong style="color:var(--white)"><?= $orderNum ?></strong>.
        We will contact you to confirm payment and arrange delivery.
    </p>
    <div style="background:var(--dark2);border:1px solid var(--border);border-radius:14px;padding:28px;max-width:420px;margin:0 auto 32px;text-align:left">
        <h3 style="font-size:15px;color:var(--gold);margin-bottom:16px">What happens next?</h3>
        <div style="display:flex;flex-direction:column;gap:12px;font-size:14px;color:rgba(255,255,255,0.75)">
            <div>1️⃣ &nbsp;We confirm receipt of your order</div>
            <div>2️⃣ &nbsp;We verify your payment (MoMo / Airtel / Bank)</div>
            <div>3️⃣ &nbsp;We prepare and package your items</div>
            <div>4️⃣ &nbsp;We deliver to your address in Kigali / Rwanda</div>
        </div>
    </div>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap">
        <a href="../client/orders.php" class="btn btn-gold">View My Orders</a>
        <a href="../shop.php" class="btn btn-outline">Continue Shopping</a>
        <a href="https://wa.me/250785723677" target="_blank" class="btn btn-primary">Contact Us on WhatsApp</a>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
