<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

if (!isset($_GET['code'])) {
    echo json_encode(['status' => 'error', 'message' => 'Code missing']);
    exit;
}

$code = $_GET['code'];

try {
    $stmt = $pdo->prepare("SELECT amount, remaining_amount, valid_until FROM vouchers WHERE code = ?");
    $stmt->execute([$code]);
    $voucher = $stmt->fetch();

    if ($voucher) {
        $today = date('Y-m-d');
        $validUntil = $voucher['valid_until'];

        if ($validUntil !== null && $validUntil < $today) {
            echo json_encode(['status' => 'error', 'message' => 'Voucher expired']);
            exit;
        }

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
