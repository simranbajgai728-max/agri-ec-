<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $products_json = file_get_contents('data/products.json');
    $products = json_decode($products_json, true) ? : [];
    
    $new_products = [];
    foreach ($products as $p) {
        if ((int)$p['id'] !== $id) {
            $new_products[] = $p;
        }
    }
    
    file_put_contents('data/products.json', json_encode($new_products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

header('Location: admin_dashboard.php');
exit;