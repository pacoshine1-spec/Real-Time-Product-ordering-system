<?php
require "../config/db.php";
require "../includes/auth.php";
require_role("customer");

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

if (isset($_POST["add_to_cart"])) {
    $product_id = (int)$_POST["product_id"];
    $quantity = max(1, (int)$_POST["quantity"]);

    if (isset($_SESSION["cart"][$product_id])) {
        $_SESSION["cart"][$product_id] += $quantity;
    } else {
        $_SESSION["cart"][$product_id] = $quantity;
    }
    header("Location: cart.php");
    exit;
}

if (isset($_GET["remove"])) {
    unset($_SESSION["cart"][(int)$_GET["remove"]]);
    header("Location: cart.php");
    exit;
}

$cart_items = [];
$total = 0;

if ($_SESSION["cart"]) {
    $ids = array_keys($_SESSION["cart"]);
    $placeholders = implode(",", array_fill(0, count($ids), "?"));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $qty = $_SESSION["cart"][$product["id"]];
        $subtotal = $qty * $product["price"];
        $total += $subtotal;
        $cart_items[] = [$product, $qty, $subtotal];
    }
}

include "../includes/header.php";
?>
<h2>My Cart</h2>
<?php if (!$cart_items): ?>
    <p>Your cart is empty.</p>
    <a class="btn" href="products.php">Shop Now</a>
<?php else: ?>
<div class="table-wrap">
<table>
    <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th></tr>
    <?php foreach ($cart_items as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item[0]["name"]); ?></td>
            <td>₱<?php echo number_format($item[0]["price"], 2); ?></td>
            <td><?php echo $item[1]; ?></td>
            <td>₱<?php echo number_format($item[2], 2); ?></td>
            <td><a class="btn danger" href="cart.php?remove=<?php echo $item[0]["id"]; ?>">Remove</a></td>
        </tr>
    <?php endforeach; ?>
</table>
</div>
<h3>Total: ₱<?php echo number_format($total, 2); ?></h3>
<a class="btn success" href="checkout.php">Checkout</a>
<?php endif; ?>
<?php include "../includes/footer.php"; ?>
