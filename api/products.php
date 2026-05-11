<?php
require_once __DIR__ . '/helpers.php';

$method = request_method();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    if ($method === 'GET') {
        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$product) error_response('Product not found', 404);
            if (!empty($product['image'])) {
                $product['image_url'] = dirname(get_base_url()) . '/uploads/' . $product['image'];
            }
            json_response(['success' => true, 'data' => $product]);
        }

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $category = isset($_GET['category']) ? trim($_GET['category']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';

        $where = [];
        $params = [];
        if ($search !== '') {
            $where[] = '(name LIKE ? OR description LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($category !== '') {
            $where[] = 'category = ?';
            $params[] = $category;
        }
        if ($status !== '') {
            $where[] = 'status = ?';
            $params[] = $status;
        }
        $sql = 'SELECT * FROM products' . (count($where) ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY id DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($products as &$p) {
            $p['image_url'] = !empty($p['image']) ? dirname(get_base_url()) . '/uploads/' . $p['image'] : dirname(get_base_url()) . '/uploads/default-food.png';
        }
        json_response(['success' => true, 'count' => count($products), 'data' => $products]);
    }

    if ($method === 'POST') {
        $data = request_data();
        $name = required_value($data, 'name');
        $price = money_value(required_value($data, 'price'), 'price');
        $description = isset($data['description']) ? trim($data['description']) : '';
        $category = isset($data['category']) && trim($data['category']) !== '' ? trim($data['category']) : 'Meals';
        $stock = isset($data['stock']) ? int_value($data['stock'], 'stock', 0) : 0;
        $status = isset($data['status']) && in_array($data['status'], ['available','unavailable']) ? $data['status'] : 'available';
        $image = isset($data['image']) && trim($data['image']) !== '' ? trim($data['image']) : 'default-food.png';

        $stmt = $pdo->prepare("INSERT INTO products (name, description, category, price, image, stock, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $category, $price, $image, $stock, $status]);
        json_response(['success' => true, 'message' => 'Product created', 'id' => (int)$pdo->lastInsertId()], 201);
    }

    if ($method === 'PUT' || $method === 'PATCH') {
        if ($id <= 0) error_response('Product id is required for update. Example: /api/products.php?id=1', 422);
        $data = request_data();
        $fields = [];
        $params = [];
        $allowed = ['name','description','category','image','status'];
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                if ($field === 'status' && !in_array($data[$field], ['available','unavailable'])) error_response('Invalid product status', 422);
                $fields[] = "{$field} = ?";
                $params[] = trim((string)$data[$field]);
            }
        }
        if (isset($data['price'])) { $fields[] = 'price = ?'; $params[] = money_value($data['price'], 'price'); }
        if (isset($data['stock'])) { $fields[] = 'stock = ?'; $params[] = int_value($data['stock'], 'stock', 0); }
        if (!$fields) error_response('No fields to update', 422);
        $params[] = $id;
        $stmt = $pdo->prepare('UPDATE products SET ' . implode(', ', $fields) . ' WHERE id = ?');
        $stmt->execute($params);
        json_response(['success' => true, 'message' => 'Product updated', 'affected_rows' => $stmt->rowCount()]);
    }

    if ($method === 'DELETE') {
        if ($id <= 0) error_response('Product id is required for delete. Example: /api/products.php?id=1', 422);
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        json_response(['success' => true, 'message' => 'Product deleted', 'affected_rows' => $stmt->rowCount()]);
    }

    error_response('Method not allowed', 405);
} catch (Exception $e) {
    error_response('Server error: ' . $e->getMessage(), 500);
}
