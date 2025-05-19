<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

$stmt = $pdo->query("SELECT code, amount, valid_until, used, remaining_amount FROM vouchers");
$vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$today = date("Y-m-d");

foreach ($vouchers as &$voucher) {
    if ($voucher['used'] || $voucher['remaining_amount'] <= 0) {
        $voucher['status'] = "Used";
    } elseif ($voucher['valid_until'] < $today) {
        $voucher['status'] = "Expired";
    } else {
        $voucher['status'] = "Active";
    }
}

echo json_encode([
  "status" => "ok",
  "vouchers" => $vouchers
]);

