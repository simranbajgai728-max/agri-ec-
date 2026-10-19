<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$products_json = file_get_contents('data/products.json');
$products = json_decode($products_json, true) ? : [];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product_index = -1;

foreach ($products as $index => $p) {
    if ((int)$p['id'] === $id) {
        $product_index = $index;
        break;
    }
}

if ($product_index === -1) {
    header('Location: admin_dashboard.php');
    exit;
}

$product = $products[$product_index];
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
        $image_name = $product['image'];
        if ($image_file['error'] === 0) {
            $ext = pathinfo($image_file['name'], PATHINFO_EXTENSION);
            $image_name = time() . '.' . $ext;
            move_uploaded_file($image_file['tmp_name'], 'images/' . $image_name);
        }

        $products[$product_index] = [
            "id" => $id,
            "name" => $name,
            "price" => $price,
            "category" => $category,
            "description" => $description,
            "stock" => $stock,
            "image" => $image_name
        ];

        file_put_contents('data/products.json', json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $success = "商品情報が更新されました！";
        $product = $products[$product_index]; // 画面表示を更新
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品編集 - AgriMarket</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header style="background-color: #2e7d32;">
    <div class="logo"><h2>🌾 管理画面 - 商品編集</h2></div>
</header>

<div class="container" style="max-width: 600px; background: white; padding: 30px; border-radius: 8px; margin-top: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
    <a href="admin_dashboard.php" class="btn" style="background: #666; margin-bottom: 20px; text-decoration: none; color: white;">← 戻る</a>
    
    <h2>商品情報の編集</h2>
    
    <?php if ($error !== ""): ?>
        <div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 4px; margin-bottom: 15px;"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success !== ""): ?>
        <div style="background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 4px; margin-bottom: 15px;"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="admin_edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>商品名</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>
        <div class="form-group" style="margin-top: 15px;">
            <label>価格 (円)</label>
            <input type="number" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
        </div>
        <div class="form-group" style="margin-top: 15px;">
            <label>カテゴリ</label>
            <select name="category" class="form-control">
                <option value="vegetables" <?php if($product['category'] == 'vegetables') echo 'selected'; ?>>野菜</option>
                <option value="fruits" <?php if($product['category'] == 'fruits') echo 'selected'; ?>>果物</option>
            </select>
        </div>
        <div class="form-group" style="margin-top: 15px;">
            <label>在庫数 (kg)</label>
            <input type="number" name="stock" class="form-control" value="<?php echo $product['stock']; ?>" required>
        </div>
        <div class="form-group" style="margin-top: 15px;">
            <label>商品説明</label>
            <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        <div class="form-group" style="margin-top: 15px;">
            <label>商品画像 (変更する場合のみ選択)</label>
            <p><small style="color: #666;">現在設定されている画像: <?php echo htmlspecialchars($product['image']); ?></small></p>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        
        <button type="submit" class="btn" style="width: 100%; margin-top: 25px; background: #f57c00; font-size: 16px;">商品情報を更新する</button>
    </form>
</div>

</body>
</html>