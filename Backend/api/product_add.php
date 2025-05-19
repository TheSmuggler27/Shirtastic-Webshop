<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 


// 接收表单字段
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$price = $_POST['price'] ?? 0;
$category_id = $_POST['category_id'] ?? null;
$rating = $_POST['rating'] ?? null;

// 验证图片上传
if (!isset($_FILES['image'])) {
    echo json_encode(["status" => "error", "message" => "No image uploaded."]);
    exit;
}
$image = $_FILES['image'];
$targetDir = __DIR__ . '/../../Frontend/img/';
$targetFile = $targetDir . basename($image["name"]);

if (!move_uploaded_file($image["tmp_name"], $targetFile)) {
    echo json_encode(["status" => "error", "message" => "Image upload failed."]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO products (name, description, price, image_path, rating, category_id)
        VALUES (:name, :description, :price, :image_path, :rating, :category_id)
    ");
    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'image_path' => $image["name"],
        'rating' => $rating,
        'category_id' => $category_id
    ]);
    echo json_encode(["status" => "ok"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

