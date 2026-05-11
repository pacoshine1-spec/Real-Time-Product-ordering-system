<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Real-Time Product Ordering System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/realtime_product_ordering_system/assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="/realtime_product_ordering_system/index.php" class="brand">OrderFlow</a>
    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="/realtime_product_ordering_system/admin/dashboard.php">Dashboard</a>
                <a href="/realtime_product_ordering_system/admin/products.php">Products</a>
                <a href="/realtime_product_ordering_system/admin/orders.php">Orders</a>
            <?php elseif ($_SESSION['role'] === 'staff'): ?>
                <a href="/realtime_product_ordering_system/admin/orders.php">Orders</a>
            <?php else: ?>
                <a href="/realtime_product_ordering_system/customer/products.php">Products</a>
                <a href="/realtime_product_ordering_system/customer/cart.php">Cart</a>
                <a href="/realtime_product_ordering_system/customer/my_orders.php">My Orders</a>
            <?php endif; ?>
            <a href="/realtime_product_ordering_system/logout.php">Logout</a>
        <?php else: ?>
            <a href="/realtime_product_ordering_system/login.php">Login</a>
            <a href="/realtime_product_ordering_system/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>
<main class="container">
