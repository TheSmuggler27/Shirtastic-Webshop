<?php
require_once 'DB.php';
$pdo = DB::getConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE ...");

$data = json_decode(file_get_contents("php://input"), true);

$firstName = $data["first_name"] ?? "";
$lastName = $data["last_name"] ?? "";
$userId = $data["userId"] ?? null;
$address = $data["address"] ?? "";
$paymentInfo = $data["payment_info"] ?? "";
$currentPassword = $data["current_password"] ?? "";

if (!$userId || !$currentPassword) {
  echo json_encode(["status" => "error", "message" => "Missing required fields"]);
  exit;
}

// 1. 获取用户的当前密码
$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$userId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || !password_verify($currentPassword, $row["password"])) {
  echo json_encode(["status" => "error", "message" => "Incorrect password"]);
  exit;
}

// 2. 执行更新
$stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, address = ?, payment_info = ? WHERE id = ?");
if ($stmt->execute([$firstName, $lastName, $address, $paymentInfo, $userId])) {
  echo json_encode(["status" => "ok"]);
} else {
  echo json_encode(["status" => "error", "message" => "Failed to update info"]);
}
