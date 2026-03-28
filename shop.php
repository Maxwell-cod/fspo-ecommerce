<?php
require_once 'includes/config.php';
$pageTitle = 'Shop';
$db = getDB();

// Filters
$catSlug  = $_GET['cat']  ?? '';
$search   = trim($_GET['q']    ?? '');
$sort     = $_GET['sort'] ?? 'newest';
$minPrice = (int)($_GET['min'] ?? 0);
$maxPrice = (int)($_GET['max'] ?? 999999);
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 12;

// Build query
$where = ["p.status = 'active'"];
$params = [];

if ($catSlug) {
    $where[] = "c.slug = ?";
    $params[] = $catSlug;
}
if ($search) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($minPrice > 0) { $where[] = "p.price >= ?"; $params[] = $minPrice; }
if ($maxPrice < 999999) { $where[] = "p.price <= ?"; $params[] = $maxPrice; }

$whereSQL = implode(' AND ', $where);

$orderSQL = match($sort) {
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name'       => 'p.name ASC',
    default      => 'p.created_at DESC',
};

// Count total
$countStmt = $db->prepare("SELECT COUNT(*) FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE $whereSQL");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = ceil($total / $perPage);
$offset = ($page - 1) * $perPage;

// Fetch products
$stmt = $db->prepare("SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE $whereSQL ORDER BY $orderSQL LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$products = $stmt->fetchAll();

// Categories for sidebar
$categories = $db->query("SELECT c.*, COUNT(p.id) as cnt FROM categories c LEFT JOIN products p ON c.id = p.category_id AND p.status='active' GROUP BY c.id ORDER BY c.name")->fetchAll();

// Current category name
$currentCat = '';
if ($catSlug) {
    foreach ($categories as $c) if ($c['slug'] === $catSlug) { $currentCat = $c['name']; break; }
}

include 'includes/header.php';
?>

<div class="page-hero">
    <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> Shop <?= $currentCat ? '<span>/</span> ' . sanitize($currentCat) : '' ?></div>
    <h1><?= $currentCat ?: ($search ? 'Search: ' . sanitize($search) : 'All Products') ?></h1>
    <p><?= $total ?> product<?= $total !== 1 ? 's' : '' ?> found</p>
</div>

<div class="shop-layout">
    <!-- SIDEBAR -->
    <aside class="filter-sidebar">
        <h3>Categories</h3>
        <ul>
            <li><a href="shop.php" class="<?= !$catSlug ? 'active' : '' ?>">All Products <span><?= array_sum(array_column($categories,'cnt')) ?></span></a></li>
            <?php foreach ($categories as $cat): ?>
            <li><a href="shop.php?cat=<?= $cat['slug'] ?>" class="<?= $catSlug===$cat['slug'] ? 'active' : '' ?>"><?= sanitize($cat['name']) ?> <span><?= $cat['cnt'] ?></span></a></li>
            <?php endforeach; ?>
        </ul>

        <h3>Price Range (Rwf)</h3>
        <form method="GET" action="shop.php">
            <?php if ($catSlug): ?><input type="hidden" name="cat" value="<?= sanitize($catSlug) ?>"><?php endif; ?>
            <?php if ($search): ?><input type="hidden" name="q" value="<?= sanitize($search) ?>"><?php endif; ?>
            <div class="price-range" style="margin-bottom:12px">
                <input type="number" class="form-control" name="min" placeholder="Min" value="<?= $minPrice ?: '' ?>" min="0">
                <span style="color:var(--text-muted)">–</span>
                <input type="number" class="form-control" name="max" placeholder="Max" value="<?= $maxPrice < 999999 ? $maxPrice : '' ?>" min="0">
            </div>
            <button type="submit" class="btn btn-gold btn-sm btn-block">Apply Filter</button>
        </form>

        <?php if ($catSlug || $search || $minPrice || $maxPrice < 999999): ?>
        <a href="shop.php" class="btn btn-outline btn-sm btn-block" style="margin-top:10px">Clear Filters</a>
        <?php endif; ?>
    </aside>

    <!-- MAIN -->
    <div>
        <div class="shop-main-header">
            <p><?= $total ?> product<?= $total !== 1 ? 's' : '' ?></p>
            <form method="GET" action="shop.php" style="display:flex;gap:8px;align-items:center">
                <?php if ($catSlug): ?><input type="hidden" name="cat" value="<?= sanitize($catSlug) ?>"><?php endif; ?>
                <?php if ($search): ?><input type="hidden" name="q" value="<?= sanitize($search) ?>"><?php endif; ?>
                <select name="sort" class="sort-select" onchange="this.form.submit()">
                    <option value="newest" <?= $sort==='newest'?'selected':'' ?>>Newest First</option>
                    <option value="price_asc" <?= $sort==='price_asc'?'selected':'' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Price: High to Low</option>
                    <option value="name" <?= $sort==='name'?'selected':'' ?>>Name A–Z</option>
                </select>
            </form>
        </div>

        <?php if (empty($products)): ?>
        <div class="empty-state">
            <div class="icon">🔍</div>
            <h3>No products found</h3>
            <p>Try adjusting your search or filters.</p>
            <a href="shop.php" class="btn btn-gold">Browse All Products</a>
        </div>
        <?php else: ?>

        <div class="products-grid">
            <?php foreach ($products as $p): ?>
            <div class="product-card">
                <a href="product.php?id=<?= $p['id'] ?>">
                    <div class="card-img">
                        <img src="<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400&h=300&fit=crop'">
                    </div>
                </a>
                <div class="card-body">
                    <div class="card-category"><?= sanitize($p['cat_name'] ?? '') ?></div>
                    <a href="product.php?id=<?= $p['id'] ?>"><div class="card-title"><?= sanitize($p['name']) ?></div></a>
                    <div class="card-price"><?= formatRwf($p['price']) ?></div>
                    <div class="card-stock <?= $p['stock'] < 5 ? ($p['stock'] == 0 ? 'out' : 'low') : '' ?>">
                        <?= $p['stock'] == 0 ? '❌ Out of stock' : ($p['stock'] < 5 ? '⚠ Only ' . $p['stock'] . ' left' : '✓ In stock') ?>
                    </div>
                    <div class="card-actions">
                        <?php if ($p['stock'] > 0): ?>
                        <form method="POST" action="cart.php" style="flex:1">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-gold btn-sm" style="width:100%">Add to Cart</button>
                        </form>
                        <?php else: ?>
                        <button class="btn btn-outline btn-sm" disabled style="flex:1;opacity:.5">Out of Stock</button>
                        <?php endif; ?>
                        <form method="POST" action="wishlist.php">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <input type="hidden" name="redirect" value="<?= sanitize($_SERVER['REQUEST_URI']) ?>">
                            <button type="submit" class="wishlist-btn" title="Add to Wishlist">🤍</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- PAGINATION -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php
            $baseUrl = '?';
            $qp = array_filter(['cat'=>$catSlug,'q'=>$search,'sort'=>$sort,'min'=>$minPrice?:null,'max'=>($maxPrice<999999?$maxPrice:null)]);
            $baseUrl .= http_build_query($qp) . '&page=';
            ?>
            <?php if ($page > 1): ?><a href="<?= $baseUrl.($page-1) ?>">‹</a><?php endif; ?>
            <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="current"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= $baseUrl.$i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?><a href="<?= $baseUrl.($page+1) ?>">›</a><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
