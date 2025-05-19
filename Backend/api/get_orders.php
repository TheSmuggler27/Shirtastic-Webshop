<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 


session_start();
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
  echo json_encode(["status" => "error", "message" => "User not logged in."]);
  exit;
}

// 判断当前用户角色（可选，如果后期要支持管理员查看全部订单）
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$isAdmin = ($user && $user['role'] === 'admin');

// 获取订单（管理员看全部，普通用户只看自己的）
if ($isAdmin) {
  $orderStmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
} else {
  $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
  $orderStmt->execute([$userId]);
}

$orders = [];

while ($order = $orderStmt->fetch(PDO::FETCH_ASSOC)) {
  $orderId = $order['id'];

  // 获取订单的商品项
  $itemStmt = $pdo->prepare("SELECT product_name, product_price FROM order_items WHERE order_id = ?");
  $itemStmt->execute([$orderId]);
  $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

  // 计算总价
  $total = array_reduce($items, function ($sum, $item) {
    return $sum + floatval($item['product_price']);
  }, 0);

  $order['items'] = $items;
  $order['total'] = $total;
  $orders[] = $order;
}

echo json_encode(["status" => "ok", "orders" => $orders]);
