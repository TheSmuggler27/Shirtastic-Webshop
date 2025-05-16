<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "shirtastic webshop");

if ($conn->connect_error) {
  die(json_encode(["status" => "error", "message" => "DB-Verbindung fehlgeschlagen."]));
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["email"]) || !isset($data["password"])) {
  echo json_encode(["status" => "error", "message" => "Fehlende Eingabedaten."]);
  exit;
}

$email = $data["email"];
$password = $data["password"];
    
$stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  if (password_verify($password, $row["password"])) {
    echo json_encode(["status" => "ok", "username" => $row["username"]]);
  } else {
    echo json_encode(["status" => "error", "message" => "Falsches Passwort."]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "E-Mail nicht gefunden."]);
}

$stmt->close();
$conn->close();
?>
