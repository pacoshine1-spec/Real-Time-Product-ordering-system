<?php
require "config/db.php";
require "includes/auth.php";

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($name && $email && $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
            $stmt->execute([$name, $email, $hashed]);
            header("Location: login.php?registered=1");
            exit;
        } catch (PDOException $e) {
            $error = "Email already exists.";
        }
    } else {
        $error = "Please complete all fields.";
    }
}
include "includes/header.php";
?>
<div class="auth-layout">
    <div class="auth-panel">
        <span class="kicker light">Join OrderFlow</span>
        <h1>Create your customer account</h1>
        <p>Register to browse products, add food to your cart, checkout, and track your orders.</p>
    </div>
    <div class="form-box auth-box">
        <h2>Register</h2>
        <?php if ($error): ?><div class="alert"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <label>Name</label>
            <input type="text" name="name" required placeholder="Your full name">
            <label>Email</label>
            <input type="email" name="email" required placeholder="you@example.com">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Create a password">
            <button class="btn full" type="submit">Create Account</button>
        </form>
        <p class="small-note">Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</div>
<?php include "includes/footer.php"; ?>
