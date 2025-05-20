<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

$data = json_decode(file_get_contents("php://input"), true);

// 注册时验证所有字段是否都填写
// Prüfen, ob alle erforderlichen Felder vorhanden sind
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

  // 提取字段
  // Felder extrahieren
  $salutation = $data['salutation'];
  $first_name = $data['first_name'];
  $last_name = $data['last_name'];
  $address = $data['address'];
  $postal_code = $data['postal_code'];
  $city = $data['city'];
  $email = $data['email'];
  $username = $data['username'];
  $password = password_hash($data['password'], PASSWORD_DEFAULT); // 密码加密 hash passwort
  $payment_info = $data['payment_info'];

  $checkStmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
  $checkStmt->execute([$email, $username]);

  // 检查邮箱或用户名是否已存在
  // Prüfen, ob E-Mail oder Benutzername bereits existieren
  if ($checkStmt->rowCount() > 0) {
      echo json_encode([
          "status" => "error",
          "message" => "Email or username already exists."
      ]);
      exit;
  }
  
  try {
    // 插入新用户，默认角色 user，激活状态 active = 1
    // Einfügen eines neuen Benutzers (Rolle = user, Aktiv = 1)
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
