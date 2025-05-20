<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 


// 确保请求是 DELETE
// Prüfen, ob die HTTP-Methode DELETE ist
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

// 从Request-Body或 URL 获取产品 ID
// Produkt-ID aus dem Request-Body oder aus der lesenURL 
parse_str(file_get_contents("php://input"), $data);
$id = $data['id'] ?? $_GET['id'] ?? null;


if (!$id) {
    echo json_encode(["status" => "error", "message" => "Missing product ID."]);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    echo json_encode(["status" => "ok"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
