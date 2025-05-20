<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

try {
  // 获取所有订单，按创建时间降序排列
  // Alle Bestellungen abrufen, sortiert nach Erstellungsdatum (absteigend)
  $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
  $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // 准备两个查询语句，用于后续循环中多次使用
  // Vorbereitete Statements für Benutzer- und Bestellpositionen
  $stmtUser = $pdo->prepare("SELECT username, first_name, last_name FROM users WHERE id = ?");
  $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");



  foreach ($orders as &$order) {
    // 获取该订单的用户信息
    // Benutzerinformationen für die Bestellung abrufen
    $stmtUser->execute([$order['user_id']]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    // 如果找到用户，给出完整姓名
    // Wenn Benutzer gefunden, vollständiger Name zusammenstellen
    if ($user) {
      $order['user'] = $user['username'] . " (" . $user['first_name'] . " " . $user['last_name'] . ")";
    } else {
      $order['user'] = "Unknown";
    }

    // 获取该订单的商品信息
    // Bestellpositionen abrufen
    $stmtItems->execute([$order['id']]);
    $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
  }



  echo json_encode(["status" => "ok", "orders" => $orders]);
} catch (Exception $e) {
  echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

