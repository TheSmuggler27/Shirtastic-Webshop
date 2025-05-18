<?php
require_once 'DB.php';
$pdo = DB::getConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE ...");


try {
  // 测试连接是否成功
  $stmt = $pdo->query("SELECT id, username, email, role, active FROM users ORDER BY id ASC");

  if ($stmt) {
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
  } else {
    echo json_encode(["status" => "error", "message" => "Query failed"]);
  }

} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
