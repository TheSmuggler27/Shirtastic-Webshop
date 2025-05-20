<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

// 从 GET 参数中读取 category_id
// Kategorie-ID aus der URL lesen
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;

try {
    // 根据是否选择分类筛选执行不同查询
    // Unterschiedliche SQL-Abfrage je nach Kategorie
    if ($category_id) {
        $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE category_id = ?");
        $stmt->execute([$category_id]);
    } else {
        // 不筛选的话查询所有商品
        // Alle Produkte abrufen
        $stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id");
    }
    
    // 获取所有商品数据（含分类名）
    // Alle Ergebnisse als Array holen
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



