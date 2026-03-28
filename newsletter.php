<?php
// newsletter.php
require_once 'includes/config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // In production, save to a newsletter table or send to Mailchimp
        setFlash('success', '✓ Subscribed! Thank you for joining our newsletter.');
    } else {
        setFlash('error', 'Invalid email address.');
    }
}
redirect(SITE_URL . '/index.php');
