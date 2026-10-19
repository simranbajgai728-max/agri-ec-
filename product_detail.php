<?php
session_start();

// Fetch products data
$products_json = file_get_contents('data/products.json');
$products = json_decode($products_json, true) ? : [];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;

foreach ($products as $p) {
    if ((int)$p['id'] === $id) {
        $product = $p;
        break;
    }
}

// Redirect to home if product not found
if (!$product) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Details</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="logo"><h2>🌾 AgriMarket</h2></div>
    <nav>
        <a href="index.php">Home</a>
        <a href="cart.php">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
    </nav>
</header>

<div class="container" style="max-width: 800px; background: white; padding: 30px; border-radius: 8px; margin-top: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
    <a href="index.php" class="btn" style="background: #666; margin-bottom: 20px; text-decoration: none; color: white;">← Back to Shop</a>
    
    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 280px;">
            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" style="width: 100%; border-radius: 8px; max-height: 350px; object-fit: cover;" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        
        <div style="flex: 1; min-width: 280px;">
            <span class="badge bg-<?php echo $product['category']; ?>"><?php echo ucfirst($product['category']); ?></span>
            <h1 style="margin: 10px 0;"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p style="font-size: 24px; color: var(--accent); font-weight: bold; margin: 10px 0;">¥<?php echo number_format($product['price']); ?></p>
            
            <p><strong>Stock Available:</strong> <?php echo $product['stock']; ?> kg</p>
            <p style="color: var(--light-text); margin-bottom: 20px;"><?php echo htmlspecialchars($product['description']); ?></p>
            
            <?php if ($product['stock'] > 0): ?>
                <form action="cart_add.php" method="POST" style="display: flex; gap: 10px;">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="form-control" style="width: 80px; text-align: center;">
                    <button type="submit" class="btn" style="flex: 1;">Add to Cart 🛒</button>
                </form>
            <?php else: ?>
                <button class="btn btn-danger" style="width: 100%;" disabled>Sold Out</button>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>