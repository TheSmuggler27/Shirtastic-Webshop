<?php
require_once 'DB.php';
$pdo = DB::getConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE ...");

$code = $_GET["code"] ?? "";

$stmt = $pdo->prepare("SELECT amount FROM vouchers WHERE code = ?");
$stmt->execute([$code]);
$voucher = $stmt->fetch(PDO::FETCH_ASSOC);

if ($voucher) {
    echo json_encode(["status" => "ok", "amount" => (float)$voucher["amount"]]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid voucher code"]);
}
?>
