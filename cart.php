<?php
session_start();

// 1. products.json から商品情報を読み込む
$products_json = file_get_contents('data/products.json');
$products = json_decode($products_json, true) ? : [];

// IDをキーにした連想配列を作成
$products_by_id = [];
foreach ($products as $p) {
    $products_by_id[(int)$p['id']] = $p;
}

// 2. カートを空にする処理
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    unset($_SESSION['cart']);
    header('Location: cart.php');
    exit;
}

// 3. カートから特定の商品を削除する処理[cite: 1]
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = (int)$_GET['id'];
    unset($_SESSION['cart'][$delete_id]);
    header('Location: cart.php');
    exit;
}

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_price = 0;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ショッピングカート - アグリマーケット</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="logo"><h2>🌾 アグリマーケット (AgriMarket)</h2></div>
    <nav>
        <a href="index.php">ホーム</a>
        <a href="cart.php">カート (<?php echo count($cart_items); ?>)</a>
    </nav>
</header>

<div class="container">
    <h1>ショッピングカート 🛒</h1>
    <a href="index.php" style="text-decoration: none; color: var(--primary); font-weight: bold;">← お買い物に戻る</a>
    
    <?php if (empty($cart_items)): ?>
        <!-- カートが空の場合の表示[cite: 1] -->
        <div style="background: white; padding: 40px; text-align: center; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-top: 20px;">
            <p style="font-size: 18px; color: var(--light-text);">カートは空です。</p>
            <a href="index.php" class="btn" style="margin-top: 15px; text-decoration: none;">商品一覧へ</a>
        </div>
    <?php else: ?>
        <!-- カート商品のテーブル[cite: 1] -->
        <table>
            <thead>
                <tr>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>数量</th>
                    <th>小計</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $id => $qty): ?>
                    <?php 
                    $product = isset($products_by_id[(int)$id]) ? $products_by_id[(int)$id] : null;
                    if (!$product) continue;
                    
                    $subtotal = $product['price'] * $qty;
                    $total_price += $subtotal;
                    ?>
                    <tr>
                        <td style="width: 100px; text-align: center;">
                            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" style="width: 70px; height: 70px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                        <td>¥<?php echo number_format($product['price']); ?></td>
                        <td><?php echo $qty; ?> kg</td>
                        <td>¥<?php echo number_format($subtotal); ?></td>
                        <td>
                            <a href="cart.php?action=delete&id=<?php echo $id; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px; text-decoration: none;">削除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- 合計金額と操作ボタン[cite: 1] -->
        <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center; background: white; padding: 20px; border-radius: 8px; border: 1px solid var(--border); box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <div>
                <a href="cart.php?action=clear" class="btn btn-danger" style="text-decoration: none;">カートを空にする 🗑️</a>
            </div>
            <div style="text-align: right;">
                <h3 style="margin: 0; color: var(--text);">合計金額:</h3>
                <span style="font-size: 24px; font-weight: bold; color: var(--accent);">¥<?php echo number_format($total_price); ?></span>
                <br><br>
                <a href="checkout.php" class="btn" style="font-size: 18px; padding: 10px 25px; background: var(--primary); text-decoration: none; color: white;">購入手続きへ進む ➔</a>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>