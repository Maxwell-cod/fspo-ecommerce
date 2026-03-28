<?php
require_once '../includes/config.php';
requireLogin(); requireAdmin();
$pageTitle = 'Manage Categories';
$db = getDB();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $name   = trim($_POST['name']        ?? '');
    $desc   = trim($_POST['description'] ?? '');
    $image  = trim($_POST['image']       ?? '');
    $catSlug = slug($name);

    if ($action === 'add' && $name) {
        // Ensure unique slug
        $check = $db->prepare("SELECT id FROM categories WHERE slug = ?");
        $check->execute([$catSlug]);
        if ($check->fetch()) $catSlug = $catSlug . '-' . time();
        $db->prepare("INSERT INTO categories (name, slug, description, image) VALUES (?, ?, ?, ?)")
           ->execute([$name, $catSlug, $desc, $image]);
        setFlash('success', 'Category "' . $name . '" added!');
    }

    if ($action === 'edit') {
        $cid = (int)$_POST['cat_id'];
        $db->prepare("UPDATE categories SET name = ?, description = ?, image = ? WHERE id = ?")
           ->execute([$name, $desc, $image, $cid]);
        setFlash('success', 'Category updated!');
    }

    if ($action === 'delete') {
        $cid = (int)$_POST['cat_id'];
        // Check if has products
        $count = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $count->execute([$cid]);
        if ((int)$count->fetchColumn() > 0) {
            setFlash('error', 'Cannot delete — this category has products. Remove or reassign them first.');
        } else {
            $db->prepare("DELETE FROM categories WHERE id = ?")->execute([$cid]);
            setFlash('info', 'Category deleted.');
        }
    }

    redirect(SITE_URL . '/admin/categories.php');
}

// Editing?
$editId  = (int)($_GET['edit'] ?? 0);
$editCat = null;
if ($editId) {
    $s = $db->prepare("SELECT * FROM categories WHERE id = ?");
    $s->execute([$editId]);
    $editCat = $s->fetch();
}

$categories = $db->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.name")->fetchAll();

include '../includes/header.php';
?>
<div class="dashboard-layout">
  <aside class="sidebar">
    <div class="sidebar-user"><div class="avatar" style="background:var(--danger)">A</div><h4><?= sanitize($_SESSION['user_name']) ?></h4><small style="color:var(--gold)">Administrator</small></div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"><span class="nav-icon-dash">📊</span> Dashboard</a>
      <a href="orders.php"><span class="nav-icon-dash">📦</span> All Orders</a>
      <a href="products.php"><span class="nav-icon-dash">🛍</span> Products</a>
      <a href="categories.php" class="active"><span class="nav-icon-dash">📂</span> Categories</a>
      <a href="users.php"><span class="nav-icon-dash">👥</span> Clients</a>
      <a href="messages.php"><span class="nav-icon-dash">✉</span> Messages</a>
      <a href="settings.php"><span class="nav-icon-dash">⚙</span> Settings & API</a>
      <a href="../index.php"><span class="nav-icon-dash">🌐</span> View Website</a>
      <a href="../logout.php"><span class="nav-icon-dash">🚪</span> Logout</a>
    </nav>
  </aside>

  <div class="dash-content">
    <div class="dash-header">
      <h1>📂 Categories</h1>
      <span style="color:var(--text-muted)"><?= count($categories) ?> categories</span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 380px;gap:24px">

      <!-- CATEGORIES TABLE -->
      <div class="dash-table-card">
        <div class="dash-table-card-header"><h3>All Categories</h3></div>
        <table class="dash-table">
          <thead><tr><th>Image</th><th>Name</th><th>Slug</th><th>Products</th><th>Actions</th></tr></thead>
          <tbody>
          <?php foreach ($categories as $cat): ?>
          <tr>
            <td><img src="<?= sanitize($cat['image'] ?? '') ?>" style="width:48px;height:48px;object-fit:cover;border-radius:7px" onerror="this.src='https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=100&h=100&fit=crop'"></td>
            <td><strong><?= sanitize($cat['name']) ?></strong><?php if ($cat['description']): ?><br><small style="color:var(--text-muted)"><?= sanitize(substr($cat['description'],0,60)) ?>...</small><?php endif; ?></td>
            <td><code style="background:var(--dark3);padding:2px 7px;border-radius:4px;font-size:12px"><?= sanitize($cat['slug']) ?></code></td>
            <td><?= $cat['product_count'] ?></td>
            <td style="display:flex;gap:6px;padding:12px 18px">
              <a href="categories.php?edit=<?= $cat['id'] ?>" class="btn btn-gold btn-sm">Edit</a>
              <a href="../shop.php?cat=<?= $cat['slug'] ?>" class="btn btn-outline btn-sm" target="_blank">View</a>
              <form method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this category? Only works if it has no products.">Del</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- ADD / EDIT FORM -->
      <div class="admin-form-card" style="height:fit-content">
        <h3><?= $editCat ? 'Edit Category' : 'Add New Category' ?></h3>
        <form method="POST">
          <input type="hidden" name="action" value="<?= $editCat ? 'edit' : 'add' ?>">
          <?php if ($editCat): ?>
          <input type="hidden" name="cat_id" value="<?= $editCat['id'] ?>">
          <?php endif; ?>
          <div class="form-group">
            <label>Category Name *</label>
            <input type="text" name="name" class="form-control" value="<?= sanitize($editCat['name'] ?? '') ?>" required placeholder="e.g. Plumbing Supplies">
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Short description of this category..."><?= sanitize($editCat['description'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label>Image URL</label>
            <input type="url" name="image" class="form-control" value="<?= sanitize($editCat['image'] ?? '') ?>" placeholder="https://images.unsplash.com/...">
            <?php if (!empty($editCat['image'])): ?>
            <img src="<?= sanitize($editCat['image']) ?>" style="margin-top:10px;height:80px;border-radius:7px;object-fit:cover" onerror="this.style.display='none'">
            <?php endif; ?>
          </div>
          <div style="display:flex;gap:10px">
            <button type="submit" class="btn btn-gold"><?= $editCat ? '✓ Save Changes' : '+ Add Category' ?></button>
            <?php if ($editCat): ?><a href="categories.php" class="btn btn-outline">Cancel</a><?php endif; ?>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
