<?php
header('Content-Type: application/json; charset=utf-8');
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $base;
echo json_encode([
    'success' => true,
    'message' => 'SHINE Ordering System API is running',
    'endpoints' => [
        'products' => [
            'GET all' => $root . '/products.php',
            'GET one' => $root . '/products.php?id=1',
            'POST create' => $root . '/products.php',
            'PUT update' => $root . '/products.php?id=1',
            'DELETE' => $root . '/products.php?id=1'
        ],
        'orders' => [
            'GET all' => $root . '/orders.php',
            'GET one' => $root . '/orders.php?id=1',
            'POST create' => $root . '/orders.php',
            'PUT status update' => $root . '/orders.php?id=1',
            'DELETE' => $root . '/orders.php?id=1'
        ],
        'users' => [
            'GET all' => $root . '/users.php',
            'GET one' => $root . '/users.php?id=1',
            'POST create' => $root . '/users.php',
            'PUT update' => $root . '/users.php?id=1',
            'DELETE' => $root . '/users.php?id=1'
        ],
        'browser_tester' => $root . '/tester.php'
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
