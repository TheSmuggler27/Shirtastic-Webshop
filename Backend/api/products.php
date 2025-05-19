<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 


$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;

try {
    if ($category_id) {
        $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE category_id = ?");
        $stmt->execute([$category_id]);
    } else {
        $stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id");
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        "status" => "ok",
        "products" => $products
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}



