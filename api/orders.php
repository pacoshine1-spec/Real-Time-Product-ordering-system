<?php
require_once __DIR__ . '/helpers.php';

$method = request_method();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    if ($method === 'GET') {
        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT o.*, u.name AS customer_name, u.email AS customer_email FROM orders o JOIN users u ON u.id=o.user_id WHERE o.id=?");
            $stmt->execute([$id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$order) error_response('Order not found', 404);
            $items = $pdo->prepare("SELECT oi.*, p.name AS product_name, p.image FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?");
            $items->execute([$id]);
            $order['items'] = $items->fetchAll(PDO::FETCH_ASSOC);
            json_response(['success' => true, 'data' => $order]);
        }

        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $where = '';
        $params = [];
        if ($status !== '') { $where = ' WHERE o.status = ?'; $params[] = $status; }
        $stmt = $pdo->prepare("SELECT o.id, o.user_id, u.name AS customer_name, o.total_amount, o.status, o.created_at FROM orders o JOIN users u ON u.id=o.user_id {$where} ORDER BY o.id DESC");
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'count' => count($orders), 'data' => $orders]);
    }

    if ($method === 'POST') {
        $data = request_data();
        $userId = int_value(required_value($data, 'user_id'), 'user_id', 1);
        if (!isset($data['items']) || !is_array($data['items']) || count($data['items']) === 0) {
            error_response('items array is required. Example: items: [{"product_id":1,"quantity":2}]', 422);
        }

        $pdo->beginTransaction();
        $total = 0;
        $validItems = [];
        foreach ($data['items'] as $item) {
            if (!isset($item['product_id'], $item['quantity'])) error_response('Each item needs product_id and quantity', 422);
            $productId = int_value($item['product_id'], 'product_id', 1);
            $qty = int_value($item['quantity'], 'quantity', 1);
            $stmt = $pdo->prepare('SELECT id, price, stock FROM products WHERE id=? AND status="available"');
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$product) error_response("Product {$productId} not found or unavailable", 404);
            $price = (float)$product['price'];
            $total += $price * $qty;
            $validItems[] = ['product_id' => $productId, 'quantity' => $qty, 'price' => $price];
        }

        $stmt = $pdo->prepare('INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)');
        $stmt->execute([$userId, number_format($total, 2, '.', ''), 'pending']);
        $orderId = (int)$pdo->lastInsertId();

        $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
        $stockStmt = $pdo->prepare('UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE id = ?');
        foreach ($validItems as $item) {
            $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
            $stockStmt->execute([$item['quantity'], $item['product_id']]);
        }
        $pdo->commit();
        json_response(['success' => true, 'message' => 'Order created', 'id' => $orderId, 'total_amount' => number_format($total, 2, '.', '')], 201);
    }

    if ($method === 'PUT' || $method === 'PATCH') {
        if ($id <= 0) error_response('Order id is required for update. Example: /api/orders.php?id=1', 422);
        $data = request_data();
        if (!isset($data['status'])) error_response('status is required', 422);
        $allowed = ['pending','preparing','ready','completed','cancelled'];
        if (!in_array($data['status'], $allowed)) error_response('Invalid order status', 422);
        $stmt = $pdo->prepare('UPDATE orders SET status=? WHERE id=?');
        $stmt->execute([$data['status'], $id]);
        json_response(['success' => true, 'message' => 'Order status updated', 'affected_rows' => $stmt->rowCount()]);
    }

    if ($method === 'DELETE') {
        if ($id <= 0) error_response('Order id is required for delete. Example: /api/orders.php?id=1', 422);
        $stmt = $pdo->prepare('DELETE FROM orders WHERE id=?');
        $stmt->execute([$id]);
        json_response(['success' => true, 'message' => 'Order deleted', 'affected_rows' => $stmt->rowCount()]);
    }

    error_response('Method not allowed', 405);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_response('Server error: ' . $e->getMessage(), 500);
}
