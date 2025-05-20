<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

$data = json_decode(file_get_contents("php://input"), true);

// 提取字段
// Extrahiere Felder aus dem Request
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
    // 执行更新操作
    // Update-Abfrage vorbereiten und ausführen
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

