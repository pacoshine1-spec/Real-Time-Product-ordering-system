<?php
require "../config/db.php";
require "../includes/auth.php";
require_role("customer");

if (empty($_SESSION["cart"])) {
    header("Location: cart.php");
    exit;
}

$pdo->beginTransaction();

try {
    $ids = array_keys($_SESSION["cart"]);
    $placeholders = implode(",", array_fill(0, count($ids), "?"));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders) FOR UPDATE");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = 0;
    foreach ($products as $product) {
        $qty = $_SESSION["cart"][$product["id"]];
        if ($qty > $product["stock"]) {
            throw new Exception("Not enough stock for " . $product["name"]);
        }
        $total += $qty * $product["price"];
    }

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$_SESSION["user_id"], $total]);
    $order_id = $pdo->lastInsertId();

    foreach ($products as $product) {
        $qty = $_SESSION["cart"][$product["id"]];
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $product["id"], $qty, $product["price"]]);

        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$qty, $product["id"]]);
    }

    $pdo->commit();
    $_SESSION["cart"] = [];
    header("Location: my_orders.php?success=1");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    include "../includes/header.php";
    echo "<div class='alert'>" . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<a class='btn' href='cart.php'>Back to Cart</a>";
    include "../includes/footer.php";
}
?>
