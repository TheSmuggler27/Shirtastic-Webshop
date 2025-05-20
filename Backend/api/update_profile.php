<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

$data = json_decode(file_get_contents("php://input"), true);

// 提取数据
// Felder extrahieren
$firstName = $data["first_name"] ?? "";
$lastName = $data["last_name"] ?? "";
$userId = $data["userId"] ?? null;
$address = $data["address"] ?? "";
$paymentInfo = $data["payment_info"] ?? "";
$currentPassword = $data["current_password"] ?? "";

// 检查是否提供关键字段
// Prüfen, ob alle Pflichtfelder vorhanden sind
if (!$userId || !$currentPassword) {
  echo json_encode(["status" => "error", "message" => "Missing required fields"]);
  exit;
}

// 从数据库中获取当前密码（哈希值）
// Aktuelles Passwort aus der Datenbank abrufen
$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$userId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// 如果用户不存在，或密码不匹配，返回错误
// Fehler zurückgeben, wenn Passwort falsch oder Benutzer nicht gefunden
if (!$row || !password_verify($currentPassword, $row["password"])) {
  echo json_encode(["status" => "error", "message" => "Incorrect password"]);
  exit;
}


// 更新改完后的信息
// Benutzerinformationen aktualisieren
$stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, address = ?, payment_info = ? WHERE id = ?");
if ($stmt->execute([$firstName, $lastName, $address, $paymentInfo, $userId])) {
  echo json_encode(["status" => "ok"]);
} else {
  echo json_encode(["status" => "error", "message" => "Failed to update info"]);
}
