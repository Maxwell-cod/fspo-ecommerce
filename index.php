<?php
require_once 'includes/config.php';
$pageTitle = 'Home';
$db = getDB();

// Featured products
$featured = $db->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.featured = 1 AND p.status = 'active' ORDER BY p.created_at DESC LIMIT 8")->fetchAll();

// All categories with product counts
$categories = $db->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id AND p.status='active' GROUP BY c.id ORDER BY c.name")->fetchAll();

// Top deals (newest products)
$deals = $db->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 'active' ORDER BY p.created_at DESC LIMIT 4")->fetchAll();

include 'includes/header.php';
?>

<!-- HERO -->
<section class="hero">
    <div class="hero-bg"></div>
    <img class="hero-img" src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=900&h=600&fit=crop" alt="FSPO Ltd" onerror="this.style.display='none'">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge">🇷🇼 Trusted Hardware Store in Rwanda</div>
        <h1>Discover the Best<br>Products in <em>Rwanda</em></h1>
        <p class="hero-sub">Shop top-quality building materials, electricals, plumbing, hardware and lighting — delivered across Kigali and Rwanda.</p>
        <div class="hero-btns">
            <a href="shop.php" class="btn btn-primary">SHOP NOW</a>
            <a href="about.php" class="btn btn-outline">ABOUT US</a>
        </div>
    </div>
</section>

<!-- CATEGORY BAR -->
<div class="category-bar">
    <a href="shop.php">All Products</a><span>·</span>
    <?php foreach ($categories as $cat): ?>
    <a href="shop.php?cat=<?= $cat['slug'] ?>"><?= sanitize($cat['name']) ?></a><?php if (!$loop_last = end($categories) == $cat) echo '<span>·</span>'; ?>
    <?php endforeach; ?>
</div>

<!-- TRENDING CATEGORIES -->
<section class="section section-dark">
    <div class="section-header">
        <h2>Shop by Category</h2>
        <p>Browse our full range of quality products</p>
    </div>
    <div class="cat-grid">
        <?php foreach ($categories as $cat): ?>
        <a href="shop.php?cat=<?= $cat['slug'] ?>" class="cat-card">
            <img src="<?= sanitize($cat['image']) ?>" alt="<?= sanitize($cat['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400&h=200&fit=crop'">
            <div class="cat-card-body">
                <h3><?= sanitize($cat['name']) ?></h3>
                <p><?= $cat['product_count'] ?> products</p>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="section">
    <div class="section-header">
        <h2>Featured Products</h2>
        <p>Hand-picked top quality items</p>
    </div>
    <div class="products-grid">
        <?php foreach ($featured as $p): ?>
        <div class="product-card">
            <a href="product.php?id=<?= $p['id'] ?>">
                <div class="card-img">
                    <img src="<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400&h=300&fit=crop'">
                </div>
            </a>
            <div class="card-body">
                <div class="card-category"><?= sanitize($p['cat_name'] ?? '') ?></div>
                <a href="product.php?id=<?= $p['id'] ?>"><div class="card-title"><?= sanitize($p['name']) ?></div></a>
                <div class="card-price"><?= formatRwf($p['price']) ?></div>
                <div class="card-stock <?= $p['stock'] < 5 ? ($p['stock'] == 0 ? 'out' : 'low') : '' ?>">
                    <?= $p['stock'] == 0 ? 'Out of stock' : ($p['stock'] < 5 ? 'Only ' . $p['stock'] . ' left' : 'In stock') ?>
                </div>
                <div class="card-actions">
                    <?php if ($p['stock'] > 0): ?>
                    <form method="POST" action="cart.php" style="flex:1">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-gold btn-sm" style="width:100%">Add to Cart</button>
                    </form>
                    <?php else: ?>
                    <button class="btn btn-outline btn-sm" disabled style="flex:1;opacity:.5">Out of Stock</button>
                    <?php endif; ?>
                    <form method="POST" action="wishlist.php">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="wishlist-btn" title="Wishlist">🤍</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:36px">
        <a href="shop.php" class="btn btn-outline">View All Products →</a>
    </div>
</section>

<!-- TOP DEALS -->
<section class="section section-dark">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px">
        <h2 style="font-family:'Playfair Display',serif;font-size:26px;font-weight:700">Top Deals</h2>
        <a href="shop.php" class="btn btn-outline btn-sm">View All →</a>
    </div>
    <div class="products-grid">
        <?php foreach ($deals as $p): ?>
        <div class="product-card">
            <a href="product.php?id=<?= $p['id'] ?>">
                <div class="card-img">
                    <img src="<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400&h=300&fit=crop'">
                </div>
            </a>
            <div class="card-body">
                <div class="card-category"><?= sanitize($p['cat_name'] ?? '') ?></div>
                <a href="product.php?id=<?= $p['id'] ?>"><div class="card-title"><?= sanitize($p['name']) ?></div></a>
                <div class="card-price"><?= formatRwf($p['price']) ?></div>
                <div class="card-actions">
                    <?php if ($p['stock'] > 0): ?>
                    <form method="POST" action="cart.php" style="flex:1">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-gold btn-sm" style="width:100%">Add to Cart</button>
                    </form>
                    <?php endif; ?>
                    <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">View</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- WHY SHOP -->
<section class="why-shop">
    <div class="why-bg"></div>
    <h2>WHY SHOP With Us?</h2>
    <div class="why-cards">
        <div class="why-card">
            <div class="why-icon">🚚</div>
            <h3>Fast Delivery</h3>
            <p>We deliver your orders quickly across Kigali and all of Rwanda. Reliable logistics, right to your door.</p>
        </div>
        <div class="why-card">
            <div class="why-icon">🔒</div>
            <h3>Secure Payments</h3>
            <p>Pay via MTN Mobile Money, Airtel Money or any major Rwandan bank. Your transactions are fully protected.</p>
        </div>
        <div class="why-card">
            <div class="why-icon">✅</div>
            <h3>Quality Assurance</h3>
            <p>Every product is carefully sourced and vetted for quality. We stand behind everything we sell.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
