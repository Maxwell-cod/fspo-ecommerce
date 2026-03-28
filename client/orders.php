<?php
require_once '../includes/config.php';
requireLogin();
$pageTitle = 'My Orders';
$db  = getDB();
$uid = $_SESSION['user_id'];

// Single order view
$viewId = (int)($_GET['view'] ?? 0);
$order  = null;
$orderItems = [];
if ($viewId) {
    $s = $db->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
    $s->execute([$viewId, $uid]);
    $order = $s->fetch();
    if ($order) {
        $s2 = $db->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=?");
        $s2->execute([$viewId]);
        $orderItems = $s2->fetchAll();
    }
}

// List all orders
$orders = $db->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
$orders->execute([$uid]);
$orders = $orders->fetchAll();

include '../includes/header.php';
?>
<div class="dashboard-layout">
  <aside class="sidebar">
    <div class="sidebar-user">
      <div class="avatar"><?= strtoupper(substr($_SESSION['user_name'],0,1)) ?></div>
      <h4><?= sanitize($_SESSION['user_name']) ?></h4>
      <small><?= sanitize($_SESSION['user_email']) ?></small>
    </div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"><span class="nav-icon-dash">📊</span> Dashboard</a>
      <a href="orders.php" class="active"><span class="nav-icon-dash">📦</span> My Orders</a>
      <a href="../wishlist.php"><span class="nav-icon-dash">🤍</span> Wishlist</a>
      <a href="profile.php"><span class="nav-icon-dash">👤</span> Profile</a>
      <a href="../shop.php"><span class="nav-icon-dash">🛍</span> Shop</a>
      <a href="../logout.php"><span class="nav-icon-dash">🚪</span> Logout</a>
    </nav>
  </aside>

  <div class="dash-content">
    <?php if ($order): ?>
    <!-- ORDER DETAIL VIEW -->
    <div class="dash-header">
      <div>
        <a href="orders.php" style="color:var(--text-muted);font-size:13px">← Back to Orders</a>
        <h1 style="margin-top:6px">Order <?= sanitize($order['order_number']) ?></h1>
      </div>
      <span class="status-badge badge-<?= $order['order_status'] ?>" style="font-size:13px;padding:6px 14px"><?= ucfirst($order['order_status']) ?></span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:24px">
      <div>
        <div class="dash-table-card">
          <div class="dash-table-card-header"><h3>Items Ordered</h3></div>
          <table class="dash-table">
            <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php foreach ($orderItems as $item): ?>
            <tr>
              <td><div style="display:flex;align-items:center;gap:12px">
                <img src="<?= sanitize($item['image']) ?>" style="width:48px;height:48px;object-fit:cover;border-radius:6px" onerror="this.style.display='none'">
                <span style="font-weight:600"><?= sanitize($item['name']) ?></span>
              </div></td>
              <td><?= formatRwf($item['unit_price']) ?></td>
              <td><?= $item['quantity'] ?></td>
              <td><strong style="color:var(--gold)"><?= formatRwf($item['subtotal']) ?></strong></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div>
        <div class="admin-form-card">
          <h3>Order Summary</h3>
          <div style="font-size:13.5px">
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Order #</span><strong><?= sanitize($order['order_number']) ?></strong></div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Date</span><span><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></span></div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Payment</span><span><?= strtoupper(str_replace('_',' ',$order['payment_method'])) ?></span></div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Payment Status</span><span class="status-badge badge-<?= $order['payment_status'] ?>"><?= ucfirst($order['payment_status']) ?></span></div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Order Status</span><span class="status-badge badge-<?= $order['order_status'] ?>"><?= ucfirst($order['order_status']) ?></span></div>
            <?php if ($order['transaction_ref']): ?>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Txn Ref</span><span><?= sanitize($order['transaction_ref']) ?></span></div>
            <?php endif; ?>
            <div style="display:flex;justify-content:space-between;padding:10px 0;font-size:16px;font-weight:700;color:var(--gold)"><span>Total</span><span><?= formatRwf($order['total_amount']) ?></span></div>
          </div>
          <?php if ($order['delivery_address']): ?>
          <div style="margin-top:14px;padding:12px;background:var(--dark3);border-radius:8px;font-size:13px;color:var(--text-muted)">
            <strong style="color:var(--white);display:block;margin-bottom:4px">Delivery Address</strong>
            <?= sanitize($order['delivery_address']) ?>
          </div>
          <?php endif; ?>
          <a href="https://wa.me/250785723677?text=Hi+FSPO+Ltd,+I+have+a+question+about+order+<?= urlencode($order['order_number']) ?>" target="_blank" class="btn btn-primary btn-block" style="margin-top:16px">💬 WhatsApp About This Order</a>
        </div>
      </div>
    </div>

    <?php else: ?>
    <!-- ORDERS LIST -->
    <div class="dash-header">
      <h1>My Orders</h1>
      <a href="../shop.php" class="btn btn-gold btn-sm">+ Shop Now</a>
    </div>

    <?php if (empty($orders)): ?>
    <div class="empty-state"><div class="icon">📦</div><h3>No orders yet</h3><p>Your orders will appear here once you purchase.</p><a href="../shop.php" class="btn btn-gold">Browse Products</a></div>
    <?php else: ?>
    <div class="dash-table-card">
      <table class="dash-table">
        <thead><tr><th>Order #</th><th>Date</th><th>Total</th><th>Payment Method</th><th>Payment</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td><strong style="color:var(--gold)"><?= sanitize($o['order_number']) ?></strong></td>
          <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
          <td><strong><?= formatRwf($o['total_amount']) ?></strong></td>
          <td><?= strtoupper(str_replace('_',' ',$o['payment_method'])) ?></td>
          <td><span class="status-badge badge-<?= $o['payment_status'] ?>"><?= ucfirst($o['payment_status']) ?></span></td>
          <td><span class="status-badge badge-<?= $o['order_status'] ?>"><?= ucfirst($o['order_status']) ?></span></td>
          <td><a href="orders.php?view=<?= $o['id'] ?>" class="btn btn-outline btn-sm">View</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
