<?php
require "../config/db.php";
require "../includes/auth.php";
require_role("admin");

function has_column($pdo, $column) {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM products LIKE ?");
    $stmt->execute([$column]);
    return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
}
$hasCategory = has_column($pdo, 'category');

$notice = "";
if (isset($_POST["save_product"])) {
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $category = trim($_POST["category"] ?? "Meals");
    $price = (float)$_POST["price"];
    $stock = (int)$_POST["stock"];
    $status = $_POST["status"];
    $imageName = $_POST['old_image'] ?? null;

    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $imageName = time() . '_' . rand(1000,9999) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $imageName);
        }
    }

    if (!empty($_POST["id"])) {
        if ($hasCategory) {
            $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, category=?, price=?, image=?, stock=?, status=? WHERE id=?");
            $stmt->execute([$name, $description, $category, $price, $imageName, $stock, $status, $_POST["id"]]);
        } else {
            $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, image=?, stock=?, status=? WHERE id=?");
            $stmt->execute([$name, $description, $price, $imageName, $stock, $status, $_POST["id"]]);
        }
    } else {
        if ($hasCategory) {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, category, price, image, stock, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $category, $price, $imageName, $stock, $status]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, stock, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $imageName, $stock, $status]);
        }
    }
    header("Location: products.php?saved=1");
    exit;
}

if (isset($_GET["delete"])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET["delete"]]);
    header("Location: products.php?deleted=1");
    exit;
}

$edit = null;
if (isset($_GET["edit"])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET["edit"]]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$totalProducts = count($products);
$availableProducts = count(array_filter($products, fn($p) => $p['status'] === 'available'));
$lowStock = count(array_filter($products, fn($p) => (int)$p['stock'] <= 10));
include "../includes/header.php";
?>

<div class="hero-banner split-hero">
    <div>
        <span class="kicker light">Admin Catalog</span>
        <h2>Product Management</h2>
        <p>Add products, upload pictures, update stocks, and control what customers can order.</p>
    </div>
    <div class="mini-stats">
        <span><?php echo $totalProducts; ?><small>Total</small></span>
        <span><?php echo $availableProducts; ?><small>Available</small></span>
        <span><?php echo $lowStock; ?><small>Low Stock</small></span>
    </div>
</div>

<?php if (isset($_GET['saved'])): ?><div class="alert success-alert">Product saved successfully.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert">Product deleted.</div><?php endif; ?>

<div class="admin-layout">
<form method="POST" enctype="multipart/form-data" class="card form-card sticky-card">
    <h3><?php echo $edit ? "Edit Product" : "Add New Product"; ?></h3>
    <input type="hidden" name="id" value="<?php echo $edit["id"] ?? ""; ?>">
    <input type="hidden" name="old_image" value="<?php echo $edit['image'] ?? ''; ?>">

    <label>Product Name</label>
    <input type="text" name="name" required placeholder="Example: Chicken Burger" value="<?php echo htmlspecialchars($edit["name"] ?? ""); ?>">

    <label>Category</label>
    <input type="text" name="category" placeholder="Meals, Drinks, Desserts" value="<?php echo htmlspecialchars($edit["category"] ?? "Meals"); ?>">

    <label>Description</label>
    <textarea name="description" rows="4" placeholder="Short product description"><?php echo htmlspecialchars($edit["description"] ?? ""); ?></textarea>

    <div class="two-fields">
        <div><label>Price</label><input type="number" step="0.01" min="0" name="price" required value="<?php echo htmlspecialchars($edit["price"] ?? ""); ?>"></div>
        <div><label>Stock</label><input type="number" min="0" name="stock" required value="<?php echo htmlspecialchars($edit["stock"] ?? ""); ?>"></div>
    </div>

    <label>Product Image</label>
    <input type="file" name="image" accept="image/*" <?php echo $edit ? '' : 'required'; ?>>
    <small class="field-help"><?php echo $edit ? 'Upload a new picture only if you want to replace the current one.' : 'Required for new products so every customer item has a picture.'; ?></small>
    <img id="imagePreview" class="preview-thumb <?php echo empty($edit['image']) ? 'is-hidden' : ''; ?>" src="../uploads/<?php echo htmlspecialchars($edit['image'] ?? 'default-food.png'); ?>" alt="Product image preview">

    <label>Status</label>
    <select name="status">
        <option value="available" <?php echo (($edit["status"] ?? "") === "available") ? "selected" : ""; ?>>Available</option>
        <option value="unavailable" <?php echo (($edit["status"] ?? "") === "unavailable") ? "selected" : ""; ?>>Unavailable</option>
    </select>

    <button class="btn full" name="save_product" type="submit"><?php echo $edit ? "Update Product" : "Add Product"; ?></button>
    <?php if ($edit): ?><a class="btn ghost full" href="products.php">Cancel Edit</a><?php endif; ?>
</form>

<div>
<div class="section-title"><h3>Current Products</h3><span><?php echo $totalProducts; ?> items</span></div>
<div class="grid product-grid">
<?php foreach ($products as $product): ?>
    <div class="card product-card">
        <div class="image-wrap">
            <img class="product-image" src="../uploads/<?php echo htmlspecialchars($product['image'] ?: 'default-food.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='../uploads/default-food.png'">
            <span class="status <?php echo $product['status'] === 'available' ? 'status-ready' : 'status-cancelled'; ?>"><?php echo ucfirst($product['status']); ?></span>
        </div>
        <div class="product-content">
            <span class="category-pill"><?php echo htmlspecialchars($product['category'] ?? 'Product'); ?></span>
            <h3><?php echo htmlspecialchars($product["name"]); ?></h3>
            <p><?php echo htmlspecialchars($product["description"]); ?></p>
            <div class="product-meta">
                <span class="price">₱<?php echo number_format($product["price"], 2); ?></span>
                <span class="stock-badge <?php echo (int)$product['stock'] <= 10 ? 'low' : ''; ?>">Stock: <?php echo $product['stock']; ?></span>
            </div>
            <div class="action-row">
                <a class="btn warning" href="products.php?edit=<?php echo $product["id"]; ?>">Edit</a>
                <a class="btn danger" onclick="return confirm('Delete this product?')" href="products.php?delete=<?php echo $product["id"]; ?>">Delete</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
</div>
</div>

<?php include "../includes/footer.php"; ?>
