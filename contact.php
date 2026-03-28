<?php
require_once 'includes/config.php';
$pageTitle = 'Contact Us';
$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) $errors[] = 'Name, email and message are required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';

    if (empty($errors)) {
        $db = getDB();
        $db->prepare("INSERT INTO contact_messages (name,email,phone,subject,message) VALUES (?,?,?,?,?)")
           ->execute([$name,$email,$phone,$subject,$message]);
        $success = true;
    }
}

include 'includes/header.php';
?>
<div class="page-hero">
    <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> Contact</div>
    <h1>Contact Us</h1>
    <p>We'd love to hear from you</p>
</div>

<div class="contact-grid">
    <div class="contact-info">
        <h2>Get in Touch</h2>
        <p style="color:var(--text-muted);margin-bottom:30px;line-height:1.7">Have a question about our products, delivery or pricing? Our team is happy to help.</p>

        <div class="contact-info-item">
            <div class="icon">📍</div>
            <div><h4>Address</h4><p><?= SITE_ADDRESS ?></p></div>
        </div>
        <div class="contact-info-item">
            <div class="icon">📞</div>
            <div><h4>Phone</h4><p><a href="tel:+250785723677" style="color:var(--gold-light)"><?= SITE_PHONE ?></a></p></div>
        </div>
        <div class="contact-info-item">
            <div class="icon">✉</div>
            <div><h4>Email</h4><p><a href="mailto:<?= SITE_EMAIL ?>" style="color:var(--gold-light)"><?= SITE_EMAIL ?></a></p></div>
        </div>
        <div class="contact-info-item">
            <div class="icon">⏰</div>
            <div><h4>Business Hours</h4><p><?= SITE_HOURS ?></p></div>
        </div>
        <div class="contact-info-item">
            <div class="icon">💬</div>
            <div><h4>WhatsApp</h4><p><a href="https://wa.me/250785723677" target="_blank" style="color:var(--gold-light)">Chat with us on WhatsApp</a></p></div>
        </div>
    </div>

    <div class="contact-form-card">
        <h2>Send a Message</h2>

        <?php if ($success): ?>
        <div class="alert alert-success">✓ Thank you! Your message has been sent. We'll respond within 24 hours.</div>
        <?php endif; ?>
        <?php if ($errors): ?>
        <div class="alert alert-danger"><?= implode('<br>', array_map('sanitize', $errors)) ?></div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Your Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="Jean Claude" value="<?= sanitize($_POST['name']??'') ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" class="form-control" placeholder="+250 7XX XXX XXX" value="<?= sanitize($_POST['phone']??'') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" class="form-control" placeholder="you@email.com" value="<?= sanitize($_POST['email']??'') ?>" required>
            </div>
            <div class="form-group">
                <label>Subject</label>
                <input type="text" name="subject" class="form-control" placeholder="e.g. Product availability" value="<?= sanitize($_POST['subject']??'') ?>">
            </div>
            <div class="form-group">
                <label>Message *</label>
                <textarea name="message" class="form-control" rows="5" placeholder="Type your message..." required><?= sanitize($_POST['message']??'') ?></textarea>
            </div>
            <button type="submit" class="btn btn-gold btn-block">Send Message →</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
