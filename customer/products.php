<?php
require "../config/db.php";
require "../includes/auth.php";
require_role("customer");

$hasCategory = false;
try {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM products LIKE 'category'");
    $stmt->execute();
    $hasCategory = (bool)$stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$category = $_GET['category'] ?? 'All';
$search = trim($_GET['search'] ?? '');
$where = "WHERE status = 'available' AND stock > 0";
$params = [];
if ($hasCategory && $category !== 'All') {
    $where .= " AND category = ?";
    $params[] = $category;
}
if ($search !== '') {
    $where .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$stmt = $pdo->prepare("SELECT * FROM products $where ORDER BY id DESC");
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$categories = $hasCategory ? $pdo->query("SELECT DISTINCT category FROM products WHERE status='available' ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN) : [];

include "../includes/header.php";
?>

<div class="hero-banner split-hero catalog-hero">
    <div>
        <span class="kicker light">Food Ordering</span>
        <h2>Fresh picks for you</h2>
        <p>Browse meals, drinks, sides, pasta, and desserts. Add your favorites to cart in seconds.</p>
    </div>
    <div class="hero-card-note">Fast checkout<br><strong><?php echo count($products); ?></strong> products shown</div>
</div>

<form class="toolbar" method="GET">
    <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
    <?php if ($hasCategory): ?>
    <select name="category">
        <option>All</option>
        <?php foreach ($categories as $cat): ?><option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option><?php endforeach; ?>
    </select>
    <?php endif; ?>
    <button class="btn" type="submit">Filter</button>
</form>

<?php if (!$products): ?>
    <div class="empty-state"><h3>No products found</h3><p>Try another keyword or category.</p></div>
<?php endif; ?>

<div class="grid product-grid">
<?php foreach ($products as $product): ?>
    <div class="card product-card shop-card">
        <div class="image-wrap">
            <img class="product-image" src="../uploads/<?php echo htmlspecialchars($product['image'] ?: 'default-food.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='../uploads/default-food.png'">
            <span class="category-pill floating"><?php echo htmlspecialchars($product['category'] ?? 'Product'); ?></span>
        </div>
        <div class="product-content">
            <h3><?php echo htmlspecialchars($product["name"]); ?></h3>
            <p><?php echo htmlspecialchars($product["description"]); ?></p>
            <div class="product-meta">
                <span class="price">₱<?php echo number_format($product["price"], 2); ?></span>
                <span class="stock-badge">Stock: <?php echo $product['stock']; ?></span>
            </div>
            <form method="POST" action="cart.php" class="cart-form">
                <input type="hidden" name="product_id" value="<?php echo $product["id"]; ?>">
                <div class="two-fields compact">
                    <div><label>Quantity</label><input type="number" name="quantity" value="1" min="1" max="<?php echo $product["stock"]; ?>"></div>
                    <div><label>&nbsp;</label><button class="btn full" type="submit" name="add_to_cart">Add to Cart</button></div>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php include "../includes/footer.php"; ?>
