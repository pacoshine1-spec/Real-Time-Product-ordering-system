<?php
require "../config/db.php";
require "../includes/auth.php";
require_role("admin");

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();

include "../includes/header.php";
?>
<span class="kicker">Administrator</span>
<h2>Admin Dashboard</h2>
<p>Overview of products, customers, and real-time order activity.</p>
<div class="dashboard-cards">
    <div class="dashboard-card"><h2><?php echo $totalProducts; ?></h2><p>Products</p></div>
    <div class="dashboard-card"><h2><?php echo $totalOrders; ?></h2><p>Orders</p></div>
    <div class="dashboard-card"><h2><?php echo $totalUsers; ?></h2><p>Customers</p></div>
    <div class="dashboard-card"><h2><?php echo $pendingOrders; ?></h2><p>Pending Orders</p></div>
</div>
<?php include "../includes/footer.php"; ?>
