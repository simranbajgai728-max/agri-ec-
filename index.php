<?php
session_start();
// JSON からデータを取得
$products_json = file_get_contents('data/products.json');
$products = json_decode($products_json, true) ? : [];

// 検索とフィルターのロジック
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$filtered_products = [];
foreach ($products as $p) {
    if ($search !== '' && strpos($p['name'], $search) === false && strpos($p['description'], $search) === false) {
        continue;
    }
    if ($category !== '' && $p['category'] !== $category) {
        continue;
    }
    $filtered_products[] = $p;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アグリマーケット - 新鮮農産物直売所</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="logo"><h2>🌾 アグリマーケット (AgriMarket)</h2></div>
    <nav>
        <a href="index.php">ホーム</a>
        <a href="cart.php">カート (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
        
        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
            <a href="admin_dashboard.php" style="background: #f57c00; padding: 5px 10px; border-radius: 4px;">管理画面</a>
            <a href="logout.php" style="color: #ffcdd2;">ログアウト</a>
        <?php else: ?>
            <a href="login.php" style="background: #1b5e20; padding: 5px 12px; border-radius: 4px; border: 1px solid white;">ログイン</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <h1>新鮮な農産物一覧</h1>
    
    <form action="index.php" method="GET" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="text" name="search" placeholder="商品を検索..." value="<?php echo htmlspecialchars($search); ?>" class="form-control" style="max-width: 300px;">
        <select name="category" class="form-control" style="max-width: 150px;">
            <option value="">すべてのカテゴリ</option>
            <option value="vegetables" <?php if($category=='vegetables') echo 'selected'; ?>>野菜</option>
            <option value="fruits" <?php if($category=='fruits') echo 'selected'; ?>>果物</option>
        </select>
        <button type="submit" class="btn">検索</button>
        <a href="index.php" class="btn" style="background:#666; text-decoration: none; color: white; line-height: 2.2;">リセット</a>
    </form>

    <div class="product-grid">
        <?php if (empty($filtered_products)): ?>
            <p>該当する商品が見つかりませんでした。</p>
        <?php else: ?>
            <?php foreach ($filtered_products as $product): ?>
                <div class="product-card">
                    <img src="images/<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : 'no-image.jpg'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>
                        <span class="badge bg-<?php echo $product['category']; ?>"><?php echo $product['category'] == 'vegetables' ? '野菜' : '果物'; ?></span>
                        
                        <?php if ($product['stock'] == 0): ?>
                            <span class="badge stock-out">売り切れ (Sold Out)</span>
                        <?php elseif ($product['stock'] <= 5): ?>
                            <span class="badge stock-low">残りわずか (あと <?php echo $product['stock']; ?> 個)</span>
                        <?php else: ?>
                            <span class="badge" style="background:#e0f2f1; color:#004d40;">在庫あり (<?php echo $product['stock']; ?>)</span>
                        <?php endif; ?>
                    </p>
                    <p style="font-size: 18px; font-weight: bold; color: var(--accent);">¥<?php echo number_format($product['price']); ?></p>
                    <p style="font-size: 14px; color: var(--light-text);"><?php echo htmlspecialchars(mb_strimwidth($product['description'], 0, 60, "...")); ?></p>
                    
                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn" style="width: 90%; text-decoration: none;">詳細を見る</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>