<?php
require_once 'includes/config.php';
requireLogin();
$pageTitle = 'My Cart';
$db = getDB();
$uid = $_SESSION['user_id'];

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $pid = (int)$_POST['product_id'];
        $qty = max(1, (int)($_POST['qty'] ?? 1));
        // Check stock
        $p = $db->prepare("SELECT stock FROM products WHERE id=? AND status='active'");
        $p->execute([$pid]);
        $prod = $p->fetch();
        if ($prod && $prod['stock'] >= $qty) {
            $db->prepare("INSERT INTO cart (user_id,product_id,quantity) VALUES(?,?,?) ON DUPLICATE KEY UPDATE quantity=quantity+?")->execute([$uid,$pid,$qty,$qty]);
            setFlash('success','Product added to cart!');
        } else {
            setFlash('error','Sorry, not enough stock available.');
        }
        $redirect = $_POST['redirect'] ?? SITE_URL . '/cart.php';
        redirect($redirect);
    }

    if ($action === 'update') {
        $cartId = (int)$_POST['cart_id'];
        $qty = max(1, (int)$_POST['qty']);
        $db->prepare("UPDATE cart SET quantity=? WHERE id=? AND user_id=?")->execute([$qty,$cartId,$uid]);
        setFlash('info','Cart updated.');
        redirect(SITE_URL . '/cart.php');
    }

    if ($action === 'remove') {
        $cartId = (int)$_POST['cart_id'];
        $db->prepare("DELETE FROM cart WHERE id=? AND user_id=?")->execute([$cartId,$uid]);
        setFlash('info','Item removed from cart.');
        redirect(SITE_URL . '/cart.php');
    }

    if ($action === 'clear') {
        $db->prepare("DELETE FROM cart WHERE user_id=?")->execute([$uid]);
        setFlash('info','Cart cleared.');
        redirect(SITE_URL . '/cart.php');
    }
}

// Fetch cart items
$items = $db->prepare("SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.image, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id=?");
$items->execute([$uid]);
$items = $items->fetchAll();

$subtotal = 0;
foreach ($items as $item) $subtotal += $item['price'] * $item['quantity'];
$shipping = $subtotal > 0 ? 2000 : 0;
$total = $subtotal + $shipping;

include 'includes/header.php';
?>

<div class="page-hero">
    <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> Cart</div>
    <h1>🛒 My Cart</h1>
    <p><?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?> in your cart</p>
</div>

<?php if (empty($items)): ?>
<div class="section">
    <div class="empty-state">
        <div class="icon">🛒</div>
        <h3>Your cart is empty</h3>
        <p>Start shopping to add products to your cart.</p>
        <a href="shop.php" class="btn btn-gold">Browse Products</a>
    </div>
</div>
<?php else: ?>

<div class="cart-layout">
    <div>
        <div class="cart-table">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="cart-product">
                                <img src="<?= sanitize($item['image']) ?>" alt="<?= sanitize($item['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=100&h=100&fit=crop'">
                                <div>
                                    <a href="product.php?id=<?= $item['product_id'] ?>" style="font-weight:600;font-size:14px"><?= sanitize($item['name']) ?></a>
                                    <?php if ($item['stock'] < $item['quantity']): ?>
                                    <div style="color:var(--danger);font-size:12px">⚠ Only <?= $item['stock'] ?> available</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td><?= formatRwf($item['price']) ?></td>
                        <td>
                            <form method="POST" class="cart-qty-form">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                <button type="button" onclick="changeQty(this,-1)">−</button>
                                <input type="number" name="qty" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>">
                                <button type="button" onclick="changeQty(this,1)">+</button>
                                <button type="submit" class="btn btn-outline btn-sm">Update</button>
                            </form>
                        </td>
                        <td style="font-weight:700;color:var(--gold)"><?= formatRwf($item['price'] * $item['quantity']) ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm" data-confirm="Remove this item from cart?">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="display:flex;gap:12px;margin-top:16px">
            <a href="shop.php" class="btn btn-outline">← Continue Shopping</a>
            <form method="POST">
                <input type="hidden" name="action" value="clear">
                <button type="submit" class="btn btn-danger" data-confirm="Clear all items from cart?">🗑 Clear Cart</button>
            </form>
        </div>
    </div>

    <!-- SUMMARY -->
    <div class="cart-summary">
        <h3>Order Summary</h3>
        <div class="summary-row"><span>Subtotal</span><span><?= formatRwf($subtotal) ?></span></div>
        <div class="summary-row"><span>Delivery Fee</span><span><?= formatRwf($shipping) ?></span></div>
        <div class="summary-row"><span>Total</span><span><?= formatRwf($total) ?></span></div>
        <a href="checkout.php" class="btn btn-gold btn-block" style="margin-top:20px">Proceed to Checkout →</a>
        <div style="margin-top:16px;text-align:center;font-size:12px;color:var(--text-muted)">
            🔒 Secure checkout · MTN MoMo · Airtel · Bank
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
