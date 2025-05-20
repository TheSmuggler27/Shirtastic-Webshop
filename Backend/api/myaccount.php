<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 


// 获取前端发送的 userId（通常来自 localStorage）
// userId aus dem JSON-Request auslesen (meist aus localStorage)
$data = json_decode(file_get_contents("php://input"), true);
$userId = $data["userId"] ?? null;

// 检查有无 userId
// Prüfen, ob userId übergeben wurde
if (!$userId) {
  echo json_encode(["status" => "error", "message" => "Missing userId"]);
  exit;
}

try {
  // 查询指定用户 ID 的信息
  // Abfrage der Benutzerdaten anhand der ID
  $stmt = $pdo->prepare("SELECT first_name, last_name, username, email, address, payment_info FROM users WHERE id = ?");
  $stmt->execute([$userId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    // 找到用户，返回用户数据
    // Benutzer gefunden, Daten zurückgeben
    echo json_encode(["status" => "ok", "user" => $user]);
  } else {
    // 用户不存在
    // Benutzer nicht gefunden
    echo json_encode(["status" => "error", "message" => "User not found"]);
  }
} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => "DB error"]);
}
