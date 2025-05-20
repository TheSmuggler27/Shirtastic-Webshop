<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 


$data = json_decode(file_get_contents("php://input"), true);

// 获取用户的 ID 和激活状态
// Benutzer-ID und Aktiv-Status extrahieren
$id = $data["id"] ?? null;
$active = $data["active"] ?? null;


// 如果缺少字段，报错
// Wenn Felder fehlen, gib Fehler zurück
if (!$id || !isset($active)) {
  echo json_encode(["status" => "error", "message" => "Missing fields"]);
  exit;
}


try {
  // 使用prepare语句更新用户的激活状态
  // Update des Aktiv-Status mit vorbereitetem Statement
  $stmt = $pdo->prepare("UPDATE users SET active = ? WHERE id = ?");

  // 检测激活状态是否成功 
  // Überprüft, ob das SQL-Statement erfolgreich ausgeführt wurde.
  if ($stmt->execute([$active, $id])) {
    echo json_encode(["status" => "ok"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Update failed"]);
  }


} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => "DB error"]);
}
