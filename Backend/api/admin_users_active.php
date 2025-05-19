<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 


$data = json_decode(file_get_contents("php://input"), true);
$id = $data["id"] ?? null;
$active = $data["active"] ?? null;

if (!$id || !isset($active)) {
  echo json_encode(["status" => "error", "message" => "Missing fields"]);
  exit;
}

try {
  $stmt = $pdo->prepare("UPDATE users SET active = ? WHERE id = ?");
  if ($stmt->execute([$active, $id])) {
    echo json_encode(["status" => "ok"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Update failed"]);
  }
} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => "DB error"]);
}
