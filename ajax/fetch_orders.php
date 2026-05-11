<?php
require "../config/db.php";
require "../includes/auth.php";
require_role(["admin", "staff"]);

$stmt = $pdo->query("
    SELECT orders.*, users.name AS customer_name
    FROM orders
    JOIN users ON orders.user_id = users.id
    ORDER BY orders.id DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="table-wrap">
<table>
<tr>
    <th>Order ID</th>
    <th>Customer</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>
<?php foreach ($orders as $order): ?>
<tr>
    <td>#<?php echo $order["id"]; ?></td>
    <td><?php echo htmlspecialchars($order["customer_name"]); ?></td>
    <td>₱<?php echo number_format($order["total_amount"], 2); ?></td>
    <td><span class="status status-<?php echo $order["status"]; ?>"><?php echo ucfirst($order["status"]); ?></span></td>
    <td><?php echo $order["created_at"]; ?></td>
    <td>
        <select onchange="updateStatus(<?php echo $order['id']; ?>, this.value)">
            <?php
            $statuses = ["pending", "preparing", "ready", "completed", "cancelled"];
            foreach ($statuses as $status):
            ?>
                <option value="<?php echo $status; ?>" <?php echo $order["status"] === $status ? "selected" : ""; ?>>
                    <?php echo ucfirst($status); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <a class="btn" href="order_details.php?id=<?php echo $order["id"]; ?>">View</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
</div>
