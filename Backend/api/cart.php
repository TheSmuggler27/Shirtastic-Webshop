<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();


$data = json_decode(file_get_contents("php://input"), true);


// 检查必要字段是否齐全
// Prüfen, ob alle erforderlichen Felder vorhanden sind
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


// 从 JSON 中提取变量
// Extrahiere Variablen aus dem JSON
$userId = $data['userId'];
$name = $data['name'];
$address = $data['address'];
$total = floatval($data['total']);
$items = $data['items'];
$voucherCode = trim($data['voucherCode'] ?? "");
$discount = isset($data['discount']) ? floatval($data['discount']) : 0;


try {
    $pdo->beginTransaction();

    // 如果有优惠券，则验证其合法性与余额
    // Wenn ein Gutscheincode vorhanden ist, prüfen ob gültig und ausreichend Guthaben
    if (!empty($voucherCode)) {
        $stmt = $pdo->prepare("SELECT remaining_amount FROM vouchers WHERE code = ?");
        $stmt->execute([$voucherCode]);
        $voucher = $stmt->fetch(PDO::FETCH_ASSOC);


        // 优惠券无效或金额不足时，不使用该券
        // Wenn ungültig oder Betrag zu gering, Gutschein ignorieren
        if (!$voucher || floatval($voucher['remaining_amount']) < $discount) {
            $voucherCode = null;
            $discount = 0;
        }
    }

    // 保存订单主信息（orders 表）
    // Speichere Bestelldaten in der Tabelle `orders`
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, address, total, voucher_code, discount_applied) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $name, $address, $total, $voucherCode ?: null, $discount]);

    // 获取刚插入订单的 ID
    // Abrufen der zuletzt eingefügten Auftrags-ID
    $orderId = $pdo->lastInsertId();

    // 保存每一个商品项（order_items 表）
    // Jede Position separat in `order_items` speichern
    foreach ($items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, product_price) VALUES (?, ?, ?)");
        $stmt->execute([$orderId, $item['name'], $item['price']]);
    }

    // 更新优惠券余额（减去折扣金额）
    // Gutscheinbetrag verringern
    if (!empty($voucherCode) && $discount > 0) {
        $stmt = $pdo->prepare("UPDATE vouchers SET remaining_amount = remaining_amount - ? WHERE code = ?");
        $stmt->execute([$discount, $voucherCode]);
    }


    // 提交Transaction
    // Transaktion abschließen (commit)
    $pdo->commit();
    echo json_encode(['status' => 'ok']);

    // 出错时回滚Transaction
    // Bei Fehler: Transaktion zurückrollen
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}







