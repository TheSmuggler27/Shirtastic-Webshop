<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

$data = json_decode(file_get_contents("php://input"), true);
$orderId = $data['orderId'] ?? null;
$newStatus = $data['status'] ?? null;

if (!$orderId || !$newStatus) {
  echo json_encode(["status" => "error", "message" => "Missing fields"]);
  exit;
}

try {
  $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
  $stmt->execute([$newStatus, $orderId]);
  echo json_encode(["status" => "ok"]);
} catch (Exception $e) {
  echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
