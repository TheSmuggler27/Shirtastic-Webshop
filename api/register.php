<?php
header('Content-Type: application/json');
require_once 'DB.php'; // ✅ 使用新的数据库连接类

$pdo = DB::getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if (
  isset($data['salutation']) &&
  isset($data['first_name']) &&
  isset($data['last_name']) &&
  isset($data['address']) &&
  isset($data['postal_code']) &&
  isset($data['city']) &&
  isset($data['email']) &&
  isset($data['username']) &&
  isset($data['password']) &&
  isset($data['payment_info'])
) {
  $salutation = $data['salutation'];
  $first_name = $data['first_name'];
  $last_name = $data['last_name'];
  $address = $data['address'];
  $postal_code = $data['postal_code'];
  $city = $data['city'];
  $email = $data['email'];
  $username = $data['username'];
  $password = password_hash($data['password'], PASSWORD_DEFAULT); // ✅ 密码加密
  $payment_info = $data['payment_info'];

  try {
    $stmt = $pdo->prepare("INSERT INTO users (salutation, first_name, last_name, address, postal_code, city, email, username, password, payment_info, role, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'user', 1)");
    $stmt->execute([$salutation, $first_name, $last_name, $address, $postal_code, $city, $email, $username, $password, $payment_info]);

    echo json_encode(["status" => "ok"]);
  } catch (PDOException $e) {
    echo json_encode([
      "status" => "error",
      "message" => "Registration failed: " . $e->getMessage()
    ]);
  }
} else {
  echo json_encode([
    "status" => "error",
    "message" => "Missing required fields."
  ]);
}
