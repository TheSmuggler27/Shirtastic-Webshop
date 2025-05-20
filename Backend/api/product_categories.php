<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();

try {
    // 执行查询：从 categories 中获取 id 和 name
    // SQL-Abfrage: Kategorien-ID und Name auslesen
    $stmt = $pdo->query("SELECT id, name FROM categories");

    // 以关联数组形式获取所有分类
    // Ergebnisse als assoziatives Array abrufen
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 返回成功 JSON 响应
    // Rückgabe der Kategorien als JSON
    echo json_encode(["status" => "ok", "categories" => $categories]);


    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "DB error", "details" => $e->getMessage()]);
}

