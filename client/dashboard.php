<?php
require_once '../includes/config.php';
requireLogin();
$pageTitle = 'My Dashboard';
$db = getDB();
$uid = $_SESSION['user_id'];

$user = $db->prepare("SELECT * FROM users WHERE id=?");
$user->execute([$uid]);
$user = $user->fetch();

$totalOrders   = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id=?"); $totalOrders->execute([$uid]); $totalOrders = (int)$totalOrders->fetchColumn();
$totalSpent    = $db->prepare("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE user_id=? AND payment_status='paid'"); $totalSpent->execute([$uid]); $totalSpent = (float)$totalSpent->fetchColumn();
$pendingOrders = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id=? AND order_status='pending'"); $pendingOrders->execute([$uid]); $pendingOrders = (int)$pendingOrders->fetchColumn();
$wishlistCount = $db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id=?"); $wishlistCount->execute([$uid]); $wishlistCount = (int)$wishlistCount->fetchColumn();

$recentOrders = $db->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC LIMIT 5");
$recentOrders->execute([$uid]);
$recentOrders = $recentOrders->fetchAll();

include '../includes/header.php';
?>
<div class="dashboard-layout">
  <aside class="sidebar">
    <div class="sidebar-user">
      <div class="avatar"><?= strtoupper(substr($user['name'],0,1)) ?></div>
      <h4><?= sanitize($user['name']) ?></h4>
      <small><?= sanitize($user['email']) ?></small>
    </div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="active"><span class="nav-icon-dash">📊</span> Dashboard</a>
      <a href="orders.php"><span class="nav-icon-dash">📦</span> My Orders</a>
      <a href="../wishlist.php"><span class="nav-icon-dash">🤍</span> Wishlist</a>
      <a href="profile.php"><span class="nav-icon-dash">👤</span> Profile</a>
      <a href="../shop.php"><span class="nav-icon-dash">🛍</span> Shop</a>
      <a href="../logout.php"><span class="nav-icon-dash">🚪</span> Logout</a>
    </nav>
  </aside>

  <div class="dash-content">
    <div class="dash-header">
      <h1>Welcome back, <?= sanitize(explode(' ',$user['name'])[0]) ?>! 👋</h1>
      <a href="../shop.php" class="btn btn-gold btn-sm">+ Shop Now</a>
    </div>

    <div class="stat-cards">
      <div class="stat-card"><div class="stat-icon">📦</div><div class="stat-info"><h3><?= $totalOrders ?></h3><p>Total Orders</p></div></div>
      <div class="stat-card"><div class="stat-icon">⏳</div><div class="stat-info"><h3><?= $pendingOrders ?></h3><p>Pending Orders</p></div></div>
      <div class="stat-card"><div class="stat-icon">💰</div><div class="stat-info"><h3 style="font-size:18px"><?= formatRwf($totalSpent) ?></h3><p>Total Spent</p></div></div>
      <div class="stat-card"><div class="stat-icon">🤍</div><div class="stat-info"><h3><?= $wishlistCount ?></h3><p>Wishlist Items</p></div></div>
    </div>

    <div class="dash-table-card">
      <div class="dash-table-card-header">
        <h3>Recent Orders</h3>
        <a href="orders.php" class="btn btn-outline btn-sm">View All</a>
      </div>
      <?php if (empty($recentOrders)): ?>
      <div class="empty-state" style="padding:40px"><div class="icon">📦</div><h3>No orders yet</h3><p>Start shopping!</p><a href="../shop.php" class="btn btn-gold">Browse Products</a></div>
      <?php else: ?>
      <table class="dash-table">
        <thead><tr><th>Order #</th><th>Date</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($recentOrders as $o):
          $itemCount = $db->prepare("SELECT SUM(quantity) FROM order_items WHERE order_id=?");
          $itemCount->execute([$o['id']]); $itemCount = (int)$itemCount->fetchColumn();
        ?>
        <tr>
          <td><strong style="color:var(--gold)"><?= sanitize($o['order_number']) ?></strong></td>
          <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
          <td><?= $itemCount ?> item<?= $itemCount!==1?'s':'' ?></td>
          <td><strong><?= formatRwf($o['total_amount']) ?></strong></td>
          <td><span class="status-badge badge-info"><?= strtoupper(str_replace('_',' ',$o['payment_method'])) ?></span></td>
          <td>
            <span class="status-badge badge-<?= $o['order_status'] ?>"><?= ucfirst($o['order_status']) ?></span>
            <span class="status-badge badge-<?= $o['payment_status'] ?>" style="margin-left:4px"><?= ucfirst($o['payment_status']) ?></span>
          </td>
          <td><a href="orders.php?view=<?= $o['id'] ?>" class="btn btn-outline btn-sm">View</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
