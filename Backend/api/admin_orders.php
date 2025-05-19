<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

try {
  // 获取所有订单，按时间倒序排列
  $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
  $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // 预处理语句：用户信息 + 订单项
  $stmtUser = $pdo->prepare("SELECT username, first_name, last_name FROM users WHERE id = ?");
  $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");

  foreach ($orders as &$order) {
    // 获取用户信息
    $stmtUser->execute([$order['user_id']]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
    if ($user) {
      $order['user'] = $user['username'] . " (" . $user['first_name'] . " " . $user['last_name'] . ")";
    } else {
      $order['user'] = "Unknown";
    }

    // 获取商品项信息
    $stmtItems->execute([$order['id']]);
    $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
  }

  echo json_encode(["status" => "ok", "orders" => $orders]);
} catch (Exception $e) {
  echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

