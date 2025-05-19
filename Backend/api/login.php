<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

session_start(); // ✅ 开启 session

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
            $_SESSION['user_id'] = $user['id']; // ✅ 添加：设置 session 中的用户 ID

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
        echo json_encode([
            "status" => "error",
            "message" => "Incorrect username or password."
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error."
    ]);
}


