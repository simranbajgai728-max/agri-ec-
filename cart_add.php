<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add quantity if item already exists in cart, otherwise add new
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

// Redirect directly to cart page after adding
header('Location: cart.php');
exit;