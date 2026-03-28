<?php
// ajax/cart.php — handles AJAX add/remove/count for cart badge updates
require_once '../includes/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart.', 'redirect' => SITE_URL . '/login.php']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$uid    = $_SESSION['user_id'];
$db     = getDB();

if ($action === 'add') {
    $pid = (int)($_POST['product_id'] ?? 0);
    $qty = max(1, (int)($_POST['qty'] ?? 1));

    if (!$pid) {
        echo json_encode(['success' => false, 'message' => 'Invalid product.']);
        exit;
    }

    // Check product exists and has stock
    $s = $db->prepare("SELECT id, name, stock FROM products WHERE id = ? AND status = 'active'");
    $s->execute([$pid]);
    $product = $s->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }

    // Check current cart quantity
    $current = $db->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $current->execute([$uid, $pid]);
    $currentQty = (int)($current->fetchColumn() ?: 0);

    if (($currentQty + $qty) > $product['stock']) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available. Only ' . $product['stock'] . ' left.']);
        exit;
    }

    $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?")
       ->execute([$uid, $pid, $qty, $qty]);

    $cartCount = getCartCount();
    echo json_encode([
        'success'    => true,
        'message'    => '✓ ' . $product['name'] . ' added to cart!',
        'cart_count' => $cartCount
    ]);
    exit;
}

if ($action === 'remove') {
    $pid = (int)($_POST['product_id'] ?? 0);
    $db->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?")->execute([$uid, $pid]);
    echo json_encode(['success' => true, 'message' => 'Item removed.', 'cart_count' => getCartCount()]);
    exit;
}

if ($action === 'count') {
    echo json_encode(['success' => true, 'cart_count' => getCartCount()]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unknown action.']);
