<?php
require_once __DIR__ . '/../config/db.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

function error_response($message, $code = 400, $extra = []) {
    json_response(array_merge(['success' => false, 'message' => $message], $extra), $code);
}

function request_method() {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST' && isset($_POST['_method'])) {
        $method = strtoupper($_POST['_method']);
    }
    if ($method === 'POST' && isset($_GET['_method'])) {
        $method = strtoupper($_GET['_method']);
    }
    return $method;
}

function request_data() {
    $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
    $raw = file_get_contents('php://input');
    $data = [];

    if (stripos($contentType, 'application/json') !== false && trim($raw) !== '') {
        $json = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_response('Invalid JSON body: ' . json_last_error_msg(), 400);
        }
        $data = is_array($json) ? $json : [];
    } else {
        $data = $_POST;
        if (empty($data) && trim($raw) !== '') {
            parse_str($raw, $data);
        }
    }

    return $data;
}

function required_value($data, $key) {
    if (!isset($data[$key]) || trim((string)$data[$key]) === '') {
        error_response("Missing required field: {$key}", 422);
    }
    return trim((string)$data[$key]);
}

function money_value($value, $field) {
    if (!is_numeric($value)) {
        error_response("{$field} must be a number", 422);
    }
    return number_format((float)$value, 2, '.', '');
}

function int_value($value, $field, $min = null) {
    if (!is_numeric($value)) {
        error_response("{$field} must be a number", 422);
    }
    $int = (int)$value;
    if ($min !== null && $int < $min) {
        error_response("{$field} must be at least {$min}", 422);
    }
    return $int;
}

function get_base_url() {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    return $scheme . '://' . $host . $dir;
}
