<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = (int)$_POST['price'];
    $category = $_POST['category'];
    $stock = (int)$_POST['stock'];
    $description = trim($_POST['description']);
    $image_file = $_FILES['image'];

    if ($name === "" || $price <= 0 || $description === "") {
        $error = "すべての項目を正しく入力してください。";
    } else {
        // 画像アップロードの処理
        $image_name = "no-image.jpg";
        if ($image_file['error'] === 0) {
            $ext = pathinfo($image_file['name'], PATHINFO_EXTENSION);
            $image_name = time() . '.' . $ext;
            move_uploaded_file($image_file['tmp_name'], 'images/' . $image_name);
        }

        // データの読み込みと新規登録
        $products_json = file_get_contents('data/products.json');
        $products = json_decode($products_json, true) ? : [];

        $new_id = count($products) > 0 ? end($products)['id'] + 1 : 1;

        $new_product = [
            "id" => $new_id,
            "name" => $name,
            "price" => $price,
            "category" => $category,
            "description" => $description,
            "stock" => $stock,
            "image" => $image_name
        ];

        $products[] = $new_product;
        file_put_contents('data/products.json', json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        
        $success = "新しい商品が登録されました！";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品登録 - AgriMarket</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header style="background-color: #2e7d32;">
    <div class="logo"><h2>🌾 管理画面 - 商品追加</h2></div>
</header>

<div class="container" style="max-width: 600px; background: white; padding: 30px; border-radius: 8px; margin-top: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
    <a href="admin_dashboard.php" class="btn" style="background: #666; margin-bottom: 20px; text-decoration: none; color: white;">← 戻る</a>
    
    <h2>新規商品登録</h2>
    
    <?php if ($error !== ""): ?>
        <div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 4px; margin-bottom: 15px;"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success !== ""): ?>
        <div style="background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 4px; margin-bottom: 15px;"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="admin_add.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>商品名</label>
            <input type="text" name="name" class="form-control" required placeholder="例: 新鮮なキャベツ">
        </div>
        <div class="form-group" style="margin-top: 15px;">
            <label>価格 (円)</label>
            <input type="number" name="price" class="form-control" required placeholder="例: 250">
        </div>
        <div class="form-group" style="margin-top: 15px;">
            <label>カテゴリ</label>
            <select name="category" class="form-control">
                <option value="vegetables">野菜</option>
                <option value="fruits">果物</option>
            </select>
        </div>
        <div class="form-group" style="margin-top: 15px;">
            <label>初期在庫数 (kg)</label>
            <input type="number" name="stock" class="form-control" value="10" required>
        </div>
        <div class="form-group" style="margin-top: 15px;">
            <label>商品説明</label>
            <textarea name="description" class="form-control" rows="4" required placeholder="商品の特徴などを入力してください..."></textarea>
        </div>
        <div class="form-group" style="margin-top: 15px;">
            <label>商品画像</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        
        <button type="submit" class="btn" style="width: 100%; margin-top: 25px; background: #2e7d32; font-size: 16px;">商品を登録する</button>
    </form>
</div>

</body>
</html>