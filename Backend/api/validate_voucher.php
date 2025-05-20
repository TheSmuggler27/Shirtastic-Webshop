<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

// 检查是否传入 code 数据
// Prüfen, ob Gutscheincode übergeben wurde
if (!isset($_GET['code'])) {
    echo json_encode(['status' => 'error', 'message' => 'Code missing']);
    exit;
}

$code = $_GET['code'];

try {
    // 查询优惠券信息：总金额、剩余金额、有效期
    // Gutscheininformationen abrufen
    $stmt = $pdo->prepare("SELECT amount, remaining_amount, valid_until FROM vouchers WHERE code = ?");
    $stmt->execute([$code]);
    $voucher = $stmt->fetch();

    if ($voucher) {
        // 获取当前日期
        // Aktuelles Datum holen
        $today = date('Y-m-d');
        $validUntil = $voucher['valid_until'];

        // 检查是否已过期
        // Gültigkeitsdatum prüfen
        if ($validUntil !== null && $validUntil < $today) {
            echo json_encode(['status' => 'error', 'message' => 'Voucher expired']);
            exit;
        }
        
        // 检查剩余金额是否为 0
        // Prüfen, ob der Gutschein bereits verbraucht wurde
        $remaining = floatval($voucher['remaining_amount']);
        if ($remaining <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Voucher already used']);
            exit;
        }

        echo json_encode([
            'status' => 'ok',
            'amount' => $remaining
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid code']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB error']);
}
