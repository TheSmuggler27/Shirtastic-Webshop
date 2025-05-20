<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

try {
    // 读取 JSON 数据（从 JS fetch 请求中传入）
    // Lese JSON-Daten aus dem Request-Body (kommt von fetch)
    $data = json_decode(file_get_contents("php://input"), true);

    // 如果没有提供 orderId，则返回错误
    // Wenn keine orderId übergeben wurde, Fehler zurückgeben
    if (!isset($data['orderId'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing order ID']);
        exit;
    }

    // 将 orderId 转换为整数类型
    // Konvertiere orderId in eine Ganzzahl
    $orderId = intval($data['orderId']);

    // 第一步：先删除数据库中 order_items 中所有关联此订单的记录
    // Schritt 1: Zuerst alle zugehörigen Einträge in der Tabelle order_items löschen
    $stmt1 = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt1->execute([$orderId]);

    // 第二步：再删除数据库 orders 中的订单本体
    // Schritt 2: Dann den eigentlichen Eintrag in der Tabelle orders löschen
    $stmt2 = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt2->execute([$orderId]);




    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
