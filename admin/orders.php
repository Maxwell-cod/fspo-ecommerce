<?php
require_once '../includes/config.php';
requireLogin(); requireAdmin();
$pageTitle = 'Manage Orders';
$db = getDB();

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oid           = (int)$_POST['order_id'];
    $orderStatus   = $_POST['order_status']   ?? '';
    $paymentStatus = $_POST['payment_status'] ?? '';
    $db->prepare("UPDATE orders SET order_status=?, payment_status=? WHERE id=?")->execute([$orderStatus,$paymentStatus,$oid]);
    setFlash('success','Order updated successfully!');
    redirect(SITE_URL.'/admin/orders.php?view='.$oid);
}

// Single order detail
$viewId = (int)($_GET['view'] ?? 0);
$order  = null; $orderItems = []; $client = null;
if ($viewId) {
    $s = $db->prepare("SELECT o.*, u.name as client_name, u.email as client_email, u.phone as client_phone FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id=?");
    $s->execute([$viewId]); $order = $s->fetch();
    if ($order) {
        $s2 = $db->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=?");
        $s2->execute([$viewId]); $orderItems = $s2->fetchAll();
    }
}

// Filters
$filterStatus  = $_GET['status']  ?? '';
$filterPayment = $_GET['payment'] ?? '';
$search        = trim($_GET['q']  ?? '');
$where = ['1=1']; $params = [];
if ($filterStatus)  { $where[] = 'o.order_status=?';   $params[] = $filterStatus; }
if ($filterPayment) { $where[] = 'o.payment_status=?'; $params[] = $filterPayment; }
if ($search)        { $where[] = '(o.order_number LIKE ? OR u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)'; $s="%$search%"; $params=array_merge($params,[$s,$s,$s,$s]); }
$whereSQL = implode(' AND ',$where);

$orders = $db->prepare("SELECT o.*, u.name as client_name, u.email as client_email, u.phone as client_phone FROM orders o JOIN users u ON o.user_id=u.id WHERE $whereSQL ORDER BY o.created_at DESC");
$orders->execute($params); $orders = $orders->fetchAll();

include '../includes/header.php';
?>
<div class="dashboard-layout">
  <aside class="sidebar">
    <div class="sidebar-user"><div class="avatar" style="background:var(--danger)">A</div><h4><?= sanitize($_SESSION['user_name']) ?></h4><small style="color:var(--gold)">Administrator</small></div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"><span class="nav-icon-dash">📊</span> Dashboard</a>
      <a href="orders.php" class="active"><span class="nav-icon-dash">📦</span> All Orders</a>
      <a href="products.php"><span class="nav-icon-dash">🛍</span> Products</a>
      <a href="categories.php"><span class="nav-icon-dash">📂</span> Categories</a>
      <a href="users.php"><span class="nav-icon-dash">👥</span> Clients</a>
      <a href="messages.php"><span class="nav-icon-dash">✉</span> Messages</a>
      <a href="settings.php"><span class="nav-icon-dash">⚙</span> Settings & API</a>
      <a href="../index.php"><span class="nav-icon-dash">🌐</span> View Website</a>
      <a href="../logout.php"><span class="nav-icon-dash">🚪</span> Logout</a>
    </nav>
  </aside>

  <div class="dash-content">
    <?php if ($order): ?>
    <!-- ORDER DETAIL -->
    <div class="dash-header">
      <div><a href="orders.php" style="color:var(--text-muted);font-size:13px">← Back to Orders</a><h1 style="margin-top:6px">Order <?= sanitize($order['order_number']) ?></h1></div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:24px">
      <div>
        <div class="dash-table-card">
          <div class="dash-table-card-header"><h3>Items Ordered</h3></div>
          <table class="dash-table">
            <thead><tr><th>Product</th><th>Unit Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php foreach ($orderItems as $item): ?>
            <tr>
              <td><div style="display:flex;align-items:center;gap:10px">
                <img src="<?= sanitize($item['image']) ?>" style="width:46px;height:46px;object-fit:cover;border-radius:6px" onerror="this.style.display='none'">
                <span style="font-weight:600"><?= sanitize($item['name']) ?></span>
              </div></td>
              <td><?= formatRwf($item['unit_price']) ?></td>
              <td><?= $item['quantity'] ?></td>
              <td><strong style="color:var(--gold)"><?= formatRwf($item['subtotal']) ?></strong></td>
            </tr>
            <?php endforeach; ?>
            <tr style="background:var(--dark3)"><td colspan="3" style="text-align:right;font-weight:700;padding:14px 18px">TOTAL</td><td style="padding:14px 18px"><strong style="color:var(--gold);font-size:16px"><?= formatRwf($order['total_amount']) ?></strong></td></tr>
            </tbody>
          </table>
        </div>

        <!-- UPDATE STATUS FORM -->
        <div class="admin-form-card">
          <h3>Update Order Status</h3>
          <form method="POST">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <div class="form-grid">
              <div class="form-group">
                <label>Order Status</label>
                <select name="order_status" class="form-control">
                  <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                  <option value="<?= $s ?>" <?= $order['order_status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Payment Status</label>
                <select name="payment_status" class="form-control">
                  <?php foreach (['pending','paid','failed'] as $s): ?>
                  <option value="<?= $s ?>" <?= $order['payment_status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <button type="submit" class="btn btn-gold">Update Order</button>
          </form>
        </div>
      </div>

      <!-- CLIENT & ORDER INFO -->
      <div>
        <div class="admin-form-card">
          <h3>Client Information</h3>
          <div style="font-size:13.5px">
            <div style="padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Name</span><br><strong><?= sanitize($order['client_name']) ?></strong></div>
            <div style="padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Email</span><br><a href="mailto:<?= sanitize($order['client_email']) ?>" style="color:var(--gold-light)"><?= sanitize($order['client_email']) ?></a></div>
            <div style="padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Phone</span><br><a href="tel:<?= sanitize($order['client_phone']??'') ?>" style="color:var(--gold-light)"><?= sanitize($order['client_phone']??'N/A') ?></a></div>
            <div style="padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Order Date</span><br><strong><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></strong></div>
            <div style="padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Payment Method</span><br><span class="status-badge badge-info"><?= strtoupper(str_replace('_',' ',$order['payment_method'])) ?></span></div>
            <?php if ($order['transaction_ref']): ?>
            <div style="padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Transaction Ref</span><br><strong style="color:var(--success)"><?= sanitize($order['transaction_ref']) ?></strong></div>
            <?php endif; ?>
            <?php if ($order['delivery_address']): ?>
            <div style="padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Delivery Address</span><br><?= sanitize($order['delivery_address']) ?></div>
            <?php endif; ?>
            <?php if ($order['notes']): ?>
            <div style="padding:8px 0"><span style="color:var(--text-muted)">Notes</span><br><?= sanitize($order['notes']) ?></div>
            <?php endif; ?>
          </div>
          <div style="margin-top:16px;display:flex;flex-direction:column;gap:8px">
            <a href="mailto:<?= sanitize($order['client_email']) ?>" class="btn btn-outline btn-sm btn-block">✉ Email Client</a>
            <a href="https://wa.me/<?= preg_replace('/\D/','',$order['client_phone']??'250785723677') ?>?text=Hello+<?= urlencode($order['client_name']) ?>+regarding+your+order+<?= urlencode($order['order_number']) ?>" target="_blank" class="btn btn-primary btn-sm btn-block">💬 WhatsApp Client</a>
          </div>
        </div>
      </div>
    </div>

    <?php else: ?>
    <!-- ORDERS LIST -->
    <div class="dash-header">
      <h1>All Orders</h1>
      <div style="font-size:13px;color:var(--text-muted)"><?= count($orders) ?> orders found</div>
    </div>

    <!-- FILTERS -->
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px">
      <input type="text" name="q" class="form-control" placeholder="Search by name, email, order #..." value="<?= sanitize($search) ?>" style="flex:1;min-width:200px">
      <select name="status" class="form-control" style="width:160px">
        <option value="">All Statuses</option>
        <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
        <option value="<?= $s ?>" <?= $filterStatus===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="payment" class="form-control" style="width:160px">
        <option value="">All Payments</option>
        <?php foreach (['pending','paid','failed'] as $s): ?>
        <option value="<?= $s ?>" <?= $filterPayment===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-gold">Filter</button>
      <a href="orders.php" class="btn btn-outline">Clear</a>
    </form>

    <div class="dash-table-card">
      <table class="dash-table">
        <thead><tr><th>Order #</th><th>Client Name</th><th>Email / Phone</th><th>Date</th><th>Amount (Rwf)</th><th>Payment Method</th><th>Payment</th><th>Order Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if (empty($orders)): ?>
        <tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted)">No orders found.</td></tr>
        <?php else: ?>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td><strong style="color:var(--gold)"><?= sanitize($o['order_number']) ?></strong></td>
          <td><strong><?= sanitize($o['client_name']) ?></strong></td>
          <td style="font-size:12px">
            <div><?= sanitize($o['client_email']) ?></div>
            <div style="color:var(--text-muted)"><?= sanitize($o['client_phone']??'') ?></div>
          </td>
          <td><?= date('d M Y', strtotime($o['created_at'])) ?><br><small style="color:var(--text-muted)"><?= date('H:i', strtotime($o['created_at'])) ?></small></td>
          <td><strong style="color:var(--gold)"><?= number_format($o['total_amount'],0,'.',',') ?></strong></td>
          <td><span class="status-badge badge-info" style="font-size:10px"><?= strtoupper(str_replace('_',' ',$o['payment_method'])) ?></span></td>
          <td><span class="status-badge badge-<?= $o['payment_status'] ?>"><?= ucfirst($o['payment_status']) ?></span></td>
          <td><span class="status-badge badge-<?= $o['order_status'] ?>"><?= ucfirst($o['order_status']) ?></span></td>
          <td><a href="orders.php?view=<?= $o['id'] ?>" class="btn btn-gold btn-sm">Manage</a></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
