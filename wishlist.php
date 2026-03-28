<?php
require_once 'includes/config.php';
requireLogin();
$pageTitle = 'My Wishlist';
$db = getDB();
$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $pid    = (int)($_POST['product_id'] ?? 0);
    $redirect = $_POST['redirect'] ?? SITE_URL . '/wishlist.php';

    if ($action === 'toggle' && $pid) {
        $check = $db->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
        $check->execute([$uid,$pid]);
        if ($check->fetch()) {
            $db->prepare("DELETE FROM wishlist WHERE user_id=? AND product_id=?")->execute([$uid,$pid]);
            setFlash('info','Removed from wishlist.');
        } else {
            $db->prepare("INSERT IGNORE INTO wishlist (user_id,product_id) VALUES(?,?)")->execute([$uid,$pid]);
            setFlash('success','Added to wishlist!');
        }
    }
    redirect($redirect);
}

$items = $db->prepare("SELECT w.id as wish_id, p.id as product_id, p.name, p.price, p.image, p.stock, c.name as cat_name FROM wishlist w JOIN products p ON w.product_id=p.id LEFT JOIN categories c ON p.category_id=c.id WHERE w.user_id=? ORDER BY w.created_at DESC");
$items->execute([$uid]);
$items = $items->fetchAll();

include 'includes/header.php';
?>
<div class="page-hero">
    <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> Wishlist</div>
    <h1>🤍 My Wishlist</h1>
    <p><?= count($items) ?> saved item<?= count($items)!==1?'s':'' ?></p>
</div>
<div class="section">
<?php if (empty($items)): ?>
<div class="empty-state">
    <div class="icon">🤍</div>
    <h3>Your wishlist is empty</h3>
    <p>Save products you love to your wishlist.</p>
    <a href="shop.php" class="btn btn-gold">Browse Products</a>
</div>
<?php else: ?>
<div class="products-grid">
    <?php foreach ($items as $p): ?>
    <div class="product-card">
        <a href="product.php?id=<?= $p['product_id'] ?>">
            <div class="card-img"><img src="<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400&h=300&fit=crop'"></div>
        </a>
        <div class="card-body">
            <div class="card-category"><?= sanitize($p['cat_name']??'') ?></div>
            <a href="product.php?id=<?= $p['product_id'] ?>"><div class="card-title"><?= sanitize($p['name']) ?></div></a>
            <div class="card-price"><?= formatRwf($p['price']) ?></div>
            <div class="card-actions">
                <?php if ($p['stock']>0): ?>
                <form method="POST" action="cart.php" style="flex:1">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                    <button type="submit" class="btn btn-gold btn-sm" style="width:100%">Add to Cart</button>
                </form>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                    <button type="submit" class="wishlist-btn active" title="Remove">❤</button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
