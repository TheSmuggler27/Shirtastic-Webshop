<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "shirtastic webshop");
if ($conn->connect_error) {
  echo json_encode(["status" => "error", "message" => "DB-Verbindung fehlgeschlagen"]);
  exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input["name"], $input["address"], $input["items"], $input["date"])) {
  echo json_encode(["status" => "error", "message" => "Ungültige Eingabedaten"]);
  exit;
}

$name = $input["name"];
$address = $input["address"];
$items = json_encode($input["items"]);
$order_date = $input["date"];

$stmt = $conn->prepare("INSERT INTO orders (name, address, items, order_date) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $address, $items, $order_date);

if ($stmt->execute()) {
  echo json_encode(["status" => "ok"]);
} else {
  echo json_encode(["status" => "error", "message" => "Fehler beim Speichern"]);
}

$stmt->close();
$conn->close();
?>

