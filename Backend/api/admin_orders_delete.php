<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 

try {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['orderId'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing order ID']);
        exit;
    }

    $orderId = intval($data['orderId']);

    // 先删除 order_items 中相关记录
    $stmt1 = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt1->execute([$orderId]);

    // 再删除订单本体
    $stmt2 = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt2->execute([$orderId]);

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
