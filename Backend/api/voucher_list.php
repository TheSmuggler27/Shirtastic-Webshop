<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();


// 查询所有优惠券
// Alle Gutscheine aus der Datenbank abrufen
$stmt = $pdo->query("SELECT code, amount, valid_until, used, remaining_amount FROM vouchers");
$vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);


// 获取当前日期
// Heutiges Datum abrufen
$today = date("Y-m-d");


// 查看每张优惠券并判断其状态
// Schleife: Status jedes Gutscheins prüfen
foreach ($vouchers as &$voucher) {
    if ($voucher['used'] || $voucher['remaining_amount'] <= 0) {
        $voucher['status'] = "Used";
    } elseif ($voucher['valid_until'] < $today) {
        $voucher['status'] = "Expired";
    } else {
        $voucher['status'] = "Active";
    }
}

// 返回所有优惠券及其状态
// Rückgabe aller Gutscheine mit Status
echo json_encode([
  "status" => "ok",
  "vouchers" => $vouchers
]);

