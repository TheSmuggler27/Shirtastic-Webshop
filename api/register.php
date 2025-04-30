<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "shirtastic webshop");

if ($conn->connect_error) {
  die(json_encode(["status" => "error", "message" => "DB-Verbindung fehlgeschlagen."]));
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!isset($data["username"]) || !isset($data["email"]) || !isset($data["password"])) {
  echo json_encode(["status" => "error", "message" => "Ungültige Eingabe."]);
  exit;
}

$username = $data["username"];
$email = $data["email"];
$password = password_hash($data["password"], PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $password);

if ($stmt->execute()) {
  echo json_encode(["status" => "ok"]);
} else {
  echo json_encode(["status" => "error", "message" => "Eintrag fehlgeschlagen."]);
}

$stmt->close();
$conn->close();
?>
