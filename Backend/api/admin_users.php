<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 


try {
  // 进行 SQL 查询：获取所有用户，然后按 ID 升序排列
  // SQL-Abfrage: Alle Benutzer nach ID aufsteigend sortieren
  $stmt = $pdo->query("SELECT id, username, email, role, active FROM users ORDER BY id ASC");

  //判断 SQL 查询用户的操作是否成功
  // Prüfen, ob die Benutzerdaten erfolgreich aus der Datenbank abgefragt wurden
  if ($stmt) {
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
  } else {
    echo json_encode(["status" => "error", "message" => "Query failed"]);
  }


} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
