<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

$data = json_decode(file_get_contents("php://input"), true);

// 提取订单 ID 和新状态
// Extrahiere Bestell-ID und neuen Status
$orderId = $data['orderId'] ?? null;
$newStatus = $data['status'] ?? null;

// 这两个如果缺少任一字段，则返回错误
// Wenn eines der Felder fehlt, gib einen Fehler zurück
if (!$orderId || !$newStatus) {
  echo json_encode(["status" => "error", "message" => "Missing fields"]);
  exit;
}


try {
  // 更新数据库中的订单状态
  // Aktualisiere den Bestellstatus in der Datenbank
  $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
  $stmt->execute([$newStatus, $orderId]);

  


  echo json_encode(["status" => "ok"]);
} catch (Exception $e) {
  echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
