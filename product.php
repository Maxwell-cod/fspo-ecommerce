<?php
require_once 'includes/config.php';
$db = getDB();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { redirect(SITE_URL . '/shop.php'); }

$stmt = $db->prepare("SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.status='active'");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { redirect(SITE_URL . '/shop.php'); }

// Handle add to cart from this page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    requireLogin();
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    $stmt2 = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?,?,?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
    $stmt2->execute([$_SESSION['user_id'], $id, $qty, $qty]);
    setFlash('success', '✓ ' . $product['name'] . ' added to cart!');
    redirect(SITE_URL . '/product.php?id=' . $id);
}

// Related products
$related = $db->prepare("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? AND p.status='active' LIMIT 4");
$related->execute([$product['category_id'], $id]);
$related = $related->fetchAll();

$pageTitle = $product['name'];
include 'includes/header.php';
?>

<div class="page-hero">
    <div class="breadcrumb">
        <a href="index.php">Home</a> <span>/</span>
        <a href="shop.php">Shop</a> <span>/</span>
        <a href="shop.php?cat=<?= $product['cat_slug'] ?>"><?= sanitize($product['cat_name']) ?></a> <span>/</span>
        <?= sanitize($product['name']) ?>
    </div>
</div>

<div class="product-detail">
    <!-- IMAGE -->
    <div class="product-detail-img">
        <img src="<?= sanitize($product['image']) ?>" alt="<?= sanitize($product['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=700&h=500&fit=crop'">
    </div>

    <!-- INFO -->
    <div class="product-detail-info">
        <div class="card-category"><?= sanitize($product['cat_name']) ?></div>
        <h1><?= sanitize($product['name']) ?></h1>
        <div class="price"><?= formatRwf($product['price']) ?></div>
        <p class="description"><?= nl2br(sanitize($product['description'])) ?></p>

        <?php if ($product['stock'] > 0): ?>
        <form method="POST">
            <div class="qty-control">
                <button type="button" onclick="changeQty(this,-1)">−</button>
                <input type="number" name="qty" value="1" min="1" max="<?= $product['stock'] ?>">
                <button type="button" onclick="changeQty(this,1)">+</button>
                <span style="color:var(--text-muted);font-size:13px">(<?= $product['stock'] ?> available)</span>
            </div>
            <div class="detail-actions">
                <button type="submit" name="add_to_cart" class="btn btn-gold">🛒 Add to Cart</button>
                <a href="checkout.php?buy_now=<?= $id ?>" class="btn btn-primary">Buy Now</a>
            </div>
        </form>
        <?php else: ?>
        <div class="alert alert-danger">This product is currently out of stock.</div>
        <?php endif; ?>

        <form method="POST" action="wishlist.php" style="margin-bottom:24px">
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="product_id" value="<?= $id ?>">
            <input type="hidden" name="redirect" value="product.php?id=<?= $id ?>">
            <button type="submit" class="btn btn-outline btn-sm">🤍 Add to Wishlist</button>
        </form>

        <div class="product-meta">
            <div><span>Category</span><span><?= sanitize($product['cat_name']) ?></span></div>
            <div><span>Stock</span><span style="color:<?= $product['stock'] > 0 ? 'var(--success)' : 'var(--danger)' ?>"><?= $product['stock'] > 0 ? $product['stock'] . ' units' : 'Out of stock' ?></span></div>
            <div><span>Price</span><span><?= formatRwf($product['price']) ?></span></div>
            <div><span>Payment</span><span>MTN MoMo, Airtel, Bank</span></div>
        </div>
    </div>
</div>

<!-- RELATED PRODUCTS -->
<?php if ($related): ?>
<section class="section section-dark">
    <div class="section-header"><h2>Related Products</h2></div>
    <div class="products-grid">
        <?php foreach ($related as $p): ?>
        <div class="product-card">
            <a href="product.php?id=<?= $p['id'] ?>">
                <div class="card-img"><img src="<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400&h=300&fit=crop'"></div>
            </a>
            <div class="card-body">
                <div class="card-category"><?= sanitize($p['cat_name']) ?></div>
                <a href="product.php?id=<?= $p['id'] ?>"><div class="card-title"><?= sanitize($p['name']) ?></div></a>
                <div class="card-price"><?= formatRwf($p['price']) ?></div>
                <div class="card-actions">
                    <form method="POST" action="cart.php" style="flex:1">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-gold btn-sm" style="width:100%">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
