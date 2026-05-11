<?php
require "../config/db.php";
require "../includes/auth.php";
require_role(["admin", "staff"]);

$order_id = (int)$_GET["id"];

$stmt = $pdo->prepare("
    SELECT orders.*, users.name AS customer_name, users.email
    FROM orders
    JOIN users ON orders.user_id = users.id
    WHERE orders.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

$stmt = $pdo->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
?>
<h2>Order #<?php echo $order["id"]; ?></h2>
<p>Customer: <?php echo htmlspecialchars($order["customer_name"]); ?> - <?php echo htmlspecialchars($order["email"]); ?></p>
<p>Status: <span class="status status-<?php echo $order["status"]; ?>"><?php echo ucfirst($order["status"]); ?></span></p>
<div class="table-wrap">
<table>
<tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr>
<?php foreach ($items as $item): ?>
<tr>
    <td><?php echo htmlspecialchars($item["name"]); ?></td>
    <td>₱<?php echo number_format($item["price"], 2); ?></td>
    <td><?php echo $item["quantity"]; ?></td>
    <td>₱<?php echo number_format($item["price"] * $item["quantity"], 2); ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<h3>Total: ₱<?php echo number_format($order["total_amount"], 2); ?></h3>
<?php include "../includes/footer.php"; ?>
