<!-- FOOTER -->
<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <div class="logo-text" style="margin-bottom:14px">
                <span class="logo-name">FSPO <span>Ltd</span></span>
                <span class="logo-sub" style="display:block">Quality Hardware &amp; Supplies</span>
            </div>
            <p>FSPO Ltd is your trusted supplier of building materials, electricals, plumbing supplies, hardware and lighting — serving Kigali and all of Rwanda.</p>
            <div class="footer-contact">
                <div>📍 <?= SITE_ADDRESS ?></div>
                <div>📞 <a href="tel:+250785723677"><?= SITE_PHONE ?></a></div>
                <div>✉ <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a></div>
                <div>🌐 <a href="http://www.fspoltd.rw" target="_blank">www.fspoltd.rw</a></div>
                <div>⏰ <?= SITE_HOURS ?></div>
            </div>
        </div>

        <div class="footer-col">
            <h4>Categories</h4>
            <ul>
                <li><a href="<?= SITE_URL ?>/shop.php?cat=building-materials">Building Materials</a></li>
                <li><a href="<?= SITE_URL ?>/shop.php?cat=electricals">Electricals</a></li>
                <li><a href="<?= SITE_URL ?>/shop.php?cat=plumbing-supplies">Plumbing Supplies</a></li>
                <li><a href="<?= SITE_URL ?>/shop.php?cat=hand-tools">Hand Tools</a></li>
                <li><a href="<?= SITE_URL ?>/shop.php?cat=hardware">Hardware</a></li>
                <li><a href="<?= SITE_URL ?>/shop.php?cat=light">Light</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?= SITE_URL ?>/index.php">Home</a></li>
                <li><a href="<?= SITE_URL ?>/about.php">About Us</a></li>
                <li><a href="<?= SITE_URL ?>/shop.php">Shop</a></li>
                <li><a href="<?= SITE_URL ?>/contact.php">Contact Us</a></li>
                <li><a href="<?= SITE_URL ?>/cart.php">Cart</a></li>
                <li><a href="<?= SITE_URL ?>/<?= isAdmin() ? 'admin/dashboard.php' : 'client/dashboard.php' ?>">My Account</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Newsletter</h4>
            <p style="font-size:13px;color:var(--text-muted);margin-bottom:14px;line-height:1.6">Receive exclusive offers and promotions from FSPO Ltd.</p>
            <form class="newsletter-form" action="<?= SITE_URL ?>/newsletter.php" method="POST">
                <input type="email" name="email" placeholder="Your email address" required>
                <button type="submit">GO</button>
            </form>
            <div class="social-links" style="margin-top:20px">
                <h4>Follow Us</h4>
                <div class="social-icons">
                    <a class="social-icon" href="https://instagram.com/fspoltd" target="_blank">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        Instagram
                    </a>
                    <a class="social-icon" href="https://facebook.com/fspoltd" target="_blank">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        Facebook
                    </a>
                    <a class="social-icon" href="https://tiktok.com/@fspoltd" target="_blank">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.34 6.34 0 00-6.13 6.3 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.22 8.22 0 004.84 1.55V6.79a4.85 4.85 0 01-1.07-.1z"/></svg>
                        TikTok
                    </a>
                    <a class="social-icon" href="https://youtube.com/@fspoltd" target="_blank">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>
                        YouTube
                    </a>
                    <a class="social-icon" href="https://wa.me/250785723677" target="_blank">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <span>&copy; <?= date('Y') ?> FSPO Ltd. All Rights Reserved.</span>
        <span><a href="#">Privacy Policy</a> &middot; <a href="#">Terms of Use</a></span>
    </div>
</footer>

<script src="<?= SITE_URL ?>/js/main.js"></script>
</body>
</html>
