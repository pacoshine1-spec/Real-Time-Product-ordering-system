<?php
require_once __DIR__ . '/helpers.php';

$method = request_method();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    if ($method === 'GET') {
        if ($id > 0) {
            $stmt = $pdo->prepare('SELECT id, name, email, role, created_at FROM users WHERE id=?');
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) error_response('User not found', 404);
            json_response(['success' => true, 'data' => $user]);
        }
        $stmt = $pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY id DESC');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'count' => count($users), 'data' => $users]);
    }

    if ($method === 'POST') {
        $data = request_data();
        $name = required_value($data, 'name');
        $email = required_value($data, 'email');
        $password = password_hash(required_value($data, 'password'), PASSWORD_DEFAULT);
        $role = isset($data['role']) && in_array($data['role'], ['customer','admin','staff']) ? $data['role'] : 'customer';
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $password, $role]);
        json_response(['success' => true, 'message' => 'User created', 'id' => (int)$pdo->lastInsertId()], 201);
    }

    if ($method === 'PUT' || $method === 'PATCH') {
        if ($id <= 0) error_response('User id is required for update. Example: /api/users.php?id=1', 422);
        $data = request_data();
        $fields = [];
        $params = [];
        foreach (['name','email','role'] as $field) {
            if (isset($data[$field])) {
                if ($field === 'role' && !in_array($data[$field], ['customer','admin','staff'])) error_response('Invalid role', 422);
                $fields[] = "{$field}=?";
                $params[] = trim((string)$data[$field]);
            }
        }
        if (isset($data['password']) && trim($data['password']) !== '') {
            $fields[] = 'password=?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (!$fields) error_response('No fields to update', 422);
        $params[] = $id;
        $stmt = $pdo->prepare('UPDATE users SET ' . implode(', ', $fields) . ' WHERE id=?');
        $stmt->execute($params);
        json_response(['success' => true, 'message' => 'User updated', 'affected_rows' => $stmt->rowCount()]);
    }

    if ($method === 'DELETE') {
        if ($id <= 0) error_response('User id is required for delete. Example: /api/users.php?id=1', 422);
        $stmt = $pdo->prepare('DELETE FROM users WHERE id=?');
        $stmt->execute([$id]);
        json_response(['success' => true, 'message' => 'User deleted', 'affected_rows' => $stmt->rowCount()]);
    }

    error_response('Method not allowed', 405);
} catch (Exception $e) {
    error_response('Server error: ' . $e->getMessage(), 500);
}
