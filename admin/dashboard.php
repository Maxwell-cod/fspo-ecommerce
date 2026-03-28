<?php
require_once '../includes/config.php';
requireLogin(); requireAdmin();
$pageTitle = 'Admin Dashboard';
$db = getDB();

$totalOrders    = (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue   = (float)$db->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE payment_status='paid'")->fetchColumn();
$pendingOrders  = (int)$db->query("SELECT COUNT(*) FROM orders WHERE order_status='pending'")->fetchColumn();
$totalProducts  = (int)$db->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn();
$totalClients   = (int)$db->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetchColumn();
$totalMessages  = (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
$lowStock       = (int)$db->query("SELECT COUNT(*) FROM products WHERE stock < 5 AND status='active'")->fetchColumn();

$recentOrders = $db->query("SELECT o.*, u.name as client_name, u.email as client_email, u.phone as client_phone FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC LIMIT 10")->fetchAll();

// Revenue by month (last 6 months)
$monthlyRevenue = $db->query("SELECT DATE_FORMAT(created_at,'%b %Y') as month, SUM(total_amount) as revenue, COUNT(*) as orders FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY created_at ASC")->fetchAll();

include '../includes/header.php';
?>
<div class="dashboard-layout">
  <aside class="sidebar">
    <div class="sidebar-user">
      <div class="avatar" style="background:var(--danger)">A</div>
      <h4><?= sanitize($_SESSION['user_name']) ?></h4>
      <small style="color:var(--gold)">Administrator</small>
    </div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="active"><span class="nav-icon-dash">📊</span> Dashboard</a>
      <a href="orders.php"><span class="nav-icon-dash">📦</span> All Orders</a>
      <a href="products.php"><span class="nav-icon-dash">🛍</span> Products</a>
      <a href="categories.php"><span class="nav-icon-dash">📂</span> Categories</a>
      <a href="users.php"><span class="nav-icon-dash">👥</span> Clients</a>
      <a href="messages.php"><span class="nav-icon-dash">✉</span> Messages <?php if($totalMessages>0): ?><span style="background:var(--danger);color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px"><?= $totalMessages ?></span><?php endif; ?></a>
      <a href="settings.php"><span class="nav-icon-dash">⚙</span> Settings & API</a>
      <a href="../index.php"><span class="nav-icon-dash">🌐</span> View Website</a>
      <a href="../logout.php"><span class="nav-icon-dash">🚪</span> Logout</a>
    </nav>
  </aside>

  <div class="dash-content">
    <div class="dash-header">
      <h1>Admin Dashboard</h1>
      <div style="display:flex;gap:10px">
        <a href="products.php?action=add" class="btn btn-gold btn-sm">+ Add Product</a>
        <a href="orders.php" class="btn btn-outline btn-sm">View Orders</a>
      </div>
    </div>

    <!-- STATS -->
    <div class="stat-cards" style="grid-template-columns:repeat(4,1fr)">
      <div class="stat-card"><div class="stat-icon">💰</div><div class="stat-info"><h3 style="font-size:16px"><?= formatRwf($totalRevenue) ?></h3><p>Total Revenue (Paid)</p></div></div>
      <div class="stat-card"><div class="stat-icon">📦</div><div class="stat-info"><h3><?= $totalOrders ?></h3><p>Total Orders</p></div></div>
      <div class="stat-card"><div class="stat-icon">⏳</div><div class="stat-info"><h3 style="color:var(--warning)"><?= $pendingOrders ?></h3><p>Pending Orders</p></div></div>
      <div class="stat-card"><div class="stat-icon">👥</div><div class="stat-info"><h3><?= $totalClients ?></h3><p>Registered Clients</p></div></div>
    </div>
    <div class="stat-cards" style="grid-template-columns:repeat(4,1fr);margin-top:0">
      <div class="stat-card"><div class="stat-icon">🛍</div><div class="stat-info"><h3><?= $totalProducts ?></h3><p>Active Products</p></div></div>
      <div class="stat-card"><div class="stat-icon">⚠</div><div class="stat-info"><h3 style="color:var(--warning)"><?= $lowStock ?></h3><p>Low Stock Products</p></div></div>
      <div class="stat-card"><div class="stat-icon">✉</div><div class="stat-info"><h3 style="color:var(--danger)"><?= $totalMessages ?></h3><p>Unread Messages</p></div></div>
      <div class="stat-card"><div class="stat-icon">📈</div><div class="stat-info"><h3><?= count($monthlyRevenue) ?></h3><p>Active Months</p></div></div>
    </div>

    <!-- MONTHLY REVENUE TABLE -->
    <?php if ($monthlyRevenue): ?>
    <div class="dash-table-card" style="margin-bottom:24px">
      <div class="dash-table-card-header"><h3>Monthly Revenue (Last 6 Months)</h3></div>
      <table class="dash-table">
        <thead><tr><th>Month</th><th>Orders</th><th>Revenue</th></tr></thead>
        <tbody>
        <?php foreach ($monthlyRevenue as $m): ?>
        <tr>
          <td><?= sanitize($m['month']) ?></td>
          <td><?= $m['orders'] ?></td>
          <td><strong style="color:var(--gold)"><?= formatRwf($m['revenue']) ?></strong></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

    <!-- RECENT ORDERS -->
    <div class="dash-table-card">
      <div class="dash-table-card-header">
        <h3>Recent Orders</h3>
        <a href="orders.php" class="btn btn-outline btn-sm">View All Orders</a>
      </div>
      <table class="dash-table">
        <thead><tr><th>Order #</th><th>Client</th><th>Phone/Email</th><th>Date</th><th>Amount</th><th>Payment</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($recentOrders as $o): ?>
        <tr>
          <td><strong style="color:var(--gold)"><?= sanitize($o['order_number']) ?></strong></td>
          <td><?= sanitize($o['client_name']) ?></td>
          <td style="font-size:12px;color:var(--text-muted)"><?= sanitize($o['client_email']) ?><br><?= sanitize($o['client_phone']??'') ?></td>
          <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
          <td><strong><?= formatRwf($o['total_amount']) ?></strong></td>
          <td><span class="status-badge badge-info" style="font-size:10px"><?= strtoupper(str_replace('_',' ',$o['payment_method'])) ?></span></td>
          <td>
            <span class="status-badge badge-<?= $o['payment_status'] ?>"><?= ucfirst($o['payment_status']) ?></span>
            <span class="status-badge badge-<?= $o['order_status'] ?>" style="margin-left:4px"><?= ucfirst($o['order_status']) ?></span>
          </td>
          <td><a href="orders.php?view=<?= $o['id'] ?>" class="btn btn-outline btn-sm">Manage</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
