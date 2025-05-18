<?php
require_once 'DB.php';
$pdo = DB::getConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE ...");

try {
  $stmt = $pdo->query("SELECT code, amount, valid_until FROM vouchers ORDER BY id DESC");
  $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($vouchers);
} catch (PDOException $e) {
  echo json_encode([]);
}
