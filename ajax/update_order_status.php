<?php
require "../config/db.php";
require "../includes/auth.php";
require_role(["admin", "staff"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $order_id = (int)$_POST["order_id"];
    $status = $_POST["status"];
    $allowed = ["pending", "preparing", "ready", "completed", "cancelled"];

    if (in_array($status, $allowed)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        echo "success";
    } else {
        echo "invalid";
    }
}
?>
