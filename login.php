<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require "config/db.php";
require "includes/auth.php";

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];
        redirect_by_role($user["role"]);
    } else {
        $error = "Invalid email or password.";
    }
}
include "includes/header.php";
?>
<div class="auth-layout">
    <div class="auth-panel">
        <span class="kicker light">Welcome back</span>
        <h1>Login to OrderFlow</h1>
        <p>Sign in with your account to continue. Admin users can manage products, upload images, update stocks, and monitor orders.</p>
        <div class="login-credentials clean-login-note">
            <strong>Secure Login</strong>
            <span>No email or password is pre-filled. Type your own account details every time.</span>
        </div>
    </div>
    <div class="form-box auth-box">
        <h2>Sign in</h2>
        <?php if (isset($_GET["registered"])): ?><div class="alert">Registration successful. Please login.</div><?php endif; ?>
        <?php if ($error): ?><div class="alert"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" required placeholder="Enter your email">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter password">
            <button class="btn full" type="submit">Login</button>
        </form>
        <p class="small-note">No account yet? <a href="register.php">Create a customer account</a>.</p>
    </div>
</div>
<?php include "includes/footer.php"; ?>
