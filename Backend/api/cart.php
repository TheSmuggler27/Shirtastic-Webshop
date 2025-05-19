<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if (
    empty($data['userId']) ||
    empty($data['name']) ||
    empty($data['address']) ||
    !isset($data['items']) ||
    !isset($data['total'])
) {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
    exit;
}

$userId = $data['userId'];
$name = $data['name'];
$address = $data['address'];
$total = floatval($data['total']);
$items = $data['items'];
$voucherCode = trim($data['voucherCode'] ?? "");
$discount = isset($data['discount']) ? floatval($data['discount']) : 0;


try {
    $pdo->beginTransaction();

    // 验证优惠券合法性，余额是否足够
    if (!empty($voucherCode)) {
        $stmt = $pdo->prepare("SELECT remaining_amount FROM vouchers WHERE code = ?");
        $stmt->execute([$voucherCode]);
        $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$voucher || floatval($voucher['remaining_amount']) < $discount) {
            // 如果券不存在，或余额不足，则作废该券
            $voucherCode = null;
            $discount = 0;
        }
    }

    // 保存订单
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, address, total, voucher_code, discount_applied) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $name, $address, $total, $voucherCode ?: null, $discount]);

    $orderId = $pdo->lastInsertId();

    // 保存每一项产品
    foreach ($items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, product_price) VALUES (?, ?, ?)");
        $stmt->execute([$orderId, $item['name'], $item['price']]);
    }

    // 减少券余额
    if (!empty($voucherCode) && $discount > 0) {
        $stmt = $pdo->prepare("UPDATE vouchers SET remaining_amount = remaining_amount - ? WHERE code = ?");
        $stmt->execute([$discount, $voucherCode]);
    }

    $pdo->commit();
    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}







