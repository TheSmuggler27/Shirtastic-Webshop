<?php
require_once 'DB.php';
$pdo = DB::getConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE ...");


// 从前端接收 JSON 数据
$data = json_decode(file_get_contents("php://input"), true);

// 提取字段
$id = $data['id'] ?? null;
$name = $data['name'] ?? '';
$description = $data['description'] ?? '';
$price = $data['price'] ?? 0;
$category_id = $data['category_id'] ?? null;
$rating = $data['rating'] ?? null;

if (!$id) {
    echo json_encode(["status" => "error", "message" => "Missing product ID."]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE products 
        SET name = :name,
            description = :description,
            price = :price,
            category_id = :category_id,
            rating = :rating
        WHERE id = :id
    ");
    $stmt->execute([
        'id' => $id,
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'category_id' => $category_id,
        'rating' => $rating
    ]);

    echo json_encode(["status" => "ok"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

