<?php
session_start();

// 試験要件に合わせたログイン情報 (ID: admin / PASS: 1234)[cite: 1]
$admin_id = "admin";
$admin_pass = "1234";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $input_pass = isset($_POST['password']) ? $_POST['password'] : '';

    if ($input_id === "" || $input_pass === "") {
        $error = "IDとパスワードを入力してください。";
    } elseif ($input_id === $admin_id && $input_pass === $admin_pass) {
        // セッションにログイン情報を保存
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $admin_id;
        
        // ログイン成功後に管理者ダッシュボードへ遷移[cite: 1]
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error = "IDまたはパスワードが正しくありません。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン - AgriMarket</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background-color: #f4f7f4;">

<header>
    <div class="logo"><h2>🌾 アグリマーケット (AgriMarket)</h2></div>
    <nav>
        <a href="index.php">ホーム</a>
    </nav>
</header>

<div class="container" style="max-width: 400px; margin-top: 80px;">
    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12 rgba(0,0,0,0.1);">
        <h2 style="text-align: center; margin-bottom: 20px; color: #2e7d32;">管理者ログイン</h2>
        
        <!-- エラーメッセージ表示エリア -->
        <?php if ($error !== ""): ?>
            <div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="id">ユーザーID</label>
                <input type="text" name="id" id="id" class="form-control" placeholder="admin" required>
            </div>
            
            <div class="form-group" style="margin-top: 15px;">
                <label for="password">パスワード</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="1234" required>
            </div>
            
            <button type="submit" class="btn" style="width: 100%; margin-top: 20px; padding: 12px; font-size: 16px; background-color: #2e7d32; color: white; border: none; border-radius: 4px; cursor: pointer;">
                ログイン
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="index.php" style="color: #666; text-decoration: none; font-size: 14px;">← ショップに戻る</a>
        </div>
    </div>
</div>

</body>
</html>