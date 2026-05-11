<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: ../login.php");
        exit;
    }
}

function require_role($roles) {
    require_login();
    if (!in_array($_SESSION['role'], (array)$roles)) {
        header("Location: ../index.php");
        exit;
    }
}

function redirect_by_role($role) {
    if ($role === 'admin') {
        header("Location: admin/dashboard.php");
    } elseif ($role === 'staff') {
        header("Location: admin/orders.php");
    } else {
        header("Location: customer/products.php");
    }
    exit;
}
?>
