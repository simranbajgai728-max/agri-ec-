<?php
session_start();
$order_id = isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : null;

// リフレッシュされたときなどのためにセッションを一度きりで消去できるようにする
if ($order_id) {
    unset($_SESSION['last_order_id']);
} else {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ご注文完了 - AgriMarket</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="logo"><h2>🌾 AgriMarket</h2></div>
    <nav>
        <a href="index.php">ホーム</a>
    </nav>
</header>

<div class="container" style="max-width: 600px; text-align: center; margin-top: 50px;">
    <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <span style="font-size: 60px;">🎉</span>
        <h1 style="color: var(--primary); margin-top: 10px;">ご注文ありがとうございます！</h1>
        <p style="font-size: 16px; color: var(--light-text); margin-bottom: 30px;">ご注文が正常に受け付けられました。新鮮な状態でお届けいたします。</p>
        
        <!-- 自動注文番号を表示[cite: 1] -->
        <div style="background: #f4f7f4; padding: 15px; border-radius: 6px; border: 1px dashed var(--primary); display: inline-block; margin-bottom: 30px;">
            <span style="font-size: 14px; color: #666;">お問い合せ注文番号</span><br>
            <strong style="font-size: 24px; color: var(--primary);">#<?php echo htmlspecialchars($order_id); ?></strong>
        </div>
        
        <div>
            <a href="index.php" class="btn" style="padding: 10px 25px; text-decoration: none;">ショップトップへ戻る</a>
        </div>
    </div>
</div>

</body>
</html>