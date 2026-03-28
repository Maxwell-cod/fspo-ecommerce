<?php
// admin/users.php
require_once '../includes/config.php';
requireLogin(); requireAdmin();
$pageTitle = 'Manage Clients';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid    = (int)$_POST['user_id'];
    $action = $_POST['action'] ?? '';
    if ($action === 'toggle_role') {
        $u = $db->prepare("SELECT role FROM users WHERE id=?"); $u->execute([$uid]); $u = $u->fetch();
        $newRole = $u['role'] === 'admin' ? 'client' : 'admin';
        $db->prepare("UPDATE users SET role=? WHERE id=?")->execute([$newRole,$uid]);
        setFlash('success','User role updated!');
    }
    if ($action === 'delete') {
        $db->prepare("DELETE FROM users WHERE id=? AND role='client'")->execute([$uid]);
        setFlash('info','User deleted.');
    }
    redirect(SITE_URL.'/admin/users.php');
}

$search = trim($_GET['q'] ?? '');
$where = ["role != 'admin'"]; $params = [];
if ($search) { $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)"; $s="%$search%"; $params=[$s,$s,$s]; }
$users = $db->prepare("SELECT u.*, COUNT(o.id) as order_count, COALESCE(SUM(o.total_amount),0) as total_spent FROM users u LEFT JOIN orders o ON u.id=o.user_id WHERE ".implode(' AND ',$where)." GROUP BY u.id ORDER BY u.created_at DESC");
$users->execute($params); $users = $users->fetchAll();

include '../includes/header.php';
?>
<div class="dashboard-layout">
  <aside class="sidebar">
    <div class="sidebar-user"><div class="avatar" style="background:var(--danger)">A</div><h4><?= sanitize($_SESSION['user_name']) ?></h4><small style="color:var(--gold)">Administrator</small></div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"><span class="nav-icon-dash">📊</span> Dashboard</a>
      <a href="orders.php"><span class="nav-icon-dash">📦</span> All Orders</a>
      <a href="products.php"><span class="nav-icon-dash">🛍</span> Products</a>
      <a href="categories.php"><span class="nav-icon-dash">📂</span> Categories</a>
      <a href="users.php" class="active"><span class="nav-icon-dash">👥</span> Clients</a>
      <a href="messages.php"><span class="nav-icon-dash">✉</span> Messages</a>
      <a href="settings.php"><span class="nav-icon-dash">⚙</span> Settings & API</a>
      <a href="../index.php"><span class="nav-icon-dash">🌐</span> View Website</a>
      <a href="../logout.php"><span class="nav-icon-dash">🚪</span> Logout</a>
    </nav>
  </aside>
  <div class="dash-content">
    <div class="dash-header"><h1>Registered Clients</h1><p style="color:var(--text-muted)"><?= count($users) ?> clients</p></div>
    <form method="GET" style="margin-bottom:20px;display:flex;gap:10px">
      <input type="text" name="q" class="form-control" placeholder="Search by name, email, phone..." value="<?= sanitize($search) ?>" style="max-width:400px">
      <button type="submit" class="btn btn-gold">Search</button>
      <?php if ($search): ?><a href="users.php" class="btn btn-outline">Clear</a><?php endif; ?>
    </form>
    <div class="dash-table-card">
      <table class="dash-table">
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th><th>Orders</th><th>Total Spent</th><th>Role</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><div style="display:flex;align-items:center;gap:10px"><div style="width:36px;height:36px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--dark)"><?= strtoupper(substr($u['name'],0,1)) ?></div><strong><?= sanitize($u['name']) ?></strong></div></td>
          <td><a href="mailto:<?= sanitize($u['email']) ?>" style="color:var(--gold-light)"><?= sanitize($u['email']) ?></a></td>
          <td><?= sanitize($u['phone']??'—') ?></td>
          <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
          <td><?= $u['order_count'] ?></td>
          <td style="color:var(--gold)"><?= formatRwf($u['total_spent']) ?></td>
          <td><span class="status-badge badge-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
          <td style="display:flex;gap:6px;flex-wrap:wrap;padding:12px 18px">
            <a href="orders.php?q=<?= urlencode($u['email']) ?>" class="btn btn-outline btn-sm">Orders</a>
            <form method="POST" style="display:inline">
              <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
              <input type="hidden" name="action" value="delete">
              <button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this client? This cannot be undone.">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
