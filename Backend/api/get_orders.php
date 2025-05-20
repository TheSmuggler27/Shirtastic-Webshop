<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 


session_start();

$userId = $_SESSION['user_id'] ?? null;

// 如果没有登录，返回错误
// Wenn nicht eingeloggt, Fehler zurückgeben
if (!$userId) {
  echo json_encode(["status" => "error", "message" => "User not logged in."]);
  exit;
}

// 判断当前用户角色（是否是 admin，管理员可以查看所有订单）
// Benutzerrolle prüfen (nur Admins dürfen alle Bestellungen sehen)
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$isAdmin = ($user && $user['role'] === 'admin');

// 获取订单（管理员获取全部，普通用户获取自己的）
// Bestellungen abrufen (Admin: alle, Benutzer: eigene)
if ($isAdmin) {
  $orderStmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
} else {
  $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
  $orderStmt->execute([$userId]);
}

$orders = [];

while ($order = $orderStmt->fetch(PDO::FETCH_ASSOC)) {
  $orderId = $order['id'];

  // 获取订单中商品条目
  // Abrufen der Artikel für jede Bestellung
  $itemStmt = $pdo->prepare("SELECT product_name, product_price FROM order_items WHERE order_id = ?");
  $itemStmt->execute([$orderId]);
  $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);


  // 计算订单总价
  // Berechne die Gesamtsumme der Bestellung
  $total = array_reduce($items, function ($sum, $item) {
    return $sum + floatval($item['product_price']);
  }, 0);

  // 将商品与总价加入订单数组
  // Füge Artikel und Summe zur Bestellung hinzu
  $order['items'] = $items;
  $order['total'] = $total;

  // 将订单加入总结果中
  // Bestellung zur Gesamtliste hinzufügen
  $orders[] = $order;
}


// 返回所有订单信息
// Rückgabe aller Bestellungen als JSON
echo json_encode(["status" => "ok", "orders" => $orders]);
