<?php
header('Content-Type: application/json');
require_once 'DB.php';
$pdo = DB::getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'] ?? '';
$address = $data['address'] ?? '';
$items = $data['items'] ?? [];
$total = floatval($data['total'] ?? 0);
$voucherCode = $data['voucherCode'] ?? null;
$discountApplied = 0;

if (!$name || !$address || empty($items)) {
  echo json_encode(["status" => "error", "message" => "Missing fields"]);
  exit;
}

try {
  $pdo->beginTransaction();

  // 如果使用了优惠券，检查它是否有效
  if ($voucherCode) {
    $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE code = ? FOR UPDATE");
    $stmt->execute([$voucherCode]);
    $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

    $today = date("Y-m-d");
    if (!$voucher || $voucher['used'] || $voucher['valid_until'] < $today || $voucher['remaining_amount'] <= 0) {
      echo json_encode(["status" => "error", "message" => "Invalid or expired voucher"]);
      exit;
    }

    // 计算折扣
    $discountApplied = min($total, $voucher['remaining_amount']);
    $newRemaining = $voucher['remaining_amount'] - $discountApplied;
    $totalAfterDiscount = $total - $discountApplied;

    // 更新优惠券余额
    $stmt = $pdo->prepare("UPDATE vouchers SET remaining_amount = ?, used = ? WHERE id = ?");
    $stmt->execute([$newRemaining, $newRemaining <= 0 ? 1 : 0, $voucher['id']]);
  } else {
    $totalAfterDiscount = $total;
  }

  // 保存订单
    $userId = $data['userId'] ?? null;
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, address, total, discount_applied, voucher_code, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$userId, $name, $address, $totalAfterDiscount, $discountApplied, $voucherCode]);


  $orderId = $pdo->lastInsertId();

  // 保存每个商品项
  foreach ($items as $item) {
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, product_price) VALUES (?, ?, ?)");
    $stmt->execute([$orderId, $item['name'], $item['price']]);
  }

  $pdo->commit();

  echo json_encode(["status" => "ok", "orderId" => $orderId]);
} catch (Exception $e) {
  $pdo->rollBack();
  echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}




