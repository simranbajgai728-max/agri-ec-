<?php
session_start();

// セキュリティチェック: 管理者がログインしていない場合はログインページにリダイレクト[cite: 1]
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// orders.json から注文データを読み込む[cite: 1]
$orders_file = 'data/orders.json';
$orders = [];

if (file_exists($orders_file)) {
    $orders_json = file_get_contents($orders_file);
    $orders = json_decode($orders_json, true);
    if (!is_array($orders)) {
        $orders = [];
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注文管理 - アグリマーケット</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- 管理画面ナビゲーション -->
<header style="background-color: #2e7d32;">
    <div class="logo"><h2>🌾 管理ダッシュボード (Management Dashboard)</h2></div>
    <nav>
        <a href="index.php">ショップを見る</a>
        <a href="admin_dashboard.php">商品管理</a>
        <a href="admin_orders.php" style="border-bottom: 2px solid white; padding-bottom: 5px;">注文ステータス</a>
        <a href="logout.php" style="color: #ffcdd2;">ログアウト</a>
    </nav>
</header>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>注文状況一覧</h1>
        <a href="admin_dashboard.php" class="btn" style="background: #666; text-decoration: none; color: white;">← ダッシュボードに戻る</a>
    </div>

    <?php if (empty($orders)): ?>
        <!-- 注文がない場合のメッセージ[cite: 1] -->
        <div style="background: white; padding: 40px; text-align: center; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <p style="font-size: 18px; color: var(--light-text); margin: 0;">まだ注文はありません。📦</p>
        </div>
    <?php else: ?>
        <!-- 注文履歴テーブル -->
        <table>
            <thead>
                <tr>
                    <th>注文ID</th>
                    <th>日時</th>
                    <th>購入者名</th>
                    <th>連絡先 / 住所</th>
                    <th>購入商品</th>
                    <th>合計金額</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // 新しい注文が上に来るように逆順で表示[cite: 1]
                $reversed_orders = array_reverse($orders);
                foreach ($reversed_orders as $order): 
                ?>
                    <tr>
                        <td><strong>#<?php echo htmlspecialchars($order['order_id']); ?></strong></td>
                        <td><?php echo htmlspecialchars($order['date']); ?></td>
                        <td><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($order['customer_phone']); ?><br>
                            <small style="color: var(--light-text);"><?php echo htmlspecialchars($order['customer_address']); ?></small>
                        </td>
                        <td>
                            <ul style="margin: 0; padding-left: 15px;">
                                <?php foreach ($order['items'] as $item): ?>
                                    <li><?php echo htmlspecialchars($item['name']); ?> (<?php echo $item['qty']; ?> kg)</li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td style="font-weight: bold; color: var(--primary);">
                            ¥<?php echo number_format($order['total_price']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>