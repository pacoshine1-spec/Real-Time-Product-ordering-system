<?php
require "../config/db.php";
require "../includes/auth.php";
require_role("customer");

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$_SESSION["user_id"]]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
?>
<h2>My Orders</h2>
<?php if (isset($_GET["success"])): ?><div class="alert">Order placed successfully.</div><?php endif; ?>
<div class="table-wrap">
<table>
<tr><th>Order ID</th><th>Total</th><th>Status</th><th>Date</th><th>Details</th></tr>
<?php foreach ($orders as $order): ?>
<tr>
    <td>#<?php echo $order["id"]; ?></td>
    <td>₱<?php echo number_format($order["total_amount"], 2); ?></td>
    <td><span class="status status-<?php echo $order["status"]; ?>"><?php echo ucfirst($order["status"]); ?></span></td>
    <td><?php echo $order["created_at"]; ?></td>
    <td><a class="btn" href="order_details.php?id=<?php echo $order["id"]; ?>">View</a></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php include "../includes/footer.php"; ?>
