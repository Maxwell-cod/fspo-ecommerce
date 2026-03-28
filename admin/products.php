<?php
require_once '../includes/config.php';
requireLogin(); requireAdmin();
$pageTitle = 'Manage Products';
$db = getDB();

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

// Handle file uploads
function handleImageUpload() {
    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] === UPLOAD_ERR_NO_FILE) {
        return trim($_POST['image'] ?? ''); // Return URL if no file uploaded
    }
    
    $file = $_FILES['product_image'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    // Validate
    if ($file['error'] !== UPLOAD_ERR_OK) {
        setFlash('danger', 'File upload error. Please try again.');
        return '';
    }
    
    if ($file['size'] > $maxSize) {
        setFlash('danger', 'Image must be smaller than 5MB.');
        return '';
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        setFlash('danger', 'Only JPG, PNG, GIF, and WebP images are allowed.');
        return '';
    }
    
    // Save file
    $uploadDir = __DIR__ . '/../uploads/products/';
    $filename = 'product_' . time() . '_' . uniqid() . '.' . $ext;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        chmod($filepath, 0644);
        return '/uploads/products/' . $filename;
    }
    
    setFlash('danger', 'Failed to save image. Check folder permissions.');
    return '';
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']        ?? '');
    $catId       = (int)($_POST['category_id'] ?? 0);
    $price       = (float)($_POST['price']    ?? 0);
    $stock       = (int)($_POST['stock']      ?? 0);
    $description = trim($_POST['description'] ?? '');
    $image       = handleImageUpload();
    $featured    = isset($_POST['featured']) ? 1 : 0;
    $status      = $_POST['status'] ?? 'active';
    $productSlug = slug($name . '-' . time());

    if ($action === 'add') {
        if (!$name || !$catId || !$price) {
            setFlash('danger', 'Please fill in all required fields.');
        } else {
            $db->prepare("INSERT INTO products (category_id,name,slug,description,price,stock,image,featured,status) VALUES (?,?,?,?,?,?,?,?,?)")
               ->execute([$catId,$name,$productSlug,$description,$price,$stock,$image,$featured,$status]);
            setFlash('success','Product "' . $name . '" added successfully!');
            redirect(SITE_URL.'/admin/products.php');
        }
    }
    if ($action === 'edit') {
        $pid = (int)$_POST['product_id'];
        // Keep old image if no new file uploaded and no URL provided
        if (empty($image)) {
            $oldProduct = $db->prepare("SELECT image FROM products WHERE id=?")->execute([$pid])->fetch();
            $image = $oldProduct['image'] ?? '';
        }
        $db->prepare("UPDATE products SET category_id=?,name=?,description=?,price=?,stock=?,image=?,featured=?,status=? WHERE id=?")
           ->execute([$catId,$name,$description,$price,$stock,$image,$featured,$status,$pid]);
        setFlash('success','Product updated!');
        redirect(SITE_URL.'/admin/products.php');
    }
    if ($action === 'deactivate') {
        $pid = (int)$_POST['product_id'];
        $db->prepare("UPDATE products SET status='inactive' WHERE id=?")->execute([$pid]);
        setFlash('info','Product deactivated.');
        redirect(SITE_URL.'/admin/products.php');
    }
    
    if ($action === 'delete') {
        $pid = (int)$_POST['product_id'];
        
        // Get product details to clean up image
        $stmt = $db->prepare("SELECT image FROM products WHERE id=?");
        $stmt->execute([$pid]);
        $product = $stmt->fetch();
        
        if ($product && $product['image']) {
            // Delete image file if it's a local upload
            if (strpos($product['image'], '/uploads/') !== false) {
                $imagePath = __DIR__ . '/../' . $product['image'];
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
        }
        
        // Remove from cart items
        $db->prepare("DELETE FROM cart WHERE product_id=?")->execute([$pid]);
        
        // Remove from wishlist
        $db->prepare("DELETE FROM wishlist WHERE product_id=?")->execute([$pid]);
        
        // Remove from order items
        $db->prepare("DELETE FROM order_items WHERE product_id=?")->execute([$pid]);
        
        // Finally delete the product
        $db->prepare("DELETE FROM products WHERE id=?")->execute([$pid]);
        
        setFlash('success','Product completely deleted from system.');
        redirect(SITE_URL.'/admin/products.php');
    }
}

$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Edit - fetch existing product
$product = null;
if ($editId && $action === 'edit') {
    $s = $db->prepare("SELECT * FROM products WHERE id=?");
    $s->execute([$editId]); $product = $s->fetch();
}

// Products list with search
$search = trim($_GET['q'] ?? '');
$catFilter = (int)($_GET['cat'] ?? 0);
$where = ['1=1']; $params = [];
if ($search)    { $where[] = 'p.name LIKE ?'; $params[] = "%$search%"; }
if ($catFilter) { $where[] = 'p.category_id=?'; $params[] = $catFilter; }
$products = $db->prepare("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE ".implode(' AND ',$where)." ORDER BY p.created_at DESC");
$products->execute($params); $products = $products->fetchAll();

include '../includes/header.php';
?>
<div class="dashboard-layout">
  <aside class="sidebar">
    <div class="sidebar-user"><div class="avatar" style="background:var(--danger)">A</div><h4><?= sanitize($_SESSION['user_name']) ?></h4><small style="color:var(--gold)">Administrator</small></div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"><span class="nav-icon-dash">📊</span> Dashboard</a>
      <a href="orders.php"><span class="nav-icon-dash">📦</span> All Orders</a>
      <a href="products.php" class="active"><span class="nav-icon-dash">🛍</span> Products</a>
      <a href="users.php"><span class="nav-icon-dash">👥</span> Clients</a>
      <a href="messages.php"><span class="nav-icon-dash">✉</span> Messages</a>
      <a href="settings.php"><span class="nav-icon-dash">⚙</span> Settings & API</a>
      <a href="../index.php"><span class="nav-icon-dash">🌐</span> View Website</a>
      <a href="../logout.php"><span class="nav-icon-dash">🚪</span> Logout</a>
    </nav>
  </aside>

  <div class="dash-content">
    <?php if ($action === 'add' || ($action === 'edit' && $product)): ?>
    <!-- ADD / EDIT FORM -->
    <div class="dash-header">
      <div><a href="products.php" style="color:var(--text-muted);font-size:13px">← Back to Products</a><h1 style="margin-top:6px"><?= $action==='add' ? 'Add New Product' : 'Edit: '.sanitize($product['name']) ?></h1></div>
    </div>
    <div class="admin-form-card">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?= $action ?>">
        <?php if ($action==='edit'): ?><input type="hidden" name="product_id" value="<?= $product['id'] ?>"><?php endif; ?>
        <div class="form-grid">
          <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="name" class="form-control" value="<?= sanitize($product['name']??'') ?>" required>
          </div>
          <div class="form-group">
            <label>Category *</label>
            <select name="category_id" class="form-control" required>
              <option value="">-- Select Category --</option>
              <?php foreach ($categories as $c): ?>
              <option value="<?= $c['id'] ?>" <?= ($product['category_id']??0)==$c['id']?'selected':'' ?>><?= sanitize($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Price (Rwf) *</label>
            <input type="number" name="price" class="form-control" value="<?= $product['price']??'' ?>" min="0" step="100" required>
          </div>
          <div class="form-group">
            <label>Stock Quantity *</label>
            <input type="number" name="stock" class="form-control" value="<?= $product['stock']??0 ?>" min="0" required>
          </div>
        </div>

        <!-- IMAGE UPLOAD OPTIONS -->
        <div style="background:var(--dark3);padding:20px;border-radius:8px;margin-bottom:20px">
          <h3 style="margin-top:0;color:var(--gold);font-size:16px">📸 Product Image</h3>
          
          <!-- Upload from Computer -->
          <div class="form-group" style="margin-bottom:16px">
            <label style="display:block;margin-bottom:8px;font-weight:600">Upload from Computer</label>
            <div style="position:relative;border:2px dashed var(--gold);border-radius:8px;padding:20px;text-align:center;cursor:pointer;transition:all 0.2s" id="uploadZone">
              <input type="file" name="product_image" id="imageInput" accept="image/*" style="display:none">
              <div style="color:var(--text-muted)">
                <span style="font-size:24px;display:block;margin-bottom:8px">📁</span>
                <strong style="display:block;margin-bottom:4px;color:var(--gold)">Click or drag image here</strong>
                <small>JPG, PNG, GIF, WebP (Max 5MB)</small>
              </div>
            </div>
          </div>

          <!-- OR divider -->
          <div style="text-align:center;margin:20px 0;color:var(--text-muted)"><small>— OR —</small></div>

          <!-- URL Input -->
          <div class="form-group">
            <label style="display:block;margin-bottom:8px;font-weight:600">Paste Image URL</label>
            <input type="url" name="image" class="form-control" value="<?= sanitize($product['image']??'') ?>" placeholder="https://images.unsplash.com/...">
            <small style="color:var(--text-muted);font-size:12px">Paste a direct image URL (Unsplash, your server, etc.)</small>
          </div>
        </div>

        <!-- IMAGE PREVIEW -->
        <?php if (!empty($product['image'])): ?>
        <div style="margin-bottom:16px;padding:16px;background:var(--dark3);border-radius:8px">
          <small style="color:var(--text-muted)">Current Image:</small>
          <img src="<?= sanitize($product['image']) ?>" style="height:150px;border-radius:8px;object-fit:cover;margin-top:8px;display:block" onerror="this.style.display='none'" id="currentImage">
        </div>
        <?php endif; ?>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" class="form-control" rows="4"><?= sanitize($product['description']??'') ?></textarea>
        </div>
        <div style="display:flex;gap:24px;margin-bottom:18px">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="featured" value="1" <?= ($product['featured']??0)?'checked':'' ?> style="accent-color:var(--gold);width:16px;height:16px">
            <span style="font-size:14px">Featured on homepage</span>
          </label>
          <div class="form-group" style="margin:0">
            <select name="status" class="form-control" style="padding:8px 12px">
              <option value="active"   <?= ($product['status']??'active')==='active'?'selected':'' ?>>Active</option>
              <option value="inactive" <?= ($product['status']??'')==='inactive'?'selected':'' ?>>Inactive</option>
            </select>
          </div>
        </div>
        <div style="display:flex;gap:12px">
          <button type="submit" class="btn btn-gold"><?= $action==='add' ? '+ Add Product' : '✓ Save Changes' ?></button>
          <a href="products.php" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>

    <script>
      const uploadZone = document.getElementById('uploadZone');
      const imageInput = document.getElementById('imageInput');
      const currentImage = document.getElementById('currentImage');

      // Click to upload
      uploadZone.addEventListener('click', () => imageInput.click());

      // Drag and drop
      uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.style.borderColor = 'var(--gold-light)';
        uploadZone.style.background = 'rgba(212, 175, 55, 0.1)';
      });

      uploadZone.addEventListener('dragleave', () => {
        uploadZone.style.borderColor = 'var(--gold)';
        uploadZone.style.background = '';
      });

      uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.style.borderColor = 'var(--gold)';
        uploadZone.style.background = '';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
          imageInput.files = files;
          updatePreview();
        }
      });

      // Show preview on file select
      imageInput.addEventListener('change', updatePreview);

      function updatePreview() {
        if (imageInput.files && imageInput.files[0]) {
          const file = imageInput.files[0];
          const reader = new FileReader();
          reader.onload = (e) => {
            if (currentImage) {
              currentImage.src = e.target.result;
              currentImage.style.display = 'block';
            } else {
              const preview = document.createElement('img');
              preview.src = e.target.result;
              preview.style.height = '150px';
              preview.style.borderRadius = '8px';
              preview.style.objectFit = 'cover';
              preview.style.marginTop = '8px';
              preview.style.display = 'block';
              uploadZone.parentElement.insertBefore(preview, uploadZone.nextSibling);
            }
          };
          reader.readAsDataURL(file);
        }
      }
    </script>

    <?php else: ?>
    <!-- PRODUCTS LIST -->
    <div class="dash-header">
      <h1>All Products</h1>
      <a href="products.php?action=add" class="btn btn-gold btn-sm">+ Add Product</a>
    </div>
    <form method="GET" style="display:flex;gap:10px;margin-bottom:20px">
      <input type="text" name="q" class="form-control" placeholder="Search products..." value="<?= sanitize($search) ?>" style="flex:1">
      <select name="cat" class="form-control" style="width:180px">
        <option value="">All Categories</option>
        <?php foreach ($categories as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $catFilter===$c['id']?'selected':'' ?>><?= sanitize($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-gold">Filter</button>
      <a href="products.php" class="btn btn-outline">Clear</a>
    </form>
    <div class="dash-table-card">
      <table class="dash-table">
        <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price (Rwf)</th><th>Stock</th><th>Featured</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td><img src="<?= sanitize($p['image']) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:7px" onerror="this.src='https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=100&h=100&fit=crop'"></td>
          <td><strong><?= sanitize($p['name']) ?></strong></td>
          <td><?= sanitize($p['cat_name']??'') ?></td>
          <td><strong style="color:var(--gold)"><?= number_format($p['price'],0,'.',',') ?></strong></td>
          <td><span style="color:<?= $p['stock']<5?'var(--warning)':'inherit' ?>"><?= $p['stock'] ?></span></td>
          <td><?= $p['featured'] ? '⭐ Yes' : 'No' ?></td>
          <td><span class="status-badge badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
          <td style="display:flex;gap:6px;padding:14px 18px">
            <a href="../product.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm" target="_blank">View</a>
            <a href="products.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-gold btn-sm">Edit</a>
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="deactivate">
              <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
              <button type="submit" class="btn btn-warning btn-sm" data-confirm="Deactivate this product?">Deactivate</button>
            </form>
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm" data-confirm="⚠️ Permanently delete this product? This action cannot be undone. All cart, wishlist, and order records will be removed.">🗑️ Delete</button>
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
