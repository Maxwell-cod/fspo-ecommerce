<?php
require_once '../includes/config.php';
requireLogin(); requireAdmin();
$pageTitle = 'Contact Messages';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mid = (int)$_POST['msg_id'];
    $action = $_POST['action'] ?? '';
    if ($action === 'mark_read') $db->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([$mid]);
    if ($action === 'delete')    $db->prepare("DELETE FROM contact_messages WHERE id=?")->execute([$mid]);
    redirect(SITE_URL.'/admin/messages.php');
}

$viewId  = (int)($_GET['view'] ?? 0);
$message = null;
if ($viewId) {
    $s = $db->prepare("SELECT * FROM contact_messages WHERE id=?");
    $s->execute([$viewId]); $message = $s->fetch();
    if ($message && !$message['is_read']) $db->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([$viewId]);
}

$messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
$unread   = count(array_filter($messages, fn($m) => !$m['is_read']));

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
      <a href="users.php"><span class="nav-icon-dash">👥</span> Clients</a>
      <a href="messages.php" class="active"><span class="nav-icon-dash">✉</span> Messages <?php if($unread>0): ?><span style="background:var(--danger);color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px"><?= $unread ?></span><?php endif; ?></a>
      <a href="settings.php"><span class="nav-icon-dash">⚙</span> Settings & API</a>
      <a href="../index.php"><span class="nav-icon-dash">🌐</span> View Website</a>
      <a href="../logout.php"><span class="nav-icon-dash">🚪</span> Logout</a>
    </nav>
  </aside>
  <div class="dash-content">
    <?php if ($message): ?>
    <div class="dash-header"><div><a href="messages.php" style="color:var(--text-muted);font-size:13px">← Back to Messages</a><h1 style="margin-top:6px">Message from <?= sanitize($message['name']) ?></h1></div></div>
    <div class="admin-form-card" style="max-width:700px">
      <div style="font-size:13.5px;margin-bottom:20px">
        <div style="padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">From:</span> <strong><?= sanitize($message['name']) ?></strong></div>
        <div style="padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Email:</span> <a href="mailto:<?= sanitize($message['email']) ?>" style="color:var(--gold-light)"><?= sanitize($message['email']) ?></a></div>
        <?php if ($message['phone']): ?><div style="padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Phone:</span> <?= sanitize($message['phone']) ?></div><?php endif; ?>
        <?php if ($message['subject']): ?><div style="padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Subject:</span> <?= sanitize($message['subject']) ?></div><?php endif; ?>
        <div style="padding:8px 0;border-bottom:1px solid var(--border)"><span style="color:var(--text-muted)">Date:</span> <?= date('d M Y, H:i', strtotime($message['created_at'])) ?></div>
        <div style="padding:16px 0"><span style="color:var(--text-muted)">Message:</span><div style="margin-top:10px;background:var(--dark3);border-radius:8px;padding:16px;line-height:1.7;color:rgba(255,255,255,0.85)"><?= nl2br(sanitize($message['message'])) ?></div></div>
      </div>
      <div style="display:flex;gap:10px">
        <a href="mailto:<?= sanitize($message['email']) ?>?subject=Re: <?= urlencode($message['subject']??'Your Message') ?>" class="btn btn-gold">✉ Reply by Email</a>
        <form method="POST" style="display:inline">
          <input type="hidden" name="msg_id" value="<?= $message['id'] ?>">
          <input type="hidden" name="action" value="delete">
          <button type="submit" class="btn btn-danger" data-confirm="Delete this message?">Delete</button>
        </form>
      </div>
    </div>
    <?php else: ?>
    <div class="dash-header"><h1>Contact Messages</h1><span style="color:var(--text-muted)"><?= $unread ?> unread</span></div>
    <div class="dash-table-card">
      <table class="dash-table">
        <thead><tr><th></th><th>Name</th><th>Email</th><th>Subject</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($messages as $m): ?>
        <tr style="<?= !$m['is_read'] ? 'background:rgba(201,168,76,0.05)' : '' ?>">
          <td><?= !$m['is_read'] ? '<span style="color:var(--gold);font-size:18px">●</span>' : '' ?></td>
          <td><strong><?= sanitize($m['name']) ?></strong></td>
          <td><?= sanitize($m['email']) ?></td>
          <td><?= sanitize($m['subject']??'—') ?></td>
          <td><?= date('d M Y', strtotime($m['created_at'])) ?></td>
          <td style="display:flex;gap:6px;padding:12px 18px">
            <a href="messages.php?view=<?= $m['id'] ?>" class="btn btn-gold btn-sm">Read</a>
            <form method="POST">
              <input type="hidden" name="msg_id" value="<?= $m['id'] ?>">
              <input type="hidden" name="action" value="delete">
              <button class="btn btn-danger btn-sm" data-confirm="Delete?">Del</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
