<?php
session_start();

// セキュリティチェック: 未ログイン時はログイン画面へ戻す
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 商品データの読み込み
$products_json = file_get_contents('data/products.json');
$products = json_decode($products_json, true) ? : [];

// 注文データの読み込みと集計
$orders_json = file_get_contents('data/orders.json');
$orders = json_decode($orders_json, true) ? : [];

$total_revenue = 0;
foreach ($orders as $order) {
    $total_revenue += $order['total_price'];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ダッシュボード - AgriMarket</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header style="background-color: #2e7d32;">
    <div class="logo"><h2>🌾 管理画面ダッシュボード</h2></div>
    <nav>
        <a href="index.php">ショップを見る</a>
        <a href="admin_dashboard.php" style="border-bottom: 2px solid white; padding-bottom: 5px;">商品管理</a>
        <a href="admin_orders.php">注文状況</a>
        <a href="logout.php" style="color: #ffcdd2;">ログアウト</a>
    </nav>
</header>

<div class="container">
    <h1>システム状況サマリー</h1>
    
    <!-- 集計ダッシュボード -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center; border-left: 5px solid #2e7d32;">
            <h3 style="color: #666; margin: 0;">登録商品数</h3>
            <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #2e7d32;"><?php echo count($products); ?> 件</p>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center; border-left: 5px solid #f57c00;">
            <h3 style="color: #666; margin: 0;">累計注文数</h3>
            <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #f57c00;"><?php echo count($orders); ?> 件</p>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center; border-left: 5px solid #1976d2;">
            <h3 style="color: #666; margin: 0;">総売上高</h3>
            <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #1976d2;">¥<?php echo number_format($total_revenue); ?></p>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>登録商品一覧</h2>
        <a href="admin_add.php" class="btn" style="background: #2e7d32; text-decoration: none; color: white;">+ 新規商品登録</a>
    </div>

    <!-- 商品一覧テーブル -->
    <table>
        <thead>
            <tr>
                <th>画像</th>
                <th>商品名</th>
                <th>カテゴリ</th>
                <th>価格</th>
                <th>現在の在庫</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #666;">登録された商品がありません。</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td style="width: 80px; text-align: center;">
                            <img src="images/<?php echo htmlspecialchars($p['image']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                        <td>
                            <span class="badge bg-<?php echo $p['category']; ?>">
                                <?php echo $p['category'] === 'vegetables' ? '野菜' : '果物'; ?>
                            </span>
                        </td>
                        <td>¥<?php echo number_format($p['price']); ?></td>
                        <td>
                            <?php if ($p['stock'] == 0): ?>
                                <span class="badge stock-out">売り切れ</span>
                            <?php elseif ($p['stock'] <= 5): ?>
                                <span class="badge stock-low">残りわずか (<?php echo $p['stock']; ?> kg)</span>
                            <?php else: ?>
                                <?php echo $p['stock']; ?> kg
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="admin_edit.php?id=<?php echo $p['id']; ?>" class="btn" style="padding: 4px 10px; font-size: 13px; background: #f57c00; text-decoration: none; color: white;">編集</a>
                            <a href="admin_delete.php?id=<?php echo $p['id']; ?>" class="btn btn-danger" style="padding: 4px 10px; font-size: 13px; text-decoration: none; color: white;" onclick="return confirm('本当にこの商品を削除しますか？');">削除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>