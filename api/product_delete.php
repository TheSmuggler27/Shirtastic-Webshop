<?php
require_once 'DB.php';
$pdo = DB::getConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE ...");


// 确保请求是 DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

// 从 URL 或 DELETE body 获取 id
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
