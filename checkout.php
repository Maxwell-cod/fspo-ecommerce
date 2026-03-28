<?php
require_once 'includes/config.php';
requireLogin();
$pageTitle = 'Checkout';
$db = getDB();
$uid = $_SESSION['user_id'];

// Buy Now single product
$buyNow = isset($_GET['buy_now']) ? (int)$_GET['buy_now'] : 0;

// Get cart items or buy-now item
if ($buyNow) {
    $stmt = $db->prepare("SELECT p.id as product_id, p.name, p.price, p.image, p.stock, 1 as quantity FROM products p WHERE p.id=? AND p.status='active' AND p.stock>0");
    $stmt->execute([$buyNow]);
    $items = $stmt->fetchAll();
} else {
    $stmt = $db->prepare("SELECT c.quantity, p.id as product_id, p.name, p.price, p.image, p.stock FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=? AND p.status='active'");
    $stmt->execute([$uid]);
    $items = $stmt->fetchAll();
}

if (empty($items)) {
    setFlash('error','Your cart is empty. Add products before checking out.');
    redirect(SITE_URL . '/shop.php');
}

$subtotal = 0;
foreach ($items as $i) $subtotal += $i['price'] * $i['quantity'];
$shipping = 2000;
$total = $subtotal + $shipping;

// Get user info
$user = $db->prepare("SELECT * FROM users WHERE id=?");
$user->execute([$uid]);
$user = $user->fetch();

// Handle order submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payMethod = $_POST['payment_method'] ?? '';
    $bankChoice = $_POST['bank_choice'] ?? '';
    $address    = trim($_POST['delivery_address'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $notes      = trim($_POST['notes'] ?? '');
    $transRef   = trim($_POST['transaction_ref'] ?? '');

    $validMethods = ['mtn_momo','airtel_money','bank'];
    $validBanks   = ['bank_bk','bank_equity','bank_cogebanque','bank_kcb','bank_access','bank_gt','bank_ab'];

    if (!$payMethod || !in_array($payMethod,$validMethods)) $errors[] = 'Please select a payment method.';
    if ($payMethod === 'bank' && (!$bankChoice || !in_array($bankChoice,$validBanks))) $errors[] = 'Please select a bank.';
    if (!$address) $errors[] = 'Delivery address is required.';
    if (!$phone)   $errors[] = 'Phone number is required.';

    $finalMethod = $payMethod === 'bank' ? $bankChoice : $payMethod;

    if (empty($errors)) {
        try {
            $db->beginTransaction();

            $orderNum = generateOrderNumber();
            $db->prepare("INSERT INTO orders (user_id,order_number,total_amount,payment_method,payment_status,order_status,delivery_address,notes,transaction_ref)
                          VALUES (?,?,?,?,'pending','pending',?,?,?)")
               ->execute([$uid,$orderNum,$total,$finalMethod,"$phone – $address",$notes,$transRef]);
            $orderId = $db->lastInsertId();

            foreach ($items as $item) {
                $db->prepare("INSERT INTO order_items (order_id,product_id,quantity,unit_price,subtotal) VALUES (?,?,?,?,?)")
                   ->execute([$orderId,$item['product_id'],$item['quantity'],$item['price'],$item['price']*$item['quantity']]);
                $db->prepare("UPDATE products SET stock = stock - ? WHERE id=?")->execute([$item['quantity'],$item['product_id']]);
            }

            if (!$buyNow) {
                $db->prepare("DELETE FROM cart WHERE user_id=?")->execute([$uid]);
            }

            $db->commit();
            setFlash('success','✓ Order ' . $orderNum . ' placed successfully! We will contact you to confirm payment.');
            redirect(SITE_URL . '/client/order-success.php?order=' . $orderNum);

        } catch (Exception $e) {
            $db->rollBack();
            $errors[] = 'Order failed. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="page-hero">
    <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> <a href="cart.php">Cart</a> <span>/</span> Checkout</div>
    <h1>Checkout</h1>
</div>

<?php if ($errors): ?>
<div class="section" style="padding-bottom:0">
    <div class="alert alert-danger"><strong>Please fix these errors:</strong><ul style="margin-top:8px;margin-left:20px"><?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?></ul></div>
</div>
<?php endif; ?>

<form method="POST">
<div class="checkout-layout">
    <div>
        <!-- DELIVERY INFO -->
        <div class="checkout-section">
            <h3>📦 Delivery Information</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" class="form-control" value="<?= sanitize($user['name']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" value="<?= sanitize($user['email']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="tel" name="phone" class="form-control" placeholder="+250 7XX XXX XXX" value="<?= sanitize($user['phone'] ?? '') ?>" required>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Delivery Address *</label>
                    <textarea name="delivery_address" class="form-control" rows="2" placeholder="e.g. Kigali, Kimironko, near KBC..." required><?= sanitize($user['address'] ?? '') ?></textarea>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Order Notes (optional)</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any special instructions for your order..."></textarea>
                </div>
            </div>
        </div>

        <!-- PAYMENT METHOD -->
        <div class="checkout-section">
            <h3>💳 Payment Method</h3>
            <div class="payment-methods">

                <!-- MTN MoMo -->
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="mtn_momo" required>
                    <div class="payment-icon">📱</div>
                    <div class="payment-label">
                        <strong>MTN Mobile Money</strong>
                        <small>Send to: +250 785 723 677 – FSPO Ltd</small>
                    </div>
                </label>

                <!-- Airtel Money -->
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="airtel_money">
                    <div class="payment-icon">📲</div>
                    <div class="payment-label">
                        <strong>Airtel Money</strong>
                        <small>Send to: +250 785 723 677 – FSPO Ltd</small>
                    </div>
                </label>

                <!-- Bank Transfer -->
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="bank" id="bankRadio">
                    <div class="payment-icon">🏦</div>
                    <div class="payment-label">
                        <strong>Bank Transfer</strong>
                        <small>Select your bank below</small>
                    </div>
                </label>

                <!-- Bank Options -->
                <div class="bank-options" id="bankOptions">
                    <?php
                    $banks = [
                        'bank_bk'         => ['Bank of Kigali (BK)',     'AC: 00040-06763401-51'],
                        'bank_equity'      => ['Equity Bank Rwanda',       'AC: 4000260773'],
                        'bank_cogebanque'  => ['Cogebanque',               'AC: 111-0003-15'],
                        'bank_kcb'         => ['KCB Bank Rwanda',          'AC: 1234567890'],
                        'bank_access'      => ['Access Bank Rwanda',       'AC: 0987654321'],
                        'bank_gt'          => ['GT Bank Rwanda',           'AC: 0112345678'],
                        'bank_ab'          => ['AB Bank Rwanda',           'AC: 0001122334'],
                    ];
                    foreach ($banks as $val => [$name, $ac]): ?>
                    <label class="bank-option">
                        <input type="radio" name="bank_choice" value="<?= $val ?>">
                        <div>
                            <strong style="font-size:13px;display:block"><?= $name ?></strong>
                            <small style="color:var(--text-muted)"><?= $ac ?></small>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>

            </div>

            <div class="form-group" style="margin-top:20px">
                <label>Transaction Reference / Screenshot Reference (optional)</label>
                <input type="text" name="transaction_ref" class="form-control" placeholder="e.g. MoMo transaction ID or bank reference number">
                <small style="color:var(--text-muted);font-size:12px;margin-top:5px;display:block">After payment, enter your transaction reference. We'll verify and confirm your order.</small>
            </div>
        </div>
    </div>

    <!-- ORDER SUMMARY -->
    <div class="order-summary-box">
        <h3 style="font-family:'Playfair Display',serif;font-size:20px;color:var(--gold);margin-bottom:20px">Order Summary</h3>
        <?php foreach ($items as $item): ?>
        <div class="order-item-row">
            <div style="display:flex;align-items:center;gap:10px">
                <img src="<?= sanitize($item['image']) ?>" alt="" onerror="this.style.display='none'" style="width:44px;height:44px;object-fit:cover;border-radius:6px">
                <div>
                    <div style="font-size:13px;font-weight:600"><?= sanitize($item['name']) ?></div>
                    <div style="font-size:12px;color:var(--text-muted)">Qty: <?= $item['quantity'] ?></div>
                </div>
            </div>
            <div style="font-weight:700;color:var(--gold);font-size:13px"><?= formatRwf($item['price'] * $item['quantity']) ?></div>
        </div>
        <?php endforeach; ?>

        <div style="border-top:1px solid var(--border);margin:16px 0;padding-top:16px">
            <div class="summary-row" style="display:flex;justify-content:space-between;font-size:13.5px;padding:7px 0;border-bottom:1px solid var(--border)"><span>Subtotal</span><span><?= formatRwf($subtotal) ?></span></div>
            <div class="summary-row" style="display:flex;justify-content:space-between;font-size:13.5px;padding:7px 0;border-bottom:1px solid var(--border)"><span>Delivery</span><span><?= formatRwf($shipping) ?></span></div>
            <div style="display:flex;justify-content:space-between;font-size:17px;font-weight:700;color:var(--gold);padding:10px 0"><span>Total</span><span><?= formatRwf($total) ?></span></div>
        </div>

        <button type="submit" class="btn btn-gold btn-block" style="font-size:15px;padding:15px">
            ✓ Place Order – <?= formatRwf($total) ?>
        </button>
        <div style="text-align:center;margin-top:14px;font-size:12px;color:var(--text-muted)">
            🔒 Your order is secure and protected
        </div>
    </div>
</div>
</form>

<?php include 'includes/footer.php'; ?>
