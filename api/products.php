<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "shirtastic webshop");
if ($conn->connect_error) {
  echo json_encode(["status" => "error", "message" => "DB-Verbindung fehlgeschlagen."]);
  exit;
}

$result = $conn->query("SELECT id, name, price, img FROM products");
$products = [];

while ($row = $result->fetch_assoc()) {
  $products[] = $row;
}

echo json_encode(["status" => "ok", "products" => $products]);

$conn->close();
?>
