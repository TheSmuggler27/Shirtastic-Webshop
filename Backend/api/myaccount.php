<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 


// 获取前端发送的 userId（来自 localStorage）
$data = json_decode(file_get_contents("php://input"), true);
$userId = $data["userId"] ?? null;

if (!$userId) {
  echo json_encode(["status" => "error", "message" => "Missing userId"]);
  exit;
}

try {
  $stmt = $pdo->prepare("SELECT first_name, last_name, username, email, address, payment_info FROM users WHERE id = ?");
  $stmt->execute([$userId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    echo json_encode(["status" => "ok", "user" => $user]);
  } else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
  }
} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => "DB error"]);
}
