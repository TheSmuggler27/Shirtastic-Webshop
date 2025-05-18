<?php
require_once 'DB.php';
$pdo = DB::getConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE ...");


// 从前端获取 JSON 数据
$data = json_decode(file_get_contents("php://input"), true);
$identifier = $data['identifier'] ?? '';
$password = $data['password'] ?? '';

try {
    // 查询数据库，允许用户名或邮箱登录
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :identifier OR email = :identifier");
    $stmt->execute(['identifier' => $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 如果查到用户并且密码正确
    if ($user && password_verify($password, $user['password'])) {
        if ((int)$user['active'] === 1) {
            echo json_encode([
                "status" => "ok",
                "username" => $user['username'],
                "role" => $user['role'],
                "userId" => $user['id']
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "User account is disabled."
            ]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid login credentials."]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error."]);
}


