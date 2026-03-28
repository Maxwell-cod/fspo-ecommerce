<?php
require_once '../includes/config.php';
requireLogin();
$pageTitle = 'My Profile';
$db  = getDB();
$uid = $_SESSION['user_id'];

$user = $db->prepare("SELECT * FROM users WHERE id=?");
$user->execute([$uid]); $user = $user->fetch();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $name    = trim($_POST['name']    ?? '');
        $phone   = trim($_POST['phone']   ?? '');
        $address = trim($_POST['address'] ?? '');
        if (!$name) $errors[] = 'Name is required.';
        if (empty($errors)) {
            $db->prepare("UPDATE users SET name=?,phone=?,address=? WHERE id=?")->execute([$name,$phone,$address,$uid]);
            $_SESSION['user_name'] = $name;
            setFlash('success','Profile updated successfully!');
            redirect(SITE_URL.'/client/profile.php');
        }
    }

    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!password_verify($current, $user['password'])) $errors[] = 'Current password is incorrect.';
        if (strlen($new) < 6) $errors[] = 'New password must be at least 6 characters.';
        if ($new !== $confirm) $errors[] = 'New passwords do not match.';
        if (empty($errors)) {
            $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($new,PASSWORD_DEFAULT),$uid]);
            setFlash('success','Password changed successfully!');
            redirect(SITE_URL.'/client/profile.php');
        }
    }
}

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
      <a href="dashboard.php"><span class="nav-icon-dash">📊</span> Dashboard</a>
      <a href="orders.php"><span class="nav-icon-dash">📦</span> My Orders</a>
      <a href="../wishlist.php"><span class="nav-icon-dash">🤍</span> Wishlist</a>
      <a href="profile.php" class="active"><span class="nav-icon-dash">👤</span> Profile</a>
      <a href="../shop.php"><span class="nav-icon-dash">🛍</span> Shop</a>
      <a href="../logout.php"><span class="nav-icon-dash">🚪</span> Logout</a>
    </nav>
  </aside>

  <div class="dash-content">
    <div class="dash-header"><h1>My Profile</h1></div>

    <?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>',array_map('sanitize',$errors)) ?></div><?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
      <div class="admin-form-card">
        <h3>Personal Information</h3>
        <form method="POST">
          <input type="hidden" name="action" value="update_profile">
          <div class="form-group"><label>Full Name</label><input type="text" name="name" class="form-control" value="<?= sanitize($user['name']) ?>" required></div>
          <div class="form-group"><label>Email (cannot change)</label><input type="email" class="form-control" value="<?= sanitize($user['email']) ?>" readonly style="opacity:.6"></div>
          <div class="form-group"><label>Phone Number</label><input type="tel" name="phone" class="form-control" value="<?= sanitize($user['phone']??'') ?>"></div>
          <div class="form-group"><label>Delivery Address</label><textarea name="address" class="form-control" rows="3"><?= sanitize($user['address']??'') ?></textarea></div>
          <button type="submit" class="btn btn-gold btn-block">Save Changes</button>
        </form>
      </div>

      <div class="admin-form-card">
        <h3>Change Password</h3>
        <form method="POST">
          <input type="hidden" name="action" value="change_password">
          <div class="form-group"><label>Current Password</label><input type="password" name="current_password" class="form-control" required></div>
          <div class="form-group"><label>New Password</label><input type="password" name="new_password" class="form-control" required></div>
          <div class="form-group"><label>Confirm New Password</label><input type="password" name="confirm_password" class="form-control" required></div>
          <button type="submit" class="btn btn-primary btn-block">Change Password</button>
        </form>

        <div style="margin-top:24px;padding-top:24px;border-top:1px solid var(--border)">
          <h3 style="margin-bottom:16px">Account Info</h3>
          <div style="font-size:13.5px;color:var(--text-muted)">
            <div style="margin-bottom:8px">Member since: <strong style="color:var(--white)"><?= date('d M Y',strtotime($user['created_at'])) ?></strong></div>
            <div>Account type: <span class="status-badge badge-client">Client</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
