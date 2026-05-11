<?php include "includes/header.php"; ?>
<section class="hero landing-hero">
    <div>
        <span class="kicker">PHP + MySQL Ordering Platform</span>
        <h1>Professional food ordering with admin product control.</h1>
        <p>
            Customers can browse a polished product catalog, add items to cart, and place orders.
            Admin users can login, add products, upload product pictures, update stock, and monitor live orders.
        </p>
        <div class="actions">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a class="btn" href="login.php">Login</a>
                <a class="btn success" href="register.php">Create Account</a>
            <?php else: ?>
                <a class="btn" href="<?php echo $_SESSION['role'] === 'customer' ? 'customer/products.php' : 'admin/dashboard.php'; ?>">Go to System</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero-product-stack">
        <img src="uploads/signature-burger.png" alt="Burger">
        <img src="uploads/chicken-rice.png" alt="Chicken meal">
        <img src="uploads/blue-lemonade.png" alt="Drink">
    </div>
</section>
<section class="feature-grid">
    <div class="card"><h3>Admin Panel</h3><p>Admins can login securely, add product pictures, update stocks, and manage customer orders.</p></div>
    <div class="card"><h3>Product Images</h3><p>Includes uploaded product photos and support for custom image uploads.</p></div>
    <div class="card"><h3>Live Orders</h3><p>Staff and admins can update order status from the real-time order page.</p></div>
</section>
<?php include "includes/footer.php"; ?>
