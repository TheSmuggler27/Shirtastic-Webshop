<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

session_start();

$data = json_decode(file_get_contents("php://input"), true);

$identifier = $data['identifier'] ?? '';
$password = $data['password'] ?? '';

try {
    // 查询数据库：用户名或邮箱
    // Datenbankabfrage: Benutzername ODER E-Mail
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :identifier OR email = :identifier");
    $stmt->execute(['identifier' => $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 验证用户是否存在 + 密码是否正确
    // Überprüfen, ob Benutzer existiert UND Passwort korrekt ist
    if ($user && password_verify($password, $user['password'])) {

        // 如果用户是激活状态（active = 1）
        // Wenn Benutzer aktiv ist (active = 1)
        if ((int)$user['active'] === 1) {

            // 保存用户 ID 到 session 中
            // Benutzer-ID in der Session speichern
            $_SESSION['user_id'] = $user['id'];
            
            // 登录成功返回用户基本信息
            // Login erfolgreich: Benutzerdaten zurückgeben
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


