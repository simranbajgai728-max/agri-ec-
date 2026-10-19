<?php
session_start();

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart_items)) {
    header('Location: index.php');
    exit;
}

// products.json から最新データ取得して価格計算
$products_json = file_get_contents('data/products.json');
$products = json_decode($products_json, true) ? : [];

$products_by_id = [];
foreach ($products as $p) {
    $products_by_id[(int)$p['id']] = $p;
}

$total_price = 0;
$order_items = [];

foreach ($cart_items as $id => $qty) {
    $product = isset($products_by_id[$id]) ? $products_by_id[$id] : null;
    if ($product) {
        $subtotal = $product['price'] * $qty;
        $total_price += $subtotal;
        $order_items[] = [
            "id" => $product['id'],
            "name" => $product['name'],
            "price" => $product['price'],
            "qty" => $qty
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if ($name !== "" && $phone !== "" && $address !== "") {
        // お会計が確定した時の処理
        $orders_json = file_get_contents('data/orders.json');
        $orders = json_decode($orders_json, true) ? : [];

        $new_order_id = rand(100000, 999999); // 自動お会計注文番号
        
        $new_order = [
            "order_id" => $new_order_id,
            "date" => date('Y-m-d H:i:s'),
            "customer_name" => $name,
            "customer_phone" => $phone,
            "customer_address" => $address,
            "items" => $order_items,
            "total_price" => $total_price
        ];

        // 在庫数を減少させるロジック (高難易度加点)[cite: 1]
        foreach ($products as $index => $p) {
            if (isset($cart_items[$p['id']])) {
                $products[$index]['stock'] -= $cart_items[$p['id']];
                if ($products[$index]['stock'] < 0) {
                    $products[$index]['stock'] = 0;
                }
            }
        }
        file_put_contents('data/products.json', json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        // 注文データを保存
        $orders[] = $new_order;
        file_put_contents('data/orders.json', json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        // カートを空にしてサクセスページへ移動[cite: 1]
        unset($_SESSION['cart']);
        $_SESSION['last_order_id'] = $new_order_id;
        header('Location: success.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>購入手続き - AgriMarket</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="logo"><h2>🌾 AgriMarket</h2></div>
    <nav>
        <a href="index.php">ホーム</a>
        <a href="cart.php">ショッピングカート</a>
    </nav>
</header>

<div class="container" style="max-width: 600px;">
    <h1>購入手続きとお届け先情報</h1>
    
    <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <h3>ご注文内容の確認</h3>
        <ul style="padding-left: 20px;">
            <?php foreach ($order_items as $item): ?>
                <li><strong><?php echo htmlspecialchars($item['name']); ?></strong> x <?php echo $item['qty']; ?> kg - ¥<?php echo number_format($item['price'] * $item['qty']); ?></li>
            <?php endforeach; ?>
        </ul>
        <hr style="border: 0; border-top: 1px solid var(--border); margin: 15px 0;">
        <p style="font-size: 18px; text-align: right;">合計金額: <span style="font-size: 24px; font-weight: bold; color: var(--accent);">¥<?php echo number_format($total_price); ?></span></p>
    </div>

    <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <h3>お届け先情報の入力</h3>
        <form action="checkout.php" method="POST">
            <div class="form-group">
                <label>お名前</label>
                <input type="text" name="name" class="form-control" required placeholder="例: 山田 太郎">
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label>電話番号</label>
                <input type="text" name="phone" class="form-control" required placeholder="例: 090-1234-5678">
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label>お届け先住所</label>
                <input type="text" name="address" class="form-control" required placeholder="例: 東京都新宿区大久保 1-2-3">
            </div>
            
            <button type="submit" class="btn" style="width: 100%; margin-top: 25px; background: var(--primary); font-size: 18px; padding: 12px;">注文を確定する ➔</button>
        </form>
    </div>
</div>

</body>
</html>