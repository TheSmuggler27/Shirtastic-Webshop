<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

$data = json_decode(file_get_contents("php://input"), true);
$code = $data["code"] ?? "";
$amount = $data["amount"] ?? 0;
$validUntil = $data["valid_until"] ?? "";

if (!$code || !$amount || !$validUntil) {
  echo json_encode(["status" => "error", "message" => "Missing fields"]);
  exit;
}

try {
  // 检查是否重复
  $stmt = $pdo->prepare("SELECT id FROM vouchers WHERE code = ?");
  $stmt->execute([$code]);
  if ($stmt->fetch()) {
    echo json_encode(["status" => "error", "message" => "Code already exists"]);
    exit;
  }

  // 插入
    $stmt = $pdo->prepare("INSERT INTO vouchers (code, amount, remaining_amount, valid_until) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$code, $amount, $amount, $validUntil])) {

    echo json_encode(["status" => "ok"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Failed to save"]);
  }
} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => "Database error"]);
}
